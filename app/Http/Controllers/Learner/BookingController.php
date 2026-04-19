<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\InstructorProfile;
use App\Models\LearnerTransaction;
use App\Models\LearnerWallet;
use App\Models\State;
use App\Models\User;
use App\Notifications\AdminBookingAlert;
use App\Notifications\BookingConfirmed;
use App\Notifications\InstructorNewBooking;
use App\Notifications\WelcomeNotification;
use App\Traits\NotifiesAdmin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Handles both authenticated learner bookings AND guest bookings.
 *
 * Guest flow (EasyLicence-style):
 *   1. Guest visits /learner/bookings/new?instructor_profile_id=X (no auth required)
 *   2. Guest fills booking details, clicks Continue
 *   3. Guest provides name/email/phone + payment details at payment step
 *   4. After successful payment:
 *       - A new User account is created (role=learner)
 *       - Guest is auto-logged in
 *       - Welcome email with password-reset link is sent
 *       - Bookings are linked to the new user
 */
class BookingController extends Controller
{
    use NotifiesAdmin;

    /**
     * Show the Make a Booking page.
     * Accessible to guests and authenticated learners.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $instructorProfileId = $request->input('instructor_profile_id');
        if (! $instructorProfileId) {
            return redirect()->route('find-instructor')->with('message', 'Please select an instructor to book.');
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
            'isGuest' => ! Auth::check(),
        ]);
    }

    /**
     * Store order in session and redirect to payment step.
     * Accessible to guests and authenticated learners.
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
        $feePercent = (float) \App\Models\SiteSetting::get('platform_fee_percent', 4);
        $fee = round($subtotal * $feePercent / 100, 2);
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
     * Show the payment step.
     * Guests see extra fields: name/email/phone for account creation.
     */
    public function payment(Request $request): View|RedirectResponse
    {
        $order = session('learner_booking_order');
        if (! $order || empty($order['items'])) {
            if (Auth::check()) {
                return redirect()->route('learner.dashboard')->with('message', 'No booking in progress. Please start again.');
            }
            return redirect()->route('find-instructor')->with('message', 'No booking in progress. Please start again.');
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
            'isGuest' => ! Auth::check(),
            'guestName' => session('guest_booking.name', ''),
            'guestEmail' => session('guest_booking.email', ''),
            'guestPhone' => session('guest_booking.phone', ''),
        ]);
    }

    /**
     * Process the booking payment.
     * Creates booking records, deducts from wallet or processes payment.
     * If guest — creates User account, links bookings, auto-logs-in, sends welcome email.
     */
    public function processPayment(Request $request): JsonResponse
    {
        $order = session('learner_booking_order');

        if (! $order || empty($order['items'])) {
            return response()->json(['message' => 'No booking in progress.'], 422);
        }

        $isGuest = ! Auth::check();

        // Guests must provide name/email/phone
        if ($isGuest) {
            $guestValidated = $request->validate([
                'guest_name'  => 'required|string|max:120',
                'guest_email' => 'required|email|max:160',
                'guest_phone' => 'required|string|max:30',
            ]);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:card,paypal,wallet',
            'billing_name' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string|max:500',
        ]);

        // Guests can't use wallet (no wallet exists)
        if ($isGuest && $validated['payment_method'] === 'wallet') {
            return response()->json(['message' => 'Wallet payment requires an account. Please use card or PayPal.'], 422);
        }

        $total = (float) ($order['total'] ?? 0);
        $useWallet = $validated['payment_method'] === 'wallet';

        return DB::transaction(function () use ($order, $total, $useWallet, $validated, $isGuest, $request) {
            $user = Auth::user();
            $newUserCreated = false;
            $tempPassword = null;

            // ── Guest flow: create user account FIRST (before bookings) ──
            if ($isGuest) {
                $guestEmail = strtolower(trim($request->input('guest_email')));
                $existing = User::where('email', $guestEmail)->first();

                if ($existing) {
                    // Email already exists — link bookings to existing user but don't log them in
                    // (prevents account takeover via guest booking)
                    $user = $existing;
                    Log::info("Guest booking: linked to existing user #{$existing->id} via email");
                } else {
                    // Create new learner account
                    $tempPassword = Str::random(12);
                    $user = User::create([
                        'name'     => $request->input('guest_name'),
                        'email'    => $guestEmail,
                        'phone'    => $request->input('guest_phone'),
                        'role'     => User::ROLE_LEARNER,
                        'password' => Hash::make($tempPassword),
                    ]);
                    $newUserCreated = true;
                }
            }

            // If paying with wallet, check balance (only authenticated users)
            if ($useWallet && $user) {
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
                    'learner_id' => $user?->id,
                    'guest_name' => $isGuest ? $request->input('guest_name') : null,
                    'guest_email' => $isGuest ? strtolower(trim($request->input('guest_email'))) : null,
                    'guest_phone' => $isGuest ? $request->input('guest_phone') : null,
                    'is_guest_booking' => $isGuest,
                    'instructor_id' => $instructorProfile ? $instructorProfile->user_id : null,
                    'instructor_profile_id' => $order['instructor_profile_id'],
                    'suburb_id' => $item['pickup_suburb_id'] ?? $item['suburb_id'] ?? null,
                    'type' => $item['booking_type'] ?? 'lesson',
                    'transmission' => $item['transmission'] ?? 'auto',
                    'scheduled_at' => $item['scheduled_at'],
                    'duration_minutes' => $item['duration_minutes'] ?? 60,
                    'amount' => $itemPrice,
                    'platform_fee' => round($itemPrice * (float) \App\Models\SiteSetting::get('platform_fee_percent', 4) / 100, 2),
                    'instructor_net_amount' => max(round($itemPrice - (float) \App\Models\SiteSetting::get('platform_service_fee', 5.00) - (float) \App\Models\SiteSetting::get('payment_processing_fee', 2.00), 2), 0),
                    'status' => Booking::STATUS_CONFIRMED,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => Booking::PAYMENT_PAID,
                ]);
                $bookings[] = $booking;
            }

            // Process wallet deduction (authenticated users only)
            if ($useWallet && $user) {
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

            // Send notifications
            foreach ($bookings as $booking) {
                try {
                    if ($user) {
                        $user->notify(new BookingConfirmed($booking));
                    } else {
                        // Send to guest email directly (via on-demand routing)
                        \Illuminate\Support\Facades\Notification::route('mail', $booking->guest_email)
                            ->notify(new BookingConfirmed($booking));
                    }
                } catch (\Throwable $e) {
                    Log::warning('Booking notification failed: ' . $e->getMessage());
                }

                // Notify instructor
                try {
                    $instructor = User::find($booking->instructor_id);
                    if ($instructor) {
                        $instructor->notify(new InstructorNewBooking($booking));
                    }
                } catch (\Throwable $e) {
                    Log::warning('Instructor booking notification failed: ' . $e->getMessage());
                }

                // Notify admin
                $this->notifyAdminAboutBooking($booking, AdminBookingAlert::EVENT_NEW);
            }

            // Guest: send welcome email + password reset link + auto-login
            if ($newUserCreated && $user) {
                try {
                    $user->notify(new WelcomeNotification($user, $tempPassword));
                } catch (\Throwable $e) {
                    Log::warning('Welcome email failed: ' . $e->getMessage());
                }

                // Also send password reset so they can set their own password
                try {
                    Password::sendResetLink(['email' => $user->email]);
                } catch (\Throwable $e) {
                    Log::warning('Password reset email failed: ' . $e->getMessage());
                }

                // Auto-login the new guest user
                Auth::login($user);
                $request->session()->regenerate();
            }

            // Clear session order + guest draft
            session()->forget(['learner_booking_order', 'guest_booking']);

            return response()->json([
                'message' => $newUserCreated
                    ? 'Booking confirmed! Your account has been created and a password-reset link has been emailed.'
                    : 'Booking confirmed!',
                'data' => [
                    'booking_ids' => collect($bookings)->pluck('id')->toArray(),
                    'total_charged' => $total,
                    'payment_method' => $validated['payment_method'],
                    'account_created' => $newUserCreated,
                    'redirect' => Auth::check() ? route('learner.dashboard') : route('find-instructor'),
                ],
            ]);
        });
    }
}
