@extends('layouts.frontend')
@section('title', 'Website + Booking Link — Your Free Profile Page — Secure Licence')
@section('meta_description', 'No website? You don\'t need one. Get a direct, shareable link to your verified profile — photo, pricing, reviews and one-click booking. No builder, no monthly fee.')

@push('styles')
    @include('frontend.pages.instructors._feature-styles')
    <style>
        .lg-browser{border:1px solid #e3e6ea;border-radius:.8rem;overflow:hidden;box-shadow:0 18px 50px rgba(20,23,28,.14);}
        .lg-browser-bar{background:#f1f3f5;padding:.5rem .75rem;display:flex;align-items:center;gap:.4rem;}
        .lg-browser-bar .dot{width:9px;height:9px;border-radius:50%;background:#cdd2d8;}
        .lg-browser-url{flex:1;background:#fff;border-radius:.4rem;font-size:.7rem;color:#8b929c;padding:.2rem .6rem;margin-left:.4rem;}
    </style>
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
            <span class="text-dark">Website + Booking Link</span>
        </nav>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="lg-eyebrow mb-3">Website + Booking Link</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">Don't have a website? You don't need one.</h1>
                <p class="text-muted mb-4">
                    The moment your Secure Licence profile is set up, you get a direct, shareable link to your
                    verified profile — your photo, pricing, reviews, service area, and a one-click booking flow.
                    Send it on socials, by SMS, or print it on a card. No website builder. No developers. No monthly fee.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>A verified Secure Licence profile, ready to share once setup is complete.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Copy + share your direct profile link by SMS, social, or business card.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Local SEO baked into the Secure Licence domain — rank for "[your suburb] driving instructor".</span></li>
                </ul>
                <a href="{{ route('instructor-application.show') }}" class="btn btn-warning btn-lg fw-semibold">Get your free booking page</a>
            </div>

            <div class="col-lg-6">
                <div class="lg-browser">
                    <div class="lg-browser-bar">
                        <span class="dot"></span><span class="dot"></span><span class="dot"></span>
                        <span class="lg-browser-url">securelicence.com.au/instructors/sarah-k-profile</span>
                    </div>
                    <div class="p-4 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="lg-avatar" style="width:52px;height:52px;"><i class="bi bi-person-fill"></i></span>
                            <div>
                                <div class="fw-bold">Sarah K.</div>
                                <div class="small text-muted">Driving Instructor</div>
                                <div class="small"><span class="text-warning">★★★★★</span> <span class="fw-semibold">4.3</span> <span class="text-muted">· 387 reviews</span></div>
                            </div>
                        </div>
                        <div class="d-flex text-center mb-3">
                            <div class="flex-fill"><div class="fw-bold">From $85</div><div class="small text-muted">per hour</div></div>
                            <div class="flex-fill"><div class="fw-bold">1,200+</div><div class="small text-muted">lessons taught</div></div>
                            <div class="flex-fill"><div class="fw-bold">12 yrs</div><div class="small text-muted">experience</div></div>
                        </div>
                        <a href="#" onclick="return false;" class="btn btn-warning w-100 fw-semibold mb-2">Book a lesson →</a>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted"><i class="bi bi-geo-alt"></i> Serves your suburb + surrounds</span>
                            <span class="lg-badge new"><i class="bi bi-trophy-fill"></i> Ranked #1</span>
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
            <h2 class="fw-bolder mt-3">Three reasons you're losing leads before they even call.</h2>
            <p class="text-muted mx-auto" style="max-width:640px;">If you can't be found online, you don't exist. If learners can't book without talking to you, half walk. Here's what no website costs you, every single week.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['The invisible instructor','A parent Googles "[your suburb] driving instructor" at 9pm on a Sunday. You don\'t show up. The instructor with a website does. They get the booking. You never knew they existed.'],
                ['The phone-tag drop-off','A learner texts you while you\'re mid-lesson. You reply 4 hours later. They\'ve already booked someone else. Half your inbound leads die this way. Every week.'],
                ['The franchise lock-in','You ride the driving school\'s domain. They own your reviews, your SEO, your learners. The day you leave, your online presence goes to zero. You start from scratch.'],
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
            <h2 class="fw-bolder mt-3">Sixty seconds. From nothing to live.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lg-step-num">1</div>
                <h5 class="fw-bold">Complete your profile</h5>
                <p class="text-muted small">Add your bio, headshot, pricing, hours, and service area in one short flow. The moment you're done, your verified Secure Licence profile is live on a direct, shareable link.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">Your profile link</div>
                    <div class="small font-monospace text-truncate">securelicence.com.au/instructors/sarah-k-profile</div>
                    <div class="d-flex align-items-center gap-2 small text-success mt-1"><i class="bi bi-broadcast"></i> Live</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">2</div>
                <h5 class="fw-bold">Add your photo + pricing</h5>
                <p class="text-muted small">Upload one headshot. Confirm your rates. Your working hours, service area and reviews auto-import from your account. No design skills required.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">Profile</div>
                    <div class="row-line"><span class="text-muted">Photo</span><span class="fw-bold text-success">Uploaded</span></div>
                    <div class="row-line"><span class="text-muted">Hourly rate</span><span class="fw-bold">$85/hr</span></div>
                    <div class="row-line"><span class="text-muted">Service area</span><span class="fw-bold">Gold Coast + 35km</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">3</div>
                <h5 class="fw-bold">Share your profile link</h5>
                <p class="text-muted small">Copy your direct profile link and share it on Insta, WhatsApp, your business card. Learners click through to your bio, pricing, reviews, and a one-click booking flow.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">Share</div>
                    <div class="d-flex align-items-center gap-1 mb-2">
                        <span class="lg-prof-tag" style="background:#f1f3f5;">FB</span>
                        <span class="lg-prof-tag" style="background:#f1f3f5;">IG</span>
                        <span class="lg-prof-tag" style="background:#f1f3f5;">WA</span>
                        <span class="btn btn-warning btn-sm ms-auto">Copy link</span>
                    </div>
                    <div class="small font-monospace text-truncate p-2 rounded" style="background:#fff8e1;">securelicence.com.au/instructors/sarah-k-profile</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BENEFITS (yellow) ─────────── --}}
<section class="py-5 lg-yellow">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">What this actually gives you</h2>
            <p class="mb-0">Not features. Outcomes.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-graph-up-arrow','+$200/mo in organic leads','Structured data, suburb-targeted copy and Google Business integration mean you show up on page 1. Free traffic, forever.'],
                ['bi-shield-check','Zero coding required','No CMS to plug in. No update on Sunday night because someone pushed a security patch. It just works.'],
                ['bi-lightning-charge','Live the moment you\'re set up','Profile complete = profile live. No CMS to set up, no hosting, no plugins. Copy your link and start sending it to learners.'],
                ['bi-patch-check','Looks legit, parents trust you','Clean design. Your photo. Real reviews. Verified badge. Mum hands over the credit card without a second call. Conversion goes up.'],
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
            <h2 class="fw-bolder">Real instructors. Real wins.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Ben · 9 years · Brisbane North','"I had zero web presence. Set up the page on a Tuesday lunch break. By the weekend I had 6 bookings from people who Googled my suburb. Still feels like magic."'],
                ['Chloe · 8 years · Perth West','"I share my Secure Licence profile link on every Insta story and in my email signature. Learners book themselves in. I haven\'t taken a \'what does it cost\' phone call in months."'],
                ['Aaron · New instructor · Adelaide Hills','"Brand new, no brand, no clue where to start. The landing page made me look like I\'d been doing this for 10 years. Mums feel safe booking me. Huge unlock."'],
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

@include('frontend.pages.instructors._ecosystem', ['exclude' => 'website-booking-link'])
@include('frontend.pages.instructors._business-cta')

@endsection
