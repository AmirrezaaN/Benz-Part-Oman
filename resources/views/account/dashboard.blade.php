@extends('account.layout')

@section('title', 'حساب کاربری')

@section('account')
    <div class="card p-5 mb-5 bg-gradient-to-l from-sky-500/10 to-transparent">
        <h1 class="text-base font-black text-white">سلام {{ $user->first_name ?: $user->username }}، خوش آمدید</h1>
        <p class="text-xs text-slate-400 mt-1.5">عضو از {{ jdate($user->created_at, 'j F Y') }} · از اینجا استعلام‌ها و سفارش‌های خود را پیگیری کنید.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
        @foreach ([
            [$counts['inquiries'], 'کل استعلام‌ها', 'fa-magnifying-glass-plus', 'text-amber-300'],
            [$counts['answered'], 'استعلام پاسخ‌داده‌شده', 'fa-reply', 'text-emerald-400'],
            [$counts['orders'], 'کل سفارش‌ها', 'fa-box', 'text-sky-400'],
            [$counts['open_orders'], 'سفارش در جریان', 'fa-truck-fast', 'text-violet-300'],
        ] as [$value, $label, $icon, $color])
            <div class="card p-4">
                <i class="fa-solid {{ $icon }} {{ $color }}"></i>
                <div class="text-xl font-black text-white mt-2">{{ fa_num($value) }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    @if (! $user->email || ! $user->phone)
        <div class="card p-4 mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-amber-500/30!">
            <p class="text-xs text-amber-200"><i class="fa-solid fa-circle-info ml-1"></i>
                برای امنیت بیشتر و دریافت اطلاع‌رسانی‌ها، {{ ! $user->phone ? 'شماره موبایل' : 'ایمیل' }} خود را هم به حساب اضافه کنید.</p>
            <a href="{{ route('account.contact.edit', ! $user->phone ? 'phone' : 'email') }}" class="btn-ghost btn-sm">افزودن</a>
        </div>
    @endif

    <div class="grid xl:grid-cols-2 gap-5">
        <div class="card">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800">
                <h2 class="text-sm font-bold text-white">آخرین استعلام‌ها</h2>
                <a href="{{ route('inquiries.create') }}" class="text-[11px] text-sky-400 font-bold"><i class="fa-solid fa-plus"></i> استعلام جدید</a>
            </div>
            @forelse ($inquiries as $inq)
                <a href="{{ route('account.inquiries.show', $inq) }}" class="flex items-center justify-between gap-3 px-5 py-3 border-b border-slate-800/60 last:border-0 hover:bg-slate-800/30">
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-white truncate">{{ $inq->part_name }}</div>
                        <div class="text-[10px] text-slate-500 mt-0.5 font-mono">{{ $inq->code }} · {{ jdate($inq->created_at) }}</div>
                    </div>
                    <x-status-badge :color="$inq->statusColor()" :label="$inq->statusLabel()" />
                </a>
            @empty
                <p class="text-xs text-slate-500 p-5">هنوز استعلامی ثبت نکرده‌اید.</p>
            @endforelse
        </div>

        <div class="card">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800">
                <h2 class="text-sm font-bold text-white">آخرین سفارش‌ها</h2>
                <a href="{{ route('account.orders') }}" class="text-[11px] text-sky-400 font-bold">همه</a>
            </div>
            @forelse ($orders as $order)
                <a href="{{ route('account.orders.show', $order) }}" class="flex items-center justify-between gap-3 px-5 py-3 border-b border-slate-800/60 last:border-0 hover:bg-slate-800/30">
                    <div>
                        <div class="text-xs font-bold text-white font-mono">{{ $order->code }}</div>
                        <div class="text-[10px] text-slate-500 mt-0.5">{{ jdate($order->created_at) }} · {{ format_toman($order->total_toman) }} تومان</div>
                    </div>
                    <x-status-badge :color="$order->statusColor()" :label="$order->statusLabel()" />
                </a>
            @empty
                <p class="text-xs text-slate-500 p-5">هنوز سفارشی ثبت نکرده‌اید.</p>
            @endforelse
        </div>
    </div>
@endsection
