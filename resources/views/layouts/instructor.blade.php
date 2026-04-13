<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Instructor') – {{ config('app.name') }}</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body { background: #fff; }
        .instructor-wrapper { display: flex; min-height: 100vh; }

        /* ── Dark Sidebar (matching admin/learner) ───────── */
        .instructor-sidebar {
            width: 260px;
            min-width: 260px;
            background: var(--sl-gray-900);
            background-image: linear-gradient(180deg, var(--sl-gray-900) 0%, #0b1220 100%);
            position: relative;
            flex-shrink: 0;
            border-right: 1px solid rgba(255,255,255,0.04);
        }
        .instructor-sidebar .logo {
            padding: 1.5rem 1.5rem 1rem;
            font-size: 1.25rem;
            font-weight: 800;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            letter-spacing: -0.02em;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .instructor-sidebar .logo .brand {
            display: flex;
            align-items: center;
        }
        .instructor-sidebar .logo .ez-l {
            width: 30px; height: 30px;
            background: var(--sl-accent-500);
            color: var(--sl-gray-900);
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 2px;
            font-size: 1rem;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(255,213,0,0.35);
        }
        .instructor-sidebar .logo .role-badge {
            display: inline-block;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--sl-accent-500);
            background: rgba(255,213,0,0.1);
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            border: 1px solid rgba(255,213,0,0.2);
            width: fit-content;
        }
        .instructor-sidebar .nav { padding: 1rem 0.75rem; }
        .instructor-sidebar .nav-section-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.35);
            padding: 0.75rem 0.875rem 0.35rem;
            font-weight: 600;
        }
        .instructor-sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 0.875rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: var(--sl-radius);
            margin-bottom: 2px;
            transition: all var(--sl-transition);
        }
        .instructor-sidebar .nav-link:hover {
            background: rgba(255,255,255,0.06);
            color: #fff;
        }
        .instructor-sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--sl-primary-500), var(--sl-primary-700));
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(255,132,0,0.3);
        }
        .instructor-sidebar .nav-link i {
            font-size: 1.1rem;
            width: 1.25rem;
            text-align: center;
        }
        .instructor-sidebar .nav-link .caret {
            margin-left: auto;
            font-size: 0.7rem;
            transition: transform var(--sl-transition);
        }
        .instructor-sidebar .nav-link[aria-expanded="true"] .caret { transform: rotate(180deg); }
        .instructor-sidebar .nav-divider {
            border-top: 1px solid rgba(255,255,255,0.08);
            margin: 0.75rem 0.5rem;
        }
        .instructor-sidebar .submenu {
            list-style: none;
            padding: 0;
            margin: 0.25rem 0 0.25rem 0.5rem;
            border-left: 2px solid rgba(255,255,255,0.12);
        }
        .instructor-sidebar .submenu .nav-link {
            padding-left: 1.5rem;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.55);
        }
        .instructor-sidebar .submenu .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.06);
        }
        .instructor-sidebar .submenu .nav-link.active {
            background: rgba(255,213,0,0.15);
            color: var(--sl-primary-500);
            box-shadow: none;
            font-weight: 600;
        }

        /* ── Sidebar Footer (profile badge) ──────────── */
        .instructor-sidebar .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .instructor-sidebar .sidebar-footer .profile-badge {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.5rem 0.625rem;
            border-radius: var(--sl-radius);
            transition: background var(--sl-transition);
        }
        .instructor-sidebar .sidebar-footer .profile-badge:hover {
            background: rgba(255,255,255,0.06);
        }
        .instructor-sidebar .sidebar-footer .avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--sl-primary-500), var(--sl-primary-700));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        .instructor-sidebar .sidebar-footer .profile-info {
            overflow: hidden;
        }
        .instructor-sidebar .sidebar-footer .profile-name {
            color: #fff;
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .instructor-sidebar .sidebar-footer .profile-role {
            color: rgba(255,255,255,0.45);
            font-size: 0.7rem;
            font-weight: 500;
        }

        /* ── Main Content Area ───────────────────────── */
        .instructor-main {
            flex: 1;
            overflow: auto;
            background: #fff;
        }
        .instructor-main .topbar {
            background: #fff;
            padding: 0.875rem 1.75rem;
            border-bottom: 1px solid var(--sl-gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--sl-shadow-xs);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .instructor-main .topbar h5 {
            font-weight: 700;
            letter-spacing: -0.015em;
            color: var(--sl-gray-900);
        }
        .instructor-main .content { padding: 2rem 1.75rem; }

        /* ── Topbar dropdown ─────────────────────────── */
        .instructor-main .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.75rem;
            border-radius: var(--sl-radius);
            border: 1px solid var(--sl-gray-200);
            background: #fff;
            color: var(--sl-gray-700);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--sl-transition);
        }
        .instructor-main .user-dropdown .dropdown-toggle:hover {
            border-color: var(--sl-gray-300);
            background: var(--sl-gray-50);
        }
        .instructor-main .user-dropdown .dropdown-toggle::after { display: none; }
        .instructor-main .user-dropdown .avatar-sm {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--sl-primary-500), var(--sl-primary-700));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        .instructor-main .user-dropdown .dropdown-menu {
            min-width: 220px;
            border: 1px solid var(--sl-gray-200);
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            padding: 0.5rem;
            margin-top: 0.5rem;
        }
        .instructor-main .user-dropdown .dropdown-menu .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.55rem 0.75rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--sl-gray-700);
            transition: all var(--sl-transition);
        }
        .instructor-main .user-dropdown .dropdown-menu .dropdown-item:hover {
            background: var(--sl-gray-50);
            color: var(--sl-gray-900);
        }
        .instructor-main .user-dropdown .dropdown-menu .dropdown-item i {
            font-size: 1rem;
            width: 1.25rem;
            text-align: center;
            color: var(--sl-gray-500);
        }
        .instructor-main .user-dropdown .dropdown-menu .dropdown-item.text-danger i { color: #dc3545; }
        .instructor-main .user-dropdown .dropdown-divider { margin: 0.35rem 0; border-color: var(--sl-gray-100); }
        .instructor-main .user-dropdown .dropdown-header {
            padding: 0.5rem 0.75rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--sl-gray-400);
            font-weight: 600;
        }

        /* ── Responsive ──────────────────────────────── */
        @media (max-width: 991.98px) {
            .instructor-sidebar { width: 72px; min-width: 72px; }
            .instructor-sidebar .logo .brand span:not(.ez-l) { display: none !important; }
            .instructor-sidebar .logo .role-badge { display: none !important; }
            .instructor-sidebar .nav-link span { display: none !important; }
            .instructor-sidebar .nav-link .caret { display: none !important; }
            .instructor-sidebar .nav-link { justify-content: center; padding: 0.75rem; }
            .instructor-sidebar .nav-section-label { display: none !important; }
            .instructor-sidebar .submenu { display: none !important; }
            .instructor-sidebar .sidebar-footer .profile-info { display: none !important; }
            .instructor-sidebar .sidebar-footer .profile-badge { justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="instructor-wrapper">
        <aside class="instructor-sidebar">
            <div class="logo">
                <span class="brand"><span>Secure</span><span class="ez-l">L</span><span>icences</span></span>
                <span class="role-badge">Instructor Portal</span>
            </div>
            <nav class="nav flex-column">
                <div class="nav-section-label">Main</div>
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

                <div class="nav-divider"></div>
                <div class="nav-section-label">Settings</div>

                <a class="nav-link {{ request()->routeIs('instructor.settings.*') && !request()->routeIs('instructor.settings.guide') ? 'active' : '' }}" href="#settings-menu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('instructor.settings.*') ? 'true' : 'false' }}" role="button">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                    <i class="bi bi-chevron-up caret"></i>
                </a>
                <div class="collapse {{ request()->routeIs('instructor.settings.*') ? 'show' : '' }}" id="settings-menu">
                    <ul class="submenu">
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.personal-details') ? 'active' : '' }}" href="{{ route('instructor.settings.personal-details') }}"><i class="bi bi-person"></i> <span>Personal Details</span></a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.profile') ? 'active' : '' }}" href="{{ route('instructor.settings.profile') }}"><i class="bi bi-card-text"></i> <span>Profile</span></a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.vehicle') ? 'active' : '' }}" href="{{ route('instructor.settings.vehicle') }}"><i class="bi bi-car-front"></i> <span>Vehicle</span></a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.service-area') ? 'active' : '' }}" href="{{ route('instructor.settings.service-area') }}"><i class="bi bi-geo-alt"></i> <span>Service Area</span></a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.opening-hours') ? 'active' : '' }}" href="{{ route('instructor.settings.opening-hours') }}"><i class="bi bi-clock"></i> <span>Opening Hours</span></a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.calendar-settings') ? 'active' : '' }}" href="{{ route('instructor.settings.calendar-settings') }}"><i class="bi bi-calendar-week"></i> <span>Calendar Settings</span></a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.pricing') ? 'active' : '' }}" href="{{ route('instructor.settings.pricing') }}"><i class="bi bi-tag"></i> <span>Pricing</span></a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.documents') ? 'active' : '' }}" href="{{ route('instructor.settings.documents') }}"><i class="bi bi-file-earmark-text"></i> <span>Documents</span></a></li>
                        <li><a class="nav-link {{ request()->routeIs('instructor.settings.banking') ? 'active' : '' }}" href="{{ route('instructor.settings.banking') }}"><i class="bi bi-bank"></i> <span>Banking</span></a></li>
                    </ul>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="profile-badge">
                    <div class="avatar">{{ strtoupper(substr(Auth::user()->name ?? 'I', 0, 1)) }}</div>
                    <div class="profile-info">
                        <div class="profile-name">{{ Auth::user()->name }}</div>
                        <div class="profile-role">Instructor</div>
                    </div>
                </div>
            </div>
        </aside>
        <main class="instructor-main">
            <div class="topbar">
                <h5 class="mb-0">@yield('heading', 'Dashboard')</h5>
                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <a href="#" class="text-muted position-relative" title="Notifications" aria-label="Notifications" style="font-size:1.15rem;" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-0" style="min-width:300px; border-radius:10px; box-shadow:0 10px 40px rgba(0,0,0,0.12); border:1px solid var(--sl-gray-200);">
                            <div class="px-3 py-2 border-bottom" style="background:var(--sl-gray-50); border-radius:10px 10px 0 0;">
                                <span class="fw-semibold small" style="color:var(--sl-gray-700);">Notifications</span>
                            </div>
                            <div class="px-3 py-4 text-center">
                                <i class="bi bi-bell-slash text-muted" style="font-size:1.5rem;"></i>
                                <p class="mb-0 mt-2 small text-muted">No new notifications</p>
                            </div>
                        </div>
                    </div>
                    <div class="user-dropdown dropdown">
                        <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar-sm">{{ strtoupper(substr(Auth::user()->name ?? 'I', 0, 1)) }}</span>
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                            <i class="bi bi-chevron-down" style="font-size:0.7rem; color: var(--sl-gray-400);"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-header">Signed in as Instructor</li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('instructor.settings.personal-details') }}"><i class="bi bi-person"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('instructor.settings.banking') }}"><i class="bi bi-bank"></i> Banking</a></li>
                            <li><a class="dropdown-item" href="{{ route('find-instructor') }}" target="_blank"><i class="bi bi-globe"></i> View Public Site</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('instructor-logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                    <form id="instructor-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </div>
            </div>
            <div class="content">
                @if(session('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
