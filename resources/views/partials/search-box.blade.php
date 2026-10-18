<form action="{{ route('search') }}" method="GET" class="relative w-full" data-search="{{ route('search.suggest') }}" role="search">
    <i class="fa-solid fa-magnifying-glass absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
    <input type="search" name="q" value="{{ request('q') }}" autocomplete="off" maxlength="100"
           placeholder="جستجوی پارت نامبر (مثلاً A213...)، نام قطعه یا کد شاسی..."
           aria-label="جستجو"
           class="w-full bg-slate-950/90 text-white pr-9 pl-20 py-2.5 rounded-xl text-xs border border-slate-700/80 focus:border-sky-400 focus:outline-none transition">
    <button type="submit" class="absolute left-1 top-1/2 -translate-y-1/2 bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold px-3 py-1.5 rounded-lg text-xs transition">جستجو</button>
    <div data-search-results hidden
         class="absolute top-full mt-2 inset-x-0 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl overflow-hidden z-50 max-h-[70vh] overflow-y-auto"></div>
</form>
