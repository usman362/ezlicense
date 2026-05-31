<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SupportArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id', 'title', 'slug', 'excerpt', 'content', 'meta_description',
        'views_count', 'helpful_yes_count', 'helpful_no_count', 'sort_order',
        'is_published', 'author_id', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'views_count'       => 'integer',
            'helpful_yes_count' => 'integer',
            'helpful_no_count'  => 'integer',
            'sort_order'        => 'integer',
            'is_published'      => 'boolean',
            'published_at'      => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $a) {
            if (empty($a->slug)) {
                $a->slug = Str::slug($a->title);
            }
            if (empty($a->excerpt) && ! empty($a->content)) {
                $a->excerpt = Str::limit(strip_tags($a->content), 200);
            }
            if (empty($a->meta_description) && ! empty($a->excerpt)) {
                $a->meta_description = Str::limit($a->excerpt, 160);
            }
            if ($a->is_published && empty($a->published_at)) {
                $a->published_at = now();
            }
        });
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(SupportSection::class, 'section_id');
    }

    public function category()
    {
        return $this->section?->category;
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(SupportArticleFeedback::class, 'article_id');
    }

    public function helpfulPercent(): int
    {
        $total = $this->helpful_yes_count + $this->helpful_no_count;
        if ($total === 0) return 0;
        return (int) round(100 * $this->helpful_yes_count / $total);
    }

    public function getRouteKeyName(): string { return 'slug'; }
}
