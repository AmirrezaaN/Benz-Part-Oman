<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Support\Identifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasSlug, SoftDeletes;

    protected $fillable = [
        'name', 'part_number', 'brand_id', 'category_id', 'manufacturer', 'country_of_origin',
        'year_from', 'year_to', 'condition', 'quantity', 'price_omr', 'price_toman',
        'warehouse', 'description', 'is_active', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'price_omr' => 'decimal:3',
            'quantity' => 'integer',
            'price_toman' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ---------------------------------------------------------------- Relations */

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function chassis(): BelongsToMany
    {
        return $this->belongsToMany(Chassis::class)->orderBy('code');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ---------------------------------------------------------------- Attributes */

    /** Part numbers are stored uppercase without spaces/dashes so "A 213 906-39 04" matches "A2139063904". */
    protected function partNumber(): Attribute
    {
        return Attribute::set(fn (?string $value) => filled($value) ? self::normalizePartNumber($value) : null);
    }

    public static function normalizePartNumber(string $value): string
    {
        return strtoupper(preg_replace('/[\s\-\.\/]/', '', Identifier::normalizeDigits($value)));
    }

    public function imageUrl(): ?string
    {
        $image = $this->relationLoaded('images') ? $this->images->first() : $this->images()->first();

        return $image ? Storage::disk('public')->url($image->path) : null;
    }

    /** Final Toman price: manual override, or computed from the OMR price × exchange rate. */
    public function tomanPrice(): ?int
    {
        return $this->price_toman ?: omr_to_toman($this->price_omr);
    }

    public function hasPrice(): bool
    {
        return $this->price_omr !== null || $this->price_toman !== null;
    }

    public function inStock(): bool
    {
        return $this->quantity !== null && $this->quantity > 0;
    }

    /** Can be added to the cart only when both price and stock are known. */
    public function isPurchasable(): bool
    {
        return $this->is_active && $this->inStock() && $this->price_omr !== null && $this->tomanPrice() !== null;
    }

    public function conditionLabel(): ?string
    {
        return $this->condition ? (config('shop.conditions')[$this->condition] ?? $this->condition) : null;
    }

    public function countryLabel(): ?string
    {
        return $this->country_of_origin
            ? (config('shop.countries')[$this->country_of_origin] ?? $this->country_of_origin)
            : null;
    }

    public function yearsLabel(): ?string
    {
        if (! $this->year_from && ! $this->year_to) {
            return null;
        }
        if ($this->year_from && $this->year_to && $this->year_from !== $this->year_to) {
            return $this->year_from.' - '.$this->year_to;
        }

        return (string) ($this->year_from ?: $this->year_to);
    }

    public function chassisCodes(): string
    {
        return $this->chassis->pluck('code')->unique()->implode('، ');
    }

    /* ---------------------------------------------------------------- Scopes */

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeWithCardData(Builder $query): void
    {
        $query->with(['brand:id,name,name_fa,slug', 'category:id,name,slug,icon', 'chassis:id,code', 'images']);
    }

    public function scopeSearch(Builder $query, ?string $term): void
    {
        $term = trim(Identifier::normalizeDigits((string) $term));
        if ($term === '') {
            return;
        }

        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';
        $partLike = '%'.self::normalizePartNumber($term).'%';

        $query->where(function (Builder $q) use ($like, $partLike) {
            $q->where('name', 'like', $like)
                ->orWhere('part_number', 'like', $partLike)
                ->orWhere('manufacturer', 'like', $like)
                ->orWhereHas('chassis', fn (Builder $c) => $c->where('code', 'like', $like))
                ->orWhereHas('brand', fn (Builder $b) => $b->where('name', 'like', $like)->orWhere('name_fa', 'like', $like));
        });
    }

    /**
     * Applies catalog filters coming from the query string.
     */
    public function scopeFilter(Builder $query, array $f): void
    {
        $query->search($f['q'] ?? null);

        if (! empty($f['category'])) {
            $category = Category::query()->where('slug', $f['category'])->first();
            $query->whereIn('category_id', $category ? $category->selfAndChildrenIds() : [0]);
        }

        if (! empty($f['brand'])) {
            $query->whereHas('brand', fn (Builder $b) => $b->where('slug', $f['brand']));
        }

        if (! empty($f['model'])) {
            $query->whereHas('chassis.carModel', fn (Builder $m) => $m->where('slug', $f['model']));
        }

        if (! empty($f['chassis'])) {
            $query->whereHas('chassis', fn (Builder $c) => $c->where('slug', $f['chassis']));
        }

        if (! empty($f['year'])) {
            $year = (int) $f['year'];
            $query->where(function (Builder $q) use ($year) {
                $q->where(fn ($q) => $q->whereNotNull('year_from')->where('year_from', '<=', $year)
                    ->where(fn ($q) => $q->whereNull('year_to')->orWhere('year_to', '>=', $year)))
                    ->orWhere(fn ($q) => $q->whereNull('year_from')->where('year_to', $year));
            });
        }

        if (! empty($f['country'])) {
            $query->where('country_of_origin', $f['country']);
        }

        if (! empty($f['condition'])) {
            $query->where('condition', $f['condition']);
        }

        if (! empty($f['in_stock'])) {
            $query->where('quantity', '>', 0);
        }

        match ($f['sort'] ?? 'newest') {
            'price_asc' => $query->orderByRaw('price_omr IS NULL')->orderBy('price_omr'),
            'price_desc' => $query->orderByRaw('price_omr IS NULL')->orderByDesc('price_omr'),
            'popular' => $query->orderByDesc('views'),
            default => $query->latest('id'),
        };
    }
}
