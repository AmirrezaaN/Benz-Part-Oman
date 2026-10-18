<?php

namespace App\Support;

use DateTimeInterface;

/**
 * Minimal Gregorian → Jalali (Persian) calendar conversion.
 */
class Jalali
{
    public const MONTHS = [
        1 => 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
        'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند',
    ];

    /** @return array{0:int,1:int,2:int} */
    public static function fromGregorian(int $gy, int $gm, int $gd): array
    {
        $gDaysInMonth = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        $gy2 = $gm > 2 ? $gy + 1 : $gy;
        $days = 355666 + (365 * $gy) + intdiv($gy2 + 3, 4) - intdiv($gy2 + 99, 100)
            + intdiv($gy2 + 399, 400) + $gd + $gDaysInMonth[$gm - 1];
        $jy = -1595 + (33 * intdiv($days, 12053));
        $days %= 12053;
        $jy += 4 * intdiv($days, 1461);
        $days %= 1461;
        if ($days > 365) {
            $jy += intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }
        if ($days < 186) {
            $jm = 1 + intdiv($days, 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + intdiv($days - 186, 30);
            $jd = 1 + (($days - 186) % 30);
        }

        return [$jy, $jm, $jd];
    }

    /**
     * Supported tokens: Y (year), m (2-digit month), n (month), d (2-digit day), j (day),
     * F (month name), H, i (time).
     */
    public static function format(?DateTimeInterface $date, string $format = 'Y/m/d'): string
    {
        if (! $date) {
            return '';
        }

        $date = \Carbon\Carbon::instance($date)->setTimezone(config('app.timezone'));
        [$y, $m, $d] = self::fromGregorian((int) $date->format('Y'), (int) $date->format('n'), (int) $date->format('j'));

        $map = [
            'Y' => (string) $y,
            'm' => str_pad((string) $m, 2, '0', STR_PAD_LEFT),
            'n' => (string) $m,
            'd' => str_pad((string) $d, 2, '0', STR_PAD_LEFT),
            'j' => (string) $d,
            'F' => self::MONTHS[$m],
            'H' => $date->format('H'),
            'i' => $date->format('i'),
        ];

        $out = '';
        foreach (mb_str_split($format) as $char) {
            $out .= $map[$char] ?? $char;
        }

        return $out;
    }
}
