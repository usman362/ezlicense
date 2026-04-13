<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServiceType extends Model
{
    protected $table = 'mechanic_service_types';

    protected $fillable = [
        'name', 'slug', 'icon', 'description', 'category',
        'display_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function serviceProviders(): BelongsToMany
    {
        return $this->belongsToMany(ServiceProvider::class, 'service_provider_mechanic_services')
            ->withPivot('price_from')
            ->withTimestamps();
    }

    // -- Scopes ------------------------------------------------

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('display_order')->orderBy('name');
    }

    public function scopeInCategory($q, string $category)
    {
        return $q->where('category', $category);
    }
}
