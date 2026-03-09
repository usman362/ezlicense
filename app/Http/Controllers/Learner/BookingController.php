<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Show the Make a Booking page for a given instructor.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $instructorProfileId = $request->input('instructor_profile_id');
        if (! $instructorProfileId) {
            return redirect()->route('learner.dashboard')->with('message', 'Please select an instructor to book.');
        }

        $instructorProfile = InstructorProfile::with(['user:id,name,phone', 'serviceAreas.state'])
            ->where('id', $instructorProfileId)
            ->where('is_active', true)
            ->firstOrFail();

        $states = State::orderBy('name')->get(['id', 'name', 'code']);
        $suburbsByState = State::with(['suburbs' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($state) => [$state->id => $state->suburbs->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'postcode' => $s->postcode])->values()->toArray()])
            ->toArray();

        return view('learner.pages.booking-new', [
            'instructorProfile' => $instructorProfile,
            'states' => $states,
            'suburbsByState' => $suburbsByState,
            'googleMapsApiKey' => config('services.google.maps_api_key'),
        ]);
    }

    /**
     * Store order in session and redirect to payment step.
     */
    public function continueToPayment(Request $request): RedirectResponse
    {
        $request->validate([
            'instructor_profile_id' => ['required', 'exists:instructor_profiles,id'],
            'items' => ['required', 'string'],
        ]);

        $items = json_decode($request->input('items'), true);
        if (! is_array($items) || empty($items)) {
            return redirect()->back()->withErrors(['items' => 'Invalid booking data.']);
        }
        foreach ($items as $item) {
            if (empty($item['booking_type']) || ! isset($item['price']) || empty($item['date_iso']) || empty($item['scheduled_at'])) {
                return redirect()->back()->withErrors(['items' => 'Invalid booking item.']);
            }
        }
        $subtotal = collect($items)->sum('price');
        $feePercent = 4;
        $fee = round($subtotal * $feePercent) / 100;
        $total = $subtotal + $fee;

        session([
            'learner_booking_order' => [
                'instructor_profile_id' => $request->input('instructor_profile_id'),
                'items' => $items,
                'subtotal' => $subtotal,
                'fee' => $fee,
                'total' => $total,
                'fee_percent' => $feePercent,
            ],
        ]);

        return redirect()->route('learner.bookings.payment');
    }

    /**
     * Show the payment step (after Continue from Make a Booking).
     */
    public function payment(Request $request): View|RedirectResponse
    {
        $order = session('learner_booking_order');
        if (! $order || empty($order['items'])) {
            return redirect()->route('learner.dashboard')->with('message', 'No booking in progress. Please start again.');
        }

        $states = State::orderBy('name')->get(['id', 'name', 'code']);
        $suburbsByState = State::with(['suburbs' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($state) => [$state->id => $state->suburbs->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'postcode' => $s->postcode])->values()->toArray()])
            ->toArray();

        $user = Auth::user();

        return view('learner.pages.booking-payment', [
            'order' => $order,
            'states' => $states,
            'suburbsByState' => $suburbsByState,
            'billingName' => $user->name ?? '',
        ]);
    }
}
