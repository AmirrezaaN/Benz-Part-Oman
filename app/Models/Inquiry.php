<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Inquiry extends Model
{
    protected $fillable = [
        'product_id', 'brand_id', 'vin', 'part_number', 'part_name', 'car_model', 'year',
        'quantity', 'image', 'message',
    ];

    protected function casts(): array
    {
        return [
            'replied_at' => 'datetime',
            'quoted_price_omr' => 'decimal:3',
            'quoted_price_toman' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Inquiry $inquiry) {
            do {
                $code = 'INQ-'.strtoupper(Str::random(7));
            } while (self::query()->where('code', $code)->exists());
            $inquiry->code = $code;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function statusLabel(): string
    {
        return config("shop.inquiry_statuses.{$this->status}.label", $this->status);
    }

    public function statusColor(): string
    {
        return config("shop.inquiry_statuses.{$this->status}.color", 'slate');
    }

    public function imageUrl(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }
}
