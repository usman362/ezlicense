@extends('layouts.frontend')
@section('title', 'Your Listing & Profile — Secure Licence')
@section('meta_description', 'A professional listing, written for you. Photo, bio, qualifications, reviews, polished and SEO-optimised so learners searching your suburb actually find you.')

@push('styles')
    @include('frontend.pages.instructors._feature-styles')
    <style>
        .lg-prof-tag{display:inline-block;background:#f1f3f5;color:#495057;font-size:.72rem;font-weight:600;padding:.2rem .55rem;border-radius:.4rem;margin:0 .25rem .25rem 0;}
        .lg-prof-stat{text-align:center;}
        .lg-prof-stat .n{font-weight:700;font-size:1.05rem;}
        .lg-prof-stat .l{font-size:.72rem;color:#8b929c;}
    </style>
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
            <span class="text-dark">Your Listing &amp; Profile</span>
        </nav>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="lg-eyebrow mb-3">Your Listing &amp; Profile</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">Look like the pro you are, without the $2k website.</h1>
                <p class="text-muted mb-4">
                    A professional listing, written for you. Photo, bio, qualifications, vehicle, service area,
                    reviews — polished and SEO-optimised so learners searching your suburb actually find you.
                    No copywriting. No $500 photo shoot. No wrestling a Facebook page that nobody sees.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Our concierge team writes your bio — you just answer a phone call.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Ranks for "[your suburb] driving instructor" on Google.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Verified badge, live availability, real reviews — trust baked in.</span></li>
                </ul>
                <a href="{{ route('instructor-application.show') }}" class="btn btn-warning btn-lg fw-semibold">Start your listing</a>
            </div>

            <div class="col-lg-6">
                <div class="lg-panel">
                    <div class="d-flex align-items-start gap-3 mb-2">
                        <div class="lg-avatar" style="width:54px;height:54px;font-size:1.1rem;">MK</div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold fs-5">Martin K.</span>
                                <span class="lg-badge new"><i class="bi bi-patch-check-fill"></i> Verified</span>
                            </div>
                            <div class="small text-muted">Gold Coast</div>
                            <div class="small"><span class="text-warning">★★★★★</span> <span class="fw-semibold">4.9</span> <span class="text-muted">(127 reviews)</span></div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold fs-4">$75</div>
                            <div class="small text-muted">per hour</div>
                        </div>
                    </div>
                    <p class="small text-muted">Patient, calm and qualified instructor with 5+ years helping Gold Coast learners pass first go. Test-route specialist for Southport, Labrador and Burleigh.</p>
                    <div class="mb-3">
                        <span class="lg-prof-tag">Auto</span>
                        <span class="lg-prof-tag">Manual</span>
                        <span class="lg-prof-tag">Test prep</span>
                        <span class="lg-prof-tag">English</span>
                    </div>
                    <div class="d-flex gap-2 mb-3">
                        <a href="#" class="btn btn-outline-dark btn-sm flex-fill" onclick="return false;">View full profile</a>
                        <a href="#" class="btn btn-warning btn-sm flex-fill fw-semibold" onclick="return false;">Book now</a>
                    </div>
                    <div class="d-flex justify-content-around pt-2 border-top">
                        <div class="lg-prof-stat"><div class="n">847</div><div class="l">lessons</div></div>
                        <div class="lg-prof-stat"><div class="n">5 yrs</div><div class="l">teaching</div></div>
                        <div class="lg-prof-stat"><div class="n">12</div><div class="l">postcodes</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── PROBLEM (6 cards) ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="lg-eyebrow neg mb-3">Without us</span>
            <h2 class="fw-bolder mt-3">Your marketing is a Facebook page with 43 likes.</h2>
            <p class="text-muted mx-auto" style="max-width:640px;">
                You're a qualified professional. Your online presence says "bloke's cousin set it up."
                Here's what independent instructors tell us about selling themselves online.
            </p>
        </div>
        <div class="row g-4">
            @foreach([
                ['The DIY trust gap','Facebook page, patchy photos, no reviews visible, no verification. Learners aren\'t handing $75 to a stranger with a blurry profile pic. They bounce.'],
                ['Buried on page 4','Google SEO is a full-time job. You can\'t outrank franchises and aggregators on your own. Your site sits on page 4 where nobody clicks.'],
                ['$2k for a website that flops','Paid a guy $2,000 to build a site. $500 for a photographer. It still doesn\'t convert, because you\'re an instructor, not a marketer. You don\'t know what turns a visitor into a booking.'],
                ['Franchise buries your name','In a school you\'re "instructor 47." Learners never see your face until the lesson starts. Zero personal brand. Walk away and you take no following.'],
                ['Writing your own bio','You sit down to write about yourself and freeze. Is "patient and friendly" overdone? Do you mention the 20-year trucking career? The blank page wins. Again.'],
                ['No live availability','Learner calls. You\'re booked out. They go elsewhere and never come back. Your website has no calendar. You\'re playing phone-tag in 2026.'],
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
            <h2 class="fw-bolder mt-3">You answer a phone call. We do the rest.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lg-step-num">1</div>
                <h5 class="fw-bold">Concierge call</h5>
                <p class="text-muted small">A 20-minute call. We ask about your teaching style, postcodes, pass rates, pet peeves. You talk. We write the bio.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">We cover</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Teaching style + philosophy</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Credentials + background</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Postcodes + vehicle + rates</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Photo guidance (which converts)</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">2</div>
                <h5 class="fw-bold">We build + verify</h5>
                <p class="text-muted small">We write the listing, optimise it for SEO, check your credentials and police clearance, and drop the verified badge on it.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">Verification checks</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Instructor accreditation (current)</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Police clearance</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Vehicle registration + insurance</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Identity verification</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">3</div>
                <h5 class="fw-bold">You go live on Google</h5>
                <p class="text-muted small">Your profile ranks for "[your suburb] driving instructor" searches. Learners find you, see the reviews, check the live calendar, book on the spot.</p>
                <div class="lg-statbox mt-3">
                    <div class="row-line"><span class="text-muted">Ranking page</span><span class="fw-bold text-success">Page 1</span></div>
                    <div class="row-line"><span class="text-muted">Live calendar</span><span class="fw-bold">Yes</span></div>
                    <div class="row-line"><span class="text-muted">Verified badge</span><span class="fw-bold">Yes</span></div>
                    <div class="row-line"><span class="text-muted">Your cost</span><span class="fw-bold text-success">$0</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BENEFITS (yellow) ─────────── --}}
<section class="py-5 lg-yellow">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">A $5k marketing package. For nothing.</h2>
            <p class="mb-0">What a marketing agency would charge you five figures to produce, built, maintained and optimised on your behalf.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-pencil-square','Professionally written','Your bio, written by people who write bios for a living. You never touch a keyboard. It actually sounds like you, just tighter.'],
                ['bi-graph-up-arrow','Ranks on Google','Your listing appears for "[suburb] driving instructor" and related searches. SEO we handle. Not you, not a freelancer, not page 4.'],
                ['bi-shield-check','Trust, pre-built','Verified badge, real reviews, visible pass rates, credentials on display. Learners book faster because the "is this guy legit?" question is already answered.'],
                ['bi-broadcast','Live availability','Learners only see you when you actually have slots. No phone tag, no missed enquiries, no "sorry, fully booked" emails at 10pm.'],
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
            <h2 class="fw-bolder">Three instructors. One listing engine.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Martin K. · 847 lessons · Gold Coast','"My Secure Licence profile looks better than the website I paid a guy $800 to build. Same photo, different layout, the conversion difference was night and day."'],
                ['Priya S. · 412 lessons · Sydney','"I never had to write the bio. They rang me, asked 15 questions, and two days later a polished profile was live. I would have procrastinated for 6 months."'],
                ['Angelo D. · 1,240 lessons · Melbourne','"I left a franchise that was running paid Google ads for itself. My Secure Licence listing now outranks those ads in my suburb. I pay nothing for that."'],
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
