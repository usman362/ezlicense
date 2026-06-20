@extends('layouts.frontend')
@section('title', 'Lead Generation for Driving Instructors — Secure Licence')
@section('meta_description', 'Google ads, SEO, referral programs and brand campaigns — we run the whole learner-acquisition engine so you don\'t have to. Zero cost until a lesson completes.')

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
            <span class="text-dark">Lead Generation</span>
        </nav>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="lg-eyebrow mb-3">Lead Generation</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">We spend six figures a month bringing learners to you.</h1>
                <p class="text-muted mb-4">
                    Google ads, SEO, referral programs, brand campaigns — the whole acquisition engine.
                    We run it so you don't have to. Thousands of learners have already booked through
                    Secure Licence. The next one could be in your postcode, right now.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Most instructors get their first booking in &lt;7 days of going live.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Leads matched to your postcode, hours and transmission.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Zero cost to you unless a lesson actually completes.</span></li>
                </ul>
                <a href="{{ route('instructor-application.show') }}" class="btn btn-warning btn-lg fw-semibold">Start getting learner leads</a>
            </div>

            <div class="col-lg-6">
                <div class="lg-panel">
                    <div class="d-flex align-items-center mb-2">
                        <div>
                            <div class="small text-muted text-uppercase fw-semibold" style="letter-spacing:.05em;">New matches</div>
                            <div class="fw-bold fs-5">This week</div>
                        </div>
                        <span class="lg-badge new ms-auto">7 new</span>
                    </div>
                    <div class="lg-row">
                        <div class="lg-avatar">JM</div>
                        <div><div class="fw-semibold">Jamie M.</div><div class="small text-muted">Gold Coast · 2.8km away · Auto</div></div>
                        <span class="lg-badge new">New</span>
                    </div>
                    <div class="lg-row">
                        <div class="lg-avatar">SK</div>
                        <div><div class="fw-semibold">Sarah K.</div><div class="small text-muted">Southport · 3.8km away · Manual</div></div>
                        <span class="lg-badge new">New</span>
                    </div>
                    <div class="lg-row">
                        <div class="lg-avatar">PR</div>
                        <div><div class="fw-semibold">Priya R.</div><div class="small text-muted">Burleigh · 5.4km · Auto · Test in 2 weeks</div></div>
                        <span class="lg-badge booked">Booked</span>
                    </div>
                    <div class="lg-row">
                        <div class="lg-avatar">DL</div>
                        <div><div class="fw-semibold">Declan L.</div><div class="small text-muted">Labrador · 1.9km · Auto · Test in 3 weeks</div></div>
                        <span class="lg-badge booked">Booked</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-3 small text-success fw-semibold">
                        <i class="bi bi-broadcast"></i> Live · matching every 10 minutes
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
            <span class="lg-eyebrow neg mb-3">Without us</span>
            <h2 class="fw-bolder mt-3">Finding learners is half the job. The half nobody taught you.</h2>
            <p class="text-muted mx-auto" style="max-width:640px;">
                Being a great instructor doesn't fill your diary. Here's what independent instructors
                tell us about the learner-acquisition grind.
            </p>
        </div>
        <div class="row g-4">
            @foreach([
                ['The referral plateau','Friends-of-friends got you to 10–15 learners. Now the top\'s slowing. Each one who passes is one less name in your diary.'],
                ['Google is expensive','Running your own ads? $4–8 per click and 98% of those clicks don\'t convert. You burn $500 before a single booking.'],
                ['Quiet August dread','Summer hits. Learners go on holiday. Without a background pipeline, your income halves for 8 weeks, every year.'],
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
            <h2 class="fw-bolder mt-3">You teach. We find.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lg-step-num">1</div>
                <h5 class="fw-bold">Learners find Secure Licence</h5>
                <p class="text-muted small">Google ads, SEO, social, brand campaigns. We spend six figures monthly acquiring learners across Australia.</p>
                <div class="lg-statbox mt-3">
                    <div class="row-line"><span class="text-muted">Monthly learners</span><span class="fw-bold">8,400+</span></div>
                    <div class="row-line"><span class="text-muted">Avg acquisition cost</span><span class="fw-bold">We absorb it</span></div>
                    <div class="row-line"><span class="text-muted">Your cost</span><span class="fw-bold text-success">$0</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">2</div>
                <h5 class="fw-bold">Match to your profile</h5>
                <p class="text-muted small">Our matching system pairs learners with instructors based on suburb, hours, transmission, rate and preferences.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">Match quality</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Suburbs within your area</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Transmission you teach</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Hours that fit your schedule</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Price point they've accepted</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">3</div>
                <h5 class="fw-bold">They book. You teach.</h5>
                <p class="text-muted small">Learner pays at booking, lesson appears in your calendar. You teach. Payouts land on your chosen 7, 14 or 28-day cycle.</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i><div><div class="fw-semibold small">New booking · Jamie M.</div><div class="small text-muted">1-hour lesson · $75</div></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BENEFITS (yellow) ─────────── --}}
<section class="py-5 lg-yellow">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Leads without the legwork.</h2>
            <p class="mb-0">Outcomes you'd otherwise pay a marketing agency five figures to produce.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-graph-up-arrow','Days, not months','Most instructors get their first booking in <7 days of going live. Referral pipelines take years to reach that volume.'],
                ['bi-cash-stack','$0 marketing spend','You don\'t run ads. Don\'t pay a web guy. Don\'t waste $500 testing Facebook. We absorb the entire acquisition cost.'],
                ['bi-diagram-3','Diversified pipeline','When referrals dip, we fill the gap. When you\'re full, leads queue. You stop being hostage to one friend\'s recommendation.'],
                ['bi-shield-check','Acquisition cost, not ongoing tax','We charge a fee only when we bring you a learner. More reliable than running ads or hiring an agency — you pay when a lesson completes.'],
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
            <h2 class="fw-bolder">Three instructors. Same lead engine.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Martin · 400 lessons','"I went from 12 lessons a week via referrals to 25+ via Secure Licence. I spent zero on ads. The matching just works."'],
                ['Priya · 200 lessons','"Day 3 on the platform I had 8 bookings. I wasn\'t ready. Now I don\'t say no, the platform only sends leads I can handle."'],
                ['Angelo · 500 lessons','"I left a franchise that took a huge chunk. Secure Licence brought me more leads than the franchise ever did, and the support is genuinely better."'],
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
                ['bi-clock-history','Work Whenever You Want','Mornings, nights, weekends'],
                ['bi-hand-thumbs-up','Flexible Commitment','Pause or leave anytime'],
                ['bi-person-vcard','Your Listing','How learners see you'],
                ['bi-star','Reviews & Reputation','Ratings drive more leads'],
                ['bi-gem','Concierge Support','We handle the hard calls'],
                ['bi-tools','Tools Included','Calendar, payments, SMS, free'],
            ] as [$ic,$t,$d])
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="lg-mini">
                        <div class="lg-mini-ic"><i class="bi {{ $ic }}"></i></div>
                        <div class="fw-semibold small">{{ $t }}</div>
                        <div class="text-muted" style="font-size:.75rem;">{{ $d }}</div>
                    </div>
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
