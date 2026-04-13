<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerVehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    /**
     * List all vehicle makes (for dropdowns).
     */
    public function makes(Request $request): JsonResponse
    {
        $query = VehicleMake::active()->ordered();

        if ($request->boolean('popular_only')) {
            $query->popular();
        }

        $makes = $query->get(['id', 'name', 'slug', 'country', 'origin_type', 'is_popular', 'is_european']);

        return response()->json(['data' => $makes]);
    }

    /**
     * List models for a given make.
     */
    public function models(VehicleMake $vehicleMake): JsonResponse
    {
        $models = $vehicleMake->models()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'body_type']);

        return response()->json(['data' => $models]);
    }

    /**
     * List the authenticated user's vehicles.
     */
    public function index(): JsonResponse
    {
        $vehicles = Auth::user()->vehicles()
            ->with(['make:id,name,slug,is_european', 'model:id,name,body_type'])
            ->orderByDesc('is_primary')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $vehicles->map(fn ($v) => $this->format($v)),
        ]);
    }

    /**
     * Add a new vehicle.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_make_id' => 'required|exists:vehicle_makes,id',
            'vehicle_model_id' => 'nullable|exists:vehicle_models,id',
            'year' => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'colour' => 'nullable|string|max:30',
            'registration' => 'nullable|string|max:20',
            'vin' => 'nullable|string|max:20',
            'transmission' => 'nullable|in:auto,manual',
            'fuel_type' => 'nullable|in:petrol,diesel,electric,hybrid,lpg',
            'photo' => 'nullable|image|max:5120', // 5MB
            'notes' => 'nullable|string|max:500',
            'is_primary' => 'boolean',
        ]);

        $user = Auth::user();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')
                ->store("vehicles/{$user->id}", 'public');
        }

        // If this is primary, un-primary existing ones
        if (!empty($validated['is_primary'])) {
            $user->vehicles()->update(['is_primary' => false]);
        }

        // If this is the user's first vehicle, make it primary
        if ($user->vehicles()->count() === 0) {
            $validated['is_primary'] = true;
        }

        $vehicle = $user->vehicles()->create($validated);
        $vehicle->load(['make:id,name,slug,is_european', 'model:id,name,body_type']);

        return response()->json([
            'data' => $this->format($vehicle),
            'message' => 'Vehicle added successfully.',
        ], 201);
    }

    /**
     * Update a vehicle.
     */
    public function update(Request $request, CustomerVehicle $vehicle): JsonResponse
    {
        if ($vehicle->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'vehicle_make_id' => 'sometimes|exists:vehicle_makes,id',
            'vehicle_model_id' => 'nullable|exists:vehicle_models,id',
            'year' => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'colour' => 'nullable|string|max:30',
            'registration' => 'nullable|string|max:20',
            'vin' => 'nullable|string|max:20',
            'transmission' => 'nullable|in:auto,manual',
            'fuel_type' => 'nullable|in:petrol,diesel,electric,hybrid,lpg',
            'photo' => 'nullable|image|max:5120',
            'notes' => 'nullable|string|max:500',
            'is_primary' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($vehicle->photo) {
                Storage::disk('public')->delete($vehicle->photo);
            }
            $validated['photo'] = $request->file('photo')
                ->store("vehicles/{$vehicle->user_id}", 'public');
        }

        if (!empty($validated['is_primary'])) {
            Auth::user()->vehicles()->where('id', '!=', $vehicle->id)->update(['is_primary' => false]);
        }

        $vehicle->update($validated);
        $vehicle->load(['make:id,name,slug,is_european', 'model:id,name,body_type']);

        return response()->json([
            'data' => $this->format($vehicle),
            'message' => 'Vehicle updated.',
        ]);
    }

    /**
     * Delete a vehicle.
     */
    public function destroy(CustomerVehicle $vehicle): JsonResponse
    {
        if ($vehicle->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($vehicle->photo) {
            Storage::disk('public')->delete($vehicle->photo);
        }

        $vehicle->delete();

        return response()->json(['message' => 'Vehicle removed.']);
    }

    private function format(CustomerVehicle $v): array
    {
        return [
            'id' => $v->id,
            'label' => $v->getLabel(),
            'make' => $v->make ? ['id' => $v->make->id, 'name' => $v->make->name, 'is_european' => $v->make->is_european] : null,
            'model' => $v->model ? ['id' => $v->model->id, 'name' => $v->model->name, 'body_type' => $v->model->body_type] : null,
            'year' => $v->year,
            'colour' => $v->colour,
            'registration' => $v->registration,
            'vin' => $v->vin,
            'transmission' => $v->transmission,
            'fuel_type' => $v->fuel_type,
            'photo_url' => $v->getPhotoUrl(),
            'notes' => $v->notes,
            'is_primary' => $v->is_primary,
        ];
    }
}
