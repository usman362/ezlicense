<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ServiceBooking extends Model
{
    use HasFactory;

    // ── Proposal statuses ────────────────���───────────────────
    public const PROPOSAL_PENDING = 'pending';
    public const PROPOSAL_ACCEPTED = 'accepted';
    public const PROPOSAL_REJECTED = 'rejected';
    public const PROPOSAL_EXPIRED = 'expired';

    protected $fillable = [
        'reference',
        'user_id',
        'service_provider_id',
        'service_category_id',
        'customer_vehicle_id',
        'vehicle_make',
        'vehicle_model',
        'vehicle_year',
        'vehicle_registration',
        'vehicle_photos',
        'mechanic_service_type_id',
        'scheduled_at',
        'duration_minutes',
        'address_line',
        'suburb',
        'postcode',
        'state',
        'job_description',
        'hourly_rate',
        'callout_fee',
        'total_amount',
        'platform_fee',
        'provider_payout',
        'status',
        'payment_status',
        'payment_intent_id',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'proposal_status',
        'proposal_message',
        'quoted_amount',
        'proposal_responded_at',
        'proposal_expires_at',
        'completion_photos',
        'completion_notes',
        'customer_confirmed_at',
        'customer_confirmed_ip',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'hourly_rate' => 'decimal:2',
            'callout_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'provider_payout' => 'decimal:2',
            'quoted_amount' => 'decimal:2',
            'vehicle_photos' => 'array',
            'completion_photos' => 'array',
            'proposal_responded_at' => 'datetime',
            'proposal_expires_at' => 'datetime',
            'customer_confirmed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ServiceBooking $b) {
            if (empty($b->reference)) {
                $b->reference = 'SB-' . strtoupper(Str::random(8));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function customerVehicle(): BelongsTo
    {
        return $this->belongsTo(CustomerVehicle::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class, 'mechanic_service_type_id');
    }

    // ── Proposal helpers ──────────────────────────────────────

    public function isProposalPending(): bool
    {
        return $this->proposal_status === self::PROPOSAL_PENDING;
    }

    public function isProposalAccepted(): bool
    {
        return $this->proposal_status === self::PROPOSAL_ACCEPTED;
    }

    public function isProposalRejected(): bool
    {
        return $this->proposal_status === self::PROPOSAL_REJECTED;
    }

    /**
     * Get a readable vehicle description from the booking.
     */
    public function vehicleDescription(): string
    {
        $parts = array_filter([
            $this->vehicle_year,
            $this->vehicle_make,
            $this->vehicle_model,
        ]);

        $desc = implode(' ', $parts) ?: 'Vehicle not specified';

        if ($this->vehicle_registration) {
            $desc .= " ({$this->vehicle_registration})";
        }

        return $desc;
    }
}
