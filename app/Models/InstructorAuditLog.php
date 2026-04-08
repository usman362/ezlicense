<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorAuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'instructor_profile_id',
        'admin_id',
        'action',
        'summary',
        'old_values',
        'new_values',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata'   => 'array',
        'created_at' => 'datetime',
    ];

    public function instructorProfile(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Convenience factory for writing an audit entry.
     */
    public static function record(
        int $instructorProfileId,
        ?int $adminId,
        string $action,
        string $summary,
        ?array $old = null,
        ?array $new = null,
        ?array $metadata = null,
    ): self {
        return self::create([
            'instructor_profile_id' => $instructorProfileId,
            'admin_id'              => $adminId,
            'action'                => $action,
            'summary'               => $summary,
            'old_values'            => $old,
            'new_values'            => $new,
            'metadata'              => $metadata,
            'created_at'            => now(),
        ]);
    }
}
