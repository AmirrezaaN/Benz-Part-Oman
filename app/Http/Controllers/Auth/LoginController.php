<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Support\Identifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:190'],
            'password' => ['required', 'string', 'max:128'],
        ], [], ['identifier' => 'ایمیل، موبایل یا نام کاربری']);

        $throttleKey = 'login:'.Str::lower(Identifier::normalizeDigits($data['identifier'])).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'identifier' => 'تلاش‌های ناموفق زیاد بود. لطفاً '.fa_num(RateLimiter::availableIn($throttleKey)).' ثانیه دیگر دوباره امتحان کنید.',
            ]);
        }

        $user = $this->findUser($data['identifier']);

        // Always run a hash check so response time does not reveal whether the account exists.
        $valid = Hash::check($data['password'], $user?->password ?? '$2y$12$'.str_repeat('a', 53));

        if (! $user || ! $valid) {
            RateLimiter::hit($throttleKey, 60);
            throw ValidationException::withMessages(['identifier' => 'اطلاعات ورود صحیح نیست.']);
        }

        if ($user->isBanned()) {
            throw ValidationException::withMessages(['identifier' => 'حساب کاربری شما مسدود شده است. برای پیگیری با پشتیبانی تماس بگیرید.']);
        }

        RateLimiter::clear($throttleKey);
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now(), 'last_login_ip' => $request->ip()])->saveQuietly();

        if ($user->canAccessAdmin()) {
            ActivityLog::record('auth.admin_login', $user, 'ورود به حساب مدیریتی');
        }

        $home = $user->canAccessAdmin() ? route('admin.dashboard') : route('account.dashboard');

        return redirect()->intended($home)->with('success', 'خوش آمدید، '.$user->full_name);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function findUser(string $input): ?User
    {
        $parsed = Identifier::parse($input);

        if ($parsed) {
            return User::query()->with('role')->where($parsed['type'], $parsed['value'])->first();
        }

        return User::query()->with('role')->where('username', Str::lower(trim($input)))->first();
    }
}
