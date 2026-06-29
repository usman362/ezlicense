<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InstructorApplication extends Model
{
    use HasFactory;

    public const STATUS_PENDING       = 'pending';
    public const STATUS_UNDER_REVIEW  = 'under_review';
    public const STATUS_APPROVED      = 'approved';
    public const STATUS_REJECTED      = 'rejected';

    protected $fillable = [
        'reference', 'first_name', 'last_name', 'email', 'phone',
        'state', 'postcode',
        'years_experience', 'transmission', 'bio', 'suburb_id',
        'lesson_price', 'vehicle_make', 'vehicle_model', 'vehicle_year',
        'documents',
        'status', 'admin_notes', 'rejection_reason',
        'reviewed_by_user_id', 'reviewed_at', 'approved_invite_id',
        'applied_ip', 'applied_user_agent',
    ];

    protected function casts(): array
    {
        return [
            'documents'       => 'array',
            'reviewed_at'     => 'datetime',
            'years_experience'=> 'integer',
            'lesson_price'    => 'decimal:2',
            'vehicle_year'    => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $a) {
            if (empty($a->reference)) $a->reference = self::generateReference();
            $a->email = strtolower(trim($a->email));
        });
    }

    public static function generateReference(): string
    {
        do {
            $candidate = 'SLA-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (self::where('reference', $candidate)->exists());
        return $candidate;
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function suburb(): BelongsTo
    {
        return $this->belongsTo(Suburb::class);
    }

    public function invite(): BelongsTo
    {
        return $this->belongsTo(InstructorInvite::class, 'approved_invite_id');
    }

    public function fullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING      => 'warning',
            self::STATUS_UNDER_REVIEW => 'info',
            self::STATUS_APPROVED     => 'success',
            self::STATUS_REJECTED     => 'danger',
            default                   => 'secondary',
        };
    }
}
