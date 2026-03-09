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

        $suburbs = Suburb::with('state')
            ->where('name', 'like', $q . '%')
            ->orWhere('postcode', 'like', $q . '%')
            ->orderBy('name')
            ->limit(15)
            ->get()
            ->map(fn (Suburb $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'postcode' => $s->postcode,
                'state' => $s->state?->code,
                'label' => $s->name . ', ' . $s->postcode . ' ' . ($s->state?->code ?? ''),
            ]);

        return response()->json(['data' => $suburbs]);
    }
}
