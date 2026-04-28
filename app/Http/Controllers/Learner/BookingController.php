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
     * STEP 2: Choose lesson amount (hours package with bulk discount).
     * GUEST-ONLY step — logged-in learners skip this and go directly to "Make a Booking".
     */
    public function amount(Request $request): View|RedirectResponse
    {
        $instructorProfileId = $request->input('instructor_profile_id');
        if (! $instructorProfileId) {
            return redirect()->route('find-instructor')->with('message', 'Please select an instructor to book.');
        }

        // Logged-in learners skip the package/upsell flow — go straight to Make a Booking
        if (Auth::check()) {
            return redirect()->route('learner.bookings.new', ['instructor_profile_id' => $instructorProfileId]);
        }

        $instructorProfile = InstructorProfile::with(['user:id,name,phone'])
            ->where('id', $instructorProfileId)
            ->where('is_active', true)
            ->firstOrFail();

        // Pre-seed the package session with instructor context
        session([
            'learner_booking_package.instructor_profile_id' => (int) $instructorProfileId,
        ]);

        return view('learner.pages.booking-amount', [
            'instructorProfile' => $instructorProfile,
        ]);
    }

    /**
     * Store the selected package (hours + discount) in session.
     * Then redirect to the Test Package upsell step.
     */
    public function storeAmount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'instructor_profile_id' => ['required', 'exists:instructor_profiles,id'],
            'hours' => ['required'],
            'custom_hours' => ['nullable', 'integer', 'min:1', 'max:40'],
        ]);

        $hours = $validated['hours'] === 'custom'
            ? (int) ($validated['custom_hours'] ?? 0)
            : (int) $validated['hours'];

        if ($hours < 1) {
            return redirect()->back()->withErrors(['hours' => 'Please select how many lesson hours you want to purchase.']);
        }

        // Calculate bulk discount percentage
        $discountPct = 0;
        if ($hours >= 10) $discountPct = 10;
        elseif ($hours >= 6) $discountPct = 5;

        session([
            'learner_booking_package' => [
                'instructor_profile_id' => (int) $validated['instructor_profile_id'],
                'hours' => $hours,
                'discount_pct' => $discountPct,
                'add_test_package' => false,
                'selected_at' => now()->toIso8601String(),
            ],
        ]);

        // Go to the Test Package upsell screen (sub-step of Amount)
        return redirect()->route('learner.bookings.test-package', ['instructor_profile_id' => $validated['instructor_profile_id']]);
    }

    /**
     * Show the "Add a Driving Test Package" upsell screen.
     * GUEST-ONLY sub-step — logged-in learners skip this.
     */
    public function testPackage(Request $request): View|RedirectResponse
    {
        $instructorProfileId = $request->input('instructor_profile_id');

        // Logged-in users skip directly to Make a Booking
        if (Auth::check()) {
            return redirect()->route('learner.bookings.new', ['instructor_profile_id' => $instructorProfileId]);
        }

        $package = session('learner_booking_package');

        // Require an existing package selection
        if (! $package || (int) ($package['instructor_profile_id'] ?? 0) !== (int) $instructorProfileId) {
            return redirect()->route('learner.bookings.amount', ['instructor_profile_id' => $instructorProfileId]);
        }

        $instructorProfile = InstructorProfile::with('user:id,name')
            ->where('id', $instructorProfileId)
            ->where('is_active', true)
            ->firstOrFail();

        return view('learner.pages.booking-test-package', [
            'instructorProfile' => $instructorProfile,
        ]);
    }

    /**
     * Handle Add or Skip from the Test Package upsell screen.
     * Then proceed to the "Book your lessons" step.
     */
    public function storeTestPackage(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:add,skip',
        ]);

        $package = session('learner_booking_package', []);
        if (empty($package)) {
            return redirect()->route('find-instructor');
        }

        $package['add_test_package'] = $request->input('action') === 'add';

        // If adding, capture the price snapshot
        if ($package['add_test_package']) {
            $instructorProfile = InstructorProfile::find($package['instructor_profile_id']);
            $package['test_package_price'] = (float) ($instructorProfile->test_package_price ?? 225);
        } else {
            $package['test_package_price'] = 0;
        }

        session(['learner_booking_package' => $package]);

        return redirect()->route('learner.bookings.new', [
            'instructor_profile_id' => $package['instructor_profile_id'],
        ]);
    }

    /**
     * Make a Booking page.
     * - GUESTS: This is STEP 3 of the 5-step EasyLicence flow (Book your lessons).
     *   Requires an active package selection in session (redirects to Amount step if missing).
     * - LOGGED-IN learners: This is the entry point (uses dashboard layout, no stepper, no package required).
     */
    public function create(Request $request): View|RedirectResponse
    {
        $instructorProfileId = $request->input('instructor_profile_id');
        if (! $instructorProfileId) {
            return redirect()->route('find-instructor')->with('message', 'Please select an instructor to book.');
        }

        $isAuth = Auth::check();
        $package = session('learner_booking_package');

        // For GUESTS only — require the Amount step to be completed first
        if (! $isAuth) {
            if (! $package || (int) ($package['instructor_profile_id'] ?? 0) !== (int) $instructorProfileId) {
                return redirect()->route('learner.bookings.amount', ['instructor_profile_id' => $instructorProfileId]);
            }
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
            'isGuest' => ! $isAuth,
            'package' => $isAuth ? null : $package, // Auth users have no package
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

        // Apply bulk-hours discount from package selection (EasyLicence-style)
        $package = session('learner_booking_package');
        $discountPct = (float) ($package['discount_pct'] ?? 0);
        $discount = $discountPct > 0 ? round($subtotal * $discountPct / 100, 2) : 0;
        $afterDiscount = $subtotal - $discount;

        // Add test package price (no discount) if user added it in step 2b
        $testPackagePrice = 0;
        $addTestPackage = ! empty($package['add_test_package']);
        if ($addTestPackage) {
            $testPackagePrice = (float) ($package['test_package_price'] ?? 0);
            $afterDiscount += $testPackagePrice;
        }

        $feePercent = (float) \App\Models\SiteSetting::get('platform_fee_percent', 4);
        $fee = round($afterDiscount * $feePercent / 100, 2);
        $total = $afterDiscount + $fee;

        session([
            'learner_booking_order' => [
                'instructor_profile_id' => $request->input('instructor_profile_id'),
                'items' => $items,
                'subtotal' => $subtotal,
                'discount_pct' => $discountPct,
                'discount_amount' => $discount,
                'add_test_package' => $addTestPackage,
                'test_package_price' => $testPackagePrice,
                'after_discount' => $afterDiscount,
                'fee' => $fee,
                'total' => $total,
                'fee_percent' => $feePercent,
                'package_hours' => $package['hours'] ?? null,
            ],
        ]);

        // Logged-in users go straight to Payment (Step 4 Registration is guest-only)
        if (Auth::check()) {
            return redirect()->route('learner.bookings.payment');
        }
        // Guests must complete Step 4 (Learner Registration) before payment
        return redirect()->route('learner.bookings.details');
    }

    /**
     * STEP 4: Learner Registration — collect personal details + (for guests) password.
     * GUEST-ONLY step — logged-in learners skip directly to Payment.
     */
    public function details(Request $request): View|RedirectResponse
    {
        $order = session('learner_booking_order');
        if (! $order || empty($order['items'])) {
            return redirect()->route('find-instructor')->with('message', 'No booking in progress. Please start again.');
        }

        // Logged-in learners skip Step 4 — go straight to payment
        if (Auth::check()) {
            return redirect()->route('learner.bookings.payment');
        }

        $instructorProfile = InstructorProfile::with('user:id,name')
            ->where('id', $order['instructor_profile_id'])
            ->firstOrFail();

        $states = State::orderBy('name')->get(['id', 'name', 'code']);
        $suburbsByState = State::with(['suburbs' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($state) => [$state->id => $state->suburbs->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'postcode' => $s->postcode])->values()->toArray()])
            ->toArray();

        return view('learner.pages.booking-registration', [
            'order' => $order,
            'instructorProfile' => $instructorProfile,
            'states' => $states,
            'suburbsByState' => $suburbsByState,
        ]);
    }

    /**
     * Save learner registration details to session, then proceed to payment.
     */
    public function storeDetails(Request $request): RedirectResponse
    {
        $order = session('learner_booking_order');
        if (! $order || empty($order['items'])) {
            return redirect()->route('find-instructor');
        }

        $isGuest = ! Auth::check();

        $rules = [
            'registering_for'  => 'required|in:myself,other',
            'pickup_address'   => 'required|string|max:255',
            'pickup_suburb_id' => 'required|exists:suburbs,id',
            'pickup_state_id'  => 'required|exists:states,id',
            'first_name'       => 'required|string|max:60',
            'last_name'        => 'required|string|max:60',
            'email'            => 'required|email|max:160',
            'phone'            => 'required|string|max:30',
            'dob_day'          => 'required|integer|min:1|max:31',
            'dob_month'        => 'required|integer|min:1|max:12',
            'dob_year'         => 'required|integer|min:1930|max:' . date('Y'),
            'describes_as'     => 'required|string|max:40',
            'marketing_opt_in' => 'nullable|boolean',
            'terms_accepted'   => 'required|accepted',
            'referral_code'    => 'nullable|string|max:60',
        ];

        if ($isGuest) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // Persist details in session for the payment step
        session([
            'learner_booking_details' => [
                'registering_for'   => $validated['registering_for'],
                'pickup_address'    => $validated['pickup_address'],
                'pickup_suburb_id'  => (int) $validated['pickup_suburb_id'],
                'pickup_state_id'   => (int) $validated['pickup_state_id'],
                'first_name'        => $validated['first_name'],
                'last_name'         => $validated['last_name'],
                'email'             => strtolower(trim($validated['email'])),
                'phone'             => $validated['phone'],
                'dob_day'           => (int) $validated['dob_day'],
                'dob_month'         => (int) $validated['dob_month'],
                'dob_year'          => (int) $validated['dob_year'],
                'describes_as'      => $validated['describes_as'],
                'marketing_opt_in'  => (bool) ($validated['marketing_opt_in'] ?? false),
                'terms_accepted'    => true,
                'terms_accepted_at' => now()->toIso8601String(),
                'referral_code'     => $validated['referral_code'] ?? null,
                'password'          => $isGuest ? $validated['password'] : null, // cleared after use
            ],
        ]);

        return redirect()->route('learner.bookings.payment');
    }

    /**
     * Show the payment step.
     * Guest details have already been captured in step 4 (Learner Registration).
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

        // For GUESTS: require Step 4 (Learner Registration) to be completed first
        $details = session('learner_booking_details');
        if (! Auth::check() && ! $details) {
            return redirect()->route('learner.bookings.details');
        }

        $states = State::orderBy('name')->get(['id', 'name', 'code']);
        $suburbsByState = State::with(['suburbs' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($state) => [$state->id => $state->suburbs->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'postcode' => $s->postcode])->values()->toArray()])
            ->toArray();

        return view('learner.pages.booking-payment', [
            'order' => $order,
            'details' => $details,
            'states' => $states,
            'suburbsByState' => $suburbsByState,
            'billingName' => trim(($details['first_name'] ?? '') . ' ' . ($details['last_name'] ?? '')),
            'isGuest' => ! Auth::check(),
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

        // Step 4 (registration) is required for GUESTS only
        $details = session('learner_booking_details');
        if ($isGuest && ! $details) {
            return response()->json(['message' => 'Please complete the registration step first.'], 422);
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

        return DB::transaction(function () use ($order, $total, $useWallet, $validated, $isGuest, $request, $details) {
            $user = Auth::user();
            $newUserCreated = false;
            $tempPassword = null;

            // ── Guest flow: create user account FIRST using Step 4 registration details ──
            if ($isGuest) {
                $guestEmail = strtolower(trim($details['email']));
                $existing = User::where('email', $guestEmail)->first();
                $fullName = trim(($details['first_name'] ?? '') . ' ' . ($details['last_name'] ?? ''));

                if ($existing) {
                    // Email already exists — link bookings to existing user but don't log them in
                    $user = $existing;
                    Log::info("Guest booking: linked to existing user #{$existing->id} via email");
                } else {
                    // Use the password they chose in Step 4 (Learner Registration)
                    $tempPassword = $details['password'] ?? Str::random(12);
                    $user = User::create([
                        'name'     => $fullName,
                        'email'    => $guestEmail,
                        'phone'    => $details['phone'] ?? null,
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

            // Bulk-discount multiplier (applied per booking item)
            $discountPct = (float) ($order['discount_pct'] ?? 0);
            $discountMultiplier = $discountPct > 0 ? (100 - $discountPct) / 100 : 1.0;

            foreach ($order['items'] as $item) {
                $itemPrice = (float) ($item['price'] ?? 0);
                $discountedPrice = round($itemPrice * $discountMultiplier, 2);

                $booking = Booking::create([
                    'learner_id' => $user?->id,
                    'guest_name' => $isGuest ? trim(($details['first_name'] ?? '') . ' ' . ($details['last_name'] ?? '')) : null,
                    'guest_email' => $isGuest ? strtolower(trim($details['email'] ?? '')) : null,
                    'guest_phone' => $isGuest ? ($details['phone'] ?? null) : null,
                    'is_guest_booking' => $isGuest,
                    'instructor_id' => $instructorProfile ? $instructorProfile->user_id : null,
                    'instructor_profile_id' => $order['instructor_profile_id'],
                    'suburb_id' => $item['pickup_suburb_id'] ?? $item['suburb_id'] ?? null,
                    'type' => $item['booking_type'] ?? 'lesson',
                    'transmission' => $item['transmission'] ?? 'auto',
                    'scheduled_at' => $item['scheduled_at'],
                    'duration_minutes' => $item['duration_minutes'] ?? 60,
                    'amount' => $discountedPrice,
                    'platform_fee' => round($discountedPrice * (float) \App\Models\SiteSetting::get('platform_fee_percent', 4) / 100, 2),
                    'instructor_net_amount' => max(round($discountedPrice - (float) \App\Models\SiteSetting::get('platform_service_fee', 5.00) - (float) \App\Models\SiteSetting::get('payment_processing_fee', 2.00), 2), 0),
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

            // Clear session order + guest draft + package + details
            session()->forget(['learner_booking_order', 'guest_booking', 'learner_booking_package', 'learner_booking_details']);

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
