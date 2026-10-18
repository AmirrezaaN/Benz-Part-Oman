<header class="sticky top-0 z-40 glass-panel shadow-xl">
    <div class="container-x py-3 flex items-center justify-between gap-3">

        {{-- Mobile menu button --}}
        <button type="button" data-drawer-open="mobile-menu" class="lg:hidden w-10 h-10 shrink-0 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 flex items-center justify-center" aria-label="منو">
            <i class="fa-solid fa-bars"></i>
        </button>

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
            <div class="w-10 h-10 bg-slate-900 border border-slate-700 rounded-xl flex items-center justify-center text-sky-400 text-xl shadow-lg">
                <i class="fa-solid fa-certificate"></i>
            </div>
            <div class="hidden sm:block">
                <span class="text-lg font-black tracking-tight text-white block leading-tight">بنز پارت <span class="text-sky-400">عمان</span></span>
                <span class="text-[9px] text-slate-400 block font-mono uppercase tracking-widest">BENZ PART OMAN</span>
            </div>
        </a>

        {{-- Search with live suggestions --}}
        <div class="hidden md:block flex-1 max-w-xl mx-2">
            @include('partials.search-box', ['id' => 'desktop'])
        </div>

        {{-- Controls --}}
        <div class="flex items-center gap-2 shrink-0">
            {{-- Quick chassis picker --}}
            @if ($navChassis->isNotEmpty())
                <select data-navigate aria-label="انتخاب شاسی"
                        class="hidden xl:block bg-slate-900 border border-slate-700 hover:border-sky-400 text-slate-200 text-xs rounded-xl px-2.5 py-2 focus:outline-none cursor-pointer max-w-40">
                    <option value="{{ route('products.index') }}">همه اتاق‌ها</option>
                    @foreach ($navChassis as $group)
                        <optgroup label="{{ $group['brand'] }}">
                            @foreach ($group['items'] as $item)
                                <option value="{{ route('products.index', ['chassis' => $item['slug']]) }}" @selected(request('chassis') === $item['slug'])>{{ $item['label'] }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            @endif

            {{-- Currency switch --}}
            <button type="button" data-currency-toggle title="تغییر واحد پول"
                    class="bg-slate-900 border border-slate-700 hover:border-sky-400 px-3 py-2 rounded-xl text-xs flex items-center gap-1.5 transition text-slate-300">
                <i class="fa-solid fa-coins text-sky-400"></i>
                <span data-currency-label class="hidden sm:inline">{{ $currency === 'omr' ? 'ریال عمان' : 'تومان' }}</span>
            </button>

            {{-- Cart --}}
            <a href="{{ route('cart.index') }}" class="relative w-10 h-10 rounded-xl bg-slate-900 border border-slate-700 hover:border-sky-400 text-slate-300 flex items-center justify-center transition" aria-label="سبد خرید">
                <i class="fa-solid fa-cart-shopping text-sm"></i>
                <span data-cart-count @if ($cartCount < 1) hidden @endif
                      class="absolute -top-1.5 -left-1.5 min-w-5 h-5 px-1 rounded-full bg-sky-500 text-slate-950 text-[10px] font-black flex items-center justify-center">{{ $cartCount }}</span>
            </a>

            {{-- Account --}}
            @auth
                <div class="relative" data-dropdown>
                    <button type="button" data-dropdown-toggle class="h-10 rounded-xl bg-slate-900 border border-slate-700 hover:border-sky-400 text-slate-200 flex items-center gap-2 px-2.5 transition">
                        <span class="w-6 h-6 rounded-lg bg-sky-500/15 text-sky-400 flex items-center justify-center text-[11px] font-black">{{ mb_substr(auth()->user()->full_name, 0, 1) }}</span>
                        <span class="hidden lg:inline text-xs max-w-24 truncate">{{ auth()->user()->full_name }}</span>
                        <i class="fa-solid fa-chevron-down text-[9px] text-slate-500"></i>
                    </button>
                    <div data-dropdown-menu hidden class="absolute left-0 mt-2 w-56 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl overflow-hidden text-xs z-50">
                        <div class="px-4 py-3 border-b border-slate-800">
                            <div class="font-bold text-white truncate">{{ auth()->user()->full_name }}</div>
                            <div class="text-slate-500 truncate font-mono text-[10px] mt-0.5">{{ '@'.auth()->user()->username }}</div>
                        </div>
                        @if (auth()->user()->canAccessAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-amber-300 hover:bg-slate-800"><i class="fa-solid fa-gauge-high w-4"></i> پنل مدیریت</a>
                        @endif
                        <a href="{{ route('account.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-slate-800"><i class="fa-solid fa-user w-4 text-slate-400"></i> حساب کاربری</a>
                        <a href="{{ route('account.inquiries') }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-slate-800"><i class="fa-solid fa-magnifying-glass-plus w-4 text-slate-400"></i> استعلام‌های من</a>
                        <a href="{{ route('account.orders') }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-slate-800"><i class="fa-solid fa-box w-4 text-slate-400"></i> سفارش‌های من</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-800">
                            @csrf
                            <button class="w-full flex items-center gap-2.5 px-4 py-2.5 text-rose-300 hover:bg-slate-800 text-right"><i class="fa-solid fa-arrow-right-from-bracket w-4"></i> خروج</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-primary px-3.5! h-10">
                    <i class="fa-solid fa-user"></i>
                    <span class="hidden sm:inline">ورود / ثبت‌نام</span>
                </a>
            @endauth

            <a href="{{ whatsapp_link('سلام بنز پارت عمان، درخواست مشاوره خرید قطعه دارم.') }}" target="_blank" rel="noopener"
               class="hidden lg:flex btn-success px-3.5! h-10" title="سفارش واتساپ">
                <i class="fa-brands fa-whatsapp text-sm"></i>
            </a>
        </div>
    </div>

    {{-- Category navigation --}}
    <nav class="bg-slate-950 border-t border-slate-800/80 hidden lg:block">
        @php($currentCategory = request()->route('category'))
        <div class="container-x flex items-center gap-1 py-2 text-xs font-medium text-slate-300">
            {{-- All categories (mega menu) — every section is always reachable here --}}
            <div class="relative shrink-0" data-dropdown>
                <button type="button" data-dropdown-toggle class="px-3 py-1.5 rounded-lg whitespace-nowrap transition flex items-center gap-1.5 bg-sky-500/10 text-sky-400 border border-sky-500/30 hover:bg-sky-500/20">
                    <i class="fa-solid fa-bars-staggered"></i> همه بخش‌ها <i class="fa-solid fa-chevron-down text-[9px]"></i>
                </button>
                <div data-dropdown-menu hidden class="absolute right-0 top-full mt-2 w-[34rem] bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl p-3 z-50 grid grid-cols-2 gap-1">
                    <a href="{{ route('products.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-800 text-white font-bold">
                        <i class="fa-solid fa-border-all w-4 text-sky-400"></i> همه قطعات
                    </a>
                    @foreach ($navCategories as $cat)
                        <a href="{{ route('categories.show', $cat) }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-800 {{ $currentCategory?->id === $cat->id ? 'text-sky-400' : 'text-slate-300' }}">
                            <i class="fa-solid {{ $cat->icon ?: 'fa-gear' }} w-4 text-slate-500"></i> {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Quick links: only as many as fit the screen width (the rest live in the menu above) --}}
            @foreach ($navCategories->take(7) as $i => $cat)
                @php($active = $currentCategory?->id === $cat->id)
                <a href="{{ route('categories.show', $cat) }}"
                   class="{{ $i < 4 ? 'flex' : ($i < 6 ? 'hidden xl:flex' : 'hidden 2xl:flex') }} px-2.5 py-1.5 rounded-lg whitespace-nowrap transition items-center gap-1.5 {{ $active ? 'bg-sky-500/10 text-sky-400 border border-sky-500/30' : 'hover:bg-slate-900' }}">
                    <i class="fa-solid {{ $cat->icon ?: 'fa-gear' }} {{ $active ? '' : 'text-slate-500' }}"></i> {{ $cat->name }}
                </a>
            @endforeach
            <span class="flex-1"></span>
            <a href="{{ route('inquiries.create') }}" class="shrink-0 px-3 py-1.5 rounded-lg whitespace-nowrap text-amber-300 hover:bg-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-magnifying-glass-plus"></i> استعلام قطعه
            </a>
        </div>
    </nav>
</header>

{{-- Mobile search --}}
<div class="md:hidden container-x pt-3">
    @include('partials.search-box', ['id' => 'mobile'])
</div>
