@extends('admin.layout')

@section('title', 'تنظیمات سایت')

@section('content')
    @php($v = fn ($k) => old($k, $settings[$k] ?? ''))
    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-4xl space-y-5">
        @csrf @method('PUT')

        <section class="card p-5 border-sky-500/30!">
            <h2 class="text-sm font-bold text-white mb-4"><i class="fa-solid fa-coins text-sky-400 ml-1.5"></i>نرخ ارز</h2>
            <div class="grid sm:grid-cols-2 gap-4 items-end">
                <div>
                    <label class="form-label" for="exchange_rate">هر ۱ ریال عمان (OMR) = چند تومان؟ *</label>
                    <input id="exchange_rate" name="exchange_rate" value="{{ $v('exchange_rate') }}" required inputmode="numeric" dir="ltr" data-latin-digits class="form-input text-left text-lg font-bold">
                </div>
                <p class="text-[11px] text-slate-400 leading-6">قیمت تومانی همه محصولاتی که «قیمت تومانی دستی» ندارند، بلافاصله با این نرخ محاسبه می‌شود (گرد شده به هزار تومان).</p>
            </div>
        </section>

        <section class="card p-5">
            <h2 class="text-sm font-bold text-white mb-4"><i class="fa-solid fa-address-book text-sky-400 ml-1.5"></i>اطلاعات تماس</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="site_name">نام سایت *</label>
                    <input id="site_name" name="site_name" value="{{ $v('site_name') }}" required maxlength="80" class="form-input">
                </div>
                <div>
                    <label class="form-label" for="whatsapp_number">شماره واتساپ (با کد کشور، فقط عدد) *</label>
                    <input id="whatsapp_number" name="whatsapp_number" value="{{ $v('whatsapp_number') }}" required dir="ltr" data-latin-digits class="form-input text-left" placeholder="96890000000">
                </div>
                <div>
                    <label class="form-label" for="phone_oman">تلفن عمان (نمایشی)</label>
                    <input id="phone_oman" name="phone_oman" value="{{ $v('phone_oman') }}" maxlength="30" dir="ltr" class="form-input text-left">
                </div>
                <div>
                    <label class="form-label" for="phone_iran">تلفن ایران (نمایشی)</label>
                    <input id="phone_iran" name="phone_iran" value="{{ $v('phone_iran') }}" maxlength="30" dir="ltr" class="form-input text-left">
                </div>
                <div>
                    <label class="form-label" for="email">ایمیل</label>
                    <input id="email" name="email" type="email" value="{{ $v('email') }}" maxlength="120" dir="ltr" class="form-input text-left">
                </div>
                <div>
                    <label class="form-label" for="working_hours">ساعات کاری</label>
                    <input id="working_hours" name="working_hours" value="{{ $v('working_hours') }}" maxlength="120" class="form-input">
                </div>
                <div>
                    <label class="form-label" for="address_oman">آدرس عمان</label>
                    <input id="address_oman" name="address_oman" value="{{ $v('address_oman') }}" maxlength="255" class="form-input">
                </div>
                <div>
                    <label class="form-label" for="address_iran">آدرس ایران</label>
                    <input id="address_iran" name="address_iran" value="{{ $v('address_iran') }}" maxlength="255" class="form-input">
                </div>
                <div>
                    <label class="form-label" for="instagram">اینستاگرام (نام کاربری)</label>
                    <input id="instagram" name="instagram" value="{{ $v('instagram') }}" maxlength="60" dir="ltr" class="form-input text-left" placeholder="benzpartoman">
                </div>
                <div>
                    <label class="form-label" for="telegram">تلگرام (نام کاربری)</label>
                    <input id="telegram" name="telegram" value="{{ $v('telegram') }}" maxlength="60" dir="ltr" class="form-input text-left">
                </div>
            </div>
        </section>

        <section class="card p-5">
            <h2 class="text-sm font-bold text-white mb-4"><i class="fa-solid fa-bullhorn text-sky-400 ml-1.5"></i>متن‌های سایت</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="announcement">اعلان نوار بالای سایت</label>
                    <input id="announcement" name="announcement" value="{{ $v('announcement') }}" maxlength="200" class="form-input">
                </div>
                <div>
                    <label class="form-label" for="delivery_days">زمان تحویل</label>
                    <input id="delivery_days" name="delivery_days" value="{{ $v('delivery_days') }}" maxlength="60" class="form-input" placeholder="۳ الی ۵ روز کاری">
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label" for="about_short">درباره ما (کوتاه — فوتر و توضیحات سئو)</label>
                    <textarea id="about_short" name="about_short" rows="3" maxlength="500" class="form-input">{{ $v('about_short') }}</textarea>
                </div>
            </div>
        </section>

        <button class="btn-gradient px-8 py-3!"><i class="fa-solid fa-floppy-disk"></i> ذخیره تنظیمات</button>
    </form>
@endsection
