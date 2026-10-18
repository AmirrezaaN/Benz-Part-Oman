<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['product_id', 'product_name', 'part_number', 'quantity', 'unit_price_omr', 'unit_price_toman'];

    protected function casts(): array
    {
        return ['unit_price_omr' => 'decimal:3', 'unit_price_toman' => 'integer'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
