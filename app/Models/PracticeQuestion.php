<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeQuestion extends Model
{
    public const SECTION_GENERAL = 'general';
    public const SECTION_ROAD_SAFETY = 'road_safety';

    protected $fillable = [
        'section', 'question', 'image_path', 'options', 'correct_index',
        'explanation', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'options'       => 'array',
        'correct_index' => 'integer',
        'is_active'     => 'boolean',
        'sort_order'    => 'integer',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /** Resolve image_path to a full URL (handles both stored Spaces paths and external URLs). */
    public function getImageUrlAttribute(): ?string
    {
        $path = $this->image_path;
        if (! $path) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        try {
            return \Illuminate\Support\Facades\Storage::disk('spaces')->url($path);
        } catch (\Throwable $e) {
            return $path;
        }
    }

    public static function sectionLabel(string $section): string
    {
        return $section === self::SECTION_ROAD_SAFETY ? 'Road Safety' : 'General Knowledge';
    }
}
