<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    protected $fillable = [
        'to_address',
        'to_name',
        'subject',
        'mailable_class',
        'notification_class',
        'user_id',
        'status',
        'error_message',
        'headers',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'headers' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }
}
