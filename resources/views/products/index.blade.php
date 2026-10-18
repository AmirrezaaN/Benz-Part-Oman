@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="container-x py-6">
        {{-- Breadcrumb --}}
        <nav class="text-[11px] text-slate-500 mb-4 flex items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-sky-400">خانه</a>
            <i class="fa-solid fa-angle-left text-[9px]"></i>
            <a href="{{ route('products.index') }}" class="hover:text-sky-400">قطعات</a>
            @if ($title !== 'همه قطعات')
                <i class="fa-solid fa-angle-left text-[9px]"></i>
                <span class="text-slate-300">{{ $title }}</span>
            @endif
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <div>
                <h1 class="text-lg font-black text-white">{{ $title }}</h1>
                <p class="text-xs text-slate-400 mt-1">
                    <strong class="text-sky-400">{{ fa_num($products->total()) }}</strong> قطعه یافت شد
                    @if ($activeCount)
                        · <a href="{{ url()->current() }}" class="text-rose-300 hover:text-rose-200"><i class="fa-solid fa-xmark text-[10px]"></i> حذف فیلترها</a>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" data-drawer-open="filters" class="lg:hidden btn-ghost btn-sm">
                    <i class="fa-solid fa-sliders"></i> فیلترها @if ($activeCount)<span class="bg-sky-500 text-slate-950 rounded-full w-4 h-4 text-[9px] flex items-center justify-center">{{ fa_num($activeCount) }}</span>@endif
                </button>
                <form method="GET" data-clean class="flex items-center gap-2">
                    @foreach (array_diff_key($filters, array_flip(['sort']), $preset) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label for="sort" class="text-[11px] text-slate-500 hidden sm:block">مرتب‌سازی:</label>
                    <select id="sort" name="sort" data-autosubmit class="bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-sky-400">
                        @foreach (['newest' => 'جدیدترین', 'popular' => 'پربازدیدترین', 'price_asc' => 'ارزان‌ترین', 'price_desc' => 'گران‌ترین'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['sort'] ?? 'newest') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="grid lg:grid-cols-4 gap-6">
            {{-- Filters: sidebar on desktop, drawer on mobile --}}
            <div data-drawer-overlay="filters" data-open="false" class="drawer-overlay fixed inset-0 z-50 bg-slate-950/70 lg:hidden"></div>
            <aside data-drawer="filters" data-open="false"
                   class="drawer fixed lg:static top-0 bottom-0 right-0 z-50 w-80 max-w-[85vw] lg:w-auto lg:max-w-none bg-navy-900 lg:bg-transparent overflow-y-auto lg:overflow-visible lg:transform-none!">
                <div class="flex items-center justify-between p-4 border-b border-slate-800 lg:hidden">
                    <span class="font-bold text-white text-sm">فیلتر قطعات</span>
                    <button type="button" data-drawer-close="filters" class="w-9 h-9 rounded-lg bg-slate-800" aria-label="بستن"><i class="fa-solid fa-xmark"></i></button>
                </div>
                @include('products.partials.filters')
            </aside>

            {{-- Results --}}
            <div class="lg:col-span-3">
                @if ($products->isEmpty())
                    <div class="text-center py-16 card">
                        <i class="fa-solid fa-boxes-packing text-4xl text-slate-600 mb-3"></i>
                        <h3 class="text-sm font-bold text-slate-300">قطعه مورد نظر در لیست فوری یافت نشد!</h3>
                        <p class="text-xs text-slate-500 mt-2 max-w-sm mx-auto leading-6">شماره شاسی (VIN) یا پارت نامبر قطعه را ارسال کنید تا مستقیم از انبار عمان استعلام شود.</p>
                        <a href="{{ route('inquiries.create', array_filter(['part_number' => $filters['q'] ?? null])) }}" class="btn-gradient mt-5"><i class="fa-solid fa-magnifying-glass-plus"></i> ثبت استعلام قطعه</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 min-[440px]:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                    {{ $products->links() }}
                @endif
            </div>
        </div>
    </div>
@endsection
