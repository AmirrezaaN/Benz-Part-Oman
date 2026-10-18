<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    use HasSlug;

    protected $fillable = ['name', 'name_fa', 'logo', 'is_featured', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'is_active' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class)->orderBy('sort_order')->orderBy('name');
    }

    public function chassis(): HasManyThrough
    {
        return $this->hasManyThrough(Chassis::class, CarModel::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('name');
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? Storage::disk('public')->url($this->logo) : null;
    }

    public function displayName(): string
    {
        return $this->name_fa ?: $this->name;
    }
}
