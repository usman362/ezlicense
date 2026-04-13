<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VehicleMake extends Model
{
    protected $fillable = [
        'name', 'slug', 'country', 'origin_type', 'logo_url',
        'is_popular', 'is_european', 'display_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_popular' => 'boolean',
            'is_european' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function models(): HasMany
    {
        return $this->hasMany(VehicleModel::class);
    }

    public function serviceProviders(): BelongsToMany
    {
        return $this->belongsToMany(ServiceProvider::class, 'service_provider_vehicle_makes');
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopePopular($q)
    {
        return $q->where('is_popular', true);
    }

    public function scopeEuropean($q)
    {
        return $q->where('is_european', true);
    }

    public function scopeNonEuropean($q)
    {
        return $q->where('is_european', false);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('display_order')->orderBy('name');
    }
}
