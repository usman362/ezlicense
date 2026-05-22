<?php

namespace App\Http\Controllers;

use App\Models\InstructorProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorSearchController extends Controller
{
    /**
     * Search instructors by suburb/postcode, transmission, test pre-booked.
     * Same logic as Secure Licence: find instructors who service the area.
     */
    public function index(Request $request): JsonResponse
    {
        $suburbId = $request->input('suburb_id');
        $transmission = $request->input('transmission'); // auto, manual, or empty for both
        $testPreBooked = $request->boolean('test_pre_booked');

        $query = InstructorProfile::with(['user', 'serviceAreas.state'])
            ->where('is_active', true);

        // ── Female-only safety filter ──
        // Hide female-only instructors from male/other learners.
        // Visible to: female users, guests (gender unknown), admins.
        $viewer = $request->user();
        $viewerGender = $viewer ? strtolower((string) ($viewer->gender ?? '')) : null;
        $shouldHideFemaleOnly = $viewer
            && ! ($viewer->isAdmin() ?? false)
            && $viewerGender !== 'female'
            && $viewerGender !== null
            && $viewerGender !== '';
        if ($shouldHideFemaleOnly) {
            $query->where('accepts_female_learners_only', false);
        }

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

        $instructors = $query->withCount(['reviews', 'bookings as completed_lessons_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->get()
            ->map(function (InstructorProfile $p) {
                // Months instructing — based on user registration date
                $instructingMonths = $p->user->created_at
                    ? (int) $p->user->created_at->diffInMonths(now())
                    : null;

                // Photo URLs (DigitalOcean Spaces)
                $profilePhotoUrl = $p->profile_photo ? \Storage::disk('spaces')->url($p->profile_photo) : null;
                $vehiclePhotoUrl = $p->vehicle_photo ? \Storage::disk('spaces')->url($p->vehicle_photo) : null;

                return [
                    'id' => $p->id,
                    'user_id' => $p->user_id,
                    'name' => $p->user->name,
                    'first_name' => $p->user->first_name ?? explode(' ', $p->user->name)[0],
                    'gender' => $p->user->gender ? strtolower($p->user->gender) : null,
                    'female_only' => $p->isFemaleOnly(),
                    'bio' => $p->bio,
                    'transmission' => $p->transmission,
                    'vehicle' => $this->formatVehicle($p),
                    'vehicle_make' => $p->vehicle_make,
                    'vehicle_model' => $p->vehicle_model,
                    'vehicle_year' => $p->vehicle_year,
                    'lesson_price' => (float) $p->lesson_price,
                    'test_package_price' => $p->test_package_price ? (float) $p->test_package_price : null,
                    'offers_test_package' => $p->offers_test_package,
                    'average_rating' => round($p->averageRating(), 1),
                    'reviews_count' => $p->reviewsCount(),
                    'completed_lessons_count' => (int) $p->completed_lessons_count,
                    'instructing_months' => $instructingMonths,
                    'is_verified' => $p->verification_status === 'verified',
                    'profile_photo_url' => $profilePhotoUrl,
                    'vehicle_photo_url' => $vehiclePhotoUrl,
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
