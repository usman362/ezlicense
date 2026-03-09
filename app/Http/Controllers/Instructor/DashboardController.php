<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    /**
     * Get or create instructor profile for the authenticated instructor.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = Auth::user();
        $profile = $user->instructorProfile;

        if (! $profile) {
            $profile = InstructorProfile::create([
                'user_id' => $user->id,
                'transmission' => 'both',
                'is_active' => false,
            ]);
        }

        $profile->load('serviceAreas.state');

        return response()->json([
            'data' => [
                'id' => $profile->id,
                'bio' => $profile->bio,
                'languages' => $profile->languages ?? [],
                'association_member' => $profile->association_member ?? false,
                'instructing_start_month' => $profile->instructing_start_month,
                'instructing_start_year' => $profile->instructing_start_year,
                'service_test_existing' => $profile->service_test_existing ?? false,
                'service_test_new' => $profile->service_test_new ?? false,
                'service_manual_no_vehicle' => $profile->service_manual_no_vehicle ?? false,
                'notification_email_marketing' => $profile->notification_email_marketing ?? true,
                'notification_sms_marketing' => $profile->notification_sms_marketing ?? true,
                'transmission' => $profile->transmission,
                'vehicle_make' => $profile->vehicle_make,
                'vehicle_model' => $profile->vehicle_model,
                'vehicle_year' => $profile->vehicle_year,
                'vehicle_safety_rating' => $profile->vehicle_safety_rating,
                'wwcc_number' => $profile->wwcc_number,
                'wwcc_verified_at' => $profile->wwcc_verified_at?->toIso8601String(),
                'accreditation_details' => $profile->accreditation_details,
                'lesson_price' => (float) $profile->lesson_price,
                'test_package_price' => $profile->test_package_price ? (float) $profile->test_package_price : null,
                'lesson_price_private' => $profile->lesson_price_private !== null ? (float) $profile->lesson_price_private : null,
                'test_package_price_private' => $profile->test_package_price_private !== null ? (float) $profile->test_package_price_private : null,
                'lesson_duration_minutes' => $profile->lesson_duration_minutes,
                'offers_test_package' => $profile->offers_test_package,
                'is_active' => $profile->is_active,
                'service_areas' => $profile->serviceAreas->map(fn ($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'postcode' => $s->postcode,
                    'state' => $s->state?->code,
                ]),
                'availability_slots' => $profile->availabilitySlots->map(fn ($s) => [
                    'id' => $s->id,
                    'day_of_week' => $s->day_of_week,
                    'start_time' => $s->start_time,
                    'end_time' => $s->end_time,
                ]),
                'travel_buffer_same_mins' => (int) ($profile->travel_buffer_same_mins ?? 30),
                'travel_buffer_synced_mins' => (int) ($profile->travel_buffer_synced_mins ?? 30),
                'min_prior_notice_hours' => (int) ($profile->min_prior_notice_hours ?? 5),
                'max_advance_notice_days' => (int) ($profile->max_advance_notice_days ?? 75),
                'smart_scheduling_enabled' => (bool) ($profile->smart_scheduling_enabled ?? true),
                'smart_scheduling_buffer_hrs' => (int) ($profile->smart_scheduling_buffer_hrs ?? 1),
                'attach_ics_to_emails' => (bool) ($profile->attach_ics_to_emails ?? true),
                'default_calendar_view' => $profile->default_calendar_view ?? 'day',
                'business_name' => $profile->business_name,
                'abn' => $profile->abn,
                'billing_address' => $profile->billing_address,
                'gst_registered' => $profile->gst_registered,
                'billing_suburb' => $profile->billing_suburb,
                'billing_postcode' => $profile->billing_postcode,
                'billing_state' => $profile->billing_state,
                'payout_frequency' => $profile->payout_frequency ?? 'weekly',
                'bank_account_name' => $profile->bank_account_name,
                'bank_bsb' => $profile->bank_bsb,
                'bank_account_number_masked' => $profile->bank_account_number ? '****'.substr($profile->bank_account_number, -4) : null,
                'bank_details_submitted_at' => $profile->bank_details_submitted_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Update instructor profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();
        $profile = $user->instructorProfile;
        if (! $profile) {
            return response()->json(['message' => 'Instructor profile not found.'], 404);
        }

        $validated = $request->validate([
            'bio' => ['nullable', 'string', 'max:1600'],
            'languages' => ['nullable', 'array'],
            'languages.*' => ['string', 'max:50'],
            'association_member' => ['boolean'],
            'instructing_start_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'instructing_start_year' => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'service_test_existing' => ['boolean'],
            'service_test_new' => ['boolean'],
            'service_manual_no_vehicle' => ['boolean'],
            'notification_email_marketing' => ['boolean'],
            'notification_sms_marketing' => ['boolean'],
            'transmission' => ['required', Rule::in(['auto', 'manual', 'both'])],
            'vehicle_make' => ['nullable', 'string', 'max:100'],
            'vehicle_model' => ['nullable', 'string', 'max:100'],
            'vehicle_year' => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'vehicle_safety_rating' => ['nullable', 'string', 'max:50'],
            'wwcc_number' => ['nullable', 'string', 'max:50'],
            'accreditation_details' => ['nullable', 'string', 'max:1000'],
            'lesson_price' => ['required', 'numeric', 'min:0'],
            'test_package_price' => ['nullable', 'numeric', 'min:0'],
            'lesson_price_private' => ['nullable', 'numeric', 'min:0'],
            'test_package_price_private' => ['nullable', 'numeric', 'min:0'],
            'lesson_duration_minutes' => ['integer', 'min:30', 'max:180'],
            'offers_test_package' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $profile->update($validated);

        return response()->json(['data' => ['message' => 'Profile updated.']]);
    }

    /**
     * Update service areas (suburb IDs).
     */
    public function updateServiceAreas(Request $request): JsonResponse
    {
        $request->validate([
            'suburb_ids' => ['required', 'array'],
            'suburb_ids.*' => ['exists:suburbs,id'],
        ]);

        $user = Auth::user();
        $profile = $user->instructorProfile;
        if (! $profile) {
            return response()->json(['message' => 'Instructor profile not found.'], 404);
        }

        $profile->serviceAreas()->sync($request->input('suburb_ids'));

        return response()->json(['data' => ['message' => 'Service areas updated.']]);
    }

    /**
     * Replace weekly availability slots.
     */
    public function updateAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'slots' => ['required', 'array'],
            'slots.*.day_of_week' => ['required', 'integer', 'min:0', 'max:6'],
            'slots.*.start_time' => ['required', 'string', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'slots.*.end_time' => ['required', 'string', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
        ]);

        $user = Auth::user();
        $profile = $user->instructorProfile;
        if (! $profile) {
            return response()->json(['message' => 'Instructor profile not found.'], 404);
        }

        $profile->availabilitySlots()->delete();
        foreach ($request->input('slots') as $slot) {
            $profile->availabilitySlots()->create([
                'day_of_week' => $slot['day_of_week'],
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
            ]);
        }

        return response()->json(['data' => ['message' => 'Availability updated.']]);
    }

    /**
     * Update calendar settings.
     */
    public function updateCalendarSettings(Request $request): JsonResponse
    {
        $user = Auth::user();
        $profile = $user->instructorProfile;
        if (! $profile) {
            return response()->json(['message' => 'Instructor profile not found.'], 404);
        }

        $validated = $request->validate([
            'travel_buffer_same_mins' => ['required', 'integer', 'min:0', 'max:120'],
            'travel_buffer_synced_mins' => ['required', 'integer', 'min:0', 'max:120'],
            'min_prior_notice_hours' => ['required', 'integer', 'min:0', 'max:168'],
            'max_advance_notice_days' => ['required', 'integer', 'min:1', 'max:365'],
            'smart_scheduling_enabled' => ['boolean'],
            'smart_scheduling_buffer_hrs' => ['required', 'integer', 'in:1,2'],
            'attach_ics_to_emails' => ['boolean'],
            'default_calendar_view' => ['required', 'string', Rule::in(['day', 'week', 'month'])],
        ]);

        $profile->update($validated);

        return response()->json(['data' => ['message' => 'Calendar settings saved.']]);
    }

    /**
     * Update banking (billing info, payout frequency, or bank account if not yet submitted).
     */
    public function updateBanking(Request $request): JsonResponse
    {
        $user = Auth::user();
        $profile = $user->instructorProfile;
        if (! $profile) {
            return response()->json(['message' => 'Instructor profile not found.'], 404);
        }

        $canEditBank = ! $profile->bank_details_submitted_at;

        $rules = [
            'business_name' => ['nullable', 'string', 'max:255'],
            'abn' => ['nullable', 'string', 'max:20'],
            'billing_address' => ['nullable', 'string', 'max:500'],
            'gst_registered' => ['nullable', 'boolean'],
            'billing_suburb' => ['nullable', 'string', 'max:100'],
            'billing_postcode' => ['nullable', 'string', 'max:10'],
            'billing_state' => ['nullable', 'string', 'max:10'],
            'payout_frequency' => ['nullable', 'string', Rule::in(['weekly', 'fortnightly', 'every_four_weeks'])],
            'bank_account_name' => [$canEditBank ? 'nullable' : 'prohibited', 'string', 'max:255'],
            'bank_bsb' => [$canEditBank ? 'nullable' : 'prohibited', 'string', 'max:10'],
            'bank_account_number' => [$canEditBank ? 'nullable' : 'prohibited', 'string', 'max:20'],
        ];
        $validated = $request->validate($rules);

        $update = array_filter($validated, fn ($v) => $v !== null);
        if ($canEditBank && isset($validated['bank_account_number']) && $validated['bank_account_number'] !== '') {
            $update['bank_details_submitted_at'] = now();
        }
        $profile->update($update);

        return response()->json(['data' => ['message' => 'Saved.']]);
    }
}
