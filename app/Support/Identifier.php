<?php

namespace App\Support;

/**
 * Normalises a login identifier (email or Iranian mobile number).
 */
class Identifier
{
    public const EMAIL = 'email';
    public const PHONE = 'phone';

    public static function normalizeDigits(string $value): string
    {
        return strtr($value, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);
    }

    public static function normalizePhone(string $value): ?string
    {
        $phone = preg_replace('/[\s\-\(\)]/', '', self::normalizeDigits(trim($value)));

        if (str_starts_with($phone, '+98')) {
            $phone = '0'.substr($phone, 3);
        } elseif (str_starts_with($phone, '0098')) {
            $phone = '0'.substr($phone, 4);
        } elseif (preg_match('/^989\d{9}$/', $phone)) {
            $phone = '0'.substr($phone, 2);
        } elseif (preg_match('/^9\d{9}$/', $phone)) {
            $phone = '0'.$phone;
        }

        return preg_match('/^09\d{9}$/', $phone) ? $phone : null;
    }

    /**
     * @return array{type: string, value: string}|null
     */
    public static function parse(?string $input): ?array
    {
        $input = trim(self::normalizeDigits((string) $input));

        if ($input === '') {
            return null;
        }

        if (str_contains($input, '@')) {
            $email = mb_strtolower($input);

            return filter_var($email, FILTER_VALIDATE_EMAIL) && mb_strlen($email) <= 190
                ? ['type' => self::EMAIL, 'value' => $email]
                : null;
        }

        $phone = self::normalizePhone($input);

        return $phone ? ['type' => self::PHONE, 'value' => $phone] : null;
    }

    /** Masks an identifier for display: 0912***4567 / al***@gmail.com */
    public static function mask(string $value): string
    {
        if (str_contains($value, '@')) {
            [$name, $domain] = explode('@', $value, 2);

            return mb_substr($name, 0, 2).'***@'.$domain;
        }

        return substr($value, 0, 4).'***'.substr($value, -4);
    }
}
