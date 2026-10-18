<div data-drawer-overlay="mobile-menu" data-open="false" class="drawer-overlay fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm lg:hidden"></div>
<aside data-drawer="mobile-menu" data-open="false"
       class="drawer fixed top-0 bottom-0 right-0 z-50 w-80 max-w-[85vw] bg-navy-900 border-l border-slate-800 overflow-y-auto lg:hidden">
    <div class="flex items-center justify-between p-4 border-b border-slate-800">
        <span class="font-black text-white">بنز پارت <span class="text-sky-400">عمان</span></span>
        <button type="button" data-drawer-close="mobile-menu" class="w-9 h-9 rounded-lg bg-slate-800 text-slate-300" aria-label="بستن"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="p-4 space-y-5 text-sm">
        @auth
            <div class="card p-3 flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-sky-500/15 text-sky-400 flex items-center justify-center font-black">{{ mb_substr(auth()->user()->full_name, 0, 1) }}</span>
                <div class="min-w-0">
                    <div class="font-bold text-white text-xs truncate">{{ auth()->user()->full_name }}</div>
                    <a href="{{ route('account.dashboard') }}" class="text-[11px] text-sky-400">مشاهده حساب کاربری</a>
                </div>
            </div>
            @if (auth()->user()->canAccessAdmin())
                <a href="{{ route('admin.dashboard') }}" class="btn w-full bg-amber-500/10 border border-amber-500/30 text-amber-300"><i class="fa-solid fa-gauge-high"></i> پنل مدیریت</a>
            @endif
        @else
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('login') }}" class="btn-primary">ورود</a>
                <a href="{{ route('register') }}" class="btn-ghost">ثبت‌نام</a>
            </div>
        @endauth

        <div>
            <div class="text-[11px] font-bold text-slate-500 mb-2">دسته‌بندی قطعات</div>
            <nav class="space-y-1">
                <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 text-slate-200"><i class="fa-solid fa-border-all w-5 text-sky-400"></i> همه قطعات</a>
                @foreach ($navCategories as $cat)
                    <a href="{{ route('categories.show', $cat) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 text-slate-300"><i class="fa-solid {{ $cat->icon ?: 'fa-gear' }} w-5 text-slate-500"></i> {{ $cat->name }}</a>
                @endforeach
            </nav>
        </div>

        <div>
            <div class="text-[11px] font-bold text-slate-500 mb-2">سایر صفحات</div>
            <nav class="space-y-1 text-slate-300">
                <a href="{{ route('inquiries.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 text-amber-300"><i class="fa-solid fa-magnifying-glass-plus w-5"></i> استعلام قطعه</a>
                <a href="{{ route('pages.how-to-buy') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800"><i class="fa-solid fa-route w-5 text-slate-500"></i> راهنمای خرید</a>
                <a href="{{ route('pages.faq') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800"><i class="fa-solid fa-circle-question w-5 text-slate-500"></i> سوالات متداول</a>
                <a href="{{ route('pages.contact') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800"><i class="fa-solid fa-phone w-5 text-slate-500"></i> تماس با ما</a>
            </nav>
        </div>

        <a href="{{ whatsapp_link() }}" target="_blank" rel="noopener" class="btn-success w-full"><i class="fa-brands fa-whatsapp"></i> سفارش و مشاوره در واتساپ</a>
    </div>
</aside>
