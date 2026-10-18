<?php

return [

    // URL prefix of the admin panel. Changing it to something non-obvious adds a small layer of obscurity.
    'admin_path' => env('ADMIN_PATH', 'admin'),

    // Owner account — created/updated by `php artisan db:seed --class=SuperAdminSeeder`
    'super_admin' => [
        'email' => env('SUPER_ADMIN_EMAIL'),
        'password' => env('SUPER_ADMIN_PASSWORD'),
        'username' => env('SUPER_ADMIN_USERNAME', 'owner'),
    ],

    'otp' => [
        'length' => 6,
        'ttl' => 120,             // seconds a code stays valid
        'resend_cooldown' => 60,  // seconds between two sends
        'max_attempts' => 5,      // wrong tries before the code is burned
        // Shows the code on screen — ONLY honoured when APP_ENV=local. For testing without SMS/SMTP.
        'debug_show' => env('OTP_DEBUG_SHOW', false),
    ],

    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),
        'api_key' => env('SMS_API_KEY'),
        'sender' => env('SMS_SENDER'),
    ],

    'conditions' => [
        'new' => 'نو اصلی (Genuine)',
        'oem' => 'نو OEM',
        'aftermarket' => 'نو غیر اصلی (Aftermarket)',
        'used' => 'استوک / کارکرده',
        'refurbished' => 'بازسازی‌شده',
    ],

    'countries' => [
        'Germany' => 'آلمان',
        'Japan' => 'ژاپن',
        'South Korea' => 'کره جنوبی',
        'China' => 'چین',
        'USA' => 'آمریکا',
        'Italy' => 'ایتالیا',
        'France' => 'فرانسه',
        'UK' => 'انگلستان',
        'Sweden' => 'سوئد',
        'Spain' => 'اسپانیا',
        'Turkey' => 'ترکیه',
        'Taiwan' => 'تایوان',
        'Thailand' => 'تایلند',
        'India' => 'هند',
        'UAE' => 'امارات',
        'Oman' => 'عمان',
        'Iran' => 'ایران',
    ],

    'inquiry_statuses' => [
        'pending' => ['label' => 'در انتظار بررسی', 'color' => 'amber'],
        'reviewing' => ['label' => 'در حال بررسی', 'color' => 'sky'],
        'answered' => ['label' => 'پاسخ داده شد', 'color' => 'emerald'],
        'closed' => ['label' => 'بسته شده', 'color' => 'slate'],
    ],

    // "short" is used in the customer's order progress bar
    'order_statuses' => [
        'pending' => ['label' => 'در حال استعلام موجودی', 'short' => 'استعلام موجودی', 'color' => 'amber'],
        'confirmed' => ['label' => 'موجودی تایید شد - در انتظار پرداخت', 'short' => 'تایید موجودی', 'color' => 'sky'],
        'paid' => ['label' => 'پرداخت شده - در حال آماده‌سازی', 'short' => 'پرداخت', 'color' => 'indigo'],
        'shipping' => ['label' => 'ارسال شده از عمان', 'short' => 'ارسال', 'color' => 'violet'],
        'delivered' => ['label' => 'تحویل داده شد', 'short' => 'تحویل', 'color' => 'emerald'],
        'cancelled' => ['label' => 'لغو شده / عدم موجودی', 'short' => 'لغو', 'color' => 'rose'],
    ],

    // Statuses in which the stock has been reserved/deducted
    'stock_holding_statuses' => ['confirmed', 'paid', 'shipping', 'delivered'],

    'provinces' => [
        'تهران', 'البرز', 'اصفهان', 'فارس', 'خراسان رضوی', 'آذربایجان شرقی', 'آذربایجان غربی', 'اردبیل',
        'ایلام', 'بوشهر', 'چهارمحال و بختیاری', 'خراسان جنوبی', 'خراسان شمالی', 'خوزستان', 'زنجان',
        'سمنان', 'سیستان و بلوچستان', 'قزوین', 'قم', 'کردستان', 'کرمان', 'کرمانشاه',
        'کهگیلویه و بویراحمد', 'گلستان', 'گیلان', 'لرستان', 'مازندران', 'مرکزی', 'هرمزگان', 'همدان', 'یزد',
    ],
];
