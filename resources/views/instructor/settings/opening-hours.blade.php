@extends('layouts.instructor')

@section('title', 'Opening Hours')
@section('heading', 'Settings › Opening Hours')

@section('content')

<div class="sett-page">
@include('instructor.settings.partials.header', [
    'current'     => 'opening-hours',
    'title'       => 'Opening Hours',
    'description' => 'Set when you are regularly available for bookings each week. You can override specific days from your calendar.',
])

<div class="sett-callout">
    <i class="bi bi-info-circle-fill"></i>
    <div><strong>Boost your bookings</strong> — increasing any 1-hour open times to 2-hours can unlock more test package bookings.</div>
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

</div> {{-- /.sett-page --}}
@endsection
