<?php

namespace App\Http\Controllers;

use App\Models\ServiceBooking;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceBookingController extends Controller
{
    public function create(ServiceProvider $provider)
    {
        abort_unless($provider->is_active && $provider->verification_status === 'approved', 404);
        $provider->load(['category', 'availabilitySlots']);
        return view('services.book', compact('provider'));
    }

    public function store(Request $request, ServiceProvider $provider)
    {
        abort_unless($provider->is_active && $provider->verification_status === 'approved', 404);

        $data = $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15',
            'address_line' => 'required|string|max:255',
            'suburb' => 'nullable|string|max:120',
            'postcode' => 'nullable|string|max:10',
            'state' => 'nullable|string|max:10',
            'job_description' => 'nullable|string',
        ]);

        $hours = $data['duration_minutes'] / 60;
        $hourlyRate = (float) $provider->hourly_rate;
        $callout = (float) $provider->callout_fee;
        $total = round($hourlyRate * $hours + $callout, 2);
        $commissionPct = (float) ($provider->category->commission_rate ?? 10);
        $platformFee = round($total * ($commissionPct / 100), 2);
        $payout = round($total - $platformFee, 2);

        $booking = ServiceBooking::create([
            'user_id' => Auth::id(),
            'service_provider_id' => $provider->id,
            'service_category_id' => $provider->service_category_id,
            'scheduled_at' => $data['scheduled_at'],
            'duration_minutes' => $data['duration_minutes'],
            'address_line' => $data['address_line'],
            'suburb' => $data['suburb'] ?? null,
            'postcode' => $data['postcode'] ?? null,
            'state' => $data['state'] ?? null,
            'job_description' => $data['job_description'] ?? null,
            'hourly_rate' => $hourlyRate,
            'callout_fee' => $callout,
            'total_amount' => $total,
            'platform_fee' => $platformFee,
            'provider_payout' => $payout,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        return redirect()->route('service-bookings.show', $booking)
            ->with('success', 'Booking created. Awaiting confirmation.');
    }

    public function show(ServiceBooking $serviceBooking)
    {
        abort_unless(
            $serviceBooking->user_id === Auth::id()
            || optional($serviceBooking->provider)->user_id === Auth::id(),
            403
        );
        $serviceBooking->load(['provider.user', 'provider.category', 'customer']);
        return view('services.booking-show', ['booking' => $serviceBooking]);
    }

    public function index()
    {
        $bookings = ServiceBooking::with(['provider.category', 'provider.user'])
            ->where('user_id', Auth::id())
            ->latest()->paginate(15);
        return view('services.my-bookings', compact('bookings'));
    }
}
