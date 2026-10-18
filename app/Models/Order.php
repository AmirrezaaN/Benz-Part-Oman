<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'full_name', 'phone', 'province', 'city', 'address', 'postal_code', 'customer_note',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_omr' => 'decimal:3',
            'total_toman' => 'integer',
            'stock_deducted' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            do {
                $code = 'BOP-'.random_int(100000, 999999);
            } while (self::query()->where('code', $code)->exists());
            $order->code = $code;
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

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLabel(): string
    {
        return config("shop.order_statuses.{$this->status}.label", $this->status);
    }

    public function statusColor(): string
    {
        return config("shop.order_statuses.{$this->status}.color", 'slate');
    }
}
