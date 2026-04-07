<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider as ProviderModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $provider = $this->currentProvider();

        if (!$provider) {
            return redirect()->route('service-provider.onboarding.create');
        }

        $upcoming = $provider->bookings()
            ->where('scheduled_at', '>=', now())
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();

        $stats = [
            'total_bookings' => $provider->bookings()->count(),
            'completed' => $provider->bookings()->where('status', 'completed')->count(),
            'earnings' => $provider->bookings()->where('payment_status', 'paid')->sum('provider_payout'),
            'pending_count' => $provider->bookings()->where('status', 'pending')->count(),
        ];

        return view('service-provider.dashboard', compact('provider', 'upcoming', 'stats'));
    }

    public function onboardingCreate()
    {
        if ($this->currentProvider()) {
            return redirect()->route('service-provider.dashboard');
        }
        $categories = ServiceCategory::active()->orderBy('name')->get();
        return view('service-provider.onboarding', compact('categories'));
    }

    public function onboardingStore(Request $request)
    {
        $data = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'business_name' => 'nullable|string|max:255',
            'abn' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'years_experience' => 'nullable|integer|min:0|max:80',
            'hourly_rate' => 'required|numeric|min:0',
            'callout_fee' => 'nullable|numeric|min:0',
            'default_duration_minutes' => 'required|integer|min:15',
            'service_radius_km' => 'required|integer|min:1',
            'base_suburb' => 'nullable|string|max:120',
            'base_postcode' => 'nullable|string|max:10',
            'base_state' => 'nullable|string|max:10',
            'service_description' => 'nullable|string',
            'license_number' => 'nullable|string|max:120',
        ]);

        $data['user_id'] = Auth::id();
        $data['verification_status'] = 'pending';
        $data['is_active'] = false;

        ProviderModel::create($data);

        return redirect()->route('service-provider.dashboard')
            ->with('success', 'Profile submitted. Awaiting admin approval.');
    }

    protected function currentProvider(): ?ProviderModel
    {
        return ProviderModel::where('user_id', Auth::id())->first();
    }
}
