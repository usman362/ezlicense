<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorAvailabilitySlot extends Model
{
    protected $fillable = ['instructor_profile_id', 'day_of_week', 'start_time', 'end_time'];

    // start_time, end_time stored as time strings (e.g. 09:00:00)

    public function instructorProfile(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class);
    }
}
