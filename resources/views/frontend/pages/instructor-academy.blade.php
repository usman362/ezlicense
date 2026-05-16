@extends('layouts.frontend')

@section('title', 'Secure Licences Instructor Academy — Training for Driving Instructors')

@section('content')

{{-- ─────────── HERO ─────────── --}}
<section class="blog-hero">
    <div class="blog-hero-bg">
        <img src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=2000&q=80&auto=format&fit=crop"
             srcset="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=1200&q=80&auto=format&fit=crop 1200w,
                     https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=2000&q=80&auto=format&fit=crop 2000w"
             alt="">
        <div class="blog-hero-overlay"></div>
    </div>
    <div class="container blog-hero-inner">
        <nav aria-label="breadcrumb" class="blog-hero-breadcrumb mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Instructor Academy</li>
            </ol>
        </nav>
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="blog-hero-eyebrow"><i class="bi bi-mortarboard-fill me-1"></i>Training · Certification · Growth</span>
                <h1 class="blog-hero-title">
                    Secure Licences <span class="blog-hero-title-accent">Instructor Academy</span>
                </h1>
                <p class="blog-hero-sub">
                    Everything you need to launch and grow a successful driving instruction career — from licence prep to advanced teaching techniques and business strategy.
                </p>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <a href="{{ route('contact') }}" class="btn btn-warning fw-bold px-4 py-2">
                        <i class="bi bi-envelope-fill me-1"></i>Enquire about courses
                    </a>
                    <a href="#curriculum" class="btn btn-outline-light fw-bold px-4 py-2">
                        See curriculum
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── ACADEMY HIGHLIGHTS STRIP ─────────── --}}
<section class="py-5">
    <div class="container">
        <h2 class="cl-section-title text-center mb-4">Train with Australia's #1 instructor platform</h2>
        <div class="row g-4 cl-stats-row">
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">12</div>
                <div class="cl-stat-label">Comprehensive learning modules covering every aspect</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">98%</div>
                <div class="cl-stat-label">Academy graduates pass their state instructor exam first time</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">100%</div>
                <div class="cl-stat-label">Self-paced online — learn around your existing schedule</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">∞</div>
                <div class="cl-stat-label">Lifetime access to course content + future updates</div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── WHAT THE ACADEMY OFFERS ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="blog-eyebrow"><i class="bi bi-stars me-1"></i>What you'll get</span>
            <h2 class="cl-section-title">Built by senior instructors, for new instructors</h2>
            <p class="text-muted" style="max-width: 640px; margin: 0 auto;">Six core pillars covering everything from your first lesson plan to running a six-figure driving school.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-journal-check',     'Instructor licence prep',     "Step-by-step prep for your Certificate IV and state instructor licence exam. Mock tests, study guides, video walkthroughs."],
                ['bi-people-fill',       'Teaching techniques',         "Proven methods for nervous learners, advanced manoeuvres, test prep, and adapting to different learning styles."],
                ['bi-shield-fill-check', 'Safety & compliance',         "Stay current with state road rules, dual-control vehicle safety, insurance requirements and incident handling."],
                ['bi-graph-up-arrow',    'Business growth',             "Marketing your driving school, pricing strategy, client retention, scaling from solo to multi-instructor operations."],
                ['bi-laptop',            'Platform mastery',            "Get the most from your Secure Licences profile — booking optimisation, review strategy, ranking higher in search."],
                ['bi-award-fill',        'Academy certification',       "Earn a verified Academy badge on your instructor profile. Higher trust = more bookings from new learners."],
            ] as [$ic, $title, $desc])
                <div class="col-md-6 col-lg-4">
                    <div class="pp-conf-card">
                        <div class="pp-conf-icon"><i class="bi {{ $ic }}"></i></div>
                        <h3>{{ $title }}</h3>
                        <p>{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── CURRICULUM ─────────── --}}
<section class="py-5" id="curriculum">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5">
                <span class="blog-eyebrow"><i class="bi bi-book-half me-1"></i>Curriculum</span>
                <h2 class="cl-section-title mb-3">12 modules. One complete instructor toolkit.</h2>
                <p class="text-muted">Work through modules at your own pace. Each module has lessons, real-world case studies, and a short knowledge check at the end.</p>
                <ul class="ia-curriculum-perks">
                    <li><i class="bi bi-check-circle-fill"></i>Self-paced online learning</li>
                    <li><i class="bi bi-check-circle-fill"></i>Video lessons by senior instructors</li>
                    <li><i class="bi bi-check-circle-fill"></i>Downloadable study guides &amp; mock tests</li>
                    <li><i class="bi bi-check-circle-fill"></i>Earn an Academy-certified badge</li>
                </ul>
            </div>
            <div class="col-lg-7">
                <div class="ia-modules">
                    @foreach([
                        ['01', 'Foundations of instruction',         'Teaching philosophy, learner psychology, first-lesson essentials.'],
                        ['02', 'Vehicle control basics',             'Cockpit drill, steering technique, gear changing, observation skills.'],
                        ['03', 'Manoeuvres & low-speed control',     'Reverse parking, hill starts, three-point turns, parallel parking.'],
                        ['04', 'Hazard perception & defensive driving','Identifying risks, scanning techniques, gap selection, anticipation.'],
                        ['05', 'High-speed driving & motorways',     'Lane discipline, overtaking, merging, motorway hazards.'],
                        ['06', 'Adverse conditions',                 'Wet weather, night driving, fog, gravel — adapting technique for safety.'],
                        ['07', 'Test preparation & coaching',        'Mock test routes, dealing with nerves, examiner expectations.'],
                        ['08', 'Communication & feedback',           'How to give actionable feedback, dealing with frustration, building confidence.'],
                        ['09', 'Compliance & legal essentials',      "WWCC, instructor licence renewal, insurance, incident reporting."],
                        ['10', 'Marketing your driving school',      'Profile optimisation, suburb targeting, referral programs, social proof.'],
                        ['11', 'Pricing & packages',                 'Setting rates by area, bundle pricing, test packages, cancellation policies.'],
                        ['12', 'Scaling your business',              "From solo to driving school — hiring, vehicle fleet, brand building."],
                    ] as [$num, $title, $desc])
                        <details class="ia-module">
                            <summary>
                                <span class="ia-module-num">{{ $num }}</span>
                                <span class="ia-module-title">{{ $title }}</span>
                                <i class="bi bi-chevron-down ia-module-chev"></i>
                            </summary>
                            <p class="ia-module-body">{{ $desc }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BECOME AN INSTRUCTOR IN 4 STEPS ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="blog-eyebrow"><i class="bi bi-signpost-2 me-1"></i>Your pathway</span>
            <h2 class="cl-section-title">Become a driving instructor in 4 steps</h2>
        </div>
        <div class="row g-4 iwu-steps">
            @foreach([
                ['1', 'bi-check2-circle',   'Check eligibility',  'Full licence held 3+ years, clean driving record, willingness to complete a Working with Children Check and police check.'],
                ['2', 'bi-mortarboard-fill','Complete training',   'Enrol in Certificate IV in Driving Instruction (Transport & Logistics) — our Academy preps you for it module by module.'],
                ['3', 'bi-patch-check-fill','Get your licence',    "Apply to your state transport authority (Service NSW, VicRoads, etc.) for your driving instructor licence."],
                ['4', 'bi-car-front-fill',  'Join Secure Licences','Sign up free, get verified, set your prices and availability, start accepting bookings.'],
            ] as [$num, $ic, $title, $desc])
                <div class="col-md-6 col-lg-3 text-center">
                    <div class="iwu-step-num">{{ $num }}</div>
                    <div class="iwu-step-icon"><i class="bi {{ $ic }}"></i></div>
                    <h3 class="iwu-step-title">{{ $title }}</h3>
                    <p class="text-muted small">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── CERTIFICATION BADGE ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 text-center">
                <div class="ia-cert-badge">
                    <svg class="ia-cert-svg" viewBox="0 0 200 200" aria-hidden="true">
                        {{-- Outer ring with notches --}}
                        <circle cx="100" cy="100" r="92" fill="#fff" stroke="#fbbf24" stroke-width="6"/>
                        <circle cx="100" cy="100" r="78" fill="none" stroke="#fbbf24" stroke-width="2" stroke-dasharray="4 4"/>
                        {{-- Center fill --}}
                        <circle cx="100" cy="100" r="64" fill="#fef3c7"/>
                        {{-- Mortar board icon --}}
                        <g transform="translate(100, 95)">
                            <polygon points="-30,-8 0,-22 30,-8 0,6" fill="#1f2937"/>
                            <path d="M -22 -4 L -22 14 L 22 14 L 22 -4" fill="none" stroke="#1f2937" stroke-width="3" stroke-linecap="round"/>
                            <line x1="14" y1="-6" x2="22" y2="6" stroke="#dc2626" stroke-width="2.5"/>
                            <circle cx="22" cy="6" r="4" fill="#dc2626"/>
                        </g>
                        {{-- Label --}}
                        <text x="100" y="148" font-size="11" font-weight="900" fill="#92400e" text-anchor="middle" letter-spacing="2">ACADEMY</text>
                        <text x="100" y="162" font-size="9" font-weight="700" fill="#92400e" text-anchor="middle" letter-spacing="3">CERTIFIED</text>
                        {{-- Ribbon underneath --}}
                        <polygon points="50,180 100,200 150,180 145,160 100,180 55,160" fill="#dc2626"/>
                        <polygon points="55,160 100,180 145,160 145,150 100,165 55,150" fill="#b91c1c"/>
                    </svg>
                </div>
            </div>
            <div class="col-lg-6">
                <span class="blog-eyebrow"><i class="bi bi-award-fill me-1"></i>Academy certification</span>
                <h2 class="cl-section-title mb-3">Stand out with a verified badge on your profile</h2>
                <p class="text-muted">When you complete the Academy program and pass the final assessment, you earn the Secure Licences <strong>Academy Certified</strong> badge — displayed prominently on your public profile and in search results.</p>
                <ul class="ia-cert-points">
                    <li><i class="bi bi-arrow-up-right-circle-fill"></i><strong>Higher search ranking</strong> — certified instructors get a boost in suburb search results.</li>
                    <li><i class="bi bi-arrow-up-right-circle-fill"></i><strong>Increased learner trust</strong> — the badge signals you've gone above standard requirements.</li>
                    <li><i class="bi bi-arrow-up-right-circle-fill"></i><strong>Higher conversion rates</strong> — certified instructors see 30%+ more profile-to-booking conversions on average.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── TESTIMONIALS / GRADUATES ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <span class="blog-eyebrow"><i class="bi bi-chat-quote-fill me-1"></i>Academy graduates</span>
            <h2 class="cl-section-title">From career-changers to top-rated instructors</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Linh',    'Melbourne, VIC',  "The Academy gave me the structure I needed. I was a corporate accountant for 12 years — six months after the program I was full-time instructing and loving it."],
                ['Hassan',  'Sydney, NSW',     "The teaching techniques module was a game-changer. Learning how to coach nervous learners doubled my pass rates. My reviews speak for themselves."],
                ['Sarah',   'Brisbane, QLD',   "Self-paced learning fit perfectly around my old job. I finished the Cert IV prep, sat my licence, and joined the platform within 8 months. Best career decision."],
            ] as [$name, $place, $text])
                <div class="col-md-4">
                    <div class="ilc-testimonial">
                        <div class="ilc-test-avatar"><i class="bi bi-person-fill"></i></div>
                        <div class="ilc-test-stars">@for($i=0;$i<5;$i++)<i class="bi bi-star-fill"></i>@endfor</div>
                        <p class="ilc-test-text">{{ $text }}</p>
                        <div class="ilc-test-name">{{ $name }}</div>
                        <div class="text-muted small">{{ $place }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── FAQ ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <span class="blog-eyebrow"><i class="bi bi-question-circle me-1"></i>FAQs</span>
                <h2 class="cl-section-title mb-3">Got questions?</h2>
                <p class="text-muted small mb-3">Our Academy team is happy to walk you through the program before you enrol.</p>
                <a href="{{ route('contact') }}" class="btn btn-warning fw-bold"><i class="bi bi-headset me-1"></i>Talk to us</a>
            </div>
            <div class="col-lg-8">
                <div class="accordion ilc-faq" id="ia-faq">
                    @foreach([
                        ['How long does the program take?',           'The full 12-module program typically takes 8–12 weeks at part-time pace (around 5 hours/week). You can move faster or slower — your account, your pace.'],
                        ['Is this the same as a Certificate IV?',     "No — the Academy is preparation and supplementary training. You'll still need to enrol in an accredited Certificate IV in Driving Instruction at an RTO to get your government qualification. Our program prepares you to ace it."],
                        ['What does enrolment cost?',                  'Pricing varies by intake. Contact our Academy team and we will send through current course fees, payment plans and any active scholarships for new instructors.'],
                        ['Do I need to be a driving instructor already?',"No — most Academy students enrol BEFORE getting their licence. The program is designed to take you from interested to fully qualified, then onto our platform."],
                        ['What if I fail the final assessment?',       'You get two free re-attempts on the final assessment. If you still need more time, we offer 1:1 coaching sessions with senior instructors at a small additional cost.'],
                        ['Is the certification recognised by the government?', "The Academy certification is our internal mark of excellence, displayed on your Secure Licences profile. Your government driving instructor licence still comes from your state transport authority via the Certificate IV."],
                    ] as $i => [$q, $a])
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#ia-faq-{{ $i }}">{{ $q }}</button>
                            </h3>
                            <div id="ia-faq-{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#ia-faq">
                                <div class="accordion-body text-muted small">{{ $a }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BOTTOM CTA ─────────── --}}
<section class="py-5 blog-cta-section">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-8">
                <h2 class="mb-2 fw-bolder text-dark">Ready to start your instructor journey?</h2>
                <p class="mb-0 text-dark">Talk to the Academy team about intake dates, course fees, and scholarships for new instructors.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('contact') }}" class="btn btn-dark fw-bold btn-lg px-4">
                    <i class="bi bi-envelope-fill me-2"></i>Enquire now
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
