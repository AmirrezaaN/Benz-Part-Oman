<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Support\Identifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings', ['settings' => Setting::all_cached()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->merge([
            'exchange_rate' => str_replace([',', '،', ' '], '', Identifier::normalizeDigits((string) $request->input('exchange_rate'))),
            'whatsapp_number' => preg_replace('/\D/', '', Identifier::normalizeDigits((string) $request->input('whatsapp_number'))),
        ]);

        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:80'],
            'exchange_rate' => ['required', 'integer', 'min:1', 'max:100000000'],
            'whatsapp_number' => ['required', 'digits_between:8,15'],
            'phone_oman' => ['nullable', 'string', 'max:30'],
            'phone_iran' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:120'],
            'address_oman' => ['nullable', 'string', 'max:255'],
            'address_iran' => ['nullable', 'string', 'max:255'],
            'working_hours' => ['nullable', 'string', 'max:120'],
            'delivery_days' => ['nullable', 'string', 'max:60'],
            'announcement' => ['nullable', 'string', 'max:200'],
            'instagram' => ['nullable', 'string', 'max:60', 'regex:/^[A-Za-z0-9_.]*$/'],
            'telegram' => ['nullable', 'string', 'max:60', 'regex:/^[A-Za-z0-9_]*$/'],
            'about_short' => ['nullable', 'string', 'max:500'],
        ], [
            'instagram.regex' => 'فقط نام کاربری اینستاگرام (بدون @ و لینک) را وارد کنید.',
            'telegram.regex' => 'فقط نام کاربری تلگرام (بدون @ و لینک) را وارد کنید.',
        ], [
            'site_name' => 'نام سایت', 'exchange_rate' => 'نرخ ریال عمان', 'whatsapp_number' => 'شماره واتساپ',
            'phone_oman' => 'تلفن عمان', 'phone_iran' => 'تلفن ایران', 'email' => 'ایمیل', 'address_oman' => 'آدرس عمان',
            'address_iran' => 'آدرس ایران', 'working_hours' => 'ساعات کاری', 'delivery_days' => 'زمان تحویل',
            'announcement' => 'متن اعلان بالای سایت', 'about_short' => 'درباره ما (کوتاه)',
        ]);

        $oldRate = exchange_rate();
        Setting::put($data);

        ActivityLog::record('settings.updated', null, $oldRate !== (int) $data['exchange_rate']
            ? 'تغییر نرخ ریال عمان از '.number_format($oldRate).' به '.number_format($data['exchange_rate']).' تومان'
            : 'به‌روزرسانی تنظیمات سایت');

        return back()->with('success', 'تنظیمات ذخیره شد.');
    }
}
