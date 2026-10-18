@extends('layouts.app')

@section('content')
    <div class="container-x py-10 sm:py-14">
        <div class="max-w-md mx-auto">
            @isset($step)
                @if ($step)
                    {{-- Registration progress --}}
                    <ol class="flex items-center justify-between mb-6 text-[10px] font-bold">
                        @foreach (['ایمیل / موبایل', 'کد تایید', 'مشخصات و رمز'] as $i => $label)
                            <li class="flex-1 flex items-center gap-2 {{ $i + 1 <= $step ? 'text-sky-400' : 'text-slate-600' }}">
                                <span class="w-7 h-7 shrink-0 rounded-full flex items-center justify-center border {{ $i + 1 < $step ? 'bg-sky-500 text-slate-950 border-sky-500' : ($i + 1 === $step ? 'border-sky-400' : 'border-slate-700') }}">
                                    @if ($i + 1 < $step)<i class="fa-solid fa-check"></i>@else{{ fa_num($i + 1) }}@endif
                                </span>
                                <span class="hidden sm:inline">{{ $label }}</span>
                                @if ($i < 2)<span class="flex-1 h-px {{ $i + 1 < $step ? 'bg-sky-500' : 'bg-slate-800' }}"></span>@endif
                            </li>
                        @endforeach
                    </ol>
                @endif
            @endisset

            <div class="card p-6 sm:p-8 relative overflow-hidden">
                <div class="absolute -top-24 -left-24 w-56 h-56 rounded-full bg-sky-500/10 blur-3xl pointer-events-none"></div>
                <div class="relative">
                    @yield('card')
                </div>
            </div>

            @if (session('otp_debug'))
                <div class="mt-4 bg-amber-500/10 border border-amber-500/30 text-amber-200 rounded-xl p-3 text-xs text-center">
                    <i class="fa-solid fa-flask ml-1"></i> حالت تست (فقط محیط local): کد تایید
                    <strong class="font-mono text-base mx-1" dir="ltr">{{ session('otp_debug') }}</strong>
                </div>
            @endif
        </div>
    </div>
@endsection
