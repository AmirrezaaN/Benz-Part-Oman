<?php

namespace App\Models\Concerns;

/**
 * Generates a unique, unicode-friendly slug (Persian letters are kept) from the
 * attribute returned by slugSource().
 */
trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::saving(function ($model) {
            if (blank($model->slug) || $model->isDirty($model->slugSourceAttribute())) {
                $model->slug = static::uniqueSlug($model->slugSourceValue(), $model->getKey());
            }
        });
    }

    protected function slugSourceAttribute(): string
    {
        return 'name';
    }

    protected function slugSourceValue(): string
    {
        return (string) $this->{$this->slugSourceAttribute()};
    }

    public static function slugify(string $value): string
    {
        $slug = preg_replace('/[^\p{L}\p{N}]+/u', '-', mb_strtolower(trim($value)));
        $slug = trim((string) $slug, '-');

        return mb_substr($slug, 0, 90) ?: 'item';
    }

    public static function uniqueSlug(string $value, mixed $ignoreId = null): string
    {
        $base = static::slugify($value);
        $slug = $base;
        $i = 2;

        while (static::query()
            ->when(method_exists(static::class, 'bootSoftDeletes'), fn ($q) => $q->withTrashed())
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
