@props(['product', 'url' => null])
@php($src = $url ?? $product->imageUrl())
@if ($src)
    <img src="{{ $src }}" alt="{{ $product->name }}" loading="lazy" {{ $attributes->class('w-full h-full object-cover') }}>
@else
    {{-- Placeholder when the product has no photo yet --}}
    <div {{ $attributes->class('w-full h-full flex flex-col items-center justify-center gap-2 bg-gradient-to-br from-slate-900 via-navy-800 to-slate-950 text-slate-600') }}>
        <i class="fa-solid {{ $product->category?->icon ?: 'fa-gears' }} text-4xl text-sky-500/30"></i>
        <span class="text-[10px] font-mono tracking-widest text-slate-600">{{ $product->part_number ?: 'BENZ PART OMAN' }}</span>
    </div>
@endif
