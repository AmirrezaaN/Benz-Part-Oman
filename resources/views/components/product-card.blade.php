@props(['product'])
@php
    $badge = match ($product->condition) {
        'new', 'oem' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
        'used', 'refurbished' => 'bg-sky-500/20 text-sky-400 border-sky-500/30',
        default => 'bg-violet-500/20 text-violet-300 border-violet-500/30',
    };
    $url = route('products.show', $product);
@endphp
<article {{ $attributes->class('glass-card rounded-xl overflow-hidden flex flex-col justify-between group h-full') }}>
    <div>
        <a href="{{ $url }}" class="relative block h-44 bg-slate-950 overflow-hidden">
            <x-product-image :product="$product" class="group-hover:scale-105 transition duration-300" />
            @if ($product->conditionLabel())
                <span class="absolute top-2 right-2 badge {{ $badge }}">{{ $product->conditionLabel() }}</span>
            @endif
            @if ($product->quantity === 0)
                <span class="absolute top-2 left-2 badge bg-rose-500/20 text-rose-300 border-rose-500/30">ناموجود</span>
            @elseif ($product->is_featured)
                <span class="absolute top-2 left-2 badge bg-amber-500/20 text-amber-300 border-amber-500/30"><i class="fa-solid fa-star text-[8px]"></i> ویژه</span>
            @endif
            @if ($product->chassis->isNotEmpty())
                <span class="absolute bottom-2 left-2 text-[10px] bg-slate-950/90 text-sky-400 px-2 py-0.5 rounded font-mono border border-slate-800 max-w-[70%] truncate" dir="ltr">
                    {{ $product->chassis->pluck('code')->take(3)->implode(' · ') }}{{ $product->chassis->count() > 3 ? ' +' : '' }}
                </span>
            @endif
        </a>

        <div class="p-3.5">
            <div class="flex items-center justify-between gap-2 mb-1">
                <span class="text-[10px] font-bold text-slate-400 truncate">{{ $product->brand?->name ?? $product->manufacturer }}</span>
                @if ($product->part_number)
                    <span class="text-[10px] text-slate-500 font-mono truncate" dir="ltr">{{ $product->part_number }}</span>
                @endif
            </div>
            <h3 class="text-xs font-bold text-white leading-6 line-clamp-2 min-h-12">
                <a href="{{ $url }}" class="hover:text-sky-400 transition">{{ $product->name }}</a>
            </h3>
            @if ($product->yearsLabel() || $product->category)
                <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-3 truncate">
                    @if ($product->category)
                        <span class="truncate"><i class="fa-solid {{ $product->category->icon ?: 'fa-gear' }} text-slate-500 ml-1"></i>{{ $product->category->name }}</span>
                    @endif
                    @if ($product->yearsLabel())
                        <span class="shrink-0"><i class="fa-regular fa-calendar text-slate-500 ml-1"></i>{{ fa_num($product->yearsLabel()) }}</span>
                    @endif
                </p>
            @endif
        </div>
    </div>

    <div class="px-3.5 pb-3.5 pt-2.5 border-t border-slate-800 flex items-center justify-between gap-2">
        <div class="min-w-0">
            <span class="block text-[9px] text-slate-500">قیمت انبار عمان</span>
            <x-price :product="$product" />
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
            @if ($product->isPurchasable())
                <form method="POST" action="{{ route('cart.add', $product) }}" data-ajax-cart>
                    @csrf
                    <button type="submit" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-emerald-600 text-slate-300 hover:text-white transition flex items-center justify-center" title="افزودن به سبد خرید" aria-label="افزودن به سبد خرید">
                        <i class="fa-solid fa-cart-plus text-xs"></i>
                    </button>
                </form>
            @endif
            <a href="{{ $url }}" class="bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1">
                <span>جزئیات</span><i class="fa-solid fa-chevron-left text-[10px]"></i>
            </a>
        </div>
    </div>
</article>
