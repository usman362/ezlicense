<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Faq extends Model
{
    protected $fillable = [
        'question', 'slug', 'category', 'answer', 'is_published', 'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order'   => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $faq) {
            if (empty($faq->slug)) {
                $base = Str::slug($faq->question) ?: 'faq';
                $slug = $base;
                $i = 1;
                while (self::where('slug', $slug)->where('id', '!=', $faq->id)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $faq->slug = $slug;
            }
        });
    }

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }
}
