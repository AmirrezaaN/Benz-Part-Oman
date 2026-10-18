<?php

namespace App\Services;

use App\Contracts\SmsSender;
use App\Mail\VerificationCodeMail;
use App\Models\VerificationCode;
use App\Support\Identifier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * One-time verification codes sent by email or SMS.
 * Codes are stored as an HMAC (never plain text), expire quickly and burn after N wrong tries.
 */
class OtpService
{
    public const OK = 'ok';
    public const INVALID = 'invalid';
    public const EXPIRED = 'expired';
    public const LOCKED = 'locked';

    public function __construct(private SmsSender $sms) {}

    /**
     * @throws ValidationException when the resend cooldown has not passed or delivery fails
     */
    public function send(string $identifier, string $channel, string $purpose, ?int $userId = null, string $field = 'identifier'): void
    {
        $wait = $this->secondsUntilResend($identifier, $purpose);
        if ($wait > 0) {
            throw ValidationException::withMessages([
                $field => 'برای ارسال مجدد کد لطفاً '.fa_num($wait).' ثانیه صبر کنید.',
            ]);
        }

        // Invalidate any previous open code for the same identifier/purpose
        VerificationCode::query()
            ->where('identifier', $identifier)->where('purpose', $purpose)->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $length = (int) config('shop.otp.length', 6);
        $code = (string) random_int(10 ** ($length - 1), (10 ** $length) - 1);

        VerificationCode::query()->create([
            'identifier' => $identifier,
            'channel' => $channel,
            'purpose' => $purpose,
            'code_hash' => $this->hash($code),
            'user_id' => $userId,
            'ip' => request()->ip(),
            'expires_at' => now()->addSeconds((int) config('shop.otp.ttl', 120)),
        ]);

        try {
            if ($channel === Identifier::EMAIL) {
                Mail::to($identifier)->send(new VerificationCodeMail($code, $purpose));
            } else {
                $this->sms->sendOtp($identifier, $code);
            }
        } catch (Throwable $e) {
            Log::error('OTP delivery failed', ['channel' => $channel, 'error' => $e->getMessage()]);
            throw ValidationException::withMessages([
                $field => 'ارسال کد با خطا مواجه شد. لطفاً چند دقیقه دیگر دوباره تلاش کنید.',
            ]);
        }

        if (app()->isLocal() && config('shop.otp.debug_show')) {
            session()->flash('otp_debug', $code);
        }
    }

    public function verify(string $identifier, string $purpose, string $code): string
    {
        $record = VerificationCode::query()
            ->where('identifier', $identifier)->where('purpose', $purpose)->whereNull('consumed_at')
            ->latest('id')->first();

        if (! $record || $record->expires_at->isPast()) {
            return self::EXPIRED;
        }

        if ($record->attempts >= (int) config('shop.otp.max_attempts', 5)) {
            $record->update(['consumed_at' => now()]);

            return self::LOCKED;
        }

        $code = Identifier::normalizeDigits(trim($code));

        if (! hash_equals($record->code_hash, $this->hash($code))) {
            $record->increment('attempts');

            return $record->attempts >= (int) config('shop.otp.max_attempts', 5) ? self::LOCKED : self::INVALID;
        }

        $record->update(['consumed_at' => now()]);

        return self::OK;
    }

    public function secondsUntilResend(string $identifier, string $purpose): int
    {
        $last = VerificationCode::query()
            ->where('identifier', $identifier)->where('purpose', $purpose)
            ->latest('id')->first();

        if (! $last) {
            return 0;
        }

        $cooldown = (int) config('shop.otp.resend_cooldown', 60);

        return max(0, $cooldown - (int) $last->created_at->diffInSeconds(now()));
    }

    public static function message(string $result): string
    {
        return match ($result) {
            self::EXPIRED => 'کد منقضی شده است. لطفاً کد جدید دریافت کنید.',
            self::LOCKED => 'تعداد تلاش‌های ناموفق زیاد بود. لطفاً کد جدید دریافت کنید.',
            default => 'کد وارد شده صحیح نیست.',
        };
    }

    private function hash(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }
}
