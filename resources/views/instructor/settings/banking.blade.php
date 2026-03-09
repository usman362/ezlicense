@extends('layouts.instructor')

@section('title', 'Banking')
@section('heading', 'Settings › Banking')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.settings.personal-details') }}">Settings</a></li>
        <li class="breadcrumb-item active" aria-current="page">Banking</li>
    </ol>
</nav>

<ul class="nav nav-tabs border-0 small mb-4">
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.personal-details') }}">Personal Details</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.profile') }}">Profile</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.vehicle') }}">Vehicles</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.service-area') }}">Service Area</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.opening-hours') }}">Opening Hours</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.calendar-settings') }}">Calendar Settings</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.pricing') }}">Pricing</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.documents') }}">Documents</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('instructor.settings.banking') }}">Banking</a></li>
</ul>

<div class="alert alert-light border mb-4">
    <i class="bi bi-info-circle me-2"></i>
    For payout reasons, please confirm your details below to make sure they're correct.
</div>

<div id="banking-loading" class="text-muted">Loading…</div>

<div id="banking-content" style="display: none;">
    {{-- Billing Info --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Billing Info</h6>
                <button type="button" class="btn btn-sm btn-link p-0 text-primary" id="billing-edit-btn">Edit</button>
            </div>
            <div id="billing-view">
                <p class="mb-1"><strong>Business name:</strong> <span id="billing-business-name">—</span></p>
                <p class="mb-1"><strong>ABN:</strong> <span id="billing-abn">—</span></p>
                <p class="mb-1"><strong>Billing address:</strong> <span id="billing-address">—</span></p>
                <p class="mb-1"><strong>GST registered:</strong> <span id="billing-gst">—</span></p>
                <p class="mb-1"><strong>Suburb:</strong> <span id="billing-suburb">—</span> <strong>Postcode:</strong> <span id="billing-postcode">—</span> <strong>State:</strong> <span id="billing-state">—</span></p>
            </div>
            <form id="billing-form" class="d-none">
                <div class="mb-2">
                    <label class="form-label">Business name</label>
                    <input type="text" name="business_name" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">ABN</label>
                    <input type="text" name="abn" class="form-control" maxlength="20">
                </div>
                <div class="mb-2">
                    <label class="form-label">Billing address <span class="text-danger">*</span></label>
                    <input type="text" name="billing_address" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">Is your business registered for GST?</label>
                    <div class="d-flex gap-3">
                        <div class="form-check"><input type="radio" name="gst_registered" class="form-check-input" value="1"><label class="form-check-label">Yes</label></div>
                        <div class="form-check"><input type="radio" name="gst_registered" class="form-check-input" value="0"><label class="form-check-label">No</label></div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4"><label class="form-label">Suburb <span class="text-danger">*</span></label><input type="text" name="billing_suburb" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Postcode <span class="text-danger">*</span></label><input type="text" name="billing_postcode" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">State <span class="text-danger">*</span></label><select name="billing_state" class="form-select"><option value="">Select</option><option value="NSW">New South Wales</option><option value="VIC">Victoria</option><option value="QLD">Queensland</option><option value="WA">Western Australia</option><option value="SA">South Australia</option><option value="TAS">Tasmania</option><option value="ACT">ACT</option><option value="NT">Northern Territory</option></select></div>
                </div>
                <button type="submit" class="btn btn-warning text-dark">Save Changes</button>
            </form>
        </div>
    </div>

    {{-- Payout Frequency --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Set Your Payout Frequency</h6>
                <button type="button" class="btn btn-sm btn-link p-0 text-primary" id="payout-edit-btn">Edit</button>
            </div>
            <p class="small text-muted mb-2">You can change this at any time</p>
            <div id="payout-view">
                <p class="mb-0"><strong id="payout-frequency-display">—</strong></p>
            </div>
            <form id="payout-form" class="d-none">
                <div class="mb-2">
                    <div class="form-check"><input type="radio" name="payout_frequency" class="form-check-input" value="weekly"><label class="form-check-label">Weekly</label></div>
                    <div class="form-check"><input type="radio" name="payout_frequency" class="form-check-input" value="fortnightly"><label class="form-check-label">Fortnightly</label></div>
                    <div class="form-check"><input type="radio" name="payout_frequency" class="form-check-input" value="every_four_weeks"><label class="form-check-label">Every 4 weeks</label></div>
                </div>
                <div class="alert alert-success small mb-2">
                    Your earnings will accrue until your scheduled payout.<br>
                    Each payout is subject to a Processing Fee of 0.39% + $0.28.<br>
                    Additionally, a $2.20 Monthly Account Fee applies to your first payout each month.<br>
                    <a href="#">For more information, please refer to the guide on payout processing fees.</a>
                </div>
                <button type="submit" class="btn btn-warning text-dark">Save Changes</button>
            </form>
        </div>
    </div>

    {{-- Bank Account --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Bank Account</h6>
            <div id="bank-view">
                <p class="mb-1"><strong>Account name:</strong> <span id="bank-account-name">—</span></p>
                <p class="mb-1"><strong>BSB:</strong> <span id="bank-bsb">—</span></p>
                <p class="mb-1"><strong>Account number:</strong> <span id="bank-account-masked">—</span></p>
                <p class="small text-muted mb-0 mt-2">Please note that bank account details cannot be edited after submission. Please visit our support centre at <a href="https://support.ezlicence.com.au/au" target="_blank" rel="noopener">support.ezlicence.com.au/au</a> to request an update.</p>
            </div>
            <form id="bank-form" class="d-none">
                <div class="mb-2"><label class="form-label">Account name</label><input type="text" name="bank_account_name" class="form-control"></div>
                <div class="mb-2"><label class="form-label">BSB</label><input type="text" name="bank_bsb" class="form-control" maxlength="10" placeholder="e.g. 013260"></div>
                <div class="mb-2"><label class="form-label">Account number</label><input type="text" name="bank_account_number" class="form-control" maxlength="20"></div>
                <p class="small text-muted mb-2">Bank account details cannot be changed after submission.</p>
                <button type="submit" class="btn btn-warning text-dark">Submit</button>
            </form>
        </div>
    </div>
</div>

<span id="banking-message" class="text-success"></span>

@push('scripts')
    @vite('resources/js/instructor-settings-banking.js')
@endpush
@endsection
