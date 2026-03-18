@extends('layouts.instructor')

@section('title', 'Calendar Settings')
@section('heading', 'Settings › Calendar Settings')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.settings.personal-details') }}">Settings</a></li>
        <li class="breadcrumb-item active" aria-current="page">Calendar Settings</li>
    </ol>
</nav>

<ul class="nav nav-tabs border-0 small mb-4">
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.personal-details') }}">Personal Details</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.profile') }}">Profile</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.vehicle') }}">Vehicles</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.service-area') }}">Service Area</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.opening-hours') }}">Opening Hours</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('instructor.settings.calendar-settings') }}">Calendar Settings</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.pricing') }}">Pricing</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.documents') }}">Documents</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.banking') }}">Banking</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.guide') }}">Guide</a></li>
</ul>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div id="calendar-settings-loading" class="text-muted">Loading…</div>
        <form id="calendar-settings-form" style="display: none;">
            <div class="mb-4">
                <h6 class="fw-bold mb-1">Travel Buffer</h6>
                <p class="small text-muted mb-2">Time between lessons.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Same Transmission</label>
                        <select name="travel_buffer_same_mins" class="form-select">
                            <option value="0">0 mins</option>
                            <option value="15">15 mins</option>
                            <option value="30">30 mins</option>
                            <option value="45">45 mins</option>
                            <option value="60">60 mins</option>
                            <option value="90">90 mins</option>
                            <option value="120">120 mins</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Synced Calendar Events</label>
                        <select name="travel_buffer_synced_mins" class="form-select">
                            <option value="0">0 mins</option>
                            <option value="15">15 mins</option>
                            <option value="30">30 mins</option>
                            <option value="45">45 mins</option>
                            <option value="60">60 mins</option>
                            <option value="90">90 mins</option>
                            <option value="120">120 mins</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold mb-1">Scheduling Window</h6>
                <p class="small text-muted mb-2">Limit the time range during which bookings can be made.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Minimum prior notice for bookings</label>
                        <select name="min_prior_notice_hours" class="form-select">
                            <option value="0">0 hours</option>
                            <option value="1">1 hour</option>
                            <option value="2">2 hours</option>
                            <option value="3">3 hours</option>
                            <option value="5">5 hours</option>
                            <option value="12">12 hours</option>
                            <option value="24">24 hours</option>
                            <option value="48">48 hours</option>
                            <option value="168">7 days</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Maximum advance notice for bookings</label>
                        <select name="max_advance_notice_days" class="form-select">
                            <option value="7">7 days</option>
                            <option value="14">14 days</option>
                            <option value="30">30 days</option>
                            <option value="45">45 days</option>
                            <option value="60">60 days</option>
                            <option value="75">75 days</option>
                            <option value="90">90 days</option>
                            <option value="180">180 days</option>
                            <option value="365">365 days</option>
                        </select>
                    </div>
                </div>
                <p class="small mt-2 mb-0"><a href="#" class="text-muted">How notice periods work</a></p>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold mb-1">Smart Scheduling</h6>
                <p class="small text-muted mb-2">Reduces awkward time gaps between bookings. <a href="#">Learn more</a></p>
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <div class="form-check form-switch mb-0">
                        <input type="checkbox" name="smart_scheduling_enabled" class="form-check-input" id="smart-scheduling" value="1">
                        <label class="form-check-label" for="smart-scheduling">Smart Scheduling</label>
                    </div>
                    <div class="d-flex gap-2" id="smart-scheduling-buffer-wrap">
                        <div class="form-check">
                            <input type="radio" name="smart_scheduling_buffer_hrs" class="form-check-input" id="buffer-1hr" value="1">
                            <label class="form-check-label" for="buffer-1hr">1hr</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="smart_scheduling_buffer_hrs" class="form-check-input" id="buffer-2hr" value="2">
                            <label class="form-check-label" for="buffer-2hr">2hr</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold mb-1">Attach calendar event to booking emails</h6>
                <p class="small text-muted mb-2">Would you like us to attach a calendar event (.ics) to your booking emails?</p>
                <p class="small text-warning mb-2">If your email app already adds events automatically — or if your calendar is connected to Secure Licences — you may get duplicate events.</p>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input type="radio" name="attach_ics_to_emails" class="form-check-input" id="attach-ics-yes" value="1">
                        <label class="form-check-label" for="attach-ics-yes">Yes — Add the calendar event</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="attach_ics_to_emails" class="form-check-input" id="attach-ics-no" value="0">
                        <label class="form-check-label" for="attach-ics-no">No — Don't add the calendar event</label>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold mb-1">Default Calendar View</h6>
                <p class="small text-muted mb-2">The default view when viewing the calendar on desktop browsers. Default view is day when viewing on mobile.</p>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input type="radio" name="default_calendar_view" class="form-check-input" id="view-day" value="day">
                        <label class="form-check-label" for="view-day">Day</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="default_calendar_view" class="form-check-input" id="view-week" value="week">
                        <label class="form-check-label" for="view-week">Week</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="default_calendar_view" class="form-check-input" id="view-month" value="month">
                        <label class="form-check-label" for="view-month">Month</label>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <button type="button" class="btn btn-link text-secondary text-decoration-none p-0" id="discard-calendar-btn" style="display: none;">Discard Changes</button>
        <span id="calendar-settings-message" class="ms-3"></span>
    </div>
    <button type="button" class="btn btn-primary" id="save-calendar-btn">Save Changes</button>
</div>

@push('scripts')
    @vite('resources/js/instructor-settings-calendar.js')
@endpush
@endsection
