<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeQuestion extends Model
{
    public const SECTION_GENERAL = 'general';
    public const SECTION_ROAD_SAFETY = 'road_safety';

    /** Australian states/territories a question can belong to. Keyed by slug. */
    public const STATES = [
        'nsw' => 'New South Wales',
        'vic' => 'Victoria',
        'qld' => 'Queensland',
        'wa'  => 'Western Australia',
        'sa'  => 'South Australia',
        'tas' => 'Tasmania',
        'act' => 'Australian Capital Territory',
    ];

    protected $fillable = [
        'section', 'state', 'question', 'image_path', 'options', 'correct_index',
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

    /** Human label for a state slug. NULL/blank = shown in every state's test. */
    public static function stateLabel(?string $state): string
    {
        if (! $state) {
            return 'All states';
        }
        return self::STATES[strtolower($state)] ?? strtoupper($state);
    }

    /**
     * Limit to questions used by a given state's test: the state's own questions
     * plus any marked "All states" (NULL state).
     */
    public function scopeForState($q, string $state)
    {
        $state = strtolower($state);
        return $q->where(function ($w) use ($state) {
            $w->whereNull('state')->orWhere('state', $state);
        });
    }
}
