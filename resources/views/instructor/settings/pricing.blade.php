@extends('layouts.instructor')

@section('title', 'Pricing')
@section('heading', 'Settings › Pricing')

@section('content')

<div class="sett-page">
@include('instructor.settings.partials.header', [
    'current'     => 'pricing',
    'title'       => 'Lesson & Package Pricing',
    'description' => 'Set hourly rates for Secure Licence learners and your own private learners. Includes test package pricing.',
])

<div class="sett-callout">
    <i class="bi bi-info-circle-fill"></i>
    <div>
        <strong>Secure Licence learners</strong> come from our marketplace.
        <strong>Private learners</strong> are people you've invited yourself (your existing clients).
        You can charge different rates for each.
    </div>
</div>

<div id="pricing-loading" class="sett-loading">
    <div class="spinner-border spinner-border-sm text-warning me-2"></div>Loading pricing…
</div>

<div id="pricing-content" style="display: none;">

    {{-- ─── Lesson Pricing ─── --}}
    <div class="sett-card">
        <div class="sett-card-body">
            <div class="sett-rate-header">
                <div class="sett-rate-icon"><i class="bi bi-car-front-fill"></i></div>
                <div>
                    <h3 class="sett-section-title">Hourly Lessons</h3>
                    <p class="sett-section-desc mb-0">Rate per booking hour. Most instructors charge $65 – $85/hr.</p>
                </div>
            </div>

            <div class="row g-3 mt-3">
                {{-- Secure Licence rate --}}
                <div class="col-md-6">
                    <div class="sett-rate-card sett-rate-marketplace">
                        <div class="sett-rate-card-head">
                            <span class="sett-rate-tag sett-rate-tag-sl"><i class="bi bi-shop-window"></i>Secure Licence</span>
                            <button type="button" class="sett-rate-edit-btn lesson-securelicence-edit" aria-label="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>
                        <div class="sett-rate-display">
                            <span class="sett-rate-currency">$</span>
                            <span class="sett-rate-amount" id="lesson-securelicence-display">0.00</span>
                            <span class="sett-rate-per">/hr</span>
                        </div>
                        <div id="lesson-securelicence-edit-wrap" class="sett-rate-edit-wrap d-none">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="lesson-securelicence-input" class="form-control" min="0" step="0.01" placeholder="0.00">
                                <span class="input-group-text">/hr</span>
                            </div>
                            <div class="sett-rate-edit-actions">
                                <button type="button" class="btn btn-sm btn-warning fw-bold lesson-securelicence-save">Save</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary lesson-securelicence-cancel">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Private rate --}}
                <div class="col-md-6">
                    <div class="sett-rate-card sett-rate-private">
                        <div class="sett-rate-card-head">
                            <span class="sett-rate-tag sett-rate-tag-pv"><i class="bi bi-person-heart"></i>Private learners</span>
                            <button type="button" class="sett-rate-edit-btn lesson-private-edit" aria-label="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>
                        <div class="sett-rate-display">
                            <span class="sett-rate-currency">$</span>
                            <span class="sett-rate-amount" id="lesson-private-display">0.00</span>
                            <span class="sett-rate-per">/hr</span>
                        </div>
                        <div id="lesson-private-edit-wrap" class="sett-rate-edit-wrap d-none">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="lesson-private-input" class="form-control" min="0" step="0.01" placeholder="0.00">
                                <span class="input-group-text">/hr</span>
                            </div>
                            <div class="sett-rate-edit-actions">
                                <button type="button" class="btn btn-sm btn-warning fw-bold lesson-private-save">Save</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary lesson-private-cancel">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Test Package Pricing ─── --}}
    <div class="sett-card">
        <div class="sett-card-body">
            <div class="sett-rate-header">
                <div class="sett-rate-icon sett-rate-icon-blue"><i class="bi bi-patch-check-fill"></i></div>
                <div>
                    <h3 class="sett-section-title">Test Day Package</h3>
                    <p class="sett-section-desc mb-0">Pick-up 1 hour before, 45-min warm-up lesson, use of car for the test, drop-off after result.</p>
                </div>
            </div>

            <div class="row g-3 mt-3">
                {{-- Secure Licence rate --}}
                <div class="col-md-6">
                    <div class="sett-rate-card sett-rate-marketplace">
                        <div class="sett-rate-card-head">
                            <span class="sett-rate-tag sett-rate-tag-sl"><i class="bi bi-shop-window"></i>Secure Licence</span>
                            <button type="button" class="sett-rate-edit-btn test-securelicence-edit" aria-label="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>
                        <div class="sett-rate-display">
                            <span class="sett-rate-currency">$</span>
                            <span class="sett-rate-amount" id="test-securelicence-display">0.00</span>
                            <span class="sett-rate-per">/package</span>
                        </div>
                        <div id="test-securelicence-edit-wrap" class="sett-rate-edit-wrap d-none">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="test-securelicence-input" class="form-control" min="0" step="0.01" placeholder="0.00">
                            </div>
                            <div class="sett-rate-edit-actions">
                                <button type="button" class="btn btn-sm btn-warning fw-bold test-securelicence-save">Save</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary test-securelicence-cancel">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Private rate --}}
                <div class="col-md-6">
                    <div class="sett-rate-card sett-rate-private">
                        <div class="sett-rate-card-head">
                            <span class="sett-rate-tag sett-rate-tag-pv"><i class="bi bi-person-heart"></i>Private learners</span>
                            <button type="button" class="sett-rate-edit-btn test-private-edit" aria-label="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>
                        <div class="sett-rate-display">
                            <span class="sett-rate-currency">$</span>
                            <span class="sett-rate-amount" id="test-private-display">0.00</span>
                            <span class="sett-rate-per">/package</span>
                        </div>
                        <div id="test-private-edit-wrap" class="sett-rate-edit-wrap d-none">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="test-private-input" class="form-control" min="0" step="0.01" placeholder="0.00">
                            </div>
                            <div class="sett-rate-edit-actions">
                                <button type="button" class="btn btn-sm btn-warning fw-bold test-private-save">Save</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary test-private-cancel">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <span id="pricing-message" class="sett-save-bar-msg success"></span>
    </div>
</div>

@push('scripts')
    @vite('resources/js/instructor-settings-pricing.js')
@endpush

</div> {{-- /.sett-page --}}
@endsection
