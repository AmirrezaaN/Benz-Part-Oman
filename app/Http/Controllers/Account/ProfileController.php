<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Rules\Username;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('account.profile', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $request->merge(['username' => Str::lower(trim((string) $request->input('username')))]);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'username' => ['required', new Username, Rule::unique('users', 'username')->ignore($user->id)],
            'province' => ['nullable', Rule::in(config('shop.provinces'))],
            'city' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'postal_code' => ['nullable', 'digits:10'],
        ], [], [
            'first_name' => 'نام', 'last_name' => 'نام خانوادگی', 'username' => 'نام کاربری',
            'province' => 'استان', 'city' => 'شهر', 'address' => 'آدرس', 'postal_code' => 'کد پستی',
        ]);

        $user->fill(array_map(fn ($v) => is_string($v) ? strip_tags($v) : $v, $data))->save();

        return back()->with('success', 'اطلاعات حساب با موفقیت ذخیره شد.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults(), 'max:128', 'different:current_password'],
        ], [], ['current_password' => 'رمز عبور فعلی', 'password' => 'رمز عبور جدید']);

        $user = $request->user();
        $user->password = $request->input('password');
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Sign out every other device
        DB::table('sessions')->where('user_id', $user->id)->where('id', '!=', $request->session()->getId())->delete();

        return back()->with('success', 'رمز عبور با موفقیت تغییر کرد.');
    }
}
