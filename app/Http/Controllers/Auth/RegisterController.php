<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Rules\Username;
use App\Services\OtpService;
use App\Support\Identifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Registration in three steps:
 *   1. email or mobile  →  2. verification code  →  3. name, username & password
 */
class RegisterController extends Controller
{
    private const SESSION = 'register';
    private const VERIFIED_TTL_MINUTES = 20;

    public function __construct(private OtpService $otp) {}

    public function show(): View
    {
        return view('auth.register');
    }

    public function sendCode(Request $request): RedirectResponse
    {
        $request->validate(['identifier' => ['required', 'string', 'max:190']], [], ['identifier' => 'ایمیل یا شماره موبایل']);

        $parsed = Identifier::parse($request->input('identifier'));
        if (! $parsed) {
            throw ValidationException::withMessages([
                'identifier' => 'یک ایمیل معتبر یا شماره موبایل ایرانی (مثل 09121234567) وارد کنید.',
            ]);
        }

        if (User::query()->where($parsed['type'], $parsed['value'])->exists()) {
            throw ValidationException::withMessages([
                'identifier' => 'این '.($parsed['type'] === 'email' ? 'ایمیل' : 'شماره').' قبلاً ثبت شده است. لطفاً وارد شوید.',
            ]);
        }

        $this->otp->send($parsed['value'], $parsed['type'], 'register');

        session([self::SESSION => ['type' => $parsed['type'], 'value' => $parsed['value'], 'verified_at' => null]]);

        return redirect()->route('register.verify');
    }

    public function showVerify(): View|RedirectResponse
    {
        $state = session(self::SESSION);
        if (! $state) {
            return redirect()->route('register');
        }

        return view('auth.verify', [
            'masked' => Identifier::mask($state['value']),
            'channel' => $state['type'],
            'action' => route('register.verify.check'),
            'resendAction' => route('register.resend'),
            'backUrl' => route('register'),
            'wait' => $this->otp->secondsUntilResend($state['value'], 'register'),
            'title' => 'تایید '.($state['type'] === 'email' ? 'ایمیل' : 'شماره موبایل'),
            'step' => 2,
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $state = session(self::SESSION);
        if (! $state) {
            return redirect()->route('register');
        }

        $request->validate(['code' => ['required', 'string', 'max:10']], [], ['code' => 'کد تایید']);

        $result = $this->otp->verify($state['value'], 'register', $request->input('code'));
        if ($result !== OtpService::OK) {
            throw ValidationException::withMessages(['code' => OtpService::message($result)]);
        }

        $state['verified_at'] = now()->timestamp;
        session([self::SESSION => $state]);

        return redirect()->route('register.complete');
    }

    public function resend(): RedirectResponse
    {
        $state = session(self::SESSION);
        if (! $state) {
            return redirect()->route('register');
        }

        $this->otp->send($state['value'], $state['type'], 'register', field: 'code');

        return back()->with('success', 'کد جدید ارسال شد.');
    }

    public function showComplete(): View|RedirectResponse
    {
        if (! $this->verifiedState()) {
            return redirect()->route('register');
        }

        return view('auth.complete', ['state' => session(self::SESSION)]);
    }

    public function complete(Request $request): RedirectResponse
    {
        $state = $this->verifiedState();
        if (! $state) {
            return redirect()->route('register')->withErrors(['identifier' => 'مهلت تکمیل ثبت‌نام تمام شد. لطفاً دوباره شروع کنید.']);
        }

        $request->merge(['username' => Str::lower(trim((string) $request->input('username')))]);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'username' => ['required', new Username, 'unique:users,username'],
            'password' => ['required', 'confirmed', Password::defaults(), 'max:128'],
            'terms' => ['accepted'],
        ], [], [
            'first_name' => 'نام', 'last_name' => 'نام خانوادگی', 'username' => 'نام کاربری',
            'password' => 'رمز عبور', 'terms' => 'قوانین سایت',
        ]);

        $user = DB::transaction(function () use ($data, $state) {
            // Guard against a race where the identifier got registered meanwhile.
            if (User::query()->where($state['type'], $state['value'])->lockForUpdate()->exists()) {
                throw ValidationException::withMessages(['username' => 'این حساب قبلاً ساخته شده است. لطفاً وارد شوید.']);
            }

            $user = new User([
                'first_name' => strip_tags($data['first_name']),
                'last_name' => strip_tags($data['last_name']),
                'username' => $data['username'],
                'password' => $data['password'],
            ]);
            $user->{$state['type']} = $state['value'];
            $user->{$state['type'].'_verified_at'} = now();
            $user->role_id = Role::customer()->id;
            $user->save();

            return $user;
        });

        session()->forget(self::SESSION);
        Auth::login($user);
        request()->session()->regenerate();
        $user->forceFill(['last_login_at' => now(), 'last_login_ip' => $request->ip()])->saveQuietly();

        return redirect()->intended(route('account.dashboard'))->with('success', 'حساب کاربری شما با موفقیت ساخته شد. خوش آمدید!');
    }

    private function verifiedState(): ?array
    {
        $state = session(self::SESSION);

        if (! $state || ! $state['verified_at']
            || now()->timestamp - $state['verified_at'] > self::VERIFIED_TTL_MINUTES * 60) {
            return null;
        }

        return $state;
    }
}
