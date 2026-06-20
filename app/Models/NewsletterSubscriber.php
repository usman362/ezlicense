<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email', 'first_name', 'last_name', 'state', 'source',
        'is_active', 'unsubscribe_token', 'subscribed_at',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'subscribed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $sub) {
            if (empty($sub->unsubscribe_token)) {
                $sub->unsubscribe_token = Str::random(40);
            }
            if (empty($sub->subscribed_at)) {
                $sub->subscribed_at = now();
            }
            $sub->email = strtolower(trim($sub->email));
        });
    }
}
