@extends('layouts.instructor')

@section('title', 'Opening Hours')
@section('heading', 'Settings › Opening Hours')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.settings.personal-details') }}">Settings</a></li>
        <li class="breadcrumb-item active" aria-current="page">Opening Hours</li>
    </ol>
</nav>

<ul class="nav nav-tabs border-0 small mb-4">
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.personal-details') }}">Personal Details</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.profile') }}">Profile</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.vehicle') }}">Vehicles</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.service-area') }}">Service Area</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('instructor.settings.opening-hours') }}">Opening Hours</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.calendar-settings') }}">Calendar Settings</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.pricing') }}">Pricing</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.documents') }}">Documents</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.banking') }}">Banking</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.guide') }}">Guide</a></li>
</ul>

<div class="alert alert-warning border-0 mb-4" role="alert">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Boost your bookings</strong> — Increasing any 1-hour open times to 2-hours can unlock more test package bookings.
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title mb-1">Opening Hours</h5>
        <p class="text-muted small mb-4">Set when you're regularly available for bookings.</p>
        <div id="opening-hours-loading" class="text-muted">Loading…</div>
        <div id="opening-hours-container" style="display: none;"></div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <button type="button" class="btn btn-link text-secondary text-decoration-none p-0" id="discard-hours-btn" style="display: none;">Discard Changes</button>
        <span id="availability-message" class="ms-3"></span>
    </div>
    <button type="button" class="btn btn-primary" id="save-availability-btn">Save Changes</button>
</div>

@push('scripts')
    @vite('resources/js/instructor-settings-opening-hours.js')
@endpush
@endsection
