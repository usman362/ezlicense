@extends('layouts.frontend')
@section('title', 'No-Show Protection — Late-Cancel Fees Auto-Enforced — Secure Licence')
@section('meta_description', 'Cancel less than 24 hours before a lesson and the learner forfeits the payment automatically, at the payment layer. No awkward calls, no eating losses.')

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
            <span class="text-dark">No-Show Protection</span>
        </nav>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="lg-eyebrow mb-3">No-Show Protection</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">A no-show costs you $70+. Now it costs them.</h1>
                <p class="text-muted mb-4">
                    Cancel less than 24 hours before a lesson and the learner forfeits the payment, automatically,
                    at the payment layer. No awkward calls. No policies to argue about. No eating losses to avoid
                    the conflict. (You also control how far in advance learners can book, and the minimum notice
                    they need to give.)
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Late-cancel fees charged automatically, no confrontation.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Self-service reschedule, learners handle it themselves.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Audit trail at every change (if a dispute ever happens).</span></li>
                </ul>
                <a href="{{ route('instructor-application.show') }}" class="btn btn-warning btn-lg fw-semibold">Stop eating no-show losses</a>
            </div>

            <div class="col-lg-6">
                <div class="lg-panel">
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <div class="small fw-semibold text-uppercase" style="letter-spacing:.05em;color:#b42318;">Late cancellation</div>
                            <div class="fw-bold fs-5">Fri 19 Apr · 9:00am lesson</div>
                        </div>
                        <span class="lg-x ms-auto" style="margin-bottom:0;"><i class="bi bi-x-lg"></i></span>
                    </div>
                    <div class="row-line"><span class="text-muted small">Learner</span><span class="fw-semibold small ms-auto">Olivia Kim</span></div>
                    <div class="row-line"><span class="text-muted small">Original booking</span><span class="fw-semibold small ms-auto">Fri 19 Apr · 9:00am</span></div>
                    <div class="row-line"><span class="text-muted small">Cancelled at</span><span class="fw-semibold small ms-auto">Fri 19 Apr · 6:33am</span></div>
                    <div class="row-line"><span class="text-muted small">Cancellation window</span><span class="fw-semibold small ms-auto">24 hours</span></div>
                    <div class="row-line"><span class="text-muted small">Inside window?</span><span class="fw-semibold small ms-auto" style="color:#b42318;">Yes · 2h 37m notice</span></div>
                    <div class="mt-2 p-3 rounded d-flex justify-content-between align-items-center" style="background:#e7f7ee;">
                        <div><div class="fw-semibold small text-success">Late-cancel fee</div><div class="small text-muted">Charged to card ····4242</div></div>
                        <div class="fw-bolder text-success fs-5">$85.00</div>
                    </div>
                    <div class="small text-muted mt-2">Payout on Monday · You teach something else in that slot anyway.</div>
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
            <h2 class="fw-bolder mt-3">You've eaten $200–500 a month in no-shows.</h2>
            <p class="text-muted mx-auto" style="max-width:640px;">You know you should charge. You don't. Here's why.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['The awkward ask','They cancel at 7am for a 9am lesson. You want your $85. You text "hey about the cancel fee…" and immediately regret it. You eat the loss.'],
                ['The policy nobody signed','You told them about your 24-hour policy once, verbally, six months ago. Now they claim they never agreed to it. You have nothing in writing.'],
                ['The reschedule nightmare','One reschedule turns into 6 WhatsApp messages. Three proposed times. Two no-replies. You lose 20 minutes of your day, every time.'],
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
            <h2 class="fw-bolder mt-3">The policy you've always wanted, finally real.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lg-step-num">1</div>
                <h5 class="fw-bold">Enforced cancellation policy</h5>
                <p class="text-muted small">A standard 24-hour cancellation window, automatically enforced. You also control how far ahead learners can book, and the minimum notice they need to give.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">The policy</div>
                    <div class="row-line"><span class="text-muted">Free reschedule</span><span class="fw-bold text-success">&gt; 24 hrs</span></div>
                    <div class="row-line"><span class="text-muted">Full charge</span><span class="fw-bold" style="color:#b42318;">&lt; 24 hrs / no-show</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">2</div>
                <h5 class="fw-bold">Learner accepts it at booking</h5>
                <p class="text-muted small">They see the policy before they check out. Click-through consent. Stored forever. No dispute about "I didn't know."</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex gap-2 small"><i class="bi bi-check-square-fill text-warning"></i> I agree to the 24-hour cancellation policy. Cancellations inside the window may incur a fee.</div>
                    <div class="small text-muted mt-1">Stored with timestamp + IP</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">3</div>
                <h5 class="fw-bold">System enforces it silently</h5>
                <p class="text-muted small">They cancel inside the window? Card auto-charged. They see the receipt. You get the deposit. You never had the conversation.</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i><div><div class="fw-semibold small">Late cancellation enforced</div><div class="small text-muted">Card ···· 4242 charged $85.00 · receipt emailed + SMS'd automatically</div></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BENEFITS (yellow) ─────────── --}}
<section class="py-5 lg-yellow">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Your income, protected.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-cash','$300+/mo recovered','Average instructor eats 4–5 no-shows monthly. At $85 each, that\'s $340–$425. Reclaimed.'],
                ['bi-heart','Zero awkward convos','You stay the nice instructor who teaches the lesson. The system plays bad cop.'],
                ['bi-shield-check','Learner behaviour shifts','When there\'s a real fee, people give 24hr notice. No-shows drop by 60–70%.'],
                ['bi-clock','1 hr/wk saved','Self-service reschedules kill phone-tag entirely.'],
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
            <h2 class="fw-bolder">Instructors who stopped eating losses.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Angela · 15 years · Melbourne West','"First month, $340 in late-cancel fees I\'d never have charged on my own. I just let the system do it. Nobody complained."'],
                ['Mel · 2 years · Brisbane','"My no-show rate went from 8% to under 2% in 6 weeks. Learners knew there was a real fee. Behaviour changed."'],
                ['Hamed · 7 years · Geelong','"Had a disputed fee. I pulled up the consent log with the timestamp. Stripe sided with me. Done in 2 minutes."'],
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

@include('frontend.pages.instructors._ecosystem', ['exclude' => 'no-show-protection'])
@include('frontend.pages.instructors._business-cta')

@endsection
