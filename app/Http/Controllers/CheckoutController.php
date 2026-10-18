<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Cart;
use App\Support\Identifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Checkout creates an order request. Payment is arranged by the sales team after the
 * order is confirmed (an online payment gateway can be plugged in later).
 */
class CheckoutController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function index(Request $request): View|RedirectResponse
    {
        $lines = $this->cart->lines();
        if ($lines->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'سبد خرید شما خالی است.');
        }

        return view('checkout.index', [
            'lines' => $lines,
            'totalOmr' => $lines->sum('omr'),
            'totalToman' => $lines->sum('toman'),
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $lines = $this->cart->lines();
        if ($lines->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'سبد خرید شما خالی است.');
        }

        $request->merge(['phone' => Identifier::normalizePhone((string) $request->input('phone')) ?? $request->input('phone')]);

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'regex:/^09\d{9}$/'],
            'province' => ['required', Rule::in(config('shop.provinces'))],
            'city' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:500'],
            'postal_code' => ['nullable', 'digits:10'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
            'save_address' => ['nullable', 'boolean'],
        ], ['phone.regex' => 'شماره موبایل معتبر نیست (مثال: 09121234567).'], [
            'full_name' => 'نام گیرنده', 'phone' => 'موبایل گیرنده', 'province' => 'استان', 'city' => 'شهر',
            'address' => 'آدرس', 'postal_code' => 'کد پستی', 'customer_note' => 'توضیحات',
        ]);

        $user = $request->user();

        $order = DB::transaction(function () use ($data, $lines, $user) {
            $order = new Order(collect($data)->except('save_address')->map(fn ($v) => is_string($v) ? strip_tags($v) : $v)->all());
            $order->user_id = $user->id;
            $order->status = 'pending';
            $order->items_count = $lines->sum('quantity');
            $order->subtotal_omr = $lines->sum('omr');
            $order->total_toman = $lines->sum('toman');
            $order->exchange_rate = exchange_rate();
            $order->save();

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'part_number' => $line['product']->part_number,
                    'quantity' => $line['quantity'],
                    'unit_price_omr' => $line['product']->price_omr,
                    'unit_price_toman' => $line['product']->tomanPrice(),
                ]);
            }

            return $order;
        });

        if (! empty($data['save_address'])) {
            $user->fill(collect($data)->only(['province', 'city', 'address', 'postal_code'])->all())->save();
        }

        $this->cart->clear();

        return redirect()->route('account.orders.show', $order)
            ->with('success', 'سفارش شما با کد '.$order->code.' ثبت شد و در حال استعلام موجودی از انبار عمان هستیم. نتیجه را در همین صفحه و با تماس کارشناسان اطلاع می‌دهیم.');
    }
}
