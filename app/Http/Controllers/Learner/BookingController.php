<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\InstructorProfile;
use App\Models\LearnerTransaction;
use App\Models\LearnerWallet;
use App\Models\State;
use App\Notifications\BookingConfirmed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    /**
     * Process the booking payment.
     * Creates booking records, deducts from wallet or processes payment, records transactions.
     */
    public function processPayment(Request $request): JsonResponse
    {
        $user = Auth::user();
        $order = session('learner_booking_order');

        if (! $order || empty($order['items'])) {
            return response()->json(['message' => 'No booking in progress.'], 422);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:card,paypal,wallet',
            'billing_name' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string|max:500',
        ]);

        $total = (float) ($order['total'] ?? 0);
        $useWallet = $validated['payment_method'] === 'wallet';

        return DB::transaction(function () use ($user, $order, $total, $useWallet, $validated) {
            // If paying with wallet, check balance
            if ($useWallet) {
                $wallet = LearnerWallet::where('user_id', $user->id)->first();
                if (! $wallet || (float) $wallet->balance < $total) {
                    return response()->json(['message' => 'Insufficient wallet balance. Please add credit or use a card.'], 422);
                }
            }

            $bookings = [];
            $instructorProfile = InstructorProfile::find($order['instructor_profile_id']);

            foreach ($order['items'] as $item) {
                $itemPrice = (float) ($item['price'] ?? 0);
                $booking = Booking::create([
                    'learner_id' => $user->id,
                    'instructor_id' => $instructorProfile ? $instructorProfile->user_id : null,
                    'instructor_profile_id' => $order['instructor_profile_id'],
                    'suburb_id' => $item['suburb_id'] ?? null,
                    'type' => $item['booking_type'] ?? 'lesson',
                    'transmission' => $item['transmission'] ?? 'auto',
                    'scheduled_at' => $item['scheduled_at'],
                    'duration_minutes' => $item['duration_minutes'] ?? 60,
                    'amount' => $itemPrice,
                    'platform_fee' => round($itemPrice * 0.04, 2),
                    'status' => Booking::STATUS_CONFIRMED,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => Booking::PAYMENT_PAID,
                ]);
                $bookings[] = $booking;
            }

            // Process wallet deduction
            if ($useWallet) {
                $wallet = LearnerWallet::where('user_id', $user->id)->first();
                $wallet->balance = (float) $wallet->balance - $total;
                $wallet->save();

                LearnerTransaction::create([
                    'user_id' => $user->id,
                    'type' => LearnerTransaction::TYPE_LESSON_PAYMENT,
                    'description' => 'Booking payment — ' . count($bookings) . ' booking(s)',
                    'amount' => -$total,
                    'balance_after' => $wallet->balance,
                    'booking_id' => $bookings[0]->id ?? null,
                ]);
            }

            // Send confirmation notification for each booking
            foreach ($bookings as $booking) {
                try {
                    $user->notify(new BookingConfirmed($booking));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Booking notification failed: ' . $e->getMessage());
                }
            }

            // Clear session order
            session()->forget('learner_booking_order');

            return response()->json([
                'message' => 'Booking confirmed!',
                'data' => [
                    'booking_ids' => collect($bookings)->pluck('id')->toArray(),
                    'total_charged' => $total,
                    'payment_method' => $validated['payment_method'],
                ],
            ]);
        });
    }
}
