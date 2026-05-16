<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorDocument extends Model
{
    public const TYPE_DRIVERS_LICENCE = 'drivers_licence';

    public const TYPE_INSTRUCTOR_LICENCE = 'instructor_licence';

    public const TYPE_WWCC = 'wwcc';

    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'instructor_profile_id',
        'type',
        'side',
        'file_path',
        'expires_at',
        'verified_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    public function instructorProfile(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class);
    }

    /**
     * Generate a signed temporary URL to view this document on Spaces.
     * Default expiry is 30 minutes — long enough for a review session but
     * short enough that leaked links go stale quickly.
     */
    public function getSignedUrl(int $minutes = 30): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        try {
            return \Storage::disk('spaces')->temporaryUrl(
                $this->file_path,
                now()->addMinutes($minutes)
            );
        } catch (\Throwable $e) {
            return null;
        }
    }
}
