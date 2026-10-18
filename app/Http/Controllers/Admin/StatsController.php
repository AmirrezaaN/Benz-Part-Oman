<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\Visit;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatsController extends Controller
{
    public function index(Request $request): View
    {
        $days = in_array((int) $request->query('days'), [7, 30, 90], true) ? (int) $request->query('days') : 30;
        $from = now()->subDays($days - 1)->startOfDay();

        // Build an empty series for every day in the range so the chart has no gaps
        $period = collect(CarbonPeriod::create($from, now()->startOfDay()))->map->toDateString();
        $series = fn ($rows) => $period->map(fn ($d) => (float) ($rows[$d] ?? 0))->values();

        $perDay = fn ($query, string $column = 'created_at', string $aggregate = 'COUNT(*)') => $query
            ->where($column, '>=', $from)
            ->selectRaw("DATE({$column}) as d, {$aggregate} as v")
            ->groupBy('d')->pluck('v', 'd');

        $visits = Visit::query()->where('date', '>=', $from->toDateString())
            ->selectRaw('date as d, COUNT(*) as uniques, SUM(hits) as hits')->groupBy('date')->get()
            ->mapWithKeys(fn ($r) => [substr((string) $r->d, 0, 10) => $r]);

        $chart = [
            'labels' => $period->map(fn ($d) => jdate(\Carbon\Carbon::parse($d), 'm/d'))->values(),
            'uniques' => $period->map(fn ($d) => (int) ($visits[$d]->uniques ?? 0))->values(),
            'hits' => $period->map(fn ($d) => (int) ($visits[$d]->hits ?? 0))->values(),
            'users' => $series($perDay(User::query())),
            'inquiries' => $series($perDay(Inquiry::query())),
            'orders' => $series($perDay(Order::query())),
            'revenue' => $series($perDay(Order::query()->whereIn('status', ['paid', 'shipping', 'delivered']), 'created_at', 'SUM(total_toman)')),
        ];

        $totals = [
            'visitors' => $chart['uniques']->sum(),
            'page_views' => $chart['hits']->sum(),
            'new_users' => $chart['users']->sum(),
            'inquiries' => $chart['inquiries']->sum(),
            'orders' => $chart['orders']->sum(),
            'revenue' => $chart['revenue']->sum(),
            'all_users' => User::query()->count(),
            'all_products' => Product::query()->count(),
        ];

        $inquiryStatus = Inquiry::query()->select('status', DB::raw('COUNT(*) as c'))->groupBy('status')->pluck('c', 'status');
        $orderStatus = Order::query()->select('status', DB::raw('COUNT(*) as c'))->groupBy('status')->pluck('c', 'status');

        return view('admin.stats', [
            'days' => $days,
            'chart' => $chart,
            'totals' => $totals,
            'inquiryStatus' => $inquiryStatus,
            'orderStatus' => $orderStatus,
            'topProducts' => Product::query()->orderByDesc('views')->take(10)->get(['id', 'name', 'views', 'quantity']),
            'lowStock' => Product::query()->whereNotNull('quantity')->where('quantity', '<=', 2)->orderBy('quantity')->take(10)->get(['id', 'name', 'quantity']),
            'categories' => Category::query()->withCount('products')->orderByDesc('products_count')->get(['id', 'name']),
            'roles' => Role::query()->withCount('users')->get(),
        ]);
    }
}
