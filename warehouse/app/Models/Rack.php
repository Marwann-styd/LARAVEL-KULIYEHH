<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rack extends Model
{
    protected $fillable = [
        'area_id',
        'rack_code',
        'level_count',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'level_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function rackLevels(): HasMany
    {
        return $this->hasMany(RackLevel::class);
    }
}
