<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ServiceProviderController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceProvider::with(['user', 'category']);

        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('service_category_id', $request->category);
        }

        $providers = $query->latest()->paginate(20)->withQueryString();
        $categories = ServiceCategory::active()->orderBy('name')->get();

        return view('admin.service-providers.index', compact('providers', 'categories'));
    }

    public function create()
    {
        $categories = ServiceCategory::active()->orderBy('name')->get();
        return view('admin.service-providers.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_mode' => 'required|in:existing,new',
            'user_id' => 'required_if:user_mode,existing|nullable|exists:users,id',
            'new_name' => 'required_if:user_mode,new|nullable|string|max:120',
            'new_email' => ['required_if:user_mode,new', 'nullable', 'email', Rule::unique('users', 'email')],
            'new_password' => 'required_if:user_mode,new|nullable|string|min:6',
            'service_category_id' => 'required|exists:service_categories,id',
            'business_name' => 'nullable|string|max:255',
            'abn' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'years_experience' => 'nullable|integer|min:0|max:80',
            'hourly_rate' => 'required|numeric|min:0',
            'callout_fee' => 'nullable|numeric|min:0',
            'default_duration_minutes' => 'required|integer|min:15',
            'service_radius_km' => 'required|integer|min:1',
            'base_suburb' => 'nullable|string|max:120',
            'base_postcode' => 'nullable|string|max:10',
            'base_state' => 'nullable|string|max:10',
            'service_description' => 'nullable|string',
            'license_number' => 'nullable|string|max:120',
            'auto_approve' => 'nullable|boolean',
        ]);

        if ($data['user_mode'] === 'new') {
            $user = User::create([
                'name' => $data['new_name'],
                'first_name' => $data['new_name'],
                'email' => $data['new_email'],
                'password' => Hash::make($data['new_password']),
                'role' => defined(User::class . '::ROLE_LEARNER') ? User::ROLE_LEARNER : 'learner',
            ]);
            $userId = $user->id;
        } else {
            $userId = $data['user_id'];
        }

        $autoApprove = (bool) ($data['auto_approve'] ?? false);

        ServiceProvider::create([
            'user_id' => $userId,
            'service_category_id' => $data['service_category_id'],
            'business_name' => $data['business_name'] ?? null,
            'abn' => $data['abn'] ?? null,
            'bio' => $data['bio'] ?? null,
            'years_experience' => $data['years_experience'] ?? null,
            'hourly_rate' => $data['hourly_rate'],
            'callout_fee' => $data['callout_fee'] ?? 0,
            'default_duration_minutes' => $data['default_duration_minutes'],
            'service_radius_km' => $data['service_radius_km'],
            'base_suburb' => $data['base_suburb'] ?? null,
            'base_postcode' => $data['base_postcode'] ?? null,
            'base_state' => $data['base_state'] ?? null,
            'service_description' => $data['service_description'] ?? null,
            'license_number' => $data['license_number'] ?? null,
            'verification_status' => $autoApprove ? 'approved' : 'pending',
            'is_active' => $autoApprove,
        ]);

        return redirect()->route('admin.service-providers.index')
            ->with('success', 'Service provider created' . ($autoApprove ? ' and approved.' : '.'));
    }

    public function show(ServiceProvider $serviceProvider)
    {
        $serviceProvider->load(['user', 'category', 'documents', 'serviceAreas']);
        return view('admin.service-providers.show', ['provider' => $serviceProvider]);
    }

    public function edit(ServiceProvider $serviceProvider)
    {
        $serviceProvider->load('user');
        $categories = ServiceCategory::active()->orderBy('name')->get();
        return view('admin.service-providers.edit', ['provider' => $serviceProvider, 'categories' => $categories]);
    }

    public function update(Request $request, ServiceProvider $serviceProvider)
    {
        $data = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'business_name' => 'nullable|string|max:255',
            'abn' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'years_experience' => 'nullable|integer|min:0|max:80',
            'hourly_rate' => 'required|numeric|min:0',
            'callout_fee' => 'nullable|numeric|min:0',
            'default_duration_minutes' => 'required|integer|min:15',
            'service_radius_km' => 'required|integer|min:1',
            'base_suburb' => 'nullable|string|max:120',
            'base_postcode' => 'nullable|string|max:10',
            'base_state' => 'nullable|string|max:10',
            'service_description' => 'nullable|string',
            'license_number' => 'nullable|string|max:120',
            'is_active' => 'nullable|boolean',
        ]);

        $serviceProvider->update([
            'service_category_id' => $data['service_category_id'],
            'business_name' => $data['business_name'] ?? null,
            'abn' => $data['abn'] ?? null,
            'bio' => $data['bio'] ?? null,
            'years_experience' => $data['years_experience'] ?? null,
            'hourly_rate' => $data['hourly_rate'],
            'callout_fee' => $data['callout_fee'] ?? 0,
            'default_duration_minutes' => $data['default_duration_minutes'],
            'service_radius_km' => $data['service_radius_km'],
            'base_suburb' => $data['base_suburb'] ?? null,
            'base_postcode' => $data['base_postcode'] ?? null,
            'base_state' => $data['base_state'] ?? null,
            'service_description' => $data['service_description'] ?? null,
            'license_number' => $data['license_number'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? $serviceProvider->is_active),
        ]);

        return redirect()->route('admin.service-providers.show', $serviceProvider)
            ->with('success', 'Service provider updated successfully.');
    }

    public function destroy(ServiceProvider $serviceProvider)
    {
        $serviceProvider->delete();
        return redirect()->route('admin.service-providers.index')
            ->with('success', 'Service provider removed.');
    }

    public function approve(ServiceProvider $serviceProvider)
    {
        $serviceProvider->update([
            'verification_status' => 'approved',
            'is_active' => true,
        ]);
        return back()->with('success', 'Provider approved and activated.');
    }

    public function reject(Request $request, ServiceProvider $serviceProvider)
    {
        $request->validate(['admin_notes' => 'nullable|string']);
        $serviceProvider->update([
            'verification_status' => 'rejected',
            'is_active' => false,
            'admin_notes' => $request->admin_notes,
        ]);
        return back()->with('success', 'Provider rejected.');
    }
}
