<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorPayoutItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'instructor_payout_id',
        'booking_id',
        'gross_amount',
        'service_fee',
        'processing_fee',
        'gst_on_fees',
        'net_amount',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount'   => 'decimal:2',
            'service_fee'    => 'decimal:2',
            'processing_fee' => 'decimal:2',
            'gst_on_fees'    => 'decimal:2',
            'net_amount'     => 'decimal:2',
            'created_at'     => 'datetime',
        ];
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(InstructorPayout::class, 'instructor_payout_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
