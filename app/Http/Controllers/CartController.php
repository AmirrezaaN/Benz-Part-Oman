<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function index(): View
    {
        $lines = $this->cart->lines();

        return view('cart.index', [
            'lines' => $lines,
            'totalOmr' => $lines->sum('omr'),
            'totalToman' => $lines->sum('toman'),
        ]);
    }

    public function add(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $qty = (int) ($request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:999']])['quantity'] ?? 1);

        if (! $product->isPurchasable()) {
            $message = 'این قطعه در حال حاضر قابل خرید آنلاین نیست. لطفاً استعلام بگیرید.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 422)
                : back()->with('error', $message);
        }

        $this->cart->add($product, max(1, $qty));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'به سبد خرید اضافه شد.', 'count' => $this->cart->count()]);
        }

        return back()->with('success', '«'.$product->name.'» به سبد خرید اضافه شد.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $qty = (int) $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:999']])['quantity'];
        $this->cart->set($product, $qty);

        return back();
    }

    public function remove(Product $product): RedirectResponse
    {
        $this->cart->remove($product->id);

        return back()->with('success', 'از سبد خرید حذف شد.');
    }
}
