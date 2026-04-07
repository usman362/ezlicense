<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Learner') – {{ config('app.name') }}</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        .learner-wrapper { display: flex; min-height: 100vh; }
        .learner-sidebar {
            width: 260px;
            min-width: 260px;
            background: #2c3e50;
            position: relative;
            flex-shrink: 0;
        }
        .learner-sidebar::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #f0ad4e;
        }
        .learner-sidebar .logo {
            padding: 1.25rem 1.5rem;
            font-size: 1.35rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .learner-sidebar .logo i { color: #f0ad4e; }
        .learner-sidebar .nav { padding: 0.75rem 0; }
        .learner-sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1.5rem;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
        }
        .learner-sidebar .nav-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .learner-sidebar .nav-link.active { background: rgba(255,255,255,0.12); border-left-color: #f0ad4e; color: #fff; font-weight: 500; }
        .learner-sidebar .nav-link i { font-size: 1.1rem; width: 1.25rem; text-align: center; }
        .learner-sidebar .nav-divider { border-top: 1px solid rgba(255,255,255,0.1); margin: 0.5rem 0; }
        .learner-main {
            flex: 1;
            overflow: auto;
            background: #f5f5f5;
        }
        .learner-main .topbar {
            background: #fff;
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .learner-main .content { padding: 1.5rem; }
        @media (max-width: 991.98px) {
            .learner-sidebar { width: 72px; min-width: 72px; }
            .learner-sidebar .logo span:not(.logo-icon),
            .learner-sidebar .nav-link span { display: none !important; }
            .learner-sidebar .nav-link { justify-content: center; padding: 0.75rem; }
        }
    </style>
</head>
<body>
    <div class="learner-wrapper">
        <aside class="learner-sidebar">
            <div class="logo">
                <span style="display:inline-flex;align-items:center;justify-content:center;width:1.25rem;height:1.25rem;background:#f0ad4e;color:#333;font-weight:700;font-size:0.85rem;margin-right:4px;">L</span> Secure Licences
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
                <h5 class="mb-0">@yield('heading', 'Learner')</h5>
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="text-dark" title="Notifications" aria-label="Notifications"><i class="bi bi-bell fs-5"></i></a>
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-secondary fw-bold" style="width: 32px; height: 32px; font-size: 0.9rem;">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                        <span class="text-muted small">{{ Auth::user()->name }}</span>
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
