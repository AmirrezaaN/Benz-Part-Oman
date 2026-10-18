<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Support\Identifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Adding / changing the account email or mobile number. The new value must be verified
 * with a code, and the current password is required to start the change.
 */
class ContactController extends Controller
{
    private const SESSION = 'contact_change';

    public function __construct(private OtpService $otp) {}

    public function edit(Request $request, string $channel): View
    {
        return view('account.contact-edit', ['channel' => $channel, 'user' => $request->user()]);
    }

    public function sendCode(Request $request, string $channel): RedirectResponse
    {
        $label = $channel === 'email' ? 'ایمیل' : 'شماره موبایل';

        $request->validate([
            'value' => ['required', 'string', 'max:190'],
            'current_password' => ['required', 'current_password'],
        ], [], ['value' => $label.' جدید', 'current_password' => 'رمز عبور فعلی']);

        $parsed = Identifier::parse($request->input('value'));
        if (! $parsed || $parsed['type'] !== $channel) {
            throw ValidationException::withMessages(['value' => $label.' وارد شده معتبر نیست.']);
        }

        if ($parsed['value'] === $request->user()->{$channel}) {
            throw ValidationException::withMessages(['value' => 'این '.$label.' هم‌اکنون روی حساب شما ثبت است.']);
        }

        if (User::query()->where($channel, $parsed['value'])->exists()) {
            throw ValidationException::withMessages(['value' => 'این '.$label.' متعلق به حساب دیگری است.']);
        }

        $this->otp->send($parsed['value'], $channel, 'contact', $request->user()->id, 'value');
        session([self::SESSION => ['channel' => $channel, 'value' => $parsed['value']]]);

        return redirect()->route('account.contact.verify', $channel);
    }

    public function showVerify(string $channel): View|RedirectResponse
    {
        $state = session(self::SESSION);
        if (! $state || $state['channel'] !== $channel) {
            return redirect()->route('account.contact.edit', $channel);
        }

        return view('auth.verify', [
            'masked' => Identifier::mask($state['value']),
            'channel' => $channel,
            'action' => route('account.contact.verify.check', $channel),
            'resendAction' => null,
            'backUrl' => route('account.contact.edit', $channel),
            'wait' => 0,
            'title' => 'تایید '.($channel === 'email' ? 'ایمیل' : 'شماره موبایل').' جدید',
            'step' => null,
        ]);
    }

    public function verify(Request $request, string $channel): RedirectResponse
    {
        $state = session(self::SESSION);
        if (! $state || $state['channel'] !== $channel) {
            return redirect()->route('account.contact.edit', $channel);
        }

        $request->validate(['code' => ['required', 'string', 'max:10']], [], ['code' => 'کد تایید']);

        $result = $this->otp->verify($state['value'], 'contact', $request->input('code'));
        if ($result !== OtpService::OK) {
            throw ValidationException::withMessages(['code' => OtpService::message($result)]);
        }

        if (User::query()->where($channel, $state['value'])->exists()) {
            session()->forget(self::SESSION);

            return redirect()->route('account.profile')->with('error', 'این مورد در این فاصله به حساب دیگری متصل شد.');
        }

        $user = $request->user();
        $user->{$channel} = $state['value'];
        $user->{$channel.'_verified_at'} = now();
        $user->save();

        session()->forget(self::SESSION);

        return redirect()->route('account.profile')->with('success', ($channel === 'email' ? 'ایمیل' : 'شماره موبایل').' حساب با موفقیت به‌روزرسانی شد.');
    }
}
