<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Driving School | Driving Lessons | Book Learners Driving Test Online') – Secure Licences</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
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
</head>
<body>
    {{-- Top bar: left = Support | Instruct with Secure Licences | Secure Licences Instructor Academy; right = Learner Login | Instructor Login (or Dashboard when logged in) --}}
    <div class="frontend-topbar py-2">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap align-items-center gap-1">
                    <a href="{{ route('support') }}">Support</a>
                    <span class="divider">|</span>
                    <a href="{{ route('instruct-with-us') }}">Instruct with Secure Licences</a>
                    <span class="divider">|</span>
                    <a href="{{ route('instructor-academy') }}">Secure Licences Instructor Academy</a>
                    <span class="divider">|</span>
                    <a href="{{ route('services.categories') }}">Find Services</a>
                    <span class="divider">|</span>
                    <a href="{{ route('services.become-provider') }}">Become a Provider</a>
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
    <header class="frontend-header">
        <nav class="navbar navbar-expand-lg navbar-light py-2 py-lg-3">
            <div class="container">
                <a class="navbar-brand logo" href="{{ url('/') }}">
                    <span class="ez">Secure</span><span class="ez-l">L</span><span class="icence">icences</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#frontendNav" aria-controls="frontendNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
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
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navLocations" role="button" data-bs-toggle="dropdown" aria-expanded="false">Locations</a>
                            <ul class="dropdown-menu" aria-labelledby="navLocations">
                                <li><a class="dropdown-item" href="{{ route('find-instructor') }}?q=Sydney">Sydney Driving Lessons</a></li>
                                <li><a class="dropdown-item" href="{{ route('find-instructor') }}?q=Melbourne">Melbourne Driving Lessons</a></li>
                                <li><a class="dropdown-item" href="{{ route('find-instructor') }}?q=Brisbane">Brisbane Driving Lessons</a></li>
                                <li><a class="dropdown-item" href="{{ route('find-instructor') }}?q=Perth">Perth Driving Lessons</a></li>
                                <li><a class="dropdown-item" href="{{ route('find-instructor') }}?q=Adelaide">Adelaide Driving Lessons</a></li>
                                <li><a class="dropdown-item" href="{{ route('find-instructor') }}?q=Hobart">Hobart Driving Lessons</a></li>
                                <li><a class="dropdown-item" href="{{ route('find-instructor') }}?q=Canberra">Canberra Driving Lessons</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('prices-packages') }}">Prices &amp; Packages</a>
                        </li>
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
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navResources" role="button" data-bs-toggle="dropdown" aria-expanded="false">Free Learner Resources</a>
                            <ul class="dropdown-menu" aria-labelledby="navResources">
                                <li><a class="dropdown-item" href="{{ url('/') }}#faqAccordion">FAQs</a></li>
                                <li><a class="dropdown-item" href="{{ route('blog.index') }}">Blog</a></li>
                                <li><a class="dropdown-item" href="{{ route('industry-insights') }}">Industry Insights</a></li>
                                <li><a class="dropdown-item" href="{{ route('practice-test') }}">Free Practice Learners Test</a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                        {{-- No user dropdown on frontend; Dashboard link is in top bar when logged in --}}
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <form id="frontend-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <main>
        @yield('content')
    </main>

    <footer class="frontend-footer-new">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4 col-md-12">
                    <a class="d-inline-flex align-items-center mb-3 text-decoration-none" href="{{ url('/') }}">
                        <span style="font-size:1.5rem;font-weight:800;color:#fff;letter-spacing:-0.02em;">Secure</span><span style="width:30px;height:30px;background:var(--sl-accent-500);color:var(--sl-gray-900);font-weight:800;display:inline-flex;align-items:center;justify-content:center;margin:0 2px;font-size:1.1rem;border-radius:6px;">L</span><span style="font-size:1.5rem;font-weight:800;color:#fff;letter-spacing:-0.02em;">icences</span>
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
                        <li><a href="{{ route('instructor-academy') }}">Instructor Academy</a></li>
                        <li><a href="{{ route('instructor.login') }}">Instructor Login</a></li>
                        <li><a href="{{ route('policies.instructor-conduct') }}">Code of Conduct</a></li>
                    </ul>
                    <h6 class="mt-4">Home Services</h6>
                    <ul class="list-unstyled mb-0" style="line-height:2;">
                        <li><a href="{{ route('services.categories') }}">Browse Services</a></li>
                        <li><a href="{{ route('services.become-provider') }}">Become a Provider</a></li>
                    </ul>
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
                        <li><a href="{{ route('support') }}">Support</a></li>
                        <li><a href="{{ route('terms') }}">Terms</a></li>
                        <li><a href="{{ route('privacy') }}">Privacy</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-divider"></div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3" style="font-size:var(--sl-text-xs);color:var(--sl-gray-500);">
                <span>&copy; {{ date('Y') }} Secure Licences Pty Ltd. All rights reserved.</span>
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

    @stack('scripts')
</body>
</html>
