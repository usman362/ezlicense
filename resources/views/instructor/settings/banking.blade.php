@extends('layouts.instructor')

@section('title', 'Banking')
@section('heading', 'Settings › Banking')

@section('content')

<div class="sett-page">
@include('instructor.settings.partials.header', [
    'current'     => 'banking',
    'title'       => 'Banking & Payouts',
    'description' => 'Where we deposit your weekly earnings. Direct-deposit to any Australian bank account.',
])

<div class="sett-callout">
    <i class="bi bi-shield-fill-check"></i>
    <div>For secure payouts, please confirm your details below are correct. Payouts run every Tuesday for the previous week.</div>
</div>

<div id="banking-loading" class="sett-loading">
    <div class="spinner-border spinner-border-sm text-warning me-2"></div>Loading banking details…
</div>

<div id="banking-content" style="display: none;">

    {{-- ─── Billing Info ─── --}}
    <div class="sett-card">
        <div class="sett-card-body">
            <div class="sett-rate-header">
                <div class="sett-rate-icon sett-rate-icon-blue"><i class="bi bi-building-fill"></i></div>
                <div class="flex-grow-1">
                    <h3 class="sett-section-title">Billing Info</h3>
                    <p class="sett-section-desc mb-0">Your business / sole trader details used on payout statements and invoices.</p>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="billing-edit-btn">
                    <i class="bi bi-pencil-fill me-1"></i>Edit
                </button>
            </div>

            <div id="billing-view" class="mt-3">
                <dl class="sett-kv">
                    <dt>Business name</dt><dd id="billing-business-name">—</dd>
                    <dt>ABN</dt><dd id="billing-abn">—</dd>
                    <dt>Billing address</dt><dd id="billing-address">—</dd>
                    <dt>GST registered</dt><dd id="billing-gst">—</dd>
                    <dt>Suburb</dt><dd id="billing-suburb">—</dd>
                    <dt>Postcode</dt><dd id="billing-postcode">—</dd>
                    <dt>State</dt><dd id="billing-state">—</dd>
                </dl>
            </div>

            <form id="billing-form" class="d-none mt-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Business name</label>
                        <input type="text" name="business_name" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ABN</label>
                        <input type="text" name="abn" class="form-control" maxlength="20">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Billing address <span class="text-danger">*</span></label>
                        <input type="text" name="billing_address" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Is your business registered for GST?</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <label class="sett-radio-card sett-radio-card-sm">
                                <input type="radio" name="gst_registered" value="1">
                                <span class="sett-radio-content"><span class="sett-radio-label">Yes</span></span>
                            </label>
                            <label class="sett-radio-card sett-radio-card-sm">
                                <input type="radio" name="gst_registered" value="0">
                                <span class="sett-radio-content"><span class="sett-radio-label">No</span></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Suburb <span class="text-danger">*</span></label>
                        <input type="text" name="billing_suburb" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Postcode <span class="text-danger">*</span></label>
                        <input type="text" name="billing_postcode" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <select name="billing_state" class="form-select">
                            <option value="">Select state</option>
                            <option value="NSW">New South Wales</option>
                            <option value="VIC">Victoria</option>
                            <option value="QLD">Queensland</option>
                            <option value="WA">Western Australia</option>
                            <option value="SA">South Australia</option>
                            <option value="TAS">Tasmania</option>
                            <option value="ACT">ACT</option>
                            <option value="NT">Northern Territory</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-warning fw-bold">
                            <i class="bi bi-check-lg me-1"></i>Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── Payout Frequency ─── --}}
    <div class="sett-card">
        <div class="sett-card-body">
            <div class="sett-rate-header">
                <div class="sett-rate-icon sett-rate-icon-green"><i class="bi bi-calendar-check-fill"></i></div>
                <div class="flex-grow-1">
                    <h3 class="sett-section-title">Payout Frequency</h3>
                    <p class="sett-section-desc mb-0">How often we deposit your earnings. Change anytime.</p>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="payout-edit-btn">
                    <i class="bi bi-pencil-fill me-1"></i>Edit
                </button>
            </div>

            <div id="payout-view" class="mt-3">
                <div class="sett-payout-current">
                    <i class="bi bi-clock-history"></i>
                    <span>Currently set to <strong id="payout-frequency-display">—</strong></span>
                </div>
            </div>

            <form id="payout-form" class="d-none mt-3">
                <div class="sett-radio-group sett-radio-group-3">
                    <label class="sett-radio-card">
                        <input type="radio" name="payout_frequency" value="weekly">
                        <span class="sett-radio-content">
                            <i class="bi bi-lightning-charge-fill sett-radio-icon"></i>
                            <span class="sett-radio-label">Weekly</span>
                            <span class="sett-radio-desc">Get paid every Tuesday</span>
                        </span>
                    </label>
                    <label class="sett-radio-card">
                        <input type="radio" name="payout_frequency" value="fortnightly">
                        <span class="sett-radio-content">
                            <i class="bi bi-calendar2-week-fill sett-radio-icon"></i>
                            <span class="sett-radio-label">Fortnightly</span>
                            <span class="sett-radio-desc">Every 2 weeks</span>
                        </span>
                    </label>
                    <label class="sett-radio-card">
                        <input type="radio" name="payout_frequency" value="every_four_weeks">
                        <span class="sett-radio-content">
                            <i class="bi bi-calendar3 sett-radio-icon"></i>
                            <span class="sett-radio-label">Monthly</span>
                            <span class="sett-radio-desc">Every 4 weeks</span>
                        </span>
                    </label>
                </div>
                <div class="sett-callout mt-3" style="background: #ecfdf5; border-color: #a7f3d0; color: #065f46;">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>
                        Your earnings accrue until your scheduled payout. Each payout incurs a small <strong>0.39% + $0.28</strong> processing fee, plus a <strong>$2.20</strong> monthly account fee on your first payout each month.
                        <a href="#" class="d-block mt-1">View full fee breakdown →</a>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning fw-bold mt-2">
                    <i class="bi bi-check-lg me-1"></i>Save Changes
                </button>
            </form>
        </div>
    </div>

    {{-- ─── Bank Account ─── --}}
    <div class="sett-card">
        <div class="sett-card-body">
            <div class="sett-rate-header">
                <div class="sett-rate-icon" style="background: linear-gradient(135deg, #0ea5e9, #0369a1);"><i class="bi bi-bank"></i></div>
                <div>
                    <h3 class="sett-section-title">Bank Account</h3>
                    <p class="sett-section-desc mb-0">Where we send your payouts. Details cannot be changed after submission for security.</p>
                </div>
            </div>

            <div id="bank-view" class="mt-3">
                <dl class="sett-kv">
                    <dt>Account name</dt><dd id="bank-account-name">—</dd>
                    <dt>BSB</dt><dd id="bank-bsb">—</dd>
                    <dt>Account number</dt><dd id="bank-account-masked">—</dd>
                </dl>
                <div class="sett-callout mt-3" style="background: var(--sl-gray-50, #f9fafb); border-color: var(--sl-gray-200, #e5e7eb); color: var(--sl-gray-700, #374151);">
                    <i class="bi bi-shield-lock-fill"></i>
                    <div>
                        Bank account details cannot be edited after submission for security reasons.
                        Visit our <a href="https://support.securelicences.com.au/au" target="_blank" rel="noopener">support centre</a> to request a change.
                    </div>
                </div>
            </div>

            <form id="bank-form" class="d-none mt-3">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Account name</label>
                        <input type="text" name="bank_account_name" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">BSB</label>
                        <input type="text" name="bank_bsb" class="form-control" maxlength="10" placeholder="e.g. 013-260">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Account number</label>
                        <input type="text" name="bank_account_number" class="form-control" maxlength="20">
                    </div>
                    <div class="col-12">
                        <div class="sett-callout" style="background: #fef3c7; border-color: #fcd34d; color: #92400e;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>Bank account details cannot be changed after submission. Double-check before submitting.</div>
                        </div>
                        <button type="submit" class="btn btn-warning fw-bold">
                            <i class="bi bi-shield-fill-check me-1"></i>Submit Bank Details
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="text-center">
    <span id="banking-message" class="sett-save-bar-msg success"></span>
</div>

@push('scripts')
    @vite('resources/js/instructor-settings-banking.js')
@endpush

</div> {{-- /.sett-page --}}
@endsection
