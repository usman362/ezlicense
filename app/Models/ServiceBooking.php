<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ServiceBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'user_id',
        'service_provider_id',
        'service_category_id',
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
}
