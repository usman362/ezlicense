<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\PasswordResetNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_LEARNER = 'learner';
    public const ROLE_INSTRUCTOR = 'instructor';
    public const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'preferred_first_name',
        'gender',
        'email',
        'phone',
        'postcode',
        'role',
        'is_active',
        'last_login_at',
        'password',
        'deactivation_reason',
        'deactivated_at',
        'blocked_until',
        'referral_code',
        'referred_by_user_id',
        'referred_at',
        'google_calendar_token',
        'google_calendar_id',
        'google_calendar_sync_enabled',
        'google_calendar_last_synced_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_calendar_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
            'deactivated_at' => 'datetime',
            'blocked_until' => 'datetime',
            'google_calendar_token' => 'encrypted:array',
            'google_calendar_sync_enabled' => 'boolean',
            'google_calendar_last_synced_at' => 'datetime',
        ];
    }

    /**
     * Route notifications for the Vonage (SMS) channel.
     * Returns the phone number in E.164 format for Australian numbers.
     */
    public function routeNotificationForVonage($notification): ?string
    {
        $phone = $this->phone;
        if (empty($phone)) {
            return null;
        }

        // Clean non-digit chars
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Convert Australian 04xx to +614xx
        if (str_starts_with($phone, '04')) {
            $phone = '+61' . substr($phone, 1);
        } elseif (str_starts_with($phone, '4') && strlen($phone) === 9) {
            $phone = '+61' . $phone;
        }

        // Ensure + prefix
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        return $phone;
    }

    public function instructorProfile(): HasOne
    {
        return $this->hasOne(InstructorProfile::class);
    }

    public function learnerBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'learner_id');
    }

    public function instructorBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'instructor_id');
    }

    public function learnerWallet(): HasOne
    {
        return $this->hasOne(LearnerWallet::class, 'user_id');
    }

    public function learnerTransactions(): HasMany
    {
        return $this->hasMany(LearnerTransaction::class, 'user_id');
    }

    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(Review::class, 'learner_id');
    }

    public function adminNotes(): HasMany
    {
        return $this->hasMany(UserAdminNote::class)
            ->orderByDesc('pinned')
            ->orderByDesc('created_at');
    }

    public function complaintsFiled(): HasMany
    {
        return $this->hasMany(InstructorComplaint::class, 'reporter_user_id')
            ->orderByDesc('created_at');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(CustomerVehicle::class);
    }

    public function primaryVehicle(): HasOne
    {
        return $this->hasOne(CustomerVehicle::class)->where('is_primary', true);
    }

    public function serviceBookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class);
    }

    public function isLearner(): bool
    {
        return $this->role === self::ROLE_LEARNER;
    }

    public function isInstructor(): bool
    {
        return $this->role === self::ROLE_INSTRUCTOR;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Send a branded password reset notification (overrides Laravel's default).
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new PasswordResetNotification($token));
    }

    /**
     * Auto-generate a unique 8-char referral code on creation.
     */
    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (empty($user->referral_code)) {
                $user->referral_code = self::generateUniqueReferralCode();
            }
        });
    }

    public static function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(\Illuminate\Support\Str::random(8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Relationships for the Invite Friends / referral system.
     */
    public function referrer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by_user_id');
    }

    public function referredUsers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'referred_by_user_id');
    }

    public function sentInvites(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\ReferralInvite::class, 'referrer_user_id');
    }

    /**
     * Check if the user has a connected and enabled Google Calendar sync.
     */
    public function isGoogleCalendarConnected(): bool
    {
        return !empty($this->google_calendar_token)
            && $this->google_calendar_sync_enabled;
    }
}
