<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Instructor') – {{ config('app.name') }}</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        .instructor-wrapper { display: flex; min-height: 100vh; }
        .instructor-sidebar {
            width: 260px;
            min-width: 260px;
            background: #fff;
            border-right: 1px solid #eee;
            position: relative;
            flex-shrink: 0;
        }
        .instructor-sidebar::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #f0ad4e;
        }
        .instructor-sidebar .logo {
            padding: 1.25rem 1.5rem;
            font-size: 1.35rem;
            font-weight: 700;
            color: #333;
            border-bottom: 1px solid #eee;
        }
        .instructor-sidebar .logo i { color: #f0ad4e; }
        .instructor-sidebar .nav { padding: 0.75rem 0; }
        .instructor-sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1.5rem;
            color: #333;
            text-decoration: none;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
        }
        .instructor-sidebar .nav-link:hover { background: #f8f9fa; color: #333; }
        .instructor-sidebar .nav-link.active { background: #f0f0f0; border-left-color: #f0ad4e; font-weight: 500; }
        .instructor-sidebar .nav-link i { font-size: 1.1rem; width: 1.25rem; text-align: center; }
        .instructor-sidebar .nav-link .caret { margin-left: auto; font-size: 0.7rem; transition: transform 0.2s; }
        .instructor-sidebar .nav-link[aria-expanded="true"] .caret { transform: rotate(180deg); }
        .instructor-sidebar .submenu { list-style: none; padding: 0; margin: 0; }
        .instructor-sidebar .submenu .nav-link { padding-left: 2.5rem; font-size: 0.9rem; }
        .instructor-main {
            flex: 1;
            overflow: auto;
            background: #f5f5f5;
        }
        .instructor-main .topbar {
            background: #fff;
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .instructor-main .content { padding: 1.5rem; }
        @media (max-width: 991.98px) {
            .instructor-sidebar { width: 72px; min-width: 72px; }
            .instructor-sidebar .logo span:not(.logo-icon),
            .instructor-sidebar .nav-link span:not(.nav-icon),
            .instructor-sidebar .submenu,
            .instructor-sidebar .nav-link .caret { display: none !important; }
            .instructor-sidebar .nav-link { justify-content: center; padding: 0.75rem; }
        }
    </style>
</head>
<body>
    <div class="instructor-wrapper">
        <aside class="instructor-sidebar">
            <div class="logo">
                <span style="display:inline-flex;align-items:center;justify-content:center;width:1.25rem;height:1.25rem;background:#f0ad4e;color:#333;font-weight:700;font-size:0.85rem;margin-right:4px;">L</span> EzLicense
            </div>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}" href="{{ route('instructor.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link {{ request()->routeIs('instructor.calendar') ? 'active' : '' }}" href="{{ route('instructor.calendar') }}">
                    <i class="bi bi-calendar3"></i>
                    <span>Calendar</span>
                </a>
                <a class="nav-link {{ request()->routeIs('instructor.learners') ? 'active' : '' }}" href="{{ route('instructor.learners') }}">
                    <i class="bi bi-people"></i>
                    <span>Learners</span>
                </a>
                <a class="nav-link {{ request()->routeIs('instructor.reports') ? 'active' : '' }}" href="{{ route('instructor.reports') }}">
                    <i class="bi bi-pie-chart"></i>
                    <span>Reports</span>
                </a>
                <a class="nav-link {{ request()->routeIs('instructor.settings.*') ? 'active' : '' }}" href="#settings-menu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('instructor.settings.*') ? 'true' : 'false' }}" role="button">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                    <i class="bi bi-chevron-up caret"></i>
                </a>
                <div class="collapse {{ request()->routeIs('instructor.settings.*') ? 'show' : '' }}" id="settings-menu">
                    <ul class="submenu">
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.personal-details') ? 'active' : '' }}" href="{{ route('instructor.settings.personal-details') }}">Personal Details</a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.profile') ? 'active' : '' }}" href="{{ route('instructor.settings.profile') }}">Profile</a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.vehicle') ? 'active' : '' }}" href="{{ route('instructor.settings.vehicle') }}">Vehicle</a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.service-area') ? 'active' : '' }}" href="{{ route('instructor.settings.service-area') }}">Service Area</a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.opening-hours') ? 'active' : '' }}" href="{{ route('instructor.settings.opening-hours') }}">Opening Hours</a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.calendar-settings') ? 'active' : '' }}" href="{{ route('instructor.settings.calendar-settings') }}">Calendar Settings</a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.pricing') ? 'active' : '' }}" href="{{ route('instructor.settings.pricing') }}">Pricing</a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.documents') ? 'active' : '' }}" href="{{ route('instructor.settings.documents') }}">Documents</a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.banking') ? 'active' : '' }}" href="{{ route('instructor.settings.banking') }}">Banking</a></li>
                    </ul>
                </div>
            </nav>
        </aside>
        <main class="instructor-main">
            <div class="topbar">
                <h5 class="mb-0">@yield('heading', 'Instructor')</h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">{{ Auth::user()->name }}</span>
                    <a href="{{ route('find-instructor') }}" class="btn btn-sm btn-outline-secondary">Public site</a>
                    <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-secondary" onclick="event.preventDefault(); document.getElementById('instructor-logout-form').submit();">Logout</a>
                    <form id="instructor-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </div>
            </div>
            <div class="content">
                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
