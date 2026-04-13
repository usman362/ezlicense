<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_category_id',
        'business_name',
        'abn',
        'bio',
        'profile_photo',
        'languages',
        'years_experience',
        'hourly_rate',
        'callout_fee',
        'default_duration_minutes',
        'service_radius_km',
        'base_suburb',
        'base_postcode',
        'base_state',
        'service_description',
        'license_number',
        'license_verified_at',
        'is_active',
        'verification_status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'hourly_rate' => 'decimal:2',
            'callout_fee' => 'decimal:2',
            'license_verified_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function serviceAreas(): BelongsToMany
    {
        return $this->belongsToMany(Suburb::class, 'service_provider_areas');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ServiceProviderDocument::class);
    }

    public function availabilitySlots(): HasMany
    {
        return $this->hasMany(ServiceProviderAvailabilitySlot::class);
    }

    public function availabilityBlocks(): HasMany
    {
        return $this->hasMany(ServiceProviderAvailabilityBlock::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class);
    }

    // ── Service-specific relationships ───────────────────────

    /**
     * Vehicle makes this provider specializes in.
     */
    public function vehicleMakes(): BelongsToMany
    {
        return $this->belongsToMany(VehicleMake::class, 'service_provider_vehicle_makes')
            ->withTimestamps();
    }

    /**
     * Service types this provider offers (brakes, servicing, etc.).
     */
    public function serviceTypes(): BelongsToMany
    {
        return $this->belongsToMany(ServiceType::class, 'service_provider_mechanic_services')
            ->withPivot('price_from')
            ->withTimestamps();
    }

    /**
     * Check if this provider works on a given vehicle make.
     */
    public function worksOnMake(int $makeId): bool
    {
        return $this->vehicleMakes()->where('vehicle_make_id', $makeId)->exists();
    }

    /**
     * Check if this provider offers a given service type.
     */
    public function offersServiceType(int $typeId): bool
    {
        return $this->serviceTypes()->where('service_type_id', $typeId)->exists();
    }

    /**
     * Check if this provider is a mechanic (belongs to mechanic category).
     */
    public function isMechanic(): bool
    {
        return $this->category
            && str_contains(strtolower($this->category->slug), 'mechanic');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->where('verification_status', 'approved');
    }

    public function scopeInCategory($q, $categoryId)
    {
        return $q->where('service_category_id', $categoryId);
    }
}
