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
        'languages',
        'association_member',
        'instructing_start_month',
        'instructing_start_year',
        'transmission',
        'vehicle_make',
        'vehicle_model',
        'vehicle_year',
        'vehicle_safety_rating',
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

    public function averageRating(): float
    {
        return (float) $this->reviews()->avg('rating');
    }

    public function reviewsCount(): int
    {
        return $this->reviews()->count();
    }
}
