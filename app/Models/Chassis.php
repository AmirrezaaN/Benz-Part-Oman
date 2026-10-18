<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Chassis extends Model
{
    use HasSlug;

    protected $table = 'chassis';

    protected $fillable = ['car_model_id', 'code', 'title', 'year_from', 'year_to', 'sort_order'];

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    protected function slugSourceAttribute(): string
    {
        return 'code';
    }

    protected function slugSourceValue(): string
    {
        return ($this->carModel?->name ?? '').' '.$this->code;
    }

    /** "W213 (2016-2023)" */
    public function label(): string
    {
        $years = $this->year_from ? ' ('.$this->year_from.'-'.($this->year_to ?: 'اکنون').')' : '';

        return $this->code.$years;
    }
}
