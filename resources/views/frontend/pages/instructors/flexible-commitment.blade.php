@extends('layouts.frontend')
@section('title', 'Flexible Commitment — No Contract, No Lock-In — Secure Licence')
@section('meta_description', 'No contract. No upfront fee. No lock-in. Pause for a holiday, leave with one click. $0 to join, no monthly fees, no exit fees — you stay because it works.')

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
            <a href="{{ route('instruct-with-us') }}" class="text-muted text-decoration-none">For Instructors</a>
            <span class="mx-1">/</span>
            <span class="text-dark">Flexible Commitment</span>
        </nav>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="lg-eyebrow mb-3">Flexible Commitment</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">No contract. No upfront. No lock-in.</h1>
                <p class="text-muted mb-4">
                    Franchises want $2–10k up front and a 12–24 month contract. We want $0 and zero strings.
                    Pause for a holiday. Leave with one click. Keep every learner you've taught. You're the boss.
                    We're the pipeline.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>$0 to join. No monthly fees. No exit fees. No notice period.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Pause bookings anytime — holidays, burnout, life happens.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Work here, run your own book, drive for a franchise — all at once.</span></li>
                </ul>
                <a href="{{ route('instructor-application.show') }}" class="btn btn-warning btn-lg fw-semibold">Join with zero commitment</a>
            </div>

            <div class="col-lg-6">
                <div class="lg-panel">
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <div class="small text-muted text-uppercase fw-semibold" style="letter-spacing:.05em;">Account status</div>
                            <div class="fw-bold fs-5">Your controls</div>
                        </div>
                        <span class="lg-badge new ms-auto">Live</span>
                    </div>
                    <div class="lg-row align-items-center" style="background:#e7f7ee;border-radius:.6rem;padding:.7rem .85rem;border-bottom:0;margin-bottom:.5rem;">
                        <div><div class="fw-semibold text-success">Active</div><div class="small text-muted">Accepting bookings</div></div>
                        <span class="lg-toggle ms-auto" style="background:#1a7f43;"></span>
                    </div>
                    <div class="lg-row">
                        <i class="bi bi-pause-circle text-muted"></i>
                        <div><div class="fw-semibold small">Pause bookings</div><div class="small text-muted">Stop new leads for a set period</div></div>
                        <span class="small text-muted ms-auto">1 click</span>
                    </div>
                    <div class="lg-row">
                        <i class="bi bi-calendar-event text-muted"></i>
                        <div><div class="fw-semibold small">Set holiday dates</div><div class="small text-muted">Auto-resume when you're back</div></div>
                        <span class="small text-muted ms-auto">Any dates</span>
                    </div>
                    <div class="lg-row">
                        <i class="bi bi-box-arrow-right text-muted"></i>
                        <div><div class="fw-semibold small">Leave platform</div><div class="small text-muted">One click. No exit fee.</div></div>
                        <span class="small text-muted ms-auto">No questions</span>
                    </div>
                    <div class="small text-muted mt-3">No notice period. No exit fee.<br>Account since March 2022 · 847 lessons taught</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── PROBLEM ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="lg-eyebrow neg mb-3">Without us</span>
            <h2 class="fw-bolder mt-3">Every other option traps you. Some with a contract. Some with the bills.</h2>
            <p class="text-muted mx-auto" style="max-width:640px;">
                The choice in front of most instructors isn't freedom vs structure — it's which flavour of locked-in you can stomach.
            </p>
        </div>
        <div class="row g-4">
            @foreach([
                ['Franchise = 12–24 months, $2–10k up front','Joining fee you\'ll never see again. Contract you can\'t exit without 6 months notice. Territory they own. Learners that stay with them when you leave.'],
                ['Going fully solo = "flexible" on paper','Technically you can stop whenever. Realistically, the mortgage doesn\'t pause. No pipeline means no buffer. You can\'t say no, so you don\'t.'],
                ['Other platforms bury the lock-in','Minimum lesson quotas. Exclusivity clauses. 30-day cancellation windows. The fine print reads like a franchise dressed up as an app.'],
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
            <h2 class="fw-bolder mt-3">Three buttons. That's the whole commitment model.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lg-step-num">1</div>
                <h5 class="fw-bold">Join in 15 minutes. Pay nothing.</h5>
                <p class="text-muted small">Profile, licence, ABN, car details. Done. No joining fee. No subscription. Leads start arriving once you're live.</p>
                <div class="lg-statbox mt-3">
                    <div class="row-line"><span class="text-muted">Joining fee</span><span class="fw-bold text-success">$0</span></div>
                    <div class="row-line"><span class="text-muted">Monthly fee</span><span class="fw-bold text-success">$0</span></div>
                    <div class="row-line"><span class="text-muted">Contract length</span><span class="fw-bold">None</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">2</div>
                <h5 class="fw-bold">Pause whenever you need to</h5>
                <p class="text-muted small">Holiday in Bali? Surgery? Busy patch with private learners? Hit pause. New bookings stop. Existing ones stay on your calendar.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">Pause options</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Pause for a week, a month, whenever</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Set holiday dates, auto-resume</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> No approval. No form. One tap.</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Come back when you're ready</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">3</div>
                <h5 class="fw-bold">Leave whenever. No exit fees.</h5>
                <p class="text-muted small">One click. Account closed. No exit fee. No 6-month notice. No lawyer letters.</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i><div><div class="fw-semibold small">Account closed · your data yours</div><div class="small text-muted">Learners kept · No exit · No catch</div></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BENEFITS (yellow) ─────────── --}}
<section class="py-5 lg-yellow">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">The opposite of a franchise.</h2>
            <p class="mb-0">Every benefit they charge you $6k and 18 months for, we give you for free, forever.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-cash','$0 to start','No joining fee. No equipment bond. No branded car lease. You don\'t owe us a dollar until a lesson actually completes.'],
                ['bi-clock','Pause on a whim','Overseas for 3 weeks. Recovering from a cold. Kids on school holidays. Hit pause. Nobody asks why. Come back whenever.'],
                ['bi-geo','Zero territory lock-in','Teach your private learners. Work a shift at a franchise. Run Secure Licence leads Tuesday through Thursday. Your business. Your rules.'],
                ['bi-patch-check','Pricing that scales with loyalty','Long-term learners reward you. Pricing scales as the relationship grows, so the economics keep getting better the longer you stay.'],
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
            <h2 class="fw-bolder">Three instructors. Three reasons they stayed, by choice.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Martin K. · 847 lessons · Gold Coast','"Paused for 3 weeks for an overseas trip. Came back, it turned back on like a tap. No phone calls. No penalty. Try doing that with a franchise."'],
                ['Priya S. · 412 lessons · Sydney','"Kept my full-time job while building up. Secure Licence was 10 hours a week for 6 months. No one pushed me to go full-time. I moved when I was ready, not when they were."'],
                ['Angelo D. · 1,240 lessons · Melbourne','"I left a franchise, tested Secure Licence for 2 months before I committed. Could have walked any day of it. That\'s why I didn\'t."'],
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

{{-- ─────────── ECOSYSTEM ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="h4 fw-bolder">Works seamlessly with every other part of the marketplace</h2>
        </div>
        <div class="row g-3">
            @foreach([
                ['bi-megaphone','Lead Generation','300k+ learners ready to book', route('for-instructors.lead-generation')],
                ['bi-clock-history','Work Whenever You Want','Mornings, nights, weekends', route('for-instructors.work-whenever-you-want')],
                ['bi-person-vcard','Your Listing','How learners see you', route('instruct-with-us')],
                ['bi-star','Reviews & Reputation','Ratings drive more leads', route('instruct-with-us')],
                ['bi-gem','Concierge Support','We handle the hard calls', route('instruct-with-us')],
                ['bi-tools','Tools included','Calendar, payments, SMS, free', route('instruct-with-us')],
            ] as [$ic,$t,$d,$url])
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ $url }}" class="text-decoration-none text-reset">
                        <div class="lg-mini">
                            <div class="lg-mini-ic"><i class="bi {{ $ic }}"></i></div>
                            <div class="fw-semibold small">{{ $t }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $d }}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── FINAL CTA ─────────── --}}
<section class="py-5 text-white" style="background:#14171c;">
    <div class="container text-center">
        <div class="text-warning fw-bold small text-uppercase mb-2" style="letter-spacing:.08em;">Zero upfront. No contract. Live in 48 hours.</div>
        <h2 class="fw-bolder mb-2 text-white">Start getting bookings this week.</h2>
        <p class="text-white-50 mb-4 mx-auto" style="max-width:560px;">
            Join instructors earning on Secure Licence. 15 minutes to apply, reviewed within 2 business days. Leave whenever you want.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="{{ route('instructor-application.show') }}" class="btn btn-warning btn-lg fw-semibold">Apply to join Secure Licence</a>
            <a href="{{ route('instruct-with-us') }}" class="btn btn-outline-light btn-lg">Back to overview</a>
        </div>
    </div>
</section>

@endsection
