@props(['products', 'title', 'icon' => null, 'link' => null, 'subtitle' => null, 'autoplay' => null])
@if ($products->isNotEmpty())
    <section {{ $attributes->class('container-x') }} data-product-slider @if ($autoplay) data-autoplay="{{ $autoplay }}" @endif>
        <div class="section-title">
            <div>
                <h2><span class="bar"></span>@if ($icon)<i class="fa-solid {{ $icon }} text-sky-400 text-base"></i>@endif {{ $title }}</h2>
                @if ($subtitle)<p class="text-[11px] text-slate-500 mt-1 mr-4">{{ $subtitle }}</p>@endif
            </div>
            <div class="flex items-center gap-2">
                @if ($link)
                    <a href="{{ $link }}" class="text-xs text-sky-400 hover:text-sky-300 font-bold ml-2 whitespace-nowrap">مشاهده همه <i class="fa-solid fa-arrow-left text-[10px]"></i></a>
                @endif
                <button type="button" data-prev class="slider-nav" aria-label="قبلی"><i class="fa-solid fa-chevron-right text-xs"></i></button>
                <button type="button" data-next class="slider-nav" aria-label="بعدی"><i class="fa-solid fa-chevron-left text-xs"></i></button>
            </div>
        </div>
        <div class="swiper">
            <div class="swiper-wrapper">
                @foreach ($products as $product)
                    <div class="swiper-slide h-auto!">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
