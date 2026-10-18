<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use LogicException;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * role_id, internal_code, banned_at and verification timestamps are deliberately NOT fillable.
     */
    protected $fillable = [
        'first_name', 'last_name', 'username', 'password',
        'province', 'city', 'address', 'postal_code',
    ];

    protected $hidden = ['password', 'remember_token', 'internal_code'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'banned_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            $user->internal_code ??= self::generateInternalCode();
        });

        // The internal code is permanent.
        static::updating(function (User $user) {
            if ($user->isDirty('internal_code')) {
                throw new LogicException('internal_code is immutable.');
            }
        });
    }

    public static function generateInternalCode(): int
    {
        do {
            $code = random_int(1_000_000_000, 9_999_999_999);
        } while (self::query()->where('internal_code', $code)->exists());

        return $code;
    }

    /** Admin routes resolve users by their internal code rather than the sequential id. */
    public function getRouteKeyName(): string
    {
        return 'internal_code';
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn () => trim($this->first_name.' '.$this->last_name) ?: $this->username);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->isSuperAdmin() ?? false;
    }

    public function hasPermission(string $permission): bool
    {
        return $this->role?->hasPermission($permission) ?? false;
    }

    public function canAccessAdmin(): bool
    {
        return ! $this->isBanned() && $this->hasPermission('admin.access');
    }

    public function isBanned(): bool
    {
        return $this->banned_at !== null;
    }

    /** Permission keys this user effectively holds. */
    public function permissionKeys(): array
    {
        return $this->isSuperAdmin()
            ? array_keys(Role::allPermissions())
            : ($this->role?->permissions ?? []);
    }

    /** A user may only hand out roles whose permissions are a subset of their own. */
    public function canAssignRole(Role $role): bool
    {
        if ($role->isSuperAdmin()) {
            return false;
        }

        return $this->isSuperAdmin()
            || empty(array_diff($role->permissions ?? [], $this->permissionKeys()));
    }
}
