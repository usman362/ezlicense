<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    // ── Booking types ──────────────────────────────────────────
    public const TYPE_LESSON = 'lesson';
    public const TYPE_TEST_PACKAGE = 'test_package';

    // ── Statuses ───────────────────────────────────────────────
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROPOSED = 'proposed';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_NO_SHOW = 'no_show';

    // ── Cancellation reason codes (matching live site presets) ─
    public const CANCEL_REASON_ILLNESS = 'illness_family_emergency';
    public const CANCEL_REASON_DOUBLE_BOOKED = 'double_booked';
    public const CANCEL_REASON_CAR_TROUBLE = 'car_trouble';
    public const CANCEL_REASON_WEATHER = 'weather_conditions';
    public const CANCEL_REASON_REQUESTED_BY_LEARNER = 'requested_by_learner';
    public const CANCEL_REASON_OTHER = 'other';

    /**
     * Human-readable labels for cancellation reason codes.
     */
    public static function cancellationReasonLabels(): array
    {
        return [
            self::CANCEL_REASON_ILLNESS => 'Illness/Family Emergency',
            self::CANCEL_REASON_DOUBLE_BOOKED => 'Double booked',
            self::CANCEL_REASON_CAR_TROUBLE => 'Car trouble',
            self::CANCEL_REASON_WEATHER => 'Weather conditions',
            self::CANCEL_REASON_REQUESTED_BY_LEARNER => 'Cancellation was requested by learner',
            self::CANCEL_REASON_OTHER => 'Other',
        ];
    }

    // ── Payment statuses ───────────────────────────────────────
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_REFUNDED = 'refunded';

    // ── Hours before booking that restrict instructor modifications ──
    public const MODIFICATION_CUTOFF_HOURS = 24;

    protected $fillable = [
        'learner_id',
        'instructor_id',
        'instructor_profile_id',
        'suburb_id',
        'type',
        'transmission',
        'scheduled_at',
        'duration_minutes',
        'amount',
        'platform_fee',
        'instructor_net_amount',
        'instructor_payout_id',
        'payment_method',
        'payment_status',
        'test_pre_booked',
        'status',
        'learner_notes',
        'cancellation_reason',
        'cancellation_reason_code',
        'cancellation_message',
        'cancelled_at',
        'cancelled_by_id',
        'cancellation_policy_accepted',
        'proposal_expires_at',
        'rescheduled_from_booking_id',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'proposal_expires_at' => 'datetime',
            'amount' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'instructor_net_amount' => 'decimal:2',
            'test_pre_booked' => 'boolean',
            'cancellation_policy_accepted' => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────────

    public function learner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'learner_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function instructorProfile(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class);
    }

    public function suburb(): BelongsTo
    {
        return $this->belongsTo(Suburb::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by_id');
    }

    public function rescheduledFromBooking(): BelongsTo
    {
        return $this->belongsTo(self::class, 'rescheduled_from_booking_id');
    }

    public function rescheduledToBooking(): HasOne
    {
        return $this->hasOne(self::class, 'rescheduled_from_booking_id');
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(InstructorPayout::class, 'instructor_payout_id');
    }

    public function payoutItem(): HasOne
    {
        return $this->hasOne(InstructorPayoutItem::class);
    }

    // ── Status helpers ─────────────────────────────────────────

    public function isCancellable(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_PROPOSED,
            self::STATUS_CONFIRMED,
        ], true);
    }

    public function isReschedulable(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
        ], true) && $this->scheduled_at->isFuture();
    }

    /**
     * Check if the booking starts within the modification cutoff window.
     * Instructors cannot modify bookings within 24 hours unless it's an emergency.
     */
    public function isWithinModificationCutoff(): bool
    {
        return $this->scheduled_at->diffInHours(now(), true) < self::MODIFICATION_CUTOFF_HOURS
            && $this->scheduled_at->isFuture();
    }

    /**
     * Check if a given user (by role) can modify this booking right now.
     * Learners: can always modify if booking is cancellable/reschedulable.
     * Instructors: blocked within 24 hours unless emergency reason provided.
     */
    public function canUserModify(User $user, ?string $reasonCode = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Must be a party to this booking
        if ($user->id !== $this->learner_id && $user->id !== $this->instructor_id) {
            return false;
        }

        // Instructor within 24-hour cutoff — only emergency reasons allowed
        if ($user->id === $this->instructor_id && $this->isWithinModificationCutoff()) {
            $emergencyReasons = [
                self::CANCEL_REASON_ILLNESS,
                self::CANCEL_REASON_CAR_TROUBLE,
            ];
            return $reasonCode !== null && in_array($reasonCode, $emergencyReasons, true);
        }

        return true;
    }

    // ── Cancellation rate helpers ──────────────────────────────

    /**
     * Get cancellation count for a user (instructor) within a given period.
     */
    public static function cancellationCountForUser(int $userId, int $days = 30): int
    {
        return static::where('instructor_id', $userId)
            ->where('cancelled_by_id', $userId)
            ->where('cancelled_at', '>=', now()->subDays($days))
            ->count();
    }

    /**
     * Get total completed + cancelled bookings for cancellation rate calculation.
     */
    public static function totalBookingsForUser(int $userId, int $days = 30): int
    {
        return static::where('instructor_id', $userId)
            ->whereIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED])
            ->where('scheduled_at', '>=', now()->subDays($days))
            ->count();
    }

    /**
     * Calculate cancellation rate as a percentage (0-100).
     */
    public static function cancellationRateForUser(int $userId, int $days = 30): float
    {
        $total = static::totalBookingsForUser($userId, $days);
        if ($total === 0) {
            return 0.0;
        }

        $cancelled = static::cancellationCountForUser($userId, $days);

        return round(($cancelled / $total) * 100, 1);
    }
}
