<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'username' => 'u'.Str::lower(Str::random(10)),
            'password' => 'password123',
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function ($user) {
            $user->email ??= fake()->unique()->safeEmail();
            $user->email_verified_at ??= now();
            $user->role_id ??= Role::query()->where('name', Role::CUSTOMER)->value('id');
        });
    }
}
