<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use Database\Seeders\CatalogSeeder;
use Database\Seeders\DemoProductSeeder;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([CatalogSeeder::class, DemoProductSeeder::class]);
    }

    public function test_public_pages_render(): void
    {
        foreach (['home', 'products.index', 'search', 'cart.index', 'login', 'register', 'password.request',
            'pages.about', 'pages.contact', 'pages.faq', 'pages.how-to-buy', 'pages.terms'] as $route) {
            $this->get(route($route))->assertOk();
        }

        $product = Product::query()->first();
        $this->get(route('products.show', $product))->assertOk()->assertSee($product->name);
        $this->get(route('categories.show', $product->category))->assertOk();
        $this->get(route('brands.show', $product->brand))->assertOk();
    }

    public function test_empty_settings_hide_their_item_and_icon(): void
    {
        \App\Models\Setting::put(['working_hours' => null, 'address_iran' => null, 'phone_iran' => '021-11112222']);

        $this->get(route('home'))->assertOk()
            ->assertDontSee('fa-regular fa-clock', false)   // working hours row removed entirely
            ->assertDontSee('ایران:</span>', false)
            ->assertSee('021-11112222');

        $this->get(route('pages.contact'))->assertOk()->assertDontSee('ساعات کاری');
    }

    public function test_security_headers_are_sent(): void
    {
        $this->get(route('home'))
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Security-Policy');
    }

    public function test_search_matches_part_numbers_written_with_spaces(): void
    {
        $this->get(route('search', ['q' => 'A 222 320 47 13']))->assertOk()->assertSee('Airmatic');

        $this->getJson(route('search.suggest', ['q' => 'W463']))
            ->assertOk()
            ->assertJsonPath('items.0.name', 'کیت دیسک و لنت ترمز سرامیکی AMG مرسدس G63');
    }

    public function test_filters_by_chassis_and_year(): void
    {
        $this->get(route('products.index', ['chassis' => 'g-class-w463']))->assertOk()->assertSee('G63')->assertDontSee('Airmatic');
        $this->get(route('products.index', ['brand' => 'mercedes-benz', 'year' => 2025]))->assertOk()->assertDontSee('M271 EVO');
    }

    public function test_inactive_products_are_hidden(): void
    {
        $product = Product::query()->first();
        $product->update(['is_active' => false]);

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_inquiry_flow(): void
    {
        $this->get(route('inquiries.create', ['vin' => 'WDD2130421A123456']))->assertRedirect(route('login'));

        $customer = $this->userWithRole(Role::CUSTOMER);
        $this->actingAs($customer)->post(route('inquiries.store'), [
            'part_name' => 'چراغ عقب', 'vin' => 'wdd2130421a123456', 'quantity' => '۲',
        ])->assertRedirect();

        $inquiry = Inquiry::query()->firstOrFail();
        $this->assertSame('WDD2130421A123456', $inquiry->vin);
        $this->assertSame(2, $inquiry->quantity);

        // Another customer cannot see it
        $this->actingAs($this->userWithRole(Role::CUSTOMER))->get(route('account.inquiries.show', $inquiry))->assertNotFound();

        // Admin replies
        $this->actingAs($this->userWithRole('sales'))->put(route('admin.inquiries.update', $inquiry), [
            'status' => 'answered', 'admin_reply' => 'موجود است', 'quoted_price_omr' => '55',
        ])->assertRedirect();

        $this->actingAs($customer)->get(route('account.inquiries.show', $inquiry))->assertOk()->assertSee('موجود است');
    }

    public function test_invalid_vin_is_rejected(): void
    {
        $this->actingAs($this->userWithRole(Role::CUSTOMER))
            ->post(route('inquiries.store'), ['part_name' => 'x', 'vin' => 'WDDIOQ', 'quantity' => 1])
            ->assertSessionHasErrors('vin');
    }

    public function test_cart_checkout_and_stock_handling(): void
    {
        $product = Product::query()->where('part_number', '34116860016')->firstOrFail(); // 10 in stock, 42 OMR
        $customer = $this->userWithRole(Role::CUSTOMER);

        $this->actingAs($customer)->postJson(route('cart.add', $product), ['quantity' => 3])->assertOk()->assertJson(['count' => 3]);
        $this->actingAs($customer)->post(route('checkout.store'), [
            'full_name' => 'تست', 'phone' => '۰۹۱۲۱۲۳۴۵۶۷', 'province' => 'تهران', 'city' => 'تهران', 'address' => 'خیابان آزادی',
        ])->assertRedirect();

        $order = Order::query()->with('items')->firstOrFail();
        $this->assertSame('09121234567', $order->phone);
        $this->assertSame(3, $order->items->first()->quantity);
        $this->assertSame(3 * 42 * 147000, $order->total_toman);
        $this->assertSame(10, $product->fresh()->quantity); // not deducted until confirmed

        $sales = $this->userWithRole('sales');
        $this->actingAs($sales)->put(route('admin.orders.update', $order), ['status' => 'confirmed']);
        $this->assertSame(7, $product->fresh()->quantity);

        $this->actingAs($sales)->put(route('admin.orders.update', $order), ['status' => 'paid']);
        $this->assertSame(7, $product->fresh()->quantity); // no double deduction

        $this->actingAs($sales)->put(route('admin.orders.update', $order), ['status' => 'cancelled']);
        $this->assertSame(10, $product->fresh()->quantity);
    }

    public function test_products_without_price_or_stock_cannot_be_bought(): void
    {
        $noPrice = Product::query()->whereNull('price_omr')->firstOrFail();
        $outOfStock = Product::query()->where('quantity', 0)->firstOrFail();

        $this->postJson(route('cart.add', $noPrice))->assertStatus(422);
        $this->postJson(route('cart.add', $outOfStock))->assertStatus(422);
    }

    public function test_customers_only_see_their_own_orders(): void
    {
        $owner = $this->userWithRole(Role::CUSTOMER);
        $product = Product::query()->where('part_number', '34116860016')->firstOrFail();
        $this->actingAs($owner)->post(route('cart.add', $product));
        $this->actingAs($owner)->post(route('checkout.store'), [
            'full_name' => 'x', 'phone' => '09121234567', 'province' => 'تهران', 'city' => 'تهران', 'address' => 'x',
        ]);
        $order = Order::query()->firstOrFail();

        $this->actingAs($this->userWithRole(Role::CUSTOMER))->get(route('account.orders.show', $order))->assertNotFound();
        $this->actingAs($owner)->get(route('account.orders.show', $order))->assertOk();
    }
}
