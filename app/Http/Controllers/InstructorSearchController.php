<?php

namespace App\Http\Controllers;

use App\Models\InstructorProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorSearchController extends Controller
{
    /**
     * Search instructors by suburb/postcode, transmission, test pre-booked.
     * Same logic as Secure Licences: find instructors who service the area.
     */
    public function index(Request $request): JsonResponse
    {
        $suburbId = $request->input('suburb_id');
        $transmission = $request->input('transmission'); // auto, manual, or empty for both
        $testPreBooked = $request->boolean('test_pre_booked');
        // Gender filter: 'female' / 'male' / null (any). Used by learners (often female learners) for safety.
        $instructorGender = $request->input('instructor_gender');

        $query = InstructorProfile::with(['user', 'serviceAreas.state'])
            ->where('is_active', true);

        if ($suburbId) {
            $query->whereHas('serviceAreas', fn ($q) => $q->where('suburbs.id', $suburbId));
        }

        if (in_array($transmission, ['auto', 'manual'], true)) {
            $query->where(function ($q) use ($transmission) {
                $q->where('transmission', $transmission)->orWhere('transmission', 'both');
            });
        }

        if ($testPreBooked) {
            $query->where('offers_test_package', true);
        }

        // Filter by instructor gender (case-insensitive match on user.gender)
        if (in_array(strtolower((string) $instructorGender), ['female', 'male'], true)) {
            $query->whereHas('user', function ($q) use ($instructorGender) {
                $q->whereRaw('LOWER(gender) = ?', [strtolower($instructorGender)]);
            });
        }

        $instructors = $query->withCount('reviews')
            ->get()
            ->map(function (InstructorProfile $p) {
                return [
                    'id' => $p->id,
                    'user_id' => $p->user_id,
                    'name' => $p->user->name,
                    'gender' => $p->user->gender ? strtolower($p->user->gender) : null,
                    'bio' => $p->bio,
                    'transmission' => $p->transmission,
                    'vehicle' => $this->formatVehicle($p),
                    'lesson_price' => (float) $p->lesson_price,
                    'test_package_price' => $p->test_package_price ? (float) $p->test_package_price : null,
                    'offers_test_package' => $p->offers_test_package,
                    'average_rating' => round($p->averageRating(), 1),
                    'reviews_count' => $p->reviewsCount(),
                    'service_areas' => $p->serviceAreas->map(fn ($s) => [
                        'id' => $s->id,
                        'name' => $s->name,
                        'postcode' => $s->postcode,
                        'state' => $s->state?->code,
                    ]),
                ];
            });

        return response()->json(['data' => $instructors]);
    }

    private function formatVehicle(InstructorProfile $p): string
    {
        $parts = array_filter([
            $p->vehicle_make,
            $p->vehicle_model,
            $p->vehicle_year,
            $p->vehicle_safety_rating ? 'Safety: ' . $p->vehicle_safety_rating : null,
        ]);

        return implode(' ', $parts) ?: 'Not specified';
    }
}
