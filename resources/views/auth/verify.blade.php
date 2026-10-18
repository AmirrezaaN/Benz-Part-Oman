@extends('auth.layout', ['step' => $step])

@section('title', $title)

@section('card')
    <div class="text-center mb-6">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-sky-500/10 border border-sky-500/20 text-sky-400 text-xl flex items-center justify-center mb-3">
            <i class="fa-solid {{ $channel === 'email' ? 'fa-envelope-open-text' : 'fa-mobile-screen' }}"></i>
        </div>
        <h1 class="text-lg font-black text-white">{{ $title }}</h1>
        <p class="text-xs text-slate-400 mt-1.5 leading-6">
            کد {{ fa_num(config('shop.otp.length')) }} رقمی ارسال‌شده به
            <span class="text-white font-mono" dir="ltr">{{ $masked }}</span>
            را وارد کنید.
            <a href="{{ $backUrl }}" class="text-sky-400">ویرایش</a>
        </p>
    </div>

    <form method="POST" action="{{ $action }}" class="space-y-4">
        @csrf
        <div>
            <label class="form-label sr-only" for="code">کد تایید</label>
            <input id="code" name="code" required inputmode="numeric" autocomplete="one-time-code" maxlength="{{ config('shop.otp.length') }}"
                   data-otp data-latin-digits class="form-input otp-input font-mono" placeholder="------">
            @error('code')<p class="form-error text-center">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-gradient w-full py-3!"><i class="fa-solid fa-check"></i> تایید کد</button>
    </form>

    <div class="mt-5 text-center text-xs text-slate-400">
        <p>کد تا {{ fa_num((int) ceil(config('shop.otp.ttl') / 60)) }} دقیقه معتبر است.</p>
        @if ($resendAction)
            <form method="POST" action="{{ $resendAction }}" class="mt-3">
                @csrf
                <button type="submit" data-countdown="{{ $wait }}" class="text-sky-400 hover:text-sky-300 disabled:text-slate-500 font-bold">
                    <i class="fa-solid fa-rotate-right ml-1"></i><span data-countdown-label>ارسال مجدد کد</span>
                </button>
            </form>
        @endif
        @if ($channel === 'email')
            <p class="text-[11px] text-slate-500 mt-3">ایمیل را نمی‌بینید؟ پوشه Spam را هم بررسی کنید.</p>
        @endif
    </div>
@endsection
