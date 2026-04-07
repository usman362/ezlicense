<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function becomeProvider()
    {
        $categories = ServiceCategory::active()->orderBy('name')->get();
        return view('services.become-provider', compact('categories'));
    }

    public function categories()
    {
        $categories = ServiceCategory::active()
            ->withCount(['providers' => fn ($q) => $q->active()])
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
        return view('services.categories', compact('categories'));
    }

    public function browse(Request $request, string $slug)
    {
        $category = ServiceCategory::active()->where('slug', $slug)->firstOrFail();

        $query = ServiceProvider::with(['user', 'category'])
            ->active()
            ->inCategory($category->id);

        if ($request->filled('postcode')) {
            $query->where('base_postcode', $request->postcode);
        }
        if ($request->filled('suburb')) {
            $query->where('base_suburb', 'like', '%' . $request->suburb . '%');
        }
        if ($request->filled('sort')) {
            match ($request->sort) {
                'price_asc' => $query->orderBy('hourly_rate', 'asc'),
                'price_desc' => $query->orderBy('hourly_rate', 'desc'),
                'experience' => $query->orderByDesc('years_experience'),
                default => $query->latest(),
            };
        } else {
            $query->latest();
        }

        $providers = $query->paginate(12)->withQueryString();

        return view('services.browse', compact('category', 'providers'));
    }

    public function show(string $slug, ServiceProvider $provider)
    {
        abort_unless($provider->is_active && $provider->verification_status === 'approved', 404);
        $provider->load(['user', 'category', 'availabilitySlots', 'serviceAreas']);
        return view('services.provider', compact('provider'));
    }
}
