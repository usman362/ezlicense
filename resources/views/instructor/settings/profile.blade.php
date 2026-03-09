@extends('layouts.instructor')

@section('title', 'Profile')
@section('heading', 'Settings › Profile')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.settings.personal-details') }}">Settings</a></li>
        <li class="breadcrumb-item active" aria-current="page">Profile</li>
    </ol>
</nav>

<ul class="nav nav-tabs border-0 small mb-4">
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.personal-details') }}">Personal Details</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('instructor.settings.profile') }}">Profile</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.vehicle') }}">Vehicle</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.service-area') }}">Service Area</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.opening-hours') }}">Opening Hours</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.calendar-settings') }}">Calendar Settings</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.pricing') }}">Pricing</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.documents') }}">Documents</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.banking') }}">Banking</a></li>
</ul>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div id="profile-loading" class="text-muted">Loading…</div>
        <form id="profile-form" style="display: none;">
            <h6 class="fw-bold mb-1">My Profile</h6>
            <p class="small text-muted mb-3">This is the public profile information viewable by learners as they choose their instructor.</p>

            <div class="mb-3">
                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white mb-2" style="width:80px;height:80px;">
                    <i class="bi bi-person fs-2"></i>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Your instructor bio</label>
                <textarea name="bio" id="profile-bio" class="form-control" rows="5" placeholder="Tell learners about your experience and approach..." maxlength="1600"></textarea>
                <div class="small text-muted mt-1"><span id="profile-bio-count">0</span> / 1600 characters</div>
            </div>

            <div class="mb-4">
                <label class="form-label">Profile Link</label>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <input type="text" id="profile-link-input" class="form-control flex-grow-1" readonly>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="profile-copy-link">Copy Link</button>
                </div>
                <p class="small text-muted mt-1 mb-0">Learners who book through this link will count towards your 'Private Learners' at a reduced rate. <a href="#">Learn More</a></p>
            </div>

            <div class="mb-4">
                <label class="form-label">Enter any languages you speak fluently</label>
                <div class="border rounded p-2 bg-light d-flex flex-wrap gap-2 align-items-center" id="profile-languages-wrap">
                    <input type="text" id="profile-language-input" class="form-control form-control-sm border-0 bg-transparent flex-grow-1" style="min-width:120px;" placeholder="Type and press Enter" maxlength="50">
                </div>
                <div id="profile-language-tags" class="d-flex flex-wrap gap-1 mt-2"></div>
                <small class="text-muted">Suggested: English, Hindi, Panjabi, Urdu, Mandarin, Arabic</small>
            </div>

            <div class="mb-4">
                <label class="form-label">Member of a driving instructor association?</label>
                <select name="association_member" class="form-select" style="max-width:200px;">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">When did you start instructing?</label>
                <div class="d-flex gap-2 flex-wrap">
                    <select name="instructing_start_month" class="form-select" style="max-width:140px;">
                        <option value="">Month</option>
                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $i => $m)
                            <option value="{{ $i + 1 }}">{{ $m }}</option>
                        @endforeach
                    </select>
                    <select name="instructing_start_year" class="form-select" style="max-width:100px;">
                        <option value="">Year</option>
                        @for($y = (int)date('Y'); $y >= 1990; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">What service(s) do you offer</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input type="checkbox" name="service_test_existing" class="form-check-input" id="svc-test-existing" value="1">
                        <label class="form-check-label" for="svc-test-existing">Driving test package: existing customers</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="service_test_new" class="form-check-input" id="svc-test-new" value="1">
                        <label class="form-check-label" for="svc-test-new">Driving test package: new customers</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="service_manual_no_vehicle" class="form-check-input" id="svc-manual-no-vehicle" value="1">
                        <label class="form-check-label" for="svc-manual-no-vehicle">Manual Instructor accredited - no vehicle</label>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Notification Preferences</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input type="checkbox" name="notification_email_marketing" class="form-check-input" id="notif-email" value="1">
                        <label class="form-check-label" for="notif-email">Email: Marketing Communications and special offers</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="notification_sms_marketing" class="form-check-input" id="notif-sms" value="1">
                        <label class="form-check-label" for="notif-sms">SMS: Marketing Communications and special offers</label>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">EzLicence Marketplace</label>
                <p class="small text-muted mb-2">Your profile is discoverable by Learners on EzLicence marketplace search results.</p>
                <div class="form-check form-switch">
                    <input type="checkbox" name="is_active" class="form-check-input" id="profile-marketplace" value="1">
                    <label class="form-check-label" for="profile-marketplace">Discoverable on marketplace</label>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <span id="profile-message" class="me-3 align-self-center"></span>
                <button type="submit" class="btn btn-warning text-dark fw-medium">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
    @vite('resources/js/instructor-settings-profile.js')
@endpush
@endsection
