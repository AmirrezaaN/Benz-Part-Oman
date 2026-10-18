{{-- VIN / part inquiry. Guests fill the form and are asked to sign in; their input is carried over. --}}
<section id="inquiry" class="container-x mt-14">
    <div class="relative overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-900 via-navy-800 to-slate-950">
        <div class="absolute inset-0 hero-grid opacity-60 pointer-events-none"></div>
        <div class="relative grid lg:grid-cols-5 gap-8 p-5 sm:p-8">
            <div class="lg:col-span-2">
                <span class="badge bg-amber-500/10 text-amber-300 border-amber-500/30 mb-3"><i class="fa-solid fa-bolt"></i> پاسخ در کمتر از ۲۴ ساعت کاری</span>
                <h2 class="text-xl sm:text-2xl font-black text-white leading-10">قطعه‌ات را پیدا نکردی؟<br><span class="text-sky-400">استعلام بگیر</span></h2>
                <p class="text-xs text-slate-400 leading-7 mt-3">شماره شاسی (VIN) یا پارت نامبر قطعه را ارسال کنید تا کارشناسان ما با کاتالوگ الکترونیکی EPC، قطعه دقیق خودروی شما را از انبارهای عمان و دبی پیدا کرده و قیمت نهایی را اعلام کنند. پاسخ استعلام در پنل کاربری شما نمایش داده می‌شود.</p>
                <ul class="mt-5 space-y-3 text-xs text-slate-300">
                    <li class="flex items-center gap-2.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> تطابق ۱۰۰٪ با کاتالوگ رسمی سازنده</li>
                    <li class="flex items-center gap-2.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> قیمت به تومان و ریال عمان</li>
                    <li class="flex items-center gap-2.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> پیگیری وضعیت از حساب کاربری</li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                @auth
                    <form method="POST" action="{{ route('inquiries.store') }}" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-3 bg-slate-950/60 border border-slate-800 rounded-2xl p-4 sm:p-5">
                        @csrf
                @else
                    <form method="GET" action="{{ route('inquiries.create') }}" data-clean class="grid sm:grid-cols-2 gap-3 bg-slate-950/60 border border-slate-800 rounded-2xl p-4 sm:p-5">
                @endauth
                        <div class="sm:col-span-2">
                            <label class="form-label" for="inq-part">نام قطعه مورد نیاز <span class="text-rose-400">*</span></label>
                            <input id="inq-part" name="part_name" required maxlength="150" class="form-input" placeholder="مثلاً: کمک فنر بادی جلو سمت راست">
                        </div>
                        <div>
                            <label class="form-label" for="inq-vin">شماره شاسی (VIN)</label>
                            <input id="inq-vin" name="vin" maxlength="17" dir="ltr" data-latin-digits class="form-input font-mono uppercase" placeholder="WDD2130421A123456">
                        </div>
                        <div>
                            <label class="form-label" for="inq-pn">پارت نامبر (اختیاری)</label>
                            <input id="inq-pn" name="part_number" maxlength="60" dir="ltr" data-latin-digits class="form-input font-mono" placeholder="A2223204713">
                        </div>
                        <div>
                            <label class="form-label" for="inq-brand">برند خودرو</label>
                            <select id="inq-brand" name="brand_id" class="form-input">
                                <option value="">انتخاب کنید</option>
                                @foreach ($brands as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}{{ $b->name_fa ? ' - '.$b->name_fa : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label" for="inq-model">مدل</label>
                                <input id="inq-model" name="car_model" maxlength="100" class="form-input" placeholder="E250">
                            </div>
                            <div>
                                <label class="form-label" for="inq-year">سال</label>
                                <input id="inq-year" name="year" inputmode="numeric" maxlength="4" data-latin-digits class="form-input" placeholder="2018">
                            </div>
                        </div>
                        @auth
                            <input type="hidden" name="quantity" value="1">
                            <div class="sm:col-span-2">
                                <label class="form-label" for="inq-msg">توضیحات</label>
                                <textarea id="inq-msg" name="message" rows="2" maxlength="2000" class="form-input" placeholder="هر توضیحی که به پیدا کردن قطعه کمک می‌کند..."></textarea>
                            </div>
                        @endauth
                        <div class="sm:col-span-2 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-1">
                            <span class="text-[11px] text-slate-500">
                                @guest <i class="fa-solid fa-lock ml-1"></i> برای ثبت استعلام ابتدا وارد حساب کاربری شوید؛ اطلاعات فرم حفظ می‌شود. @endguest
                                @auth <i class="fa-solid fa-image ml-1"></i> برای افزودن عکس قطعه از <a href="{{ route('inquiries.create') }}" class="text-sky-400">فرم کامل استعلام</a> استفاده کنید. @endauth
                            </span>
                            <button type="submit" class="btn-gradient px-6">
                                <i class="fa-solid fa-paper-plane"></i> ثبت استعلام
                            </button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</section>
