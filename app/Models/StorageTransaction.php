<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorageTransaction extends Model
{
    protected $fillable = [
        'transaction_code',
        'transaction_type',
        'product_id',
        'from_rack_level_id',
        'to_rack_level_id',
        'pallet_quantity',
        'user_id',
        'transaction_date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'pallet_quantity' => 'integer',
            'transaction_date' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function fromRackLevel(): BelongsTo
    {
        return $this->belongsTo(
            RackLevel::class,
            'from_rack_level_id'
        );
    }

    public function toRackLevel(): BelongsTo
    {
        return $this->belongsTo(
            RackLevel::class,
            'to_rack_level_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCartonQuantityAttribute(): int
    {
        return $this->pallet_quantity
            * $this->product->cartons_per_pallet;
    }
}
