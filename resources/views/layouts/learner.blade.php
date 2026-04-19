<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Learner') – {{ config('app.name') }}</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body { background: #fff; }
        .learner-wrapper { display: flex; min-height: 100vh; }
        .learner-sidebar {
            width: 260px;
            min-width: 260px;
            background: var(--sl-gray-900);
            background-image: linear-gradient(180deg, var(--sl-gray-900) 0%, #0b1220 100%);
            position: relative;
            flex-shrink: 0;
            border-right: 1px solid rgba(255,255,255,0.04);
        }
        .learner-sidebar .logo {
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
        .learner-sidebar .logo .brand {
            display: flex;
            align-items: center;
        }
        .learner-sidebar .logo .ez-l {
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
        .learner-sidebar .logo .role-badge {
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
        .learner-sidebar .nav { padding: 1rem 0.75rem; }
        .learner-sidebar .nav-link {
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
        .learner-sidebar .nav-link:hover {
            background: rgba(255,255,255,0.06);
            color: #fff;
        }
        .learner-sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--sl-primary-500), var(--sl-primary-700));
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(255,132,0,0.3);
        }
        .learner-sidebar .nav-link i {
            font-size: 1.1rem;
            width: 1.25rem;
            text-align: center;
        }
        .learner-sidebar .nav-divider {
            border-top: 1px solid rgba(255,255,255,0.08);
            margin: 0.75rem 0.5rem;
        }
        .learner-main {
            flex: 1;
            overflow: auto;
            background: #fff;
        }
        .learner-main .topbar {
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
        .learner-main .topbar h5 {
            font-weight: 700;
            letter-spacing: -0.015em;
            color: var(--sl-gray-900);
        }
        .learner-main .content { padding: 2rem 1.75rem; }

        /* ── Topbar dropdown ─────────────────────────── */
        .learner-main .user-dropdown .dropdown-toggle {
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
        .learner-main .user-dropdown .dropdown-toggle:hover {
            border-color: var(--sl-gray-300);
            background: var(--sl-gray-50);
        }
        .learner-main .user-dropdown .dropdown-toggle::after { display: none; }
        .learner-main .user-dropdown .avatar-sm {
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
        .learner-main .user-dropdown .dropdown-menu {
            min-width: 220px;
            border: 1px solid var(--sl-gray-200);
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            padding: 0.5rem;
            margin-top: 0.5rem;
        }
        .learner-main .user-dropdown .dropdown-menu .dropdown-item {
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
        .learner-main .user-dropdown .dropdown-menu .dropdown-item:hover {
            background: var(--sl-gray-50);
            color: var(--sl-gray-900);
        }
        .learner-main .user-dropdown .dropdown-menu .dropdown-item i {
            font-size: 1rem;
            width: 1.25rem;
            text-align: center;
            color: var(--sl-gray-500);
        }
        .learner-main .user-dropdown .dropdown-menu .dropdown-item.text-danger i { color: #dc3545; }
        .learner-main .user-dropdown .dropdown-divider { margin: 0.35rem 0; border-color: var(--sl-gray-100); }
        .learner-main .user-dropdown .dropdown-header {
            padding: 0.5rem 0.75rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--sl-gray-400);
            font-weight: 600;
        }

        @media (max-width: 991.98px) {
            .learner-sidebar { width: 72px; min-width: 72px; }
            .learner-sidebar .logo .brand span:not(.ez-l) { display: none !important; }
            .learner-sidebar .logo .role-badge { display: none !important; }
            .learner-sidebar .nav-link span { display: none !important; }
            .learner-sidebar .nav-link { justify-content: center; padding: 0.75rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="learner-wrapper">
        <aside class="learner-sidebar">
            <div class="logo">
                <span class="brand"><span>Secure</span><span class="ez-l">L</span><span>icences</span></span>
                <span class="role-badge">Learner Portal</span>
            </div>
            <nav class="nav flex-column">
                @auth
                    <a class="nav-link {{ request()->routeIs('learner.dashboard') ? 'active' : '' }}" href="{{ route('learner.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('learner.calendar') ? 'active' : '' }}" href="{{ route('learner.calendar') }}">
                        <i class="bi bi-calendar3"></i>
                        <span>My Calendar</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('learner.wallet*') ? 'active' : '' }}" href="{{ route('learner.wallet') }}">
                        <i class="bi bi-wallet2"></i>
                        <span>Wallet</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('service-bookings.*') ? 'active' : '' }}" href="{{ route('service-bookings.index') }}">
                        <i class="bi bi-tools"></i>
                        <span>My Service Bookings</span>
                    </a>
                    <a class="nav-link" href="{{ route('services.categories') }}">
                        <i class="bi bi-search"></i>
                        <span>Find Services</span>
                    </a>
                    <div class="nav-divider"></div>
                    <a class="nav-link" href="#">
                        <i class="bi bi-gift"></i>
                        <span>Invite Friends</span>
                    </a>
                    <a class="nav-link" href="#">
                        <i class="bi bi-chat-left-text"></i>
                        <span>Give Feedback</span>
                    </a>
                    <a class="nav-link" href="{{ route('contact') }}">
                        <i class="bi bi-headset"></i>
                        <span>Support</span>
                    </a>
                @else
                    {{-- Guest booking navigation — limited menu --}}
                    <a class="nav-link {{ request()->routeIs('find-instructor*') ? 'active' : '' }}" href="{{ route('find-instructor') }}">
                        <i class="bi bi-search"></i>
                        <span>Find Instructor</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('learner.bookings.*') ? 'active' : '' }}" href="#" onclick="event.preventDefault();">
                        <i class="bi bi-calendar-check"></i>
                        <span>Book a Lesson</span>
                    </a>
                    <a class="nav-link" href="{{ route('prices-packages') }}">
                        <i class="bi bi-tag"></i>
                        <span>Prices &amp; Packages</span>
                    </a>
                    <div class="nav-divider"></div>
                    <a class="nav-link" href="{{ url('/') }}">
                        <i class="bi bi-house"></i>
                        <span>Home</span>
                    </a>
                    <a class="nav-link" href="{{ route('contact') }}">
                        <i class="bi bi-headset"></i>
                        <span>Support</span>
                    </a>
                    <div class="px-3 py-3 mt-2" style="background:rgba(255,213,0,0.08); border-radius:8px; margin:0.5rem;">
                        <p class="small text-white-50 mb-2">Already have an account?</p>
                        <a href="{{ route('learner.login') }}" class="btn btn-sm btn-warning w-100 fw-semibold">Log in</a>
                    </div>
                @endauth
            </nav>
        </aside>
        <main class="learner-main">
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
                    @auth
                        <div class="user-dropdown dropdown">
                            <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="avatar-sm">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                                <i class="bi bi-chevron-down" style="font-size:0.7rem; color: var(--sl-gray-400);"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li class="dropdown-header">Signed in as Learner</li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('learner.wallet') }}"><i class="bi bi-wallet2"></i> My Wallet</a></li>
                                <li><a class="dropdown-item" href="{{ route('find-instructor') }}"><i class="bi bi-search"></i> Find Instructor</a></li>
                                <li><a class="dropdown-item" href="{{ url('/') }}" target="_blank"><i class="bi bi-globe"></i> View Public Site</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('learner-logout-form').submit();">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <form id="learner-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    @else
                        <a href="{{ route('learner.login') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Log in
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-warning fw-semibold">
                            Sign up
                        </a>
                    @endauth
                </div>
            </div>
            <div class="content">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
