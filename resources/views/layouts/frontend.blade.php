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
                    <a href="#">Support</a>
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
                                <li><a class="dropdown-item" href="#">Blog</a></li>
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

    <footer class="frontend-footer">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <h6 class="text-white mb-3">Learn to Drive</h6>
                    <ul class="list-unstyled small mb-0">
                        <li><a href="{{ route('find-instructor') }}">Driving Lessons</a></li>
                        <li><a href="{{ route('find-instructor') }}">Test Packages</a></li>
                        <li><a href="#">Refresher Driving Lessons</a></li>
                        <li><a href="#">International Licence Conversions</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-white mb-3">Resources</h6>
                    <ul class="list-unstyled small mb-0">
                        <li><a href="#">Support</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">About</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-white mb-3">Policies</h6>
                    <ul class="list-unstyled small mb-0">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms &amp; Conditions</a></li>
                        <li><a href="#">Learner User policies</a></li>
                        <li><a href="#">Instructor User policies</a></li>
                    </ul>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 small">
                <span>EzLicence Pty Ltd © {{ date('Y') }}</span>
                <div class="d-flex flex-wrap align-items-center gap-1">
                    <a href="#">Privacy Policy</a><span class="divider">|</span>
                    <a href="#">Terms and Conditions</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
