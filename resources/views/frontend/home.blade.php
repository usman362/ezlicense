@extends('layouts.frontend')

@section('title', 'Driving School | Driving Lessons | Book Learners Driving Test Online')

@section('content')
{{-- Hero: Discover Top Driving Instructors + Search --}}
<section class="ez-hero position-relative overflow-hidden" style="background: linear-gradient(rgba(27,33,44,0.75), rgba(27,33,44,0.8)), url('https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=1600&q=80') center/cover no-repeat; min-height: 420px;">
    <div class="container position-relative py-5">
        <div class="text-center mb-4 pt-3">
            <h1 class="display-4 fw-bold text-white mb-3">Discover Top Driving Instructors Near You</h1>
            <p class="d-flex align-items-center justify-content-center gap-2 flex-wrap mb-0">
                <img src="https://www.google.com/favicon.ico" alt="Google" width="20" height="20" style="border-radius:50%;">
                <span class="text-white fw-bold">Rated 4.9</span>
                <span class="text-warning"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></span>
                <span class="text-white-50">(10,000+)</span>
            </p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow border-0 rounded-3 overflow-hidden">
                    <div class="card-body p-4">
                        <form action="{{ route('find-instructor') }}" method="get" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Pick-up Location <span class="text-danger">*</span></label>
                                <input type="text" name="q" class="form-control form-control-lg" placeholder="Enter your suburb" value="{{ request('q') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Transmission <span class="text-danger">*</span></label>
                                <select name="transmission" class="form-select form-select-lg">
                                    <option value="auto" {{ request('transmission') === 'auto' ? 'selected' : '' }}>Auto</option>
                                    <option value="manual" {{ request('transmission') === 'manual' ? 'selected' : '' }}>Manual</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Test pre-booked?</label>
                                <input type="date" name="test_date" class="form-control form-control-lg" placeholder="Select date">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-lg w-100 fw-bold" style="background: var(--ez-accent); color: #333; height: 48px;"><i class="bi bi-search me-1"></i> Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- We are Australia's #1 --}}
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center fw-bold mb-5" style="color: var(--ez-dark);">We are Australia's #1 booking platform for driving lessons</h2>
        <div class="row g-4 text-center">
            <div class="col-6 col-md-4 col-lg-2">
                <span class="display-5 fw-bold text-warning d-block">100k+</span>
                <p class="small text-muted mb-0">Learners trusted us to get them road-ready</p>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <i class="bi bi-clock" style="font-size: 2.5rem; color: var(--ez-dark);"></i>
                <p class="small text-muted mt-2 mb-0">Book lessons 24/7 online in real time</p>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <i class="bi bi-shield-check" style="font-size: 2.5rem; color: var(--ez-dark);"></i>
                <p class="small text-muted mt-2 mb-0">Have a valid Working with Children Check</p>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <i class="bi bi-people" style="font-size: 2.5rem; color: var(--ez-dark);"></i>
                <p class="small text-muted mt-2 mb-0">Change your instructor anytime</p>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <i class="bi bi-calendar-check" style="font-size: 2.5rem; color: var(--ez-dark);"></i>
                <p class="small text-muted mt-2 mb-0">Manage your lesson bookings online</p>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <i class="bi bi-cart-check" style="font-size: 2.5rem; color: var(--ez-dark);"></i>
                <p class="small text-muted mt-2 mb-0">Purchase with peace of mind. Flexible rebooking.</p>
            </div>
        </div>
    </div>
</section>

{{-- How EzLicence works --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-2">How EzLicence works</h2>
        <p class="text-center text-muted mb-4">Simple, Trusted & Flexible Booking System</p>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="rounded-circle bg-warning text-dark d-inline-flex align-items-center justify-content-center mb-3 fw-bold" style="width: 56px; height: 56px; font-size: 1.25rem;">1</div>
                <h5 class="fw-bold">Browse Our Trusted Driving Instructors</h5>
                <p class="text-muted">Choose from a wide variety of instructors in your area. Check rating & reviews from real learners.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="rounded-circle bg-warning text-dark d-inline-flex align-items-center justify-content-center mb-3 fw-bold" style="width: 56px; height: 56px; font-size: 1.25rem;">2</div>
                <h5 class="fw-bold">Book Lessons In Under 5 Mins</h5>
                <p class="text-muted">Book online with instant confirmation. Easily manage your lesson schedule via our online dashboard.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="rounded-circle bg-warning text-dark d-inline-flex align-items-center justify-content-center mb-3 fw-bold" style="width: 56px; height: 56px; font-size: 1.25rem;">3</div>
                <h5 class="fw-bold">Get Your Licence</h5>
                <p class="text-muted">Your instructor picks you up from your chosen address and you're on your way 🚗</p>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-lg fw-bold">Start learning to drive now</a>
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center fw-bold mb-2">What more than 100,000 learners say</h2>
        <p class="text-center text-muted mb-4">Hear from learners about their EzLicence experience</p>
        <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @php
                    $testimonials = [
                        ['name' => 'Adriana', 'text' => 'Adriana is a really good instructor! She knows everything and she always make sure to correct every detail before the test. Apart from that, she is very sweet and calm. I was blessed to find her. Thanks Adriana!!!!', 'by' => 'Livia'],
                        ['name' => 'Tim', 'text' => 'Tim is a very calm and encouraging teacher. I really enjoy learning from him. I was really anxious about beginning lessons. Tim is very reassuring and kind.', 'by' => 'Mara'],
                        ['name' => 'Simon', 'text' => 'Simon is a great instructor! I took a 2-hour lesson on a day prior to my driving test. Got a few valuable tips and much-needed practice, which helped me to pass the test on the first go.', 'by' => 'Dmitry'],
                        ['name' => 'Shahida', 'text' => 'Shahida is an incredible driving instructor. My first driving experience with Shahida was absolutely great. Her calm, gentle nature and professionalism helped me overcome my anxiety with driving.', 'by' => 'Sepi'],
                        ['name' => 'Mick', 'text' => 'Mick is fantastic! He is very friendly and I was comfortable straight away. He helped me very much on achieving my Ps, I passed first go. I highly recommend Mick to everyone!', 'by' => 'Isabella'],
                    ];
                @endphp
                @foreach($testimonials as $i => $t)
                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4 p-lg-5">
                                    <p class="lead mb-3">"{{ $t['text'] }}"</p>
                                    <p class="fw-bold mb-0">{{ $t['name'] }}</p>
                                    <p class="small text-muted mb-0">{{ $t['by'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</section>

{{-- Driving test package --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-4">Driving test package</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i> Pick-up 1hr prior to test start time</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i> 45 min pre-test warm up</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i> Use of instructor's vehicle to sit the test</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i> Drop-off after the test result is received</li>
                </ul>
                <div class="text-center mt-4">
                    <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-lg fw-bold">Book Test Package Now</a>
                </div>
                <p class="small text-muted text-center mt-3 mb-0">Test package not available in ACT, SA and TAS.</p>
            </div>
        </div>
    </div>
</section>

{{-- Book with confidence --}}
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center fw-bold mb-2">Book driving lessons with confidence</h2>
        <p class="text-center text-muted mb-4">Choose a driving instructor you can trust</p>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3 text-center">
                <div class="rounded-circle bg-warning bg-opacity-25 d-inline-flex align-items-center justify-content-center mb-2" style="width: 64px; height: 64px;"><i class="bi bi-star-fill text-warning fs-4"></i></div>
                <h5 class="fw-bold">Instructor Ratings</h5>
                <p class="small text-muted">Access peer reviews & find an instructor who has consistently provided a great learning experience.</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center">
                <div class="rounded-circle bg-warning bg-opacity-25 d-inline-flex align-items-center justify-content-center mb-2" style="width: 64px; height: 64px;"><i class="bi bi-patch-check-fill text-warning fs-4"></i></div>
                <h5 class="fw-bold">Accredited</h5>
                <p class="small text-muted">We obtain up to date copies of relevant instructor accreditations & verify their working with children credentials.</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center">
                <div class="rounded-circle bg-warning bg-opacity-25 d-inline-flex align-items-center justify-content-center mb-2" style="width: 64px; height: 64px;"><i class="bi bi-car-front-fill text-warning fs-4"></i></div>
                <h5 class="fw-bold">Vehicle Safety</h5>
                <p class="small text-muted">Gain access to instructor vehicle make, model, year & safety rating.</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center">
                <div class="rounded-circle bg-warning bg-opacity-25 d-inline-flex align-items-center justify-content-center mb-2" style="width: 64px; height: 64px;"><i class="bi bi-arrow-left-right text-warning fs-4"></i></div>
                <h5 class="fw-bold">Always Your Choice</h5>
                <p class="small text-muted">Don't like your current instructor? Select a new instructor via our online portal, no questions asked.</p>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('find-instructor') }}" class="btn btn-warning fw-bold">Book driving lessons now</a>
        </div>
    </div>
</section>

{{-- FAQs --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-2">FAQs</h2>
        <p class="text-center text-muted mb-4">Here's a few of the questions we get on a regular basis. Can't find the answer? Please check our <a href="#">full FAQ page</a>.</p>
        <div class="accordion accordion-flush col-lg-8 mx-auto" id="faqAccordion">
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false" aria-controls="faq1">How Much Do Driving Lessons Cost?</button>
                </h3>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        Driving lesson prices on EzLicence are set by each instructor, so they can vary depending on where you're located, your chosen transmission (manual or auto), and the instructor you select. Enter your suburb in our search tool and compare lesson costs instantly. You'll see available instructors, their pricing, ratings, and car details — all in one spot. Bonus: Save when you book a lesson package.
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
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">Is EzLicence a Driving School?</button>
                </h3>
                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        EzLicence is an online platform that connects you with verified, independent driving instructors across Australia. Unlike a traditional driving school, you can find and compare instructors, view real-time availability, book online 24/7, and change your instructor anytime. Each instructor runs their own business — all in one place.
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
                        <p class="small text-muted mb-0">EzLicence · 7 November 2018</p>
                        <a href="#" class="btn btn-outline-warning btn-sm mt-2">Read more</a>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-center mt-3"><a href="#">Read more blogs</a></p>
    </div>
</section>

{{-- Why choose EzLicence --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-2">Why choose EzLicence?</h2>
        <p class="text-center text-muted mb-4">Unlike a typical driving school, EzLicence is an Australian first platform that allows learner drivers & parents to find, compare and book verified driving instructors online.</p>
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

{{-- The EzLicence advantage (accordion) --}}
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center fw-bold mb-4">The EzLicence advantage</h2>
        <p class="text-center text-muted mb-4">Enjoy a seamless, flexible, and convenient way to book and manage your driving lessons with EzLicence.</p>
        <div class="accordion col-lg-8 mx-auto" id="advantageAccordion">
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#adv1" aria-expanded="true" aria-controls="adv1">Book driving lessons online in under 60 seconds</button>
                </h3>
                <div id="adv1" class="accordion-collapse collapse show" data-bs-parent="#advantageAccordion">
                    <div class="accordion-body">
                        Booking driving lessons through EzLicence is a quick and hassle free process that gives you all the choice and control. Why deal with traditional Driving Schools over the phone or by email when you can manage your driving instructor choice & book driving lessons yourself anywhere, and at any time through our secure online platform?
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
                        EzLicence provides access to more than 1000+ fully qualified driving instructors across Sydney, Melbourne, Brisbane, Perth, Adelaide, Hobart and beyond. All driving instructors are required to have a current, valid clearance for working with children, and vehicles equipped with dual control pedals for added safety.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#adv5" aria-expanded="false" aria-controls="adv5">Servicing YOUR area</button>
                </h3>
                <div id="adv5" class="accordion-collapse collapse" data-bs-parent="#advantageAccordion">
                    <div class="accordion-body">
                        Thanks to our comprehensive driving instructor service area coverage, you can choose your pickup location from anywhere in Sydney, Melbourne, Brisbane, Adelaide, Perth, Hobart and surrounding areas. EzLicence proudly services over 3700+ suburbs across NSW, VIC, QLD, SA, TAS, WA and ACT.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
