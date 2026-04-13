<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerVehicle extends Model
{
    protected $fillable = [
        'user_id', 'vehicle_make_id', 'vehicle_model_id', 'year',
        'colour', 'registration', 'vin', 'transmission', 'fuel_type',
        'photo', 'notes', 'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────────────────────

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function make(): BelongsTo
    {
        return $this->belongsTo(VehicleMake::class, 'vehicle_make_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class, 'customer_vehicle_id');
    }

    // ── Helpers ───────────────────────────────────────────────

    /**
     * Get a human-readable label for this vehicle.
     * e.g. "2019 Toyota Corolla (ABC 123)"
     */
    public function getLabel(): string
    {
        $parts = [];
        if ($this->year) {
            $parts[] = $this->year;
        }
        if ($this->make) {
            $parts[] = $this->make->name;
        }
        if ($this->model) {
            $parts[] = $this->model->name;
        }
        $label = implode(' ', $parts) ?: 'Vehicle #' . $this->id;

        if ($this->registration) {
            $label .= " ({$this->registration})";
        }

        return $label;
    }

    /**
     * Get photo URL or a placeholder.
     */
    public function getPhotoUrl(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}
