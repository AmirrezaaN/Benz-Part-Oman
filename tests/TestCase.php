<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    protected function userWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->make($attributes);
        $user->role_id = Role::query()->where('name', $role)->value('id');
        $user->save();

        return $user->fresh('role');
    }

    protected function roleWith(array $permissions, string $name = 'custom'): Role
    {
        $role = new Role(['label' => $name, 'permissions' => $permissions]);
        $role->name = $name;
        $role->save();

        return $role;
    }
}
