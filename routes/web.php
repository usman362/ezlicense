<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstructorProfileController;
use App\Http\Controllers\InstructorSearchController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboard;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SuburbController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('frontend.home');
});
Route::get('/find-instructor', function () {
    return view('find-instructor');
})->name('find-instructor');

Route::get('/find-instructor/results', function (\Illuminate\Http\Request $request) {
    $suburbId = (string) $request->query('suburb_id', '');
    $q = trim((string) $request->query('q', ''));

    // If absolutely nothing was provided, send back to the search form.
    if ($suburbId === '' && $q === '') {
        return redirect()->route('find-instructor');
    }

    return view('find-instructor-results', [
        'suburb_id' => $suburbId,
        'q' => $q,
        'transmission' => $request->query('transmission', ''),
        'test_pre_booked' => $request->boolean('test_pre_booked'),
    ]);
})->name('find-instructor.results');

Route::get('/instructors/{instructorProfile}', function (App\Models\InstructorProfile $instructorProfile) {
    $instructorProfile->load(['user', 'serviceAreas']);
    return view('instructor-public-show', ['instructorProfile' => $instructorProfile]);
})->name('instructors.show');

// Static pages
Route::get('/about', fn () => view('frontend.pages.about'))->name('about');
Route::get('/contact', fn () => view('frontend.pages.contact'))->name('contact');
Route::get('/terms-and-conditions', fn () => view('frontend.pages.terms'))->name('terms');
Route::get('/privacy-policy', fn () => view('frontend.pages.privacy'))->name('privacy');
Route::get('/support', fn () => redirect('/contact'))->name('support');

// Policies hub and individual policies
Route::prefix('policies')->name('policies.')->group(function () {
    Route::get('/',                     fn () => view('frontend.policies.index'))->name('index');
    Route::get('/instructor-code-of-conduct', fn () => view('frontend.policies.instructor-conduct'))->name('instructor-conduct');
    Route::get('/learner-code-of-conduct',    fn () => view('frontend.policies.learner-conduct'))->name('learner-conduct');
    Route::get('/complaint-handling',   fn () => view('frontend.policies.complaint-handling'))->name('complaint-handling');
    Route::get('/refund-and-cancellation', fn () => view('frontend.policies.refund-cancellation'))->name('refund-cancellation');
    Route::get('/safety',               fn () => view('frontend.policies.safety'))->name('safety');
    Route::get('/dispute-resolution',   fn () => view('frontend.policies.dispute-resolution'))->name('dispute-resolution');
});
Route::get('/driving-test-packages', fn () => view('frontend.pages.driving-test-packages'))->name('driving-test-packages');
Route::get('/international-licence-conversions', fn () => view('frontend.pages.international-licence'))->name('international-licence');
Route::get('/refresher-lessons', fn () => view('frontend.pages.refresher-lessons'))->name('refresher-lessons');
Route::get('/prices-and-packages', fn () => view('frontend.pages.prices-packages'))->name('prices-packages');
Route::get('/industry-insights', fn () => view('frontend.pages.industry-insights'))->name('industry-insights');
Route::get('/instruct-with-us', fn () => view('frontend.pages.instruct-with-us'))->name('instruct-with-us');
Route::get('/instructor-academy', fn () => view('frontend.pages.instructor-academy'))->name('instructor-academy');
Route::get('/gift-vouchers', fn () => view('frontend.pages.gift-vouchers'))->name('gift-vouchers');
Route::get('/practice-test', [App\Http\Controllers\PracticeTestController::class, 'index'])->name('practice-test');
Route::get('/practice-test/{state}', [App\Http\Controllers\PracticeTestController::class, 'state'])->name('practice-test.state');

// Blog (public)
Route::get('/blog', [App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// Calendar ICS feeds (token-authenticated, no login needed)
Route::get('/calendar/instructor/{token}/feed.ics', [App\Http\Controllers\CalendarFeedController::class, 'instructorFeed'])->name('calendar.instructor.feed');
Route::get('/calendar/learner/{token}/feed.ics', [App\Http\Controllers\CalendarFeedController::class, 'learnerFeed'])->name('calendar.learner.feed');

Auth::routes();

// Dedicated login URLs for top bar (split-screen UI)
Route::get('/learner/login', function () {
    return view('auth.learner-login');
})->name('learner.login')->middleware('guest');
Route::get('/instructor/login', function () {
    return view('auth.instructor-login');
})->name('instructor.login')->middleware('guest');

Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

// Admin panel
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Users management
    Route::get('/users', [App\Http\Controllers\Admin\UsersController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [App\Http\Controllers\Admin\UsersController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/json', [App\Http\Controllers\Admin\UsersController::class, 'showJson'])->name('users.show-json');
    Route::patch('/users/{user}/toggle-active', [App\Http\Controllers\Admin\UsersController::class, 'toggleActive'])->name('users.toggle-active');
    Route::patch('/users/{user}/update-role', [App\Http\Controllers\Admin\UsersController::class, 'updateRole'])->name('users.update-role');
    Route::post('/users/{user}/notes', [App\Http\Controllers\Admin\UsersController::class, 'storeNote'])->name('users.notes.store');
    Route::delete('/users/notes/{userAdminNote}', [App\Http\Controllers\Admin\UsersController::class, 'deleteNote'])->name('users.notes.destroy');
    Route::patch('/users/notes/{userAdminNote}/toggle-pin', [App\Http\Controllers\Admin\UsersController::class, 'toggleNotePin'])->name('users.notes.toggle-pin');

    // Instructors management
    Route::get('/instructors', [App\Http\Controllers\Admin\InstructorsController::class, 'index'])->name('instructors.index');
    Route::get('/instructors/{instructorProfile}', [App\Http\Controllers\Admin\InstructorsController::class, 'show'])->name('instructors.show');
    Route::patch('/instructors/{instructorProfile}/update-verification', [App\Http\Controllers\Admin\InstructorsController::class, 'updateVerification'])->name('instructors.update-verification');
    Route::patch('/instructors/{instructorProfile}/toggle-active', [App\Http\Controllers\Admin\InstructorsController::class, 'toggleActive'])->name('instructors.toggle-active');
    Route::patch('/instructors/documents/{instructorDocument}/status', [App\Http\Controllers\Admin\InstructorsController::class, 'updateDocumentStatus'])->name('instructors.update-document-status');
    Route::patch('/instructors/reviews/{review}/approve', [App\Http\Controllers\Admin\InstructorsController::class, 'approveReview'])->name('instructors.approve-review');
    Route::patch('/instructors/reviews/{review}/reject', [App\Http\Controllers\Admin\InstructorsController::class, 'rejectReview'])->name('instructors.reject-review');
    Route::delete('/instructors/reviews/{review}', [App\Http\Controllers\Admin\InstructorsController::class, 'deleteReview'])->name('instructors.delete-review');
    Route::patch('/instructors/reviews/{review}/toggle-visibility', [App\Http\Controllers\Admin\InstructorsController::class, 'toggleReviewVisibility'])->name('instructors.toggle-review-visibility');

    // Instructor audit/history — blocks
    Route::post('/instructors/{instructorProfile}/blocks', [App\Http\Controllers\Admin\InstructorsController::class, 'storeBlock'])->name('instructors.blocks.store');
    Route::patch('/instructors/blocks/{instructorBlock}/lift', [App\Http\Controllers\Admin\InstructorsController::class, 'liftBlock'])->name('instructors.blocks.lift');

    // Instructor audit/history — warnings
    Route::post('/instructors/{instructorProfile}/warnings', [App\Http\Controllers\Admin\InstructorsController::class, 'storeWarning'])->name('instructors.warnings.store');
    Route::delete('/instructors/warnings/{instructorWarning}', [App\Http\Controllers\Admin\InstructorsController::class, 'deleteWarning'])->name('instructors.warnings.destroy');

    // Instructor audit/history — complaints
    Route::post('/instructors/{instructorProfile}/complaints', [App\Http\Controllers\Admin\InstructorsController::class, 'storeComplaint'])->name('instructors.complaints.store');
    Route::patch('/instructors/complaints/{instructorComplaint}', [App\Http\Controllers\Admin\InstructorsController::class, 'updateComplaintStatus'])->name('instructors.complaints.update');
    Route::delete('/instructors/complaints/{instructorComplaint}', [App\Http\Controllers\Admin\InstructorsController::class, 'deleteComplaint'])->name('instructors.complaints.destroy');

    // Instructor audit/history — admin notes
    Route::post('/instructors/{instructorProfile}/notes', [App\Http\Controllers\Admin\InstructorsController::class, 'storeNote'])->name('instructors.notes.store');
    Route::delete('/instructors/notes/{instructorAdminNote}', [App\Http\Controllers\Admin\InstructorsController::class, 'deleteNote'])->name('instructors.notes.destroy');
    Route::patch('/instructors/notes/{instructorAdminNote}/toggle-pin', [App\Http\Controllers\Admin\InstructorsController::class, 'toggleNotePin'])->name('instructors.notes.toggle-pin');

    // Instructor audit/history — correspondence log
    Route::post('/instructors/{instructorProfile}/correspondences', [App\Http\Controllers\Admin\InstructorsController::class, 'storeCorrespondence'])->name('instructors.correspondences.store');
    Route::delete('/instructors/correspondences/{instructorCorrespondence}', [App\Http\Controllers\Admin\InstructorsController::class, 'deleteCorrespondence'])->name('instructors.correspondences.destroy');

    // Gift Vouchers management
    Route::get('/gift-vouchers', [App\Http\Controllers\Admin\GiftVouchersController::class, 'index'])->name('gift-vouchers.index');
    Route::get('/gift-vouchers/create', [App\Http\Controllers\Admin\GiftVouchersController::class, 'create'])->name('gift-vouchers.create');
    Route::post('/gift-vouchers', [App\Http\Controllers\Admin\GiftVouchersController::class, 'store'])->name('gift-vouchers.store');
    Route::patch('/gift-vouchers/{giftVoucher}/cancel', [App\Http\Controllers\Admin\GiftVouchersController::class, 'cancel'])->name('gift-vouchers.cancel');

    // Bookings management
    Route::get('/bookings', [App\Http\Controllers\Admin\BookingsController::class, 'index'])->name('bookings.index');
    Route::patch('/bookings/{booking}/update-status', [App\Http\Controllers\Admin\BookingsController::class, 'updateStatus'])->name('bookings.update-status');

    // Blog management
    Route::get('/blog', [App\Http\Controllers\Admin\BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/create', [App\Http\Controllers\Admin\BlogController::class, 'create'])->name('blog.create');
    Route::post('/blog', [App\Http\Controllers\Admin\BlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{blogPost}/edit', [App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{blogPost}', [App\Http\Controllers\Admin\BlogController::class, 'update'])->name('blog.update');
    Route::get('/blog/categories', [App\Http\Controllers\Admin\BlogController::class, 'categories'])->name('blog.categories');

    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/seed', [App\Http\Controllers\Admin\SettingsController::class, 'seed'])->name('settings.seed');
});
Route::put('/user/profile', function (\Illuminate\Http\Request $request) {
    $user = $request->user();
    $isPersonalDetails = $request->has('first_name') || $request->has('postcode') || $request->has('current_password');
    if ($isPersonalDetails) {
        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'preferred_first_name' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female,other,prefer_not_to_say',
            'phone' => 'required|string|max:20',
            'postcode' => 'required|string|max:10',
            'current_password' => 'required|string|current_password',
            'new_password' => 'nullable|string|min:8|confirmed',
        ];
        $validated = $request->validate($rules);
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->preferred_first_name = $validated['preferred_first_name'] ?? null;
        $user->gender = $validated['gender'];
        $user->phone = $validated['phone'];
        $user->postcode = $validated['postcode'];
        $user->name = trim($validated['first_name'] . ' ' . $validated['last_name']);
        if (! empty($validated['new_password'])) {
            $user->password = $validated['new_password'];
        }
        $user->save();
        return response()->json(['message' => 'Saved.']);
    }
    $request->validate(['name' => 'required|string|max:255', 'phone' => 'nullable|string|max:20']);
    $user->update($request->only('name', 'phone'));
    return response()->json(['message' => 'Updated']);
})->name('user.profile.update')->middleware('auth');

Route::middleware(['auth', 'role:learner'])->prefix('learner')->name('learner.')->group(function () {
    Route::get('/dashboard', fn () => view('learner.pages.dashboard'))->name('dashboard');
    Route::get('/wallet', fn () => view('learner.pages.wallet'))->name('wallet');
    Route::get('/wallet/add-credit', fn () => view('learner.pages.wallet-add-credit'))->name('wallet.add-credit');
    Route::get('/bookings/new', [App\Http\Controllers\Learner\BookingController::class, 'create'])->name('bookings.new');
    Route::post('/bookings/continue', [App\Http\Controllers\Learner\BookingController::class, 'continueToPayment'])->name('bookings.continue');
    Route::get('/bookings/payment', [App\Http\Controllers\Learner\BookingController::class, 'payment'])->name('bookings.payment');
});

Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', fn () => view('instructor.pages.dashboard'))->name('dashboard');
    Route::get('/calendar', fn () => view('instructor.pages.calendar'))->name('calendar');
    Route::get('/learners', fn () => view('instructor.pages.learners'))->name('learners');
    Route::get('/reports', fn () => view('instructor.pages.reports'))->name('reports');
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/personal-details', function () {
            $postcodes = \App\Models\Suburb::select('postcode')->distinct()->orderBy('postcode')->pluck('postcode');
            return view('instructor.settings.personal-details', ['postcodes' => $postcodes]);
        })->name('personal-details');
        Route::get('/profile', fn () => view('instructor.settings.profile'))->name('profile');
        Route::get('/vehicle', fn () => view('instructor.settings.vehicle'))->name('vehicle');
        Route::get('/service-area', fn () => view('instructor.settings.service-area'))->name('service-area');
        Route::get('/opening-hours', fn () => view('instructor.settings.opening-hours'))->name('opening-hours');
        Route::get('/calendar-settings', fn () => view('instructor.settings.calendar-settings'))->name('calendar-settings');
        Route::get('/pricing', fn () => view('instructor.settings.pricing'))->name('pricing');
        Route::get('/documents', fn () => view('instructor.settings.documents'))->name('documents');
        Route::get('/banking', fn () => view('instructor.settings.banking'))->name('banking');
        Route::get('/guide', fn () => view('instructor.settings.guide'))->name('guide');
    });
});

// API-style JSON routes (session auth for same-origin JS)
Route::prefix('api')->middleware('web')->group(function () {
    Route::get('suburbs/search', [SuburbController::class, 'search'])->name('api.suburbs.search');
    Route::get('instructors', [InstructorSearchController::class, 'index'])->name('api.instructors.index');
    Route::get('instructors/{instructorProfile}', [InstructorProfileController::class, 'show'])->name('api.instructors.show');
    Route::get('instructors/{instructorProfile}/availability/dates', [AvailabilityController::class, 'dates'])->name('api.availability.dates');
    Route::get('instructors/{instructorProfile}/availability/slots', [AvailabilityController::class, 'slots'])->name('api.availability.slots');

    // Admin blog API routes
    Route::middleware(['auth', 'role:admin'])->prefix('admin/blog')->group(function () {
        Route::get('list', [App\Http\Controllers\Admin\BlogController::class, 'list']);
        Route::delete('{blogPost}', [App\Http\Controllers\Admin\BlogController::class, 'destroy']);
        Route::patch('{blogPost}/toggle-featured', [App\Http\Controllers\Admin\BlogController::class, 'toggleFeatured']);
        Route::get('categories/list', [App\Http\Controllers\Admin\BlogController::class, 'categoryList']);
        Route::post('categories', [App\Http\Controllers\Admin\BlogController::class, 'categoryStore']);
        Route::put('categories/{blogCategory}', [App\Http\Controllers\Admin\BlogController::class, 'categoryUpdate']);
        Route::delete('categories/{blogCategory}', [App\Http\Controllers\Admin\BlogController::class, 'categoryDestroy']);
    });

    Route::middleware('auth')->group(function () {
        Route::get('bookings', [BookingController::class, 'index'])->name('api.bookings.index');
        Route::post('bookings', [BookingController::class, 'store'])->name('api.bookings.store');
        Route::get('bookings/{booking}', [BookingController::class, 'show'])->name('api.bookings.show');
        Route::put('bookings/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('api.bookings.reschedule');
        Route::put('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('api.bookings.cancel');
        Route::post('reviews', [ReviewController::class, 'store'])->name('api.reviews.store');
        Route::patch('reviews/{review}/google-prompted', [ReviewController::class, 'markGooglePrompted'])->name('api.reviews.google-prompted');
        Route::put('bookings/{booking}/complete', [BookingController::class, 'complete'])->name('api.bookings.complete');

        // Gift Vouchers API
        Route::post('gift-vouchers/purchase', [App\Http\Controllers\GiftVoucherController::class, 'purchase'])->name('api.gift-vouchers.purchase');
        Route::post('gift-vouchers/{giftVoucher}/confirm-payment', [App\Http\Controllers\GiftVoucherController::class, 'confirmPayment'])->name('api.gift-vouchers.confirm-payment');
        Route::post('gift-vouchers/redeem', [App\Http\Controllers\GiftVoucherController::class, 'redeem'])->name('api.gift-vouchers.redeem');
        Route::post('gift-vouchers/check', [App\Http\Controllers\GiftVoucherController::class, 'check'])->name('api.gift-vouchers.check');

        // Calendar sync
        Route::get('calendar/subscribe-urls', [App\Http\Controllers\CalendarFeedController::class, 'generateToken'])->name('api.calendar.subscribe');
        Route::post('calendar/regenerate-token', [App\Http\Controllers\CalendarFeedController::class, 'regenerateToken'])->name('api.calendar.regenerate');
        Route::get('bookings/{booking}/download-ics', [App\Http\Controllers\CalendarFeedController::class, 'downloadBooking'])->name('api.bookings.download-ics');

        Route::middleware('role:learner')->prefix('learner')->name('api.learner.')->group(function () {
            Route::get('dashboard', [App\Http\Controllers\Learner\DashboardController::class, 'index'])->name('dashboard');
            Route::get('wallet', [App\Http\Controllers\Learner\WalletController::class, 'show'])->name('wallet.show');
            Route::get('wallet/transactions', [App\Http\Controllers\Learner\WalletController::class, 'transactions'])->name('wallet.transactions');
            Route::post('wallet/add-credit', [App\Http\Controllers\Learner\WalletController::class, 'addCredit'])->name('wallet.add-credit');
            Route::post('bookings/pay', [App\Http\Controllers\Learner\BookingController::class, 'processPayment'])->name('bookings.pay');
        });

        Route::middleware('role:instructor')->prefix('instructor')->name('api.instructor.')->group(function () {
            Route::get('profile', [InstructorDashboard::class, 'profile'])->name('profile');
            Route::put('profile', [InstructorDashboard::class, 'updateProfile'])->name('profile.update');
            Route::post('profile/photo', [InstructorDashboard::class, 'uploadProfilePhoto'])->name('profile.photo');
            Route::post('profile/vehicle-photo', [InstructorDashboard::class, 'uploadVehiclePhoto'])->name('profile.vehicle-photo');
            Route::get('learners', [App\Http\Controllers\Instructor\LearnersController::class, 'index'])->name('learners');
            Route::get('learners/{user}', [App\Http\Controllers\Instructor\LearnersController::class, 'show'])->name('learners.show');
            Route::post('learners/invite', [App\Http\Controllers\Instructor\LearnersController::class, 'invite'])->name('learners.invite');
            Route::post('booking-proposals', [App\Http\Controllers\Instructor\BookingProposalController::class, 'store'])->name('booking-proposals.store');
            Route::put('profile/service-areas', [InstructorDashboard::class, 'updateServiceAreas'])->name('profile.service-areas');
            Route::put('profile/availability', [InstructorDashboard::class, 'updateAvailability'])->name('profile.availability');
            Route::put('profile/calendar-settings', [InstructorDashboard::class, 'updateCalendarSettings'])->name('profile.calendar-settings');
            Route::get('documents', [App\Http\Controllers\Instructor\DocumentsController::class, 'index'])->name('documents.index');
            Route::post('documents', [App\Http\Controllers\Instructor\DocumentsController::class, 'store'])->name('documents.store');
            Route::put('profile/banking', [InstructorDashboard::class, 'updateBanking'])->name('profile.banking');
            Route::get('reports', [App\Http\Controllers\Instructor\ReportsController::class, 'index'])->name('reports.index');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Service Provider Marketplace (Plumber, Electrician, etc.)
|--------------------------------------------------------------------------
*/

// Public browse
Route::get('/become-a-provider', [App\Http\Controllers\ServiceController::class, 'becomeProvider'])->name('services.become-provider');
Route::get('/services', [App\Http\Controllers\ServiceController::class, 'categories'])->name('services.categories');
Route::get('/services/{slug}', [App\Http\Controllers\ServiceController::class, 'browse'])->name('services.browse');
Route::get('/services/{slug}/{provider}', [App\Http\Controllers\ServiceController::class, 'show'])->name('services.show');

// Customer bookings (auth required)
Route::middleware(['auth'])->group(function () {
    Route::get('/services/{provider}/book', [App\Http\Controllers\ServiceBookingController::class, 'create'])->name('service-bookings.create');
    Route::post('/services/{provider}/book', [App\Http\Controllers\ServiceBookingController::class, 'store'])->name('service-bookings.store');
    Route::get('/my-service-bookings', [App\Http\Controllers\ServiceBookingController::class, 'index'])->name('service-bookings.index');
    Route::get('/service-bookings/{serviceBooking}', [App\Http\Controllers\ServiceBookingController::class, 'show'])->name('service-bookings.show');
});

// Service provider dashboard
Route::middleware(['auth'])->prefix('service-provider')->name('service-provider.')->group(function () {
    Route::get('/onboarding', [App\Http\Controllers\ServiceProvider\DashboardController::class, 'onboardingCreate'])->name('onboarding.create');
    Route::post('/onboarding', [App\Http\Controllers\ServiceProvider\DashboardController::class, 'onboardingStore'])->name('onboarding.store');
    Route::get('/dashboard', [App\Http\Controllers\ServiceProvider\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/availability', [App\Http\Controllers\ServiceProvider\AvailabilityController::class, 'index'])->name('availability.index');
    Route::post('/availability/slots', [App\Http\Controllers\ServiceProvider\AvailabilityController::class, 'storeSlot'])->name('availability.slots.store');
    Route::delete('/availability/slots/{slot}', [App\Http\Controllers\ServiceProvider\AvailabilityController::class, 'destroySlot'])->name('availability.slots.destroy');
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('service-categories', App\Http\Controllers\Admin\ServiceCategoryController::class)->except(['show']);
    Route::get('service-providers', [App\Http\Controllers\Admin\ServiceProviderController::class, 'index'])->name('service-providers.index');
    Route::get('service-providers/create', [App\Http\Controllers\Admin\ServiceProviderController::class, 'create'])->name('service-providers.create');
    Route::post('service-providers', [App\Http\Controllers\Admin\ServiceProviderController::class, 'store'])->name('service-providers.store');
    Route::get('service-providers/{serviceProvider}', [App\Http\Controllers\Admin\ServiceProviderController::class, 'show'])->name('service-providers.show');
    Route::post('service-providers/{serviceProvider}/approve', [App\Http\Controllers\Admin\ServiceProviderController::class, 'approve'])->name('service-providers.approve');
    Route::post('service-providers/{serviceProvider}/reject', [App\Http\Controllers\Admin\ServiceProviderController::class, 'reject'])->name('service-providers.reject');
});
