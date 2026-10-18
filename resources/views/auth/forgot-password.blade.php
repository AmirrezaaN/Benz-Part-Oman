@extends('auth.layout')

@section('title', 'بازیابی رمز عبور')

@section('card')
    <div class="text-center mb-6">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xl flex items-center justify-center mb-3"><i class="fa-solid fa-key"></i></div>
        <h1 class="text-lg font-black text-white">بازیابی رمز عبور</h1>
        <p class="text-xs text-slate-400 mt-1.5 leading-6">ایمیل یا شماره موبایل ثبت‌شده در حساب را وارد کنید</p>
    </div>

    <form method="POST" action="{{ route('password.send') }}" class="space-y-4">
        @csrf
        <div>
            <label class="form-label" for="identifier">ایمیل یا شماره موبایل</label>
            <input id="identifier" name="identifier" value="{{ old('identifier') }}" required autofocus dir="ltr" data-latin-digits class="form-input text-left">
            @error('identifier')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-gradient w-full py-3!"><i class="fa-solid fa-paper-plane"></i> ارسال کد بازیابی</button>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-800 text-center text-xs">
        <a href="{{ route('login') }}" class="text-sky-400 hover:text-sky-300"><i class="fa-solid fa-arrow-right ml-1"></i> بازگشت به صفحه ورود</a>
    </div>
@endsection
