<?php

namespace App\Http\Controllers;

use App\Models\Suburb;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuburbController extends Controller
{
    /**
     * Autocomplete suburbs by name or postcode (for search box).
     * When searching by postcode, uses radius-based lookup to catch
     * all suburbs in the same geographic area (Geonames data can have
     * slightly different postcodes for nearby suburbs).
     */
    public function search(Request $request): JsonResponse
    {
        $q = $request->input('q', '');
        $q = trim($q);
        if (strlen($q) < 2) {
            return response()->json(['data' => []]);
        }

        $isPostcodeSearch = preg_match('/^\d{3,4}$/', $q);

        if ($isPostcodeSearch) {
            $suburbs = $this->searchByPostcode($q);
        } else {
            $suburbs = $this->searchByName($q);
        }

        $result = $suburbs->map(fn (Suburb $s) => [
            'id' => $s->id,
            'name' => $s->name,
            'postcode' => $s->postcode,
            'state' => $s->state?->code,
            'label' => $s->name . ', ' . $s->postcode . ' ' . ($s->state?->code ?? ''),
        ]);

        return response()->json(['data' => $result]);
    }

    /**
     * Search by postcode — find exact matches PLUS nearby suburbs
     * within 5km radius using lat/lng coordinates.
     * This ensures all suburbs sharing a postcode area show up
     * even if Geonames assigned them a slightly different postcode.
     */
    private function searchByPostcode(string $postcode)
    {
        // Get exact postcode matches
        $exactMatches = Suburb::with('state')
            ->where('postcode', $postcode)
            ->orderBy('name')
            ->get();

        // Find the center point of the postcode area
        $center = $exactMatches->first();

        if ($center && $center->latitude && $center->longitude) {
            // Also find nearby suburbs within 5km radius
            $nearbySuburbs = Suburb::with('state')
                ->withinRadius($center->latitude, $center->longitude, 5)
                ->whereNotIn('id', $exactMatches->pluck('id')->all())
                ->orderBy('name')
                ->get();

            // Merge: exact matches first, then nearby sorted by name
            $suburbs = $exactMatches->merge($nearbySuburbs);
        } else {
            // Fallback: also try adjacent postcodes (±2)
            $pcNum = intval($postcode);
            $adjacentCodes = range($pcNum - 2, $pcNum + 2);
            $adjacentCodes = array_map(fn ($p) => str_pad($p, 4, '0', STR_PAD_LEFT), $adjacentCodes);

            $suburbs = Suburb::with('state')
                ->whereIn('postcode', $adjacentCodes)
                ->orderByRaw("postcode = ? DESC", [$postcode])
                ->orderBy('name')
                ->limit(30)
                ->get();
        }

        return $suburbs->unique('id')->values();
    }

    /**
     * Search by suburb name — prefix match plus cross-state lookup.
     */
    private function searchByName(string $q)
    {
        $suburbs = Suburb::with('state')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', $q . '%')
                      ->orWhere('postcode', 'like', $q . '%');
            })
            ->orderBy('name')
            ->orderBy('postcode')
            ->limit(20)
            ->get();

        // Also include same-name suburbs in other states
        $matchedNames = $suburbs->pluck('name')->unique()->values();
        if ($matchedNames->isNotEmpty()) {
            $existingIds = $suburbs->pluck('id')->all();
            $crossStateSuburbs = Suburb::with('state')
                ->whereIn('name', $matchedNames)
                ->whereNotIn('id', $existingIds)
                ->orderBy('name')
                ->orderBy('postcode')
                ->limit(10)
                ->get();

            $suburbs = $suburbs->merge($crossStateSuburbs)
                ->sortBy('name')
                ->values();
        }

        return $suburbs;
    }

    /**
     * Get all suburbs for an exact postcode + nearby suburbs.
     * Used by the find-instructor page for postcode-based search.
     */
    public function byPostcode(Request $request, string $postcode): JsonResponse
    {
        if (!preg_match('/^\d{4}$/', $postcode)) {
            return response()->json(['data' => [], 'message' => 'Enter a valid 4-digit postcode'], 422);
        }

        $suburbs = $this->searchByPostcode($postcode);

        // Group: exact matches first, then nearby
        $exact = $suburbs->where('postcode', $postcode)->values();
        $nearby = $suburbs->where('postcode', '!=', $postcode)->values();

        $format = fn (Suburb $s) => [
            'id' => $s->id,
            'name' => $s->name,
            'postcode' => $s->postcode,
            'state' => $s->state?->code,
            'label' => $s->name . ', ' . $s->postcode . ' ' . ($s->state?->code ?? ''),
            'is_exact' => $s->postcode === $postcode,
        ];

        return response()->json([
            'data' => [
                'exact' => $exact->map($format)->values(),
                'nearby' => $nearby->map($format)->values(),
            ],
            'postcode' => $postcode,
            'total' => $suburbs->count(),
        ]);
    }
}
