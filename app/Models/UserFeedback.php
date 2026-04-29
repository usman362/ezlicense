<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFeedback extends Model
{
    protected $table = 'user_feedback';

    protected $fillable = [
        'user_id',
        'category',
        'rating',
        'message',
        'page_context',
        'user_agent',
        'status',
        'admin_response',
        'responded_by_user_id',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'responded_at' => 'datetime',
        ];
    }

    public const CATEGORIES = [
        'bug' => 'Bug / Something is broken',
        'suggestion' => 'Suggestion / Feature request',
        'compliment' => 'Compliment / Praise',
        'complaint' => 'Complaint',
        'other' => 'Other',
    ];

    public const STATUSES = ['new', 'reviewing', 'resolved', 'archived'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by_user_id');
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }
}
