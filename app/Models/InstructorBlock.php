<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorBlock extends Model
{
    use HasFactory;

    public const DURATION_30_DAYS = '30_days';
    public const DURATION_60_DAYS = '60_days';
    public const DURATION_90_DAYS = '90_days';
    public const DURATION_CUSTOM = 'custom';
    public const DURATION_PERMANENT = 'permanent';

    protected $fillable = [
        'instructor_profile_id',
        'admin_id',
        'duration_type',
        'started_at',
        'expires_at',
        'reason',
        'internal_notes',
        'lifted_at',
        'lifted_by',
        'lifted_reason',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'lifted_at'  => 'datetime',
    ];

    public function instructorProfile(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function lifter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lifted_by');
    }

    public function isActive(): bool
    {
        if ($this->lifted_at) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        return true;
    }

    public function isPermanent(): bool
    {
        return $this->duration_type === self::DURATION_PERMANENT;
    }

    public static function durationLabels(): array
    {
        return [
            self::DURATION_30_DAYS  => '30 days',
            self::DURATION_60_DAYS  => '60 days',
            self::DURATION_90_DAYS  => '90 days',
            self::DURATION_CUSTOM   => 'Custom',
            self::DURATION_PERMANENT => 'Permanent',
        ];
    }
}
