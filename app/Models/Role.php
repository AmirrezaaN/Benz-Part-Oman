<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const SUPER_ADMIN = 'super-admin';
    public const ADMIN = 'admin';
    public const CUSTOMER = 'customer';

    protected $fillable = ['name', 'label', 'description', 'permissions'];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_system' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** Flat list of every permission key => label defined in config/permissions.php */
    public static function allPermissions(): array
    {
        return collect(config('permissions.groups'))->collapse()->all();
    }

    public function isSuperAdmin(): bool
    {
        return $this->name === self::SUPER_ADMIN;
    }

    public function hasPermission(string $permission): bool
    {
        return $this->isSuperAdmin() || in_array($permission, $this->permissions ?? [], true);
    }

    public static function customer(): self
    {
        return self::query()->where('name', self::CUSTOMER)->firstOrFail();
    }
}
