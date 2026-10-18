<?php

use App\Models\Setting;
use App\Support\Jalali;

if (! function_exists('fa_num')) {
    /** Converts Latin digits to Persian digits. */
    function fa_num(string|int|float|null $value): string
    {
        return strtr((string) $value, ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
            '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
    }
}

if (! function_exists('jdate')) {
    function jdate(?DateTimeInterface $date, string $format = 'Y/m/d'): string
    {
        return fa_num(Jalali::format($date, $format));
    }
}

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('exchange_rate')) {
    /** Toman per 1 Omani Rial. */
    function exchange_rate(): int
    {
        return (int) setting('exchange_rate', 0);
    }
}

if (! function_exists('format_toman')) {
    function format_toman(int|float|null $amount): string
    {
        return $amount === null ? '' : fa_num(number_format((float) $amount));
    }
}

if (! function_exists('format_omr')) {
    function format_omr(int|float|string|null $amount): string
    {
        if ($amount === null) {
            return '';
        }

        // OMR has 3 decimals (baisa); hide trailing zeros for round values.
        return rtrim(rtrim(number_format((float) $amount, 3), '0'), '.');
    }
}

if (! function_exists('omr_to_toman')) {
    function omr_to_toman(int|float|string|null $omr): ?int
    {
        if ($omr === null || exchange_rate() <= 0) {
            return null;
        }

        // Round to nearest 1,000 Toman for clean prices
        return (int) (round(((float) $omr * exchange_rate()) / 1000) * 1000);
    }
}

if (! function_exists('whatsapp_link')) {
    /**
     * Link that opens a WhatsApp chat (app on mobile, app/web on desktop).
     * Uses api.whatsapp.com rather than the wa.me short domain, which is unreachable on many Iranian networks.
     * $phone defaults to the store number; Iranian numbers like 0912... are converted to 98912...
     */
    function whatsapp_link(?string $text = null, ?string $phone = null): string
    {
        $number = preg_replace('/\D/', '', (string) ($phone ?? setting('whatsapp_number', '')));
        if (preg_match('/^09\d{9}$/', $number)) {
            $number = '98'.substr($number, 1);
        }

        return 'https://api.whatsapp.com/send?'.http_build_query(array_filter(['phone' => $number, 'text' => $text]), '', '&', PHP_QUERY_RFC3986);
    }
}
