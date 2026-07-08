<?php

namespace App\Http\Controllers;

use App\Models\Suburb;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SuburbController extends Controller
{
    /**
     * Full street-address autocomplete for booking pickup locations.
     *
     * Proxies OpenStreetMap Nominatim **server-side** (the browser can't set a
     * User-Agent, so direct browser calls get blocked/CORS-failed — this is why
     * the address box was stuck on "Searching addresses…"). Results are cached
     * and returned in Nominatim's native shape, which the front-end already parses.
     */
    public function addressSearch(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 3) {
            return response()->json([]);
        }

        $cacheKey = 'addr_search:' . md5(strtolower($q));

        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        $results = $this->fetchAddressResults($q);

        Cache::put(
            $cacheKey,
            $results,
            empty($results) ? now()->addMinutes(10) : now()->addDay()
        );

        return response()->json($results);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchAddressResults(string $q): array
    {
        try {
            // Photon (komoot) is an autocomplete-optimised geocoder that — unlike
            // Nominatim — does not block datacenter/cloud IPs, so it works from the
            // production server. bbox biases results to Australia.
            $resp = Http::withHeaders([
                'User-Agent' => 'SecureLicence/1.0 (+https://securelicence.com; support@securelicence.com)',
                'Accept'     => 'application/json',
            ])->timeout(8)->get('https://photon.komoot.io/api/', [
                'q'     => $q,
                'limit' => 8,
                'lang'  => 'en',
                // Australia bounding box: minLon,minLat,maxLon,maxLat
                'bbox'  => '112.9,-43.7,153.7,-10.6',
            ]);

            if (! $resp->successful() || ! is_array($resp->json('features'))) {
                return [];
            }

            return $this->mapPhotonResults($resp->json('features'));
        } catch (\Throwable $e) {
            Log::warning('Address search (Photon) failed: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Convert Photon GeoJSON features into the Nominatim-compatible shape the
     * booking page's autocomplete already consumes (address.{house_number,road,
     * suburb,...}, display_name, lat, lon) — so no frontend change is needed.
     * Non-Australian results are dropped.
     *
     * @param  array<int, array<string, mixed>>  $features
     * @return array<int, array<string, mixed>>
     */
    private function mapPhotonResults(array $features): array
    {
        $stateCodes = [
            'new south wales' => 'NSW', 'victoria' => 'VIC', 'queensland' => 'QLD',
            'south australia' => 'SA', 'western australia' => 'WA', 'tasmania' => 'TAS',
            'northern territory' => 'NT', 'australian capital territory' => 'ACT',
        ];

        $out = [];
        foreach ($features as $f) {
            $p = $f['properties'] ?? [];
            if (strtoupper((string) ($p['countrycode'] ?? '')) !== 'AU') {
                continue; // keep Australian addresses only
            }

            $coords = $f['geometry']['coordinates'] ?? [null, null];
            $suburb = $p['district'] ?? ($p['city'] ?? ($p['locality'] ?? null));
            $stateName = (string) ($p['state'] ?? '');
            $stateCode = $stateCodes[strtolower($stateName)] ?? null;

            $street = trim(((string) ($p['housenumber'] ?? '')) . ' ' . ((string) ($p['street'] ?? $p['name'] ?? '')));
            $displayName = implode(', ', array_filter([
                $street !== '' ? $street : ($p['name'] ?? null),
                $suburb,
                $stateName,
                $p['postcode'] ?? null,
            ]));

            $out[] = [
                'display_name' => $displayName,
                'lat' => isset($coords[1]) ? (string) $coords[1] : null,
                'lon' => isset($coords[0]) ? (string) $coords[0] : null,
                'address' => array_filter([
                    'house_number'     => $p['housenumber'] ?? null,
                    'road'             => $p['street'] ?? null,
                    'suburb'           => $suburb,
                    'city'             => $p['city'] ?? null,
                    'state'            => $stateName ?: null,
                    'postcode'         => $p['postcode'] ?? null,
                    'ISO3166-2-lvl4'   => $stateCode ? 'AU-' . $stateCode : null,
                ], fn ($v) => $v !== null && $v !== ''),
            ];
        }

        return $out;
    }

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
