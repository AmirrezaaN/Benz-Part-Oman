@extends('pages.layout')

@section('title', 'راهنمای خرید')
@section('lead', 'خرید قطعه از انبار عمان در چند قدم ساده')

@section('page')
    <ol class="max-w-3xl space-y-4">
        @foreach ([
            ['fa-user-plus', 'ساخت حساب کاربری', 'با ایمیل یا شماره موبایل ثبت‌نام کنید و کد تایید را وارد کنید.'],
            ['fa-magnifying-glass', 'پیدا کردن قطعه', 'با جستجوی پارت نامبر، انتخاب برند/مدل/شاسی یا دسته‌بندی‌ها قطعه را پیدا کنید. اگر پیدا نکردید، استعلام بگیرید.'],
            ['fa-cart-shopping', 'ثبت سفارش و استعلام موجودی', 'قطعات را به سبد خرید اضافه کرده و آدرس ارسال را وارد کنید. سفارش شما ابتدا برای استعلام موجودی از انبار عمان بررسی می‌شود؛ در این مرحله پرداختی انجام نمی‌شود.'],
            ['fa-headset', 'تایید موجودی و پرداخت', 'پس از تایید موجودی، قیمت نهایی و هزینه ارسال اعلام و پرداخت هماهنگ می‌شود.'],
            ['fa-plane-departure', 'ارسال و تحویل', 'سفارش از مسقط ارسال و '.(setting('delivery_days') ? setting('delivery_days').' ' : '').'تحویل داده می‌شود. وضعیت را از «سفارش‌های من» پیگیری کنید.'],
        ] as $i => [$icon, $title, $text])
            <li class="card p-5 flex gap-4">
                <span class="w-12 h-12 shrink-0 rounded-2xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center text-lg"><i class="fa-solid {{ $icon }}"></i></span>
                <div>
                    <h2 class="text-sm font-bold text-white">{{ fa_num($i + 1) }}. {{ $title }}</h2>
                    <p class="text-xs text-slate-400 mt-1.5 leading-6">{{ $text }}</p>
                </div>
            </li>
        @endforeach
    </ol>
@endsection
