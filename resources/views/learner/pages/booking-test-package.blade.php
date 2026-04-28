@extends('layouts.booking', ['step' => 2])

@section('title', 'Add a Driving Test Package')

@section('content')
@php
    $package = session('learner_booking_package', []);
    $hours = (int) ($package['hours'] ?? 0);
    $discountPct = (float) ($package['discount_pct'] ?? 0);
    $testPackagePrice = (float) ($instructorProfile->test_package_price ?? 225);
    $lessonPrice = (float) ($instructorProfile->lesson_price ?? 75);

    $hoursSubtotal = $lessonPrice * $hours;
    $hoursDiscount = round($hoursSubtotal * $discountPct / 100, 2);
    $hoursAfterDiscount = $hoursSubtotal - $hoursDiscount;
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="mb-4">
            <h3 class="fw-bolder mb-1" style="letter-spacing:-0.02em;">Add a Driving Test Package</h3>
            <p class="text-muted mb-0">Ace your test!</p>
        </div>

        {{-- Test Package card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
                    <h5 class="fw-bold mb-0">Driving Test Package</h5>
                    <h4 class="fw-bolder mb-0">${{ number_format($testPackagePrice, 0) }}</h4>
                </div>

                <h6 class="text-muted small fw-semibold text-uppercase mb-3" style="letter-spacing:0.06em;">Package details</h6>

                <div class="row g-4 align-items-center">
                    {{-- Visual: P-plate + instructor + car --}}
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-plate">P</div>
                            @if($instructorProfile->profile_photo)
                                <img src="{{ asset('storage/' . $instructorProfile->profile_photo) }}" class="rounded-circle" style="width:48px;height:48px;object-fit:cover;border:2px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.1);" alt="Instructor">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bolder text-white" style="width:48px;height:48px;background:linear-gradient(135deg, var(--sl-primary-500), var(--sl-teal-500));border:2px solid #fff;">{{ strtoupper(substr($instructorProfile->user->name ?? 'I', 0, 1)) }}</div>
                            @endif
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:48px;background:#f5f5f5;border:1px solid var(--sl-gray-200);">
                                <i class="bi bi-car-front-fill" style="font-size:1.4rem;color:var(--sl-primary-600);"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Features list --}}
                    <div class="col-md-8">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-2 small">
                                    <i class="bi bi-clock text-muted mt-1"></i>
                                    <span>2.5hr Test Package</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-2 small">
                                    <i class="bi bi-geo-alt text-muted mt-1"></i>
                                    <span>Pick up &amp; Drop off included</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-2 small">
                                    <i class="bi bi-car-front text-muted mt-1"></i>
                                    <span>Use instructor's vehicle for test</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-2 small">
                                    <i class="bi bi-check2-square text-muted mt-1"></i>
                                    <span>45 minute pre-test warm up lesson</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tip box --}}
        <div class="tip-box">
            <div class="d-flex align-items-start gap-3">
                <div class="tip-icon">
                    <i class="bi bi-emoji-smile-fill"></i>
                    <span class="tip-flag">Tip</span>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-2">Ace Your Driving Test with Confidence!</h6>
                    <p class="small mb-0">
                        Our Driving Test Package gets you calm, focused, and ready to roll with pick-up &amp; drop-off, a warm-up lesson, and use of your instructor's car.
                    </p>
                </div>
                <div class="d-none d-md-block">
                    <i class="bi bi-arrow-up-right text-muted" style="font-size:2rem;transform:rotate(-15deg);display:inline-block;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Sidebar: Order Summary --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Order Summary</h6>

                <div class="d-flex justify-content-between align-items-center py-2 small">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-ticket-perforated"></i>
                        <span>{{ $hours }} hrs Booking Credit</span>
                    </span>
                    <span class="fw-semibold">${{ number_format($hoursSubtotal, 2) }}</span>
                </div>

                @if($discountPct > 0)
                    <div class="d-flex justify-content-between align-items-center py-2 small">
                        <span>
                            Credit Discount
                            <span class="ms-1" style="font-size:0.7rem;padding:0.1rem 0.45rem;background:#d1f4e1;color:#0b7b3c;font-weight:700;border-radius:12px;">{{ (int) $discountPct }}% OFF</span>
                        </span>
                        <span class="text-success fw-semibold">-${{ number_format($hoursDiscount, 2) }}</span>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center py-2 small">
                    <span>
                        Platform Processing Fee
                        <i class="bi bi-info-circle text-muted ms-1" title="Processing fee"></i>
                    </span>
                    <span>${{ number_format(round($hoursAfterDiscount * 0.04, 2), 2) }}</span>
                </div>

                <hr>
                @php
                    $platformFee = round($hoursAfterDiscount * 0.04, 2);
                    $total = $hoursAfterDiscount + $platformFee;
                @endphp
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="fw-bold">Total Payment Due</span>
                    <span class="fw-bolder fs-5">${{ number_format($total, 2) }}</span>
                </div>
                <p class="small text-muted mb-3">Or 4 payments of ${{ number_format($total / 4, 2) }}</p>

                {{-- Action buttons --}}
                <form method="POST" action="{{ route('learner.bookings.test-package.store') }}">
                    @csrf
                    <input type="hidden" name="action" value="add" id="action-input">
                    <button type="submit" class="btn btn-warning w-100 fw-semibold mb-2" onclick="document.getElementById('action-input').value='add';">
                        <i class="bi bi-plus-lg me-1"></i> Add a Driving Test Package
                    </button>
                    <button type="submit" class="btn btn-outline-secondary w-100" onclick="document.getElementById('action-input').value='skip';">
                        Skip
                    </button>
                </form>
            </div>
        </div>

        {{-- Buy Now Pay Later --}}
        <div class="bnpl-panel">
            <div class="bnpl-title">
                Buy Now Pay Later <i class="bi bi-info-circle text-muted small"></i>
            </div>
            <div class="bnpl-amount">4 payments of ${{ number_format($total / 4, 2) }}</div>
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
    .p-plate {
        width: 32px;
        height: 32px;
        background: #ffd500;
        border: 2px solid #fff;
        border-radius: 6px;
        color: #c01c1c;
        font-weight: 800;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        flex-shrink: 0;
    }

    /* Tip box (shared with amount page) */
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
@endsection
