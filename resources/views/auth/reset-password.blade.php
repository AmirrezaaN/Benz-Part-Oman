@extends('auth.layout')

@section('title', 'تعیین رمز عبور جدید')

@section('card')
    <div class="text-center mb-6">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xl flex items-center justify-center mb-3"><i class="fa-solid fa-lock-open"></i></div>
        <h1 class="text-lg font-black text-white">تعیین رمز عبور جدید</h1>
        <p class="text-xs text-slate-400 mt-1.5 leading-6">
            کد ارسال‌شده به <span class="text-white font-mono" dir="ltr">{{ $masked }}</span> و رمز جدید را وارد کنید.
            <a href="{{ route('password.request') }}" class="text-sky-400">ویرایش</a>
        </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <div>
            <label class="form-label" for="code">کد تایید</label>
            <input id="code" name="code" required inputmode="numeric" autocomplete="one-time-code" maxlength="{{ config('shop.otp.length') }}" data-latin-digits class="form-input otp-input font-mono" placeholder="------">
            @error('code')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="form-label" for="password">رمز عبور جدید</label>
            <div class="relative">
                <input id="password" name="password" type="password" required autocomplete="new-password" dir="ltr" class="form-input text-left pl-10">
                <button type="button" data-toggle-password class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500" aria-label="نمایش رمز"><i class="fa-solid fa-eye text-xs"></i></button>
            </div>
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="form-label" for="password_confirmation">تکرار رمز عبور جدید</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" dir="ltr" class="form-input text-left">
        </div>
        <button type="submit" class="btn-gradient w-full py-3!"><i class="fa-solid fa-check"></i> ذخیره رمز جدید</button>
    </form>
@endsection
