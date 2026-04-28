@extends('layouts.booking', ['step' => 2])

@section('title', 'Choose Lesson Amount')

@section('content')
@php
    $lessonPrice = (float) ($instructorProfile->lesson_price ?? 75);
    // Discount tiers — matches EasyLicence reference
    $packages = [
        [
            'hours' => 10,
            'discount_pct' => 10,
            'label' => '10 hours',
            'description' => 'Perfect for new learners starting their driving journey from scratch',
            'badge' => '10% OFF',
            'recommended' => true,
        ],
        [
            'hours' => 6,
            'discount_pct' => 5,
            'label' => '6 hours',
            'description' => 'Ideal for new learners, overseas license holders, or anyone needing a driving skill refresh.',
            'badge' => '5% OFF',
            'recommended' => false,
        ],
    ];
    foreach ($packages as &$pkg) {
        $pkg['subtotal'] = $lessonPrice * $pkg['hours'];
        $pkg['discount_amount'] = round($pkg['subtotal'] * $pkg['discount_pct'] / 100, 2);
        $pkg['total'] = $pkg['subtotal'] - $pkg['discount_amount'];
    }
    unset($pkg);

    $selectedHours = session('learner_booking_package.hours');
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="mb-4">
            <h3 class="fw-bolder mb-1" style="letter-spacing:-0.02em;">Choose lesson amount</h3>
            <p class="text-muted mb-0">Buy more and save!</p>
        </div>

        <form method="POST" action="{{ route('learner.bookings.amount.store') }}" id="amount-form">
            @csrf
            <input type="hidden" name="instructor_profile_id" value="{{ $instructorProfile->id }}">

            {{-- Package options --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    @foreach($packages as $pkg)
                        <label class="package-option {{ $pkg['recommended'] ? 'recommended' : '' }} {{ (int)$selectedHours === $pkg['hours'] ? 'selected' : '' }}" for="pkg-{{ $pkg['hours'] }}">
                            <input type="radio" name="hours" value="{{ $pkg['hours'] }}" id="pkg-{{ $pkg['hours'] }}"
                                   {{ (int)$selectedHours === $pkg['hours'] ? 'checked' : ($pkg['recommended'] && !$selectedHours ? 'checked' : '') }}
                                   class="pkg-radio">
                            <span class="pkg-radio-dot"></span>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-bold fs-5">{{ $pkg['label'] }}</span>
                                    <span class="pkg-badge pkg-badge-discount">{{ $pkg['badge'] }}</span>
                                    @if($pkg['recommended'])
                                        <span class="pkg-badge pkg-badge-best">BEST VALUE</span>
                                    @endif
                                </div>
                                <div class="small text-muted mt-1">{{ $pkg['description'] }}</div>
                                <div class="small text-muted mt-1">
                                    ${{ number_format($pkg['total'], 2) }}
                                    <span class="text-decoration-line-through ms-1">${{ number_format($pkg['subtotal'], 2) }}</span>
                                    — you save ${{ number_format($pkg['discount_amount'], 2) }}
                                </div>
                            </div>
                        </label>
                    @endforeach

                    {{-- Custom hours --}}
                    <label class="package-option {{ $selectedHours && !in_array((int)$selectedHours, [6, 10]) ? 'selected' : '' }}" for="pkg-custom">
                        <input type="radio" name="hours" value="custom" id="pkg-custom"
                               {{ $selectedHours && !in_array((int)$selectedHours, [6, 10]) ? 'checked' : '' }}
                               class="pkg-radio">
                        <span class="pkg-radio-dot"></span>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fw-bold">Select custom hours</span>
                                <i class="bi bi-chevron-down" id="custom-chevron"></i>
                            </div>
                            <div id="custom-hours-wrap" class="mt-3" style="display:{{ $selectedHours && !in_array((int)$selectedHours, [6, 10]) ? 'block' : 'none' }};">
                                <select class="form-select" name="custom_hours" id="custom_hours_select">
                                    <option value="">Select hours…</option>
                                    @foreach([1, 2, 3, 4, 5, 7, 8, 9, 12, 15, 20] as $n)
                                        <option value="{{ $n }}" {{ (int)$selectedHours === $n ? 'selected' : '' }}>
                                            {{ $n }} hour{{ $n > 1 ? 's' : '' }}
                                            @if($n >= 10) — 10% OFF
                                            @elseif($n >= 6) — 5% OFF
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div class="small text-muted mt-2">
                                    <i class="bi bi-info-circle me-1"></i>Discounts apply at 6+ hours (5%) and 10+ hours (10%).
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Tip box matching EasyLicence reference --}}
            <div class="tip-box mb-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="tip-icon">
                        <i class="bi bi-emoji-smile-fill"></i>
                        <span class="tip-flag">Tip</span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-3">How many lessons do I need?</h6>
                        <table class="tip-table">
                            <tr>
                                <td class="hrs-col"><strong>10-15hrs</strong></td>
                                <td class="who-col">New Learners</td>
                                <td class="desc-col">Beginners starting their driving journey from scratch.</td>
                                <td><span class="pkg-badge pkg-badge-best">BEST VALUE</span></td>
                            </tr>
                            <tr>
                                <td><strong>3-6hrs</strong></td>
                                <td>Overseas Licence</td>
                                <td>Perfect for those looking to learn our local driving rules.</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><strong>4-7hrs</strong></td>
                                <td>Refresher Drivers</td>
                                <td>Ideal for those needing a confidence boost or skill refresh.</td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Instructor Details card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Instructor Details</h6>
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="text-muted small mb-2">Instructor</div>
                            <div class="d-flex align-items-center gap-3">
                                @if($instructorProfile->profile_photo)
                                    <img src="{{ asset('storage/' . $instructorProfile->profile_photo) }}" class="rounded-circle" style="width:56px;height:56px;object-fit:cover;" alt="{{ $instructorProfile->user->name }}">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bolder text-white" style="width:56px;height:56px;background:linear-gradient(135deg, var(--sl-primary-500), var(--sl-teal-500));">{{ strtoupper(substr($instructorProfile->user->name ?? 'I', 0, 1)) }}</div>
                                @endif
                                <div>
                                    <div class="fw-bold">{{ $instructorProfile->user->name }}</div>
                                    <div class="small text-muted">${{ (int) $lessonPrice }}/hr</div>
                                    <div class="small text-muted">Offers 1 &amp; 2hr lessons</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small mb-2">Vehicle</div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:#f5f5f5;border:1px solid var(--sl-gray-200);">
                                    <i class="bi bi-car-front-fill" style="font-size:1.6rem;color:var(--sl-primary-600);"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ trim(implode(' ', array_filter([$instructorProfile->vehicle_make, $instructorProfile->vehicle_model, $instructorProfile->vehicle_year]))) ?: 'Vehicle' }}</div>
                                    <div class="small text-muted">5-star ANCAP rating</div>
                                    <div class="small text-muted">Dual controls fitted</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Right Sidebar: Order Summary --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Order Summary</h6>
                <div id="order-summary-content">
                    <div class="d-flex justify-content-between align-items-center py-2 small">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-ticket-perforated"></i>
                            <span id="os-hours-label">10 hrs Booking Credit</span>
                        </span>
                        <span class="fw-semibold" id="os-subtotal">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 small">
                        <span>
                            Credit Discount
                            <span class="pkg-badge pkg-badge-discount ms-1" id="os-discount-badge">10% OFF</span>
                        </span>
                        <span class="text-success fw-semibold" id="os-discount">-$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 small">
                        <span>
                            Platform Processing Fee
                            <i class="bi bi-info-circle text-muted ms-1" title="4% processing fee"></i>
                        </span>
                        <span id="os-fee">$0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="fw-bold">Total Payment Due</span>
                        <span class="fw-bolder fs-5" id="os-total">$0.00</span>
                    </div>
                    <p class="small text-muted mb-3">Or 4 payments of <span id="os-instalment">$0.00</span></p>
                </div>
                <button type="submit" form="amount-form" class="btn btn-warning w-100 fw-semibold">
                    Continue <i class="bi bi-chevron-right ms-1"></i>
                </button>
            </div>
        </div>

        {{-- Buy Now Pay Later --}}
        <div class="bnpl-panel">
            <div class="bnpl-title">
                Buy Now Pay Later <i class="bi bi-info-circle text-muted small"></i>
            </div>
            <div class="bnpl-amount">4 payments of <span id="bnpl-amount">$0.00</span></div>
            <div class="bnpl-badges">
                <span class="bnpl-badge paypal"><i class="bi bi-paypal me-1"></i>Pay in 4</span>
                <span class="bnpl-badge afterpay">afterpay&lt;&gt;</span>
                <span class="bnpl-badge klarna">Klarna</span>
            </div>
        </div>

        {{-- Trust signals --}}
        <div class="trust-panel">
            <h6><i class="bi bi-shield-check text-success me-1"></i>Purchase With Peace Of Mind</h6>
            <p>Flexible rebooking if your plans change.</p>

            <h6><i class="bi bi-calendar2-check text-primary me-1"></i>Manage Your Lessons Online</h6>
            <p>24/7 access. Manage your account. Switch your instructor at no cost.</p>

            <h6><i class="bi bi-lock-fill text-warning me-1"></i>Secure Payments</h6>
            <p>We use 100% secure payments to provide you with a simple and safe experience.</p>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Package option cards */
    .package-option {
        display: flex;
        align-items: flex-start;
        gap: 0.9rem;
        padding: 1.1rem 1.25rem;
        border: 2px solid var(--sl-gray-200);
        border-radius: 12px;
        margin-bottom: 0.75rem;
        cursor: pointer;
        position: relative;
        transition: all 0.2s;
        background: #fff;
    }
    .package-option:hover {
        border-color: var(--sl-accent-500);
        background: #fffbeb;
    }
    .package-option.selected,
    .package-option:has(input[type="radio"]:checked) {
        border-color: var(--sl-accent-500);
        background: #fffbeb;
    }
    .package-option input[type="radio"] { position: absolute; opacity: 0; pointer-events: none; }
    .pkg-radio-dot {
        width: 22px; height: 22px;
        border: 2px solid var(--sl-gray-300);
        border-radius: 50%;
        flex-shrink: 0;
        margin-top: 2px;
        position: relative;
        transition: all 0.15s;
    }
    .package-option:has(input[type="radio"]:checked) .pkg-radio-dot {
        border-color: var(--sl-accent-500);
        background: var(--sl-accent-500);
    }
    .package-option:has(input[type="radio"]:checked) .pkg-radio-dot::after {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 10px; height: 10px;
        background: #fff;
        border-radius: 50%;
    }

    .pkg-badge {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        letter-spacing: 0.03em;
    }
    .pkg-badge-discount {
        background: #d1f4e1;
        color: #0b7b3c;
    }
    .pkg-badge-best {
        background: #ffe2e0;
        color: #c01c1c;
    }

    /* Tip box */
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
    .tip-table {
        width: 100%;
        border-collapse: collapse;
    }
    .tip-table td {
        padding: 0.35rem 0.5rem;
        font-size: 0.88rem;
        vertical-align: middle;
    }
    .tip-table .hrs-col, .tip-table td:first-child {
        color: var(--sl-gray-900);
        white-space: nowrap;
        width: 90px;
    }
    .tip-table .who-col, .tip-table td:nth-child(2) {
        color: var(--sl-gray-700);
        font-weight: 600;
        width: 170px;
    }

    @media (max-width: 768px) {
        .tip-table td { display: block; padding: 0.15rem 0; }
        .tip-icon { display: none; }
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    var LESSON_PRICE = {{ $lessonPrice }};
    var PLATFORM_FEE_PERCENT = {{ (float) \App\Models\SiteSetting::get('platform_fee_percent', 4) }};

    function getDiscountPct(hours) {
        if (hours >= 10) return 10;
        if (hours >= 6) return 5;
        return 0;
    }

    function formatMoney(n) {
        return '$' + (Number(n) || 0).toFixed(2);
    }

    function getSelectedHours() {
        var selected = document.querySelector('input[name="hours"]:checked');
        if (!selected) return 0;
        if (selected.value === 'custom') {
            return parseInt(document.getElementById('custom_hours_select').value, 10) || 0;
        }
        return parseInt(selected.value, 10) || 0;
    }

    function updateSummary() {
        var hours = getSelectedHours();
        var subtotal = LESSON_PRICE * hours;
        var discountPct = getDiscountPct(hours);
        var discount = Math.round(subtotal * discountPct) / 100;
        var afterDiscount = subtotal - discount;
        var fee = Math.round(afterDiscount * PLATFORM_FEE_PERCENT) / 100;
        var total = afterDiscount + fee;
        var instalment = total / 4;

        document.getElementById('os-hours-label').textContent = hours + ' hr' + (hours !== 1 ? 's' : '') + ' Booking Credit';
        document.getElementById('os-subtotal').textContent = formatMoney(subtotal);
        document.getElementById('os-discount').textContent = '-' + formatMoney(discount);
        document.getElementById('os-discount-badge').textContent = discountPct + '% OFF';
        document.getElementById('os-discount-badge').style.display = discountPct > 0 ? 'inline-block' : 'none';
        document.getElementById('os-fee').textContent = formatMoney(fee);
        document.getElementById('os-total').textContent = formatMoney(total);
        document.getElementById('os-instalment').textContent = formatMoney(instalment);
        var bnplAmt = document.getElementById('bnpl-amount');
        if (bnplAmt) bnplAmt.textContent = formatMoney(instalment);
    }

    // Package radio toggle
    document.querySelectorAll('input[name="hours"]').forEach(function(r) {
        r.addEventListener('change', function() {
            var isCustom = this.value === 'custom';
            document.getElementById('custom-hours-wrap').style.display = isCustom ? 'block' : 'none';
            document.getElementById('custom-chevron').classList.toggle('bi-chevron-up', isCustom);
            document.getElementById('custom-chevron').classList.toggle('bi-chevron-down', !isCustom);
            updateSummary();
        });
    });

    document.getElementById('custom_hours_select').addEventListener('change', updateSummary);

    // Make the whole option card clickable
    document.querySelectorAll('.package-option').forEach(function(label) {
        label.addEventListener('click', function(e) {
            var radio = this.querySelector('input[type="radio"]');
            if (radio && e.target.tagName !== 'SELECT' && e.target.tagName !== 'OPTION') {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            }
        });
    });

    // Validation before submit
    document.getElementById('amount-form').addEventListener('submit', function(e) {
        var hours = getSelectedHours();
        if (hours < 1) {
            e.preventDefault();
            alert('Please select how many lesson hours you want to purchase.');
            return false;
        }
    });

    updateSummary();
})();
</script>
@endpush
@endsection
