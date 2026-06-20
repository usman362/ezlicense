<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Admin-controlled SEO + social meta + analytics (Google Analytics, Facebook Pixel, etc.) --}}
    @include('partials.meta-tags')

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/apple-touch-icon.svg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; }
        main { flex: 1; }
        /* Layout-specific: logo block */
        .frontend-header .logo {
            font-size: 1.5rem; font-weight: 800; color: var(--sl-gray-900);
            text-decoration: none; display: inline-flex; align-items: center;
            letter-spacing: -0.02em;
        }
        .frontend-header .logo .ez-l {
            width: 34px; height: 34px; background: var(--sl-accent-500);
            color: var(--sl-gray-900); font-weight: 800;
            display: inline-flex; align-items: center; justify-content: center;
            margin: 0 2px; font-size: 1.15rem;
            border-radius: 6px;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Top utility bar — DESKTOP ONLY (mobile uses hamburger menu for these links) --}}
    <div class="frontend-topbar py-2 d-none d-lg-block">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap align-items-center gap-1">
                    <a href="{{ route('support.home') }}">Support</a>
                    <span class="divider">|</span>
                    <a href="{{ route('instruct-with-us') }}">Instruct with Secure Licence</a>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-1">
                    @auth
                        @if(Auth::user()->isLearner())
                            <a href="{{ route('learner.dashboard') }}">Dashboard</a>
                        @elseif(Auth::user()->isInstructor())
                            <a href="{{ route('instructor.dashboard') }}">Dashboard</a>
                        @else
                            <a href="{{ route('home') }}">Dashboard</a>
                        @endif
                    @else
                        <a href="{{ route('learner.login') }}">Learner Login</a>
                        <span class="divider">|</span>
                        <a href="{{ route('instructor.login') }}">Instructor Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
    <style>
        /* ── "For Instructors" mega-menu ── */
        .dropdown-mega {
            width: min(580px, 94vw);
            border: 0;
            box-shadow: 0 14px 44px rgba(20, 23, 28, .14);
            border-radius: .85rem;
            margin-top: .6rem;
            padding: 0;
            overflow: hidden;
        }
        .dropdown-mega-inner {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem 1.75rem;
            padding: 1.35rem 1.6rem 1rem;
        }
        .dropdown-mega-head {
            font-size: .7rem;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: #9aa1ab;
            font-weight: 700;
            margin-bottom: .55rem;
        }
        .dropdown-mega-link {
            display: flex;
            align-items: center;
            gap: .55rem;
            padding: .38rem 0;
            color: #2b2f36;
            text-decoration: none;
            font-size: .94rem;
            line-height: 1.2;
            transition: color .12s ease;
        }
        .dropdown-mega-link i { color: #c9a400; font-size: 1rem; width: 1.15rem; text-align: center; }
        .dropdown-mega-link:hover { color: #1a1d21; }
        .dropdown-mega-link:hover i { color: #b38f00; }
        .dropdown-mega-foot {
            display: flex;
            gap: .6rem;
            align-items: center;
            padding: .9rem 1.6rem;
            background: #f8f9fa;
            border-top: 1px solid #edeff2;
        }
        .dropdown-mega-foot .ms-auto-note { margin-left: auto; font-size: .82rem; color: #8b929c; }
        @media (max-width: 991.98px) {
            .dropdown-mega { width: 100%; box-shadow: none; margin-top: .25rem; }
            .dropdown-mega-inner { grid-template-columns: 1fr; gap: .35rem 0; padding: .75rem 1rem .5rem; }
            .dropdown-mega-foot { flex-wrap: wrap; padding: .75rem 1rem; }
            .dropdown-mega-foot .ms-auto-note { display: none; }
        }
    </style>
    <header class="frontend-header">
        <nav class="navbar navbar-expand-lg navbar-light py-2 py-lg-3">
            <div class="container">
                <a class="navbar-brand logo" href="{{ url('/') }}">
                    <span class="ez">Secure</span><span class="ez-l">L</span><span class="icence">icence</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#frontendNav" aria-controls="frontendNav" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-list nav-icon-open"></i>
                    <i class="bi bi-x-lg nav-icon-close"></i>
                </button>
                <div class="collapse navbar-collapse" id="frontendNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navDrivingLessons" role="button" data-bs-toggle="dropdown" aria-expanded="false">Driving Lessons</a>
                            <ul class="dropdown-menu" aria-labelledby="navDrivingLessons">
                                <li><a class="dropdown-item" href="{{ route('driving-test-packages') }}">Driving Test Packages</a></li>
                                <li><a class="dropdown-item" href="{{ route('international-licence') }}">International Licence Conversions</a></li>
                                <li><a class="dropdown-item" href="{{ route('refresher-lessons') }}">Refresher Lessons</a></li>
                                <li><a class="dropdown-item" href="{{ route('gift-vouchers') }}">Gift Vouchers</a></li>
                                <li><a class="dropdown-item" href="{{ route('prices-packages') }}">Prices &amp; Packages</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navLocations" role="button" data-bs-toggle="dropdown" aria-expanded="false">Driving Lesson Locations</a>
                            <ul class="dropdown-menu" aria-labelledby="navLocations">
                                <li><a class="dropdown-item" href="{{ route('city.landing', 'sydney') }}">Sydney Driving Lessons</a></li>
                                <li><a class="dropdown-item" href="{{ route('city.landing', 'melbourne') }}">Melbourne Driving Lessons</a></li>
                                <li><a class="dropdown-item" href="{{ route('city.landing', 'brisbane') }}">Brisbane Driving Lessons</a></li>
                                <li><a class="dropdown-item" href="{{ route('city.landing', 'perth') }}">Perth Driving Lessons</a></li>
                                <li><a class="dropdown-item" href="{{ route('city.landing', 'adelaide') }}">Adelaide Driving Lessons</a></li>
                                <li><a class="dropdown-item" href="{{ route('city.landing', 'hobart') }}">Hobart Driving Lessons</a></li>
                                <li><a class="dropdown-item" href="{{ route('city.landing', 'canberra') }}">Canberra Driving Lessons</a></li>
                            </ul>
                        </li>
                        {{-- "For Instructors" mega-menu — EzLicence-style, two feature columns. --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navForInstructors" role="button" data-bs-toggle="dropdown" aria-expanded="false">For Instructors</a>
                            <div class="dropdown-menu dropdown-mega" aria-labelledby="navForInstructors">
                                <div class="dropdown-mega-inner">
                                    <div class="dropdown-mega-col">
                                        <a class="dropdown-mega-link" href="{{ route('for-instructors.lead-generation') }}">Lead Generation</a>
                                        <a class="dropdown-mega-link" href="{{ route('for-instructors.work-whenever-you-want') }}">Work Whenever You Want</a>
                                        <a class="dropdown-mega-link" href="{{ route('for-instructors.flexible-commitment') }}">Flexible Commitment</a>
                                        <a class="dropdown-mega-link" href="{{ route('for-instructors.your-listing-profile') }}">Your Listing &amp; Profile</a>
                                        <a class="dropdown-mega-link" href="{{ route('for-instructors.reputation-management') }}">Reputation Management</a>
                                        <a class="dropdown-mega-link" href="{{ route('for-instructors.white-glove-concierge') }}">White-Glove Concierge</a>
                                        <a class="dropdown-mega-link" href="{{ route('for-instructors.tools-you-already-know') }}">Tools You Already Know</a>
                                    </div>
                                    <div class="dropdown-mega-col">
                                        <a class="dropdown-mega-link" href="{{ route('for-instructors.calendar-scheduling') }}">Calendar &amp; Scheduling</a>
                                        <a class="dropdown-mega-link" href="{{ route('for-instructors.payments-payouts') }}">Payments &amp; Payouts</a>
                                        <a class="dropdown-mega-link" href="{{ route('for-instructors.automated-reminders') }}">Automated Reminders</a>
                                        <a class="dropdown-mega-link" href="{{ route('for-instructors.no-show-protection') }}">No-Show Protection</a>
                                        <a class="dropdown-mega-link" href="{{ route('for-instructors.lesson-catalog') }}">Lesson Catalog</a>
                                        <a class="dropdown-mega-link" href="{{ route('for-instructors.learner-management') }}">Learner Management</a>
                                        <a class="dropdown-mega-link" href="{{ route('for-instructors.website-booking-link') }}">Website + Booking Link</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        {{-- DISABLED for Phase 1 launch — Home Services dropdown
                             Re-enable when Service Providers feature is ready.
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navServices" role="button" data-bs-toggle="dropdown" aria-expanded="false">Home Services</a>
                            <ul class="dropdown-menu" aria-labelledby="navServices">
                                <li><a class="dropdown-item" href="{{ route('services.categories') }}">Browse all categories</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @foreach(\App\Models\ServiceCategory::active()->orderBy('display_order')->orderBy('name')->limit(8)->get() as $navCat)
                                    <li><a class="dropdown-item" href="{{ route('services.browse', $navCat->slug) }}">{{ $navCat->name }}</a></li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item fw-semibold text-success" href="{{ route('services.become-provider') }}">Become a Provider →</a></li>
                            </ul>
                        </li>
                        --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navResources" role="button" data-bs-toggle="dropdown" aria-expanded="false">Free Learner Resources</a>
                            <ul class="dropdown-menu" aria-labelledby="navResources">
                                <li><a class="dropdown-item" href="{{ route('faqs.index') }}">FAQs</a></li>
                                <li><a class="dropdown-item" href="{{ route('blog.index') }}">Blog</a></li>
                                <li><a class="dropdown-item" href="{{ route('industry-insights') }}">Industry Insights</a></li>
                                <li><a class="dropdown-item" href="{{ route('industry-insights.newsletter') }}">Industry Insights Newsletter</a></li>
                                <li><a class="dropdown-item" href="{{ route('practice-test') }}">Free Practice Learners Test</a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-2 book-online-wrap d-lg-none">
                        {{-- "Book Online" CTA — mobile menu only (hidden on desktop) --}}
                        <li class="nav-item">
                            <a class="btn btn-warning fw-bold px-3 py-2 d-flex align-items-center justify-content-center gap-1" href="{{ route('find-instructor') }}" style="border-radius: 8px;">
                                Book Online <i class="bi bi-chevron-right small"></i>
                            </a>
                        </li>
                    </ul>

                    {{-- MOBILE ONLY footer section: Dashboard/Login + Support/Contact --}}
                    <ul class="navbar-nav d-lg-none mobile-footer-nav mt-2">
                        @auth
                            @php
                                $dashUrl = Auth::user()->isLearner()
                                    ? route('learner.dashboard')
                                    : (Auth::user()->isInstructor() ? route('instructor.dashboard') : route('home'));
                            @endphp
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center justify-content-between" href="{{ $dashUrl }}">
                                    <span><i class="bi bi-speedometer2 me-2"></i>Dashboard</span>
                                    <i class="bi bi-chevron-right small text-muted"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="#" onclick="event.preventDefault(); document.getElementById('frontend-logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i>Log out
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('learner.login') }}">
                                    <i class="bi bi-person me-2"></i>Learner Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('instructor.login') }}">
                                    <i class="bi bi-person-badge me-2"></i>Instructor Login
                                </a>
                            </li>
                        @endauth
                        <li class="nav-item"><hr class="my-1" style="opacity:0.1;"></li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('support.home') }}">
                                <i class="bi bi-life-preserver me-2"></i>Support
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('contact') }}">
                                <i class="bi bi-envelope me-2"></i>Contact
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <form id="frontend-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    {{-- Old single-link floating help button removed — replaced by the richer
         sl-help-widget popover further below. --}}

    <main>
        @yield('content')
    </main>

    <footer class="frontend-footer-new">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4 col-md-12">
                    <a class="d-inline-flex align-items-center mb-3 text-decoration-none" href="{{ url('/') }}">
                        <span style="font-size:1.5rem;font-weight:800;color:#fff;letter-spacing:-0.02em;">Secure</span><span style="width:30px;height:30px;background:var(--sl-accent-500);color:var(--sl-gray-900);font-weight:800;display:inline-flex;align-items:center;justify-content:center;margin:0 2px;font-size:1.1rem;border-radius:6px;">L</span><span style="font-size:1.5rem;font-weight:800;color:#fff;letter-spacing:-0.02em;">icence</span>
                    </a>
                    <p style="color:var(--sl-gray-400);font-size:var(--sl-text-sm);line-height:1.6;">
                        <strong style="color:var(--sl-gray-200);">Australia's trusted driving school marketplace.</strong>
                        Find, compare and book verified instructors in minutes — with real reviews, transparent pricing, and zero hassle.
                    </p>
                    <div class="d-flex gap-2 mt-4">
                        <a href="#" class="social-link" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-link" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-link" title="Twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="social-link" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <h6>Learn to Drive</h6>
                    <ul class="list-unstyled mb-0" style="line-height:2;">
                        <li><a href="{{ route('find-instructor') }}">Driving Lessons</a></li>
                        <li><a href="{{ route('find-instructor') }}">Test Packages</a></li>
                        <li><a href="{{ route('gift-vouchers') }}">Gift Vouchers</a></li>
                        <li><a href="{{ route('refresher-lessons') }}">Refresher Lessons</a></li>
                        <li><a href="{{ route('international-licence') }}">Licence Conversions</a></li>
                        <li><a href="{{ route('practice-test') }}">Free Practice Test</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <h6>For Instructors</h6>
                    <ul class="list-unstyled mb-0" style="line-height:2;">
                        <li><a href="{{ route('instruct-with-us') }}">Become an Instructor</a></li>
                        <li><a href="{{ route('instructor.login') }}">Instructor Login</a></li>
                        <li><a href="{{ route('policies.instructor-conduct') }}">Code of Conduct</a></li>
                    </ul>
                    {{-- DISABLED for Phase 1 launch — Home Services footer block
                    <h6 class="mt-4">Home Services</h6>
                    <ul class="list-unstyled mb-0" style="line-height:2;">
                        <li><a href="{{ route('services.categories') }}">Browse Services</a></li>
                        <li><a href="{{ route('services.become-provider') }}">Become a Provider</a></li>
                    </ul>
                    --}}
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <h6>Policies</h6>
                    <ul class="list-unstyled mb-0" style="line-height:2;">
                        <li><a href="{{ route('policies.index') }}">All Policies</a></li>
                        <li><a href="{{ route('policies.complaint-handling') }}">Complaint Handling</a></li>
                        <li><a href="{{ route('policies.refund-cancellation') }}">Refunds & Cancellation</a></li>
                        <li><a href="{{ route('policies.safety') }}">Safety Policy</a></li>
                        <li><a href="{{ route('policies.dispute-resolution') }}">Dispute Resolution</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 col-6">
                    <h6>Company</h6>
                    <ul class="list-unstyled mb-0" style="line-height:2;">
                        <li><a href="{{ route('about') }}">About</a></li>
                        <li><a href="{{ route('blog.index') }}">Blog</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                        <li><a href="{{ route('support.home') }}">Support</a></li>
                        <li><a href="{{ route('terms') }}">Terms</a></li>
                        <li><a href="{{ route('privacy') }}">Privacy</a></li>
                    </ul>
                </div>
            </div>

            {{-- NEW: Extra footer rows — matches EzLicence reference (state/city specific links + state practice tests) --}}
            <div class="row g-4 mb-4 pt-3" style="border-top: 1px solid rgba(255,255,255,0.08);">
                <div class="col-lg-4 col-md-12 col-12">
                    <h6>Learner Tests Online</h6>
                    <ul class="list-unstyled mb-0" style="line-height:2;">
                        <li><a href="{{ route('practice-test') }}">FREE Practice Learners Test</a></li>
                        <li><a href="{{ route('practice-test') }}?state=NSW">NSW Driver Knowledge Test</a></li>
                        <li><a href="{{ route('practice-test') }}?state=VIC">VIC Learner Permit Knowledge Test</a></li>
                        <li><a href="{{ route('practice-test') }}?state=QLD">QLD Road Rules Test</a></li>
                        <li><a href="{{ route('practice-test') }}?state=WA">WA Road Rules Theory Test</a></li>
                        <li><a href="{{ route('practice-test') }}?state=SA">SA Learner Theory Test</a></li>
                        <li><a href="{{ route('practice-test') }}?state=TAS">TAS Driver Knowledge Test</a></li>
                        <li><a href="{{ route('practice-test') }}?state=ACT">ACT Road Rules Knowledge Test</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <h6>Driving Instructors by State</h6>
                    <ul class="list-unstyled mb-0" style="line-height:2;">
                        <li><a href="{{ route('find-instructor') }}?q=NSW">NSW Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=VIC">VIC Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=QLD">QLD Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=WA">WA Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=SA">SA Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=TAS">TAS Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=ACT">ACT Driving Instructors</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <h6>Driving Instructors by City</h6>
                    <ul class="list-unstyled mb-0" style="line-height:2;">
                        <li><a href="{{ route('city.landing', 'sydney') }}">Sydney Driving Instructors</a></li>
                        <li><a href="{{ route('city.landing', 'melbourne') }}">Melbourne Driving Instructors</a></li>
                        <li><a href="{{ route('city.landing', 'brisbane') }}">Brisbane Driving Instructors</a></li>
                        <li><a href="{{ route('city.landing', 'perth') }}">Perth Driving Instructors</a></li>
                        <li><a href="{{ route('city.landing', 'adelaide') }}">Adelaide Driving Instructors</a></li>
                        <li><a href="{{ route('city.landing', 'hobart') }}">Hobart Driving Instructors</a></li>
                        <li><a href="{{ route('city.landing', 'canberra') }}">Canberra Driving Instructors</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-divider"></div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3" style="font-size:var(--sl-text-xs);color:var(--sl-gray-500);">
                <span>&copy; {{ date('Y') }} Secure Licence Pty Ltd. All rights reserved.</span>
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <span><i class="bi bi-geo-alt me-1"></i>Australia</span>
                    <span>·</span>
                    <span><i class="bi bi-shield-check me-1"></i>ABN Verified</span>
                    <span>·</span>
                    <span><i class="bi bi-lock me-1"></i>SSL Secured</span>
                </div>
            </div>
        </div>
    </footer>

    {{-- ────────────────────────────────────────────────────────────────
         Floating "Help" widget — bottom-right.
         Click → small panel with link to support center + submit-request.
         Hidden on admin/dashboard pages (only on public frontend).
         ──────────────────────────────────────────────────────────────── --}}
    @php
        $supportHome = config('app.support_domain') ? 'https://' . config('app.support_domain') : url('/support');
        $supportRequest = rtrim($supportHome, '/') . '/submit-request';
    @endphp
    <div class="sl-help-widget" id="slHelpWidget">
        <button type="button" class="sl-help-toggle" id="slHelpToggle" aria-label="Open help">
            <i class="bi bi-question-circle-fill"></i>
            <span>Help</span>
        </button>
        <div class="sl-help-panel" id="slHelpPanel" hidden>
            <div class="sl-help-panel-header">
                <strong>How can we help?</strong>
                <button type="button" class="sl-help-close" id="slHelpClose" aria-label="Close">&times;</button>
            </div>
            <div class="sl-help-panel-body">
                <a href="{{ $supportHome }}" class="sl-help-link" target="_blank">
                    <i class="bi bi-search me-2"></i>
                    <div>
                        <strong>Browse Help Articles</strong>
                        <div class="small text-muted">Search 100+ articles · 24/7</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto text-muted"></i>
                </a>
                <a href="{{ $supportRequest }}" class="sl-help-link" target="_blank">
                    <i class="bi bi-envelope-fill me-2"></i>
                    <div>
                        <strong>Submit a Request</strong>
                        <div class="small text-muted">Reply within 1 business day</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto text-muted"></i>
                </a>
            </div>
        </div>
    </div>
    <style>
        .sl-help-widget { position: fixed; bottom: 22px; right: 22px; z-index: 1050; font-family: -apple-system, sans-serif; }
        .sl-help-toggle {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--sl-accent-500, #f59e0b); color: var(--sl-gray-900, #121110);
            border: none; padding: 12px 18px; border-radius: 28px;
            font-weight: 700; box-shadow: 0 6px 20px rgba(0,0,0,.18);
            cursor: pointer; transition: transform .15s, box-shadow .15s;
        }
        .sl-help-toggle:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(0,0,0,.22); }
        .sl-help-toggle i { font-size: 19px; }
        .sl-help-panel {
            position: absolute; bottom: 64px; right: 0;
            width: 320px; max-width: calc(100vw - 28px);
            background: #fff; border-radius: 14px; overflow: hidden;
            box-shadow: 0 12px 36px rgba(0,0,0,.18); border: 1px solid #e5e7eb;
            animation: slHelpFadeIn .18s ease-out;
        }
        @keyframes slHelpFadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .sl-help-panel-header { background: #1a1a1a; color: #fff; padding: 14px 16px; display: flex; justify-content: space-between; align-items: center; }
        .sl-help-close { background: transparent; border: none; color: #fff; font-size: 24px; line-height: 1; cursor: pointer; opacity: .8; }
        .sl-help-close:hover { opacity: 1; }
        .sl-help-panel-body { padding: 6px 0; }
        .sl-help-link { display: flex; align-items: center; padding: 14px 16px; color: #111827; text-decoration: none; border-bottom: 1px solid #f3f4f6; }
        .sl-help-link:last-child { border-bottom: none; }
        .sl-help-link:hover { background: #f9fafb; color: #111827; }
        .sl-help-link i.bi:first-child { font-size: 20px; color: var(--sl-accent-500, #f59e0b); }
        @media (max-width: 480px) { .sl-help-widget { bottom: 14px; right: 14px; } .sl-help-toggle span { display: none; } .sl-help-toggle { padding: 14px; } }
    </style>
    <script>
    (function() {
        const toggle = document.getElementById('slHelpToggle');
        const panel = document.getElementById('slHelpPanel');
        const close = document.getElementById('slHelpClose');
        if (!toggle || !panel) return;
        toggle.addEventListener('click', () => panel.hidden = !panel.hidden);
        close?.addEventListener('click', () => panel.hidden = true);
        document.addEventListener('click', (e) => {
            if (!document.getElementById('slHelpWidget').contains(e.target)) panel.hidden = true;
        });
    })();
    </script>

    @stack('scripts')
</body>
</html>
