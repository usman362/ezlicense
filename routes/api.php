<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\InstructorProfileController;
use App\Http\Controllers\InstructorSearchController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboard;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SuburbController;
use Illuminate\Support\Facades\Route;

// Public (no auth)
Route::get('suburbs/search', [SuburbController::class, 'search'])->name('api.suburbs.search');
Route::get('instructors', [InstructorSearchController::class, 'index'])->name('api.instructors.index');
Route::get('instructors/{instructorProfile}', [InstructorProfileController::class, 'show'])->name('api.instructors.show');
Route::get('instructors/{instructorProfile}/availability/dates', [AvailabilityController::class, 'dates'])->name('api.availability.dates');
Route::get('instructors/{instructorProfile}/availability/slots', [AvailabilityController::class, 'slots'])->name('api.availability.slots');

// Auth required
Route::middleware('auth')->group(function () {
    Route::get('bookings', [BookingController::class, 'index'])->name('api.bookings.index');
    Route::post('bookings', [BookingController::class, 'store'])->name('api.bookings.store');
    Route::get('bookings/{booking}', [BookingController::class, 'show'])->name('api.bookings.show');
    Route::put('bookings/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('api.bookings.reschedule');
    Route::put('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('api.bookings.cancel');
    Route::get('bookings/{booking}/modification-context', [BookingController::class, 'modificationContext'])->name('api.bookings.modification-context');
    Route::post('reviews', [ReviewController::class, 'store'])->name('api.reviews.store');

    // Instructor dashboard
    Route::middleware('role:instructor')->prefix('instructor')->name('api.instructor.')->group(function () {
        Route::get('profile', [InstructorDashboard::class, 'profile'])->name('profile');
        Route::put('profile', [InstructorDashboard::class, 'updateProfile'])->name('profile.update');
        Route::put('profile/service-areas', [InstructorDashboard::class, 'updateServiceAreas'])->name('profile.service-areas');
        Route::put('profile/availability', [InstructorDashboard::class, 'updateAvailability'])->name('profile.availability');
    });
});
