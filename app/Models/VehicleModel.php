<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleModel extends Model
{
    protected $fillable = [
        'vehicle_make_id', 'name', 'year_from', 'year_to', 'body_type', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function make(): BelongsTo
    {
        return $this->belongsTo(VehicleMake::class, 'vehicle_make_id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
