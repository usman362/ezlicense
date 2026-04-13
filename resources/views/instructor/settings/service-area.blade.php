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

{{-- Header Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h5 class="card-title mb-1"><i class="bi bi-geo-alt-fill text-warning me-2"></i>Service Area</h5>
                <p class="text-muted small mb-0">Define the suburbs where you're available to pick up learners. Learners searching in these areas will see you as an available instructor.</p>
            </div>
            <div class="text-end" id="coverage-stats">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-center">
                        <div class="h4 fw-bold text-primary mb-0" id="stat-suburbs">0</div>
                        <div class="small text-muted">Suburbs</div>
                    </div>
                    <div class="vr"></div>
                    <div class="text-center">
                        <div class="h4 fw-bold text-success mb-0" id="stat-states">0</div>
                        <div class="small text-muted">States</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Unsaved Changes Banner --}}
<div id="unsaved-banner" class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-3" style="display: none !important;">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span class="small fw-semibold">You have unsaved changes</span>
    <button type="button" class="btn btn-sm btn-outline-warning ms-auto" id="discard-areas-btn">Discard</button>
    <button type="button" class="btn btn-sm btn-warning fw-bold" id="save-areas-btn-top">Save Changes</button>
</div>

{{-- Search & Add Section --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="bi bi-search me-2"></i>Add Suburbs</h6>
        <div class="row g-3">
            <div class="col-lg-8">
                <label class="form-label small fw-semibold">Search by suburb name or postcode</label>
                <div class="position-relative">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-geo"></i></span>
                        <input type="text" id="suburb-add-input" class="form-control" placeholder="Type suburb name or postcode..." autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                        <button type="button" class="btn btn-primary" id="suburb-add-btn"><i class="bi bi-plus-lg me-1"></i>Add</button>
                    </div>
                    <ul id="suburb-suggestions" class="list-group position-absolute w-100 mt-1 shadow-sm" style="display: none; z-index: 1050; max-height: 260px; overflow-y: auto;"></ul>
                    <div class="small text-muted mt-1"><i class="bi bi-info-circle me-1"></i>Type at least 2 characters to search. Suburbs with the same name in different states will all be shown.</div>
                </div>
            </div>
            <div class="col-lg-4">
                <label class="form-label small fw-semibold">Quick add by postcode</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-mailbox"></i></span>
                    <input type="text" id="postcode-add-input" class="form-control" placeholder="e.g. 2000" maxlength="4">
                    <button type="button" class="btn btn-outline-primary" id="postcode-add-btn">Add All</button>
                </div>
                <div class="small text-muted mt-1">Adds all suburbs with this postcode</div>
            </div>
        </div>
    </div>
</div>

{{-- Map Placeholder --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <div id="service-area-map-placeholder" class="rounded position-relative overflow-hidden" style="min-height: 200px; background: linear-gradient(135deg, #e8f4f8 0%, #f0f7fa 50%, #eef5f0 100%);">
            <div class="d-flex align-items-center justify-content-center h-100 p-4" style="min-height: 200px;">
                <div class="text-center">
                    <i class="bi bi-globe-asia-australia display-4 text-primary opacity-50 d-block mb-2"></i>
                    <p class="text-muted small mb-1" id="service-area-summary">You are servicing <strong>0</strong> suburbs across Australia</p>
                    <p class="text-muted small mb-0">Select suburbs below to define your coverage area</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Selected Suburbs --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-pin-map me-2"></i>Selected Suburbs</h6>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="view-all-suburbs" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-list-ul me-1"></i>View All
            </button>
            <ul class="dropdown-menu dropdown-menu-end" id="view-all-suburbs-list" aria-labelledby="view-all-suburbs" style="max-height: 300px; overflow-y: auto;"></ul>
        </div>
    </div>
    <div class="card-body">
        <div id="service-area-chips" class="d-flex flex-wrap gap-2" style="min-height: 48px;"></div>
        <p class="small text-muted mt-2 mb-0" id="service-area-chips-empty">
            <i class="bi bi-info-circle me-1"></i>No suburbs selected yet. Use the search above to add suburbs to your service area.
        </p>
    </div>
</div>

{{-- Save Bar --}}
<div class="card border-0 shadow-sm">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <span id="areas-message" class="small"></span>
        </div>
        <button type="button" class="btn btn-primary px-4" id="save-areas-btn">
            <i class="bi bi-check-lg me-1"></i>Save Changes
        </button>
    </div>
</div>

@push('scripts')
    @vite('resources/js/instructor-settings-service-area.js')
@endpush
@endsection
