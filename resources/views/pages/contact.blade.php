@extends('pages.layout')

@section('title', 'تماس با ما')
@section('lead', 'برای مشاوره خرید، استعلام قطعه یا پیگیری سفارش از راه‌های زیر با ما در ارتباط باشید.')

@section('page')
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach (array_filter([
            ['fa-brands fa-whatsapp', 'واتساپ (مسقط)', setting('phone_oman'), whatsapp_link(), 'text-emerald-400'],
            ['fa-solid fa-phone', 'تلفن ایران', setting('phone_iran'), 'tel:'.preg_replace('/[^\d+]/', '', (string) setting('phone_iran')), 'text-sky-400'],
            ['fa-solid fa-envelope', 'ایمیل', setting('email'), 'mailto:'.setting('email'), 'text-sky-400'],
            ['fa-solid fa-location-dot', 'آدرس عمان', setting('address_oman'), null, 'text-sky-400'],
            ['fa-solid fa-location-dot', 'آدرس ایران', setting('address_iran'), null, 'text-sky-400'],
            ['fa-regular fa-clock', 'ساعات کاری', setting('working_hours'), null, 'text-sky-400'],
        ], fn ($card) => filled($card[2])) as [$icon, $title, $value, $link, $color])
            <div class="card p-5">
                <i class="{{ $icon }} {{ $color }} text-2xl"></i>
                <div class="text-xs text-slate-500 mt-3">{{ $title }}</div>
                @if ($link)
                    <a href="{{ $link }}" @if (str_starts_with($link, 'http')) target="_blank" rel="noopener" @endif class="text-sm font-bold text-white mt-1 block hover:text-sky-400" dir="ltr">{{ $value }}</a>
                @else
                    <div class="text-sm font-bold text-white mt-1">{{ $value }}</div>
                @endif
            </div>
        @endforeach
    </div>
    <div class="-mx-4 sm:-mx-6 lg:-mx-8">
        @include('partials.inquiry-section')
    </div>
@endsection
