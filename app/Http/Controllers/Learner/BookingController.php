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
use App\Notifications\PaymentReceipt;
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
     * Returns a redirect response if the (logged-in) user can't book this
     * female-only instructor. Returns null if all OK.
     * Guests pass through — they're checked at registration step.
     */
    private function checkFemaleOnlyGate(?InstructorProfile $profile): ?RedirectResponse
    {
        if (! $profile || ! $profile->isFemaleOnly()) {
            return null;
        }
        $user = Auth::user();
        if (! $user) {
            return null; // guest — gate at registration
        }
        if ($user->isAdmin() || strtolower((string) ($user->gender ?? '')) === 'female') {
            return null;
        }
        return redirect()->route('find-instructor')
            ->with('error', 'This instructor only accepts female learners. Please choose another instructor.');
    }

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

        $instructorProfile = InstructorProfile::with(['user:id,name,phone,gender'])
            ->where('id', $instructorProfileId)
            ->where('is_active', true)
            ->firstOrFail();

        if ($redirect = $this->checkFemaleOnlyGate($instructorProfile)) return $redirect;

        // Pre-seed the package session with instructor context
        session([
            'learner_booking_package.instructor_profile_id' => (int) $instructorProfileId,
        ]);

        $pricing = new \App\Services\PricingService();
        return view('learner.pages.booking-amount', [
            'instructorProfile' => $instructorProfile,
            'discountTiers' => $pricing->getDiscountTiers(),
            'hourPackages' => $pricing->getBookingHourPackages(),
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

        // Calculate bulk discount percentage from admin-configured tiers
        $discountPct = (new \App\Services\PricingService())->discountPctForHours($hours);

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

        $instructorProfile = InstructorProfile::with('user:id,name,gender')
            ->where('id', $instructorProfileId)
            ->where('is_active', true)
            ->firstOrFail();

        if ($redirect = $this->checkFemaleOnlyGate($instructorProfile)) return $redirect;

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
            $defaultTestPrice = (new \App\Services\PricingService())->defaultTestPackagePrice();
            $package['test_package_price'] = (float) ($instructorProfile->test_package_price ?? $defaultTestPrice);
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

        $instructorProfile = InstructorProfile::with(['user:id,name,phone,gender', 'serviceAreas.state'])
            ->where('id', $instructorProfileId)
            ->where('is_active', true)
            ->firstOrFail();

        if ($redirect = $this->checkFemaleOnlyGate($instructorProfile)) return $redirect;

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
            // Prefer admin-managed key from SiteSetting; fall back to env GOOGLE_MAPS_API_KEY for dev
            'googleMapsApiKey' => \App\Models\SiteSetting::get('google_maps_api_key') ?: config('services.google.maps_api_key'),
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

        // Apply referral discount automatically (if user qualifies for first-booking discount)
        $referralDiscount = 0;
        $user = Auth::user();
        if ($user) {
            $referralDiscount = (new \App\Services\PricingService())->referralDiscountFor($user, $afterDiscount);
            if ($referralDiscount > 0) {
                $afterDiscount -= $referralDiscount;
            }
        }

        // ── New fee model ──
        // Instructor receives their full price. Platform adds:
        //   - $5 service fee per lesson (flat, always charged)
        //   - $2 processing fee per lesson (waived on 5+ lesson packages)
        $lessonCount = count($items);
        $fees = (new \App\Services\FeeCalculator())->calculate($afterDiscount, $lessonCount);

        // Backwards-compat shape kept for downstream readers (payment view + JS expect `fee` + `total`).
        $fee = $fees['platform_fee_total'];
        $total = $fees['total'];

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
                'fee' => $fee,                                    // total platform fees (service + processing)
                'service_fee_total' => $fees['service_fee_total'],
                'processing_fee_total' => $fees['processing_fee_total'],
                'savings_vs_single' => $fees['savings_vs_single'],
                'package_eligible' => $fees['package_eligible'],
                'lesson_count' => $lessonCount,
                'total' => $total,
                'package_hours' => $package['hours'] ?? null,
                'referral_discount' => $referralDiscount,
                'coupon_code' => null,
                'coupon_discount' => 0,
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

        $instructorProfile = InstructorProfile::with('user:id,name,gender')
            ->where('id', $order['instructor_profile_id'])
            ->firstOrFail();

        if ($redirect = $this->checkFemaleOnlyGate($instructorProfile)) return $redirect;

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
            'gender'           => 'required|in:male,female,other,prefer_not_to_say',
            'describes_as'     => 'required|string|max:40',
            'marketing_opt_in' => 'nullable|boolean',
            'terms_accepted'   => 'required|accepted',
            'referral_code'    => 'nullable|string|max:60',
        ];

        if ($isGuest) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // Female-only safety gate: block if instructor is female-only and learner isn't female
        $instructorProfile = InstructorProfile::with('user:id,name,gender')
            ->find($order['instructor_profile_id']);
        if ($instructorProfile && $instructorProfile->isFemaleOnly() && $validated['gender'] !== 'female') {
            return back()->withErrors([
                'gender' => 'This instructor only accepts female learners. Please choose a different instructor.',
            ])->withInput();
        }

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
                'gender'            => $validated['gender'],
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

        // Female-only safety gate (logged-in user check)
        $instructorProfile = InstructorProfile::with('user:id,name,gender')
            ->find($order['instructor_profile_id']);
        if ($redirect = $this->checkFemaleOnlyGate($instructorProfile)) return $redirect;

        // ALSO check the guest's chosen gender from Step 4 if provided
        if (! Auth::check() && $details && $instructorProfile?->isFemaleOnly()) {
            // Step 4 collects "registering_for" but not gender. We rely on User::create
            // to be blocked at processPayment if gender provided is non-female.
            // Nothing to do here — final gate is in processPayment().
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
     * Apply a coupon code to the current order.
     * Called via AJAX from the payment page; returns updated totals.
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string|max:50']);
        $order = session('learner_booking_order');
        if (! $order) {
            return response()->json(['ok' => false, 'message' => 'No active order.'], 422);
        }

        $pricing = new \App\Services\PricingService();
        // Re-compute the cart total *before* coupon and referral so we can re-apply both cleanly
        $itemsSubtotal = (float) ($order['subtotal'] ?? 0);
        $bulkDiscount = (float) ($order['discount_amount'] ?? 0);
        $testPrice = (float) ($order['test_package_price'] ?? 0);
        $referralDiscount = (float) ($order['referral_discount'] ?? 0);
        $preCouponTotal = max(0, $itemsSubtotal - $bulkDiscount + $testPrice - $referralDiscount);

        $result = $pricing->validateCoupon($request->input('code'), Auth::user(), $preCouponTotal);
        if (! $result['ok']) {
            return response()->json(['ok' => false, 'message' => $result['reason']], 422);
        }

        $couponDiscount = (float) $result['discount'];
        $afterDiscount = max(0, $preCouponTotal - $couponDiscount);
        // Recompute fees with the flat model (service + processing) — must match how
        // the order was originally built and how the final charge is calculated.
        $lessonCount = (int) ($order['lesson_count'] ?? 1);
        $fees = (new \App\Services\FeeCalculator())->calculate($afterDiscount, $lessonCount);
        $fee = $fees['platform_fee_total'];
        $total = $fees['total'];

        $order['coupon_code'] = $result['coupon']->code;
        $order['coupon_discount'] = $couponDiscount;
        $order['after_discount'] = $afterDiscount;
        $order['fee'] = $fee;
        $order['service_fee_total'] = $fees['service_fee_total'];
        $order['processing_fee_total'] = $fees['processing_fee_total'];
        $order['total'] = $total;
        session(['learner_booking_order' => $order]);

        return response()->json([
            'ok' => true,
            'message' => 'Coupon applied — you saved $' . number_format($couponDiscount, 2),
            'order' => [
                'coupon_code' => $order['coupon_code'],
                'coupon_discount' => $couponDiscount,
                'after_discount' => $afterDiscount,
                'fee' => $fee,
                'total' => $total,
            ],
        ]);
    }

    /**
     * Remove an applied coupon.
     */
    public function removeCoupon(Request $request): JsonResponse
    {
        $order = session('learner_booking_order');
        if (! $order) {
            return response()->json(['ok' => false, 'message' => 'No active order.'], 422);
        }

        $itemsSubtotal = (float) ($order['subtotal'] ?? 0);
        $bulkDiscount = (float) ($order['discount_amount'] ?? 0);
        $testPrice = (float) ($order['test_package_price'] ?? 0);
        $referralDiscount = (float) ($order['referral_discount'] ?? 0);
        $afterDiscount = max(0, $itemsSubtotal - $bulkDiscount + $testPrice - $referralDiscount);
        // Recompute fees with the flat model (service + processing) to match the order build.
        $lessonCount = (int) ($order['lesson_count'] ?? 1);
        $fees = (new \App\Services\FeeCalculator())->calculate($afterDiscount, $lessonCount);
        $fee = $fees['platform_fee_total'];
        $total = $fees['total'];

        $order['coupon_code'] = null;
        $order['coupon_discount'] = 0;
        $order['after_discount'] = $afterDiscount;
        $order['fee'] = $fee;
        $order['service_fee_total'] = $fees['service_fee_total'];
        $order['processing_fee_total'] = $fees['processing_fee_total'];
        $order['total'] = $total;
        session(['learner_booking_order' => $order]);

        return response()->json([
            'ok' => true,
            'message' => 'Coupon removed.',
            'order' => [
                'after_discount' => $afterDiscount,
                'fee' => $fee,
                'total' => $total,
            ],
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
            'payment_method' => 'required|in:card,wallet',
            'billing_name' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string|max:500',
        ]);

        // Guests can't use wallet (no wallet exists)
        if ($isGuest && $validated['payment_method'] === 'wallet') {
            return response()->json(['message' => 'Wallet payment requires an account. Please use card.'], 422);
        }

        // Final female-only safety gate (defensive — also enforced upstream)
        $instructorProfileForCheck = InstructorProfile::with('user:id,gender')->find($order['instructor_profile_id']);
        if ($instructorProfileForCheck && $instructorProfileForCheck->isFemaleOnly()) {
            $bookerGender = $isGuest
                ? ($details['gender'] ?? null)
                : strtolower((string) (Auth::user()->gender ?? ''));
            if (strtolower((string) $bookerGender) !== 'female' && ! (Auth::user()?->isAdmin() ?? false)) {
                return response()->json([
                    'message' => 'This instructor only accepts female learners. Please choose a different instructor.',
                ], 422);
            }
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
                        'first_name' => $details['first_name'] ?? null,
                        'last_name'  => $details['last_name'] ?? null,
                        'email'    => $guestEmail,
                        'phone'    => $details['phone'] ?? null,
                        'gender'   => $details['gender'] ?? null,
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

            // Coupon + referral discounts apply to the whole order — distribute proportionally across items
            $couponTotal = (float) ($order['coupon_discount'] ?? 0);
            $referralTotal = (float) ($order['referral_discount'] ?? 0);
            $itemsSubtotalAfterBulk = max(0.01, collect($order['items'])->sum(fn ($i) => (float) ($i['price'] ?? 0) * $discountMultiplier));

            // ── Fees per item — package waiver detected from total item count ──
            $feeCalc = new \App\Services\FeeCalculator();
            $itemCount = count($order['items']);
            $servicePerItem    = $feeCalc->serviceFeePerItem();
            $processingPerItem = $feeCalc->processingFeePerItem($itemCount);

            foreach ($order['items'] as $item) {
                $itemPrice = (float) ($item['price'] ?? 0);
                $discountedPrice = round($itemPrice * $discountMultiplier, 2);

                // Pro-rata share of coupon + referral discount for this item
                $share = $discountedPrice / $itemsSubtotalAfterBulk;
                $itemCouponDiscount = round($couponTotal * $share, 2);
                $itemReferralDiscount = round($referralTotal * $share, 2);
                $itemBulkDiscount = round($itemPrice - $discountedPrice, 2);
                $finalAmount = max(0, round($discountedPrice - $itemCouponDiscount - $itemReferralDiscount, 2));

                // For card payments, hold bookings as PROPOSED + PENDING — Stripe
                // webhook (or success page fallback) flips them to CONFIRMED + PAID
                // after the charge succeeds. Wallet/free bookings remain confirmed
                // immediately.
                $isCard = $validated['payment_method'] === 'card' && $finalAmount > 0;
                $bookingStatus = $isCard ? Booking::STATUS_PROPOSED : Booking::STATUS_CONFIRMED;
                $bookingPayStatus = $isCard ? Booking::PAYMENT_PENDING : Booking::PAYMENT_PAID;

                // ── NEW FEE MODEL ──
                // amount             = lesson price (what learner pays for the lesson itself)
                // platform_fee       = service fee ($5) — repurposed from old 4% calc
                // processing_fee     = $2 or $0 (waived 5+ packages)
                // instructor_net     = full lesson price ($finalAmount) — instructor keeps their price
                // stripe_fee_estimate= estimated portion of Stripe's cut allocated to this booking
                $totalChargeForBooking = $finalAmount + $servicePerItem + $processingPerItem;
                $stripeEstimateForBooking = $feeCalc->estimateStripeFee($totalChargeForBooking);

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
                    'amount' => $finalAmount,
                    'bulk_discount_amount' => $itemBulkDiscount,
                    'coupon_discount_amount' => $itemCouponDiscount,
                    'coupon_code' => $order['coupon_code'] ?? null,
                    'referral_discount_amount' => $itemReferralDiscount,
                    'platform_fee' => $servicePerItem,                  // flat service fee per booking
                    'processing_fee' => $processingPerItem,             // $2 or $0
                    'stripe_fee_estimate' => $stripeEstimateForBooking, // for admin dashboard
                    'instructor_net_amount' => $finalAmount,            // instructor gets their full price
                    'status' => $bookingStatus,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => $bookingPayStatus,
                ]);
                $bookings[] = $booking;
            }

            // ── CARD PAYMENT FORK ──
            // For card method, we don't send receipts or do wallet stuff here.
            // We create a Stripe Checkout session for the whole order and
            // return the URL. The webhook (or success page fallback) flips
            // bookings to PAID and fires the receipt + notifications.
            if ($validated['payment_method'] === 'card' && $total > 0) {
                try {
                    $checkoutUrl = app(\App\Services\StripeService::class)
                        ->createCheckoutSessionForBookings($bookings);

                    // Record coupon redemption now so it can't be re-used during checkout
                    if (! empty($order['coupon_code']) && $couponTotal > 0 && ! empty($bookings) && $user) {
                        $coupon = \App\Models\Coupon::where('code', $order['coupon_code'])->first();
                        if ($coupon) {
                            \App\Models\CouponRedemption::create([
                                'coupon_id'        => $coupon->id,
                                'user_id'          => $user->id,
                                'booking_id'       => $bookings[0]->id,
                                'discount_applied' => $couponTotal,
                                'order_total'     => (float) ($order['total'] ?? 0),
                            ]);
                            $coupon->increment('used_count');
                        }
                    }

                    // Auto-login the new guest user so they can return to success page
                    if ($isGuest && $user && ! Auth::check()) {
                        Auth::login($user, remember: true);
                    }

                    // Clear order from session — bookings are persisted
                    session()->forget(['learner_booking_order', 'learner_booking_details', 'learner_booking_package']);

                    return response()->json([
                        'data' => [
                            'requires_payment' => true,
                            'checkout_url'     => $checkoutUrl,
                            'booking_ids'      => collect($bookings)->pluck('id')->all(),
                        ],
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Stripe checkout creation failed in processPayment: ' . $e->getMessage());
                    // Roll back the transaction — bookings disappear, no charge happened
                    throw $e;
                }
            }

            // ── Record coupon redemption + bump usage counter ──
            if (! empty($order['coupon_code']) && $couponTotal > 0 && ! empty($bookings)) {
                $coupon = \App\Models\Coupon::where('code', $order['coupon_code'])->first();
                if ($coupon && $user) {
                    \App\Models\CouponRedemption::create([
                        'coupon_id' => $coupon->id,
                        'user_id' => $user->id,
                        'booking_id' => $bookings[0]->id, // link to first item
                        'discount_applied' => $couponTotal,
                        'order_total' => (float) ($order['total'] ?? 0),
                    ]);
                    $coupon->increment('used_count');
                }
            }

            // ── Credit the referrer's wallet (if this is the invitee's first paid booking) ──
            if ($referralTotal > 0 && $user && $user->referred_by_user_id) {
                try {
                    $credit = (new \App\Services\PricingService())->referrerCreditAmount();
                    if ($credit > 0) {
                        $referrerWallet = LearnerWallet::firstOrCreate(['user_id' => $user->referred_by_user_id]);
                        $referrerWallet->balance = (float) $referrerWallet->balance + $credit;
                        $referrerWallet->save();
                        LearnerTransaction::create([
                            'user_id' => $user->referred_by_user_id,
                            'type' => LearnerTransaction::TYPE_CREDIT_PURCHASE,
                            'amount' => $credit,
                            'description' => 'Referral reward: ' . ($user->name ?? 'a friend') . ' completed first booking',
                            'balance_after' => $referrerWallet->balance,
                        ]);
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Referral credit failed: ' . $e->getMessage());
                }
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

            // Send a single payment receipt for the whole transaction (covers all bookings at once)
            try {
                $receipt = new PaymentReceipt(
                    bookings: $bookings,
                    totalCharged: $total,
                    paymentMethod: $validated['payment_method'],
                    transactionRef: null, // populate when real gateway integration is added
                );
                if ($user) {
                    $user->notify($receipt);
                } else {
                    // Guests get the receipt to their guest email
                    $guestEmail = $bookings[0]->guest_email ?? null;
                    if ($guestEmail) {
                        \Illuminate\Support\Facades\Notification::route('mail', $guestEmail)->notify($receipt);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Payment receipt email failed: ' . $e->getMessage());
            }

            // Send per-booking notifications
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
