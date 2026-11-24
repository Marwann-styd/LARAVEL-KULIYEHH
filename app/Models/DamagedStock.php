<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamagedStock extends Model
{
    protected $fillable = [
        'warehouse_id',
        'rack_level_id',
        'product_id',
        'reported_by',
        'damaged_cartons',
        'damage_category',
        'photo_path',
        'notes',
        'reported_at',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
    ];

    public function getNetCartonsAttribute(): int
    {
        $gross = $this->pallet_quantity * ($this->product->cartons_per_pallet ?? 0);
        return max(0, $gross - ($this->damaged_cartons ?? 0));
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function rackLevel()
    {
        return $this->belongsTo(RackLevel::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
