<!DOCTYPE html>
<html lang="fa" dir="rtl" data-currency="toman">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') | پنل مدیریت {{ setting('site_name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen">
    @php
        $u = auth()->user();
        $nav = [
            ['پیشخوان', 'fa-gauge-high', 'admin.dashboard', 'admin.dashboard', 'admin.access'],
            ['آمار سایت', 'fa-chart-line', 'admin.stats', 'admin.stats', 'stats.view'],
            ['—', 'فروشگاه'],
            ['محصولات', 'fa-box', 'admin.products.index', 'admin.products.*', 'products.view'],
            ['بخش‌ها (دسته‌بندی)', 'fa-layer-group', 'admin.categories.index', 'admin.categories.*', 'catalog.manage'],
            ['برندها', 'fa-copyright', 'admin.brands.index', 'admin.brands.*', 'catalog.manage'],
            ['مدل‌های خودرو', 'fa-car', 'admin.car-models.index', 'admin.car-models.*', 'catalog.manage'],
            ['شاسی‌ها', 'fa-barcode', 'admin.chassis.index', 'admin.chassis.*', 'catalog.manage'],
            ['—', 'مشتریان'],
            ['استعلام‌ها', 'fa-magnifying-glass-plus', 'admin.inquiries.index', 'admin.inquiries.*', 'inquiries.view'],
            ['سفارش‌ها', 'fa-cart-shopping', 'admin.orders.index', 'admin.orders.*', 'orders.view'],
            ['کاربران', 'fa-users', 'admin.users.index', 'admin.users.*', 'users.view'],
            ['—', 'تنظیمات'],
            ['نقش‌ها و دسترسی‌ها', 'fa-user-shield', 'admin.roles.index', 'admin.roles.*', 'super-admin'],
            ['اسلایدر صفحه اصلی', 'fa-images', 'admin.banners.index', 'admin.banners.*', 'banners.manage'],
            ['تنظیمات و نرخ ارز', 'fa-sliders', 'admin.settings.edit', 'admin.settings.*', 'settings.manage'],
            ['گزارش فعالیت‌ها', 'fa-clock-rotate-left', 'admin.activity.index', 'admin.activity.*', 'super-admin'],
        ];
        $pending = [
            'admin.inquiries.index' => \Illuminate\Support\Facades\Gate::allows('inquiries.view') ? \App\Models\Inquiry::query()->where('status', 'pending')->count() : 0,
            'admin.orders.index' => \Illuminate\Support\Facades\Gate::allows('orders.view') ? \App\Models\Order::query()->where('status', 'pending')->count() : 0,
        ];
    @endphp

    {{-- Sidebar --}}
    <div data-drawer-overlay="admin-nav" data-open="false" class="drawer-overlay fixed inset-0 z-40 bg-slate-950/70 lg:hidden"></div>
    <aside data-drawer="admin-nav" data-open="false"
           class="drawer fixed top-0 bottom-0 right-0 z-50 w-64 bg-navy-900 border-l border-slate-800 overflow-y-auto lg:transform-none!">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-5 h-16 border-b border-slate-800">
            <span class="w-9 h-9 rounded-xl bg-slate-950 border border-slate-700 text-sky-400 flex items-center justify-center"><i class="fa-solid fa-certificate"></i></span>
            <span>
                <span class="block text-sm font-black text-white leading-tight">بنز پارت <span class="text-sky-400">عمان</span></span>
                <span class="block text-[10px] text-slate-500">پنل مدیریت</span>
            </span>
        </a>
        <nav class="p-3 space-y-0.5 text-xs">
            @foreach ($nav as $item)
                @if ($item[0] === '—')
                    <div class="px-3 pt-4 pb-1.5 text-[10px] font-bold text-slate-600">{{ $item[1] }}</div>
                @elseif (\Illuminate\Support\Facades\Gate::allows($item[4]))
                    @php($active = request()->routeIs($item[3]))
                    <a href="{{ route($item[2]) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ $active ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'text-slate-300 hover:bg-slate-800/70' }}">
                        <i class="fa-solid {{ $item[1] }} w-4 text-center {{ $active ? '' : 'text-slate-500' }}"></i>
                        <span class="flex-1">{{ $item[0] }}</span>
                        @if (($pending[$item[2]] ?? 0) > 0)
                            <span class="min-w-5 h-5 px-1 rounded-full bg-amber-500 text-slate-950 text-[10px] font-black flex items-center justify-center">{{ fa_num($pending[$item[2]]) }}</span>
                        @endif
                    </a>
                @endif
            @endforeach
        </nav>
    </aside>

    <div class="lg:mr-64 min-h-screen flex flex-col">
        {{-- Top bar --}}
        <header class="sticky top-0 z-30 glass-panel h-16 flex items-center justify-between gap-3 px-4 sm:px-6">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" data-drawer-open="admin-nav" class="lg:hidden w-9 h-9 rounded-lg bg-slate-900 border border-slate-700" aria-label="منو"><i class="fa-solid fa-bars"></i></button>
                <h1 class="text-sm font-black text-white truncate">@yield('title')</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" target="_blank" class="btn-ghost btn-sm"><i class="fa-solid fa-arrow-up-right-from-square"></i><span class="hidden sm:inline">مشاهده سایت</span></a>
                <div class="relative" data-dropdown>
                    <button type="button" data-dropdown-toggle class="flex items-center gap-2 h-9 px-2.5 rounded-lg bg-slate-900 border border-slate-700 text-xs">
                        <span class="w-6 h-6 rounded-md bg-amber-500/15 text-amber-300 flex items-center justify-center font-black">{{ mb_substr($u->full_name, 0, 1) }}</span>
                        <span class="hidden sm:inline max-w-28 truncate">{{ $u->full_name }}</span>
                        <i class="fa-solid fa-chevron-down text-[9px] text-slate-500"></i>
                    </button>
                    <div data-dropdown-menu hidden class="absolute left-0 mt-2 w-52 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl overflow-hidden text-xs z-50">
                        <div class="px-4 py-3 border-b border-slate-800">
                            <div class="text-white font-bold truncate">{{ $u->full_name }}</div>
                            <div class="text-amber-300 text-[10px] mt-0.5">{{ $u->role?->label }}</div>
                        </div>
                        <a href="{{ route('account.profile') }}" class="block px-4 py-2.5 hover:bg-slate-800"><i class="fa-solid fa-user-gear ml-2 text-slate-500"></i>ویرایش حساب من</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-right px-4 py-2.5 hover:bg-slate-800 text-rose-300"><i class="fa-solid fa-arrow-right-from-bracket ml-2"></i>خروج</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        @include('partials.flash')

        <main class="flex-1 p-4 sm:p-6">
            @if ($errors->any() && ! isset($hideErrorSummary))
                <div class="mb-4 bg-rose-950/60 border border-rose-500/30 text-rose-200 rounded-xl p-3 text-xs">
                    <i class="fa-solid fa-circle-exclamation ml-1"></i> لطفاً خطاهای فرم را برطرف کنید:
                    <ul class="list-disc pr-5 mt-1.5 space-y-0.5">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
