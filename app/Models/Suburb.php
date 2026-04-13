<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Suburb extends Model
{
    use HasFactory;

    protected $fillable = ['state_id', 'name', 'postcode', 'latitude', 'longitude'];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(InstructorProfile::class, 'instructor_service_areas')
            ->withTimestamps();
    }

    public function serviceProviders(): BelongsToMany
    {
        return $this->belongsToMany(ServiceProvider::class, 'service_provider_areas')
            ->withTimestamps();
    }

    /**
     * Calculate distance (in km) to a given lat/lng using Haversine formula.
     */
    public function distanceTo(float $lat, float $lng): ?float
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        $earthRadius = 6371; // km
        $dLat = deg2rad($lat - $this->latitude);
        $dLng = deg2rad($lng - $this->longitude);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($this->latitude)) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Scope: suburbs within a given radius (km) of a lat/lng point.
     * Uses a bounding box for fast filtering, then Haversine for precision.
     */
    public function scopeWithinRadius($query, float $lat, float $lng, float $radiusKm)
    {
        // Rough bounding box (1 degree ≈ 111km)
        $latDelta = $radiusKm / 111.0;
        $lngDelta = $radiusKm / (111.0 * cos(deg2rad($lat)));

        return $query
            ->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta]);
    }
}
