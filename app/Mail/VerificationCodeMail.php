<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code, public string $purpose) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'کد تایید '.setting('site_name').': '.$this->code);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.verification-code', with: [
            'purposeLabel' => match ($this->purpose) {
                'register' => 'ثبت‌نام',
                'reset' => 'بازیابی رمز عبور',
                default => 'تایید اطلاعات تماس',
            },
            'minutes' => (int) ceil(config('shop.otp.ttl', 120) / 60),
        ]);
    }
}
