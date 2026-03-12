<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Driving School | Driving Lessons | Book Learners Driving Test Online') – EzLicence</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        :root { --ez-accent: #f0ad4e; --ez-accent-dark: #ec971f; --ez-dark: #2c3e50; --ez-topbar: #1b212c; }
        .frontend-topbar { background: var(--ez-topbar); color: rgba(255,255,255,0.9); font-size: 0.875rem; }
        .frontend-topbar a { color: rgba(255,255,255,0.9); text-decoration: none; }
        .frontend-topbar a:hover { color: #fff; }
        .frontend-topbar .divider { color: rgba(255,255,255,0.5); margin: 0 0.5rem; user-select: none; }
        .frontend-header { background: #fff; border-bottom: 1px solid #eee; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .frontend-header .logo { font-size: 1.5rem; font-weight: 700; color: #333; text-decoration: none; display: inline-flex; align-items: center; }
        .frontend-header .logo .ez { color: #333; }
        .frontend-header .logo .ez-l { width: 32px; height: 32px; background: var(--ez-accent); color: #333; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; margin: 0 1px; font-size: 1.1rem; }
        .frontend-header .logo .icence { color: #333; }
        .frontend-header .nav-link { color: #333; font-weight: 500; padding: 0.6rem 0.75rem !important; }
        .frontend-header .nav-link:hover { color: var(--ez-accent-dark); }
        .frontend-header .dropdown-menu { border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 0 0 4px 4px; padding: 0.5rem 0; }
        .frontend-header .dropdown-item { padding: 0.5rem 1rem; color: #333; }
        .frontend-header .dropdown-item:hover { background: #f8f9fa; color: var(--ez-accent-dark); }
        .frontend-header .navbar-nav .nav-link.dropdown-toggle::after { margin-left: 0.35em; }
        .frontend-header .btn-book { background: var(--ez-accent); color: #333; border: none; font-weight: 600; }
        .frontend-header .btn-book:hover { background: var(--ez-accent-dark); color: #333; }
        .frontend-footer { background: var(--ez-dark); color: rgba(255,255,255,0.85); padding: 2.5rem 0; margin-top: auto; }
        .frontend-footer a { color: rgba(255,255,255,0.85); text-decoration: none; }
        .frontend-footer a:hover { color: #fff; }
        .frontend-footer .divider { color: rgba(255,255,255,0.4); margin: 0 0.5rem; }
        body { display: flex; flex-direction: column; min-height: 100vh; }
        main { flex: 1; }
    </style>
</head>
<body>
    {{-- Top bar: left = Support | Instruct with EzLicence | EzLicence Instructor Academy; right = Learner Login | Instructor Login (or Dashboard when logged in) --}}
    <div class="frontend-topbar py-2">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap align-items-center gap-1">
                    <a href="{{ route('support') }}">Support</a>
                    <span class="divider">|</span>
                    <a href="#">Instruct with EzLicence</a>
                    <span class="divider">|</span>
                    <a href="#">EzLicence Instructor Academy</a>
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
                    <span class="ez">Ez</span><span class="ez-l">L</span><span class="icence">icence</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#frontendNav" aria-controls="frontendNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="frontendNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navDrivingLessons" role="button" data-bs-toggle="dropdown" aria-expanded="false">Driving Lessons</a>
                            <ul class="dropdown-menu" aria-labelledby="navDrivingLessons">
                                <li><a class="dropdown-item" href="{{ route('find-instructor') }}">Driving Test Packages</a></li>
                                <li><a class="dropdown-item" href="{{ route('find-instructor') }}">International Licence Conversions</a></li>
                                <li><a class="dropdown-item" href="{{ route('find-instructor') }}">Refresher Lessons</a></li>
                                <li><a class="dropdown-item" href="#">Gift Vouchers</a></li>
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
                            <a class="nav-link" href="{{ route('find-instructor') }}">Prices &amp; Packages</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navResources" role="button" data-bs-toggle="dropdown" aria-expanded="false">Free Learner Resources</a>
                            <ul class="dropdown-menu" aria-labelledby="navResources">
                                <li><a class="dropdown-item" href="{{ url('/') }}#faqAccordion">FAQs</a></li>
                                <li><a class="dropdown-item" href="{{ route('blog.index') }}">Blog</a></li>
                                <li><a class="dropdown-item" href="#">Industry Insights</a></li>
                                <li><a class="dropdown-item" href="#">Free Practice Learners Test</a></li>
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

    <footer style="background: #f4f5f7; color: #333; padding: 3rem 0 1.5rem; margin-top: auto;">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <a class="d-inline-flex align-items-center mb-3 text-decoration-none" href="{{ url('/') }}">
                        <span style="font-size:1.4rem;font-weight:700;color:#333;">Ez</span><span style="width:28px;height:28px;background:var(--ez-accent);color:#333;font-weight:700;display:inline-flex;align-items:center;justify-content:center;margin:0 1px;font-size:1rem;">L</span><span style="font-size:1.4rem;font-weight:700;color:#333;">icence</span>
                    </a>
                    <p class="small" style="color:#555;"><strong>EzLicence takes the hassle out of choosing a driving school</strong> by helping learner drivers find, compare and book verified driving instructors online.</p>
                    <p class="small" style="color:#777;">The EzLicence online platform brings transparency, choice and efficiency to booking and managing driving instructors and driving lessons across Australia.</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold mb-3" style="color: var(--ez-dark);">Learner Tests Online</h6>
                    <ul class="list-unstyled small mb-0" style="line-height:1.8;">
                        <li><a href="#" class="text-decoration-none" style="color:#555;">FREE Practice Learners Test</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">NSW Driver Knowledge Test</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">VIC Learner Permit Knowledge Test</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">QLD Road Rules Test</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">WA Road Rules Theory Test</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">SA Learner Theory Test</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">TAS Driver Knowledge Test</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">ACT Road Rules Knowledge Test</a></li>
                    </ul>
                    <h6 class="fw-bold mt-3 mb-2" style="color: var(--ez-dark);">Driving Instructors by State</h6>
                    <ul class="list-unstyled small mb-0" style="line-height:1.8;">
                        <li><a href="{{ route('find-instructor') }}?q=NSW" class="text-decoration-none" style="color:#555;">NSW Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=VIC" class="text-decoration-none" style="color:#555;">VIC Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=QLD" class="text-decoration-none" style="color:#555;">QLD Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=WA" class="text-decoration-none" style="color:#555;">WA Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=SA" class="text-decoration-none" style="color:#555;">SA Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=TAS" class="text-decoration-none" style="color:#555;">TAS Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=ACT" class="text-decoration-none" style="color:#555;">ACT Driving Instructors</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold mb-3" style="color: var(--ez-dark);">Resources</h6>
                    <ul class="list-unstyled small mb-0" style="line-height:1.8;">
                        <li><a href="{{ route('support') }}" class="text-decoration-none" style="color:#555;">Support</a></li>
                        <li><a href="{{ route('blog.index') }}" class="text-decoration-none" style="color:#555;">Blog</a></li>
                        <li><a href="{{ route('contact') }}" class="text-decoration-none" style="color:#555;">Contact</a></li>
                        <li><a href="{{ route('about') }}" class="text-decoration-none" style="color:#555;">About</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">Learn more about EzLicence</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">Instruct with EzLicence</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">Become an instructor</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">Driving Instructor User policies</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">Learner User policies</a></li>
                    </ul>
                    <h6 class="fw-bold mt-3 mb-2" style="color: var(--ez-dark);">Driving Instructors by City</h6>
                    <ul class="list-unstyled small mb-0" style="line-height:1.8;">
                        <li><a href="{{ route('find-instructor') }}?q=Sydney" class="text-decoration-none" style="color:#555;">Sydney Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=Melbourne" class="text-decoration-none" style="color:#555;">Melbourne Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=Brisbane" class="text-decoration-none" style="color:#555;">Brisbane Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=Perth" class="text-decoration-none" style="color:#555;">Perth Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=Adelaide" class="text-decoration-none" style="color:#555;">Adelaide Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=Hobart" class="text-decoration-none" style="color:#555;">Hobart Driving Instructors</a></li>
                        <li><a href="{{ route('find-instructor') }}?q=Canberra" class="text-decoration-none" style="color:#555;">Canberra Driving Instructors</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold mb-3" style="color: var(--ez-dark);">Learn to Drive</h6>
                    <ul class="list-unstyled small mb-0" style="line-height:1.8;">
                        <li><a href="{{ route('find-instructor') }}" class="text-decoration-none" style="color:#555;">Driving Lessons</a></li>
                        <li><a href="{{ route('find-instructor') }}" class="text-decoration-none" style="color:#555;">Test Packages</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">Gift Vouchers</a></li>
                        <li><a href="{{ route('find-instructor') }}" class="text-decoration-none" style="color:#555;">Refresher Driving Lessons</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">International Licence Conversions</a></li>
                        <li><a href="#" class="text-decoration-none" style="color:#555;">UK Driving Lessons</a></li>
                    </ul>
                </div>
            </div>
            <hr style="border-color: rgba(0,0,0,0.1);">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 small" style="color:#777;">
                <span>Ez Licence Pty Ltd &copy; {{ date('Y') }}</span>
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <a href="{{ route('privacy') }}" class="text-decoration-none" style="color:#555;">Privacy Policy</a>
                    <a href="{{ route('terms') }}" class="text-decoration-none" style="color:#555;">Terms and Conditions</a>
                </div>
                <div class="d-flex gap-3">
                    <a href="#" style="color:#555;" title="Facebook"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" style="color:#555;" title="Instagram"><i class="bi bi-instagram fs-5"></i></a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
