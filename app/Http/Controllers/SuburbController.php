<?php

namespace App\Http\Controllers;

use App\Models\Suburb;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuburbController extends Controller
{
    /**
     * Autocomplete suburbs by name or postcode (for search box).
     */
    public function search(Request $request): JsonResponse
    {
        $q = $request->input('q', '');
        $q = trim($q);
        if (strlen($q) < 2) {
            return response()->json(['data' => []]);
        }

        // First, find suburbs matching by name or postcode
        $suburbs = Suburb::with('state')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', $q . '%')
                      ->orWhere('postcode', 'like', $q . '%');
            })
            ->orderBy('name')
            ->orderBy('postcode')
            ->limit(20)
            ->get();

        // If we matched by name, also include same-name suburbs in other states
        // so the instructor can see e.g. "Auburn NSW", "Auburn VIC", "Auburn SA"
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

        $result = $suburbs->map(fn (Suburb $s) => [
            'id' => $s->id,
            'name' => $s->name,
            'postcode' => $s->postcode,
            'state' => $s->state?->code,
            'label' => $s->name . ', ' . $s->postcode . ' ' . ($s->state?->code ?? ''),
        ]);

        return response()->json(['data' => $result]);
    }
}
