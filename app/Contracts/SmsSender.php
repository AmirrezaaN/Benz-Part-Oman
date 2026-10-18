<?php

namespace App\Contracts;

interface SmsSender
{
    /**
     * Sends a one-time verification code to an Iranian mobile number (09xxxxxxxxx).
     * Most Iranian providers (Kavenegar, SMS.ir, Melipayamak ...) have a dedicated
     * "verify/lookup" template API for OTPs which is faster and cheaper than plain SMS.
     */
    public function sendOtp(string $phone, string $code): void;
}
