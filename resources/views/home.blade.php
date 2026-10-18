@extends('layouts.app')

@section('title', 'خرید مستقیم قطعات مرسدس بنز از مسقط')

@section('content')
    {{-- ============================================================ HERO --}}
    <section class="container-x pt-5">
        <div class="grid lg:grid-cols-3 gap-5">
            {{-- Banner slider --}}
            <div class="lg:col-span-2 min-w-0 relative rounded-3xl overflow-hidden border border-slate-800 bg-navy-900 min-h-[340px] sm:min-h-[400px]" data-slider>
                <div class="swiper hero-swiper h-full">
                    <div class="swiper-wrapper">
                        @forelse ($banners as $i => $banner)
                            <div class="swiper-slide">
                                @include('partials.hero-slide', ['banner' => $banner, 'i' => $i])
                            </div>
                        @empty
                            <div class="swiper-slide">
                                @include('partials.hero-slide', ['banner' => new \App\Models\Banner([
                                    'badge' => 'واردات مستقیم از مسقط',
                                    'title' => 'قطعات اصلی مرسدس بنز با ضمانت اصالت',
                                    'subtitle' => 'موجودی لحظه‌ای انبار عمان، قیمت شفاف و ارسال سریع به سراسر ایران',
                                    'button_text' => 'مشاهده قطعات', 'button_url' => '/products',
                                ]), 'i' => 0])
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="absolute bottom-4 left-4 z-10 flex items-center gap-2">
                    <button type="button" data-prev class="slider-nav bg-slate-950/70!" aria-label="قبلی"><i class="fa-solid fa-chevron-right text-xs"></i></button>
                    <button type="button" data-next class="slider-nav bg-slate-950/70!" aria-label="بعدی"><i class="fa-solid fa-chevron-left text-xs"></i></button>
                </div>
                <div class="swiper-pagination bottom-5! right-6! left-auto! w-auto!"></div>
            </div>

            {{-- Vehicle finder --}}
            <aside class="card p-5 flex flex-col">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-11 h-11 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center"><i class="fa-solid fa-car-side"></i></div>
                    <div>
                        <h2 class="text-sm font-black text-white">قطعه خودروی خود را پیدا کنید</h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">برند، مدل و شاسی خودرو را انتخاب کنید</p>
                    </div>
                </div>
                <form action="{{ route('products.index') }}" method="GET" data-vehicle-finder data-clean class="space-y-3 flex-1 flex flex-col">
                    <select name="brand" class="form-input" aria-label="برند"></select>
                    <select name="model" class="form-input" aria-label="مدل" disabled></select>
                    <select name="chassis" class="form-input" aria-label="شاسی" disabled></select>
                    <select name="category" class="form-input" aria-label="بخش">
                        <option value="">همه بخش‌ها (موتور، بدنه، ...)</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-gradient w-full mt-auto"><i class="fa-solid fa-magnifying-glass"></i> جستجوی قطعات</button>
                </form>
                <script type="application/json" id="vehicle-tree">@json($vehicleTree)</script>
                <div class="mt-4 pt-4 border-t border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                    <span>شاسی خودرو را نمی‌دانید؟</span>
                    <a href="#inquiry" class="text-amber-300 font-bold">استعلام با VIN <i class="fa-solid fa-arrow-left text-[9px]"></i></a>
                </div>
            </aside>
        </div>
    </section>

    {{-- ============================================================ BRANDS --}}
    @if ($brands->isNotEmpty())
        <section class="container-x mt-8">
            <div class="swiper brands-swiper">
                <div class="swiper-wrapper">
                    @foreach ($brands->concat($brands->count() < 8 ? $brands : collect()) as $brand)
                        <div class="swiper-slide w-auto!">
                            <a href="{{ route('brands.show', $brand) }}" class="flex items-center gap-3 bg-slate-900/70 border border-slate-800 hover:border-sky-500/50 rounded-2xl px-4 py-3 transition group">
                                <span class="w-10 h-10 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center overflow-hidden">
                                    @if ($brand->logoUrl())
                                        <img src="{{ $brand->logoUrl() }}" alt="{{ $brand->name }}" class="w-8 h-8 object-contain" loading="lazy">
                                    @else
                                        <span class="text-sm font-black text-sky-400 font-mono">{{ mb_substr($brand->name, 0, 1) }}</span>
                                    @endif
                                </span>
                                <span>
                                    <span class="block text-xs font-bold text-white group-hover:text-sky-400 transition whitespace-nowrap">{{ $brand->name }}</span>
                                    <span class="block text-[10px] text-slate-500 whitespace-nowrap">{{ $brand->name_fa }} · {{ fa_num($brand->products_count) }} قطعه</span>
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================================ CATEGORIES --}}
    <section class="container-x mt-12">
        <div class="section-title">
            <h2><span class="bar"></span> خرید بر اساس بخش خودرو</h2>
            <a href="{{ route('products.index') }}" class="text-xs text-sky-400 font-bold">همه قطعات <i class="fa-solid fa-arrow-left text-[10px]"></i></a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
            @foreach ($categories as $cat)
                <a href="{{ route('categories.show', $cat) }}" class="glass-card rounded-2xl p-4 flex flex-col items-center text-center gap-2.5 group">
                    <span class="w-14 h-14 rounded-2xl bg-gradient-to-br from-sky-500/15 to-blue-600/10 border border-sky-500/20 text-sky-400 text-xl flex items-center justify-center group-hover:scale-110 transition">
                        <i class="fa-solid {{ $cat->icon ?: 'fa-gear' }}"></i>
                    </span>
                    <span class="text-xs font-bold text-white leading-5">{{ $cat->name }}</span>
                    <span class="text-[10px] text-slate-500">{{ fa_num($cat->products_count) }} قطعه</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ============================================================ FEATURED --}}
    <x-product-slider class="mt-12" :products="$featured" title="پیشنهاد ویژه انبار مسقط" icon="fa-star"
                      subtitle="قطعات منتخب با بهترین قیمت و موجودی قطعی" :link="route('products.index', ['sort' => 'popular'])" autoplay="5000" />

    {{-- ============================================================ PROMO CARDS --}}
    <section class="container-x mt-12 grid md:grid-cols-3 gap-4">
        <a href="{{ route('inquiries.create') }}" class="relative overflow-hidden rounded-2xl p-5 border border-amber-500/20 bg-gradient-to-br from-amber-500/10 via-slate-900 to-slate-950 group">
            <i class="fa-solid fa-barcode absolute -left-3 -bottom-4 text-8xl text-amber-400/10 group-hover:scale-110 transition"></i>
            <span class="text-[11px] font-bold text-amber-300">استعلام هوشمند</span>
            <h3 class="text-base font-black text-white mt-1">فقط شماره شاسی را بفرستید</h3>
            <p class="text-[11px] text-slate-400 mt-2 leading-6">تطبیق دقیق با کاتالوگ EPC و اعلام قیمت نهایی</p>
        </a>
        <a href="{{ route('products.index', ['q' => 'AMG']) }}" class="relative overflow-hidden rounded-2xl p-5 border border-sky-500/20 bg-gradient-to-br from-sky-500/10 via-slate-900 to-slate-950 group">
            <i class="fa-solid fa-flag-checkered absolute -left-3 -bottom-4 text-8xl text-sky-400/10 group-hover:scale-110 transition"></i>
            <span class="text-[11px] font-bold text-sky-300">کیت‌های اصلی</span>
            <h3 class="text-base font-black text-white mt-1">فیس‌لیفت و قطعات AMG</h3>
            <p class="text-[11px] text-slate-400 mt-2 leading-6">سپر، جلوپنجره، ترمز و دیفیوزر اورجینال</p>
        </a>
        <a href="{{ route('pages.how-to-buy') }}" class="relative overflow-hidden rounded-2xl p-5 border border-emerald-500/20 bg-gradient-to-br from-emerald-500/10 via-slate-900 to-slate-950 group">
            <i class="fa-solid fa-plane-departure absolute -left-3 -bottom-4 text-8xl text-emerald-400/10 group-hover:scale-110 transition"></i>
            <span class="text-[11px] font-bold text-emerald-300">مسقط ← ایران</span>
            <h3 class="text-base font-black text-white mt-1">{{ setting('delivery_days') ? 'ارسال '.setting('delivery_days') : 'ارسال سریع به سراسر ایران' }}</h3>
            <p class="text-[11px] text-slate-400 mt-2 leading-6">بسته‌بندی ایمن و رهگیری تا درب منزل</p>
        </a>
    </section>

    {{-- ============================================================ LATEST --}}
    <x-product-slider class="mt-12" :products="$latest" title="تازه‌های انبار" icon="fa-box-open"
                      subtitle="آخرین قطعات اضافه‌شده به موجودی" :link="route('products.index')" />

    {{-- ============================================================ PER-CATEGORY ROWS --}}
    @foreach ($categoryRows as $row)
        <x-product-slider class="mt-12" :products="$row['products']" :title="$row['category']->name"
                          :icon="$row['category']->icon" :link="route('categories.show', $row['category'])" />
    @endforeach

    {{-- ============================================================ HOW IT WORKS --}}
    <section class="container-x mt-14">
        <div class="section-title">
            <h2><span class="bar"></span> مراحل خرید از بنز پارت عمان</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([
                ['fa-magnifying-glass', 'جستجو یا استعلام', 'قطعه را پیدا کنید یا با VIN استعلام بگیرید'],
                ['fa-file-invoice-dollar', 'تایید و پرداخت', 'پس از تایید موجودی، پرداخت را هماهنگ می‌کنیم'],
                ['fa-plane-departure', 'ارسال از مسقط', 'بسته‌بندی ایمن و ارسال از انبار عمان'],
                ['fa-house-circle-check', 'تحویل در ایران', setting('delivery_days') ? 'تحویل '.setting('delivery_days').' در شهر شما' : 'تحویل در شهر شما با رهگیری مرسوله'],
            ] as $i => [$icon, $title, $text])
                <div class="card p-5 relative">
                    <span class="absolute top-4 left-4 text-3xl font-black text-slate-800">{{ fa_num($i + 1) }}</span>
                    <span class="w-12 h-12 rounded-2xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center text-lg"><i class="fa-solid {{ $icon }}"></i></span>
                    <h3 class="text-sm font-bold text-white mt-4">{{ $title }}</h3>
                    <p class="text-[11px] text-slate-400 mt-1.5 leading-6">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============================================================ POPULAR --}}
    <x-product-slider class="mt-12" :products="$popular" title="پربازدیدترین قطعات" icon="fa-fire"
                      :link="route('products.index', ['sort' => 'popular'])" />

    {{-- ============================================================ NUMBERS --}}
    <section class="container-x mt-12">
        <div class="card p-6 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            @foreach ([
                [$stats['products'], 'قطعه در انبار', 'fa-boxes-stacked'],
                [$stats['brands'], 'برند خودرو', 'fa-car'],
                [$stats['categories'], 'دسته‌بندی قطعات', 'fa-layer-group'],
                ['۱۰۰٪', 'ضمانت اصالت', 'fa-shield-halved'],
            ] as [$value, $label, $icon])
                <div>
                    <i class="fa-solid {{ $icon }} text-sky-400 text-lg"></i>
                    <div class="text-2xl font-black text-white mt-2">{{ is_int($value) ? fa_num($value) : $value }}</div>
                    <div class="text-[11px] text-slate-500 mt-1">{{ $label }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============================================================ INQUIRY --}}
    @include('partials.inquiry-section')
@endsection
