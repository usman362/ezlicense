@extends('layouts.frontend')

@section('title', 'Driving School | Driving Lessons | Book Learners Driving Test Online')

@section('content')
{{-- Hero: Discover Top Driving Instructors + Search --}}
<section class="ez-hero position-relative overflow-hidden"
         style="background-image: linear-gradient(135deg, rgba(18,17,16,0.88) 0%, rgba(124,61,13,0.82) 100%), url('https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=1600&q=80'); background-size: cover; background-position: center; min-height: 560px;">
    {{-- Decorative glow --}}
    <div class="position-absolute" style="top:-120px; right:-120px; width:420px; height:420px; background: radial-gradient(circle, rgba(245,158,11,0.22) 0%, transparent 70%); pointer-events:none;"></div>
    <div class="position-absolute" style="bottom:-140px; left:-100px; width:380px; height:380px; background: radial-gradient(circle, rgba(255,132,0,0.25) 0%, transparent 70%); pointer-events:none;"></div>

    <div class="container position-relative" style="padding-top:5rem; padding-bottom:6rem;">
        <div class="text-center mb-5 animate-fade-in-up">
            <span class="badge px-3 py-2 mb-3" style="background: rgba(245,158,11,0.15); color: var(--sl-accent-400); border: 1px solid rgba(245,158,11,0.3); font-size: var(--sl-text-xs); letter-spacing: 0.08em; text-transform: uppercase;">
                <i class="bi bi-star-fill me-1"></i>Australia's #1 Platform
            </span>
            <h1 class="display-3 fw-bolder text-white mb-3" style="letter-spacing:-0.035em;">
                Find the Best <span style="color: var(--sl-accent-400);">Driving Instructors</span><br class="d-none d-lg-block">
                Near You
            </h1>
            <p class="text-white-50 mb-4" style="font-size:1.15rem; max-width:620px; margin:0 auto;">
                Compare verified instructors, read real reviews, and book online in under 60 seconds.
            </p>
            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                <img src="https://www.google.com/favicon.ico" alt="Google" width="20" height="20" style="border-radius:50%;">
                <span class="text-white fw-bold">4.9</span>
                <span style="color: var(--sl-accent-400);"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></span>
                <span class="text-white-50">based on <strong class="text-white">10,000+</strong> reviews</span>
            </div>
        </div>

        <div class="row justify-content-center animate-fade-in-up animate-delay-200">
            <div class="col-lg-11 col-xl-10">
                <div class="card border-0" style="overflow:visible; border-radius: var(--sl-radius-xl); box-shadow: var(--sl-shadow-2xl);">
                    <div class="card-body p-4 p-lg-5" style="overflow:visible;">
                        <form action="{{ route('find-instructor.results') }}" method="get" id="home-search-form" class="row g-3 align-items-end">
                            <input type="hidden" name="suburb_id" id="home-suburb-id" value="">
                            <input type="hidden" name="q" id="home-q" value="">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><i class="bi bi-geo-alt-fill me-1 text-primary"></i>Pick-up Location <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="text" class="form-control form-control-lg" id="home-suburb-input" placeholder="e.g. Parramatta, Sydney" autocomplete="off" data-list-id="home-suburb-list">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold"><i class="bi bi-gear-fill me-1 text-primary"></i>Transmission <span class="text-danger">*</span></label>
                                <select name="transmission" class="form-select form-select-lg">
                                    <option value="">Any</option>
                                    <option value="auto">Auto</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold"><i class="bi bi-calendar3 me-1 text-primary"></i>Test date (optional)</label>
                                <input type="date" name="test_date" class="form-control form-control-lg">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold" style="height: 52px;">
                                    <i class="bi bi-search me-1"></i>Search
                                </button>
                            </div>
                        </form>
                        <div class="d-flex flex-wrap gap-3 mt-4 pt-3 border-top" style="font-size: var(--sl-text-sm); color: var(--sl-gray-500);">
                            <span><i class="bi bi-shield-check-fill me-1 text-success"></i>Verified instructors</span>
                            <span><i class="bi bi-lightning-charge-fill me-1 text-primary"></i>Instant booking</span>
                            <span><i class="bi bi-cash-coin me-1 text-success"></i>Pay online</span>
                            <span><i class="bi bi-star-fill me-1 text-warning"></i>Real reviews</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Why choose Secure Licences --}}
<section class="section bg-white position-relative">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary-dark px-3 py-2 mb-2" style="font-size: var(--sl-text-xs); letter-spacing:0.08em; text-transform:uppercase;">Why Choose Us</span>
            <h2 class="display-5 fw-bolder mb-2">Built for learners. Trusted by thousands.</h2>
            <p class="text-muted mb-0" style="font-size: 1.1rem;">Everything you need to pass your test with confidence.</p>
        </div>
        <div class="row g-4">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card card-hover h-100 border-0 text-center p-3">
                    <div class="icon-bubble mx-auto mb-3"><i class="bi bi-people-fill"></i></div>
                    <div class="display-6 fw-bolder gradient-text mb-1">100k+</div>
                    <p class="small text-muted mb-0">Learners passed with us</p>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card card-hover h-100 border-0 text-center p-3">
                    <div class="icon-bubble icon-bubble-accent mx-auto mb-3"><i class="bi bi-clock-fill"></i></div>
                    <h6 class="fw-bold mb-1">24/7 Booking</h6>
                    <p class="small text-muted mb-0">Real-time availability</p>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card card-hover h-100 border-0 text-center p-3">
                    <div class="icon-bubble icon-bubble-success mx-auto mb-3"><i class="bi bi-shield-check"></i></div>
                    <h6 class="fw-bold mb-1">Verified WWCC</h6>
                    <p class="small text-muted mb-0">All instructors checked</p>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card card-hover h-100 border-0 text-center p-3">
                    <div class="icon-bubble icon-bubble-teal mx-auto mb-3"><i class="bi bi-arrow-left-right"></i></div>
                    <h6 class="fw-bold mb-1">Switch Anytime</h6>
                    <p class="small text-muted mb-0">Change instructor freely</p>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card card-hover h-100 border-0 text-center p-3">
                    <div class="icon-bubble mx-auto mb-3"><i class="bi bi-calendar-check-fill"></i></div>
                    <h6 class="fw-bold mb-1">Easy Scheduling</h6>
                    <p class="small text-muted mb-0">Manage bookings online</p>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card card-hover h-100 border-0 text-center p-3">
                    <div class="icon-bubble icon-bubble-accent mx-auto mb-3"><i class="bi bi-shield-lock-fill"></i></div>
                    <h6 class="fw-bold mb-1">Buy with Confidence</h6>
                    <p class="small text-muted mb-0">Flexible rebooking</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- How Secure Licences works --}}
<section class="section position-relative" style="background: linear-gradient(180deg, var(--sl-gray-50) 0%, #fff 100%);">
    <div class="bg-dots position-absolute" style="top:0; left:0; right:0; height:100%; opacity:0.4; pointer-events:none;"></div>
    <div class="container position-relative">
        <div class="text-center mb-5">
            <span class="badge bg-accent-subtle text-accent px-3 py-2 mb-2" style="font-size: var(--sl-text-xs); letter-spacing:0.08em; text-transform:uppercase;">Simple · Trusted · Flexible</span>
            <h2 class="display-5 fw-bolder mb-2">How Secure Licences works</h2>
            <p class="text-muted" style="font-size: 1.1rem;">From search to licence in three easy steps.</p>
        </div>
        <div class="row g-4 position-relative">
            {{-- Connecting line on desktop --}}
            <div class="d-none d-md-block position-absolute" style="top:38px; left:16%; right:16%; height:2px; border-top:2px dashed var(--sl-gray-300); z-index:0;"></div>

            <div class="col-md-4 text-center position-relative" style="z-index:1;">
                <div class="d-inline-flex align-items-center justify-content-center mb-4 fw-bold"
                     style="width:76px;height:76px;background:#fff;color:var(--sl-primary-600);border:3px solid var(--sl-primary-600);border-radius:50%;font-size:1.75rem;box-shadow:var(--sl-shadow-lg);">1</div>
                <h5 class="fw-bold mb-2">Browse Verified Instructors</h5>
                <p class="text-muted mb-0">Compare ratings, reviews, and vehicles. Filter by transmission and location.</p>
            </div>
            <div class="col-md-4 text-center position-relative" style="z-index:1;">
                <div class="d-inline-flex align-items-center justify-content-center mb-4 fw-bold"
                     style="width:76px;height:76px;background:#fff;color:var(--sl-accent-600);border:3px solid var(--sl-accent-500);border-radius:50%;font-size:1.75rem;box-shadow:var(--sl-shadow-lg);">2</div>
                <h5 class="fw-bold mb-2">Book in Under 5 Minutes</h5>
                <p class="text-muted mb-0">Instant confirmation. Manage your schedule from your learner dashboard.</p>
            </div>
            <div class="col-md-4 text-center position-relative" style="z-index:1;">
                <div class="d-inline-flex align-items-center justify-content-center mb-4 fw-bold"
                     style="width:76px;height:76px;background:#fff;color:var(--sl-teal-600);border:3px solid var(--sl-teal-500);border-radius:50%;font-size:1.75rem;box-shadow:var(--sl-shadow-lg);">3</div>
                <h5 class="fw-bold mb-2">Get Your Licence</h5>
                <p class="text-muted mb-0">Your instructor picks you up from your chosen address and you're on your way.</p>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('find-instructor') }}" class="btn btn-primary btn-lg fw-bold px-5">
                Start learning today<i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="section" style="background: var(--sl-gray-50);">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary-dark px-3 py-2 mb-2" style="font-size: var(--sl-text-xs); letter-spacing:0.08em; text-transform:uppercase;">Testimonials</span>
            <h2 class="display-5 fw-bolder mb-2">Loved by 100,000+ learners</h2>
            <p class="text-muted" style="font-size: 1.1rem;">Real stories from learners who passed with Secure Licences.</p>
        </div>
        <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @php
                    $testimonials = [
                        ['instructor' => 'Adriana', 'text' => 'A really great instructor — she makes sure every detail is corrected before the test. Sweet, calm, and so patient. I felt totally ready on test day.', 'by' => 'Livia', 'rating' => 5],
                        ['instructor' => 'Tim',     'text' => 'A very calm and encouraging teacher. I was really anxious about starting lessons, but his reassuring manner made everything so much easier.', 'by' => 'Mara', 'rating' => 5],
                        ['instructor' => 'Simon',   'text' => 'Took a 2-hour lesson the day before my test. Got a few valuable tips and much-needed practice which helped me pass on my first go.', 'by' => 'Dmitry', 'rating' => 5],
                        ['instructor' => 'Shahida', 'text' => 'An incredible driving instructor. Her calm, gentle nature and professionalism helped me overcome my driving anxiety completely.', 'by' => 'Sepi', 'rating' => 5],
                        ['instructor' => 'Mick',    'text' => 'Fantastic! Very friendly and I was comfortable straight away. He helped me achieve my Ps on the first attempt. Highly recommend!', 'by' => 'Isabella', 'rating' => 5],
                    ];
                @endphp
                @foreach($testimonials as $i => $t)
                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card border-0" style="box-shadow: var(--sl-shadow-xl); border-radius: var(--sl-radius-xl);">
                                <div class="card-body p-4 p-lg-5">
                                    <div class="mb-3" style="color: var(--sl-accent-500); font-size: 2.5rem; line-height: 1;">
                                        <i class="bi bi-quote"></i>
                                    </div>
                                    <div class="mb-3">
                                        @for($s = 1; $s <= 5; $s++)
                                            <i class="bi bi-star-fill" style="color: var(--sl-accent-500); font-size: 1.1rem;"></i>
                                        @endfor
                                    </div>
                                    <p class="mb-4" style="font-size: 1.15rem; line-height: 1.6; color: var(--sl-gray-700);">{{ $t['text'] }}</p>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bolder"
                                             style="width:48px; height:48px; background: linear-gradient(135deg, var(--sl-primary-500), var(--sl-teal-500)); color:#fff; font-size:1.15rem;">
                                            {{ substr($t['by'], 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $t['by'] }}</div>
                                            <div class="small text-muted">Taught by <strong>{{ $t['instructor'] }}</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <button class="btn btn-outline-secondary btn-sm me-2" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- Driving test package CTA --}}
<section class="section" style="background: linear-gradient(135deg, var(--sl-primary-900) 0%, var(--sl-gray-900) 100%); color: #fff; position: relative; overflow: hidden;">
    <div class="position-absolute" style="top:-100px; right:-100px; width:320px; height:320px; background: radial-gradient(circle, rgba(245,158,11,0.2) 0%, transparent 70%); pointer-events:none;"></div>
    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark px-3 py-2 mb-3" style="font-size: var(--sl-text-xs); letter-spacing:0.08em; text-transform:uppercase;">Test Package</span>
                <h2 class="display-5 fw-bolder text-white mb-3">Everything you need on test day.</h2>
                <p class="mb-4" style="color: rgba(255,255,255,0.75); font-size: 1.1rem;">Walk in relaxed, walk out licensed. One complete package.</p>
                <ul class="list-unstyled">
                    <li class="mb-2 d-flex align-items-start gap-2"><i class="bi bi-check-circle-fill" style="color:var(--sl-accent-500); font-size:1.25rem; margin-top:0.15rem;"></i> <span>Pick-up one hour before your test</span></li>
                    <li class="mb-2 d-flex align-items-start gap-2"><i class="bi bi-check-circle-fill" style="color:var(--sl-accent-500); font-size:1.25rem; margin-top:0.15rem;"></i> <span>45-minute pre-test warm up on test route</span></li>
                    <li class="mb-2 d-flex align-items-start gap-2"><i class="bi bi-check-circle-fill" style="color:var(--sl-accent-500); font-size:1.25rem; margin-top:0.15rem;"></i> <span>Use of your instructor's vehicle for the test</span></li>
                    <li class="mb-2 d-flex align-items-start gap-2"><i class="bi bi-check-circle-fill" style="color:var(--sl-accent-500); font-size:1.25rem; margin-top:0.15rem;"></i> <span>Drop-off once your result is in</span></li>
                </ul>
                <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-lg fw-bold mt-3 px-5">
                    Book your test package<i class="bi bi-arrow-right ms-2"></i>
                </a>
                <p class="small mt-3 mb-0" style="color: rgba(255,255,255,0.5);">Not available in ACT, SA and TAS.</p>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-center">
                <div class="position-relative d-inline-block">
                    <div class="position-absolute" style="top:50%;left:50%;transform:translate(-50%,-50%);width:280px;height:280px;background:radial-gradient(circle,rgba(245,158,11,0.3) 0%,transparent 70%);border-radius:50%;"></div>
                    <i class="bi bi-trophy-fill position-relative" style="font-size:14rem; color: var(--sl-accent-500); filter: drop-shadow(0 20px 40px rgba(245,158,11,0.4));"></i>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Book with confidence --}}
<section class="section bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-success-subtle px-3 py-2 mb-2" style="color: #065f46; font-size: var(--sl-text-xs); letter-spacing:0.08em; text-transform:uppercase;">Safety First</span>
            <h2 class="display-5 fw-bolder mb-2">Book with total confidence</h2>
            <p class="text-muted" style="font-size: 1.1rem;">Every instructor is vetted so you don't have to worry.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card card-hover h-100 border-0 p-4 text-center">
                    <div class="icon-bubble icon-bubble-lg icon-bubble-accent mx-auto mb-3"><i class="bi bi-star-fill"></i></div>
                    <h5 class="fw-bold mb-2">Real Reviews</h5>
                    <p class="small text-muted mb-0">Honest feedback from learners who actually passed their test.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card card-hover h-100 border-0 p-4 text-center">
                    <div class="icon-bubble icon-bubble-lg icon-bubble-success mx-auto mb-3"><i class="bi bi-patch-check-fill"></i></div>
                    <h5 class="fw-bold mb-2">Fully Accredited</h5>
                    <p class="small text-muted mb-0">Up-to-date driving instructor licences and WWCC verified.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card card-hover h-100 border-0 p-4 text-center">
                    <div class="icon-bubble icon-bubble-lg mx-auto mb-3"><i class="bi bi-car-front-fill"></i></div>
                    <h5 class="fw-bold mb-2">Safe Vehicles</h5>
                    <p class="small text-muted mb-0">See the make, model, year and safety rating before you book.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card card-hover h-100 border-0 p-4 text-center">
                    <div class="icon-bubble icon-bubble-lg icon-bubble-teal mx-auto mb-3"><i class="bi bi-arrow-left-right"></i></div>
                    <h5 class="fw-bold mb-2">Your Choice</h5>
                    <p class="small text-muted mb-0">Switch instructors any time from your dashboard — no hassle.</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('find-instructor') }}" class="btn btn-primary btn-lg fw-bold px-5">
                <i class="bi bi-search me-2"></i>Find my instructor
            </a>
        </div>
    </div>
</section>

{{-- FAQs --}}
<section class="section bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary-dark px-3 py-2 mb-2" style="font-size: var(--sl-text-xs); letter-spacing:0.08em; text-transform:uppercase;">FAQs</span>
            <h2 class="display-5 fw-bolder mb-2">Got questions?</h2>
            <p class="text-muted" style="font-size: 1.1rem;">The answers to what learners ask most often. Still stuck? <a href="{{ route('contact') }}" class="text-primary fw-semibold">Contact us</a>.</p>
        </div>
        <div class="accordion accordion-flush col-lg-8 mx-auto" id="faqAccordion">
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false" aria-controls="faq1">How Much Do Driving Lessons Cost?</button>
                </h3>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        Driving lesson prices on Secure Licences are set by each instructor, so they can vary depending on where you're located, your chosen transmission (manual or auto), and the instructor you select. Enter your suburb in our search tool and compare lesson costs instantly. You'll see available instructors, their pricing, ratings, and car details — all in one spot. Bonus: Save when you book a lesson package.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">Do You Offer Any Special Lessons to Prepare for the Driving Test?</button>
                </h3>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        Test Package prices are set by each instructor. Every Test Package includes: Pick-up from your chosen location, a 45-minute pre-test driving lesson, use of your instructor's car for the test, and drop-off afterwards. Test Packages are available in most states, but not currently offered in ACT, SA and TAS.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">How Many Driving Lessons Do I Need?</button>
                </h3>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        After your first lesson, your driving instructor will assess how many lessons you should take. We recommend at least 7 to 10 hours for new drivers with no experience; 5 to 7 hours if you've had some practice with family; 3 to 5 hours for international licence conversions or manual learners. These are guides only and vary by learner.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">Can I Change Instructors?</button>
                </h3>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        Absolutely. From your dashboard select 'find another instructor', choose the instructor you'd like, check their availability and book online. It's that simple.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">Is Secure Licences a Driving School?</button>
                </h3>
                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        Secure Licences is an online platform that connects you with verified, independent driving instructors across Australia. Unlike a traditional driving school, you can find and compare instructors, view real-time availability, book online 24/7, and change your instructor anytime. Each instructor runs their own business — all in one place.
                    </div>
                </div>
            </div>
        </div>
        <p class="text-center mt-3"><a href="#">Read More FAQs</a></p>
    </div>
</section>

{{-- Featured blog --}}
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center fw-bold mb-4">Featured Blogs</h2>
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h3 class="h5 card-title fw-bold">11 Tips for Choosing a Good Driving Instructor</h3>
                        <p class="small text-muted mb-0">Secure Licences · 7 November 2018</p>
                        <a href="#" class="btn btn-outline-warning btn-sm mt-2">Read more</a>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-center mt-3"><a href="#">Read more blogs</a></p>
    </div>
</section>

{{-- Why choose Secure Licences --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-2">Why choose Secure Licences?</h2>
        <p class="text-center text-muted mb-4">Unlike a typical driving school, Secure Licences is an Australian first platform that allows learner drivers & parents to find, compare and book verified driving instructors online.</p>
        <div class="row g-4 mb-4">
            <div class="col-md-4 text-center">
                <span class="display-5 fw-bold text-warning d-block">1000+</span>
                <p class="mb-0">Driving Instructors</p>
            </div>
            <div class="col-md-4 text-center">
                <span class="display-5 fw-bold text-warning d-block">3700+</span>
                <p class="mb-0">Suburbs Serviced</p>
            </div>
            <div class="col-md-4 text-center">
                <span class="display-5 fw-bold text-warning d-block">#1</span>
                <p class="mb-0">Online Bookings</p>
            </div>
        </div>
        <div class="row g-2">
            <div class="col-6 col-md-4 col-lg-3"><i class="bi bi-check2 text-warning me-1"></i> Choose your own private driving instructors</div>
            <div class="col-6 col-md-4 col-lg-3"><i class="bi bi-check2 text-warning me-1"></i> Manage your lesson bookings online</div>
            <div class="col-6 col-md-4 col-lg-3"><i class="bi bi-check2 text-warning me-1"></i> Licenced and accredited driving instructors</div>
            <div class="col-6 col-md-4 col-lg-3"><i class="bi bi-check2 text-warning me-1"></i> Largest choice of driving instructors in Australia</div>
            <div class="col-6 col-md-4 col-lg-3"><i class="bi bi-check2 text-warning me-1"></i> Change your driving instructor online</div>
            <div class="col-6 col-md-4 col-lg-3"><i class="bi bi-check2 text-warning me-1"></i> Book driving lessons online in real-time</div>
            <div class="col-6 col-md-4 col-lg-3"><i class="bi bi-check2 text-warning me-1"></i> Driving instructor cars dual controlled</div>
            <div class="col-6 col-md-4 col-lg-3"><i class="bi bi-check2 text-warning me-1"></i> Auto & manual cars available</div>
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-lg fw-bold">Book a driving school today</a>
        </div>
    </div>
</section>

{{-- The Secure Licences advantage (accordion) --}}
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center fw-bold mb-4">The Secure Licences advantage</h2>
        <p class="text-center text-muted mb-4">Enjoy a seamless, flexible, and convenient way to book and manage your driving lessons with Secure Licences.</p>
        <div class="accordion col-lg-8 mx-auto" id="advantageAccordion">
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#adv1" aria-expanded="true" aria-controls="adv1">Book driving lessons online in under 60 seconds</button>
                </h3>
                <div id="adv1" class="accordion-collapse collapse show" data-bs-parent="#advantageAccordion">
                    <div class="accordion-body">
                        Booking driving lessons through Secure Licences is a quick and hassle free process that gives you all the choice and control. Why deal with traditional Driving Schools over the phone or by email when you can manage your driving instructor choice & book driving lessons yourself anywhere, and at any time through our secure online platform?
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#adv2" aria-expanded="false" aria-controls="adv2">More control over your bookings</button>
                </h3>
                <div id="adv2" class="accordion-collapse collapse" data-bs-parent="#advantageAccordion">
                    <div class="accordion-body">
                        From the moment you enter your pickup suburb you have more control over your driving lesson compared to traditional driving schools. Choose, compare, and book your driving instructor and preferred vehicle transmission based on in-depth driving instructor profiles, including ratings and reviews from learners just like you. Bookings are made in real-time.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#adv3" aria-expanded="false" aria-controls="adv3">Your online dashboard</button>
                </h3>
                <div id="adv3" class="accordion-collapse collapse" data-bs-parent="#advantageAccordion">
                    <div class="accordion-body">
                        Manage your preferences, existing bookings & future driving lesson bookings from your secure online account. Reschedule bookings up to 24 hrs prior to the lesson start time. Want to try a different driving instructor? You can change your driving instructor at the push of a button, no questions asked.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#adv4" aria-expanded="false" aria-controls="adv4">The widest range of driving instructors</button>
                </h3>
                <div id="adv4" class="accordion-collapse collapse" data-bs-parent="#advantageAccordion">
                    <div class="accordion-body">
                        Secure Licences provides access to more than 1000+ fully qualified driving instructors across Sydney, Melbourne, Brisbane, Perth, Adelaide, Hobart and beyond. All driving instructors are required to have a current, valid clearance for working with children, and vehicles equipped with dual control pedals for added safety.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#adv5" aria-expanded="false" aria-controls="adv5">Servicing YOUR area</button>
                </h3>
                <div id="adv5" class="accordion-collapse collapse" data-bs-parent="#advantageAccordion">
                    <div class="accordion-body">
                        Thanks to our comprehensive driving instructor service area coverage, you can choose your pickup location from anywhere in Sydney, Melbourne, Brisbane, Adelaide, Perth, Hobart and surrounding areas. Secure Licences proudly services over 3700+ suburbs across NSW, VIC, QLD, SA, TAS, WA and ACT.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
    @vite('resources/js/home-search.js')
@endpush
@endsection
