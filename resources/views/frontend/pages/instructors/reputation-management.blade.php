@extends('layouts.frontend')
@section('title', 'Reputation Management — Reviews That Win Bookings — Secure Licence')
@section('meta_description', 'Every passed test becomes a verified review. Five-star reviews push you up the rankings, pull in more bookings, and unlock premium pricing.')

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
            <span class="text-dark">Reputation Management</span>
        </nav>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="lg-eyebrow mb-3">Reputation Management</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">Every passed test becomes a review. Every review, a lead.</h1>
                <p class="text-muted mb-4">
                    We auto-prompt every learner to review you the moment they pass. Five-star reviews push you
                    up the rankings, pull in more bookings, and unlock pricing above the platform default.
                    It's the only asset in this business that compounds.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Verified reviews from real learners, not DIY Google stars.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Higher ratings unlock premium pricing.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>A review system and policy that ensures reviews are fair.</span></li>
                </ul>
                <a href="{{ route('instructor-application.show') }}" class="btn btn-warning btn-lg fw-semibold">Start building your reputation</a>
            </div>

            <div class="col-lg-6">
                <div class="lg-panel">
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <div class="small text-muted text-uppercase fw-semibold" style="letter-spacing:.05em;">Your reputation</div>
                            <div class="fw-bold fs-5">Last 90 days</div>
                        </div>
                        <span class="lg-badge new ms-auto"><i class="bi bi-trophy-fill"></i> Top Rated</span>
                    </div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="text-center">
                            <div class="fw-bolder" style="font-size:2.4rem;line-height:1;">4.9</div>
                            <div class="text-warning small">★★★★★</div>
                            <div class="small text-muted">127 reviews</div>
                        </div>
                        <div class="flex-grow-1">
                            @foreach([['5',92],['4',7],['3',1],['2',0],['1',0]] as [$star,$pct])
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="small text-muted" style="width:10px;">{{ $star }}</span>
                                    <span class="lg-bar"><span style="left:0;width:{{ $pct }}%;"></span></span>
                                    <span class="small text-muted" style="width:34px;text-align:right;">{{ $pct }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="lg-row">
                        <div><div class="fw-semibold small">Jamie · Sep 2025</div><div class="small text-muted">"Nerns was patient and clear, passed first try!"</div></div>
                        <span class="text-warning small ms-auto">★★★★★</span>
                    </div>
                    <div class="lg-row">
                        <div><div class="fw-semibold small">Sarah · Aug 2025</div><div class="small text-muted">"The only instructor my anxious teen felt calm with."</div></div>
                        <span class="text-warning small ms-auto">★★★★★</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 small">
                        <span class="text-muted">Profile views → bookings</span>
                        <span class="fw-bold text-success">23% <span class="text-muted fw-normal">(Au avg 7%)</span></span>
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
            <h2 class="fw-bolder mt-3">You teach brilliantly. Nobody knows.</h2>
            <p class="text-muted mx-auto" style="max-width:640px;">
                A decade of great lessons. And if you leave the franchise tomorrow, you start from zero.
                Here's why the old way of building a name doesn't work.
            </p>
        </div>
        <div class="row g-4">
            @foreach([
                ['Franchise owns your reputation','Every five-star review goes to the school\'s Google page, not yours. The day you quit, ten years of goodwill stays with them.'],
                ['Google reviews are a dead end','Learners forget. Anyone can fake them. You get one a year, buried on page four of search. Not proof, noise.'],
                ['No reward for being great','Alone, a brilliant teacher and an average one charge the same rate. There\'s no system that rewards quality with more leads or higher prices.'],
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
            <h2 class="fw-bolder mt-3">Teach well. Get reviewed. Rank higher. Repeat.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lg-step-num">1</div>
                <h5 class="fw-bold">Learner passes. Prompt fires.</h5>
                <p class="text-muted small">The moment a learner marks their test as passed, we SMS and email them for a review. No chasing, no awkward asking.</p>
                <div class="lg-statbox mt-3">
                    <div class="row-line"><span class="text-muted">Review response rate</span><span class="fw-bold">68%</span></div>
                    <div class="row-line"><span class="text-muted">Average time to review</span><span class="fw-bold">4.2 hours</span></div>
                    <div class="row-line"><span class="text-muted">Your effort</span><span class="fw-bold text-success">Zero</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">2</div>
                <h5 class="fw-bold">Verified, then published</h5>
                <p class="text-muted small">Only learners who actually booked and paid can review. Our concierge team investigates disputes before anything goes live, fair reviews, nothing else.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">Every review checked</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Verified booking on record</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> One review per learner, one account</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Disputes investigated by concierge</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Review system + policy ensures fairness</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">3</div>
                <h5 class="fw-bold">More reviews. Higher rank. Premium pricing.</h5>
                <p class="text-muted small">Higher ratings push you up the search rankings, attract more leads, and unlock the ability to price above the platform default. Your reputation, compounding.</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex gap-2 small mb-1"><i class="bi bi-check-circle-fill text-success"></i> Higher rank in search</div>
                    <div class="d-flex gap-2 small mb-1"><i class="bi bi-check-circle-fill text-success"></i> More learner leads</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-success"></i> Premium pricing</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BENEFITS (yellow) ─────────── --}}
<section class="py-5 lg-yellow">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Reviews compound. Nothing else does.</h2>
            <p class="mb-0">One great lesson today is one more star rating next month, three more leads the month after, and a pricing bump the month after that.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-graph-up-arrow','Higher rank, more leads','Five-star instructors sit at the top of marketplace search. Higher ranking = more profile views = more bookings. It\'s a direct line.'],
                ['bi-cash','Price above the default','Higher ratings unlock the ability to price above the platform default. Your reviews are the receipt that justifies it.'],
                ['bi-person-badge','Your name, not the franchise','Every review is attached to you personally. Change platforms, move states, raise prices, your reputation comes with you, forever.'],
                ['bi-google','Indexed by Google','Your profile page is Google-indexed. Learners searching "driving instructor [suburb]" find your reviews, without you running a single ad.'],
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
            <h2 class="fw-bolder">Three instructors. Reputation doing the selling.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Martin · 400 lessons','"After 200+ reviews I now charge $15/hr above the platform default and the diary\'s still full. The reviews did the selling for me."'],
                ['Priya · 412 lessons · Sydney','"First 20 reviews took me from 5 lessons a week to 25 in four months. Every five-star pushed me up the rankings and brought three more leads."'],
                ['Angelo · 1,240 lessons · Melbourne','"Left the franchise with zero reviews, they all stayed on the school\'s page. I\'m now on 380 reviews on Secure Licence. It\'s my asset now, not theirs."'],
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
                ['bi-hand-thumbs-up','Flexible Commitment','Pause or leave anytime', route('for-instructors.flexible-commitment')],
                ['bi-person-vcard','Your Listing','How learners see you', route('for-instructors.your-listing-profile')],
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
