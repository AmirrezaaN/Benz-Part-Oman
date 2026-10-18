@extends('admin.layout')

@section('title', 'آمار سایت')

@section('content')
    <div class="flex items-center justify-between gap-3 flex-wrap mb-5">
        <p class="text-xs text-slate-400">آمار {{ fa_num($days) }} روز گذشته</p>
        <div class="flex gap-1 bg-slate-900 border border-slate-800 rounded-xl p-1 text-xs">
            @foreach ([7 => '۷ روز', 30 => '۳۰ روز', 90 => '۹۰ روز'] as $d => $label)
                <a href="{{ route('admin.stats', ['days' => $d]) }}" class="px-3 py-1.5 rounded-lg {{ $days === $d ? 'bg-sky-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-3">
        @foreach ([
            ['بازدیدکننده یکتا', $totals['visitors']], ['بازدید صفحات', $totals['page_views']], ['کاربر جدید', $totals['new_users']],
            ['استعلام', $totals['inquiries']], ['سفارش', $totals['orders']], ['فروش قطعی (تومان)', $totals['revenue']],
            ['کل کاربران', $totals['all_users']], ['کل محصولات', $totals['all_products']],
        ] as [$label, $value])
            <div class="card p-4">
                <div class="text-lg font-black text-white">{{ fa_num(number_format($value)) }}</div>
                <div class="text-[10px] text-slate-500 mt-1">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    <script type="application/json" id="stats-data">@json($chart)</script>

    <div class="grid xl:grid-cols-2 gap-5 mt-5">
        <div class="card p-4"><h2 class="text-sm font-bold text-white mb-3">بازدید سایت</h2><div class="h-64"><canvas id="chart-visits"></canvas></div></div>
        <div class="card p-4"><h2 class="text-sm font-bold text-white mb-3">ثبت‌نام، استعلام و سفارش</h2><div class="h-64"><canvas id="chart-activity"></canvas></div></div>
        <div class="card p-4 xl:col-span-2"><h2 class="text-sm font-bold text-white mb-3">فروش قطعی روزانه (پرداخت‌شده، در حال ارسال، تحویل‌شده)</h2><div class="h-56"><canvas id="chart-revenue"></canvas></div></div>
    </div>

    <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-5 mt-5">
        <div class="card p-4">
            <h2 class="text-sm font-bold text-white mb-3">وضعیت استعلام‌ها</h2>
            <div class="h-52"><canvas data-doughnut="{{ json_encode(['labels' => collect(config('shop.inquiry_statuses'))->pluck('label')->values(), 'values' => collect(config('shop.inquiry_statuses'))->keys()->map(fn ($k) => (int) ($inquiryStatus[$k] ?? 0))]) }}"></canvas></div>
        </div>
        <div class="card p-4">
            <h2 class="text-sm font-bold text-white mb-3">وضعیت سفارش‌ها</h2>
            <div class="h-52"><canvas data-doughnut="{{ json_encode(['labels' => collect(config('shop.order_statuses'))->pluck('label')->values(), 'values' => collect(config('shop.order_statuses'))->keys()->map(fn ($k) => (int) ($orderStatus[$k] ?? 0))]) }}"></canvas></div>
        </div>
        <div class="card p-4">
            <h2 class="text-sm font-bold text-white mb-3">کاربران بر اساس نقش</h2>
            <ul class="space-y-2 text-xs">
                @foreach ($roles as $role)
                    <li class="flex justify-between"><span class="text-slate-400">{{ $role->label }}</span><span class="text-white font-bold">{{ fa_num($role->users_count) }}</span></li>
                @endforeach
            </ul>
        </div>
        <div class="card p-4">
            <h2 class="text-sm font-bold text-white mb-3">محصولات هر بخش</h2>
            <ul class="space-y-2 text-xs max-h-52 overflow-y-auto">
                @foreach ($categories as $cat)
                    <li class="flex justify-between gap-2"><span class="text-slate-400 truncate">{{ $cat->name }}</span><span class="text-white font-bold">{{ fa_num($cat->products_count) }}</span></li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-5 mt-5">
        <div class="card overflow-x-auto">
            <h2 class="text-sm font-bold text-white px-4 py-3.5">پربازدیدترین محصولات</h2>
            <table class="table-base">
                <thead><tr><th>محصول</th><th>بازدید</th><th>موجودی</th></tr></thead>
                <tbody>
                    @foreach ($topProducts as $p)
                        <tr><td class="max-w-xs truncate">{{ $p->name }}</td><td>{{ fa_num($p->views) }}</td><td>{{ $p->quantity === null ? '—' : fa_num($p->quantity) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card overflow-x-auto">
            <h2 class="text-sm font-bold text-white px-4 py-3.5">ناموجود / رو به اتمام (۲ عدد و کمتر)</h2>
            <table class="table-base">
                <thead><tr><th>محصول</th><th>موجودی</th><th></th></tr></thead>
                <tbody>
                    @forelse ($lowStock as $p)
                        <tr>
                            <td class="max-w-xs truncate">{{ $p->name }}</td>
                            <td class="{{ $p->quantity === 0 ? 'text-rose-400' : 'text-amber-300' }} font-bold">{{ fa_num($p->quantity) }}</td>
                            <td>@can('products.edit')<a href="{{ route('admin.products.edit', $p) }}" class="text-sky-400">ویرایش</a>@endcan</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-slate-500 text-center py-6">موردی نیست.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
