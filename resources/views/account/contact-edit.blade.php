@extends('account.layout')

@php($label = $channel === 'email' ? 'ایمیل' : 'شماره موبایل')

@section('title', ($user->{$channel} ? 'تغییر ' : 'افزودن ').$label)

@section('account')
    <div class="card p-6 max-w-lg">
        <h1 class="text-base font-black text-white">{{ $user->{$channel} ? 'تغییر' : 'افزودن' }} {{ $label }}</h1>
        <p class="text-xs text-slate-400 mt-2 leading-6">یک کد تایید به {{ $label }} جدید ارسال می‌شود. برای امنیت حساب، رمز عبور فعلی را هم وارد کنید.</p>

        <form method="POST" action="{{ route('account.contact.send', $channel) }}" class="space-y-4 mt-5">
            @csrf
            <div>
                <label class="form-label" for="value">{{ $label }} جدید</label>
                <input id="value" name="value" value="{{ old('value') }}" required dir="ltr" data-latin-digits class="form-input text-left"
                       type="{{ $channel === 'email' ? 'email' : 'tel' }}" placeholder="{{ $channel === 'email' ? 'name@mail.com' : '09121234567' }}">
                @error('value')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label" for="current_password">رمز عبور فعلی</label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password" dir="ltr" class="form-input text-left">
                @error('current_password')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-2">
                <button class="btn-primary"><i class="fa-solid fa-paper-plane"></i> ارسال کد تایید</button>
                <a href="{{ route('account.profile') }}" class="btn-ghost">انصراف</a>
            </div>
        </form>

        @if (session('otp_debug'))
            <p class="mt-4 text-xs text-amber-200">کد تست: <span class="font-mono">{{ session('otp_debug') }}</span></p>
        @endif
    </div>
@endsection
