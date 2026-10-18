<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    public const CACHE_KEY = 'site_settings';

    public const DEFAULTS = [
        'site_name' => 'بنز پارت عمان',
        'exchange_rate' => 147000,
        'whatsapp_number' => '96890000000',
        'phone_oman' => '+968 9000 0000',
        'phone_iran' => '021-88880000',
        'email' => 'info@benzomanpart.com',
        'address_oman' => 'مسقط، منطقه صنعتی المعبیله',
        'address_iran' => 'تهران',
        'working_hours' => 'شنبه تا پنجشنبه ۹ الی ۱۸',
        'delivery_days' => '۳ الی ۵ روز کاری',
        'announcement' => 'استعلام آنلاین موجودی انبار معبیله مسقط و دبی',
        'instagram' => '',
        'telegram' => '',
        'about_short' => 'واردات مستقیم قطعات اصلی مرسدس بنز و سایر خودروهای لوکس از انبارهای عمان به ایران، با ضمانت اصالت کالا و ارسال سریع.',
    ];

    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['key', 'value'];

    public static function all_cached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            if (! Schema::hasTable('settings')) {
                return self::DEFAULTS;
            }

            return array_merge(self::DEFAULTS, self::query()->pluck('value', 'key')->all());
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::all_cached()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            self::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget(self::CACHE_KEY);
    }
}
