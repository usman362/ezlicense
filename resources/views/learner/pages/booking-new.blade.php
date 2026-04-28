@extends(auth()->check() ? 'layouts.learner' : 'layouts.booking', ['step' => 3])

@section('title', 'Make a Booking')
@section('heading', 'Make a Booking')

@section('content')
@auth
    {{-- Logged-in learner: dashboard layout with breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Make a Booking</li>
        </ol>
    </nav>
    <div class="mb-4">
        <h3 class="fw-bolder mb-1" style="letter-spacing:-0.02em;">Make a Booking</h3>
    </div>
@else
    {{-- Guest: minimal layout with stepper banner --}}
    <div class="alert mb-4 text-center" style="background: var(--sl-accent-500); color: var(--sl-gray-900); border:none; font-weight:600;">
        Complete your purchase to secure your bookings and prices.
    </div>
    <div class="mb-4">
        <h3 class="fw-bolder mb-1" style="letter-spacing:-0.02em;">Book your lessons</h3>
        <p class="text-muted mb-0">Book now or later from your dashboard.</p>
    </div>
@endauth

<div class="row g-4">
    <div class="col-lg-8">
        {{-- My Instructor --}}
        @php
            $instructor = $instructorProfile->user;
            $profile = $instructorProfile;
            $rate = $profile->lesson_price !== null ? '$' . number_format((float) $profile->lesson_price, 0) . '/hr' : null;
            $vehicle = trim(implode(' ', array_filter([$profile->vehicle_make, $profile->vehicle_model, $profile->vehicle_year])));
        @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                @auth
                    <h5 class="fw-bold mb-3">My Instructor</h5>
                @else
                    <h6 class="text-muted small text-uppercase mb-1" style="letter-spacing:0.08em;">Booking with</h6>
                    <h5 class="fw-bold mb-3">Your Instructor</h5>
                @endauth
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 h-100" style="background: var(--sl-gray-50);">
                            @if($profile->profile_photo)
                                <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="{{ $instructor->name }}" class="rounded-circle" style="width:56px;height:56px;object-fit:cover;flex-shrink:0;">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bolder text-white" style="width:56px;height:56px;font-size:1.2rem;background:linear-gradient(135deg, var(--sl-primary-500), var(--sl-teal-500));flex-shrink:0;">{{ strtoupper(substr($instructor->name ?? 'I', 0, 1)) }}</div>
                            @endif
                            <div class="min-w-0 flex-grow-1">
                                <div class="fw-bolder text-truncate">{{ $instructor->name }}</div>
                                <a href="tel:{{ $instructor->phone }}" class="small text-decoration-none"><i class="bi bi-telephone me-1"></i>{{ $instructor->phone }}</a>
                                @if($rate)<div class="small text-muted">{{ $rate }}</div>@endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 h-100" style="background: var(--sl-gray-50);">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:#fff;border:1px solid var(--sl-gray-200);flex-shrink:0;"><i class="bi bi-car-front-fill" style="font-size:1.4rem;color:var(--sl-primary-600);"></i></div>
                            <div class="small flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">{{ $vehicle ?: 'Vehicle' }}</div>
                                <div class="text-muted">{{ ucfirst($profile->transmission ?? 'Auto') }} · 5-star ANCAP · Dual controls</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bookings (saved items table - shown after first Save) --}}
        <div class="card border-0 shadow-sm mb-4" id="bookings-list-card" style="display: none;">
            <div class="card-body p-0">
                <h6 class="fw-bold mb-0 p-4 pb-3">Bookings</h6>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle bookings-table">
                        <thead style="background: var(--sl-gray-50);">
                            <tr class="small text-muted">
                                <th class="ps-4 fw-normal">Booking</th>
                                <th class="fw-normal">Time</th>
                                <th class="fw-normal">Duration</th>
                                <th class="fw-normal">Price</th>
                                <th class="pe-4"></th>
                            </tr>
                        </thead>
                        <tbody id="bookings-list"></tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- "Add Another Booking" button (shown only when there's at least one saved booking + form is hidden) --}}
        <div class="text-center mb-4" id="add-another-row" style="display: none;">
            <button type="button" class="btn btn-outline-secondary" id="btn-show-new-form">
                <i class="bi bi-plus-lg me-1"></i> Add Another Booking
            </button>
        </div>

        {{-- Yellow Tip box (shown when there are saved bookings) --}}
        <div class="tip-box mb-4" id="tip-box" style="display: none;">
            <div class="d-flex align-items-start gap-3">
                <div class="tip-icon">
                    <i class="bi bi-emoji-smile-fill"></i>
                    <span class="tip-flag">Tip</span>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-2">Book all your lessons upfront to avoid price changes!</h6>
                    <p class="small mb-0">
                        Lock in your preferred times now to avoid price changes.<br>
                        Book all your lessons now at your Instructor's current rate.
                    </p>
                </div>
                <div class="d-none d-md-block">
                    <i class="bi bi-arrow-up-right text-muted" style="font-size:2rem;transform:rotate(-15deg);display:inline-block;"></i>
                </div>
            </div>
        </div>

        {{-- New Booking form (add more below saved bookings) --}}
        <div class="card border-0 shadow-sm mb-4" id="new-booking-form-card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">New Booking</h6>

                {{-- Booking type --}}
                <div class="btn-group w-100 mb-3" role="group">
                    <input type="radio" class="btn-check" name="booking_type" id="type-1hr" value="1hr" checked>
                    <label class="btn btn-outline-secondary" for="type-1hr">1-Hour Lesson</label>
                    <input type="radio" class="btn-check" name="booking_type" id="type-2hr" value="2hr">
                    <label class="btn btn-outline-secondary" for="type-2hr">2-Hour Lesson</label>
                    <input type="radio" class="btn-check" name="booking_type" id="type-test" value="test_package">
                    <label class="btn btn-outline-secondary" for="type-test">Driving Test Package</label>
                </div>

                <div id="test-package-warning" class="alert alert-warning small mb-3" style="display: none;">
                    WARNING: You must book your own driving test with local roads authority. Our Test Package only books the instructor and vehicle.
                </div>

                <div id="field-test-location" class="mb-3" style="display: none;">
                    <label class="form-label">Driving test location</label>
                    <select class="form-select" id="test_location">
                        <option value="">Select the test location</option>
                        @foreach($instructorProfile->serviceAreas ?? [] as $suburb)
                            <option value="{{ $suburb->id }}">{{ $suburb->name }}, {{ $suburb->postcode }} {{ $suburb->state?->code }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Available Dates</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                        <input type="text" class="form-control" id="booking_date" placeholder="Select an available date" readonly>
                        <input type="hidden" id="booking_date_iso">
                    </div>
                    <small class="text-muted" id="dates-loading-hint" style="display:none;">
                        <span class="spinner-border spinner-border-sm me-1" style="width:12px;height:12px;border-width:1.5px;"></span>
                        Loading instructor availability...
                    </small>
                </div>

                {{-- Hidden select holds the actual selected time value (used by save handler) --}}
                <select id="booking_time" class="d-none"><option value="">Select time</option></select>
                <input type="text" id="booking_time_display" class="d-none">

                {{-- Visual time slot grid (shown when a date is selected) --}}
                <div id="time-slot-picker" class="mb-3" style="display:none;">
                    <label class="form-label d-flex justify-content-between align-items-center">
                        <span>Available Times</span>
                        <small class="text-muted fw-normal" id="time-slot-count"></small>
                    </label>
                    <div class="card border-0" style="background:var(--sl-gray-50); border-radius:12px;">
                        <div class="card-body p-3" id="time-slot-content">
                            <div class="text-center text-muted small py-3" id="time-slot-loading">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Loading available times...
                            </div>
                        </div>
                    </div>
                </div>

                <p id="pickup-time-note" class="small text-muted mb-3" style="display: none;">The pickup time will be 1 hour prior to the test start time. You can reschedule this package any time up until 24 hrs before the pick up time.</p>

                <div class="border rounded p-3 mb-3">
                    <h6 class="small fw-bold mb-2">Lesson Pick Up Location</h6>

                    {{-- Service-area info banner — non-blocking warning --}}
                    <div class="alert alert-warning d-none" id="pickup-service-area-error">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Heads up — this pickup location is outside {{ $instructorProfile->user->name ?? 'this instructor' }}'s usual service area, but you can still book.
                        <a href="{{ route('find-instructor') }}" class="text-decoration-underline">Find another instructor</a> if you'd prefer one closer to <span id="pickup-error-suburb"></span>.
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label small"><span class="text-danger">*</span> Pick up address</label>
                        <input type="text" class="form-control" id="pickup_address"
                               placeholder="Enter a location (street, suburb, or postcode)" autocomplete="off">
                        <div class="suburb-ac-dropdown" id="pickup_address_ac"></div>
                        <small class="text-muted">Start typing — we'll find your address</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 position-relative">
                            <label class="form-label small"><span class="text-danger">*</span> Suburb</label>
                            <input type="text" class="form-control" id="pickup_suburb_search"
                                   placeholder="Type a suburb or postcode" autocomplete="off"
                                   data-suburb-search="pickup">
                            <div class="suburb-ac-dropdown" id="pickup_suburb_ac"></div>
                            <input type="hidden" id="pickup_suburb" name="pickup_suburb">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small"><span class="text-danger">*</span> State</label>
                            <select class="form-select" id="pickup_state" name="pickup_state">
                                <option value="">Select state</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div id="dropoff-section" class="border rounded p-3 mb-3" style="display: none;">
                    <h6 class="small fw-bold mb-2">Drop Off Location</h6>

                    <div class="alert alert-danger d-none" id="dropoff-service-area-error">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <strong>Sorry, {{ $instructorProfile->user->name ?? 'this instructor' }} doesn't service this drop-off address.</strong>
                        Choose a drop-off inside their service area or <span id="dropoff-error-suburb"></span>.
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label small">Drop off address</label>
                        <input type="text" class="form-control" id="dropoff_address"
                               placeholder="Enter a location (street, suburb, or postcode)" autocomplete="off">
                        <div class="suburb-ac-dropdown" id="dropoff_address_ac"></div>
                        <small class="text-muted">Start typing — we'll find your address</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 position-relative">
                            <label class="form-label small"><span class="text-danger">*</span> Suburb</label>
                            <input type="text" class="form-control" id="dropoff_suburb_search"
                                   placeholder="Type a suburb or postcode" autocomplete="off"
                                   data-suburb-search="dropoff">
                            <div class="suburb-ac-dropdown" id="dropoff_suburb_ac"></div>
                            <input type="hidden" id="dropoff_suburb" name="dropoff_suburb">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small"><span class="text-danger">*</span> State</label>
                            <select class="form-select" id="dropoff_state" name="dropoff_state">
                                <option value="">Select state</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between gap-2">
                    <button type="button" class="btn btn-outline-secondary" id="btn-cancel-booking" style="display:none;">Cancel</button>
                    <span></span>
                    <button type="button" class="btn btn-warning" id="btn-save-booking" disabled>Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Order Summary</h6>

                @if(!empty($package))
                    {{-- Package selected — show credit-based summary (EasyLicence-style) --}}
                    @php
                        $pkgSubtotal = ((float) $instructorProfile->lesson_price) * (int) $package['hours'];
                        $pkgDiscount = round($pkgSubtotal * (float) $package['discount_pct'] / 100, 2);
                        $pkgAfter = $pkgSubtotal - $pkgDiscount;
                    @endphp
                    <div class="d-flex justify-content-between align-items-center py-2 small">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-ticket-perforated"></i>
                            <span>{{ $package['hours'] }} hrs Booking Credit</span>
                        </span>
                        <span class="fw-semibold">${{ number_format($pkgSubtotal, 2) }}</span>
                    </div>
                    @if($package['discount_pct'] > 0)
                        <div class="d-flex justify-content-between align-items-center py-2 small">
                            <span>
                                Credit Discount
                                <span class="pkg-badge pkg-badge-discount ms-1" style="font-size:0.7rem;padding:0.1rem 0.45rem;background:#d1f4e1;color:#0b7b3c;font-weight:700;border-radius:12px;">{{ $package['discount_pct'] }}% OFF</span>
                            </span>
                            <span class="text-success fw-semibold">-${{ number_format($pkgDiscount, 2) }}</span>
                        </div>
                    @endif
                    @if(!empty($package['add_test_package']))
                        <div class="d-flex justify-content-between align-items-center py-2 small">
                            <span>
                                <i class="bi bi-check2-circle text-success me-1"></i>
                                Driving Test Package
                            </span>
                            <span class="fw-semibold">${{ number_format((float) $package['test_package_price'], 2) }}</span>
                        </div>
                    @endif
                    <div id="pkg-scheduled-info" class="mt-2 p-2 small rounded" style="background:#fffbeb;border:1px solid var(--sl-accent-500);">
                        <i class="bi bi-info-circle me-1"></i>
                        Scheduled: <strong id="scheduled-hours">0</strong> / {{ $package['hours'] }} hrs
                        @if(!empty($package['add_test_package']))
                            + Test Package
                        @endif
                    </div>
                    <hr>
                @endif

                <div id="order-items" class="mb-2 small"></div>
                <div id="order-empty" class="text-muted small mb-2">
                    @if(!empty($package))
                        Schedule your lesson time slots below.
                    @else
                        No bookings added yet. Fill the form and click Save.
                    @endif
                </div>
                <div id="order-totals" class="mb-3" style="display: none;">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Platform Processing Fee</span>
                        <span id="order-fee">$0.00</span>
                        <i class="bi bi-info-circle text-muted ms-1" title="4% processing fee" style="cursor: help;"></i>
                    </div>
                    <div class="d-flex justify-content-between fw-bold pt-2 border-top">
                        <span>Total Payment Due</span>
                        <span id="order-total">$0.00</span>
                    </div>
                    <p class="small text-muted mb-0 mt-1">Or 4 payments of <span id="order-instalment">$0.00</span></p>
                </div>
                <button type="button" class="btn btn-outline-secondary w-100 mb-2" id="btn-add-another">
                    <i class="bi bi-plus-lg me-1"></i> Add Another Booking
                </button>
                <a href="#" class="btn btn-warning w-100 fw-semibold" id="btn-continue">
                    Continue <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>

        @guest
            {{-- BNPL + Trust signals — guest-only (logged-in learners don't need them) --}}
            <div class="bnpl-panel">
                <div class="bnpl-title">
                    Buy Now Pay Later <i class="bi bi-info-circle text-muted small" title="Split your payment into 4 interest-free instalments"></i>
                </div>
                <div class="bnpl-amount">4 payments of <span id="bnpl-amount">$0.00</span></div>
                <div class="bnpl-badges">
                    <span class="bnpl-badge paypal"><i class="bi bi-paypal me-1"></i>Pay in 4</span>
                    <span class="bnpl-badge afterpay">afterpay&lt;&gt;</span>
                    <span class="bnpl-badge klarna">Klarna</span>
                </div>
            </div>

            <div class="trust-panel">
                <h6><i class="bi bi-shield-check text-success me-1"></i>Purchase With Peace Of Mind</h6>
                <p>Flexible rebooking if your plans change.</p>

                <h6><i class="bi bi-calendar2-check text-primary me-1"></i>Manage Your Lessons Online</h6>
                <p>24/7 access. Manage your account. Switch your instructor at no cost.</p>

                <h6><i class="bi bi-lock-fill text-warning me-1"></i>Secure Payments</h6>
                <p>We use 100% secure payments to provide you with a simple and safe experience.</p>
            </div>
        @endguest
    </div>
</div>

<input type="hidden" id="instructor_profile_id" value="{{ $instructorProfile->id }}">
<input type="hidden" id="instructor_transmission" value="{{ strtolower($profile->transmission ?? 'auto') }}">
<input type="hidden" id="lesson_price" value="{{ (float) ($profile->lesson_price ?? 65) }}">
<input type="hidden" id="test_package_price" value="{{ (float) ($profile->test_package_price ?? 225) }}">
<input type="hidden" id="package_hours" value="{{ $package['hours'] ?? '' }}">
<input type="hidden" id="package_discount_pct" value="{{ $package['discount_pct'] ?? 0 }}">
<input type="hidden" id="package_add_test" value="{{ ($package['add_test_package'] ?? false) ? '1' : '0' }}">
<input type="hidden" id="package_test_price" value="{{ $package['test_package_price'] ?? 0 }}">
<input type="hidden" id="instructor_service_area_ids" value="{{ json_encode($instructorProfile->serviceAreas->pluck('id')->all()) }}">
@if(!empty($googleMapsApiKey))
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places&callback=Function.prototype" async defer></script>
@endif

{{-- Tom Select for searchable suburb/state dropdowns --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
<style>
    .ts-wrapper .ts-control { border-radius: var(--sl-radius, 0.375rem); border-color: var(--sl-gray-300, #dee2e6); min-height: calc(1.5em + 0.75rem + 2px); padding: 0.375rem 0.75rem; }
    .ts-wrapper.focus .ts-control { border-color: var(--sl-accent-500, #ffd500); box-shadow: 0 0 0 0.2rem rgba(255,213,0,0.15); }
    .ts-dropdown { border-radius: var(--sl-radius, 0.375rem); box-shadow: 0 10px 40px rgba(0,0,0,0.12); border: 1px solid var(--sl-gray-200, #e5e7eb); }
    .ts-dropdown .option.active { background-color: var(--sl-accent-500, #ffd500); color: var(--sl-gray-900, #111827); }
    .ts-dropdown .option:hover { background: #fffbeb; }
    .ts-wrapper.single .ts-control:after { border-top-color: var(--sl-gray-500, #6b7280); }

    /* Address autocomplete dropdown */
    .suburb-ac-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        background: #fff;
        border: 1px solid var(--sl-gray-200, #e5e7eb);
        border-radius: 0.5rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        max-height: 320px;
        overflow-y: auto;
        display: none;
        margin-top: 2px;
    }
    .suburb-ac-dropdown.show { display: block; }
    .suburb-ac-item {
        padding: 0.6rem 0.875rem;
        cursor: pointer;
        border-bottom: 1px solid var(--sl-gray-100, #f3f4f6);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.1s;
    }
    .suburb-ac-item:last-child { border-bottom: none; }
    .suburb-ac-item:hover, .suburb-ac-item.active {
        background: var(--sl-primary-50, #fff7ed);
    }
    .suburb-ac-item .suburb-name { font-weight: 600; color: var(--sl-gray-900, #111827); }
    .suburb-ac-item .suburb-meta { color: var(--sl-gray-500, #6b7280); font-size: 0.85rem; margin-left: auto; }
    .suburb-ac-item .state-badge {
        display: inline-block;
        font-size: 0.7rem;
        padding: 0.15rem 0.45rem;
        background: var(--sl-gray-100, #f3f4f6);
        color: var(--sl-gray-700, #374151);
        border-radius: 4px;
        font-weight: 600;
    }
    .suburb-ac-empty, .suburb-ac-loading {
        padding: 0.75rem 1rem;
        text-align: center;
        color: var(--sl-gray-500, #6b7280);
        font-size: 0.875rem;
    }

    /* Highlight suburb dropdown when there's a service area error */
    .ts-wrapper.has-error .ts-control,
    select.has-error + .ts-wrapper .ts-control {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15) !important;
    }

    /* Bookings table */
    .bookings-table th { font-weight: 500; font-size: 0.85rem; padding: 0.6rem 0.75rem; border-bottom: 1px solid var(--sl-gray-200); }
    .bookings-table td { padding: 1rem 0.75rem; border-bottom: 1px solid var(--sl-gray-100); }
    .bookings-table tr:last-child td { border-bottom: none; }

    /* ── Flatpickr custom theme (matches site's yellow/orange brand) ── */
    .flatpickr-calendar {
        border-radius: 14px;
        box-shadow: 0 14px 50px rgba(0,0,0,0.18);
        border: 1px solid var(--sl-gray-200, #e5e7eb);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        padding: 6px;
    }
    .flatpickr-months { padding: 6px 4px 8px; }
    .flatpickr-month { color: var(--sl-gray-900, #111827); font-weight: 700; }
    .flatpickr-current-month .flatpickr-monthDropdown-months,
    .flatpickr-current-month input.cur-year { font-weight: 700; color: var(--sl-gray-900, #111827); }
    .flatpickr-prev-month, .flatpickr-next-month { color: var(--sl-gray-700, #374151) !important; padding: 8px; }
    .flatpickr-prev-month:hover, .flatpickr-next-month:hover { color: var(--sl-accent-500, #ffd500) !important; }
    .flatpickr-weekdays { background: transparent; padding: 4px 0; }
    span.flatpickr-weekday { color: var(--sl-gray-500, #6b7280); font-weight: 600; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.04em; }

    .flatpickr-day {
        border-radius: 10px;
        font-weight: 500;
        color: var(--sl-gray-900, #111827);
        height: 38px;
        line-height: 38px;
        max-width: 38px;
        margin: 1px;
        border: none;
    }
    /* Available — show as clickable with subtle highlight */
    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.flatpickr-disabled:hover {
        color: rgba(57, 57, 57, 0.25);
        background: transparent;
        cursor: not-allowed;
        text-decoration: line-through;
        text-decoration-thickness: 1px;
        text-decoration-color: rgba(57, 57, 57, 0.18);
    }
    .flatpickr-day:not(.flatpickr-disabled):not(.selected):not(.today) {
        background: rgba(255, 213, 0, 0.08);
        color: var(--sl-gray-900, #111827);
        cursor: pointer;
    }
    .flatpickr-day:not(.flatpickr-disabled):not(.selected):hover {
        background: rgba(255, 213, 0, 0.35);
        border-color: transparent;
    }
    .flatpickr-day.today {
        border: 2px solid var(--sl-accent-500, #ffd500);
        background: #fff;
        font-weight: 700;
    }
    .flatpickr-day.today:hover {
        background: rgba(255, 213, 0, 0.35);
        border-color: var(--sl-accent-500, #ffd500);
    }
    .flatpickr-day.selected,
    .flatpickr-day.selected:hover {
        background: var(--sl-accent-500, #ffd500);
        color: var(--sl-gray-900, #111827);
        font-weight: 800;
        border: none;
        box-shadow: 0 4px 14px rgba(255, 213, 0, 0.45);
    }
    .flatpickr-day.prevMonthDay, .flatpickr-day.nextMonthDay {
        color: rgba(57, 57, 57, 0.18);
    }

    /* ── Time slot picker (visual button grid) ── */
    .time-period-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--sl-gray-500, #6b7280);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .time-period-label .period-icon {
        font-size: 0.95rem;
        color: var(--sl-accent-500, #ffd500);
    }
    .time-slot-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(108px, 1fr));
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .time-slot-grid:last-child { margin-bottom: 0; }
    .time-slot-btn {
        background: #fff;
        border: 1.5px solid var(--sl-gray-200, #e5e7eb);
        color: var(--sl-gray-900, #111827);
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.55rem 0.75rem;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.15s ease;
        text-align: center;
        white-space: nowrap;
    }
    .time-slot-btn:hover {
        border-color: var(--sl-accent-500, #ffd500);
        background: rgba(255, 213, 0, 0.08);
        transform: translateY(-1px);
    }
    .time-slot-btn.selected {
        background: var(--sl-accent-500, #ffd500);
        border-color: var(--sl-accent-500, #ffd500);
        color: var(--sl-gray-900, #111827);
        font-weight: 800;
        box-shadow: 0 4px 14px rgba(255, 213, 0, 0.45);
    }
    .time-slot-empty {
        text-align: center;
        padding: 1.5rem 0;
        color: var(--sl-gray-500, #6b7280);
        font-size: 0.875rem;
    }

    /* Tip box (matches Amount step) */
    .tip-box {
        border: 2px solid var(--sl-accent-500);
        border-radius: 14px;
        padding: 1.5rem;
        background: #fffbeb;
    }
    .tip-icon {
        width: 80px;
        height: 80px;
        position: relative;
        flex-shrink: 0;
    }
    .tip-icon i {
        font-size: 3.5rem;
        color: var(--sl-accent-500);
    }
    .tip-flag {
        position: absolute;
        top: -4px; right: -8px;
        background: var(--sl-accent-500);
        color: var(--sl-gray-900);
        font-size: 0.75rem;
        font-weight: 800;
        padding: 0.1rem 0.5rem;
        border-radius: 4px;
        transform: rotate(10deg);
    }
    @media (max-width: 768px) {
        .tip-icon { display: none; }
    }
</style>
@endpush
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13"></script>

@push('scripts')
<script>
(function() {
  var instructorProfileId = document.getElementById('instructor_profile_id').value;
  var transmission = document.getElementById('instructor_transmission').value;
  var lessonPrice = parseFloat(document.getElementById('lesson_price').value) || 65;
  var testPackagePrice = parseFloat(document.getElementById('test_package_price').value) || 225;
  var orderItems = [];
  var csrf = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').content;
  var opts = { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf || '', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' };
  var PLATFORM_FEE_PERCENT = 4;
  var PACKAGE_HOURS = parseInt(document.getElementById('package_hours').value, 10) || 0;
  var PACKAGE_DISCOUNT_PCT = parseFloat(document.getElementById('package_discount_pct').value) || 0;
  var PACKAGE_ADD_TEST = document.getElementById('package_add_test').value === '1';
  var PACKAGE_TEST_PRICE = parseFloat(document.getElementById('package_test_price').value) || 0;
  var SERVICE_AREA_IDS = JSON.parse(document.getElementById('instructor_service_area_ids').value || '[]').map(String);

  function showHideTestPackage() {
    var isTest = document.getElementById('type-test').checked;
    document.getElementById('test-package-warning').style.display = isTest ? 'block' : 'none';
    document.getElementById('field-test-location').style.display = isTest ? 'block' : 'none';
    document.getElementById('pickup-time-note').style.display = isTest ? 'block' : 'none';
    document.getElementById('dropoff-section').style.display = isTest ? 'block' : 'none';
  }
  document.querySelectorAll('input[name="booking_type"]').forEach(function(r) {
    r.addEventListener('change', showHideTestPackage);
  });
  showHideTestPackage();

  // ── State dropdown uses Tom Select (small list, fine to dropdown) ──
  var STATES_MAP = @json($states->mapWithKeys(fn($s) => [$s->code => $s->id])->toArray());

  function initTomSelect(el, opts) {
    if (!el || el.tomselect) return null;
    return new TomSelect(el, Object.assign({
      create: false,
      allowEmptyOption: true,
      maxOptions: 1000,
      searchField: ['text'],
      placeholder: el.options[0] ? el.options[0].text : 'Select...',
    }, opts || {}));
  }

  initTomSelect(document.getElementById('pickup_state'));
  initTomSelect(document.getElementById('dropoff_state'));
  var testLocEl = document.getElementById('test_location');
  if (testLocEl) initTomSelect(testLocEl);

  // ── Service-area validation (works with hidden suburb input) ──
  function validateServiceArea(prefix) {
    var suburbHidden = document.getElementById(prefix + '_suburb');
    var errorEl = document.getElementById(prefix + '-service-area-error');
    var errorSuburbEl = document.getElementById(prefix + '-error-suburb');
    var searchInput = document.getElementById(prefix + '_suburb_search');

    if (!suburbHidden.value) {
      if (errorEl) errorEl.classList.add('d-none');
      if (searchInput) searchInput.classList.remove('is-invalid');
      return true;
    }
    if (SERVICE_AREA_IDS.length > 0 && SERVICE_AREA_IDS.indexOf(String(suburbHidden.value)) === -1) {
      var label = searchInput ? searchInput.value : 'this suburb';
      if (errorSuburbEl) errorSuburbEl.textContent = label;
      if (errorEl) errorEl.classList.remove('d-none');
      // Don't add red invalid styling — it's a soft warning now, not a blocker
      return false;
    }
    if (errorEl) errorEl.classList.add('d-none');
    if (searchInput) searchInput.classList.remove('is-invalid');
    return true;
  }

  // ── Typeable suburb autocomplete (hits /api/suburbs/search after 3 chars) ──
  function initSuburbSearch(prefix) {
    var input = document.getElementById(prefix + '_suburb_search');
    var dropdown = document.getElementById(prefix + '_suburb_ac');
    var hidden = document.getElementById(prefix + '_suburb');
    var stateSelect = document.getElementById(prefix + '_state');
    if (!input || !dropdown || !hidden) return;

    var debounceTimer = null;
    var activeIndex = -1;
    var currentResults = [];

    function escapeHtml(s) {
      return String(s || '').replace(/[&<>"']/g, function(c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
      });
    }

    function render(items) {
      currentResults = items || [];
      activeIndex = -1;
      if (items.length === 0) {
        dropdown.innerHTML = '<div class="suburb-ac-empty">No suburbs match — try a postcode</div>';
      } else {
        dropdown.innerHTML = items.map(function(item, i) {
          var inSA = SERVICE_AREA_IDS.length === 0 || SERVICE_AREA_IDS.indexOf(String(item.id)) !== -1;
          return '<div class="suburb-ac-item" data-idx="' + i + '">' +
            '<span class="suburb-name">' + escapeHtml(item.name) + '</span>' +
            (inSA ? '' : '<span class="badge bg-warning text-dark ms-2" style="font-size:0.65rem;">Out of area</span>') +
            '<span class="suburb-meta">' + escapeHtml(item.postcode || '') + '</span>' +
            '<span class="state-badge">' + escapeHtml(item.state || '') + '</span>' +
          '</div>';
        }).join('');
        Array.prototype.slice.call(dropdown.querySelectorAll('.suburb-ac-item')).forEach(function(el) {
          el.addEventListener('mousedown', function(e) {
            e.preventDefault();
            var idx = parseInt(el.getAttribute('data-idx'), 10);
            selectItem(currentResults[idx]);
          });
        });
      }
      dropdown.classList.add('show');
    }

    function selectItem(item) {
      if (!item) return;
      hidden.value = item.id;
      input.value = item.name + ', ' + (item.postcode || '') + ' ' + (item.state || '');
      dropdown.classList.remove('show');

      // Auto-fill state
      var stateId = STATES_MAP[item.state] || '';
      if (stateSelect) {
        if (stateSelect.tomselect) stateSelect.tomselect.setValue(String(stateId), true);
        else stateSelect.value = stateId;
      }
      // Validate service area + form state
      validateServiceArea(prefix);
      if (typeof validateFormState === 'function') validateFormState();
    }

    input.addEventListener('input', function() {
      // If user types after selecting, clear the hidden value (force re-select)
      hidden.value = '';
      var q = input.value.trim();
      if (q.length < 3) { dropdown.classList.remove('show'); return; }
      dropdown.innerHTML = '<div class="suburb-ac-loading"><span class="spinner-border spinner-border-sm me-1"></span>Searching...</div>';
      dropdown.classList.add('show');

      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function() {
        fetch('/api/suburbs/search?q=' + encodeURIComponent(q), {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin'
        })
        .then(function(r) { return r.json(); })
        .then(function(res) { render(res.data || []); })
        .catch(function() {
          dropdown.innerHTML = '<div class="suburb-ac-empty text-danger">Search failed</div>';
        });
      }, 220);
    });

    input.addEventListener('keydown', function(e) {
      if (!dropdown.classList.contains('show')) return;
      var items = dropdown.querySelectorAll('.suburb-ac-item');
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        activeIndex = Math.min(activeIndex + 1, items.length - 1);
        Array.prototype.slice.call(items).forEach(function(el, i) { el.classList.toggle('active', i === activeIndex); });
        if (items[activeIndex]) items[activeIndex].scrollIntoView({ block: 'nearest' });
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        activeIndex = Math.max(activeIndex - 1, 0);
        Array.prototype.slice.call(items).forEach(function(el, i) { el.classList.toggle('active', i === activeIndex); });
        if (items[activeIndex]) items[activeIndex].scrollIntoView({ block: 'nearest' });
      } else if (e.key === 'Enter' && activeIndex >= 0) {
        e.preventDefault();
        selectItem(currentResults[activeIndex]);
      } else if (e.key === 'Escape') {
        dropdown.classList.remove('show');
      }
    });

    input.addEventListener('blur', function() {
      setTimeout(function() { dropdown.classList.remove('show'); }, 200);
    });
  }

  initSuburbSearch('pickup');
  initSuburbSearch('dropoff');

  // Public helper used by address autocomplete to fill suburb after selection
  window.__setSuburbFromMatch = function(prefix, match) {
    var hidden = document.getElementById(prefix + '_suburb');
    var input = document.getElementById(prefix + '_suburb_search');
    var stateSelect = document.getElementById(prefix + '_state');
    if (!hidden || !match) return;
    hidden.value = match.id;
    if (input) input.value = match.name + ', ' + (match.postcode || '') + ' ' + (match.state || '');
    var stateId = STATES_MAP[match.state] || '';
    if (stateSelect) {
      if (stateSelect.tomselect) stateSelect.tomselect.setValue(String(stateId), true);
      else stateSelect.value = stateId;
    }
    validateServiceArea(prefix);
    if (typeof validateFormState === 'function') validateFormState();
  };

  // ── Address autocomplete using OpenStreetMap Nominatim (free, no API key) ──
  // Returns real Australian street addresses, just like Google Places does
  function initAddressAutocomplete(prefix) {
    var input = document.getElementById(prefix + '_address');
    var dropdown = document.getElementById(prefix + '_address_ac');
    if (!input || !dropdown) return;

    var debounceTimer = null;
    var activeIndex = -1;
    var currentResults = [];

    function escapeHtml(s) {
      return String(s || '').replace(/[&<>"']/g, function(c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
      });
    }

    // Format a Nominatim result into a clean street/suburb/state display
    function formatAddress(item) {
      var addr = item.address || {};
      // Build a "street line" — house number + road, fallback to first part of display_name
      var streetParts = [];
      if (addr.house_number) streetParts.push(addr.house_number);
      if (addr.road) streetParts.push(addr.road);
      var streetLine = streetParts.join(' ');
      if (!streetLine) {
        // Fallback to first segment of display_name (e.g. "Sydney Road")
        streetLine = (item.display_name || '').split(',')[0] || '';
      }

      var suburb = addr.suburb || addr.city || addr.town || addr.village || addr.municipality || '';
      var stateShort = addr['ISO3166-2-lvl4'] ? addr['ISO3166-2-lvl4'].replace('AU-', '') : '';
      var stateName = addr.state || '';
      var postcode = addr.postcode || '';

      return {
        streetLine: streetLine,
        suburb: suburb,
        state: stateShort || stateName,
        postcode: postcode,
        full: streetLine + (suburb ? ', ' + suburb : '') + (postcode ? ' ' + postcode : ''),
      };
    }

    function render(items) {
      currentResults = items || [];
      activeIndex = -1;
      if (items.length === 0) {
        dropdown.innerHTML = '<div class="suburb-ac-empty">No matches — try a different spelling or postcode</div>';
      } else {
        dropdown.innerHTML = items.map(function(item, i) {
          var fmt = formatAddress(item);
          return '<div class="suburb-ac-item" data-idx="' + i + '">' +
            '<i class="bi bi-geo-alt-fill text-muted me-1"></i>' +
            '<span class="suburb-name">' + escapeHtml(fmt.streetLine) + '</span>' +
            (fmt.suburb ? '<span class="suburb-meta ms-2">' + escapeHtml(fmt.suburb) + (fmt.state ? ' ' + escapeHtml(fmt.state) : '') + '</span>' : '') +
          '</div>';
        }).join('') + '<div class="px-3 py-1 text-end small text-muted" style="border-top:1px solid var(--sl-gray-100);">powered by <strong>OpenStreetMap</strong></div>';

        Array.prototype.slice.call(dropdown.querySelectorAll('.suburb-ac-item')).forEach(function(el) {
          el.addEventListener('mousedown', function(e) {
            e.preventDefault();
            var idx = parseInt(el.getAttribute('data-idx'), 10);
            selectItem(currentResults[idx]);
          });
        });
      }
      dropdown.classList.add('show');
    }

    function selectItem(item) {
      if (!item) return;
      var fmt = formatAddress(item);

      // Put the full street address into the input
      input.value = fmt.full || item.display_name || '';
      dropdown.classList.remove('show');

      // Match the suburb to our DB and auto-fill the suburb + state dropdowns
      if (fmt.suburb || fmt.postcode) {
        var query = fmt.postcode || fmt.suburb;
        fetch('/api/suburbs/search?q=' + encodeURIComponent(query), {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin'
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
          var dbSuburbs = res.data || [];
          var match = dbSuburbs.find(function(s) {
            return (s.name || '').toLowerCase() === (fmt.suburb || '').toLowerCase()
              && (!fmt.postcode || s.postcode === fmt.postcode);
          }) || dbSuburbs.find(function(s) {
            return (s.name || '').toLowerCase() === (fmt.suburb || '').toLowerCase();
          }) || dbSuburbs[0];

          if (match && window.__setSuburbFromMatch) {
            window.__setSuburbFromMatch(prefix, match);
          }
        })
        .catch(function() { /* silent */ });
      }
    }

    input.addEventListener('input', function() {
      var q = input.value.trim();
      if (q.length < 3) { dropdown.classList.remove('show'); return; }
      dropdown.innerHTML = '<div class="suburb-ac-loading"><span class="spinner-border spinner-border-sm me-1"></span>Searching addresses...</div>';
      dropdown.classList.add('show');

      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function() {
        // Nominatim — free, no API key. Restricted to Australia for relevance.
        var url = 'https://nominatim.openstreetmap.org/search'
          + '?q=' + encodeURIComponent(q)
          + '&format=json&addressdetails=1&countrycodes=au&limit=6';
        fetch(url, {
          headers: { 'Accept': 'application/json' }
          // Note: no credentials — this is a CORS-public API
        })
        .then(function(r) { return r.json(); })
        .then(function(items) { render(Array.isArray(items) ? items : []); })
        .catch(function() {
          dropdown.innerHTML = '<div class="suburb-ac-empty text-danger">Search failed — please try again</div>';
        });
      }, 350); // Slightly longer debounce — respects Nominatim's 1 req/sec policy
    });

    input.addEventListener('keydown', function(e) {
      if (!dropdown.classList.contains('show')) return;
      var items = dropdown.querySelectorAll('.suburb-ac-item');
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        activeIndex = Math.min(activeIndex + 1, items.length - 1);
        Array.prototype.slice.call(items).forEach(function(el, i) { el.classList.toggle('active', i === activeIndex); });
        if (items[activeIndex]) items[activeIndex].scrollIntoView({ block: 'nearest' });
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        activeIndex = Math.max(activeIndex - 1, 0);
        Array.prototype.slice.call(items).forEach(function(el, i) { el.classList.toggle('active', i === activeIndex); });
        if (items[activeIndex]) items[activeIndex].scrollIntoView({ block: 'nearest' });
      } else if (e.key === 'Enter' && activeIndex >= 0) {
        e.preventDefault();
        selectItem(currentResults[activeIndex]);
      } else if (e.key === 'Escape') {
        dropdown.classList.remove('show');
      }
    });

    input.addEventListener('blur', function() {
      setTimeout(function() { dropdown.classList.remove('show'); }, 200);
    });
  }

  initAddressAutocomplete('pickup');
  initAddressAutocomplete('dropoff');

  function getBookingTypeLabel() {
    if (document.getElementById('type-test').checked) return 'Driving Test Package';
    if (document.getElementById('type-2hr').checked) return '2-Hour Lesson';
    return '1-Hour Lesson';
  }

  function getPriceAndDuration(type) {
    if (type === 'test_package') return { price: testPackagePrice, durationLabel: '2.5hr Test Package' };
    if (type === '2hr') return { price: lessonPrice * 2, durationLabel: '2 Hour' };
    return { price: lessonPrice, durationLabel: '1 Hour' };
  }

  // ── Form visibility helpers ──
  function showNewBookingForm() {
    document.getElementById('new-booking-form-card').style.display = 'block';
    document.getElementById('add-another-row').style.display = 'none';
    // Cancel button only shown if there are saved bookings
    document.getElementById('btn-cancel-booking').style.display = orderItems.length > 0 ? 'inline-block' : 'none';
    // Disable sidebar buttons while form is open (per EasyLicence reference)
    var sidebarAdd = document.getElementById('btn-add-another');
    var sidebarCont = document.getElementById('btn-continue');
    if (sidebarAdd) sidebarAdd.disabled = true;
    if (sidebarCont) { sidebarCont.classList.add('disabled'); sidebarCont.style.pointerEvents = 'none'; sidebarCont.style.opacity = '0.5'; }
    document.getElementById('new-booking-form-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  function hideNewBookingForm() {
    document.getElementById('new-booking-form-card').style.display = 'none';
    // Show "Add Another" only if at least one booking saved
    document.getElementById('add-another-row').style.display = orderItems.length > 0 ? 'block' : 'none';
    // Re-enable sidebar buttons
    var sidebarAdd = document.getElementById('btn-add-another');
    var sidebarCont = document.getElementById('btn-continue');
    if (sidebarAdd) sidebarAdd.disabled = false;
    if (sidebarCont) { sidebarCont.classList.remove('disabled'); sidebarCont.style.pointerEvents = ''; sidebarCont.style.opacity = ''; }
  }
  function clearForm() {
    if (flatpickrInstance) flatpickrInstance.clear();
    document.getElementById('booking_date').value = '';
    document.getElementById('booking_date_iso').value = '';
    document.getElementById('booking_time').innerHTML = '<option value="">Select time</option>';
    document.getElementById('booking_time_display').value = '';
    document.getElementById('time-slot-picker').style.display = 'none';
    document.getElementById('type-1hr').checked = true;
    showHideTestPackage();
    validateFormState();
  }

  // ── Validate form state to enable/disable Save button ──
  function validateFormState() {
    var dateOk = !!document.getElementById('booking_date_iso').value;
    var timeOk = !!document.getElementById('booking_time').value;
    var suburbOk = !!document.getElementById('pickup_suburb').value;
    var stateOk = !!document.getElementById('pickup_state').value;
    var isTest = document.getElementById('type-test').checked;
    var dropOk = !isTest || (
      document.getElementById('dropoff_suburb').value && document.getElementById('dropoff_state').value
    );
    var allValid = dateOk && timeOk && suburbOk && stateOk && dropOk;
    document.getElementById('btn-save-booking').disabled = !allValid;
  }

  // Wire up validation triggers
  ['booking_date_iso', 'booking_time', 'pickup_suburb', 'pickup_state', 'dropoff_suburb', 'dropoff_state'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) el.addEventListener('change', validateFormState);
  });
  document.querySelectorAll('input[name="booking_type"]').forEach(function(r) {
    r.addEventListener('change', validateFormState);
  });

  function renderBookingsList() {
    var card = document.getElementById('bookings-list-card');
    var tbody = document.getElementById('bookings-list');
    var tipBox = document.getElementById('tip-box');
    if (orderItems.length === 0) {
      card.style.display = 'none';
      tipBox.style.display = 'none';
      document.getElementById('add-another-row').style.display = 'none';
      // Show form by default if no bookings
      document.getElementById('new-booking-form-card').style.display = 'block';
      document.getElementById('btn-cancel-booking').style.display = 'none';
      return;
    }
    card.style.display = 'block';
    tipBox.style.display = 'block';
    tbody.innerHTML = orderItems.map(function(item, i) {
      var typeIcon = item.booking_type === 'test_package' ? 'check2-square' : 'calendar';
      return '<tr>' +
        '<td class="ps-4">' +
          '<i class="bi bi-' + typeIcon + ' text-muted me-2"></i>' +
          '<span class="fw-semibold">' + escapeHtml(item.dateLabel || '') + '</span>' +
        '</td>' +
        '<td>' + escapeHtml(item.timeLabel || '') + '</td>' +
        '<td>' + escapeHtml(item.durationLabel || '') + '</td>' +
        '<td class="fw-semibold">$' + (item.price != null ? item.price.toFixed(2) : '0') + '</td>' +
        '<td class="pe-4 text-end">' +
          '<button type="button" class="btn btn-link btn-sm p-1 text-secondary" title="Edit" data-edit="' + i + '"><i class="bi bi-pencil"></i></button>' +
          '<button type="button" class="btn btn-link btn-sm p-1 text-danger" title="Delete" data-remove-booking="' + i + '"><i class="bi bi-trash"></i></button>' +
        '</td>' +
      '</tr>';
    }).join('');

    function escapeHtml(s) {
      return String(s || '').replace(/[&<>"']/g, function(c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
      });
    }

    tbody.querySelectorAll('[data-remove-booking]').forEach(function(btn) {
      btn.addEventListener('click', function() {
        orderItems.splice(parseInt(btn.getAttribute('data-remove-booking'), 10), 1);
        renderBookingsList();
        renderOrderSummary();
      });
    });
    tbody.querySelectorAll('[data-edit]').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var idx = parseInt(btn.getAttribute('data-edit'), 10);
        var item = orderItems[idx];
        if (!item) return;
        orderItems.splice(idx, 1);
        renderBookingsList();
        renderOrderSummary();
        showNewBookingForm();
        document.getElementById('booking_date_iso').value = item.date_iso || '';
        if (flatpickrInstance && item.date_iso) {
          flatpickrInstance.setDate(item.date_iso, false);
        } else {
          document.getElementById('booking_date').value = item.dateLabel || '';
        }
        document.getElementById('pickup_address').value = item.pickup_address || '';

        function restoreSuburb(prefix, suburbId, stateId, label) {
          document.getElementById(prefix + '_suburb').value = suburbId || '';
          var search = document.getElementById(prefix + '_suburb_search');
          if (search) search.value = label || '';
          var stateEl = document.getElementById(prefix + '_state');
          if (stateEl) {
            if (stateEl.tomselect) stateEl.tomselect.setValue(stateId || '', true);
            else stateEl.value = stateId || '';
          }
        }
        restoreSuburb('pickup', item.pickup_suburb_id, item.pickup_state_id, item.pickup_label);
        if (item.booking_type === 'test_package') {
          document.getElementById('type-test').checked = true;
          restoreSuburb('dropoff', item.dropoff_suburb_id, item.dropoff_state_id, item.dropoff_label);
          document.getElementById('dropoff_address').value = item.dropoff_address || '';
        } else {
          document.getElementById('type-1hr').checked = item.booking_type === '1hr';
          document.getElementById('type-2hr').checked = item.booking_type === '2hr';
        }
        showHideTestPackage();
        if (item.date_iso) loadTimeSlots(item.date_iso);
        validateFormState();
      });
    });

    // Hide the form once user has saved bookings (it'll re-show when they click Add Another)
    hideNewBookingForm();
  }

  // ── Flatpickr date picker — only enables instructor's available dates ──
  var flatpickrInstance = null;
  var availableDatesCache = null;

  function loadAvailableDates() {
    var hint = document.getElementById('dates-loading-hint');
    if (hint) hint.style.display = 'block';

    return fetch('/api/instructors/' + instructorProfileId + '/availability/dates?days=60', opts)
      .then(function(r) { return r.json(); })
      .then(function(res) {
        availableDatesCache = (res.data || []).map(function(d) { return d.date; }); // ['2026-04-29', ...]
        if (hint) hint.style.display = 'none';
        initOrUpdateFlatpickr();
      })
      .catch(function() {
        if (hint) hint.innerHTML = '<span class="text-danger">Could not load availability — please refresh.</span>';
      });
  }

  function initOrUpdateFlatpickr() {
    var input = document.getElementById('booking_date');
    if (!input) return;

    if (flatpickrInstance) {
      // Update existing instance with new available dates
      flatpickrInstance.set('enable', availableDatesCache || []);
      return;
    }

    flatpickrInstance = flatpickr(input, {
      dateFormat: 'Y-m-d',
      altInput: true,
      altFormat: 'D, j M Y',          // Display: "Wed, 29 Apr 2026"
      minDate: 'today',
      maxDate: new Date().fp_incr(60), // Next 60 days
      disableMobile: true,             // Use Flatpickr UI on mobile too (not native)
      enable: availableDatesCache || [],
      static: false,                   // Float above content
      onChange: function(selectedDates, dateStr) {
        if (!dateStr) return;
        document.getElementById('booking_date_iso').value = dateStr;
        loadTimeSlots(dateStr);
        if (typeof validateFormState === 'function') validateFormState();
      }
    });
  }

  // ── Visual time slot grid (Morning / Afternoon / Evening) ──
  function formatTimeLabel(timeHm) {
    if (!timeHm || timeHm.indexOf(':') === -1) return timeHm || '';
    var parts = timeHm.split(':');
    var h = parseInt(parts[0], 10);
    var am = h < 12;
    if (h === 0) h = 12; else if (h > 12) h -= 12;
    return h + ':' + parts[1] + (am ? ' am' : ' pm');
  }

  function selectTimeSlot(value, label) {
    var timeSelect = document.getElementById('booking_time');
    timeSelect.innerHTML = '<option value="' + value + '" selected>' + label + '</option>';
    document.getElementById('booking_time_display').value = label;

    // Highlight the selected button
    document.querySelectorAll('#time-slot-content .time-slot-btn').forEach(function(btn) {
      btn.classList.toggle('selected', btn.getAttribute('data-value') === value);
    });

    // Trigger change so validateFormState runs
    timeSelect.dispatchEvent(new Event('change'));
    if (typeof validateFormState === 'function') validateFormState();
  }

  function renderTimeSlots(slots, dateStr) {
    var picker = document.getElementById('time-slot-picker');
    var content = document.getElementById('time-slot-content');
    var counter = document.getElementById('time-slot-count');
    picker.style.display = 'block';

    if (!slots.length) {
      content.innerHTML = '<div class="time-slot-empty"><i class="bi bi-calendar-x me-1"></i>No times available for this date — please pick another date.</div>';
      counter.textContent = '';
      return;
    }

    counter.textContent = slots.length + ' slot' + (slots.length === 1 ? '' : 's') + ' available';

    // Group into morning / afternoon / evening
    var groups = { morning: [], afternoon: [], evening: [] };
    slots.forEach(function(s) {
      var dt = s.datetime || s.time;
      var t = (dt && dt.length >= 16) ? dt.substr(11, 5) : (s.time || '');
      var hour = parseInt(t.split(':')[0], 10);
      var slot = {
        time: t,
        label: formatTimeLabel(t),
        value: dt || (dateStr + ' ' + t + ':00'),
      };
      if (hour < 12) groups.morning.push(slot);
      else if (hour < 17) groups.afternoon.push(slot);
      else groups.evening.push(slot);
    });

    var html = '';
    var groupConfig = [
      ['morning',   'Morning',   'bi-sunrise'],
      ['afternoon', 'Afternoon', 'bi-sun'],
      ['evening',   'Evening',   'bi-moon-stars'],
    ];

    groupConfig.forEach(function(g) {
      var key = g[0], label = g[1], icon = g[2];
      if (groups[key].length === 0) return;
      html += '<div class="time-period-label"><i class="bi ' + icon + ' period-icon"></i>' + label + '</div>';
      html += '<div class="time-slot-grid">';
      groups[key].forEach(function(slot) {
        html += '<button type="button" class="time-slot-btn" data-value="' + slot.value + '" data-label="' + slot.label + '">' + slot.label + '</button>';
      });
      html += '</div>';
    });

    content.innerHTML = html;

    // Wire up click handlers
    content.querySelectorAll('.time-slot-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        selectTimeSlot(btn.getAttribute('data-value'), btn.getAttribute('data-label'));
      });
    });
  }

  function loadTimeSlots(date) {
    var picker = document.getElementById('time-slot-picker');
    var content = document.getElementById('time-slot-content');
    var displayInput = document.getElementById('booking_time_display');
    var timeSelect = document.getElementById('booking_time');

    // Reset selection
    timeSelect.innerHTML = '<option value="">Select time</option>';
    displayInput.value = '';
    picker.style.display = 'block';
    content.innerHTML = '<div class="text-center text-muted small py-3"><span class="spinner-border spinner-border-sm me-1"></span>Loading available times...</div>';

    fetch('/api/instructors/' + instructorProfileId + '/availability/slots?date=' + encodeURIComponent(date), opts)
      .then(function(r) { return r.json(); })
      .then(function(res) {
        renderTimeSlots(res.data || [], date);
      })
      .catch(function() {
        content.innerHTML = '<div class="time-slot-empty text-danger"><i class="bi bi-exclamation-circle me-1"></i>Could not load times. Please try again.</div>';
      });
  }

  // Initialize the date picker on page load (so dates are ready before user clicks)
  loadAvailableDates();

  function countScheduledHours() {
    var totalMinutes = 0;
    orderItems.forEach(function(item) {
      if (item.booking_type === '2hr') totalMinutes += 120;
      else if (item.booking_type === 'test_package') totalMinutes += 150;
      else totalMinutes += 60;
    });
    return totalMinutes / 60;
  }

  function updateScheduledProgress() {
    var el = document.getElementById('scheduled-hours');
    if (el) el.textContent = countScheduledHours();
  }

  function renderOrderSummary() {
    var wrap = document.getElementById('order-items');
    var empty = document.getElementById('order-empty');
    var totals = document.getElementById('order-totals');
    updateScheduledProgress();
    if (orderItems.length === 0) {
      wrap.innerHTML = '';
      empty.style.display = 'block';
      totals.style.display = 'none';
      document.getElementById('btn-continue').style.opacity = '0.6';
      return;
    }
    empty.style.display = 'none';
    totals.style.display = 'block';
    document.getElementById('btn-continue').style.opacity = '1';
    var subtotal = 0;
    orderItems.forEach(function(item) { subtotal += item.price != null ? item.price : 0; });
    // Apply bulk-hours discount from the package (if any) — only on lesson hours, not test package
    var discount = PACKAGE_DISCOUNT_PCT > 0 ? Math.round(subtotal * PACKAGE_DISCOUNT_PCT) / 100 : 0;
    var afterDiscount = subtotal - discount;
    // Add test package price (no discount applied) if user opted in
    if (PACKAGE_ADD_TEST) afterDiscount += PACKAGE_TEST_PRICE;
    var fee = Math.round(afterDiscount * PLATFORM_FEE_PERCENT) / 100;
    var total = afterDiscount + fee;
    var instalment = total / 4;

    wrap.innerHTML = orderItems.map(function(item, i) {
      var shortDate = (item.dateLabel || '').replace(/^[^,]+, (\d+)/, '$1').trim();
    if (shortDate && item.date_iso) {
      var d = new Date(item.date_iso + 'T12:00:00');
      var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      shortDate = d.getDate() + ' ' + months[d.getMonth()];
    }
    var lineLabel = (item.booking_type === 'test_package' ? 'Test Package' : 'Lesson') + ' - ' + shortDate + ', ' + (item.timeLabel || '');
      return '<div class="d-flex justify-content-between align-items-start py-2 border-bottom">' +
        '<div class="small">' + lineLabel + '</div>' +
        '<div class="d-flex align-items-center gap-1"><span>$' + (item.price != null ? item.price.toFixed(2) : '0') + '</span>' +
        '<button type="button" class="btn btn-link btn-sm text-danger p-0" data-remove="' + i + '" title="Remove"><i class="bi bi-x"></i></button></div></div>';
    }).join('');
    document.getElementById('order-fee').textContent = '$' + fee.toFixed(2);
    document.getElementById('order-total').textContent = '$' + total.toFixed(2);
    document.getElementById('order-instalment').textContent = '$' + instalment.toFixed(2);
    var bnplEl = document.getElementById('bnpl-amount');
    if (bnplEl) bnplEl.textContent = '$' + instalment.toFixed(2);

    wrap.querySelectorAll('[data-remove]').forEach(function(btn) {
      btn.addEventListener('click', function() {
        orderItems.splice(parseInt(btn.getAttribute('data-remove'), 10), 1);
        renderBookingsList();
        renderOrderSummary();
      });
    });
  }

  document.getElementById('btn-save-booking').addEventListener('click', function() {
    var dateIso = document.getElementById('booking_date_iso').value;
    var timeVal = document.getElementById('booking_time').value;
    var pickupAddr = document.getElementById('pickup_address').value.trim();
    var pickupSuburb = document.getElementById('pickup_suburb').value;
    var pickupState = document.getElementById('pickup_state').value;
    if (!dateIso || !timeVal) {
      alert('Please select date and time.');
      return;
    }
    if (!pickupSuburb || !pickupState) {
      alert('Please type and select a pickup suburb (3+ letters or postcode).');
      return;
    }
    // Service-area check is INFORMATIONAL ONLY — does not block save (testing mode)
    validateServiceArea('pickup');
    var isTest = document.getElementById('type-test').checked;
    var dropoffSuburb = document.getElementById('dropoff_suburb').value;
    var dropoffState = document.getElementById('dropoff_state').value;
    if (isTest && (!dropoffSuburb || !dropoffState)) {
      alert('Please type and select a drop-off suburb for the test package.');
      return;
    }
    // Pull the visible label from the search input
    var pickupLabel = document.getElementById('pickup_suburb_search').value || '';
    var dropoffLabel = isTest ? (document.getElementById('dropoff_suburb_search').value || '') : null;
    var type = isTest ? 'test_package' : (document.getElementById('type-2hr').checked ? '2hr' : '1hr');
    var timeLabel = document.getElementById('booking_time').selectedOptions[0] ? document.getElementById('booking_time').selectedOptions[0].text : timeVal;
    var pd = getPriceAndDuration(type);
    orderItems.push({
      booking_type: type,
      typeLabel: getBookingTypeLabel(),
      durationLabel: pd.durationLabel,
      price: pd.price,
      date_iso: dateIso,
      dateLabel: document.getElementById('booking_date').value,
      scheduled_at: timeVal,
      timeLabel: timeLabel,
      pickup_address: pickupAddr,
      pickup_suburb_id: pickupSuburb,
      pickup_state_id: pickupState,
      pickup_label: pickupLabel,
      pickupAddress: pickupAddr || pickupLabel,
      dropoff_address: isTest ? document.getElementById('dropoff_address').value.trim() : null,
      dropoff_suburb_id: isTest ? dropoffSuburb : null,
      dropoff_state_id: isTest ? dropoffState : null,
      dropoff_label: dropoffLabel,
    });
    // Cache the last pickup location for fast re-use on subsequent bookings
    window.__lastPickup = {
      address: pickupAddr,
      suburb_id: pickupSuburb,
      state_id: pickupState,
      label: pickupLabel,
    };
    renderBookingsList();
    renderOrderSummary();
    clearForm();
    // After saving, hide the form — user clicks "Add Another Booking" to add more
    hideNewBookingForm();
  });

  // ── Cancel button: discard new form, return to bookings list view ──
  document.getElementById('btn-cancel-booking').addEventListener('click', function() {
    clearForm();
    hideNewBookingForm();
  });

  // ── "Add Another Booking" button (shown between bookings list and form) ──
  document.getElementById('btn-show-new-form').addEventListener('click', function() {
    clearForm();
    // Pre-fill pickup location from the previous booking
    if (window.__lastPickup) {
      var p = window.__lastPickup;
      document.getElementById('pickup_address').value = p.address || '';
      document.getElementById('pickup_suburb').value = p.suburb_id || '';
      var sub = document.getElementById('pickup_suburb_search');
      if (sub) sub.value = p.label || '';
      var st = document.getElementById('pickup_state');
      if (st) {
        if (st.tomselect) st.tomselect.setValue(p.state_id || '', true);
        else st.value = p.state_id || '';
      }
      validateFormState();
    }
    showNewBookingForm();
  });

  // ── Sidebar "Add Another Booking" button (mirrors the inline one) ──
  document.getElementById('btn-add-another').addEventListener('click', function() {
    document.getElementById('btn-show-new-form').click();
  });

  document.getElementById('btn-continue').addEventListener('click', function(e) {
    e.preventDefault();
    if (orderItems.length === 0) {
      alert('Add at least one booking before continuing.');
      return;
    }
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("learner.bookings.continue") }}';
    var csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrf || document.querySelector('meta[name="csrf-token"]').content;
    form.appendChild(csrfInput);
    var idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'instructor_profile_id';
    idInput.value = instructorProfileId;
    form.appendChild(idInput);
    var itemsInput = document.createElement('input');
    itemsInput.type = 'hidden';
    itemsInput.name = 'items';
    itemsInput.value = JSON.stringify(orderItems);
    form.appendChild(itemsInput);
    document.body.appendChild(form);
    form.submit();
  });

  renderOrderSummary();

  @if(!empty($googleMapsApiKey))
  function initPlaces() {
    if (typeof google === 'undefined' || !google.maps || !google.maps.places) return;
    var pickupInput = document.getElementById('pickup_address');
    var dropoffInput = document.getElementById('dropoff_address');
    function setupAutocomplete(input, prefix) {
      if (!input) return;
      var autocomplete = new google.maps.places.Autocomplete(input, { types: ['address'], componentRestrictions: { country: 'au' } });
      autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.address_components) return;
        var suburb = '', postcode = '', state = '';
        for (var i = 0; i < place.address_components.length; i++) {
          var c = place.address_components[i];
          if (c.types.indexOf('postal_code') !== -1) postcode = c.long_name;
          if (c.types.indexOf('administrative_area_level_1') !== -1) state = c.short_name;
          if (c.types.indexOf('locality') !== -1) suburb = c.long_name;
        }
        // Look up matching suburb in our DB via the API
        if (suburb || postcode) {
          var q = postcode || suburb;
          fetch('/api/suburbs/search?q=' + encodeURIComponent(q))
            .then(function(r) { return r.json(); })
            .then(function(res) {
              var items = res.data || [];
              // Prefer an exact match on suburb+postcode
              var match = items.find(function(i) {
                return (i.name || '').toLowerCase() === suburb.toLowerCase()
                  && (!postcode || i.postcode === postcode);
              }) || items[0];
              if (match) {
                var suburbEl = document.getElementById(prefix + '_suburb');
                var stateEl = document.getElementById(prefix + '_state');
                var stateId = STATES_MAP[match.state] || '';
                if (suburbEl.tomselect) suburbEl.tomselect.setValue(String(match.id), true);
                else suburbEl.value = match.id;
                if (stateEl.tomselect) stateEl.tomselect.setValue(String(stateId), true);
                else stateEl.value = stateId;
              }
            });
        }
      });
    }
    setupAutocomplete(pickupInput, 'pickup');
    setupAutocomplete(dropoffInput, 'dropoff');
  }
  if (typeof google !== 'undefined' && google.maps && google.maps.places) {
    initPlaces();
  } else {
    window.addEventListener('load', function() { setTimeout(initPlaces, 500); });
  }
  @endif
})();
</script>
@endpush
@endsection
