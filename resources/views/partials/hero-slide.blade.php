@php
    $gradients = [
        'from-sky-600/30 via-navy-900 to-navy-950',
        'from-amber-500/20 via-navy-900 to-navy-950',
        'from-indigo-600/30 via-navy-900 to-navy-950',
    ];
    $icons = ['fa-certificate', 'fa-barcode', 'fa-flag-checkered', 'fa-gears'];
@endphp
<div class="relative h-full min-h-[340px] sm:min-h-[400px] flex items-center bg-gradient-to-l {{ $gradients[$i % count($gradients)] }}">
    @if ($banner->imageUrl())
        <img src="{{ $banner->imageUrl() }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-40" @if ($i > 0) loading="lazy" @endif>
        <div class="absolute inset-0 bg-gradient-to-l from-navy-950/95 via-navy-950/70 to-transparent"></div>
    @else
        <div class="absolute inset-0 hero-grid"></div>
        <i class="fa-solid {{ $icons[$i % count($icons)] }} absolute left-8 sm:left-16 top-1/2 -translate-y-1/2 text-[9rem] sm:text-[13rem] text-sky-400/10"></i>
        <div class="absolute -left-20 -top-20 w-72 h-72 rounded-full bg-sky-500/10 blur-3xl"></div>
    @endif

    <div class="relative z-10 p-6 sm:p-10 max-w-xl">
        @if ($banner->badge)
            <span class="badge bg-sky-500/10 text-sky-300 border-sky-500/30 mb-4 text-[11px]!"><span class="w-1.5 h-1.5 rounded-full bg-sky-400 animate-pulse"></span>{{ $banner->badge }}</span>
        @endif
        <h2 class="text-2xl sm:text-4xl font-black text-white leading-[1.5]">{{ $banner->title }}</h2>
        @if ($banner->subtitle)
            <p class="text-xs sm:text-sm text-slate-300 mt-4 leading-7">{{ $banner->subtitle }}</p>
        @endif
        @if ($banner->button_text && $banner->button_url)
            <a href="{{ $banner->button_url }}" class="btn-gradient mt-6 px-6! py-3!">
                {{ $banner->button_text }} <i class="fa-solid fa-arrow-left"></i>
            </a>
        @endif
    </div>
</div>
