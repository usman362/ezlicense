@extends('layouts.frontend')
@section('title', 'International Driver\'s Licence Conversions')

@section('content')
{{-- ─────────── HERO: dark navy with suburb search ─────────── --}}
<section class="ilc-hero">
    <div class="container ilc-hero-inner text-center">
        <h1 class="ilc-hero-title">International Driver's Licence Conversions</h1>
        <p class="ilc-hero-sub">Prepare to pass your driving test at the first attempt</p>
        <form action="{{ route('find-instructor.results') }}" method="get" class="ilc-search-form mt-4">
            <input type="hidden" name="suburb_id" id="ilc-hero-suburb-id">
            <input type="hidden" name="q" id="ilc-hero-q">
            <div class="dtp-form-row justify-content-center">
                <div class="btn-group dtp-trans-toggle" role="group">
                    <input type="radio" class="btn-check" name="transmission" id="ilc-auto" value="auto" checked>
                    <label class="btn" for="ilc-auto"><i class="bi bi-check-lg me-1"></i>Auto</label>
                    <input type="radio" class="btn-check" name="transmission" id="ilc-manual" value="manual">
                    <label class="btn" for="ilc-manual">Manual</label>
                </div>
                <input type="text" id="ilc-hero-suburb" class="form-control dtp-suburb-input" placeholder="Enter your suburb" autocomplete="off">
                <button type="submit" class="btn btn-warning fw-bold dtp-search-btn"><i class="bi bi-search me-1"></i>Search</button>
            </div>
        </form>
    </div>
</section>

{{-- ─────────── Bridge: "Book a driving instructor in your area" ─────────── --}}
<section class="ilc-bridge">
    <div class="container">
        <div class="ilc-bridge-arrow"><i class="bi bi-arrow-down-circle-fill"></i></div>
        <div class="row align-items-center g-4 mt-2">
            <div class="col-md-5 text-center">
                {{-- SVG: stylized yellow car with L-plate badge — driving school themed --}}
                <svg viewBox="0 0 320 200" xmlns="http://www.w3.org/2000/svg" class="ilc-bridge-img" aria-hidden="true">
                    <defs><filter id="ilcCarShadow"><feDropShadow dx="0" dy="6" stdDeviation="6" flood-opacity="0.18"/></filter></defs>
                    <ellipse cx="160" cy="180" rx="130" ry="8" fill="#000" opacity="0.1"/>
                    <g filter="url(#ilcCarShadow)" transform="translate(40,40)">
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
                <p class="text-muted mb-0">Driving instructors on Secure Licence assist thousands of international licence holders each year.</p>
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
                    <input type="hidden" name="suburb_id" id="ilc-cta-suburb-id">
                    <input type="hidden" name="q" id="ilc-cta-q">
                    <div class="btn-group dtp-trans-toggle" role="group">
                        <input type="radio" class="btn-check" name="transmission" id="ilc-cta-auto" value="auto" checked>
                        <label class="btn" for="ilc-cta-auto"><i class="bi bi-check-lg me-1"></i>Auto</label>
                        <input type="radio" class="btn-check" name="transmission" id="ilc-cta-manual" value="manual">
                        <label class="btn" for="ilc-cta-manual">Manual</label>
                    </div>
                    <input type="text" id="ilc-cta-suburb" class="form-control" placeholder="Enter your suburb" autocomplete="off">
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

{{-- ─────────── How Secure Licence works ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="ilc-section-title">How Secure Licence works</h2>
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
                        <p>Book online with instant confirmation, easily manage your lesson schedule via our online dashboard.</p>
                    </div>
                </div>
                <div class="ilc-step">
                    <div class="ilc-step-num">3</div>
                    <div>
                        <h3>Learn to Drive</h3>
                        <p>Your instructor picks you up from your chosen address and you're on your way to becoming a licensed driver.</p>
                    </div>
                </div>
                <a href="{{ route('find-instructor') }}" class="btn btn-warning fw-bolder px-4 mt-3"><i class="bi bi-arrow-right-circle-fill me-1"></i>Start learning to drive now ›</a>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── Convert your international licence ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="ilc-section-title">Convert Your International Licence</h2>
        </div>
        <div class="row g-4 align-items-center">
            <div class="col-md-5 text-center">
                <div class="ilc-convert-illu">
                    <i class="bi bi-globe2"></i>
                    <i class="bi bi-arrow-right-circle-fill ilc-convert-arrow"></i>
                    <i class="bi bi-car-front-fill"></i>
                </div>
            </div>
            <div class="col-md-7">
                <ul class="ilc-checklist">
                    <li><i class="bi bi-check-circle-fill"></i>Driving lessons will prepare you for the driving test</li>
                    <li><i class="bi bi-check-circle-fill"></i>Choose your pick up location, your chosen instructor will arrive at this address</li>
                    <li><i class="bi bi-check-circle-fill"></i>Driving test packages available</li>
                </ul>
                <a href="{{ route('find-instructor') }}" class="btn btn-warning fw-bolder px-4 mt-3"><i class="bi bi-arrow-right-circle-fill me-1"></i>Find your driving instructor now ›</a>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── Converting your licence — state-specific (tabs) ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="ilc-section-title">Converting your international drivers licence to an Australian licence</h2>
            <p class="text-muted">International drivers licence holders are generally able to drive on Australian roads for a period of time. Your visa or residency status will determine how long you can use your international drivers licence for and whether or not you need to apply to convert your international drivers licence to an Australian drivers licence. The rules and conversion processes are different in each state of Australia.</p>
            <p class="text-muted small">Please select your state from the tabs below to learn more about the rules and conversion steps.</p>

            <ul class="nav nav-pills justify-content-center ilc-state-tabs mt-3" id="ilc-state-tabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-nsw" type="button">NSW</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-vic" type="button">VIC</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-qld" type="button">QLD</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-wa" type="button">WA</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-sa" type="button">SA</button></li>
            </ul>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-nsw">
                <div class="row g-4 align-items-start mt-3">
                    <div class="col-md-4 text-center">
                        <div class="ilc-traffic-illu">
                            <span class="ilc-tl-red"></span>
                            <span class="ilc-tl-yel"></span>
                            <span class="ilc-tl-grn"></span>
                        </div>
                        <h4 class="fw-bold mt-3">How do I get started?</h4>
                        <p class="text-muted small">The process for converting your licence is easy and only takes a few steps.</p>
                    </div>
                    <div class="col-md-8">
                        <h3 class="fw-bolder mb-4">Converting your international drivers licence to a NSW licence</h3>

                        <div class="ilc-nswstep">
                            <div class="ilc-nswstep-head"><span class="ilc-nswstep-dot">1</span><strong>Have your documents and ID ready</strong></div>
                            <ul class="ilc-nswstep-body">
                                <li><i class="bi bi-check2"></i>Your overseas licence (and a translation if not in English)</li>
                                <li><i class="bi bi-check2"></i>Your proof of Australian permanent residency or NSW</li>
                                <li><i class="bi bi-check2"></i>Two pieces of documentation to prove your identity as required by the NSW Government</li>
                            </ul>
                        </div>

                        <div class="ilc-nswstep">
                            <div class="ilc-nswstep-head"><span class="ilc-nswstep-dot">2</span><strong>Pass an eyesight test</strong></div>
                            <div class="ilc-nswstep-body small text-muted">All drivers must meet eyesight standards for driving, and even if you have previous experience of driving overseas you will be required to undergo an eyesight test to prove that you meet the Australian standards.</div>
                        </div>

                        <div class="ilc-nswstep">
                            <div class="ilc-nswstep-head"><span class="ilc-nswstep-dot">3</span><strong>Complete a medical test (if required)</strong></div>
                            <div class="ilc-nswstep-body small text-muted">If you have a medical condition that may impact your ability to drive, you may be required to complete a medical test to assess your fitness to drive.</div>
                        </div>

                        <div class="ilc-nswstep">
                            <div class="ilc-nswstep-head"><span class="ilc-nswstep-dot">4</span><strong>Pass a Driver knowledge test</strong></div>
                            <div class="ilc-nswstep-body small text-muted">You may be required to pass a Driver Knowledge Test (DKT) as part of the process. If you do not convert your licence within 3 months of obtaining your Australian residency, you may be required to undertake the test as part of gaining your Australian customer permit. We have a resource available for you to <a href="{{ route('practice-test') }}">practice the DKT</a> for free.</div>
                        </div>

                        <div class="ilc-nswstep">
                            <div class="ilc-nswstep-head"><span class="ilc-nswstep-dot">5</span><strong>Pass a driving test</strong></div>
                            <div class="ilc-nswstep-body small text-muted">You may be required to pass a driving test as part of the process. If you do not convert your licence within 3 months of obtaining your Australian residency, you may also be required to undertake a driving test.</div>
                        </div>

                        <div class="ilc-nswstep">
                            <div class="ilc-nswstep-head"><span class="ilc-nswstep-dot">6</span><strong>Pay the relevant licence fees</strong></div>
                            <div class="ilc-nswstep-body small text-muted">As part of the conversion process you will be required to pay relevant administrative fees each time you sit a test or submit an application.</div>
                        </div>

                        <p class="small text-muted mt-3">For further information please contact your local roads authority or visit Service NSW.</p>
                        <a href="{{ route('find-instructor') }}" class="btn btn-warning fw-bolder px-4 mt-2"><i class="bi bi-arrow-right-circle-fill me-1"></i>Find your driving instructor now ›</a>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-vic"><p class="text-muted text-center py-4">Detailed VIC conversion process — please check Service Victoria for current requirements.</p></div>
            <div class="tab-pane fade" id="tab-qld"><p class="text-muted text-center py-4">Detailed QLD conversion process — please check Department of Transport &amp; Main Roads.</p></div>
            <div class="tab-pane fade" id="tab-wa"><p class="text-muted text-center py-4">Detailed WA conversion process — please check Department of Transport WA.</p></div>
            <div class="tab-pane fade" id="tab-sa"><p class="text-muted text-center py-4">Detailed SA conversion process — please check Service SA.</p></div>
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
                    ['name' => 'Sheraz', 'text' => "Sheraz was so professional and very kind. He always knew what every learner needs to be able to do well, knew what weakness was in the lessons before."],
                    ['name' => 'Shaun', 'text' => "Shaun was excellent at adapting his teaching style. The practical skills he taught me from the perspective of the actions he wants me to take. I'm patient and we both went through a lot of encouragement in the lessons. Pleased."],
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
                <div class="accordion ilc-faq" id="ilc-faq">
                    @php
                        $faqs = [
                            ['q' => 'How Much Do Driving Lessons Cost?', 'a' => 'Driving lesson prices vary by instructor and area. On Secure Licence, lessons typically start from $55–$85/hour. Multi-hour packages save more.'],
                            ['q' => 'Do You Offer Any Special Lessons to Prepare for the Driving Test?', 'a' => 'Yes — our 2.5-hour Driving Test Package includes a pre-test warm-up lesson plus use of the instructor\'s vehicle for the test.'],
                            ['q' => 'How Many Driving Lessons Do I Need?', 'a' => 'Most learners take between 20–50 hours of professional driving lessons. Your instructor will help assess your progress.'],
                            ['q' => 'Can Driving Lessons Count Towards My Logbook Hours?', 'a' => 'Yes — professional driving lessons count as 3-for-1 hours in your NSW logbook (up to 30 logged hours).'],
                            ['q' => 'What if there are no available Driving Instructors in my area?', 'a' => 'Try expanding your search radius or contact our support team — we are continually adding new instructors.'],
                            ['q' => 'Can I take Refresher Driving Lessons?', 'a' => 'Absolutely. Many of our instructors offer refresher lessons for licensed drivers returning to driving.'],
                            ['q' => 'Can I Change Instructors?', 'a' => 'Yes — you can switch instructors any time through your dashboard at no extra cost.'],
                            ['q' => 'Is Secure Licence a Driving School?', 'a' => 'No, we are an online marketplace connecting learners with verified independent driving instructors across Australia.'],
                            ['q' => 'Can I Book Driving Lessons to Learn How to Drive Manual?', 'a' => 'Yes — filter by Manual transmission when searching to find instructors offering manual lessons.'],
                            ['q' => 'Where does Secure Licence offer driving lessons?', 'a' => 'We service 3,700+ suburbs across NSW, VIC, QLD, WA, SA, TAS and ACT.'],
                        ];
                    @endphp
                    @foreach($faqs as $i => $f)
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#ilc-faq-{{ $i }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">{{ $f['q'] }}</button>
                            </h3>
                            <div id="ilc-faq-{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#ilc-faq">
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

{{-- ─────────── Why choose Secure Licence ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="ilc-section-title text-center mb-2">Why choose Secure Licence?</h2>
        <p class="text-muted text-center mb-5">Unlike a typical driving school, Secure Licence is an Australian-first platform that allows learner drivers &amp; parents to find, compare and book verified driving instructors online.</p>

        <div class="row g-4 text-center mb-5 ilc-stats-row">
            <div class="col-md-4">
                <div class="ilc-stat">
                    <div class="ilc-stat-num">1000+</div>
                    <div class="ilc-stat-label">DRIVING INSTRUCTORS</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ilc-stat">
                    <div class="ilc-stat-num">3700+</div>
                    <div class="ilc-stat-label">SUBURBS SERVICED</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ilc-stat">
                    <div class="ilc-stat-num">#1</div>
                    <div class="ilc-stat-label">ONLINE BOOKINGS</div>
                </div>
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

{{-- ─────────── The Secure Licence advantage ─────────── --}}
<section class="py-5">
    <div class="container">
        <h2 class="ilc-section-title text-center mb-4">The Secure Licence advantage</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion ilc-advantage" id="ilc-adv">
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
                                <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#ilc-adv-{{ $i }}">{{ $a['t'] }}</button>
                            </h3>
                            <div id="ilc-adv-{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#ilc-adv">
                                <div class="accordion-body text-muted small">{{ $a['d'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="ilc-faq-callout mt-4">
                    <h4 class="fw-bolder mb-2">How do I find private driving instructors near me?</h4>
                    <p class="text-muted small mb-0">We know what it's like — life gets busy and learning to drive feels overwhelming. Whether you live in Sydney, Melbourne, Brisbane, Adelaide or beyond, Secure Licence makes it easy: search your suburb, browse local verified instructors and book online in under 60 seconds.</p>
                    <p class="text-muted small mt-3 mb-0">Whether you're learning to drive for the first time, brushing up after a few years, or easing yourself back into the driver's seat, our platform connects you with instructors who fit your schedule, vehicle preference and learning style.</p>
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
    attachSuburbAutocomplete('ilc-hero-suburb', 'ilc-hero-suburb-id', 'ilc-hero-q');
    attachSuburbAutocomplete('ilc-cta-suburb', 'ilc-cta-suburb-id', 'ilc-cta-q');
})();
</script>
@endpush
@endsection
