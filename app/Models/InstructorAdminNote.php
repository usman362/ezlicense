<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorAdminNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_profile_id',
        'admin_id',
        'note',
        'pinned',
    ];

    protected $casts = [
        'pinned' => 'boolean',
    ];

    public function instructorProfile(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
