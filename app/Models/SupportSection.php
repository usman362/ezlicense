<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SupportSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'icon', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active'  => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $s) {
            if (empty($s->slug)) $s->slug = Str::slug($s->name);
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SupportCategory::class, 'category_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(SupportArticle::class, 'section_id')
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    public function getRouteKeyName(): string { return 'slug'; }
}
