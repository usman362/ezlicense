@extends('layouts.instructor')

@section('title', 'Calendar Settings')
@section('heading', 'Settings › Calendar Settings')

@section('content')

<div class="sett-page">
@include('instructor.settings.partials.header', [
    'current'     => 'calendar-settings',
    'title'       => 'Calendar Settings',
    'description' => 'Booking notice, travel buffers, smart scheduling and how bookings appear in your calendar.',
])

<div id="calendar-settings-loading" class="sett-loading">
    <div class="spinner-border spinner-border-sm text-warning me-2"></div>Loading calendar settings…
</div>

<form id="calendar-settings-form" style="display: none;">

    {{-- ─── Lesson Duration ─── --}}
    <div class="sett-card">
        <div class="sett-card-body">
            <div class="sett-rate-header">
                <div class="sett-rate-icon sett-rate-icon-orange"><i class="bi bi-clock-history"></i></div>
                <div>
                    <h3 class="sett-section-title">Lesson Duration</h3>
                    <p class="sett-section-desc mb-0">Lesson lengths you offer. <strong>1h and 2h are required.</strong></p>
                </div>
            </div>
            <div class="sett-duration-grid mt-3">
                @php
                    $durationOptions = [
                        ['mins' => 60,  'label' => '1hr',   'required' => true],
                        ['mins' => 90,  'label' => '1.5hr', 'required' => false],
                        ['mins' => 120, 'label' => '2hr',   'required' => true],
                        ['mins' => 180, 'label' => '3hr',   'required' => false],
                        ['mins' => 240, 'label' => '4hr',   'required' => false],
                        ['mins' => 300, 'label' => '5hr',   'required' => false],
                    ];
                @endphp
                @foreach ($durationOptions as $opt)
                    <label class="sett-duration-card {{ $opt['required'] ? 'is-required' : '' }}">
                        <input type="checkbox"
                               name="lesson_durations[]"
                               value="{{ $opt['mins'] }}"
                               class="sett-duration-check"
                               data-required="{{ $opt['required'] ? '1' : '0' }}"
                               @if($opt['required']) checked disabled @endif>
                        <span class="sett-duration-label">{{ $opt['label'] }}</span>
                        @if($opt['required'])
                            <span class="sett-duration-required-badge">Required</span>
                        @endif
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ─── Travel Buffer ─── --}}
    <div class="sett-card">
        <div class="sett-card-body">
            <div class="sett-rate-header">
                <div class="sett-rate-icon"><i class="bi bi-stopwatch-fill"></i></div>
                <div>
                    <h3 class="sett-section-title">Travel Buffer</h3>
                    <p class="sett-section-desc mb-0">How much breathing room between back-to-back lessons.</p>
                </div>
            </div>
            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Between same-transmission lessons</label>
                    <select name="travel_buffer_same_mins" class="form-select">
                        <option value="0">No buffer</option>
                        <option value="15">15 mins</option>
                        <option value="30">30 mins</option>
                        <option value="45">45 mins</option>
                        <option value="60">60 mins</option>
                        <option value="90">90 mins</option>
                        <option value="120">120 mins</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Between Secure Licence &amp; synced events</label>
                    <select name="travel_buffer_synced_mins" class="form-select">
                        <option value="0">No buffer</option>
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
    </div>

    {{-- ─── Scheduling Window ─── --}}
    <div class="sett-card">
        <div class="sett-card-body">
            <div class="sett-rate-header">
                <div class="sett-rate-icon sett-rate-icon-blue"><i class="bi bi-calendar-range-fill"></i></div>
                <div>
                    <h3 class="sett-section-title">Booking Window</h3>
                    <p class="sett-section-desc mb-0">How far in advance &mdash; or how close to last-minute &mdash; learners can book.</p>
                </div>
            </div>
            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Minimum advance notice</label>
                    <select name="min_prior_notice_hours" class="form-select">
                        <option value="0">No minimum (instant bookings)</option>
                        <option value="1">1 hour</option>
                        <option value="2">2 hours</option>
                        <option value="3">3 hours</option>
                        <option value="5">5 hours</option>
                        <option value="12">12 hours</option>
                        <option value="24">24 hours</option>
                        <option value="48">48 hours</option>
                        <option value="168">7 days</option>
                    </select>
                    <div class="form-text">Block bookings that come in less than this much notice.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Maximum advance booking</label>
                    <select name="max_advance_notice_days" class="form-select">
                        <option value="7">7 days ahead</option>
                        <option value="14">14 days ahead</option>
                        <option value="30">30 days ahead</option>
                        <option value="45">45 days ahead</option>
                        <option value="60">60 days ahead</option>
                        <option value="75">75 days ahead</option>
                        <option value="90">90 days ahead</option>
                        <option value="180">180 days ahead</option>
                        <option value="365">365 days ahead</option>
                    </select>
                    <div class="form-text">How far into the future learners can book.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Smart Scheduling ─── --}}
    <div class="sett-card">
        <div class="sett-card-body">
            <div class="sett-rate-header">
                <div class="sett-rate-icon sett-rate-icon-green"><i class="bi bi-magic"></i></div>
                <div>
                    <h3 class="sett-section-title">Smart Scheduling</h3>
                    <p class="sett-section-desc mb-0">Reduces awkward time gaps between bookings by clustering them together.</p>
                </div>
            </div>
            <div class="sett-toggle-row mt-3">
                <div class="form-check form-switch sett-switch">
                    <input type="checkbox" name="smart_scheduling_enabled" class="form-check-input" id="smart-scheduling" value="1">
                    <label class="form-check-label fw-semibold" for="smart-scheduling">Enable Smart Scheduling</label>
                </div>
                <div class="sett-btn-group" id="smart-scheduling-buffer-wrap">
                    <span class="sett-btn-group-label">Cluster within:</span>
                    <div class="btn-group" role="group">
                        <input type="radio" name="smart_scheduling_buffer_hrs" class="btn-check" id="buffer-1hr" value="1" autocomplete="off">
                        <label class="btn btn-sett-pill" for="buffer-1hr">1 hour</label>
                        <input type="radio" name="smart_scheduling_buffer_hrs" class="btn-check" id="buffer-2hr" value="2" autocomplete="off">
                        <label class="btn btn-sett-pill" for="buffer-2hr">2 hours</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Email Calendar Attachments ─── --}}
    <div class="sett-card">
        <div class="sett-card-body">
            <div class="sett-rate-header">
                <div class="sett-rate-icon sett-rate-icon-purple"><i class="bi bi-envelope-paper-fill"></i></div>
                <div>
                    <h3 class="sett-section-title">Email Calendar Events</h3>
                    <p class="sett-section-desc mb-0">Attach a calendar invite (.ics) to every booking email.</p>
                </div>
            </div>
            <div class="sett-callout mt-3" style="background: #fef9c3; border-color: #fde047; color: #854d0e;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>If your email app adds events automatically — or your calendar is already synced — you may get duplicate events.</div>
            </div>
            <div class="sett-radio-group mt-3">
                <label class="sett-radio-card">
                    <input type="radio" name="attach_ics_to_emails" id="attach-ics-yes" value="1">
                    <span class="sett-radio-content">
                        <span class="sett-radio-label">Yes, attach calendar events</span>
                        <span class="sett-radio-desc">Send .ics with every email</span>
                    </span>
                </label>
                <label class="sett-radio-card">
                    <input type="radio" name="attach_ics_to_emails" id="attach-ics-no" value="0">
                    <span class="sett-radio-content">
                        <span class="sett-radio-label">No, don't attach</span>
                        <span class="sett-radio-desc">Just plain text emails</span>
                    </span>
                </label>
            </div>
        </div>
    </div>

    {{-- ─── Default Calendar View ─── --}}
    <div class="sett-card">
        <div class="sett-card-body">
            <div class="sett-rate-header">
                <div class="sett-rate-icon"><i class="bi bi-grid-3x2-gap-fill"></i></div>
                <div>
                    <h3 class="sett-section-title">Default Calendar View</h3>
                    <p class="sett-section-desc mb-0">How your calendar opens by default on desktop. Mobile always opens to day view.</p>
                </div>
            </div>
            <div class="sett-radio-group sett-radio-group-3 mt-3">
                <label class="sett-radio-card">
                    <input type="radio" name="default_calendar_view" id="view-day" value="day">
                    <span class="sett-radio-content">
                        <i class="bi bi-calendar-day sett-radio-icon"></i>
                        <span class="sett-radio-label">Day</span>
                    </span>
                </label>
                <label class="sett-radio-card">
                    <input type="radio" name="default_calendar_view" id="view-week" value="week">
                    <span class="sett-radio-content">
                        <i class="bi bi-calendar-week sett-radio-icon"></i>
                        <span class="sett-radio-label">Week</span>
                    </span>
                </label>
                <label class="sett-radio-card">
                    <input type="radio" name="default_calendar_view" id="view-month" value="month">
                    <span class="sett-radio-content">
                        <i class="bi bi-calendar-month sett-radio-icon"></i>
                        <span class="sett-radio-label">Month</span>
                    </span>
                </label>
            </div>
        </div>
    </div>
</form>

{{-- ─── Calendar Sync (phone) ─── --}}
<div class="sett-card">
    <div class="sett-card-body">
        <div class="sett-rate-header">
            <div class="sett-rate-icon" style="background: linear-gradient(135deg, #6366f1, #4f46e5);"><i class="bi bi-phone-fill"></i></div>
            <div>
                <h3 class="sett-section-title">Sync with your phone</h3>
                <p class="sett-section-desc mb-0">New bookings, reschedules and cancellations appear automatically in your phone calendar.</p>
            </div>
        </div>

        <div id="calendar-sync-section" class="mt-3">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="#" id="apple-cal-btn" class="btn btn-outline-dark" target="_blank">
                    <i class="bi bi-apple me-1"></i>Apple Calendar
                </a>
                <a href="#" id="google-cal-btn" class="btn btn-outline-primary" target="_blank">
                    <i class="bi bi-google me-1"></i>Google Calendar
                </a>
                <button class="btn btn-outline-secondary" id="copy-cal-url-btn">
                    <i class="bi bi-clipboard me-1"></i>Copy URL
                </button>
            </div>

            <div class="sett-code-box mb-3">
                <i class="bi bi-link-45deg text-muted"></i>
                <code id="calendar-feed-url">Loading…</code>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button class="btn btn-outline-warning btn-sm" id="regenerate-cal-btn">
                    <i class="bi bi-arrow-clockwise me-1"></i>Regenerate URL
                </button>
                <span class="small text-muted">This invalidates any previously subscribed calendars.</span>
            </div>
        </div>
    </div>
</div>

<div class="sett-save-bar">
    <div>
        <button type="button" class="btn btn-link text-secondary text-decoration-none p-0" id="discard-calendar-btn" style="display: none;">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Discard changes
        </button>
        <span id="calendar-settings-message" class="sett-save-bar-msg ms-2"></span>
    </div>
    <button type="button" class="btn btn-warning fw-bold" id="save-calendar-btn">
        <i class="bi bi-check-lg me-1"></i>Save Changes
    </button>
</div>

@push('scripts')
    @vite('resources/js/instructor-settings-calendar.js')
@endpush

</div> {{-- /.sett-page --}}
@endsection
