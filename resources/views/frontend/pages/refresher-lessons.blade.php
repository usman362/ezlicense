@extends('layouts.frontend')
@section('title', 'Refresher Driving Lessons')

@section('content')
{{-- ─────────── HERO: dark navy with suburb search ─────────── --}}
<section class="ilc-hero">
    <div class="container ilc-hero-inner text-center">
        <h1 class="ilc-hero-title">Refresher Driving Lessons</h1>
        <p class="ilc-hero-sub">Find, compare &amp; book driving instructors online. Regain your driving skills &amp; confidence today.</p>
        <form action="{{ route('find-instructor.results') }}" method="get" class="ilc-search-form mt-4">
            <input type="hidden" name="suburb_id" id="rl-hero-suburb-id">
            <input type="hidden" name="q" id="rl-hero-q">
            <div class="dtp-form-row justify-content-center">
                <div class="btn-group dtp-trans-toggle" role="group">
                    <input type="radio" class="btn-check" name="transmission" id="rl-auto" value="auto" checked>
                    <label class="btn" for="rl-auto"><i class="bi bi-check-lg me-1"></i>Auto</label>
                    <input type="radio" class="btn-check" name="transmission" id="rl-manual" value="manual">
                    <label class="btn" for="rl-manual">Manual</label>
                </div>
                <input type="text" id="rl-hero-suburb" class="form-control dtp-suburb-input" placeholder="Enter your suburb" autocomplete="off">
                <button type="submit" class="btn btn-warning fw-bold dtp-search-btn"><i class="bi bi-search me-1"></i>Search</button>
            </div>
        </form>
    </div>
</section>

{{-- ─────────── Bridge: car illustration + "Book a driving instructor in your area." ─────────── --}}
<section class="ilc-bridge">
    <div class="container">
        <div class="ilc-bridge-arrow"><i class="bi bi-arrow-down-circle-fill"></i></div>
        <div class="row align-items-center g-4 mt-2">
            <div class="col-md-5 text-center">
                {{-- SVG: yellow driving school car with L-plate --}}
                <svg viewBox="0 0 320 200" xmlns="http://www.w3.org/2000/svg" class="ilc-bridge-img" aria-hidden="true">
                    <defs><filter id="rlCarShadow"><feDropShadow dx="0" dy="6" stdDeviation="6" flood-opacity="0.18"/></filter></defs>
                    <ellipse cx="160" cy="180" rx="130" ry="8" fill="#000" opacity="0.1"/>
                    <g filter="url(#rlCarShadow)" transform="translate(40,40)">
                        <circle cx="50" cy="130" r="20" fill="#1f2937"/>
                        <circle cx="50" cy="130" r="8" fill="#9ca3af"/>
                        <circle cx="190" cy="130" r="20" fill="#1f2937"/>
                        <circle cx="190" cy="130" r="8" fill="#9ca3af"/>
                        <rect x="0" y="92" width="240" height="42" rx="10" fill="#fbbf24" stroke="#1f2937" stroke-width="2"/>
                        <path d="M30 60 Q40 40 70 40 L170 40 Q200 40 210 60 L220 92 L20 92 Z" fill="#fbbf24" stroke="#1f2937" stroke-width="2"/>
                        <path d="M40 60 Q50 50 75 50 L120 50 L125 88 L35 88 Z" fill="#dbeafe" stroke="#1f2937" stroke-width="1"/>
                        <path d="M130 50 L170 50 Q195 50 205 60 L210 88 L130 88 Z" fill="#dbeafe" stroke="#1f2937" stroke-width="1"/>
                        <ellipse cx="240" cy="105" rx="6" ry="4" fill="#fffbeb"/>
                        <rect x="0" y="100" width="20" height="20" rx="3" fill="#fff" stroke="#1f2937" stroke-width="1.5"/>
                        <text x="10" y="116" font-family="Arial" font-size="15" font-weight="900" fill="#dc2626" text-anchor="middle">L</text>
                    </g>
                </svg>
            </div>
            <div class="col-md-7 text-md-start text-center">
                <h2 class="ilc-section-title">Book a driving instructor in your area.</h2>
                <p class="text-muted mb-0">Driving instructors on Secure Licences have helped thousands of driver's licence holders brush up on their skills.</p>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── Yellow CTA strip ─────────── --}}
<section class="dtp-cta-strip">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-3">
                    <div class="dtp-cta-icon"><i class="bi bi-currency-dollar"></i></div>
                    <div>
                        <h4 class="mb-0 fw-bolder">Driving lesson pricing &amp; packages</h4>
                        <small class="text-dark">Buy more lessons &amp; get more discount</small>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <label class="fw-bold mb-2"><i class="bi bi-search me-1"></i>Check suburb pricing</label>
                <form action="{{ route('find-instructor.results') }}" method="get" class="dtp-cta-form">
                    <input type="hidden" name="suburb_id" id="rl-cta-suburb-id">
                    <input type="hidden" name="q" id="rl-cta-q">
                    <div class="btn-group dtp-trans-toggle" role="group">
                        <input type="radio" class="btn-check" name="transmission" id="rl-cta-auto" value="auto" checked>
                        <label class="btn" for="rl-cta-auto"><i class="bi bi-check-lg me-1"></i>Auto</label>
                        <input type="radio" class="btn-check" name="transmission" id="rl-cta-manual" value="manual">
                        <label class="btn" for="rl-cta-manual">Manual</label>
                    </div>
                    <input type="text" id="rl-cta-suburb" class="form-control" placeholder="Enter your suburb" autocomplete="off">
                    <button type="submit" class="btn btn-light fw-bold"><i class="bi bi-search me-1"></i>Search</button>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── Purchase with confidence dark strip ─────────── --}}
<section class="ilc-confidence-strip">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-center gap-3 text-center">
        <i class="bi bi-shield-fill-check"></i>
        <div>
            <strong>Purchase with confidence</strong>
            <span class="opacity-75 ms-2">Flexible rebooking if plans change.</span>
        </div>
    </div>
</section>

{{-- ─────────── How Secure Licences works ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="ilc-section-title">How Secure Licences works</h2>
            <p class="text-muted">Simple &amp; flexible booking system</p>
        </div>
        <div class="row g-5 align-items-center">
            <div class="col-md-5">
                <div class="ilc-howto-illu">
                    <div class="ilc-howto-screen">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <button type="button" class="ilc-play-btn"><i class="bi bi-play-fill"></i> Play Video</button>
                </div>
            </div>
            <div class="col-md-7">
                <div class="ilc-step">
                    <div class="ilc-step-num">1</div>
                    <div>
                        <h3>Find Your Driving Instructors</h3>
                        <p>Choose from a wide variety of instructors in your area. Check rating &amp; reviews from real learners.</p>
                    </div>
                </div>
                <div class="ilc-step">
                    <div class="ilc-step-num">2</div>
                    <div>
                        <h3>Book Your Driving Lessons</h3>
                        <p>Book online with instant confirmation. Easily manage your lesson schedule via our online dashboard.</p>
                    </div>
                </div>
                <div class="ilc-step">
                    <div class="ilc-step-num">3</div>
                    <div>
                        <h3>Learn to Drive</h3>
                        <p>Your instructor picks you up from your chosen address and you're on your way to becoming a confident driver again.</p>
                    </div>
                </div>
                <a href="{{ route('find-instructor') }}" class="btn btn-warning fw-bolder px-4 mt-3"><i class="bi bi-arrow-right-circle-fill me-1"></i>Start learning to drive now ›</a>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── Should you get refresher driving lessons? ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="ilc-section-title">Should you get refresher driving lessons?</h2>
        </div>
        <div class="row g-4 align-items-start">
            <div class="col-md-5 text-center">
                {{-- SVG: stylized steering wheel with hands — refresher driving theme --}}
                <svg viewBox="0 0 280 280" xmlns="http://www.w3.org/2000/svg" class="rl-illu-svg" aria-hidden="true">
                    <defs>
                        <radialGradient id="rlWheelBg" cx="50%" cy="50%" r="50%">
                            <stop offset="0%" stop-color="#fef3c7"/>
                            <stop offset="100%" stop-color="#fde68a"/>
                        </radialGradient>
                    </defs>
                    <circle cx="140" cy="140" r="138" fill="url(#rlWheelBg)"/>
                    {{-- Steering wheel outer ring --}}
                    <circle cx="140" cy="140" r="90" fill="none" stroke="#1f2937" stroke-width="14"/>
                    {{-- Inner hub --}}
                    <circle cx="140" cy="140" r="28" fill="#1f2937"/>
                    <text x="140" y="148" font-family="Arial" font-size="14" font-weight="900" fill="#fbbf24" text-anchor="middle">SL</text>
                    {{-- Spokes --}}
                    <path d="M140 60 L140 112 M140 168 L140 220 M60 140 L112 140 M168 140 L220 140" stroke="#1f2937" stroke-width="10" stroke-linecap="round"/>
                    {{-- Hands on the wheel --}}
                    <g fill="#fbbf24" stroke="#1f2937" stroke-width="2">
                        <ellipse cx="55" cy="140" rx="14" ry="20"/>
                        <ellipse cx="225" cy="140" rx="14" ry="20"/>
                    </g>
                </svg>
            </div>
            <div class="col-md-7">
                <div class="rl-q-item">
                    <div class="rl-q-head"><i class="bi bi-question-circle-fill"></i><strong>Haven't driven regularly for a while?</strong></div>
                    <p class="rl-q-body">You may have your driver's licence but not been driving regularly for quite some time. If your skills and confidence are in need of a boost, then refresher driving lessons can help you get back up to speed.</p>
                </div>

                <div class="rl-q-item">
                    <div class="rl-q-head"><i class="bi bi-question-circle-fill"></i><strong>Driving in a new city?</strong></div>
                    <p class="rl-q-body">You may be a confident and experienced driver who has recently moved to a new location. You may need to improve your confidence and awareness driving on a different side of the road, or maybe the unfamiliar road rules and conditions have dented your confidence.</p>
                </div>

                <div class="rl-q-item">
                    <div class="rl-q-head"><i class="bi bi-question-circle-fill"></i><strong>Want to learn to drive a manual?</strong></div>
                    <p class="rl-q-body">You may have bought a manual car or are now required to drive one for your employment. Refresher driving lessons can help experienced drivers learn to drive and build confidence in a manual transmission car.</p>
                </div>

                <div class="rl-q-item">
                    <div class="rl-q-head"><i class="bi bi-question-circle-fill"></i><strong>Have you lost your licence?</strong></div>
                    <p class="rl-q-body">If you've lost your licence in the past and you're just getting back on the road, then refresher driving lessons with a professional instructor will help you bridge knowledge or skill gaps that may have developed in that time.</p>
                </div>

                <div class="rl-q-item">
                    <div class="rl-q-head"><i class="bi bi-question-circle-fill"></i><strong>Have you been racking up fines or demerit points?</strong></div>
                    <p class="rl-q-body">If you find that you've been getting along with driving fines or demerit points regularly (for example speeding, parking incorrectly, or failure to give way), then correcting your driving skills with refresher driving lessons could help you get yourself back on track to saving your money.</p>
                </div>

                <div class="rl-q-item">
                    <div class="rl-q-head"><i class="bi bi-question-circle-fill"></i><strong>Have you reached the age of 75 or 85?</strong></div>
                    <p class="rl-q-body">If you're a senior driver aged 75 or over then you are required by law to prove your medical fitness if you wish to retain your full unrestricted licence. Drivers aged 75 and over are required to have a yearly medical assessment while drivers aged 85 and over are required to have a yearly medical assessment and also pass an on-road driving assessment every two years.</p>
                </div>

                <a href="{{ route('find-instructor') }}" class="btn btn-warning fw-bolder px-4 mt-3"><i class="bi bi-arrow-right-circle-fill me-1"></i>Find your driving instructor now ›</a>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── Are your driving skills in need of a boost? ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-md-5 text-center">
                {{-- SVG: confident driver giving thumbs-up with L-plate badge --}}
                <svg viewBox="0 0 280 280" xmlns="http://www.w3.org/2000/svg" class="rl-illu-svg" aria-hidden="true">
                    <defs>
                        <radialGradient id="rlBoostBg" cx="50%" cy="50%" r="50%">
                            <stop offset="0%" stop-color="#fde68a"/>
                            <stop offset="100%" stop-color="#fbbf24"/>
                        </radialGradient>
                    </defs>
                    <circle cx="140" cy="140" r="138" fill="url(#rlBoostBg)"/>
                    {{-- Confident driver figure (head + body) --}}
                    <g transform="translate(140,90)">
                        {{-- Head --}}
                        <circle cx="0" cy="0" r="30" fill="#fef3c7" stroke="#1f2937" stroke-width="2.5"/>
                        {{-- Smile --}}
                        <path d="M-12 6 Q0 18 12 6" fill="none" stroke="#1f2937" stroke-width="2.5" stroke-linecap="round"/>
                        {{-- Eyes --}}
                        <circle cx="-10" cy="-4" r="2.5" fill="#1f2937"/>
                        <circle cx="10" cy="-4" r="2.5" fill="#1f2937"/>
                        {{-- Body --}}
                        <path d="M-40 50 Q-40 35 -20 30 L20 30 Q40 35 40 50 L40 130 L-40 130 Z" fill="#1f2937"/>
                        {{-- Tie --}}
                        <path d="M-3 30 L3 30 L4 60 L0 70 L-4 60 Z" fill="#fbbf24"/>
                    </g>
                    {{-- L-plate badge (top right) --}}
                    <g transform="translate(200,40)">
                        <rect x="0" y="0" width="38" height="38" rx="5" fill="#fff" stroke="#1f2937" stroke-width="2"/>
                        <text x="19" y="30" font-family="Arial" font-size="28" font-weight="900" fill="#dc2626" text-anchor="middle">L</text>
                    </g>
                </svg>
            </div>
            <div class="col-md-7">
                <h2 class="ilc-section-title mb-3">Are your driving skills in need of a boost?</h2>
                <p class="text-muted mb-0">Driving lessons are not just for new learners. Driving lessons can be booked by anyone who believes they are in need of a skill or confidence boost.</p>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── Testimonials ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="ilc-section-title text-center mb-2">What our learners say</h2>
        <p class="text-muted text-center mb-4">Choose a driving instructor you can trust</p>
        <div class="row g-4">
            @php
                $testimonials = [
                    ['name' => 'Adriana', 'text' => 'Adriana is really patient and an excellent instructor. She made identifying actions so easy, gave me clear tips and even practical lessons. Would highly recommend Adriana to anyone looking to learn safely.'],
                    ['name' => 'Sam', 'text' => "Passed on my first attempt and all thanks to my driving instructor who explained with so much teaching with everything to become a good road user. I am also patient and made me really comfortable. I highly recommend."],
                    ['name' => 'Sheraz', 'text' => "Sheraz is an incredible driving instructor. I highly recommend him. My first driving experience with Sheraz was many years back, I really enjoyed his time and have learnt so much."],
                ];
            @endphp
            @foreach($testimonials as $t)
                <div class="col-md-4">
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
            <span class="active"></span><span></span><span></span><span></span><span></span>
        </div>
    </div>
</section>

{{-- ─────────── FAQ + Featured Blogs ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <h2 class="ilc-section-title mb-4">Top Frequently Asked Questions</h2>
                <div class="accordion ilc-faq" id="rl-faq">
                    @php
                        $faqs = [
                            ['q' => 'How Much Do Driving Lessons Cost?', 'a' => 'Driving lesson prices vary by instructor and area. On Secure Licences, lessons typically start from $55–$85/hour. Multi-hour packages save more.'],
                            ['q' => 'Do You Offer Any Special Lessons to Prepare for the Driving Test?', 'a' => 'Yes — our 2.5-hour Driving Test Package includes a pre-test warm-up lesson plus use of the instructor\'s vehicle for the test.'],
                            ['q' => 'How Many Driving Lessons Do I Need?', 'a' => 'Most refresher learners take between 2–5 hours of professional driving lessons. Your instructor will help assess your progress.'],
                            ['q' => 'Can Driving Lessons Count Towards My Logbook Hours?', 'a' => 'Yes — professional driving lessons count as 3-for-1 hours in your NSW logbook (up to 30 logged hours).'],
                            ['q' => 'What if there are no available Driving Instructors in my area?', 'a' => 'Try expanding your search radius or contact our support team — we are continually adding new instructors.'],
                            ['q' => 'Can I take Refresher Driving Lessons?', 'a' => 'Absolutely. Many of our instructors offer refresher lessons for licensed drivers returning to driving.'],
                            ['q' => 'Can I Change Instructors?', 'a' => 'Yes — you can switch instructors any time through your dashboard at no extra cost.'],
                            ['q' => 'Is Secure Licences a Driving School?', 'a' => 'No, we are an online marketplace connecting learners with verified independent driving instructors across Australia.'],
                            ['q' => 'Can I Book Driving Lessons to Learn How to Drive Manual?', 'a' => 'Yes — filter by Manual transmission when searching to find instructors offering manual lessons.'],
                            ['q' => 'Where does Secure Licences offer driving lessons?', 'a' => 'We service 3,700+ suburbs across NSW, VIC, QLD, WA, SA, TAS and ACT.'],
                        ];
                    @endphp
                    @foreach($faqs as $i => $f)
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#rl-faq-{{ $i }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">{{ $f['q'] }}</button>
                            </h3>
                            <div id="rl-faq-{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#rl-faq">
                                <div class="accordion-body text-muted small">{{ $f['a'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="#" class="btn btn-outline-dark fw-semibold mt-3">Read more FAQs</a>
            </div>
            <div class="col-lg-5">
                <h2 class="ilc-section-title mb-4">Featured Blogs</h2>
                <div class="ilc-blogs">
                    @php
                        $blogs = [
                            ['title' => "Australia's Most Common Driving Theory Practice Test Mistakes, Revealed by Data", 'tag' => 'Theory'],
                            ['title' => "Driving Test Tips: A guide to passing your driving test", 'tag' => 'Test Day'],
                            ['title' => "5 Tips for Choosing a Good Driving Instructor", 'tag' => 'Choosing Instructor'],
                        ];
                    @endphp
                    @foreach($blogs as $b)
                        <article class="ilc-blog-card">
                            <div class="ilc-blog-thumb"><i class="bi bi-book-half"></i></div>
                            <div class="flex-grow-1">
                                <div class="ilc-blog-tag">{{ $b['tag'] }}</div>
                                <h4>{{ $b['title'] }}</h4>
                            </div>
                            <i class="bi bi-arrow-right ilc-blog-arrow"></i>
                        </article>
                    @endforeach
                    <a href="{{ route('blog.index') }}" class="btn btn-outline-dark fw-semibold w-100 mt-2">Read More Blogs</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── Why choose Secure Licences ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="ilc-section-title text-center mb-2">Why choose Secure Licences?</h2>
        <p class="text-muted text-center mb-5">Unlike a typical driving school, Secure Licences is an Australian-first platform that allows learner drivers &amp; parents to find, compare and book verified driving instructors online.</p>

        <div class="row g-4 text-center mb-5 ilc-stats-row">
            <div class="col-md-4">
                <div class="ilc-stat"><div class="ilc-stat-num">1000+</div><div class="ilc-stat-label">DRIVING INSTRUCTORS</div></div>
            </div>
            <div class="col-md-4">
                <div class="ilc-stat"><div class="ilc-stat-num">3700+</div><div class="ilc-stat-label">SUBURBS SERVICED</div></div>
            </div>
            <div class="col-md-4">
                <div class="ilc-stat"><div class="ilc-stat-num">#1</div><div class="ilc-stat-label">ONLINE BOOKINGS</div></div>
            </div>
        </div>

        <div class="row g-3">
            @php
                $features = [
                    ['Choose your own private driving instructor', 'bi-person-check-fill'],
                    ['Manage your driving lesson bookings online', 'bi-calendar-check-fill'],
                    ['Licenced and accredited driving instructors', 'bi-patch-check-fill'],
                    ['Change your driving instructor online', 'bi-arrow-repeat'],
                    ['Eligible for bonus log book hours (3 = 1 hour)', 'bi-journal-bookmark-fill'],
                    ['Largest choice of driving instructors in Australia', 'bi-grid-fill'],
                    ['Book driving lessons online in real-time', 'bi-lightning-charge-fill'],
                    ['International drivers licence conversions', 'bi-globe2'],
                    ['Driving instructor cars dual-controlled', 'bi-car-front-fill'],
                    ['Manage your lessons online via online dashboard', 'bi-laptop'],
                    ['Patient & friendly, hand-picked driving instructors', 'bi-emoji-smile-fill'],
                    ['Auto & manual cars available', 'bi-gear-fill'],
                ];
            @endphp
            @foreach($features as [$txt, $ic])
                <div class="col-md-6 col-lg-4">
                    <div class="ilc-feature-item">
                        <i class="bi {{ $ic }}"></i>
                        <span>{{ $txt }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('find-instructor') }}" class="btn btn-warning fw-bolder px-5 py-3">Book a driving school today ›</a>
        </div>
    </div>
</section>

{{-- ─────────── The Secure Licences advantage ─────────── --}}
<section class="py-5">
    <div class="container">
        <h2 class="ilc-section-title text-center mb-4">The Secure Licences advantage</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion ilc-advantage" id="rl-adv">
                    @php
                        $adv = [
                            ['t' => 'Book driving lessons online in under 60 seconds', 'd' => 'Compare verified instructors, view availability and book online in under a minute.'],
                            ['t' => 'More control over your bookings', 'd' => 'View all your upcoming and past bookings, reschedule with one click.'],
                            ['t' => 'Your online dashboard', 'd' => 'Manage everything in one place — bookings, payments, reviews, instructor history.'],
                            ['t' => 'The widest range of driving instructors', 'd' => '1000+ verified instructors across Australia, including auto, manual, and female-only options.'],
                            ['t' => 'Servicing YOUR area', 'd' => 'We cover 3,700+ suburbs nationwide — find an instructor near you.'],
                        ];
                    @endphp
                    @foreach($adv as $i => $a)
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#rl-adv-{{ $i }}">{{ $a['t'] }}</button>
                            </h3>
                            <div id="rl-adv-{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#rl-adv">
                                <div class="accordion-body text-muted small">{{ $a['d'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="ilc-faq-callout mt-4">
                    <h4 class="fw-bolder mb-2">How do I find private driving instructors near me?</h4>
                    <p class="text-muted small mb-0">We know what it's like — life gets busy and getting back behind the wheel feels overwhelming. Whether you live in Sydney, Melbourne, Brisbane, Adelaide or beyond, Secure Licences makes it easy: search your suburb, browse local verified instructors and book online in under 60 seconds.</p>
                    <p class="text-muted small mt-3 mb-0">Whether you're brushing up after a few years, returning after an injury, or easing yourself back into the driver's seat, our platform connects you with instructors who fit your schedule, vehicle preference and learning style.</p>
                    <p class="small mt-3 mb-0"><a href="{{ route('blog.index') }}" class="fw-semibold">Learn more</a> about how to choose a driving instructor for you.</p>
                </div>
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
    attachSuburbAutocomplete('rl-hero-suburb', 'rl-hero-suburb-id', 'rl-hero-q');
    attachSuburbAutocomplete('rl-cta-suburb', 'rl-cta-suburb-id', 'rl-cta-q');
})();
</script>
@endpush
@endsection
