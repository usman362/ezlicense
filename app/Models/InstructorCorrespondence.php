<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorCorrespondence extends Model
{
    use HasFactory;

    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_SMS = 'sms';
    public const CHANNEL_PHONE = 'phone_call';
    public const CHANNEL_IN_PERSON = 'in_person';
    public const CHANNEL_SYSTEM = 'system_message';
    public const CHANNEL_OTHER = 'other';

    public const DIRECTION_OUTBOUND = 'outbound';
    public const DIRECTION_INBOUND = 'inbound';

    protected $fillable = [
        'instructor_profile_id',
        'admin_id',
        'channel',
        'direction',
        'subject',
        'body',
        'attachments',
        'related_complaint_id',
        'related_warning_id',
        'related_block_id',
        'communicated_at',
    ];

    protected $casts = [
        'attachments'     => 'array',
        'communicated_at' => 'datetime',
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

    public function relatedWarning(): BelongsTo
    {
        return $this->belongsTo(InstructorWarning::class, 'related_warning_id');
    }

    public function relatedBlock(): BelongsTo
    {
        return $this->belongsTo(InstructorBlock::class, 'related_block_id');
    }

    public static function channels(): array
    {
        return [
            self::CHANNEL_EMAIL     => 'Email',
            self::CHANNEL_SMS       => 'SMS',
            self::CHANNEL_PHONE     => 'Phone Call',
            self::CHANNEL_IN_PERSON => 'In Person',
            self::CHANNEL_SYSTEM    => 'System Message',
            self::CHANNEL_OTHER     => 'Other',
        ];
    }
}
