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
    $suburbId = $request->query('suburb_id');
    if (empty($suburbId)) {
        return redirect()->route('find-instructor');
    }
    return view('find-instructor-results', [
        'suburb_id' => $suburbId,
        'q' => $request->query('q', ''),
        'transmission' => $request->query('transmission', ''),
        'test_pre_booked' => $request->boolean('test_pre_booked'),
    ]);
})->name('find-instructor.results');

Route::get('/instructors/{instructorProfile}', function (App\Models\InstructorProfile $instructorProfile) {
    $instructorProfile->load(['user', 'serviceAreas']);
    return view('instructor-public-show', ['instructorProfile' => $instructorProfile]);
})->name('instructors.show');

Auth::routes();

// Dedicated login URLs for top bar (split-screen UI)
Route::get('/learner/login', function () {
    return view('auth.learner-login');
})->name('learner.login')->middleware('guest');
Route::get('/instructor/login', function () {
    return view('auth.instructor-login');
})->name('instructor.login')->middleware('guest');

Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

// Admin panel: /login is for admin; after login admins go to /admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [App\Http\Controllers\Admin\UsersController::class, 'index'])->name('users.index');
    Route::get('/instructors', [App\Http\Controllers\Admin\InstructorsController::class, 'index'])->name('instructors.index');
    Route::get('/bookings', [App\Http\Controllers\Admin\BookingsController::class, 'index'])->name('bookings.index');
    Route::get('/settings', fn () => view('admin.settings'))->name('settings');
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
    });
});

// API-style JSON routes (session auth for same-origin JS)
Route::prefix('api')->middleware('web')->group(function () {
    Route::get('suburbs/search', [SuburbController::class, 'search'])->name('api.suburbs.search');
    Route::get('instructors', [InstructorSearchController::class, 'index'])->name('api.instructors.index');
    Route::get('instructors/{instructorProfile}', [InstructorProfileController::class, 'show'])->name('api.instructors.show');
    Route::get('instructors/{instructorProfile}/availability/dates', [AvailabilityController::class, 'dates'])->name('api.availability.dates');
    Route::get('instructors/{instructorProfile}/availability/slots', [AvailabilityController::class, 'slots'])->name('api.availability.slots');

    Route::middleware('auth')->group(function () {
        Route::get('bookings', [BookingController::class, 'index'])->name('api.bookings.index');
        Route::post('bookings', [BookingController::class, 'store'])->name('api.bookings.store');
        Route::get('bookings/{booking}', [BookingController::class, 'show'])->name('api.bookings.show');
        Route::put('bookings/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('api.bookings.reschedule');
        Route::put('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('api.bookings.cancel');
        Route::post('reviews', [ReviewController::class, 'store'])->name('api.reviews.store');

        Route::middleware('role:learner')->prefix('learner')->name('api.learner.')->group(function () {
            Route::get('dashboard', [App\Http\Controllers\Learner\DashboardController::class, 'index'])->name('dashboard');
            Route::get('wallet', [App\Http\Controllers\Learner\WalletController::class, 'show'])->name('wallet.show');
            Route::get('wallet/transactions', [App\Http\Controllers\Learner\WalletController::class, 'transactions'])->name('wallet.transactions');
        });

        Route::middleware('role:instructor')->prefix('instructor')->name('api.instructor.')->group(function () {
            Route::get('profile', [InstructorDashboard::class, 'profile'])->name('profile');
            Route::put('profile', [InstructorDashboard::class, 'updateProfile'])->name('profile.update');
            Route::get('learners', [App\Http\Controllers\Instructor\LearnersController::class, 'index'])->name('learners');
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
