<nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 glass-panel border-t border-slate-800 grid grid-cols-5 text-[10px] text-slate-400">
    @foreach ([
        [route('home'), 'fa-house', 'خانه', request()->routeIs('home')],
        [route('products.index'), 'fa-border-all', 'قطعات', request()->routeIs('products.*', 'categories.*', 'brands.*', 'search')],
        [route('inquiries.create'), 'fa-magnifying-glass-plus', 'استعلام', request()->routeIs('inquiries.*')],
        [route('cart.index'), 'fa-cart-shopping', 'سبد خرید', request()->routeIs('cart.*', 'checkout.*')],
        [auth()->check() ? route('account.dashboard') : route('login'), 'fa-user', 'حساب من', request()->routeIs('account.*', 'login', 'register*')],
    ] as [$url, $icon, $label, $active])
        <a href="{{ $url }}" class="flex flex-col items-center justify-center gap-1 py-2.5 {{ $active ? 'text-sky-400' : 'hover:text-white' }}">
            <i class="fa-solid {{ $icon }} text-base"></i>
            <span>{{ $label }}</span>
        </a>
    @endforeach
</nav>
