<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * 4-30 chars, starts with a letter, only english letters, digits, "_" and ".".
 */
class Username implements ValidationRule
{
    private const RESERVED = ['admin', 'administrator', 'root', 'support', 'owner', 'system', 'benz', 'benzomanpart', 'benzpartoman', 'moderator'];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^[a-z][a-z0-9_.]{3,29}$/', $value)) {
            $fail('نام کاربری باید ۴ تا ۳۰ کاراکتر، با حرف انگلیسی شروع شود و فقط شامل حروف انگلیسی، عدد، _ و . باشد.');

            return;
        }

        if (in_array($value, self::RESERVED, true)) {
            $fail('این نام کاربری رزرو شده است.');
        }
    }
}
