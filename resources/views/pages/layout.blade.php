@extends('layouts.app')

@section('content')
    <div class="bg-gradient-to-b from-slate-900 to-transparent border-b border-slate-800/60">
        <div class="container-x py-10 relative overflow-hidden">
            <div class="absolute inset-0 hero-grid opacity-50 pointer-events-none"></div>
            <div class="relative">
                <nav class="text-[11px] text-slate-500 mb-3"><a href="{{ route('home') }}" class="hover:text-sky-400">خانه</a> <i class="fa-solid fa-angle-left text-[9px] mx-1"></i> @yield('title')</nav>
                <h1 class="text-xl sm:text-2xl font-black text-white">@yield('title')</h1>
                @hasSection('lead')<p class="text-sm text-slate-400 mt-3 max-w-2xl leading-7">@yield('lead')</p>@endif
            </div>
        </div>
    </div>
    <div class="container-x py-8">
        @yield('page')
    </div>
@endsection
