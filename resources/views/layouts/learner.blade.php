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
        body { background: var(--sl-gray-50); }
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
            padding: 1.5rem 1.5rem;
            font-size: 1.25rem;
            font-weight: 800;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .learner-sidebar .logo .ez-l {
            width: 30px; height: 30px;
            background: var(--sl-accent-500);
            color: var(--sl-gray-900);
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(245,158,11,0.35);
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
            background: linear-gradient(135deg, var(--sl-primary-600), var(--sl-primary-700));
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(37,99,235,0.3);
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
            background: var(--sl-gray-50);
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

        @media (max-width: 991.98px) {
            .learner-sidebar { width: 72px; min-width: 72px; }
            .learner-sidebar .logo span:not(.ez-l) { display: none !important; }
            .learner-sidebar .nav-link span { display: none !important; }
            .learner-sidebar .nav-link { justify-content: center; padding: 0.75rem; }
        }
    </style>
</head>
<body>
    <div class="learner-wrapper">
        <aside class="learner-sidebar">
            <div class="logo">
                <span>Secure</span><span class="ez-l">L</span><span>icences</span>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('learner.dashboard') ? 'active' : '' }}" href="{{ route('learner.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
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
                <a class="nav-link" href="#">
                    <i class="bi bi-headset"></i>
                    <span>Support</span>
                </a>
            </nav>
        </aside>
        <main class="learner-main">
            <div class="topbar">
                <h5 class="mb-0">@yield('heading', 'Dashboard')</h5>
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="text-muted" title="Notifications" aria-label="Notifications" style="font-size:1.15rem;"><i class="bi bi-bell"></i></a>
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 36px; height: 36px; font-size: 0.9rem; background: linear-gradient(135deg, var(--sl-primary-500), var(--sl-teal-500));">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                        <span class="fw-semibold small d-none d-md-inline" style="color: var(--sl-gray-700);">{{ Auth::user()->name }}</span>
                        <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-secondary" onclick="event.preventDefault(); document.getElementById('learner-logout-form').submit();">Logout</a>
                    </div>
                    <form id="learner-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
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
