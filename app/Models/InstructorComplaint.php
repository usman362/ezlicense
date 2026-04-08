<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstructorComplaint extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_INVESTIGATING = 'investigating';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_DISMISSED = 'dismissed';
    public const STATUS_ESCALATED = 'escalated';

    protected $fillable = [
        'instructor_profile_id',
        'reporter_user_id',
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'booking_id',
        'category',
        'severity',
        'subject',
        'description',
        'attachments',
        'status',
        'resolution_notes',
        'resolved_by',
        'resolved_at',
        'police_reported',
        'police_reference',
        'police_reported_at',
        'created_by',
    ];

    protected $casts = [
        'attachments'         => 'array',
        'police_reported'     => 'boolean',
        'police_reported_at'  => 'datetime',
        'resolved_at'         => 'datetime',
    ];

    public function instructorProfile(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function relatedWarnings(): HasMany
    {
        return $this->hasMany(InstructorWarning::class, 'related_complaint_id');
    }

    public function reporterLabel(): string
    {
        if ($this->reporter) {
            return $this->reporter->name . ' (' . $this->reporter->email . ')';
        }
        $bits = array_filter([$this->reporter_name, $this->reporter_email, $this->reporter_phone]);
        return $bits ? implode(' · ', $bits) : 'Anonymous';
    }

    public static function categories(): array
    {
        return [
            'harassment'            => 'Harassment',
            'safety'                => 'Safety',
            'misconduct'            => 'Misconduct',
            'no_show'               => 'No Show',
            'pricing_dispute'       => 'Pricing Dispute',
            'vehicle_condition'     => 'Vehicle Condition',
            'late'                  => 'Late / Tardy',
            'unprofessional'        => 'Unprofessional',
            'inappropriate_contact' => 'Inappropriate Contact',
            'other'                 => 'Other',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN          => 'Open',
            self::STATUS_INVESTIGATING => 'Investigating',
            self::STATUS_RESOLVED      => 'Resolved',
            self::STATUS_DISMISSED     => 'Dismissed',
            self::STATUS_ESCALATED     => 'Escalated',
        ];
    }

    public static function severities(): array
    {
        return [
            'low'      => 'Low',
            'medium'   => 'Medium',
            'high'     => 'High',
            'critical' => 'Critical',
        ];
    }
}
