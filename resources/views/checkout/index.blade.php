@extends('layouts.app')

@section('title', 'ثبت سفارش')

@section('content')
    <div class="container-x py-8">
        <h1 class="text-lg font-black text-white mb-6"><i class="fa-solid fa-truck-fast text-sky-400 ml-2"></i>اطلاعات ارسال و استعلام موجودی</h1>

        <form method="POST" action="{{ route('checkout.store') }}" class="grid lg:grid-cols-3 gap-6">
            @csrf
            <div class="lg:col-span-2 card p-5 grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="full_name">نام و نام خانوادگی گیرنده</label>
                    <input id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}" required maxlength="120" class="form-input">
                    @error('full_name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="phone">موبایل گیرنده</label>
                    <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required dir="ltr" inputmode="tel" data-latin-digits class="form-input text-left" placeholder="09121234567">
                    @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="province">استان</label>
                    <select id="province" name="province" required class="form-input">
                        <option value="">انتخاب استان</option>
                        @foreach (config('shop.provinces') as $province)
                            <option @selected(old('province', $user->province) === $province)>{{ $province }}</option>
                        @endforeach
                    </select>
                    @error('province')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="city">شهر</label>
                    <input id="city" name="city" value="{{ old('city', $user->city) }}" required maxlength="50" class="form-input">
                    @error('city')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label" for="address">آدرس کامل پستی</label>
                    <textarea id="address" name="address" required maxlength="500" rows="3" class="form-input">{{ old('address', $user->address) }}</textarea>
                    @error('address')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="postal_code">کد پستی (۱۰ رقمی)</label>
                    <input id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" dir="ltr" inputmode="numeric" maxlength="10" data-latin-digits class="form-input text-left">
                    @error('postal_code')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2.5 text-xs text-slate-300 cursor-pointer pb-3">
                        <input type="checkbox" name="save_address" value="1" class="form-check" checked>
                        ذخیره این آدرس در حساب کاربری
                    </label>
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label" for="customer_note">توضیحات سفارش (اختیاری)</label>
                    <textarea id="customer_note" name="customer_note" maxlength="1000" rows="2" class="form-input" placeholder="مثلاً شماره شاسی خودرو برای اطمینان از تطبیق قطعه">{{ old('customer_note') }}</textarea>
                </div>
            </div>

            <aside class="card p-5 h-fit">
                <h2 class="text-sm font-bold text-white mb-4">اقلام سفارش</h2>
                <ul class="space-y-3 text-xs mb-4 max-h-64 overflow-y-auto">
                    @foreach ($lines as $line)
                        <li class="flex justify-between gap-3">
                            <span class="text-slate-300 line-clamp-2">{{ $line['product']->name }} <span class="text-slate-500">× {{ fa_num($line['quantity']) }}</span></span>
                            <span class="text-white whitespace-nowrap">{{ format_toman($line['toman']) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="border-t border-slate-800 pt-3 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-400"><span>جمع (OMR)</span><span dir="ltr">{{ format_omr($totalOmr) }} OMR</span></div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-white">جمع کل</span>
                        <span class="text-lg font-black text-sky-400">{{ format_toman($totalToman) }} <small class="text-[10px] text-slate-400 font-normal">تومان</small></span>
                    </div>
                </div>
                <div class="bg-sky-500/5 border border-sky-500/20 rounded-xl p-3 mt-4 text-[11px] text-slate-300 leading-6">
                    <i class="fa-solid fa-circle-info text-sky-400 ml-1"></i>
                    با ثبت این درخواست، موجودی قطعات را از انبار عمان استعلام می‌کنیم. پس از تایید موجودی و اعلام هزینه ارسال، برای پرداخت با شما تماس می‌گیریم. در این مرحله هیچ پرداختی انجام نمی‌شود.
                </div>
                <button type="submit" class="btn-gradient w-full mt-4 py-3!"><i class="fa-solid fa-magnifying-glass-chart"></i> ثبت سفارش و استعلام موجودی</button>
            </aside>
        </form>
    </div>
@endsection
