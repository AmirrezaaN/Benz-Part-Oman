<?php

namespace App\Services\Sms;

use App\Contracts\SmsSender;
use Illuminate\Support\Facades\Log;

/**
 * Development driver: writes the SMS to storage/logs/laravel.log instead of sending it.
 * Replace with a real provider driver (see KavenegarSmsSender) once an API key is available.
 */
class LogSmsSender implements SmsSender
{
    public function sendOtp(string $phone, string $code): void
    {
        Log::info("[SMS] OTP for {$phone}: {$code}");
    }
}
