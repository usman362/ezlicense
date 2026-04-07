@extends('layouts.frontend')

@section('title', 'Become a Service Provider')

@section('content')
<div class="container py-5" style="max-width: 760px;">
    <h1 class="h2 fw-bold mb-2">Become a Service Provider</h1>
    <p class="text-muted mb-4">Fill out your profile to start receiving bookings. An admin will review and approve your listing.</p>

    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('service-provider.onboarding.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Service Category <span class="text-danger">*</span></label>
                    <select name="service_category_id" required class="form-select form-select-lg">
                        <option value="">Select a category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('service_category_id')==$cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Business name</label>
                        <input type="text" name="business_name" class="form-control" value="{{ old('business_name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ABN</label>
                        <input type="text" name="abn" class="form-control" value="{{ old('abn') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Short bio</label>
                    <textarea name="bio" rows="3" class="form-control">{{ old('bio') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Service description</label>
                    <textarea name="service_description" rows="4" class="form-control" placeholder="Describe your services...">{{ old('service_description') }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Years experience</label>
                        <input type="number" name="years_experience" class="form-control" value="{{ old('years_experience') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hourly rate ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="hourly_rate" required class="form-control" value="{{ old('hourly_rate') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Call-out fee ($)</label>
                        <input type="number" step="0.01" name="callout_fee" class="form-control" value="{{ old('callout_fee', 0) }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Default duration (min) <span class="text-danger">*</span></label>
                        <input type="number" name="default_duration_minutes" required class="form-control" value="{{ old('default_duration_minutes', 60) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Service radius (km) <span class="text-danger">*</span></label>
                        <input type="number" name="service_radius_km" required class="form-control" value="{{ old('service_radius_km', 20) }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <label class="form-label">Base suburb</label>
                        <input type="text" name="base_suburb" class="form-control" value="{{ old('base_suburb') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Postcode</label>
                        <input type="text" name="base_postcode" class="form-control" value="{{ old('base_postcode') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input type="text" name="base_state" class="form-control" value="{{ old('base_state') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">License number (if applicable)</label>
                    <input type="text" name="license_number" class="form-control" value="{{ old('license_number') }}">
                </div>

                <button class="btn btn-primary btn-lg w-100">Submit for Approval</button>
            </form>
        </div>
    </div>
</div>
@endsection
