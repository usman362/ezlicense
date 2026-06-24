@extends('layouts.frontend')
@section('title', 'Driving Lesson Pricing & Packages — Secure Licence')
@section('content')

{{-- ─────────── HERO (full-width steering-wheel photo + overlay form card) ─────────── --}}
<section class="pp-hero">
    <div class="pp-hero-bg">
        <img src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=2000&q=80&auto=format&fit=crop"
             srcset="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=1200&q=80&auto=format&fit=crop 1200w,
                     https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=2000&q=80&auto=format&fit=crop 2000w"
             alt="Driver hands on steering wheel during a driving lesson">
        <div class="pp-hero-overlay"></div>
    </div>

    <div class="container pp-hero-inner">
        <h1 class="pp-hero-title">Driving Lesson Pricing &amp; Packages</h1>
        <div class="pp-hero-rating">
            <img src="https://www.google.com/favicon.ico" alt="Google" width="26" height="26">
            <span class="pp-hero-rating-text"><strong>Rated</strong> <span class="pp-hero-rating-num">4.9</span></span>
            <span class="pp-hero-rating-stars">
                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
            </span>
            <span class="pp-hero-rating-count">(14,000+)</span>
        </div>
    </div>

    <div class="container pp-hero-form-wrap">
        <form action="{{ route('find-instructor.results') }}" method="get" class="pp-hero-form">
            <input type="hidden" name="suburb_id" id="pp-hero-suburb-id">
            <input type="hidden" name="q" id="pp-hero-q">

            <div class="pp-hero-field">
                <label for="pp-hero-suburb" class="pp-hero-label">Pick-up Location <span class="pp-req">*</span></label>
                <div class="pp-hero-control">
                    <input type="text" id="pp-hero-suburb" class="form-control" placeholder="Enter your suburb" autocomplete="off" required>
                    <i class="bi bi-chevron-down pp-hero-chev"></i>
                </div>
            </div>

            <div class="pp-hero-field">
                <label for="pp-hero-transmission" class="pp-hero-label">Transmission <span class="pp-req">*</span></label>
                <div class="pp-hero-control">
                    <select id="pp-hero-transmission" name="transmission" class="form-select" required>
                        <option value="auto" selected>Auto</option>
                        <option value="manual">Manual</option>
                    </select>
                </div>
            </div>

            <div class="pp-hero-field">
                <label for="pp-hero-date" class="pp-hero-label">Test pre-booked?</label>
                <div class="pp-hero-control">
                    <input type="date" id="pp-hero-date" name="test_date" class="form-control" placeholder="Select date">
                </div>
            </div>

            <div class="pp-hero-field pp-hero-field-btn">
                <button type="submit" class="pp-hero-search-btn" disabled>
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</section>

{{-- ─────────── BUNDLE & SAVE ─────────── --}}
<section class="py-5 bg-light pp-bundle-section">
    <div class="container">
        <h2 class="cl-section-title text-center mb-4">Bundle your lessons and save!</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="pp-bundle-card">
                    <div class="pp-bundle-title">6+ hour bundle</div>
                    <div class="pp-bundle-discount">5% <span>off</span></div>
                    <p class="pp-bundle-desc">Ideal for new learners, overseas licence holders, or drivers needing a skills refresh.</p>
                    <a href="{{ route('find-instructor') }}" class="btn btn-warning fw-bold pp-bundle-btn">Get Started</a>
                </div>
            </div>
            <div class="col-md-6 col-lg-5">
                <div class="pp-bundle-card pp-bundle-card-featured">
                    <span class="pp-bundle-badge">BEST VALUE</span>
                    <div class="pp-bundle-title">10+ hour bundle</div>
                    <div class="pp-bundle-discount">10% <span>off</span></div>
                    <p class="pp-bundle-desc">Perfect for new learners starting their driving journey from scratch.</p>
                    <a href="{{ route('find-instructor') }}" class="btn btn-warning fw-bold pp-bundle-btn">Get Started</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── HOW MANY LESSONS DO I NEED ─────────── --}}
<section class="py-5">
    <div class="container">
        <h2 class="cl-section-title text-center mb-5">How many lessons do I need?</h2>
        <div class="row g-4 align-items-center">
            <div class="col-md-5 text-center">
                <svg class="pp-lessons-illu" viewBox="0 0 240 200" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    {{-- Yellow road background --}}
                    <ellipse cx="120" cy="180" rx="100" ry="10" fill="#fde68a"/>
                    {{-- Person body --}}
                    <circle cx="120" cy="60" r="22" fill="#fcd34d"/>
                    <path d="M95 95 Q120 80 145 95 L150 160 Q120 170 90 160 Z" fill="#1f2937"/>
                    {{-- Steering wheel --}}
                    <circle cx="120" cy="125" r="18" fill="none" stroke="#fbbf24" stroke-width="4"/>
                    <line x1="102" y1="125" x2="138" y2="125" stroke="#fbbf24" stroke-width="3"/>
                    <line x1="120" y1="107" x2="120" y2="143" stroke="#fbbf24" stroke-width="3"/>
                    {{-- Face --}}
                    <circle cx="113" cy="58" r="2" fill="#1f2937"/>
                    <circle cx="127" cy="58" r="2" fill="#1f2937"/>
                    <path d="M112 67 Q120 73 128 67" stroke="#1f2937" stroke-width="2" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="col-md-7">
                <ul class="pp-lessons-list">
                    <li>
                        <span class="pp-lessons-hours">10-15hrs</span>
                        <div>
                            <strong>New learners</strong>
                            <p>Beginners starting from scratch, journeying from learner to test-ready.</p>
                        </div>
                    </li>
                    <li>
                        <span class="pp-lessons-hours">5-8hrs</span>
                        <div>
                            <strong>Overseas licence</strong>
                            <p>Perfect for those looking to learn local driving rules.</p>
                        </div>
                    </li>
                    <li>
                        <span class="pp-lessons-hours">1-2hrs</span>
                        <div>
                            <strong>Refresher drivers</strong>
                            <p>Ideal for those needing a confidence boost or refresher.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── HOW MUCH ARE DRIVING LESSONS? ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center mb-4">
            <div class="col-lg-9 text-center">
                <h2 class="cl-section-title">How much are driving lessons?</h2>
                <p class="text-muted">At Secure Licence, you can compare prices from a range of verified, experienced driving instructors — and book the one that fits your budget and schedule. No rigid packages. Just flexible lesson bookings that work around you.</p>
            </div>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="pp-pricing-card">
                    <div class="pp-pricing-icon"><i class="bi bi-credit-card-2-front-fill"></i></div>
                    <h3>Lesson credit</h3>
                    <p class="text-muted small mb-0">Lesson credit can be used about every 1, 2 hour, and last day packages — no end-date! No second-use fee.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-5">
                <div class="pp-pricing-card">
                    <div class="pp-pricing-icon"><i class="bi bi-person-badge-fill"></i></div>
                    <h3>Prices vary by instructor</h3>
                    <p class="text-muted small mb-0">Prices vary featured, so you can be assured of what you spend.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── STATS STRIP ─────────── --}}
<section class="py-5">
    <div class="container">
        <h2 class="cl-section-title text-center mb-4">We are Australia's #1 booking platform for driving lessons</h2>
        <div class="row g-4 cl-stats-row">
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">300k+</div>
                <div class="cl-stat-label">Learners trusted us to get them ready</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">1.5m+</div>
                <div class="cl-stat-label">Book lessons 24/7 online in real time</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">7k+</div>
                <div class="cl-stat-label">Home and working with Children Check</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">24/7</div>
                <div class="cl-stat-label">Manage your lesson bookings online</div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── SEARCH ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="cl-section-title text-center mb-2">Search for an instructor to book driving lesson packages</h2>
        <p class="text-muted text-center mb-4">Book lessons in a flash, in just a few clicks!</p>
        <form action="{{ route('find-instructor.results') }}" method="get" class="ilc-search-form">
            <input type="hidden" name="suburb_id" id="pp-search-suburb-id">
            <input type="hidden" name="q" id="pp-search-q">
            <div class="dtp-form-row justify-content-center">
                <div class="btn-group dtp-trans-toggle" role="group">
                    <input type="radio" class="btn-check" name="transmission" id="pp-search-auto" value="auto" checked>
                    <label class="btn" for="pp-search-auto"><i class="bi bi-check-lg me-1"></i>Auto</label>
                    <input type="radio" class="btn-check" name="transmission" id="pp-search-manual" value="manual">
                    <label class="btn" for="pp-search-manual">Manual</label>
                </div>
                <input type="text" id="pp-search-suburb" class="form-control" placeholder="Enter your suburb" autocomplete="off">
                <button type="submit" class="btn btn-warning fw-bold"><i class="bi bi-search me-1"></i>Search</button>
            </div>
        </form>
    </div>
</section>

{{-- ─────────── HOW SECURE LICENCES WORKS ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="cl-section-title">How Secure Licence works</h2>
            <p class="text-muted">We connect you with the best driving instructors in Australia</p>
        </div>
        <div class="row g-4 cl-how-row">
            <div class="col-md-4 text-center">
                <div class="cl-how-icon"><i class="bi bi-person-check-fill"></i></div>
                <h3 class="cl-how-title">Find Your Driving Instructor</h3>
                <p class="text-muted small">Choose from a wide variety of instructors in your area. Check rating &amp; reviews from real learners.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="cl-how-icon cl-how-icon-accent"><i class="bi bi-calendar-check-fill"></i></div>
                <h3 class="cl-how-title">Book Online</h3>
                <p class="text-muted small">Book your driving lessons online; choose packages, time and reschedule whenever you need.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="cl-how-icon cl-how-icon-teal"><i class="bi bi-car-front-fill"></i></div>
                <h3 class="cl-how-title">Learn to Drive!</h3>
                <p class="text-muted small">Your instructor picks you up from your chosen address and you're on your way.</p>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── TESTIMONIALS ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="cl-section-title text-center mb-4">What more than 300,000 learners say</h2>
        <p class="text-muted text-center mb-4">Hear how learners describe their Secure Licence experience.</p>
        <div class="row g-4">
            @php
                $reviews = [
                    ['name' => 'Travis', 'text' => "A genuinely great experience from start to finish. My instructor was relaxed, encouraging, and I picked up more in a handful of lessons than I expected to all year."],
                    ['name' => 'Noah',   'text' => "Passed my driving test on the first attempt. Kind, patient and great at settling my nerves — I've already recommended them to a friend who's just starting out."],
                    ['name' => 'Rebecca','text' => "I learnt so much in my very first lesson. Everything was explained clearly and patiently, and the road rules finally started to make sense. Highly recommend."],
                    ['name' => 'Megan',  'text' => "Fantastic teacher. Really easy to learn with, quick to point out what I needed to work on, and supportive the whole way. I wouldn't hesitate to recommend them to anyone."],
                ];
            @endphp
            @foreach($reviews as $r)
                <div class="col-md-6 col-lg-3">
                    <div class="ilc-testimonial">
                        <div class="ilc-test-avatar"><i class="bi bi-person-fill"></i></div>
                        <div class="ilc-test-stars">@for($i = 0; $i < 5; $i++)<i class="bi bi-star-fill"></i>@endfor</div>
                        <p class="ilc-test-text">{{ $r['text'] }}</p>
                        <div class="ilc-test-name">{{ $r['name'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4 ilc-test-dots">
            <span class="active"></span><span></span><span></span><span></span><span></span>
        </div>
    </div>
</section>

{{-- ─────────── BOOK WITH CONFIDENCE ─────────── --}}
<section class="py-5">
    <div class="container">
        <h2 class="cl-section-title text-center mb-2">Book driving lessons with confidence</h2>
        <p class="text-muted text-center mb-5">Find the right instructor for you and start building real skills from day one — all through Australia's #1 driving lesson booking platform.</p>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="pp-conf-card">
                    <div class="pp-conf-icon"><i class="bi bi-star-fill"></i></div>
                    <h3>Instructor Ratings</h3>
                    <p>Browse real reviews of the instructor who has consistently provided a great learning experience.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="pp-conf-card">
                    <div class="pp-conf-icon"><i class="bi bi-patch-check-fill"></i></div>
                    <h3>Accredited</h3>
                    <p>Reviews and ratings curated of instructors that are accredited &amp; verify with working with children checks.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="pp-conf-card">
                    <div class="pp-conf-icon"><i class="bi bi-shield-fill-check"></i></div>
                    <h3>Vehicle Safety</h3>
                    <p>Dual-control cars include modern, well-maintained, model, mode &amp; safety rating.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="pp-conf-card">
                    <div class="pp-conf-icon"><i class="bi bi-arrow-repeat"></i></div>
                    <h3>Always Your Choice</h3>
                    <p>Don't like your current instructor? Select a new instructor on our online portal, no questions asked.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── GIFT VOUCHER ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-6 text-center">
                <div class="pp-gift-illu">
                    <div class="pp-gift-card-back"></div>
                    <div class="pp-gift-card-mid"></div>
                    <div class="pp-gift-card-front">
                        <div class="pp-gift-card-strip"></div>
                        <div class="pp-gift-card-label">GIFT</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h2 class="cl-section-title mb-3">The gift of life long skills</h2>
                <ul class="pp-gift-points">
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>Pick a voucher</strong>
                            <p>Choose the number of lessons that you want to purchase.</p>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>Send your gift</strong>
                            <p>For the recipient it'll be a great gift on the way.</p>
                        </div>
                    </li>
                </ul>
                <a href="{{ route('gift-vouchers') }}" class="btn btn-warning fw-bold mt-2"><i class="bi bi-gift-fill me-2"></i>Buy a gift voucher</a>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── READY FOR DRIVING LESSONS ─────────── --}}
<section class="py-5">
    <div class="container">
        <h2 class="cl-section-title text-center mb-2">Ready for driving lessons?</h2>
        <p class="text-muted text-center mb-5">Secure Licence connects learner drivers with the best driving schools.</p>
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="pp-ready-card">
                    <div class="pp-ready-icon"><i class="bi bi-mortarboard-fill"></i></div>
                    <h3>Learner drivers</h3>
                    <p>New learners, prepare for your driving test and complete your log book hours.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="pp-ready-card">
                    <div class="pp-ready-icon"><i class="bi bi-globe-americas"></i></div>
                    <h3>International conversions</h3>
                    <p>Convert your licence or simply build your confidence on Australian roads.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="pp-ready-card">
                    <div class="pp-ready-icon"><i class="bi bi-clipboard-check-fill"></i></div>
                    <h3>Driving tests</h3>
                    <p>Book a test package which includes pick up, a pre-test lesson, use of car &amp; drop off.</p>
                </div>
            </div>
        </div>
        <div class="text-center">
            <a href="{{ route('find-instructor') }}" class="btn btn-warning fw-bold btn-lg px-4">Book driving lessons now</a>
        </div>
    </div>
</section>

{{-- ─────────── FAQs ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <h2 class="cl-section-title mb-3">FAQs</h2>
                <p class="text-muted small mb-3">Here's a few of the questions we get on a regular basis. Can't find the answer you're looking for? Please check our <a href="#">full FAQ menu</a>.</p>
                <a href="#" class="btn btn-warning fw-bold"><i class="bi bi-arrow-right me-1"></i>Read More FAQs</a>
            </div>
            <div class="col-lg-8">
                <div class="accordion ilc-faq" id="pp-faq">
                    @php
                        $faqs = [
                            ['q' => 'How Much Are Driving Lessons Cost?',                                  'a' => 'Driving lesson prices in Australia typically start from $45/hour and go up to $85/hour depending on instructor experience, vehicle and your suburb. Multi-hour bundles save 5–10%.'],
                            ['q' => 'Do You Offer Any Special Lessons to Prepare for the Driving Test?',   'a' => 'Yes — most instructors offer a Driving Test Package: pick-up, a 45-minute warm-up lesson, use of the dual-controlled car for the test and drop-off.'],
                            ['q' => 'How Many Driving Lessons Do I Need?',                                 'a' => 'Beginners typically need 10–15 hours of professional lessons alongside supervised practice. Overseas licence holders may only need 5–8 hours.'],
                            ['q' => 'Can driving lessons count towards my logbook hours?',                 'a' => 'Yes — in NSW each professional driving lesson counts as 3 hours in your logbook (up to 10 lessons = 30 logbook hours).'],
                            ['q' => 'What if There Are No Available Driving Instructors in My Area?',     'a' => 'Reach out to us and we will do our best to find an instructor for your suburb — our network grows weekly.'],
                            ['q' => 'Can I Take Refresher Driving Lessons?',                              'a' => 'Absolutely — refresher lessons are perfect for drivers returning to the road or who feel rusty.'],
                            ['q' => 'Can I change instructor?',                                            'a' => 'Yes — you can switch instructors anytime through your dashboard at no extra cost.'],
                            ['q' => 'Is There a Driving Refund?',                                          'a' => 'Lesson credits never expire and unused credit can be transferred to a different instructor.'],
                            ['q' => 'Can I book Driving Lessons to Learn How to Drive Manual?',            'a' => 'Yes — filter by Manual transmission when searching instructors.'],
                            ['q' => "Where does Secure Licence offer driving lessons?",                    'a' => 'We cover Sydney, Melbourne, Brisbane, Perth, Adelaide, Hobart, Canberra and most major regional centres.'],
                        ];
                    @endphp
                    @foreach($faqs as $i => $f)
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#pp-faq-{{ $i }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">{{ $f['q'] }}</button>
                            </h3>
                            <div id="pp-faq-{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#pp-faq">
                                <div class="accordion-body text-muted small">{{ $f['a'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── FEATURED BLOGS ─────────── --}}
<section class="py-5">
    <div class="container">
        <h2 class="cl-section-title text-center mb-4">Featured Blogs</h2>
        <div class="row g-4">
            @php
                $blogs = [
                    ['title' => "Australia's Most Common Driving Theory Practice Test Mistakes, Revealed by State", 'tag' => 'Driving Theory', 'date' => '21 March 2026'],
                    ['title' => "Driving Test Tips: A guide to passing your driving test",                          'tag' => 'Driving Test',   'date' => '7 May 2026'],
                    ['title' => "11 Tips for Choosing a Good Driving Instructor",                                    'tag' => 'Instructor',     'date' => '14 April 2026'],
                ];
            @endphp
            @foreach($blogs as $b)
                <div class="col-md-4">
                    <article class="cl-guide-card">
                        <div class="cl-guide-thumb"><i class="bi bi-journal-text"></i></div>
                        <div class="cl-guide-body">
                            <span class="ilc-blog-tag">{{ $b['tag'] }}</span>
                            <h3>{{ $b['title'] }}</h3>
                            <p class="text-muted small mb-0">{{ $b['date'] }}</p>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="#" class="btn btn-outline-warning fw-bold"><i class="bi bi-arrow-right me-1"></i>Read more Blogs</a>
        </div>
    </div>
</section>

{{-- ─────────── WHY CHOOSE US ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="cl-section-title text-center mb-2">Why choose Secure Licence?</h2>
        <p class="text-muted text-center mb-5" style="max-width: 720px; margin-left: auto; margin-right: auto;">Unlike a typical driving school Secure Licence is an Australian-first platform that allows learner drivers &amp; parents to find, compare and book verified driving instructors online. The platform brings transparency, choice and flexibility to the selection of a driving instructor and the ongoing management of driving lessons.</p>

        <div class="row g-4 mb-5 text-center">
            <div class="col-md-4">
                <div class="ilc-stat-num" style="color: var(--sl-accent-600, #d97706);">1000+</div>
                <div class="ilc-stat-label">Driving Instructors</div>
            </div>
            <div class="col-md-4">
                <div class="ilc-stat-num" style="color: var(--sl-accent-600, #d97706);">3700+</div>
                <div class="ilc-stat-label">Suburbs Serviced</div>
            </div>
            <div class="col-md-4">
                <div class="ilc-stat-num" style="color: var(--sl-accent-600, #d97706);">#1</div>
                <div class="ilc-stat-label">Online Bookings</div>
            </div>
        </div>

        <div class="row g-3">
            @php
                $features = [
                    'Choose your own driving school instructor',
                    'Logbook check of driving instructors to validate',
                    'Book driving lessons online & offline 24/7',
                    'Driving lesson credits never expire',
                    'Manage your driving lesson bookings online',
                    'Change your driving lesson instructor any time',
                    'International driver licence conversions',
                    'Driving instructor satisfaction rating',
                    'Read reviews and unsolicited driving lesson reviews',
                    'Eligible for testing log book hours (NSW only)',
                    'Driving instructors vary cost ($45+)',
                    'Free booking helpline & dashboard',
                    'Save up to 10% on driving lessons',
                    'Review of the driving instructor in your area',
                    "Patient & friendly, head of female driving instructors",
                    'Auto & manual cars available',
                ];
            @endphp
            @foreach($features as $f)
                <div class="col-md-6 col-lg-3">
                    <div class="ilc-feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ $f }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('find-instructor') }}" class="btn btn-warning fw-bold btn-lg px-4">Book a driving lesson today</a>
        </div>
    </div>
</section>

{{-- ─────────── ADVANTAGE ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <h2 class="cl-section-title mb-3">The Secure Licence advantage</h2>
                <p class="text-muted">Enjoy a seamless, flexible, and convenient way to book and manage your driving lessons with Secure Licence.</p>
            </div>
            <div class="col-lg-7">
                <ul class="pp-advantage-list">
                    <li><i class="bi bi-check2-circle"></i>Book driving lessons online in under 90 seconds</li>
                    <li><i class="bi bi-check2-circle"></i>Manage and track your bookings</li>
                    <li><i class="bi bi-check2-circle"></i>Your online dashboard</li>
                    <li><i class="bi bi-check2-circle"></i>The widest range of driving instructors</li>
                    <li><i class="bi bi-check2-circle"></i>Servicing YOUR area</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BOTTOM CTA ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-3">
            <h2 class="cl-section-title">Learn to drive today!</h2>
            <p class="text-muted">Join over 300,000+ learners driving with Secure Licence.</p>
        </div>
        <form action="{{ route('find-instructor.results') }}" method="get" class="ilc-search-form">
            <input type="hidden" name="suburb_id" id="pp-bottom-suburb-id">
            <input type="hidden" name="q" id="pp-bottom-q">
            <div class="dtp-form-row justify-content-center">
                <div class="btn-group dtp-trans-toggle" role="group">
                    <input type="radio" class="btn-check" name="transmission" id="pp-bottom-auto" value="auto" checked>
                    <label class="btn" for="pp-bottom-auto"><i class="bi bi-check-lg me-1"></i>Auto</label>
                    <input type="radio" class="btn-check" name="transmission" id="pp-bottom-manual" value="manual">
                    <label class="btn" for="pp-bottom-manual">Manual</label>
                </div>
                <input type="text" id="pp-bottom-suburb" class="form-control" placeholder="Enter your suburb" autocomplete="off">
                <button type="submit" class="btn btn-warning fw-bold"><i class="bi bi-search me-1"></i>Search</button>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
(function(){
    function attachSuburbAutocomplete(inputId, hiddenIdSuburbId, hiddenIdQ) {
        var input = document.getElementById(inputId);
        if (!input) return;
        var list = document.createElement('div');
        list.className = 'dtp-suburb-list';
        list.hidden = true;
        if (getComputedStyle(input.parentNode).position === 'static') {
            input.parentNode.style.position = 'relative';
        }
        input.parentNode.appendChild(list);
        var debounce;
        input.addEventListener('input', function(){
            clearTimeout(debounce);
            var q = input.value.trim();
            if (q.length < 2) { list.hidden = true; return; }
            debounce = setTimeout(function(){
                fetch('/api/suburbs/search?q=' + encodeURIComponent(q), { credentials:'same-origin' })
                  .then(function(r){return r.json();})
                  .then(function(res){
                      var items = res.data || [];
                      if (!items.length) { list.hidden = true; return; }
                      list.innerHTML = items.slice(0,8).map(function(s){
                          return '<button type="button" data-id="'+s.id+'" data-name="'+s.name+'" data-postcode="'+s.postcode+'" data-state="'+(s.state||'')+'">'+s.name+', '+(s.state||'')+' '+s.postcode+'</button>';
                      }).join('');
                      list.hidden = false;
                  })
                  .catch(function(){ list.hidden = true; });
            }, 220);
        });
        list.addEventListener('mousedown', function(e){
            var btn = e.target.closest('button[data-id]');
            if (!btn) return;
            e.preventDefault();
            input.value = btn.dataset.name + ', ' + btn.dataset.state + ' ' + btn.dataset.postcode;
            document.getElementById(hiddenIdSuburbId).value = btn.dataset.id;
            document.getElementById(hiddenIdQ).value = btn.dataset.name;
            list.hidden = true;
        });
        input.addEventListener('blur', function(){ setTimeout(function(){ list.hidden = true; }, 200); });
    }
    attachSuburbAutocomplete('pp-hero-suburb',   'pp-hero-suburb-id',   'pp-hero-q');
    attachSuburbAutocomplete('pp-search-suburb', 'pp-search-suburb-id', 'pp-search-q');
    attachSuburbAutocomplete('pp-bottom-suburb', 'pp-bottom-suburb-id', 'pp-bottom-q');

    // Enable hero Search button once user has typed/picked a suburb
    (function(){
        var suburb = document.getElementById('pp-hero-suburb');
        var btn    = document.querySelector('.pp-hero-search-btn');
        if (!suburb || !btn) return;
        function sync(){ btn.disabled = suburb.value.trim().length < 2; }
        suburb.addEventListener('input', sync);
        suburb.addEventListener('change', sync);
        sync();
    })();
})();
</script>
@endpush

@endsection
