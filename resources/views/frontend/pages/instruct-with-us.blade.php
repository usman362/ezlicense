@extends('layouts.frontend')

@section('title', 'Become a Driving Instructor — Instruct with Secure Licence')

@section('content')

{{-- ─────────── HERO (driver photo background) ─────────── --}}
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
                <li class="breadcrumb-item active">Become an Instructor</li>
            </ol>
        </nav>
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="blog-hero-eyebrow"><i class="bi bi-person-badge me-1"></i>For Driving Instructors</span>
                <h1 class="blog-hero-title">
                    Grow your driving school with <span class="blog-hero-title-accent">Secure Licence</span>
                </h1>
                <p class="blog-hero-sub">
                    Join Australia's fastest-growing platform for driving instructors. More bookings, less admin — manage your schedule, payments and learners from one place.
                </p>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <a href="{{ route('instructor-application.show') }}" class="btn btn-warning fw-bold px-4 py-2">
                        <i class="bi bi-file-earmark-person-fill me-1"></i>Apply to teach
                    </a>
                    <a href="#how-it-works" class="btn btn-outline-light fw-bold px-4 py-2">
                        See how it works
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── STATS STRIP ─────────── --}}
<section class="py-5">
    <div class="container">
        <h2 class="cl-section-title text-center mb-4">Why instructors love Secure Licence</h2>
        <div class="row g-4 cl-stats-row">
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">1,000+</div>
                <div class="cl-stat-label">Active driving instructors Australia-wide</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">300k+</div>
                <div class="cl-stat-label">Learners booking through the platform</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">$2k+</div>
                <div class="cl-stat-label">Average weekly earnings for full-time instructors</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">0%</div>
                <div class="cl-stat-label">Setup or subscription fees — we only earn when you do</div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BENEFITS GRID ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="blog-eyebrow"><i class="bi bi-stars me-1"></i>Built for instructors</span>
            <h2 class="cl-section-title">Everything you need to run your driving school</h2>
            <p class="text-muted" style="max-width: 640px; margin: 0 auto;">From the first enquiry to test-day pickup, we handle the admin so you can focus on teaching.</p>
        </div>
        <div class="row g-4">
            @php
                $benefits = [
                    ['bi-calendar-check-fill', 'Smart calendar',        'Set your availability once and let learners book the slots that suit them. Drag-and-drop to reschedule, recurring lessons supported.'],
                    ['bi-people-fill',         'Steady stream of leads', 'Get discovered by thousands of learners searching your suburb every day. No marketing budget needed — your profile does the work.'],
                    ['bi-cash-stack',          'Reliable payouts',       'Learners pay upfront via card or wallet. You get weekly direct-deposit payouts with full transaction history.'],
                    ['bi-shield-fill-check',   'Verified & trusted',     'WWCC, licence and insurance checks build instant trust with learners. Verified badge on every profile.'],
                    ['bi-phone-fill',          'Mobile-first dashboard', 'Manage bookings, message learners and check earnings from anywhere. Works flawlessly on phone, tablet or desktop.'],
                    ['bi-star-fill',           'Build your reputation',  'Collect verified reviews from every learner. Higher ratings = top of search results = more bookings.'],
                ];
            @endphp
            @foreach($benefits as [$ic, $title, $desc])
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

{{-- ─────────── EARNINGS CALCULATOR ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="blog-eyebrow"><i class="bi bi-calculator me-1"></i>Earnings potential</span>
                <h2 class="cl-section-title mb-3">See what you could earn</h2>
                <p class="text-muted mb-4">Set your own hourly rate. Most instructors charge between $65 – $85/hr. Adjust the sliders to see your potential weekly &amp; monthly earnings.</p>

                <div class="iwu-calc">
                    <div class="iwu-calc-field">
                        <label class="form-label fw-bold d-flex justify-content-between">
                            <span>Lessons per week</span>
                            <span class="text-warning-emphasis"><strong id="iwu-hours-out">20</strong> hours</span>
                        </label>
                        <input type="range" min="5" max="50" value="20" step="1" id="iwu-hours" class="form-range">
                    </div>
                    <div class="iwu-calc-field">
                        <label class="form-label fw-bold d-flex justify-content-between">
                            <span>Your hourly rate</span>
                            <span class="text-warning-emphasis">$<strong id="iwu-rate-out">75</strong>/hr</span>
                        </label>
                        <input type="range" min="50" max="120" value="75" step="5" id="iwu-rate" class="form-range">
                    </div>
                    <div class="iwu-calc-result">
                        <div>
                            <span class="iwu-calc-label">Per week</span>
                            <span class="iwu-calc-num">$<span id="iwu-week-out">1,500</span></span>
                        </div>
                        <div>
                            <span class="iwu-calc-label">Per month</span>
                            <span class="iwu-calc-num">$<span id="iwu-month-out">6,000</span></span>
                        </div>
                        <div>
                            <span class="iwu-calc-label">Per year</span>
                            <span class="iwu-calc-num">$<span id="iwu-year-out">78,000</span></span>
                        </div>
                    </div>
                    <p class="text-muted small mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Earnings estimates only. Actual earnings depend on demand in your area, lesson length and your availability.</p>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <svg class="iwu-illu" viewBox="0 0 400 320" aria-hidden="true">
                    {{-- Background gradient circle --}}
                    <circle cx="200" cy="160" r="140" fill="#fef3c7"/>
                    {{-- Coins stack --}}
                    <g transform="translate(120, 180)">
                        <ellipse cx="40" cy="80" rx="50" ry="12" fill="#1f2937" opacity="0.1"/>
                        <ellipse cx="40" cy="68" rx="40" ry="10" fill="#fbbf24" stroke="#d97706" stroke-width="2"/>
                        <rect x="0" y="58" width="80" height="10" fill="#fbbf24"/>
                        <line x1="0" y1="58" x2="0" y2="68" stroke="#d97706" stroke-width="2"/>
                        <line x1="80" y1="58" x2="80" y2="68" stroke="#d97706" stroke-width="2"/>
                        <ellipse cx="40" cy="58" rx="40" ry="10" fill="#fcd34d" stroke="#d97706" stroke-width="2"/>

                        <ellipse cx="40" cy="42" rx="42" ry="11" fill="#fbbf24" stroke="#d97706" stroke-width="2"/>
                        <rect x="-2" y="30" width="84" height="12" fill="#fbbf24"/>
                        <line x1="-2" y1="30" x2="-2" y2="42" stroke="#d97706" stroke-width="2"/>
                        <line x1="82" y1="30" x2="82" y2="42" stroke="#d97706" stroke-width="2"/>
                        <ellipse cx="40" cy="30" rx="42" ry="11" fill="#fcd34d" stroke="#d97706" stroke-width="2"/>

                        <ellipse cx="40" cy="10" rx="44" ry="12" fill="#fbbf24" stroke="#d97706" stroke-width="2"/>
                        <rect x="-4" y="-2" width="88" height="12" fill="#fbbf24"/>
                        <line x1="-4" y1="-2" x2="-4" y2="10" stroke="#d97706" stroke-width="2"/>
                        <line x1="84" y1="-2" x2="84" y2="10" stroke="#d97706" stroke-width="2"/>
                        <ellipse cx="40" cy="-2" rx="44" ry="12" fill="#fcd34d" stroke="#d97706" stroke-width="2"/>
                        <text x="40" y="2" font-size="16" font-weight="900" fill="#92400e" text-anchor="middle">$</text>
                    </g>
                    {{-- Growth arrow --}}
                    <path d="M 80 220 L 180 160 L 240 180 L 320 100" fill="none" stroke="#059669" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                    <polygon points="310,95 325,100 318,113" fill="#059669"/>
                    {{-- Dots on arrow --}}
                    <circle cx="80" cy="220" r="6" fill="#059669"/>
                    <circle cx="180" cy="160" r="6" fill="#059669"/>
                    <circle cx="240" cy="180" r="6" fill="#059669"/>
                </svg>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── HOW IT WORKS ─────────── --}}
<section class="py-5 bg-light" id="how-it-works">
    <div class="container">
        <div class="text-center mb-5">
            <span class="blog-eyebrow"><i class="bi bi-list-check me-1"></i>Get started</span>
            <h2 class="cl-section-title">How it works — from application to first booking</h2>
            <p class="text-muted">Most approved instructors get their first paid booking within 7 days of going live.</p>
        </div>
        <div class="row g-4 iwu-steps">
            @foreach([
                ['1', 'bi-file-earmark-person-fill', 'Submit application', 'Tell us about yourself and upload your licence, instructor certificate and insurance — no account created yet.'],
                ['2', 'bi-patch-check-fill',        'We verify (2 days)', 'Our team reviews your documents against Australian driving instructor standards.'],
                ['3', 'bi-key-fill',                'Set up your profile','Once approved, we email you a one-click link to set your password and finalise your bio, pricing and availability.'],
                ['4', 'bi-car-front-fill',          'Start teaching',     'Learners book you online, you teach. Get paid weekly via direct deposit.'],
            ] as [$num, $ic, $title, $desc])
                <div class="col-md-6 col-lg-3 text-center">
                    <div class="iwu-step-num">{{ $num }}</div>
                    <div class="iwu-step-icon"><i class="bi {{ $ic }}"></i></div>
                    <h3 class="iwu-step-title">{{ $title }}</h3>
                    <p class="text-muted small">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('instructor-application.show') }}" class="btn btn-warning fw-bold btn-lg px-4">
                <i class="bi bi-arrow-right me-1"></i>Start your application
            </a>
        </div>
    </div>
</section>

{{-- ─────────── TESTIMONIALS ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <span class="blog-eyebrow"><i class="bi bi-chat-quote-fill me-1"></i>Real instructors</span>
            <h2 class="cl-section-title">What our instructors say</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Mark',    'Sydney, NSW',     "I went from 12 lessons a week to 35 in three months. The platform handles all the booking admin so I just turn up and teach. Best decision I made for my driving school."],
                ['Priya',   'Melbourne, VIC',  "The verified badge and reviews built trust fast. New learners contact me daily. I love that I set my own rate and hours — total freedom."],
                ['Daniel',  'Brisbane, QLD',   "Payouts are reliable every week, learners pay upfront so no chasing money. Customer support actually picks up the phone. 10/10."],
                ['Amelia',  'Perth, WA',       "I started part-time alongside my day job. Within six months I went full-time. The mobile app makes managing everything so easy."],
            ] as [$name, $place, $text])
                <div class="col-md-6 col-lg-3">
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
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <span class="blog-eyebrow"><i class="bi bi-question-circle me-1"></i>FAQs</span>
                <h2 class="cl-section-title mb-3">Common questions</h2>
                <p class="text-muted small mb-3">Got something specific to ask? Our instructor success team is here to help.</p>
                <a href="{{ route('contact') }}" class="btn btn-warning fw-bold"><i class="bi bi-headset me-1"></i>Talk to our team</a>
            </div>
            <div class="col-lg-8">
                <div class="accordion ilc-faq" id="iwu-faq">
                    @foreach([
                        ['What does it cost to join?',                          "Joining is completely free. We charge a small commission only on completed lessons — no setup fees, no monthly subscriptions, no hidden costs. If you don't earn, we don't earn."],
                        ['Do I need my own car?',                                'Yes, you need a roadworthy vehicle with dual controls and current driving school insurance. We can recommend trusted suppliers for both if needed.'],
                        ['What documents do I need to get verified?',            "You'll need: a valid driving instructor licence for your state, current Working with Children Check, current driver insurance, plus a recent vehicle registration. We verify everything within 24-48 hours."],
                        ['When do I get paid?',                                  'Learners pay upfront when they book. Your earnings accumulate in your wallet and we send payouts via direct deposit every Tuesday for the previous week.'],
                        ['Can I set my own prices?',                             'Absolutely. You set your own hourly rate, package prices, cancellation policies and discounts. Most instructors charge $65 – $85/hr depending on suburb and experience.'],
                        ['Can I work part-time or only on weekends?',            "Yes — set your availability however you like. Many of our instructors teach part-time around other jobs, evenings only, or weekends only."],
                        ['What happens if a learner cancels?',                   'Your cancellation policy is your call (24-hour, 48-hour, etc.). Late cancellations result in the learner being charged a fee that goes to you.'],
                        ['Can I switch to a different instructor account type?', 'Yes — if you start with us as an individual and later set up a driving school with multiple instructors, we have a school account upgrade path.'],
                    ] as $i => [$q, $a])
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#iwu-faq-{{ $i }}">{{ $q }}</button>
                            </h3>
                            <div id="iwu-faq-{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#iwu-faq">
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
                <h2 class="mb-2 fw-bolder text-dark">Ready to grow your driving school?</h2>
                <p class="mb-0 text-dark">Free to apply. We review your documents within 2 business days.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('instructor-application.show') }}" class="btn btn-dark fw-bold btn-lg px-4">
                    <i class="bi bi-file-earmark-person-fill me-2"></i>Apply to teach
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
(function(){
    var h = document.getElementById('iwu-hours');
    var r = document.getElementById('iwu-rate');
    if (!h || !r) return;

    function fmt(n) { return n.toLocaleString('en-AU'); }
    function update() {
        var hours = parseInt(h.value, 10);
        var rate  = parseInt(r.value, 10);
        var week  = hours * rate;
        var month = week * 4;
        var year  = week * 52;
        document.getElementById('iwu-hours-out').textContent = hours;
        document.getElementById('iwu-rate-out').textContent  = rate;
        document.getElementById('iwu-week-out').textContent  = fmt(week);
        document.getElementById('iwu-month-out').textContent = fmt(month);
        document.getElementById('iwu-year-out').textContent  = fmt(year);
    }
    h.addEventListener('input', update);
    r.addEventListener('input', update);
    update();
})();
</script>
@endpush
