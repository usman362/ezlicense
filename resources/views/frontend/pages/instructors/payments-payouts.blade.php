@extends('layouts.frontend')
@section('title', 'Payments & Payouts — Get Paid at Booking — Secure Licence')
@section('meta_description', 'Every learner pays by card at the moment of booking. Bank payouts hit your account on your chosen 7, 14 or 28-day cycle. Just a 2.4% card-processing fee.')

@push('styles')
    @include('frontend.pages.instructors._feature-styles')
@endpush

@section('content')

{{-- ─────────── HERO ─────────── --}}
<section class="py-5">
    <div class="container">
        <nav class="small text-muted mb-3">
            <a href="{{ url('/') }}" class="text-muted text-decoration-none">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('instruct-with-us') }}" class="text-muted text-decoration-none">For Business</a>
            <span class="mx-1">/</span>
            <span class="text-dark">Payments &amp; Payouts</span>
        </nav>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="lg-eyebrow mb-3">Payments &amp; Payouts</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">Get paid when you book, not chase.</h1>
                <p class="text-muted mb-4">
                    Every learner pays by card at the moment of booking. Bank payouts hit your account on your
                    chosen 7, 14, or 28-day cycle. Just a 2.4% card-processing fee on payments you take.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Card-at-booking via Stripe — no cash-in-hand, no bank transfers.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Flexible bank payouts (7, 14, or 28-day cycles), not monthly franchise drip.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Per-product pricing · tax-compliant invoices auto-generated.</span></li>
                </ul>
                <a href="{{ route('instructor-application.show') }}" class="btn btn-warning btn-lg fw-semibold">Start taking card payments</a>
            </div>

            <div class="col-lg-6">
                <div class="lg-panel">
                    <div class="lg-row align-items-start" style="border-bottom:0;">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <div><div class="fw-semibold small">Payment received</div><div class="small text-muted">Olivia Kim booked a 1hr lesson</div></div>
                        <div class="text-end ms-auto"><div class="fw-bold">$85.00</div><div class="small text-muted">just now</div></div>
                    </div>
                    <div class="mt-2 p-3 rounded" style="background:#e7f7ee;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-bank text-success"></i>
                            <div><div class="fw-semibold small">Commonwealth Bank</div><div class="small text-muted">****4812 · Everyday Business</div></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div><div class="small fw-semibold text-uppercase text-muted" style="letter-spacing:.05em;">Deposit</div><div class="small text-muted">14 lessons · period 14–18 Apr</div></div>
                            <div class="fw-bolder text-success fs-5">+$1,247.40</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── PROBLEM ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="lg-eyebrow neg mb-3">Without it</span>
            <h2 class="fw-bolder mt-3">Payment chaos eats your evenings.</h2>
            <p class="text-muted mx-auto" style="max-width:640px;">Every independent instructor has had at least two of these happen this month.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['The "I\'ll transfer tonight"','You teach the lesson. They promise to bank-transfer. Three days later, you\'re sending the third polite chase message.'],
                ['The franchise drip','Driving school pays you monthly. Cash flow is a nightmare. You\'re teaching in April to pay for March\'s rent.'],
                ['The tax-time spreadsheet','You\'re backtracking through 800 WhatsApp messages to reconstruct who paid what. Your accountant is unimpressed.'],
            ] as [$t,$d])
                <div class="col-md-4">
                    <div class="lg-card">
                        <div class="lg-x"><i class="bi bi-x-lg"></i></div>
                        <h5 class="fw-bold">{{ $t }}</h5>
                        <p class="text-muted small mb-0">{{ $d }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── HOW IT WORKS ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="lg-eyebrow mb-3">How it works</span>
            <h2 class="fw-bolder mt-3">Money in your bank, on autopilot.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lg-step-num">1</div>
                <h5 class="fw-bold">Learner books + pays at the same moment</h5>
                <p class="text-muted small">No more booking without payment. Card details captured. Money pre-authorised until the lesson starts.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">Checkout</div>
                    <div class="row-line"><span class="text-muted">1hr lesson · Fri 9:00am</span><span class="fw-bold">$85.00</span></div>
                    <div class="d-flex align-items-center gap-2 small mt-1"><i class="bi bi-credit-card text-info"></i> •••• 4242 <i class="bi bi-check-circle-fill text-success ms-auto"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">2</div>
                <h5 class="fw-bold">Lesson runs. Payment captures automatically.</h5>
                <p class="text-muted small">No-show? Late-cancel window already expired? Card auto-charged. Zero awkward conversations.</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i><div><div class="fw-semibold small">Payment captured</div><div class="small text-muted">Lesson completed · $85.00</div></div></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">3</div>
                <h5 class="fw-bold">Bank payouts on your schedule</h5>
                <p class="text-muted small">No statements. No waiting for the school's monthly BPAY. Just deposits, every few days, direct to your account.</p>
                <div class="lg-statbox mt-3">
                    <div class="row-line"><span class="text-muted">Mon 14 Apr</span><span class="fw-bold text-success">+$612.40</span></div>
                    <div class="row-line"><span class="text-muted">Wed 16 Apr</span><span class="fw-bold text-success">+$385.00</span></div>
                    <div class="row-line"><span class="text-muted">Fri 18 Apr</span><span class="fw-bold text-success">+$627.50</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BENEFITS (yellow) ─────────── --}}
<section class="py-5 lg-yellow">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Numbers you'll feel in week one</h2>
            <p class="mb-0">Better cash flow. Less admin. No margin to us.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-cash','0% commission','No cut to Secure Licence. Just Stripe\'s card fee, passed through at cost.'],
                ['bi-graph-up-arrow','Flexible payouts (7, 14, or 28-day)','Choose your payout cadence 7, 14, or 28-day bank deposits. Not 30-day terms. Not monthly franchise drip.'],
                ['bi-geo','Zero chasing','Card captured at booking. They pay when they book, not after you teach.'],
                ['bi-receipt','Tax-ready','Auto-generated invoices + payout reports. Your accountant will stop charging you for "reconciliation".'],
            ] as [$ic,$t,$d])
                <div class="col-md-6 col-lg-3">
                    <div class="lg-yellow-card">
                        <div class="lg-yellow-ic"><i class="bi {{ $ic }}"></i></div>
                        <h6 class="fw-bold">{{ $t }}</h6>
                        <p class="text-muted small mb-0">{{ $d }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── SOCIAL PROOF ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Real instructors. Real money. Faster.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Ben · 6 years · Brisbane','"I had $3,200 owing across my books when I signed up. Now everyone pays at booking. I don\'t even think about it."'],
                ['Rina · Left a franchise · Perth','"Franchise took 40% and paid monthly. Now I keep 97.6% and get paid twice a week. My partner nearly cried."'],
                ['Priya · Tax-time convert · Sydney','"First BAS I didn\'t dread. Every invoice, every payout, every fee, already categorised. My accountant actually smiled."'],
            ] as [$name,$quote])
                <div class="col-md-4">
                    <div class="lg-card">
                        <div class="lg-avatar mb-3"><i class="bi bi-person-fill"></i></div>
                        <div class="fw-bold mb-2">{{ $name }}</div>
                        <p class="text-muted small mb-0">{{ $quote }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@include('frontend.pages.instructors._ecosystem', ['exclude' => 'payments-payouts'])
@include('frontend.pages.instructors._business-cta')

@endsection
