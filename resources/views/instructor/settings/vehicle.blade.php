@extends('layouts.instructor')

@section('title', 'Vehicle')
@section('heading', 'Settings › Vehicle')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.settings.personal-details') }}">Settings</a></li>
        <li class="breadcrumb-item active" aria-current="page">Vehicles</li>
    </ol>
</nav>

<ul class="nav nav-tabs border-0 small mb-4">
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.personal-details') }}">Personal Details</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.profile') }}">Profile</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('instructor.settings.vehicle') }}">Vehicles</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.service-area') }}">Service Area</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.opening-hours') }}">Opening Hours</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.calendar-settings') }}">Calendar Settings</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.pricing') }}">Pricing</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.documents') }}">Documents</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.banking') }}">Banking</a></li>
</ul>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div id="vehicle-loading" class="text-muted">Loading…</div>
        <form id="vehicle-form" style="display: none;">
            <h6 class="fw-bold mb-2">Vehicle Details</h6>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-3">
                    <label class="form-label">Transmission <span class="text-danger">*</span></label>
                    <select name="transmission" class="form-select">
                        <option value="auto">Auto</option>
                        <option value="manual">Manual</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <label class="form-label">Make <span class="text-danger">*</span></label>
                    <select name="vehicle_make" id="vehicle-make" class="form-select" required>
                        <option value="">Select make</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <label class="form-label">Model <span class="text-danger">*</span></label>
                    <select name="vehicle_model" id="vehicle-model" class="form-select" required>
                        <option value="">Select model</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <label class="form-label">Year <span class="text-danger">*</span></label>
                    <select name="vehicle_year" id="vehicle-year" class="form-select">
                        <option value="">Select year</option>
                        @for($y = (int)date('Y'); $y >= 1990; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <label class="form-label">ANCAP safety rating <span class="text-danger">*</span></label>
                    <select name="vehicle_safety_rating" id="vehicle-safety-rating" class="form-select">
                        <option value="">Select rating</option>
                        <option value="5 Stars">5 Stars</option>
                        <option value="4 Stars">4 Stars</option>
                        <option value="3 Stars">3 Stars</option>
                        <option value="2 Stars">2 Stars</option>
                        <option value="1 Star">1 Star</option>
                        <option value="Not rated">Not rated</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <span id="vehicle-message" class="me-3 align-self-center"></span>
                <button type="submit" class="btn btn-warning text-dark fw-medium">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
    @vite('resources/js/instructor-settings-vehicle.js')
@endpush
@endsection
