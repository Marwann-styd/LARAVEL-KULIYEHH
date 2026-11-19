<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoragePlacement extends Model
{
    protected $fillable = [
        'rack_level_id',
        'product_id',
        'pallet_quantity',
    ];

    protected function casts(): array
    {
        return [
            'pallet_quantity' => 'integer',
        ];
    }

    public function rackLevel(): BelongsTo
    {
        return $this->belongsTo(RackLevel::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getCartonQuantityAttribute(): int
    {
        return $this->pallet_quantity
            * $this->product->cartons_per_pallet;
    }
}
