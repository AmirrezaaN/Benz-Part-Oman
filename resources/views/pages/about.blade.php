@extends('pages.layout')

@section('title', 'درباره بنز پارت عمان')
@if (setting('about_short'))
    @section('lead', setting('about_short'))
@endif

@section('page')
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card p-6 prose-fa">
            <h2>ما چه کاری انجام می‌دهیم؟</h2>
            <p>بنز پارت عمان با انبار و دفتر در منطقه صنعتی معبیله مسقط، قطعات اصلی (Genuine)، OEM و استوک باکیفیت خودروهای مرسدس بنز و سایر برندهای لوکس را مستقیماً از بازار عمان و امارات تهیه کرده و با کوتاه‌ترین زمان ممکن به دست مشتریان در سراسر ایران می‌رساند.</p>
            <h2>چرا عمان؟</h2>
            <p>بازار قطعات عمان و امارات یکی از بزرگ‌ترین بازارهای قطعات خودروهای اروپایی و ژاپنی در منطقه است. دسترسی مستقیم به نمایندگی‌ها و تامین‌کنندگان این بازار، امکان تهیه قطعات اصلی با قیمت منصفانه و اصالت تضمین‌شده را فراهم می‌کند.</p>
            <h2>تعهدات ما</h2>
            <ul>
                <li>تطبیق دقیق قطعه با شماره شاسی (VIN) و کاتالوگ رسمی سازنده</li>
                <li>اعلام شفاف قیمت به ریال عمان و تومان</li>
                <li>بسته‌بندی ایمن و ارسال سریع</li>
                <li>پاسخگویی و پیگیری تا زمان تحویل</li>
            </ul>
        </div>
        <div class="space-y-4">
            @foreach (array_filter([['fa-warehouse', 'انبار مسقط', setting('address_oman')], ['fa-truck-fast', 'زمان تحویل', setting('delivery_days')], ['fa-clock', 'ساعات پاسخگویی', setting('working_hours')]], fn ($card) => filled($card[2])) as [$icon, $t, $v])
                <div class="card p-5 flex items-center gap-4">
                    <span class="w-12 h-12 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-lg"><i class="fa-solid {{ $icon }}"></i></span>
                    <div><div class="text-xs text-slate-500">{{ $t }}</div><div class="text-sm font-bold text-white mt-1">{{ $v }}</div></div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
