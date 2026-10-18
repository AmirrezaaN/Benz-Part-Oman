@extends('account.layout')

@section('title', 'ویرایش حساب')

@section('account')
    <div class="space-y-5">
        {{-- Personal info & address --}}
        <form method="POST" action="{{ route('account.profile.update') }}" class="card p-5">
            @csrf @method('PUT')
            <h2 class="text-sm font-bold text-white mb-4"><i class="fa-solid fa-id-card text-sky-400 ml-1.5"></i>اطلاعات شخصی</h2>
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="form-label" for="first_name">نام</label>
                    <input id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required maxlength="60" class="form-input">
                    @error('first_name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="last_name">نام خانوادگی</label>
                    <input id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required maxlength="60" class="form-input">
                    @error('last_name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="username">نام کاربری</label>
                    <input id="username" name="username" value="{{ old('username', $user->username) }}" required maxlength="30" dir="ltr" class="form-input text-left font-mono">
                    @error('username')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <h3 class="text-xs font-bold text-slate-300 mt-6 mb-3"><i class="fa-solid fa-location-dot text-sky-400 ml-1.5"></i>آدرس پیش‌فرض ارسال</h3>
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="form-label" for="province">استان</label>
                    <select id="province" name="province" class="form-input">
                        <option value="">انتخاب کنید</option>
                        @foreach (config('shop.provinces') as $province)
                            <option @selected(old('province', $user->province) === $province)>{{ $province }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="city">شهر</label>
                    <input id="city" name="city" value="{{ old('city', $user->city) }}" maxlength="50" class="form-input">
                </div>
                <div>
                    <label class="form-label" for="postal_code">کد پستی</label>
                    <input id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" maxlength="10" dir="ltr" data-latin-digits class="form-input text-left">
                    @error('postal_code')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-3">
                    <label class="form-label" for="address">آدرس</label>
                    <textarea id="address" name="address" rows="2" maxlength="500" class="form-input">{{ old('address', $user->address) }}</textarea>
                </div>
            </div>
            <div class="flex justify-end mt-5">
                <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> ذخیره تغییرات</button>
            </div>
        </form>

        {{-- Contact details --}}
        <div class="card p-5">
            <h2 class="text-sm font-bold text-white mb-4"><i class="fa-solid fa-address-book text-sky-400 ml-1.5"></i>اطلاعات تماس و ورود</h2>
            <div class="grid sm:grid-cols-2 gap-3">
                @foreach (['email' => ['ایمیل', 'fa-envelope'], 'phone' => ['شماره موبایل', 'fa-mobile-screen']] as $channel => [$label, $icon])
                    <div class="flex items-center justify-between gap-3 bg-slate-950/60 border border-slate-800 rounded-xl p-3.5">
                        <div class="min-w-0">
                            <div class="text-[11px] text-slate-500"><i class="fa-solid {{ $icon }} ml-1"></i>{{ $label }}</div>
                            @if ($user->{$channel})
                                <div class="text-xs text-white font-mono mt-1 truncate" dir="ltr">{{ $user->{$channel} }}</div>
                                <div class="text-[10px] text-emerald-400 mt-0.5"><i class="fa-solid fa-circle-check"></i> تایید شده</div>
                            @else
                                <div class="text-xs text-slate-500 mt-1">ثبت نشده</div>
                            @endif
                        </div>
                        <a href="{{ route('account.contact.edit', $channel) }}" class="btn-ghost btn-sm">{{ $user->{$channel} ? 'تغییر' : 'افزودن' }}</a>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Password --}}
        <form method="POST" action="{{ route('account.password.update') }}" class="card p-5">
            @csrf @method('PUT')
            <h2 class="text-sm font-bold text-white mb-4"><i class="fa-solid fa-lock text-sky-400 ml-1.5"></i>تغییر رمز عبور</h2>
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="form-label" for="current_password">رمز فعلی</label>
                    <input id="current_password" name="current_password" type="password" required autocomplete="current-password" dir="ltr" class="form-input text-left">
                    @error('current_password', 'password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="password">رمز جدید</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" dir="ltr" class="form-input text-left">
                    @error('password', 'password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="password_confirmation">تکرار رمز جدید</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" dir="ltr" class="form-input text-left">
                </div>
            </div>
            <div class="flex items-center justify-between gap-3 mt-5">
                <p class="text-[11px] text-slate-500">با تغییر رمز، از سایر دستگاه‌ها خارج می‌شوید.</p>
                <button class="btn-primary"><i class="fa-solid fa-key"></i> تغییر رمز</button>
            </div>
        </form>
    </div>
@endsection
