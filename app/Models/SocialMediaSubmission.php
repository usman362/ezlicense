<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMediaSubmission extends Model
{
    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_POSTED   = 'posted';
    public const STATUS_REJECTED = 'rejected';

    public const CATEGORIES = [
        'drive_test'       => 'Driving test passed',
        'lesson_milestone' => 'Lesson milestone',
        'other'            => 'Other',
    ];

    protected $fillable = [
        'instructor_id', 'instructor_profile_id',
        'learner_name', 'category', 'test_date', 'caption',
        'video_path', 'photo_paths',
        'status', 'admin_notes', 'reviewed_by_user_id', 'posted_at',
    ];

    protected function casts(): array
    {
        return [
            'photo_paths' => 'array',
            'test_date'   => 'date',
            'posted_at'   => 'datetime',
        ];
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function instructorProfile(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class, 'instructor_profile_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst(str_replace('_', ' ', (string) $this->category));
    }

    public function statusBadge(): string
    {
        $color = match ($this->status) {
            self::STATUS_PENDING  => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_POSTED   => 'success',
            self::STATUS_REJECTED => 'danger',
            default               => 'secondary',
        };
        $label = ucfirst((string) $this->status);

        return '<span class="badge text-bg-' . $color . '">' . e($label) . '</span>';
    }

    /**
     * A suggested caption the admin can copy-paste for the social post.
     */
    public function suggestedCaption(): string
    {
        $learner = $this->learner_name ?: 'Our learner';
        $instructor = $this->instructor?->name ?? 'their instructor';
        $date = $this->test_date?->format('j M Y');

        $line = $this->category === 'drive_test'
            ? "🎉 Congratulations {$learner} on passing your driving test with {$instructor}"
            : "🚗 {$learner} smashing it with {$instructor}";

        if ($date) {
            $line .= " on {$date}";
        }
        $line .= '! 👏';

        if ($this->caption) {
            $line .= "\n\n" . $this->caption;
        }

        $line .= "\n\n#SecureLicence #DrivingTestPassed #LearnToDrive";

        return $line;
    }
}
