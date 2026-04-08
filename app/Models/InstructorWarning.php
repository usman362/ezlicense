<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorWarning extends Model
{
    use HasFactory;

    public const SEVERITY_LOW = 'low';
    public const SEVERITY_MEDIUM = 'medium';
    public const SEVERITY_HIGH = 'high';
    public const SEVERITY_CRITICAL = 'critical';

    protected $fillable = [
        'instructor_profile_id',
        'admin_id',
        'severity',
        'category',
        'subject',
        'description',
        'internal_notes',
        'related_complaint_id',
        'related_booking_id',
        'notified_instructor',
        'notified_at',
        'acknowledged_at',
    ];

    protected $casts = [
        'notified_instructor' => 'boolean',
        'notified_at'         => 'datetime',
        'acknowledged_at'     => 'datetime',
    ];

    public function instructorProfile(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function relatedComplaint(): BelongsTo
    {
        return $this->belongsTo(InstructorComplaint::class, 'related_complaint_id');
    }

    public static function severities(): array
    {
        return [
            self::SEVERITY_LOW      => 'Low',
            self::SEVERITY_MEDIUM   => 'Medium',
            self::SEVERITY_HIGH     => 'High',
            self::SEVERITY_CRITICAL => 'Critical',
        ];
    }

    public static function categories(): array
    {
        return [
            'conduct'        => 'Professional Conduct',
            'safety'         => 'Safety',
            'no_show'        => 'No Show',
            'pricing'        => 'Pricing',
            'communication'  => 'Communication',
            'vehicle'        => 'Vehicle Condition',
            'other'          => 'Other',
        ];
    }
}
