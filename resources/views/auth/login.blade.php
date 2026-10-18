@extends('auth.layout')

@section('title', 'ورود به حساب کاربری')

@section('card')
    <div class="text-center mb-6">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-sky-500/10 border border-sky-500/20 text-sky-400 text-xl flex items-center justify-center mb-3"><i class="fa-solid fa-right-to-bracket"></i></div>
        <h1 class="text-lg font-black text-white">ورود به حساب کاربری</h1>
        <p class="text-xs text-slate-400 mt-1.5">با ایمیل، شماره موبایل یا نام کاربری وارد شوید</p>
    </div>

    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
        @csrf
        <div>
            <label class="form-label" for="identifier">ایمیل، موبایل یا نام کاربری</label>
            <input id="identifier" name="identifier" value="{{ old('identifier') }}" required autofocus autocomplete="username" dir="ltr" data-latin-digits
                   class="form-input text-left" placeholder="09121234567 / name@mail.com">
            @error('identifier')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="form-label mb-0!" for="password">رمز عبور</label>
                <a href="{{ route('password.request') }}" class="text-[11px] text-sky-400 hover:text-sky-300">فراموشی رمز عبور</a>
            </div>
            <div class="relative">
                <input id="password" name="password" type="password" required autocomplete="current-password" dir="ltr" class="form-input text-left pl-10">
                <button type="button" data-toggle-password class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300" aria-label="نمایش رمز"><i class="fa-solid fa-eye text-xs"></i></button>
            </div>
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
            <input type="checkbox" name="remember" value="1" class="form-check"> مرا به خاطر بسپار
        </label>
        <button type="submit" class="btn-gradient w-full py-3!"><i class="fa-solid fa-right-to-bracket"></i> ورود</button>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-800 text-center text-xs text-slate-400">
        حساب کاربری ندارید؟
        <a href="{{ route('register') }}" class="text-sky-400 font-bold hover:text-sky-300">ثبت‌نام کنید</a>
    </div>
@endsection
