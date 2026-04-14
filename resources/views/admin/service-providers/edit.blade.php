@extends('layouts.admin')
@section('title', 'Edit Service Provider')
@section('content')
<div class="container-fluid p-4" style="max-width: 900px;">
    <a href="{{ route('admin.service-providers.show', $provider) }}" class="text-decoration-none small">&larr; Back to provider</a>
    <h1 class="h3 mt-2 mb-4">Edit Service Provider</h1>

    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('admin.service-providers.update', $provider) }}">
        @csrf
        @method('PUT')

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">User Account</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Name</label>
                        <p class="fw-semibold mb-0">{{ $provider->user->name ?? '—' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Email</label>
                        <p class="fw-semibold mb-0">{{ $provider->user->email ?? '—' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">User ID</label>
                        <p class="fw-semibold mb-0">#{{ $provider->user_id }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Provider Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Service category *</label>
                        <select name="service_category_id" required class="form-select">
                            <option value="">Select...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('service_category_id', $provider->service_category_id)==$cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Business name</label>
                        <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $provider->business_name) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ABN</label>
                        <input type="text" name="abn" class="form-control" value="{{ old('abn', $provider->abn) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Years experience</label>
                        <input type="number" name="years_experience" class="form-control" value="{{ old('years_experience', $provider->years_experience) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">License number</label>
                        <input type="text" name="license_number" class="form-control" value="{{ old('license_number', $provider->license_number) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Short bio</label>
                        <textarea name="bio" rows="2" class="form-control">{{ old('bio', $provider->bio) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Service description</label>
                        <textarea name="service_description" rows="3" class="form-control">{{ old('service_description', $provider->service_description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Pricing & Service Area</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Hourly rate ($) *</label>
                        <input type="number" step="0.01" name="hourly_rate" required class="form-control" value="{{ old('hourly_rate', $provider->hourly_rate) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Call-out fee ($)</label>
                        <input type="number" step="0.01" name="callout_fee" class="form-control" value="{{ old('callout_fee', $provider->callout_fee) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Default duration (min) *</label>
                        <input type="number" name="default_duration_minutes" required class="form-control" value="{{ old('default_duration_minutes', $provider->default_duration_minutes) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Radius (km) *</label>
                        <input type="number" name="service_radius_km" required class="form-control" value="{{ old('service_radius_km', $provider->service_radius_km) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Base suburb</label>
                        <input type="text" name="base_suburb" class="form-control" value="{{ old('base_suburb', $provider->base_suburb) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Postcode</label>
                        <input type="text" name="base_postcode" class="form-control" value="{{ old('base_postcode', $provider->base_postcode) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">State</label>
                        <input type="text" name="base_state" class="form-control" value="{{ old('base_state', $provider->base_state) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-check mb-4">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $provider->is_active))>
            <label for="is_active" class="form-check-label">Active (visible to public)</label>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-lg">Save Changes</button>
            <a href="{{ route('admin.service-providers.show', $provider) }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
