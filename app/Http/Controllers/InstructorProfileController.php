<?php

namespace App\Http\Controllers;

use App\Models\InstructorProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorProfileController extends Controller
{
    /**
     * Public show for learner view.
     */
    public function show(InstructorProfile $instructorProfile): JsonResponse
    {
        $instructorProfile->load(['user', 'serviceAreas.state', 'availabilitySlots']);
        $instructorProfile->loadCount('reviews');

        $reviews = $instructorProfile->reviews()
            ->with('learner:id,name')
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'data' => [
                'id' => $instructorProfile->id,
                'user_id' => $instructorProfile->user_id,
                'name' => $instructorProfile->user->name,
                'bio' => $instructorProfile->bio,
                'transmission' => $instructorProfile->transmission,
                'vehicle_make' => $instructorProfile->vehicle_make,
                'vehicle_model' => $instructorProfile->vehicle_model,
                'vehicle_year' => $instructorProfile->vehicle_year,
                'vehicle_safety_rating' => $instructorProfile->vehicle_safety_rating,
                'wwcc_verified_at' => $instructorProfile->wwcc_verified_at?->toIso8601String(),
                'lesson_price' => (float) $instructorProfile->lesson_price,
                'test_package_price' => $instructorProfile->test_package_price ? (float) $instructorProfile->test_package_price : null,
                'lesson_duration_minutes' => $instructorProfile->lesson_duration_minutes,
                'offers_test_package' => $instructorProfile->offers_test_package,
                'average_rating' => round($instructorProfile->averageRating(), 1),
                'reviews_count' => $instructorProfile->reviews_count,
                'service_areas' => $instructorProfile->serviceAreas->map(fn ($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'postcode' => $s->postcode,
                    'state' => $s->state?->code,
                ]),
                'availability_slots' => $instructorProfile->availabilitySlots->map(fn ($s) => [
                    'day_of_week' => $s->day_of_week,
                    'start_time' => $s->start_time,
                    'end_time' => $s->end_time,
                ]),
                'reviews' => $reviews->map(fn ($r) => [
                    'id' => $r->id,
                    'rating' => $r->rating,
                    'comment' => $r->comment,
                    'learner_name' => $r->learner->name ?? 'Anonymous',
                    'created_at' => $r->created_at->toIso8601String(),
                ]),
            ],
        ]);
    }
}
