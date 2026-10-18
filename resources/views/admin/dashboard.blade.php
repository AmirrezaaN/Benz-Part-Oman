@extends('admin.layout')

@section('title', 'پیشخوان')

@section('content')
    @php
        $colors = [
            'sky' => 'bg-sky-500/10 text-sky-400 border-sky-500/20', 'amber' => 'bg-amber-500/10 text-amber-300 border-amber-500/20',
            'violet' => 'bg-violet-500/10 text-violet-300 border-violet-500/20', 'emerald' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            'indigo' => 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20', 'rose' => 'bg-rose-500/10 text-rose-300 border-rose-500/20',
        ];
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
        @foreach ($cards as $card)
            <a href="{{ $card['url'] ?? '#' }}" class="card p-4 hover:border-slate-700 transition">
                <span class="w-10 h-10 rounded-xl border flex items-center justify-center {{ $colors[$card['color']] }}"><i class="fa-solid {{ $card['icon'] }}"></i></span>
                <div class="text-2xl font-black text-white mt-3">{{ fa_num(number_format($card['value'])) }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">{{ $card['label'] }}</div>
            </a>
        @endforeach
    </div>

    <div class="flex flex-wrap gap-2 mt-5">
        @can('products.create')<a href="{{ route('admin.products.create') }}" class="btn-primary btn-sm"><i class="fa-solid fa-plus"></i> محصول جدید</a>@endcan
        @can('settings.manage')<a href="{{ route('admin.settings.edit') }}" class="btn-ghost btn-sm"><i class="fa-solid fa-coins text-sky-400"></i> نرخ ریال عمان: {{ format_toman(exchange_rate()) }} تومان</a>@endcan
        @can('stats.view')<a href="{{ route('admin.stats') }}" class="btn-ghost btn-sm"><i class="fa-solid fa-chart-line text-sky-400"></i> آمار کامل</a>@endcan
    </div>

    <div class="grid xl:grid-cols-3 gap-5 mt-5">
        @can('inquiries.view')
            <div class="card">
                <div class="flex items-center justify-between px-4 py-3.5 border-b border-slate-800">
                    <h2 class="text-sm font-bold text-white">آخرین استعلام‌ها</h2>
                    <a href="{{ route('admin.inquiries.index') }}" class="text-[11px] text-sky-400">همه</a>
                </div>
                @forelse ($recentInquiries as $inq)
                    <a href="{{ route('admin.inquiries.show', $inq) }}" class="flex items-center justify-between gap-3 px-4 py-3 border-b border-slate-800/60 last:border-0 hover:bg-slate-800/30 text-xs">
                        <div class="min-w-0">
                            <div class="font-bold text-white truncate">{{ $inq->part_name }}</div>
                            <div class="text-[10px] text-slate-500 mt-0.5">{{ $inq->user?->full_name }} · {{ jdate($inq->created_at, 'm/d H:i') }}</div>
                        </div>
                        <x-status-badge :color="$inq->statusColor()" :label="$inq->statusLabel()" />
                    </a>
                @empty
                    <p class="p-4 text-xs text-slate-500">استعلامی ثبت نشده است.</p>
                @endforelse
            </div>
        @endcan

        @can('orders.view')
            <div class="card">
                <div class="flex items-center justify-between px-4 py-3.5 border-b border-slate-800">
                    <h2 class="text-sm font-bold text-white">آخرین سفارش‌ها</h2>
                    <a href="{{ route('admin.orders.index') }}" class="text-[11px] text-sky-400">همه</a>
                </div>
                @forelse ($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between gap-3 px-4 py-3 border-b border-slate-800/60 last:border-0 hover:bg-slate-800/30 text-xs">
                        <div>
                            <div class="font-bold text-white font-mono">{{ $order->code }}</div>
                            <div class="text-[10px] text-slate-500 mt-0.5">{{ $order->full_name }} · {{ format_toman($order->total_toman) }} ت</div>
                        </div>
                        <x-status-badge :color="$order->statusColor()" :label="$order->statusLabel()" />
                    </a>
                @empty
                    <p class="p-4 text-xs text-slate-500">سفارشی ثبت نشده است.</p>
                @endforelse
            </div>
        @endcan

        @can('users.view')
            <div class="card">
                <div class="flex items-center justify-between px-4 py-3.5 border-b border-slate-800">
                    <h2 class="text-sm font-bold text-white">کاربران جدید</h2>
                    <a href="{{ route('admin.users.index') }}" class="text-[11px] text-sky-400">همه</a>
                </div>
                @forelse ($recentUsers as $user)
                    <a href="{{ route('admin.users.show', $user) }}" class="flex items-center justify-between gap-3 px-4 py-3 border-b border-slate-800/60 last:border-0 hover:bg-slate-800/30 text-xs">
                        <div class="min-w-0">
                            <div class="font-bold text-white truncate">{{ $user->full_name }}</div>
                            <div class="text-[10px] text-slate-500 mt-0.5 font-mono" dir="ltr">{{ $user->email ?? $user->phone }}</div>
                        </div>
                        <span class="text-[10px] text-slate-500">{{ jdate($user->created_at) }}</span>
                    </a>
                @empty
                    <p class="p-4 text-xs text-slate-500">کاربری وجود ندارد.</p>
                @endforelse
            </div>
        @endcan
    </div>
@endsection
