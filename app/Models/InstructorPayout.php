<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class InstructorPayout extends Model
{
    use HasFactory;

    public const STATUS_PENDING    = 'pending';
    public const STATUS_APPROVED   = 'approved';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PAID       = 'paid';
    public const STATUS_FAILED     = 'failed';

    protected $fillable = [
        'instructor_profile_id',
        'reference',
        'period_start',
        'period_end',
        'bookings_count',
        'gross_amount',
        'service_fee_total',
        'processing_fee_total',
        'gst_on_fees',
        'net_amount',
        'adjustment_amount',
        'status',
        'admin_notes',
        'approved_at',
        'approved_by',
        'paid_at',
        'payment_reference',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'period_start'         => 'datetime',
            'period_end'           => 'datetime',
            'gross_amount'         => 'decimal:2',
            'service_fee_total'    => 'decimal:2',
            'processing_fee_total' => 'decimal:2',
            'gst_on_fees'          => 'decimal:2',
            'net_amount'           => 'decimal:2',
            'adjustment_amount'    => 'decimal:2',
            'approved_at'          => 'datetime',
            'paid_at'              => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────

    public function instructorProfile(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InstructorPayoutItem::class);
    }

    // ── Scopes ───────────────────────────────────

    public function scopePending($q)    { return $q->where('status', self::STATUS_PENDING); }
    public function scopeApproved($q)   { return $q->where('status', self::STATUS_APPROVED); }
    public function scopePaid($q)       { return $q->where('status', self::STATUS_PAID); }
    public function scopeFailed($q)     { return $q->where('status', self::STATUS_FAILED); }

    public function scopeForInstructor($q, int $profileId)
    {
        return $q->where('instructor_profile_id', $profileId);
    }

    // ── Helpers ──────────────────────────────────

    public function isPending(): bool    { return $this->status === self::STATUS_PENDING; }
    public function isApproved(): bool   { return $this->status === self::STATUS_APPROVED; }
    public function isPaid(): bool       { return $this->status === self::STATUS_PAID; }
    public function isFailed(): bool     { return $this->status === self::STATUS_FAILED; }

    public function canApprove(): bool
    {
        return $this->isPending();
    }

    public function canMarkPaid(): bool
    {
        return $this->isApproved() || $this->status === self::STATUS_PROCESSING;
    }

    public function totalDeductions(): float
    {
        return (float) $this->service_fee_total + (float) $this->processing_fee_total;
    }

    public function periodLabel(): string
    {
        return $this->period_start->format('d M') . ' – ' . $this->period_end->format('d M Y');
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING    => 'Pending',
            self::STATUS_APPROVED   => 'Approved',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_PAID       => 'Paid',
            self::STATUS_FAILED     => 'Failed',
        ];
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            self::STATUS_PENDING    => 'warning',
            self::STATUS_APPROVED   => 'info',
            self::STATUS_PROCESSING => 'primary',
            self::STATUS_PAID       => 'success',
            self::STATUS_FAILED     => 'danger',
            default                 => 'secondary',
        };
    }

    /**
     * Generate a unique reference like PAY-2026-W15-00042.
     */
    public static function generateReference(\DateTimeInterface $periodEnd): string
    {
        $year = $periodEnd->format('Y');
        $week = $periodEnd->format('W');
        $lastId = static::max('id') ?? 0;
        return sprintf('PAY-%s-W%s-%05d', $year, $week, $lastId + 1);
    }
}
