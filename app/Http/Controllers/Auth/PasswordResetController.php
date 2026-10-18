<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Support\Identifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    private const SESSION = 'password_reset';

    public function __construct(private OtpService $otp) {}

    public function show(): View
    {
        return view('auth.forgot-password');
    }

    public function sendCode(Request $request): RedirectResponse
    {
        $request->validate(['identifier' => ['required', 'string', 'max:190']], [], ['identifier' => 'ایمیل یا شماره موبایل']);

        $parsed = Identifier::parse($request->input('identifier'));
        if (! $parsed) {
            throw ValidationException::withMessages(['identifier' => 'یک ایمیل معتبر یا شماره موبایل ایرانی وارد کنید.']);
        }

        $user = User::query()->where($parsed['type'], $parsed['value'])->first();

        // Only send when the account exists, but respond identically either way.
        if ($user && ! $user->isBanned()) {
            $this->otp->send($parsed['value'], $parsed['type'], 'reset', $user->id);
        }

        session([self::SESSION => $parsed]);

        return redirect()->route('password.reset')
            ->with('success', 'اگر حسابی با این مشخصات وجود داشته باشد، کد بازیابی برای آن ارسال شد.');
    }

    public function showReset(): View|RedirectResponse
    {
        $state = session(self::SESSION);
        if (! $state) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password', ['masked' => Identifier::mask($state['value']), 'channel' => $state['type']]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $state = session(self::SESSION);
        if (! $state) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'code' => ['required', 'string', 'max:10'],
            'password' => ['required', 'confirmed', Password::defaults(), 'max:128'],
        ], [], ['code' => 'کد تایید', 'password' => 'رمز عبور جدید']);

        $result = $this->otp->verify($state['value'], 'reset', $request->input('code'));
        if ($result !== OtpService::OK) {
            throw ValidationException::withMessages(['code' => OtpService::message($result)]);
        }

        $user = User::query()->where($state['type'], $state['value'])->firstOrFail();
        $user->password = $request->input('password');
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Sign the user out of every other device
        DB::table('sessions')->where('user_id', $user->id)->delete();

        session()->forget(self::SESSION);

        return redirect()->route('login')->with('success', 'رمز عبور با موفقیت تغییر کرد. اکنون می‌توانید وارد شوید.');
    }
}
