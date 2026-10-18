@extends('layouts.app')

@section('content')
    @php($u = auth()->user())
    <div class="container-x py-8">
        <div class="grid lg:grid-cols-4 gap-6">
            <aside class="card p-4 h-fit lg:sticky lg:top-40">
                <div class="flex items-center gap-3 pb-4 mb-3 border-b border-slate-800">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-sky-500 to-blue-600 text-white flex items-center justify-center text-lg font-black">{{ mb_substr($u->full_name, 0, 1) }}</span>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-white truncate">{{ $u->full_name }}</div>
                        <div class="text-[11px] text-slate-500 font-mono truncate" dir="ltr">{{ '@'.$u->username }}</div>
                        <x-status-badge class="mt-1" :color="$u->canAccessAdmin() ? 'amber' : 'sky'" :label="$u->role?->label ?? 'مشتری'" />
                    </div>
                </div>
                <nav class="grid grid-cols-2 lg:grid-cols-1 gap-1 text-xs">
                    @foreach ([
                        ['account.dashboard', 'fa-gauge', 'پیشخوان', 'account.dashboard'],
                        ['account.inquiries', 'fa-magnifying-glass-plus', 'استعلام‌های من', 'account.inquiries*'],
                        ['account.orders', 'fa-box', 'سفارش‌های من', 'account.orders*'],
                        ['account.profile', 'fa-user-gear', 'ویرایش حساب', 'account.profile*|account.contact*'],
                    ] as [$route, $icon, $label, $pattern])
                        @php($active = request()->routeIs(...explode('|', $pattern)))
                        <a href="{{ route($route) }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ $active ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'text-slate-300 hover:bg-slate-800' }}">
                            <i class="fa-solid {{ $icon }} w-4"></i> {{ $label }}
                        </a>
                    @endforeach
                    @if ($u->canAccessAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-amber-300 hover:bg-slate-800"><i class="fa-solid fa-gauge-high w-4"></i> پنل مدیریت</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="col-span-2 lg:col-span-1">
                        @csrf
                        <button class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-rose-300 hover:bg-slate-800"><i class="fa-solid fa-arrow-right-from-bracket w-4"></i> خروج از حساب</button>
                    </form>
                </nav>
            </aside>

            <div class="lg:col-span-3 min-w-0">
                @yield('account')
            </div>
        </div>
    </div>
@endsection
