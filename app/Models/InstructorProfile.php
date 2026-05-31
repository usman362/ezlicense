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
        'public_slug',
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
        'lesson_durations',
        'offers_test_package',
        'service_test_existing',
        'service_test_new',
        'service_manual_no_vehicle',
        'is_active',
        'accepts_female_learners_only',
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
            'accepts_female_learners_only' => 'boolean',
            'languages' => 'array',
            'lesson_durations' => 'array',
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

    // ─────────────────────────────────────────────────────────────
    //  Female-only safety mode
    // ─────────────────────────────────────────────────────────────

    /**
     * True if this instructor only accepts female learners.
     * Only meaningful when the instructor herself is female-gendered.
     */
    public function isFemaleOnly(): bool
    {
        if (! $this->accepts_female_learners_only) {
            return false;
        }
        // Defensive: a non-female user shouldn't be able to enable this anyway,
        // but if data is stale, treat as not enforced.
        return strtolower((string) ($this->user?->gender ?? '')) === 'female';
    }

    /**
     * Determine whether the given user (or guest) can BOOK this instructor.
     * Returns ['ok' => bool, 'reason' => string].
     *
     * - Female learners (or admin): always allowed
     * - Male/other learners: blocked when the instructor is female-only
     * - Guests (null): allowed to PROCEED, but must pass gender at registration
     */
    public function canBeBookedBy(?\App\Models\User $user): array
    {
        if (! $this->isFemaleOnly()) {
            return ['ok' => true, 'reason' => ''];
        }

        // Admin can always proceed (e.g. for support actions)
        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return ['ok' => true, 'reason' => ''];
        }

        // Guests get a "soft pass" — final gate is at registration step
        if (! $user) {
            return ['ok' => true, 'reason' => 'guest_pending_gender_check'];
        }

        $gender = strtolower((string) ($user->gender ?? ''));
        if ($gender === 'female') {
            return ['ok' => true, 'reason' => ''];
        }

        return [
            'ok' => false,
            'reason' => 'This instructor only accepts female learners.',
        ];
    }

    /**
     * Determine whether the given user can SEE this instructor in search/listings.
     * Same logic as canBeBookedBy but stricter: guests still see (with warnings).
     */
    public function isVisibleTo(?\App\Models\User $user): bool
    {
        if (! $this->isFemaleOnly()) {
            return true;
        }
        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }
        if (! $user) {
            // Guest — gender unknown, show profile but warn before booking
            return true;
        }
        return strtolower((string) ($user->gender ?? '')) === 'female';
    }

    /* ─────────────── Public-shareable slug + URL ─────────────── */

    /**
     * Auto-generate a unique slug when the profile is created if none was set.
     * Slug is derived from the user's name and stays the same unless explicitly
     * changed via the instructor's settings.
     */
    protected static function booted(): void
    {
        static::creating(function (self $p) {
            if (empty($p->public_slug)) {
                $p->public_slug = self::generateUniqueSlug($p->user?->name ?? null, null);
            }
        });
    }

    /**
     * Generate a URL-safe, unique slug — falls back to "instructor-{n}" if name is empty.
     * Pass $excludeId to allow saving the same slug back to the same row (edit case).
     */
    public static function generateUniqueSlug(?string $name, ?int $excludeId = null): string
    {
        $base = \Illuminate\Support\Str::slug($name ?: '') ?: 'instructor';
        $slug = $base;
        $i = 1;
        while (self::where('public_slug', $slug)
                    ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                    ->exists()) {
            $slug = $base . '-' . ++$i;
        }
        return $slug;
    }

    /**
     * Validate that a user-supplied slug is allowed (format + uniqueness).
     * Returns the cleaned slug, or null if invalid.
     */
    public static function sanitizeSlug(string $raw, ?int $excludeId = null): ?string
    {
        $slug = strtolower(trim($raw));
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        if (strlen($slug) < 3 || strlen($slug) > 60) return null;

        // Reserve a few obvious paths
        $reserved = ['admin', 'api', 'login', 'register', 'dashboard', 'home', 'i', 'instructor', 'instructors', 'profile', 'logout'];
        if (in_array($slug, $reserved, true)) return null;

        if (self::where('public_slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()) {
            return null;
        }

        return $slug;
    }

    /**
     * Pretty public profile URL — e.g. https://securelicence.com/i/john-smith
     * Falls back to the numeric route if no slug is set yet.
     */
    public function publicUrl(): string
    {
        return $this->public_slug
            ? url('/i/' . $this->public_slug)
            : route('instructors.show', $this);
    }

    /**
     * Short URL specifically for sharing (alias of publicUrl for clarity).
     */
    public function shareUrl(): string
    {
        return $this->publicUrl();
    }
}
