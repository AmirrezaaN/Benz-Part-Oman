<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarModel extends Model
{
    use HasSlug;

    protected $fillable = ['brand_id', 'name', 'name_fa', 'sort_order'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function chassis(): HasMany
    {
        return $this->hasMany(Chassis::class)->orderBy('sort_order')->orderBy('code');
    }

    protected function slugSourceValue(): string
    {
        return ($this->brand?->name ?? '').' '.$this->name;
    }
}
