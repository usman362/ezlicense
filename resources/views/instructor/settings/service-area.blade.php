@extends('layouts.instructor')

@section('title', 'Service Area')
@section('heading', 'Settings › Service Area')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.settings.personal-details') }}">Settings</a></li>
        <li class="breadcrumb-item active" aria-current="page">Service Area</li>
    </ol>
</nav>

<ul class="nav nav-tabs border-0 small mb-4">
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.personal-details') }}">Personal Details</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.profile') }}">Profile</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.vehicle') }}">Vehicles</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('instructor.settings.service-area') }}">Service Area</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.opening-hours') }}">Opening Hours</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.calendar-settings') }}">Calendar Settings</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.pricing') }}">Pricing</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.documents') }}">Documents</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.banking') }}">Banking</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.guide') }}">Guide</a></li>
</ul>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title mb-2">Service Area</h5>
        <p class="text-muted small mb-0">Select the suburbs where you can pick up learners. Searches in these locations will return you as an available instructor. You can add and remove suburbs at any time.</p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <p class="mb-0" id="service-area-summary">You are servicing <strong>0</strong> suburbs.</p>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="view-all-suburbs" data-bs-toggle="dropdown" aria-expanded="false">View all</button>
                <ul class="dropdown-menu dropdown-menu-end" id="view-all-suburbs-list" aria-labelledby="view-all-suburbs"></ul>
            </div>
        </div>
        <div id="service-area-map-placeholder" class="rounded border bg-light d-flex align-items-center justify-content-center text-muted" style="min-height: 280px;">
            <div class="text-center p-4">
                <i class="bi bi-map fs-1 d-block mb-2"></i>
                <span>Map view</span>
                <div class="mt-2"><button type="button" class="btn btn-sm btn-outline-secondary" disabled>Edit Service Region</button></div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <label class="form-label mb-2">Add suburb</label>
        <div class="row g-2">
            <div class="col-md-8 position-relative">
                <input type="text" id="suburb-add-input" class="form-control" placeholder="Type suburb or postcode...">
                <ul id="suburb-suggestions" class="list-group position-absolute w-100 mt-1" style="display: none; z-index: 10; max-height: 220px; overflow-y: auto;"></ul>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-outline-primary w-100" id="suburb-add-btn">Add suburb</button>
            </div>
        </div>
        <p class="small text-muted mt-2 mb-0">Search by suburb name or postcode, then add to your service area.</p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <label class="form-label mb-2">Selected suburbs</label>
        <div id="service-area-chips" class="d-flex flex-wrap gap-2" style="min-height: 48px;"></div>
        <p class="small text-muted mt-2 mb-0" id="service-area-chips-empty">No suburbs selected. Add suburbs above.</p>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <button type="button" class="btn btn-link text-secondary text-decoration-none p-0" id="discard-areas-btn" style="display: none;">Discard Changes</button>
        <span id="areas-message" class="ms-3"></span>
    </div>
    <button type="button" class="btn btn-primary" id="save-areas-btn">Save Changes</button>
</div>

@push('scripts')
    @vite('resources/js/instructor-settings-service-area.js')
@endpush
@endsection
