@extends('layouts.instructor')

@section('title', 'Pricing')
@section('heading', 'Settings › Pricing')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.settings.personal-details') }}">Settings</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pricing</li>
    </ol>
</nav>

<ul class="nav nav-tabs border-0 small mb-4">
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.personal-details') }}">Personal Details</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.profile') }}">Profile</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.vehicle') }}">Vehicles</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.service-area') }}">Service Area</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.opening-hours') }}">Opening Hours</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.calendar-settings') }}">Calendar Settings</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('instructor.settings.pricing') }}">Pricing</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.documents') }}">Documents</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.banking') }}">Banking</a></li>
</ul>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div id="pricing-loading" class="text-muted">Loading…</div>
        <div id="pricing-content" style="display: none;">
            <p class="small text-muted mb-3">
                <strong>EzLicence Learners:</strong> from EzLicence marketplace.<br>
                <strong>Private Learners:</strong> invited to EzLicence by you.
            </p>

            <div class="mb-4">
                <h6 class="fw-bold mb-1">Lesson</h6>
                <p class="small text-muted mb-2">Price per booking hour</p>
                <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                    <span class="d-inline-flex align-items-center gap-2">
                        <span class="rounded-circle bg-warning" style="width:10px;height:10px;"></span>
                        <span>EzLicence</span>
                    </span>
                    <span id="lesson-ezlicence-display" class="fw-medium">$0.00</span>
                    <button type="button" class="btn btn-sm btn-link p-0 text-primary lesson-ezlicence-edit">Edit</button>
                    <span id="lesson-ezlicence-edit-wrap" class="d-none align-items-center gap-2">
                        <input type="number" id="lesson-ezlicence-input" class="form-control form-control-sm d-inline-block" style="width:100px;" min="0" step="0.01" placeholder="0.00">
                        <button type="button" class="btn btn-sm btn-primary lesson-ezlicence-save">Save</button>
                        <button type="button" class="btn btn-sm btn-link p-0 lesson-ezlicence-cancel">Cancel</button>
                    </span>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                    <span class="d-inline-flex align-items-center gap-2">
                        <span class="rounded-circle bg-primary" style="width:10px;height:10px;"></span>
                        <span>Private</span>
                    </span>
                    <span id="lesson-private-display" class="fw-medium">$0.00</span>
                    <button type="button" class="btn btn-sm btn-link p-0 text-primary lesson-private-edit">Edit</button>
                    <span id="lesson-private-edit-wrap" class="d-none align-items-center gap-2">
                        <input type="number" id="lesson-private-input" class="form-control form-control-sm d-inline-block" style="width:100px;" min="0" step="0.01" placeholder="0.00">
                        <button type="button" class="btn btn-sm btn-primary lesson-private-save">Save</button>
                        <button type="button" class="btn btn-sm btn-link p-0 lesson-private-cancel">Cancel</button>
                    </span>
                </div>
            </div>

            <div class="mb-0">
                <h6 class="fw-bold mb-1">Test Package</h6>
                <p class="small text-muted mb-2">Pick up 1hr before, 45-minute pre-test warm-up, and drop-off after result</p>
                <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                    <span class="d-inline-flex align-items-center gap-2">
                        <span class="rounded-circle bg-warning" style="width:10px;height:10px;"></span>
                        <span>EzLicence</span>
                    </span>
                    <span id="test-ezlicence-display" class="fw-medium">$0.00</span>
                    <button type="button" class="btn btn-sm btn-link p-0 text-primary test-ezlicence-edit">Edit</button>
                    <span id="test-ezlicence-edit-wrap" class="d-none align-items-center gap-2">
                        <input type="number" id="test-ezlicence-input" class="form-control form-control-sm d-inline-block" style="width:100px;" min="0" step="0.01" placeholder="0.00">
                        <button type="button" class="btn btn-sm btn-primary test-ezlicence-save">Save</button>
                        <button type="button" class="btn btn-sm btn-link p-0 test-ezlicence-cancel">Cancel</button>
                    </span>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                    <span class="d-inline-flex align-items-center gap-2">
                        <span class="rounded-circle bg-primary" style="width:10px;height:10px;"></span>
                        <span>Private</span>
                    </span>
                    <span id="test-private-display" class="fw-medium">$0.00</span>
                    <button type="button" class="btn btn-sm btn-link p-0 text-primary test-private-edit">Edit</button>
                    <span id="test-private-edit-wrap" class="d-none align-items-center gap-2">
                        <input type="number" id="test-private-input" class="form-control form-control-sm d-inline-block" style="width:100px;" min="0" step="0.01" placeholder="0.00">
                        <button type="button" class="btn btn-sm btn-primary test-private-save">Save</button>
                        <button type="button" class="btn btn-sm btn-link p-0 test-private-cancel">Cancel</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<span id="pricing-message" class="text-success"></span>

@push('scripts')
    @vite('resources/js/instructor-settings-pricing.js')
@endpush
@endsection
