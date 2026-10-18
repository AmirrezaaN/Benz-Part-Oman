@extends('auth.layout', ['step' => 1])

@section('title', 'ثبت‌نام')

@section('card')
    <div class="text-center mb-6">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-sky-500/10 border border-sky-500/20 text-sky-400 text-xl flex items-center justify-center mb-3"><i class="fa-solid fa-user-plus"></i></div>
        <h1 class="text-lg font-black text-white">ساخت حساب کاربری</h1>
        <p class="text-xs text-slate-400 mt-1.5 leading-6">ایمیل یا شماره موبایل خود را وارد کنید تا کد تایید برایتان ارسال شود</p>
    </div>

    <form method="POST" action="{{ route('register.send') }}" class="space-y-4">
        @csrf
        <div>
            <label class="form-label" for="identifier">ایمیل یا شماره موبایل</label>
            <input id="identifier" name="identifier" value="{{ old('identifier') }}" required autofocus dir="ltr" data-latin-digits
                   class="form-input text-left" placeholder="09121234567 / name@mail.com">
            @error('identifier')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-gradient w-full py-3!"><i class="fa-solid fa-paper-plane"></i> دریافت کد تایید</button>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-800 text-center text-xs text-slate-400">
        قبلاً ثبت‌نام کرده‌اید؟
        <a href="{{ route('login') }}" class="text-sky-400 font-bold hover:text-sky-300">وارد شوید</a>
    </div>
@endsection
