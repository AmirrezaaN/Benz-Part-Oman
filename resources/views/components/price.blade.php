@props(['product', 'size' => 'sm'])
@php
    $toman = $product->tomanPrice();
    $omr = $product->price_omr;
    $big = $size === 'lg';
@endphp
@if ($toman === null && $omr === null)
    <span {{ $attributes->class(['font-bold text-amber-300', $big ? 'text-base' : 'text-xs']) }}>
        <i class="fa-solid fa-tag ml-1"></i>قیمت با استعلام
    </span>
@else
    <span {{ $attributes->class(['font-black text-white', $big ? 'text-2xl text-sky-400' : 'text-sm']) }}>
        @if ($toman !== null)
            <span class="price-toman whitespace-nowrap">{{ format_toman($toman) }} <span class="{{ $big ? 'text-xs' : 'text-[10px]' }} font-normal text-slate-400">تومان</span></span>
        @else
            <span class="price-toman text-amber-300 text-xs">استعلام قیمت تومانی</span>
        @endif
        @if ($omr !== null)
            <span class="price-omr whitespace-nowrap" dir="ltr">{{ format_omr($omr) }} <span class="{{ $big ? 'text-xs' : 'text-[10px]' }} font-normal text-slate-400">OMR</span></span>
        @else
            <span class="price-omr text-amber-300 text-xs">استعلام قیمت</span>
        @endif
    </span>
@endif
