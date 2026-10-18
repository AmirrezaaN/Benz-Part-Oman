<?php

namespace App\Services\Sms;

use App\Contracts\SmsSender;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Ready-to-use Kavenegar "verify lookup" driver.
 * Enable with: SMS_DRIVER=kavenegar, SMS_API_KEY=..., SMS_SENDER=<template name created in the Kavenegar panel>
 */
class KavenegarSmsSender implements SmsSender
{
    public function __construct(private string $apiKey, private string $template) {}

    public function sendOtp(string $phone, string $code): void
    {
        $response = Http::asForm()->timeout(10)->post(
            "https://api.kavenegar.com/v1/{$this->apiKey}/verify/lookup.json",
            ['receptor' => $phone, 'token' => $code, 'template' => $this->template]
        );

        if (! $response->successful()) {
            throw new RuntimeException('SMS provider error: '.$response->status());
        }
    }
}
