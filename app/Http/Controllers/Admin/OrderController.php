<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()->with('user:id,first_name,last_name,username,internal_code')
            ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($request->query('q'), fn ($q, $v) => $q->where(fn ($q) => $q->where('code', 'like', "%{$v}%")
                ->orWhere('full_name', 'like', "%{$v}%")->orWhere('phone', 'like', "%{$v}%")))
            ->latest('id')->paginate(20)->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'counts' => Order::query()->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', ['order' => $order->load('user', 'items.product')]);
    }

    /**
     * Stock is deducted when an order enters a "holding" status (confirmed and later)
     * and returned if it is cancelled afterwards.
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(config('shop.order_statuses')))],
            'admin_note' => ['nullable', 'string', 'max:3000'],
        ], [], ['status' => 'وضعیت', 'admin_note' => 'یادداشت']);

        $holding = config('shop.stock_holding_statuses');

        DB::transaction(function () use ($order, $data, $holding) {
            $order->refresh()->load('items');

            if (in_array($data['status'], $holding, true) && ! $order->stock_deducted) {
                foreach ($order->items as $item) {
                    $product = $item->product_id ? Product::withTrashed()->lockForUpdate()->find($item->product_id) : null;
                    if ($product && $product->quantity !== null) {
                        if ($product->quantity < $item->quantity) {
                            throw ValidationException::withMessages([
                                'status' => 'موجودی «'.$product->name.'» کافی نیست (موجودی: '.$product->quantity.').',
                            ]);
                        }
                        $product->decrement('quantity', $item->quantity);
                    }
                }
                $order->stock_deducted = true;
            }

            if ($data['status'] === 'cancelled' && $order->stock_deducted) {
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        Product::withTrashed()->whereKey($item->product_id)->whereNotNull('quantity')->increment('quantity', $item->quantity);
                    }
                }
                $order->stock_deducted = false;
            }

            $order->status = $data['status'];
            $order->admin_note = $data['admin_note'] ?? null;
            $order->save();
        });

        ActivityLog::record('order.updated', $order, 'سفارش '.$order->code.' ← '.$order->statusLabel());

        return back()->with('success', 'وضعیت سفارش به‌روزرسانی شد.');
    }
}
