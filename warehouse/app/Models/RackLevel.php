<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RackLevel extends Model
{
    protected $fillable = [
        'rack_id',
        'level_number',
        'capacity_pallets',
    ];

    protected function casts(): array
    {
        return [
            'level_number' => 'integer',
            'capacity_pallets' => 'integer',
        ];
    }

    public function rack(): BelongsTo
    {
        return $this->belongsTo(Rack::class);
    }

    public function storagePlacement(): HasOne
    {
        return $this->hasOne(StoragePlacement::class);
    }
}
