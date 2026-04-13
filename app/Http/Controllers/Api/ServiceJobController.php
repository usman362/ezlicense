<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use App\Models\ServiceBooking;
use App\Models\ServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ServiceJobController extends Controller
{
    /**
     * List service types (for dropdowns).
     */
    public function serviceTypes(): JsonResponse
    {
        $types = ServiceType::active()
            ->ordered()
            ->get(['id', 'name', 'slug', 'icon', 'description', 'category']);

        // Group by category
        $grouped = $types->groupBy('category');

        return response()->json([
            'data' => $types,
            'grouped' => $grouped,
        ]);
    }

    /**
     * Customer submits a job request to a service provider.
     * This creates a service_booking with proposal_status = pending.
     * The provider must accept before it becomes a confirmed booking.
     */
    public function submitJobRequest(Request $request, ServiceProvider $serviceProvider): JsonResponse
    {
        $validated = $request->validate([
            'customer_vehicle_id' => 'nullable|exists:customer_vehicles,id',
            'vehicle_make' => 'nullable|string|max:60',
            'vehicle_model' => 'nullable|string|max:80',
            'vehicle_year' => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'vehicle_registration' => 'nullable|string|max:20',
            'vehicle_photos' => 'nullable|array|max:5',
            'vehicle_photos.*' => 'image|max:5120',
            'mechanic_service_type_id' => 'nullable|exists:mechanic_service_types,id',
            'job_description' => 'required|string|min:10|max:2000',
            'address_line' => 'required|string|max:255',
            'suburb' => 'required|string|max:100',
            'postcode' => 'required|string|max:10',
            'state' => 'required|string|max:10',
            'preferred_date' => 'nullable|date|after:today',
        ]);

        $user = Auth::user();

        // Handle vehicle photo uploads
        $photosPaths = [];
        if ($request->hasFile('vehicle_photos')) {
            foreach ($request->file('vehicle_photos') as $photo) {
                $photosPaths[] = $photo->store("job-photos/{$user->id}", 'public');
            }
        }

        // If customer selected an existing vehicle, pull details from it
        if (!empty($validated['customer_vehicle_id'])) {
            $vehicle = $user->vehicles()->with(['make', 'model'])->find($validated['customer_vehicle_id']);
            if ($vehicle) {
                $validated['vehicle_make'] = $vehicle->make?->name;
                $validated['vehicle_model'] = $vehicle->model?->name;
                $validated['vehicle_year'] = $vehicle->year;
                $validated['vehicle_registration'] = $vehicle->registration;
            }
        }

        $booking = ServiceBooking::create([
            'user_id' => $user->id,
            'service_provider_id' => $serviceProvider->id,
            'service_category_id' => $serviceProvider->service_category_id,
            'customer_vehicle_id' => $validated['customer_vehicle_id'] ?? null,
            'vehicle_make' => $validated['vehicle_make'] ?? null,
            'vehicle_model' => $validated['vehicle_model'] ?? null,
            'vehicle_year' => $validated['vehicle_year'] ?? null,
            'vehicle_registration' => $validated['vehicle_registration'] ?? null,
            'vehicle_photos' => !empty($photosPaths) ? $photosPaths : null,
            'mechanic_service_type_id' => $validated['mechanic_service_type_id'] ?? null,
            'job_description' => $validated['job_description'],
            'address_line' => $validated['address_line'],
            'suburb' => $validated['suburb'],
            'postcode' => $validated['postcode'],
            'state' => $validated['state'],
            'scheduled_at' => $validated['preferred_date'] ?? null,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'proposal_status' => ServiceBooking::PROPOSAL_PENDING,
            'proposal_expires_at' => now()->addHours(48), // provider has 48 hours to respond
            'hourly_rate' => $serviceProvider->hourly_rate ?? 0,
            'callout_fee' => $serviceProvider->callout_fee ?? 0,
        ]);

        Log::info('Service job request submitted', [
            'booking_id' => $booking->id,
            'reference' => $booking->reference,
            'customer_id' => $user->id,
            'provider_id' => $serviceProvider->id,
            'vehicle' => $booking->vehicleDescription(),
        ]);

        // TODO: notify provider about new job request

        return response()->json([
            'data' => $this->formatBooking($booking->fresh()->load(['provider', 'customer', 'serviceType'])),
            'message' => 'Job request submitted! The service provider will review your request and respond within 48 hours.',
        ], 201);
    }

    /**
     * Provider views their pending job requests.
     */
    public function pendingJobs(Request $request): JsonResponse
    {
        $provider = $this->getProviderForUser();
        if (!$provider) {
            return response()->json(['message' => 'No service provider profile found.'], 404);
        }

        $jobs = ServiceBooking::where('service_provider_id', $provider->id)
            ->where('proposal_status', ServiceBooking::PROPOSAL_PENDING)
            ->where(function ($q) {
                $q->whereNull('proposal_expires_at')
                  ->orWhere('proposal_expires_at', '>', now());
            })
            ->with(['customer:id,name,email,phone', 'customerVehicle.make', 'customerVehicle.model', 'serviceType'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'data' => $jobs->map(fn ($b) => $this->formatBooking($b)),
            'meta' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'total' => $jobs->total(),
            ],
        ]);
    }

    /**
     * Provider accepts a job request.
     */
    public function acceptJob(Request $request, ServiceBooking $serviceBooking): JsonResponse
    {
        $provider = $this->getProviderForUser();
        if (!$provider || $serviceBooking->service_provider_id !== $provider->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (!$serviceBooking->isProposalPending()) {
            return response()->json(['message' => 'This job request has already been responded to.'], 422);
        }

        $validated = $request->validate([
            'quoted_amount' => 'nullable|numeric|min:0',
            'proposal_message' => 'nullable|string|max:1000',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $serviceBooking->update([
            'proposal_status' => ServiceBooking::PROPOSAL_ACCEPTED,
            'proposal_message' => $validated['proposal_message'] ?? 'Job accepted!',
            'quoted_amount' => $validated['quoted_amount'] ?? null,
            'proposal_responded_at' => now(),
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'scheduled_at' => $validated['scheduled_at'] ?? $serviceBooking->scheduled_at,
        ]);

        // If provider provided a quote, update the total
        if (!empty($validated['quoted_amount'])) {
            $total = (float) $validated['quoted_amount'];
            $commissionRate = $serviceBooking->category?->commission_rate ?? 10;
            $platformFee = round($total * $commissionRate / 100, 2);
            $serviceBooking->update([
                'total_amount' => $total,
                'platform_fee' => $platformFee,
                'provider_payout' => $total - $platformFee,
            ]);
        }

        Log::info('Provider accepted job', [
            'booking_id' => $serviceBooking->id,
            'provider_id' => $provider->id,
        ]);

        // TODO: notify customer that provider accepted

        return response()->json([
            'data' => $this->formatBooking($serviceBooking->fresh()->load(['customer', 'serviceType'])),
            'message' => 'Job accepted! The customer has been notified.',
        ]);
    }

    /**
     * Provider rejects a job request.
     */
    public function rejectJob(Request $request, ServiceBooking $serviceBooking): JsonResponse
    {
        $provider = $this->getProviderForUser();
        if (!$provider || $serviceBooking->service_provider_id !== $provider->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (!$serviceBooking->isProposalPending()) {
            return response()->json(['message' => 'This job request has already been responded to.'], 422);
        }

        $validated = $request->validate([
            'proposal_message' => 'nullable|string|max:500',
        ]);

        $serviceBooking->update([
            'proposal_status' => ServiceBooking::PROPOSAL_REJECTED,
            'proposal_message' => $validated['proposal_message'] ?? 'Unable to take this job at this time.',
            'proposal_responded_at' => now(),
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Declined by provider',
        ]);

        Log::info('Provider rejected job', [
            'booking_id' => $serviceBooking->id,
            'provider_id' => $provider->id,
        ]);

        // TODO: notify customer that provider declined

        return response()->json([
            'message' => 'Job request declined.',
        ]);
    }

    /**
     * Provider's job list (all statuses).
     */
    public function myJobs(Request $request): JsonResponse
    {
        $provider = $this->getProviderForUser();
        if (!$provider) {
            return response()->json(['message' => 'No service provider profile found.'], 404);
        }

        $query = ServiceBooking::where('service_provider_id', $provider->id)
            ->with(['customer:id,name,email,phone', 'serviceType']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($proposalStatus = $request->input('proposal_status')) {
            $query->where('proposal_status', $proposalStatus);
        }

        $jobs = $query->orderByDesc('created_at')->paginate(20);

        return response()->json([
            'data' => $jobs->map(fn ($b) => $this->formatBooking($b)),
            'meta' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'total' => $jobs->total(),
            ],
        ]);
    }

    // -- Helpers ------------------------------------------------

    private function getProviderForUser(): ?ServiceProvider
    {
        return ServiceProvider::where('user_id', Auth::id())->first();
    }

    private function formatBooking(ServiceBooking $b): array
    {
        return [
            'id' => $b->id,
            'reference' => $b->reference,
            'customer' => $b->customer ? [
                'id' => $b->customer->id,
                'name' => $b->customer->name,
                'phone' => $b->customer->phone,
            ] : null,
            'provider' => $b->provider ? [
                'id' => $b->provider->id,
                'business_name' => $b->provider->business_name,
            ] : null,
            'vehicle' => [
                'description' => $b->vehicleDescription(),
                'make' => $b->vehicle_make,
                'model' => $b->vehicle_model,
                'year' => $b->vehicle_year,
                'registration' => $b->vehicle_registration,
                'photos' => $b->vehicle_photos ? collect($b->vehicle_photos)->map(fn ($p) => asset("storage/{$p}"))->all() : [],
            ],
            'service_type' => $b->serviceType ? [
                'id' => $b->serviceType->id,
                'name' => $b->serviceType->name,
                'category' => $b->serviceType->category,
            ] : null,
            'job_description' => $b->job_description,
            'location' => [
                'address' => $b->address_line,
                'suburb' => $b->suburb,
                'postcode' => $b->postcode,
                'state' => $b->state,
            ],
            'scheduled_at' => $b->scheduled_at?->toIso8601String(),
            'status' => $b->status,
            'proposal_status' => $b->proposal_status,
            'proposal_message' => $b->proposal_message,
            'quoted_amount' => $b->quoted_amount ? (float) $b->quoted_amount : null,
            'total_amount' => $b->total_amount ? (float) $b->total_amount : null,
            'proposal_expires_at' => $b->proposal_expires_at?->toIso8601String(),
            'created_at' => $b->created_at->toIso8601String(),
        ];
    }
}
