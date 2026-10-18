<?php

namespace Tests\Feature;

use App\Contracts\SmsSender;
use App\Mail\VerificationCodeMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthTest extends TestCase
{
    private ?string $smsCode = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Capture SMS codes instead of sending them
        $this->app->instance(SmsSender::class, new class($this) implements SmsSender {
            public function __construct(private $test) {}

            public function sendOtp(string $phone, string $code): void
            {
                $this->test->captureSms($code);
            }
        });
    }

    public function captureSms(string $code): void
    {
        $this->smsCode = $code;
    }

    private function mailedCode(): string
    {
        $code = null;
        Mail::assertSent(VerificationCodeMail::class, function ($mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        return $code;
    }

    public function test_full_registration_with_email(): void
    {
        Mail::fake();

        $this->post(route('register.send'), ['identifier' => 'Ali@Example.com'])->assertRedirect(route('register.verify'));
        $code = $this->mailedCode();

        $this->get(route('register.complete'))->assertRedirect(route('register'));   // not verified yet
        $this->post(route('register.verify.check'), ['code' => $code])->assertRedirect(route('register.complete'));

        $this->post(route('register.store'), [
            'first_name' => 'علی', 'last_name' => 'احمدی', 'username' => 'Ali.Ahmadi',
            'password' => 'secret1234', 'password_confirmation' => 'secret1234', 'terms' => '1',
        ])->assertRedirect(route('account.dashboard'));

        $user = User::query()->where('email', 'ali@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('ali.ahmadi', $user->username);
        $this->assertSame(Role::CUSTOMER, $user->role->name);
        $this->assertNotNull($user->email_verified_at);
        $this->assertGreaterThanOrEqual(1_000_000_000, $user->internal_code);
    }

    public function test_registration_with_persian_digit_mobile_number(): void
    {
        $this->post(route('register.send'), ['identifier' => '۰۹۱۲ ۱۲۳ ۴۵۶۷'])->assertRedirect(route('register.verify'));
        $this->assertNotNull($this->smsCode);

        $this->post(route('register.verify.check'), ['code' => $this->smsCode]);
        $this->post(route('register.store'), [
            'first_name' => 'رضا', 'last_name' => 'کریمی', 'username' => 'reza99',
            'password' => 'secret1234', 'password_confirmation' => 'secret1234', 'terms' => '1',
        ]);

        $this->assertDatabaseHas('users', ['phone' => '09121234567', 'username' => 'reza99']);
    }

    public function test_existing_identifier_cannot_register_again(): void
    {
        $this->userWithRole(Role::CUSTOMER, ['email' => 'taken@example.com']);

        $this->post(route('register.send'), ['identifier' => 'taken@example.com'])->assertSessionHasErrors('identifier');
    }

    public function test_verification_code_locks_after_too_many_wrong_attempts(): void
    {
        Mail::fake();
        $this->post(route('register.send'), ['identifier' => 'lock@example.com']);
        $code = $this->mailedCode();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('register.verify.check'), ['code' => '000000'])->assertSessionHasErrors('code');
        }

        // Even the right code no longer works
        $this->post(route('register.verify.check'), ['code' => $code])->assertSessionHasErrors('code');
    }

    public function test_resend_cooldown_is_enforced(): void
    {
        Mail::fake();
        $this->post(route('register.send'), ['identifier' => 'cool@example.com']);
        $this->post(route('register.resend'))->assertSessionHasErrors('code');
        Mail::assertSentCount(1);
    }

    public function test_login_with_email_phone_or_username(): void
    {
        $user = $this->userWithRole(Role::CUSTOMER, ['email' => 'me@example.com', 'username' => 'myname']);
        $user->phone = '09350000000';
        $user->save();

        foreach (['ME@example.com', '+989350000000', 'MyName'] as $identifier) {
            $this->post(route('login.attempt'), ['identifier' => $identifier, 'password' => 'password123'])
                ->assertRedirect(route('account.dashboard'));
            $this->assertAuthenticatedAs($user);
            $this->post(route('logout'));
        }

        $this->post(route('login.attempt'), ['identifier' => 'me@example.com', 'password' => 'wrong-pass1'])
            ->assertSessionHasErrors('identifier');
        $this->assertGuest();
    }

    public function test_banned_user_cannot_login_and_is_kicked_out(): void
    {
        $user = $this->userWithRole(Role::CUSTOMER, ['email' => 'bad@example.com']);
        $user->banned_at = now();
        $user->save();

        $this->post(route('login.attempt'), ['identifier' => 'bad@example.com', 'password' => 'password123'])
            ->assertSessionHasErrors('identifier');
        $this->assertGuest();

        $this->actingAs($user)->get(route('account.dashboard'))->assertRedirect(route('login'));
    }

    public function test_password_reset_with_code(): void
    {
        Mail::fake();
        $user = $this->userWithRole(Role::CUSTOMER, ['email' => 'forgot@example.com']);

        $this->post(route('password.send'), ['identifier' => 'forgot@example.com'])->assertRedirect(route('password.reset'));
        $code = $this->mailedCode();

        $this->post(route('password.update'), ['code' => $code, 'password' => 'brandnew123', 'password_confirmation' => 'brandnew123'])
            ->assertRedirect(route('login'));

        $this->post(route('login.attempt'), ['identifier' => 'forgot@example.com', 'password' => 'brandnew123']);
        $this->assertAuthenticatedAs($user);
    }

    public function test_internal_code_is_immutable(): void
    {
        $user = $this->userWithRole(Role::CUSTOMER);

        $this->expectException(\LogicException::class);
        $user->internal_code = 1234567890;
        $user->save();
    }

    public function test_changing_email_requires_password_and_code(): void
    {
        Mail::fake();
        $user = $this->userWithRole(Role::CUSTOMER, ['email' => 'old@example.com']);

        $this->actingAs($user)->post(route('account.contact.send', 'email'), ['value' => 'new@example.com', 'current_password' => 'nope'])
            ->assertSessionHasErrors('current_password');

        $this->actingAs($user)->post(route('account.contact.send', 'email'), ['value' => 'new@example.com', 'current_password' => 'password123'])
            ->assertRedirect(route('account.contact.verify', 'email'));

        $this->actingAs($user)->post(route('account.contact.verify.check', 'email'), ['code' => $this->mailedCode()])
            ->assertRedirect(route('account.profile'));

        $this->assertSame('new@example.com', $user->fresh()->email);
    }
}
