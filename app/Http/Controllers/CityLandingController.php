<?php

namespace App\Http\Controllers;

use App\Models\InstructorProfile;
use App\Models\Suburb;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * City-specific landing pages (e.g. /driving-lessons/sydney).
 * Each major Australian city has its own SEO-optimised page with stats,
 * top instructors, suburb list, FAQs, etc.
 */
class CityLandingController extends Controller
{
    /**
     * Whitelisted cities + their state codes and metadata.
     * Add a new city here to make it available — no other code changes needed.
     */
    private const CITIES = [
        'sydney'    => ['name' => 'Sydney',    'state' => 'NSW', 'state_full' => 'New South Wales',              'learners' => 27000,  'lessons' => 135000, 'price' => 85.00, 'authority' => 'Service NSW'],
        'melbourne' => ['name' => 'Melbourne', 'state' => 'VIC', 'state_full' => 'Victoria',                     'learners' => 32000,  'lessons' => 158000, 'price' => 75.00, 'authority' => 'VicRoads'],
        'brisbane'  => ['name' => 'Brisbane',  'state' => 'QLD', 'state_full' => 'Queensland',                   'learners' => 18000,  'lessons' => 90000,  'price' => 75.00, 'authority' => 'TMR Queensland'],
        'perth'     => ['name' => 'Perth',     'state' => 'WA',  'state_full' => 'Western Australia',            'learners' => 12000,  'lessons' => 62000,  'price' => 70.00, 'authority' => 'Transport WA'],
        'adelaide'  => ['name' => 'Adelaide',  'state' => 'SA',  'state_full' => 'South Australia',              'learners' => 9000,   'lessons' => 45000,  'price' => 70.00, 'authority' => 'Service SA'],
        'hobart'    => ['name' => 'Hobart',    'state' => 'TAS', 'state_full' => 'Tasmania',                     'learners' => 3500,   'lessons' => 18000,  'price' => 70.00, 'authority' => 'Transport Tasmania'],
        'canberra'  => ['name' => 'Canberra',  'state' => 'ACT', 'state_full' => 'Australian Capital Territory', 'learners' => 4500,   'lessons' => 22000,  'price' => 75.00, 'authority' => 'Access Canberra'],
    ];

    public function show(string $citySlug): View
    {
        $citySlug = strtolower($citySlug);
        if (! isset(self::CITIES[$citySlug])) {
            throw new NotFoundHttpException("City '{$citySlug}' not supported.");
        }

        $city = self::CITIES[$citySlug];

        // Top instructors covering this city (by service area or name match)
        $topInstructors = InstructorProfile::with(['user', 'serviceAreas.state'])
            ->where('is_active', true)
            ->whereHas('serviceAreas', function ($q) use ($city) {
                $q->where('suburbs.name', 'like', '%' . $city['name'] . '%');
            })
            ->withCount(['reviews', 'bookings as completed_lessons_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->take(8)
            ->get();

        // Suburb list around the city — first 40 suburbs from this state for quick links
        $suburbs = Suburb::with('state')
            ->whereHas('state', fn ($q) => $q->where('code', $city['state']))
            ->orderBy('name')
            ->take(48)
            ->get();

        return view('frontend.pages.city-landing', [
            'citySlug' => $citySlug,
            'city' => $city,
            'topInstructors' => $topInstructors,
            'suburbs' => $suburbs,
        ]);
    }
}
