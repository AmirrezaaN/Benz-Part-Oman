<footer class="bg-slate-950 border-t border-slate-800/80 mt-12">
    {{-- Trust strip --}}
    <div class="border-b border-slate-800/80">
        <div class="container-x grid grid-cols-2 lg:grid-cols-4 gap-4 py-6">
            @foreach ([
                ['fa-shield-halved', 'ضمانت اصالت کالا', 'قطعات Genuine و OEM با پارت نامبر رسمی'],
                ['fa-plane-arrival', 'واردات مستقیم از عمان', 'بدون واسطه از انبارهای مسقط و دبی'],
                ['fa-truck-fast', 'ارسال به سراسر ایران', setting('delivery_days') ? 'تحویل '.setting('delivery_days') : 'ارسال سریع و ایمن'],
                ['fa-headset', 'مشاوره تخصصی', 'تطبیق قطعه با شماره شاسی (VIN)'],
            ] as [$icon, $title, $text])
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 shrink-0 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center">
                        <i class="fa-solid {{ $icon }}"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white">{{ $title }}</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $text }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="container-x py-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-8 text-xs">
        {{-- About --}}
        <div class="lg:col-span-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-4">
                <div class="w-10 h-10 bg-slate-900 border border-slate-700 rounded-xl flex items-center justify-center text-sky-400 text-xl"><i class="fa-solid fa-certificate"></i></div>
                <div>
                    <span class="text-lg font-black text-white block leading-tight">بنز پارت <span class="text-sky-400">عمان</span></span>
                    <span class="text-[9px] text-slate-500 block font-mono uppercase tracking-widest">BENZ PART OMAN</span>
                </div>
            </a>
            @if (setting('about_short'))
                <p class="text-slate-400 leading-7">{{ setting('about_short') }}</p>
            @endif
            <div class="flex items-center gap-2 mt-4">
                <a href="{{ whatsapp_link() }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 hover:border-emerald-400 hover:text-emerald-400 flex items-center justify-center transition" aria-label="واتساپ"><i class="fa-brands fa-whatsapp"></i></a>
                @if (setting('instagram'))
                    <a href="https://instagram.com/{{ setting('instagram') }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 hover:border-pink-400 hover:text-pink-400 flex items-center justify-center transition" aria-label="اینستاگرام"><i class="fa-brands fa-instagram"></i></a>
                @endif
                @if (setting('telegram'))
                    <a href="https://t.me/{{ setting('telegram') }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 hover:border-sky-400 hover:text-sky-400 flex items-center justify-center transition" aria-label="تلگرام"><i class="fa-brands fa-telegram"></i></a>
                @endif
                @if (setting('email'))
                    <a href="mailto:{{ setting('email') }}" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 hover:border-sky-400 hover:text-sky-400 flex items-center justify-center transition" aria-label="ایمیل"><i class="fa-solid fa-envelope"></i></a>
                @endif
            </div>
        </div>

        {{-- Categories --}}
        <div class="lg:col-span-3">
            <h4 class="text-sm font-bold text-white mb-4">دسته‌بندی قطعات</h4>
            <ul class="space-y-2.5 text-slate-400">
                @foreach ($navCategories->take(7) as $cat)
                    <li><a href="{{ route('categories.show', $cat) }}" class="hover:text-sky-400 transition flex items-center gap-2"><i class="fa-solid fa-angle-left text-[9px] text-slate-600"></i>{{ $cat->name }}</a></li>
                @endforeach
            </ul>
        </div>

        {{-- Links --}}
        <div class="lg:col-span-2">
            <h4 class="text-sm font-bold text-white mb-4">دسترسی سریع</h4>
            <ul class="space-y-2.5 text-slate-400">
                @foreach ([
                    ['products.index', 'همه محصولات'],
                    ['inquiries.create', 'استعلام قطعه'],
                    ['pages.how-to-buy', 'راهنمای خرید'],
                    ['pages.faq', 'سوالات متداول'],
                    ['pages.about', 'درباره ما'],
                    ['pages.contact', 'تماس با ما'],
                    ['pages.terms', 'قوانین و حریم خصوصی'],
                ] as [$route, $label])
                    <li><a href="{{ route($route) }}" class="hover:text-sky-400 transition flex items-center gap-2"><i class="fa-solid fa-angle-left text-[9px] text-slate-600"></i>{{ $label }}</a></li>
                @endforeach
            </ul>
        </div>

        {{-- Contact --}}
        <div class="lg:col-span-3">
            <h4 class="text-sm font-bold text-white mb-4">ارتباط با ما</h4>
            <ul class="space-y-3 text-slate-400">
                @if (setting('address_oman'))
                    <li class="flex gap-2.5"><i class="fa-solid fa-location-dot text-sky-400 mt-1"></i><span><span class="text-slate-500">عمان:</span> {{ setting('address_oman') }}</span></li>
                @endif
                @if (setting('address_iran'))
                    <li class="flex gap-2.5"><i class="fa-solid fa-location-dot text-sky-400 mt-1"></i><span><span class="text-slate-500">ایران:</span> {{ setting('address_iran') }}</span></li>
                @endif
                @if (setting('phone_iran'))
                    <li class="flex gap-2.5"><i class="fa-solid fa-phone text-sky-400 mt-1"></i><span dir="ltr">{{ setting('phone_iran') }}</span></li>
                @endif
                @if (setting('phone_oman'))
                    <li class="flex gap-2.5"><i class="fa-brands fa-whatsapp text-emerald-400 mt-1"></i><span dir="ltr">{{ setting('phone_oman') }}</span></li>
                @endif
                @if (setting('email'))
                    <li class="flex gap-2.5"><i class="fa-solid fa-envelope text-sky-400 mt-1"></i><span dir="ltr">{{ setting('email') }}</span></li>
                @endif
                @if (setting('working_hours'))
                    <li class="flex gap-2.5"><i class="fa-regular fa-clock text-sky-400 mt-1"></i><span>{{ setting('working_hours') }}</span></li>
                @endif
            </ul>
        </div>
    </div>

    <div class="border-t border-slate-800/80 py-4 text-center text-[11px] text-slate-500">
        <div class="container-x flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>© {{ fa_num(\App\Support\Jalali::format(now(), 'Y')) }} {{ setting('site_name') }} | فروشگاه آنلاین و واردات مستقیم قطعات خودرو از عمان</span>
            <span class="flex items-center gap-3">
                <span class="badge bg-emerald-500/10 text-emerald-400 border-emerald-500/20">اصلی Genuine / OEM</span>
                <span class="badge bg-sky-500/10 text-sky-400 border-sky-500/20" dir="ltr">OMR ⇄ Toman</span>
            </span>
        </div>
    </div>
</footer>
