@extends('auth.layout', ['step' => 3])

@section('title', 'تکمیل ثبت‌نام')

@section('card')
    <div class="text-center mb-6">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xl flex items-center justify-center mb-3"><i class="fa-solid fa-user-check"></i></div>
        <h1 class="text-lg font-black text-white">تکمیل اطلاعات حساب</h1>
        <p class="text-xs text-slate-400 mt-1.5">
            <i class="fa-solid fa-circle-check text-emerald-400"></i>
            <span class="font-mono" dir="ltr">{{ \App\Support\Identifier::mask($state['value']) }}</span> تایید شد
        </p>
    </div>

    <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="form-label" for="first_name">نام</label>
                <input id="first_name" name="first_name" value="{{ old('first_name') }}" required maxlength="60" autofocus class="form-input">
                @error('first_name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label" for="last_name">نام خانوادگی</label>
                <input id="last_name" name="last_name" value="{{ old('last_name') }}" required maxlength="60" class="form-input">
                @error('last_name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="form-label" for="username">نام کاربری</label>
            <input id="username" name="username" value="{{ old('username') }}" required maxlength="30" dir="ltr" autocomplete="username" class="form-input text-left font-mono" placeholder="ali.ahmadi">
            <p class="form-hint">حروف انگلیسی، عدد، _ و . — بعداً قابل تغییر است.</p>
            @error('username')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="form-label" for="password">رمز عبور</label>
            <div class="relative">
                <input id="password" name="password" type="password" required autocomplete="new-password" dir="ltr" class="form-input text-left pl-10">
                <button type="button" data-toggle-password class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300" aria-label="نمایش رمز"><i class="fa-solid fa-eye text-xs"></i></button>
            </div>
            <p class="form-hint">حداقل ۸ کاراکتر، شامل حرف و عدد.</p>
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="form-label" for="password_confirmation">تکرار رمز عبور</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" dir="ltr" class="form-input text-left">
        </div>
        <label class="flex items-start gap-2 text-xs text-slate-300 cursor-pointer leading-6">
            <input type="checkbox" name="terms" value="1" class="form-check mt-1" @checked(old('terms'))>
            <span><a href="{{ route('pages.terms') }}" target="_blank" class="text-sky-400">قوانین و حریم خصوصی</a> سایت را مطالعه کرده و می‌پذیرم.</span>
        </label>
        @error('terms')<p class="form-error">{{ $message }}</p>@enderror
        <button type="submit" class="btn-gradient w-full py-3!"><i class="fa-solid fa-user-plus"></i> ساخت حساب کاربری</button>
    </form>
@endsection
