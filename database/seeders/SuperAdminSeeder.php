<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Creates (or updates) the owner account from SUPER_ADMIN_EMAIL / SUPER_ADMIN_PASSWORD in .env.
 * Run again any time with:  php artisan db:seed --class=SuperAdminSeeder
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = mb_strtolower((string) config('shop.super_admin.email'));
        $password = (string) config('shop.super_admin.password');

        if ($email === '' || $password === '') {
            $this->command?->warn('SUPER_ADMIN_EMAIL / SUPER_ADMIN_PASSWORD are not set in .env — owner account skipped.');

            return;
        }

        $role = Role::query()->where('name', Role::SUPER_ADMIN)->firstOrFail();

        $user = User::query()->where('email', $email)->first() ?? new User;
        $user->fill([
            'first_name' => $user->first_name ?? 'مدیر',
            'last_name' => $user->last_name ?? 'سایت',
            'username' => $user->username ?? config('shop.super_admin.username', 'owner'),
            'password' => $password,
        ]);
        $user->email = $email;
        $user->email_verified_at ??= now();
        $user->role_id = $role->id;
        $user->banned_at = null;
        $user->save();

        $this->command?->info("Super admin ready: {$email}");
    }
}
