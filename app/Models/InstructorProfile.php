<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstructorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'profile_photo',
        'profile_description',
        'languages',
        'association_member',
        'instructing_start_month',
        'instructing_start_year',
        'transmission',
        'vehicle_make',
        'vehicle_model',
        'vehicle_year',
        'vehicle_safety_rating',
        'vehicle_photo',
        'wwcc_number',
        'wwcc_verified_at',
        'accreditation_details',
        'lesson_price',
        'test_package_price',
        'lesson_price_private',
        'test_package_price_private',
        'lesson_duration_minutes',
        'offers_test_package',
        'service_test_existing',
        'service_test_new',
        'service_manual_no_vehicle',
        'is_active',
        'notification_email_marketing',
        'notification_sms_marketing',
        'travel_buffer_same_mins',
        'travel_buffer_synced_mins',
        'min_prior_notice_hours',
        'max_advance_notice_days',
        'smart_scheduling_enabled',
        'smart_scheduling_buffer_hrs',
        'attach_ics_to_emails',
        'default_calendar_view',
        'business_name',
        'abn',
        'billing_address',
        'gst_registered',
        'billing_suburb',
        'billing_postcode',
        'billing_state',
        'payout_frequency',
        'bank_account_name',
        'bank_bsb',
        'bank_account_number',
        'bank_details_submitted_at',
        'verification_status',
        'admin_notes',
        'weighted_rating',
        'rating_points',
        'total_completed_lessons',
        'consecutive_five_stars',
        'recovery_deficit',
    ];

    protected function casts(): array
    {
        return [
            'wwcc_verified_at' => 'datetime',
            'lesson_price' => 'decimal:2',
            'test_package_price' => 'decimal:2',
            'lesson_price_private' => 'decimal:2',
            'test_package_price_private' => 'decimal:2',
            'offers_test_package' => 'boolean',
            'is_active' => 'boolean',
            'languages' => 'array',
            'association_member' => 'boolean',
            'service_test_existing' => 'boolean',
            'service_test_new' => 'boolean',
            'service_manual_no_vehicle' => 'boolean',
            'notification_email_marketing' => 'boolean',
            'notification_sms_marketing' => 'boolean',
            'smart_scheduling_enabled' => 'boolean',
            'attach_ics_to_emails' => 'boolean',
            'gst_registered' => 'boolean',
            'bank_details_submitted_at' => 'datetime',
            'weighted_rating' => 'decimal:2',
            'rating_points' => 'decimal:2',
            'total_completed_lessons' => 'integer',
            'consecutive_five_stars' => 'integer',
            'recovery_deficit' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceAreas(): BelongsToMany
    {
        return $this->belongsToMany(Suburb::class, 'instructor_service_areas')
            ->withTimestamps();
    }

    public function availabilitySlots(): HasMany
    {
        return $this->hasMany(InstructorAvailabilitySlot::class, 'instructor_profile_id');
    }

    public function availabilityBlocks(): HasMany
    {
        return $this->hasMany(InstructorAvailabilityBlock::class, 'instructor_profile_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(InstructorDocument::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'instructor_id', 'user_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'instructor_id', 'user_id');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(InstructorBlock::class)->orderByDesc('started_at');
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(InstructorWarning::class)->orderByDesc('created_at');
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(InstructorComplaint::class)->orderByDesc('created_at');
    }

    public function adminNotes(): HasMany
    {
        return $this->hasMany(InstructorAdminNote::class)
            ->orderByDesc('pinned')
            ->orderByDesc('created_at');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(InstructorAuditLog::class)->orderByDesc('created_at');
    }

    public function correspondences(): HasMany
    {
        return $this->hasMany(InstructorCorrespondence::class)->orderByDesc('communicated_at');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(InstructorPayout::class)->orderByDesc('period_start');
    }

    /**
     * The currently-active block, if any.
     */
    public function currentBlock(): ?InstructorBlock
    {
        return $this->blocks()
            ->whereNull('lifted_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    public function isBlocked(): bool
    {
        return $this->currentBlock() !== null;
    }

    /**
     * Average rating from approved, visible reviews only.
     * Returns weighted_rating if available, otherwise falls back to simple average.
     */
    public function averageRating(): float
    {
        if ($this->weighted_rating !== null && (float) $this->weighted_rating > 0) {
            return (float) $this->weighted_rating;
        }

        return (float) $this->reviews()->public()->avg('rating');
    }

    /**
     * Get the weighted rating value directly.
     */
    public function getWeightedRating(): float
    {
        return (float) ($this->weighted_rating ?? 4.00);
    }

    /**
     * Get a breakdown of all rating components for display / debugging.
     */
    public function getRatingBreakdown(): array
    {
        return [
            'weighted_rating' => (float) ($this->weighted_rating ?? 4.00),
            'rating_points' => (float) ($this->rating_points ?? 4.00),
            'total_completed_lessons' => (int) ($this->total_completed_lessons ?? 0),
            'consecutive_five_stars' => (int) ($this->consecutive_five_stars ?? 0),
            'recovery_deficit' => (int) ($this->recovery_deficit ?? 0),
            'simple_average' => (float) $this->reviews()->public()->avg('rating'),
            'approved_reviews_count' => $this->reviews()->public()->count(),
        ];
    }

    /**
     * Count of approved, visible reviews only.
     */
    public function reviewsCount(): int
    {
        return $this->reviews()->public()->count();
    }

    /**
     * All reviews including pending (for admin views).
     */
    public function allReviewsCount(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Pending reviews count (for admin badge).
     */
    public function pendingReviewsCount(): int
    {
        return $this->reviews()->pending()->count();
    }
}
