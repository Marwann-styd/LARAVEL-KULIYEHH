<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'product_code',
        'product_name',
        'description',
        'cartons_per_pallet',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'cartons_per_pallet' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function storagePlacements(): HasMany
    {
        return $this->hasMany(StoragePlacement::class);
    }

    public function storageTransactions(): HasMany
    {
        return $this->hasMany(StorageTransaction::class);
    }
}
