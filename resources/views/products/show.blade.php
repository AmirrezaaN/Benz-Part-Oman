@extends('layouts.app')

@section('title', $product->name)
@section('description', \Illuminate\Support\Str::limit(strip_tags($product->description ?? $product->name), 155))

@section('content')
    @php
        $specs = array_filter([
            ['پارت نامبر', $product->part_number, 'fa-barcode', true],
            ['برند خودرو', $product->brand?->name, 'fa-car', false],
            ['بخش', $product->category ? ($product->category->parent ? $product->category->parent->name.' / ' : '').$product->category->name : null, 'fa-layer-group', false],
            ['سازنده قطعه', $product->manufacturer, 'fa-industry', false],
            ['کشور ساخت', $product->countryLabel(), 'fa-flag', false],
            ['سال ساخت خودرو', $product->yearsLabel() ? fa_num($product->yearsLabel()) : null, 'fa-calendar', false],
            ['وضعیت کالا', $product->conditionLabel(), 'fa-certificate', false],
            ['موقعیت انبار', $product->warehouse, 'fa-warehouse', false],
        ], fn ($row) => filled($row[1]));

        $waText = "سلام بنز پارت عمان، درخواست سفارش این قطعه را دارم:\n\nنام: {$product->name}"
            .($product->part_number ? "\nپارت نامبر: {$product->part_number}" : '')
            .($product->chassis->isNotEmpty() ? "\nشاسی: ".$product->chassisCodes() : '')
            ."\nلینک: ".route('products.show', $product);
    @endphp

    <div class="container-x py-6">
        <nav class="text-[11px] text-slate-500 mb-5 flex items-center gap-2 flex-wrap">
            <a href="{{ route('home') }}" class="hover:text-sky-400">خانه</a>
            <i class="fa-solid fa-angle-left text-[9px]"></i>
            <a href="{{ route('products.index') }}" class="hover:text-sky-400">قطعات</a>
            @if ($product->category)
                <i class="fa-solid fa-angle-left text-[9px]"></i>
                <a href="{{ route('categories.show', $product->category) }}" class="hover:text-sky-400">{{ $product->category->name }}</a>
            @endif
            <i class="fa-solid fa-angle-left text-[9px]"></i>
            <span class="text-slate-300 truncate max-w-60">{{ $product->name }}</span>
        </nav>

        <div class="grid lg:grid-cols-2 gap-6 lg:gap-10">
            {{-- Gallery --}}
            <div data-gallery class="min-w-0">
                <div class="relative rounded-2xl overflow-hidden bg-slate-950 border border-slate-800">
                    <div class="swiper gallery-main aspect-square">
                        <div class="swiper-wrapper">
                            @forelse ($product->images as $image)
                                <div class="swiper-slide"><x-product-image :product="$product" :url="$image->url()" /></div>
                            @empty
                                <div class="swiper-slide"><x-product-image :product="$product" /></div>
                            @endforelse
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                    @if ($product->images->count() > 1)
                        <button type="button" data-prev class="slider-nav absolute right-3 top-1/2 -translate-y-1/2 z-10" aria-label="قبلی"><i class="fa-solid fa-chevron-right text-xs"></i></button>
                        <button type="button" data-next class="slider-nav absolute left-3 top-1/2 -translate-y-1/2 z-10" aria-label="بعدی"><i class="fa-solid fa-chevron-left text-xs"></i></button>
                    @endif
                    @if ($product->conditionLabel())
                        <span class="absolute top-3 right-3 z-10 badge bg-emerald-500/20 text-emerald-300 border-emerald-500/30">{{ $product->conditionLabel() }}</span>
                    @endif
                </div>
                @if ($product->images->count() > 1)
                    <div class="swiper gallery-thumbs mt-3">
                        <div class="swiper-wrapper">
                            @foreach ($product->images as $image)
                                <div class="swiper-slide">
                                    <div class="aspect-square rounded-xl overflow-hidden border-2 border-slate-800">
                                        <img src="{{ $image->url() }}" alt="" class="w-full h-full object-cover" loading="lazy">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-3">
                    @if ($product->brand)
                        <a href="{{ route('brands.show', $product->brand) }}" class="badge bg-slate-800 text-slate-300 border-slate-700">{{ $product->brand->name }}</a>
                    @endif
                    @if ($product->part_number)
                        <span class="badge bg-slate-800 text-slate-300 border-slate-700 font-mono" dir="ltr">OEM: {{ $product->part_number }}</span>
                    @endif
                    @if ($product->is_featured)
                        <span class="badge bg-amber-500/10 text-amber-300 border-amber-500/30"><i class="fa-solid fa-star text-[8px]"></i> پیشنهاد ویژه</span>
                    @endif
                </div>

                <h1 class="text-lg sm:text-xl font-black text-white leading-9">{{ $product->name }}</h1>

                <div class="card p-4 mt-5">
                    <div class="flex items-end justify-between gap-3 flex-wrap">
                        <div>
                            <div class="text-[11px] text-slate-500 mb-1">قیمت انبار عمان</div>
                            <x-price :product="$product" size="lg" />
                            @if ($product->price_omr !== null && $product->tomanPrice())
                                <div class="text-[10px] text-slate-500 mt-1.5">
                                    <span class="price-toman">معادل <span dir="ltr">{{ format_omr($product->price_omr) }} OMR</span></span>
                                    <span class="price-omr">معادل {{ format_toman($product->tomanPrice()) }} تومان</span>
                                    · با دکمه <i class="fa-solid fa-coins"></i> بالای صفحه واحد پول را عوض کنید
                                </div>
                            @endif
                        </div>
                        <div class="text-left">
                            @if ($product->quantity === null)
                                <span class="badge bg-amber-500/10 text-amber-300 border-amber-500/30"><i class="fa-solid fa-circle-question"></i> موجودی با استعلام</span>
                            @elseif ($product->quantity > 0)
                                <span class="badge bg-emerald-500/10 text-emerald-300 border-emerald-500/30"><i class="fa-solid fa-circle-check"></i> موجود در انبار ({{ fa_num($product->quantity) }} عدد)</span>
                            @else
                                <span class="badge bg-rose-500/10 text-rose-300 border-rose-500/30"><i class="fa-solid fa-circle-xmark"></i> ناموجود</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-2 mt-5">
                        @if ($product->isPurchasable())
                            <form method="POST" action="{{ route('cart.add', $product) }}" data-ajax-cart class="flex gap-2 sm:col-span-2">
                                @csrf
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->quantity }}" class="form-input w-20 text-center" aria-label="تعداد">
                                <button type="submit" class="btn-primary flex-1"><i class="fa-solid fa-cart-plus"></i> افزودن به سبد خرید</button>
                            </form>
                        @endif
                        <a href="{{ route('inquiries.create', ['product' => $product->slug]) }}" class="btn-ghost"><i class="fa-solid fa-magnifying-glass-plus text-amber-300"></i> استعلام قیمت و موجودی</a>
                        <a href="{{ whatsapp_link($waText) }}" target="_blank" rel="noopener" class="btn-success"><i class="fa-brands fa-whatsapp"></i> سفارش در واتساپ</a>
                    </div>
                </div>

                {{-- Specs --}}
                @if ($specs)
                    <div class="card mt-4 divide-y divide-slate-800">
                        @foreach ($specs as [$label, $value, $icon, $mono])
                            <div class="flex items-center justify-between gap-3 px-4 py-2.5 text-xs">
                                <span class="text-slate-500 flex items-center gap-2"><i class="fa-solid {{ $icon }} w-4 text-slate-600"></i>{{ $label }}</span>
                                <span class="font-medium text-white text-left {{ $mono ? 'font-mono' : '' }}" @if ($mono) dir="ltr" @endif>{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="grid grid-cols-3 gap-2 mt-4 text-center text-[10px] text-slate-400">
                    <div class="card p-3"><i class="fa-solid fa-shield-halved text-sky-400 text-base mb-1.5 block"></i>ضمانت اصالت</div>
                    <div class="card p-3"><i class="fa-solid fa-truck-fast text-sky-400 text-base mb-1.5 block"></i>{{ setting('delivery_days') ? 'ارسال '.setting('delivery_days') : 'ارسال سریع' }}</div>
                    <div class="card p-3"><i class="fa-solid fa-headset text-sky-400 text-base mb-1.5 block"></i>مشاوره تخصصی</div>
                </div>
            </div>
        </div>

        {{-- Description & compatibility --}}
        <div class="card mt-8" data-tabs>
            <div class="flex border-b border-slate-800 text-xs font-bold" role="tablist">
                <button type="button" data-tab="compat" aria-selected="true" class="px-5 py-3.5 text-slate-400 aria-selected:text-sky-400 aria-selected:border-b-2 aria-selected:border-sky-400">
                    <i class="fa-solid fa-car ml-1"></i> خودروهای سازگار ({{ fa_num($product->chassis->count()) }})
                </button>
                <button type="button" data-tab="desc" aria-selected="false" class="px-5 py-3.5 text-slate-400 aria-selected:text-sky-400 aria-selected:border-b-2 aria-selected:border-sky-400">
                    <i class="fa-solid fa-align-right ml-1"></i> توضیحات
                </button>
            </div>
            <div data-tab-panel="compat" class="p-5">
                @if ($product->chassis->isEmpty())
                    <p class="text-xs text-slate-500">اطلاعات سازگاری برای این قطعه ثبت نشده است. برای اطمینان، شماره شاسی خودرو را استعلام کنید.</p>
                @else
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach ($product->chassis as $c)
                            <a href="{{ route('products.index', ['chassis' => $c->slug]) }}" class="flex items-center justify-between gap-2 bg-slate-950/60 border border-slate-800 hover:border-sky-500/40 rounded-xl px-3.5 py-2.5 text-xs transition">
                                <span class="text-slate-300">{{ $c->carModel?->brand?->name }} {{ $c->carModel?->name }}</span>
                                <span class="font-mono text-sky-400" dir="ltr">{{ $c->label() }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <div data-tab-panel="desc" hidden class="p-5 text-sm text-slate-300 leading-8 whitespace-pre-line">{{ $product->description ?: 'توضیحاتی برای این قطعه ثبت نشده است.' }}</div>
        </div>
    </div>

    <x-product-slider class="mt-6" :products="$related" title="قطعات مرتبط" icon="fa-link" />
@endsection
