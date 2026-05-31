<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class SupportCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'sort_order', 'is_active',
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
        static::saving(function (self $cat) {
            if (empty($cat->slug)) $cat->slug = Str::slug($cat->name);
        });
    }

    public function sections(): HasMany
    {
        return $this->hasMany(SupportSection::class, 'category_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function articles(): HasManyThrough
    {
        return $this->hasManyThrough(SupportArticle::class, SupportSection::class, 'category_id', 'section_id');
    }

    public function getRouteKeyName(): string { return 'slug'; }
}
