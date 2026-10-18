<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = now()->toDateString();

        $cards = [
            ['label' => 'بازدیدکننده امروز', 'value' => Visit::query()->where('date', $today)->count(), 'icon' => 'fa-eye', 'color' => 'sky', 'show' => Gate::allows('stats.view')],
            ['label' => 'استعلام‌های باز', 'value' => Inquiry::query()->whereIn('status', ['pending', 'reviewing'])->count(), 'icon' => 'fa-magnifying-glass-plus', 'color' => 'amber', 'show' => Gate::allows('inquiries.view'), 'url' => route('admin.inquiries.index', ['status' => 'pending'])],
            ['label' => 'سفارش‌های منتظر استعلام موجودی', 'value' => Order::query()->where('status', 'pending')->count(), 'icon' => 'fa-cart-shopping', 'color' => 'violet', 'show' => Gate::allows('orders.view'), 'url' => route('admin.orders.index', ['status' => 'pending'])],
            ['label' => 'کاربران', 'value' => User::query()->count(), 'icon' => 'fa-users', 'color' => 'emerald', 'show' => Gate::allows('users.view'), 'url' => route('admin.users.index')],
            ['label' => 'محصولات فعال', 'value' => Product::query()->active()->count(), 'icon' => 'fa-box', 'color' => 'indigo', 'show' => Gate::allows('products.view'), 'url' => route('admin.products.index')],
            ['label' => 'ناموجود / کم موجود', 'value' => Product::query()->whereNotNull('quantity')->where('quantity', '<=', 2)->count(), 'icon' => 'fa-triangle-exclamation', 'color' => 'rose', 'show' => Gate::allows('products.view'), 'url' => route('admin.products.index', ['stock' => 'low'])],
        ];

        return view('admin.dashboard', [
            'cards' => array_filter($cards, fn ($c) => $c['show']),
            'recentInquiries' => Gate::allows('inquiries.view') ? Inquiry::query()->with('user')->latest('id')->take(6)->get() : collect(),
            'recentOrders' => Gate::allows('orders.view') ? Order::query()->with('user')->latest('id')->take(6)->get() : collect(),
            'recentUsers' => Gate::allows('users.view') ? User::query()->with('role')->latest('id')->take(6)->get() : collect(),
        ]);
    }
}
