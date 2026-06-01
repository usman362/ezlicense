@extends('layouts.frontend')
@section('title', 'Driving Lessons in ' . $city['name'] . ', ' . $city['state'])

@section('content')
@php
    $cityName  = $city['name'];
    $cityState = $city['state'];
    $cityFull  = $cityName . ', ' . $cityState;
@endphp

{{-- ─────────── HERO: breadcrumb + card on left, hero image on right ─────────── --}}
<section class="cl-hero">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6 position-relative">
                <div class="cl-hero-card">
                    {{-- Breadcrumb --}}
                    <nav aria-label="breadcrumb" class="cl-hero-breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('find-instructor') }}">Driving School, Driving Lessons &amp; Instructors</a></li>
                            <li class="breadcrumb-item">{{ $city['state_full'] }}</li>
                            <li class="breadcrumb-item active">{{ $cityName }}</li>
                        </ol>
                    </nav>

                    <h1 class="cl-hero-title">
                        Driving lessons in<br>
                        <span class="cl-hero-title-bold">{{ $cityName }}, {{ $cityState }}</span>
                    </h1>
                    <p class="cl-hero-sub">Find and book certified driving instructors on Secure Licence</p>

                    <form action="{{ route('find-instructor.results') }}" method="get" class="cl-hero-form">
                        <input type="hidden" name="suburb_id" id="cl-hero-suburb-id">
                        <input type="hidden" name="q" id="cl-hero-q" value="{{ $cityName }}">

                        <div class="cl-hero-form-row">
                            <div class="btn-group dtp-trans-toggle cl-hero-toggle" role="group">
                                <input type="radio" class="btn-check" name="transmission" id="cl-auto" value="auto" checked>
                                <label class="btn" for="cl-auto"><i class="bi bi-check-lg text-success me-1"></i>Auto</label>
                                <input type="radio" class="btn-check" name="transmission" id="cl-manual" value="manual">
                                <label class="btn" for="cl-manual">Manual</label>
                            </div>
                            <div class="cl-hero-suburb-wrap">
                                <input type="text" id="cl-hero-suburb" class="form-control" placeholder="Enter your suburb" autocomplete="off">
                                <i class="bi bi-chevron-down cl-hero-suburb-chev"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning fw-bold w-100 cl-hero-search-btn mt-3">
                            <i class="bi bi-search me-2"></i>Search
                        </button>
                    </form>
                </div>

                {{-- Below the card: Google rating row --}}
                <div class="cl-hero-trust mt-4 px-3">
                    <img src="https://www.google.com/favicon.ico" alt="Google" width="22" height="22">
                    <span class="ms-2"><strong>Rated 4.9 from 14,000+ Google reviews</strong></span>
                    <span class="cl-hero-trust-stars ms-2">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </span>
                </div>

                {{-- Curved decorative arrow (desktop only) --}}
                <svg class="cl-hero-arrow d-none d-lg-block" viewBox="0 0 120 200" aria-hidden="true">
                    <path d="M10 10 Q 100 60 60 180" fill="none" stroke="#fbbf24" stroke-width="4" stroke-linecap="round" stroke-dasharray="0 0"/>
                    <path d="M50 165 L60 180 L70 165" fill="none" stroke="#fbbf24" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <div class="col-lg-6 d-none d-lg-block">
                <div class="cl-hero-image">
                    <img src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=1000&q=80&auto=format&fit=crop"
                         srcset="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&q=80&auto=format&fit=crop 600w,
                                 https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=1000&q=80&auto=format&fit=crop 1000w"
                         alt="Learner driver sitting in a car during a driving lesson in {{ $cityName }}"
                         loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── Stats strip: "We are Australia's #1 booking platform" ─────────── --}}
<section class="py-5">
    <div class="container">
        <h2 class="cl-section-title text-center mb-4">We are Australia's #1 booking platform for driving lessons</h2>
        <div class="row g-4 cl-stats-row">
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">{{ number_format($city['learners']) }}</div>
                <div class="cl-stat-label">{{ $cityName }} learners have trusted us to get them ready</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">{{ number_format($city['lessons']) }}</div>
                <div class="cl-stat-label">Lessons delivered in {{ $cityName }}</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">${{ number_format($city['price'], 2) }}</div>
                <div class="cl-stat-label">The average price per second hour in {{ $cityName }}</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num"><i class="bi bi-clock-fill"></i> 24/7</div>
                <div class="cl-stat-label">Change your instructor anytime &amp; manage your lesson bookings online</div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── Search driving instructors in {city} ─────────── --}}
<section class="py-4 bg-light">
    <div class="container">
        <h2 class="cl-section-title text-center mb-4">Search driving instructors in {{ $cityName }}</h2>
        <p class="text-muted text-center mb-4">Discover local driving instructors, current fees, friendly faces, search 1k and 2k suburbs</p>
        <form action="{{ route('find-instructor.results') }}" method="get" class="ilc-search-form">
            <input type="hidden" name="suburb_id" id="cl-search-suburb-id">
            <input type="hidden" name="q" id="cl-search-q" value="{{ $cityName }}">
            <div class="dtp-form-row justify-content-center">
                <div class="btn-group dtp-trans-toggle" role="group">
                    <input type="radio" class="btn-check" name="transmission" id="cl-search-auto" value="auto" checked>
                    <label class="btn" for="cl-search-auto"><i class="bi bi-check-lg me-1"></i>Auto</label>
                    <input type="radio" class="btn-check" name="transmission" id="cl-search-manual" value="manual">
                    <label class="btn" for="cl-search-manual">Manual</label>
                </div>
                <input type="text" id="cl-search-suburb" class="form-control" placeholder="Enter your suburb" autocomplete="off">
                <button type="submit" class="btn btn-warning fw-bold"><i class="bi bi-search me-1"></i>Search</button>
            </div>
        </form>
    </div>
</section>

{{-- ─────────── Top instructors in {city} ─────────── --}}
<section class="py-5">
    <div class="container">
        <h2 class="cl-section-title text-center mb-2">Driving lessons in {{ $cityName }}</h2>
        <p class="text-muted text-center mb-4">Secure Licence has connected {{ number_format($city['learners']) }} learners to the best instructors in {{ $cityName }}</p>

        {{-- Filter chips --}}
        <ul class="nav nav-pills justify-content-center cl-chips mb-4" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#cl-tab-featured" type="button">FEATURED</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#cl-tab-new" type="button">NEW</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#cl-tab-lowest" type="button">LOWEST PRICE</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#cl-tab-auto" type="button">AUTO/MANUAL</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#cl-tab-female" type="button">FEMALE</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#cl-tab-fastest" type="button">FASTEST</button></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="cl-tab-featured">
                <div class="row g-3">
                    @forelse($topInstructors as $p)
                        <div class="col-md-6 col-lg-3">
                            <div class="cl-inst-card">
                                <div class="cl-inst-photo">
                                    @if($p->profile_photo)
                                        <img src="{{ \Storage::disk('spaces')->url($p->profile_photo) }}" alt="{{ $p->user->name }}">
                                    @else
                                        <div class="cl-inst-initials">{{ strtoupper(substr($p->user->first_name ?? $p->user->name, 0, 1)) }}</div>
                                    @endif
                                </div>
                                <div class="cl-inst-body">
                                    <div class="fw-bold">{{ $p->user->first_name ?? explode(' ', $p->user->name)[0] }}</div>
                                    <div class="cl-inst-rating">
                                        <i class="bi bi-star-fill"></i>
                                        <strong>{{ number_format($p->averageRating() > 0 ? $p->averageRating() : 4.9, 1) }}</strong>
                                        <span class="text-muted">· {{ $p->reviewsCount() }} {{ Str::plural('rating', $p->reviewsCount()) }}</span>
                                    </div>
                                    <div class="cl-inst-meta">
                                        @if($p->completed_lessons_count >= 5)
                                            {{ $p->completed_lessons_count }} completed lessons
                                        @else
                                            Verified instructor
                                        @endif
                                    </div>
                                    <a href="{{ route('instructors.show', $p) }}" class="cl-inst-link">View profile <i class="bi bi-arrow-right small"></i></a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4 text-muted">
                            <i class="bi bi-search fs-1 d-block mb-3"></i>
                            <p class="mb-2">No instructors found in {{ $cityName }} yet — be the first to list with us!</p>
                            <a href="{{ route('instruct-with-us') }}" class="btn btn-warning btn-sm fw-bold">Become an Instructor</a>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="tab-pane fade" id="cl-tab-new"><p class="text-muted text-center py-4">Showing newest instructors first…</p></div>
            <div class="tab-pane fade" id="cl-tab-lowest"><p class="text-muted text-center py-4">Sorted by lowest price first…</p></div>
            <div class="tab-pane fade" id="cl-tab-auto"><p class="text-muted text-center py-4">Filter by transmission preference at the top of the page.</p></div>
            <div class="tab-pane fade" id="cl-tab-female"><p class="text-muted text-center py-4">Showing female instructors…</p></div>
            <div class="tab-pane fade" id="cl-tab-fastest"><p class="text-muted text-center py-4">Showing instructors with the soonest availability…</p></div>
        </div>

        <div class="text-center mt-3">
            <span class="cl-pagination-dot active"></span>
            <span class="cl-pagination-dot"></span>
            <span class="cl-pagination-dot"></span>
        </div>

        <p class="text-muted small text-center mt-4 px-md-5">
            Top-rated driving instructors have earned outstanding driving lesson reviews for the {{ $cityName }} area. Learners commonly cite their consistency, ability to teach lessons in formal yet relaxed manner, their use of practical methods, deeply applicable advice and lesson planning that works around your work hours. Driving lessons don't get any more complete and tailored than our verified instructors.
        </p>
    </div>
</section>

{{-- ─────────── How Secure Licence works ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="cl-section-title">How Secure Licence works</h2>
            <p class="text-muted">We connect you with the best driving instructors in {{ $cityName }}</p>
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
                <p class="text-muted small">Book online or via the dashboard, then go through a simple payment confirmation, instant or via direct booking.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="cl-how-icon cl-how-icon-teal"><i class="bi bi-car-front-fill"></i></div>
                <h3 class="cl-how-title">Learn to Drive!</h3>
                <p class="text-muted small">Your instructor picks you up from your chosen address and you're on your way.</p>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── As featured in ─────────── --}}
<section class="py-4">
    <div class="container">
        <p class="text-center text-muted small mb-3">As featured in</p>
        <div class="cl-press-row">
            <span class="cl-press-logo">Herald Sun</span>
            <span class="cl-press-logo cl-press-logo-italic">Courier&amp;Mail</span>
            <span class="cl-press-logo">The Daily Telegraph</span>
            <span class="cl-press-logo">7NEWS</span>
            <span class="cl-press-logo cl-press-logo-circle">ABC</span>
        </div>
    </div>
</section>

{{-- ─────────── Why choose us ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="cl-section-title text-center mb-2">Why choose us?</h2>
        <p class="text-muted text-center mb-4">We are committed to your program with a sense of safety, quality &amp; trust. Hire our driving for an industry leader in the platform</p>
        <div class="row g-4">
            @php
                $benefits = [
                    ['Flexible Instructor Options', 'Change your instructor at any time to ensure a perfect fit for your learning style.', 'bi-arrow-repeat'],
                    ['Convenient Online Bookings', 'Fit your lessons into your busy schedule with 24/7 booking management.', 'bi-laptop'],
                    ['Stress Free Test Package', 'Driving test and driving lesson packages make you feel ready for your driving test.', 'bi-shield-fill-check'],
                    ['Gift Vouchers', 'Driving lesson gift cards are a perfect for a present popper present of driving lessons.', 'bi-gift-fill'],
                    ['Pricing Options', 'Bulk lesson discounts &amp; transparent instructor pricing.', 'bi-cash-stack'],
                ];
            @endphp
            @foreach($benefits as [$title, $desc, $ic])
                <div class="col-md-6 col-lg-4">
                    <div class="cl-benefit">
                        <div class="cl-benefit-icon"><i class="bi {{ $ic }}"></i></div>
                        <h3>{{ $title }}</h3>
                        <p class="text-muted small mb-0">{!! $desc !!}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── What learners say ─────────── --}}
<section class="py-5">
    <div class="container">
        <h2 class="cl-section-title text-center mb-4">What our 300,000+ learners say</h2>
        <p class="text-muted text-center mb-4">Learn from learners in {{ $cityName }} &amp; {{ $cityState }} about their Secure Licence experience.</p>
        <div class="row g-4">
            @php
                $tests = [
                    ['name' => 'Paul', 'text' => "He explained that this driving instructor took every single thing about my needs and helped me get through different situations on my driver's test."],
                    ['name' => 'Sidney', 'text' => "My driving instructor was knowledgeable and patient, I have learnt all the things I needed for my test."],
                    ['name' => 'Cecil', 'text' => "Best driving instructor experience for my driver and learner's licence."],
                    ['name' => 'Peter', 'text' => "I passed the same day thanks for my driving instructor's expertise, big help and confidence."],
                ];
            @endphp
            @foreach($tests as $t)
                <div class="col-md-6 col-lg-3">
                    <div class="ilc-testimonial">
                        <div class="ilc-test-avatar"><i class="bi bi-person-fill"></i></div>
                        <div class="ilc-test-stars">@for($i = 0; $i < 5; $i++)<i class="bi bi-star-fill"></i>@endfor</div>
                        <p class="ilc-test-text">{{ $t['text'] }}</p>
                        <div class="ilc-test-name">{{ $t['name'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4 ilc-test-dots">
            <span class="active"></span><span></span><span></span><span></span><span></span><span></span>
        </div>
    </div>
</section>

{{-- ─────────── Suburb quick links ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="cl-section-title mb-3">Looking for lessons in a specific suburb near {{ $cityName }}?</h2>
        <p class="text-muted small mb-4">We currently offer driving lessons in neighbouring Sydney &amp; its surrounding areas, including:</p>
        <div class="row g-2 cl-suburbs">
            @foreach($suburbs as $s)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('find-instructor.results') }}?q={{ urlencode($s->name) }}" class="cl-suburb-link">{{ $s->name }}</a>
                </div>
            @endforeach
        </div>
        <p class="text-muted small mt-4">If you don't see your suburb on the list, contact us — there's a good chance our instructors cover it.</p>
    </div>
</section>

{{-- ─────────── FAQs ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <h2 class="cl-section-title mb-2">FAQs</h2>
                <p class="text-muted small mb-4">Here's a few of the questions we get on a regular basis. Can't find the answer you're looking for? Please check our <a href="{{ route('support.home') }}">support page</a>.</p>
                <div class="accordion ilc-faq" id="cl-faq">
                    @php
                        $cityFaqs = [
                            ['q' => 'How Much Are EzLicence Driving Lessons in ' . $cityName . '?', 'a' => 'Driving lesson prices in ' . $cityName . ' typically start from $' . number_format($city['price'] - 15, 0) . '–$' . number_format($city['price'] + 10, 0) . '/hour. Multi-hour packages save more.'],
                            ['q' => 'Which is best driving school?', 'a' => 'Rather than a traditional driving school, Secure Licence connects you with independent verified instructors — letting you compare ratings, reviews and prices to pick the best fit.'],
                            ['q' => 'Can I change my instructor?', 'a' => 'Yes — you can switch instructors any time through your dashboard at no extra cost.'],
                        ];
                    @endphp
                    @foreach($cityFaqs as $i => $f)
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#cl-faq-{{ $i }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">{{ $f['q'] }}</button>
                            </h3>
                            <div id="cl-faq-{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#cl-faq">
                                <div class="accordion-body text-muted small">{{ $f['a'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="#" class="btn btn-warning fw-bold mt-3"><i class="bi bi-arrow-right me-1"></i>Read More FAQs</a>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── Featured guides ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="cl-section-title text-center mb-4">Learn more about driving with Secure Licence's guides</h2>
        <div class="row g-4">
            @php
                $guides = [
                    ['title' => 'How Do I Get My Full Licence in ' . $cityState . '?', 'tag' => 'Licence', 'date' => '15 February 2026'],
                    ['title' => '1 hr = 3 hrs: How to get bonus log book hours (' . $cityState . ')', 'tag' => 'Logbook', 'date' => '5 December 2025'],
                    ['title' => 'The Ultimate P1 Licence Guide for ' . $cityState . ' Learner Drivers', 'tag' => 'Licence Guide', 'date' => '12 December 2025'],
                ];
            @endphp
            @foreach($guides as $g)
                <div class="col-md-4">
                    <article class="cl-guide-card">
                        <div class="cl-guide-thumb"><i class="bi bi-book-fill"></i></div>
                        <div class="cl-guide-body">
                            <span class="ilc-blog-tag">{{ $g['tag'] }}</span>
                            <h3>{{ $g['title'] }}</h3>
                            <p class="text-muted small mb-0">{{ $g['date'] }}</p>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── SEO content block ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h2 class="cl-section-title text-center mb-3">Quality Driving Lessons in {{ $cityFull }}</h2>
                <p class="text-muted text-center mb-5">{{ $cityName }} learners benefit from the most experienced and most patient driving instructors. To pass the {{ $cityState }} driving test, knowledge, practical skills, and a calm head are all essential.</p>

                <h4 class="fw-bold mb-2">Embrace the Driving Experience in {{ $cityName }}</h4>
                <p class="text-muted small mb-4">Learning to drive requires sound judgment and intuitive responses, both of which are honed under the watchful eye of {{ $cityName }} expert driving instructors. With the right blend of theoretical training, practical driving and confidence-building exercises, our local driving school turns hesitant beginners into confident drivers.</p>

                <h4 class="fw-bold mb-2">Why Choose Us?</h4>
                <p class="text-muted small mb-4">We are committed to your progress and ease of use as a learning experience. On our website, we offer a wide range of driving instructors with different specialties and styles to make your learning journey ideal for you.</p>

                <h4 class="fw-bold mb-2">Expert Driving Instruction</h4>
                <p class="text-muted small mb-4">{{ $cityName }} licence is no joke. Our experienced instructors aren't satisfied unless you feel confident behind the wheel. We don't push you to your test before you're ready. We listen, learn what works for you, and adapt every lesson accordingly.</p>

                <p class="text-muted small">Ready to start your driving journey? Book a lesson now in the {{ $cityFull }} area with the friendliest driving instructors team in {{ $cityState }}.</p>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── Bottom CTA ─────────── --}}
<section class="dtp-cta-strip">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-md-5">
                <h2 class="mb-0 fw-bolder text-dark">Learn to drive today!</h2>
                <small class="text-dark">Join over 300,000+ learners driving with Secure Licence.</small>
            </div>
            <div class="col-md-7">
                <form action="{{ route('find-instructor.results') }}" method="get" class="dtp-cta-form">
                    <input type="hidden" name="suburb_id" id="cl-bottom-suburb-id">
                    <input type="hidden" name="q" id="cl-bottom-q" value="{{ $cityName }}">
                    <div class="btn-group dtp-trans-toggle" role="group">
                        <input type="radio" class="btn-check" name="transmission" id="cl-bottom-auto" value="auto" checked>
                        <label class="btn" for="cl-bottom-auto"><i class="bi bi-check-lg me-1"></i>Auto</label>
                        <input type="radio" class="btn-check" name="transmission" id="cl-bottom-manual" value="manual">
                        <label class="btn" for="cl-bottom-manual">Manual</label>
                    </div>
                    <input type="text" id="cl-bottom-suburb" class="form-control" placeholder="Enter your suburb" autocomplete="off">
                    <button type="submit" class="btn btn-light fw-bold"><i class="bi bi-search me-1"></i>Search</button>
                </form>
            </div>
        </div>
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
    attachSuburbAutocomplete('cl-hero-suburb', 'cl-hero-suburb-id', 'cl-hero-q');
    attachSuburbAutocomplete('cl-search-suburb', 'cl-search-suburb-id', 'cl-search-q');
    attachSuburbAutocomplete('cl-bottom-suburb', 'cl-bottom-suburb-id', 'cl-bottom-q');
})();
</script>
@endpush
@endsection
