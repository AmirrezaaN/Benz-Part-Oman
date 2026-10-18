<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Chassis;
use App\Models\Product;
use App\Models\Role;
use Database\Seeders\CatalogSeeder;
use Database\Seeders\DemoProductSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    public function test_customers_cannot_open_the_admin_panel(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->actingAs($this->userWithRole(Role::CUSTOMER))->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_super_admin_can_open_every_admin_page(): void
    {
        $this->seed([CatalogSeeder::class, DemoProductSeeder::class]);
        $owner = $this->userWithRole(Role::SUPER_ADMIN);
        $product = Product::query()->first();

        $pages = [
            'admin.dashboard', 'admin.stats', 'admin.products.index', 'admin.products.create', 'admin.categories.index',
            'admin.categories.create', 'admin.brands.index', 'admin.brands.create', 'admin.car-models.index',
            'admin.car-models.create', 'admin.chassis.index', 'admin.chassis.create', 'admin.inquiries.index',
            'admin.orders.index', 'admin.users.index', 'admin.roles.index', 'admin.roles.create', 'admin.banners.index',
            'admin.banners.create', 'admin.settings.edit', 'admin.activity.index',
        ];
        foreach ($pages as $page) {
            $this->actingAs($owner)->get(route($page))->assertOk();
        }
        $this->actingAs($owner)->get(route('admin.products.edit', $product))->assertOk();
        $this->actingAs($owner)->get(route('admin.users.show', $owner))->assertOk();
    }

    public function test_admin_role_cannot_manage_roles(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);

        $this->actingAs($admin)->get(route('admin.products.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.roles.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.activity.index'))->assertForbidden();
    }

    public function test_sales_role_only_sees_its_sections(): void
    {
        $sales = $this->userWithRole('sales');

        $this->actingAs($sales)->get(route('admin.products.index'))->assertOk();
        $this->actingAs($sales)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($sales)->get(route('admin.settings.edit'))->assertForbidden();
    }

    public function test_role_assignment_cannot_escalate_privileges(): void
    {
        $support = $this->roleWith(['admin.access', 'users.view', 'users.change-role'], 'support');
        $actor = $this->userWithRole('support');
        $customer = $this->userWithRole(Role::CUSTOMER);
        $adminRole = Role::query()->where('name', Role::ADMIN)->first();
        $superRole = Role::query()->where('name', Role::SUPER_ADMIN)->first();

        // Cannot hand out a role with more permissions than they have
        $this->actingAs($actor)->put(route('admin.users.role', $customer), ['role_id' => $adminRole->id])->assertForbidden();
        // Nobody can hand out super-admin through the panel
        $this->actingAs($this->userWithRole(Role::SUPER_ADMIN))->put(route('admin.users.role', $customer), ['role_id' => $superRole->id])->assertForbidden();
        // Allowed: a role within their own permissions
        $this->actingAs($actor)->put(route('admin.users.role', $customer), ['role_id' => $support->id])->assertRedirect();
        $this->assertSame('support', $customer->fresh()->role->name);
    }

    public function test_nobody_can_modify_the_super_admin(): void
    {
        $owner = $this->userWithRole(Role::SUPER_ADMIN);
        $admin = $this->userWithRole(Role::ADMIN);
        $customerRole = Role::query()->where('name', Role::CUSTOMER)->first();

        $this->actingAs($admin)->put(route('admin.users.role', $owner), ['role_id' => $customerRole->id])->assertForbidden();
        $this->actingAs($admin)->put(route('admin.users.ban', $owner))->assertForbidden();
        $this->assertTrue($owner->fresh()->isSuperAdmin());
    }

    public function test_users_are_addressed_by_internal_code_not_id(): void
    {
        $owner = $this->userWithRole(Role::SUPER_ADMIN);
        $customer = $this->userWithRole(Role::CUSTOMER);

        $this->actingAs($owner)->get('/admin/users/'.$customer->id)->assertNotFound();
        $this->actingAs($owner)->get('/admin/users/'.$customer->internal_code)->assertOk();
    }

    public function test_banning_a_user(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $customer = $this->userWithRole(Role::CUSTOMER);

        $this->actingAs($admin)->put(route('admin.users.ban', $customer), ['ban_reason' => 'spam'])->assertRedirect();
        $this->assertTrue($customer->fresh()->isBanned());
    }

    public function test_creating_a_product_with_chassis_and_images(): void
    {
        Storage::fake('public');
        $this->seed(CatalogSeeder::class);
        $admin = $this->userWithRole(Role::ADMIN);
        $chassis = Chassis::query()->whereIn('code', ['W213', 'W205'])->pluck('id')->all();

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'سنسور ABS جلو',
            'part_number' => 'A 213 905 50 00',
            'brand_id' => Brand::query()->where('name', 'Mercedes-Benz')->value('id'),
            'price_omr' => '۱۲.۵',
            'quantity' => '4',
            'year_from' => '2016',
            'country_of_origin' => 'Germany',
            'condition' => 'new',
            'chassis' => $chassis,
            'is_active' => '1',
            'images' => [UploadedFile::fake()->image('abs.jpg', 1200, 900)],
        ])->assertRedirect();

        $product = Product::query()->where('part_number', 'A2139055000')->firstOrFail();
        $this->assertSame('12.500', $product->price_omr);
        $this->assertCount(2, $product->chassis);
        $this->assertCount(1, $product->images);
        $this->assertStringEndsWith('.webp', $product->images->first()->path);
        Storage::disk('public')->assertExists($product->images->first()->path);
        $this->assertDatabaseHas('activity_logs', ['action' => 'product.created']);
    }

    public function test_only_name_is_required_for_a_product(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);

        $this->actingAs($admin)->post(route('admin.products.store'), ['name' => 'قطعه بدون اطلاعات', 'is_active' => '1'])->assertRedirect();
        $product = Product::query()->where('name', 'قطعه بدون اطلاعات')->firstOrFail();

        $this->assertFalse($product->isPurchasable());
        $this->get(route('products.show', $product))->assertOk()->assertSee('قیمت با استعلام');
    }

    public function test_changing_the_exchange_rate_reprices_products(): void
    {
        $owner = $this->userWithRole(Role::SUPER_ADMIN);
        $product = Product::query()->create(['name' => 'Test', 'price_omr' => 10, 'is_active' => true]);

        $this->actingAs($owner)->put(route('admin.settings.update'), [
            'site_name' => 'Benz', 'exchange_rate' => '200,000', 'whatsapp_number' => '+968 9000 0000',
        ])->assertSessionHasNoErrors();

        $this->assertSame(2_000_000, $product->fresh()->tomanPrice());
    }
}
