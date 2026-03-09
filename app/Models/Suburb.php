<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Suburb extends Model
{
    use HasFactory;

    protected $fillable = ['state_id', 'name', 'postcode'];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(InstructorProfile::class, 'instructor_service_areas')
            ->withTimestamps();
    }
}
