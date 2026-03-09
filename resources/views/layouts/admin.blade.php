<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – Admin</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        :root { --ez-admin-accent: #1b212c; --ez-admin-sidebar: #2c3e50; }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar {
            width: 260px; min-width: 260px; background: var(--ez-admin-sidebar); flex-shrink: 0;
        }
        .admin-sidebar .logo { padding: 1.25rem 1.5rem; font-size: 1.25rem; font-weight: 700; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-sidebar .logo span { color: #f0ad4e; }
        .admin-sidebar .nav { padding: 0.75rem 0; }
        .admin-sidebar .nav-link {
            display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 1.5rem;
            color: rgba(255,255,255,0.85); text-decoration: none; font-size: 0.95rem;
            border-left: 3px solid transparent;
        }
        .admin-sidebar .nav-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .admin-sidebar .nav-link.active { background: rgba(255,255,255,0.12); border-left-color: #f0ad4e; color: #fff; font-weight: 500; }
        .admin-sidebar .nav-link i { font-size: 1.1rem; width: 1.25rem; text-align: center; }
        .admin-main { flex: 1; overflow: auto; background: #f0f2f5; }
        .admin-header { background: #fff; border-bottom: 1px solid #e5e7eb; padding: 0.75rem 1.5rem; display: flex; align-items: center; justify-content: space-between; }
        .admin-content { padding: 1.5rem; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="logo d-block text-decoration-none">Ez<span>L</span>icence Admin</a>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i> Users
                </a>
                <a class="nav-link {{ request()->routeIs('admin.instructors*') ? 'active' : '' }}" href="{{ route('admin.instructors.index') }}">
                    <i class="bi bi-person-badge"></i> Instructors
                </a>
                <a class="nav-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">
                    <i class="bi bi-calendar-check"></i> Bookings
                </a>
                <a class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </nav>
        </aside>
        <main class="admin-main">
            <header class="admin-header">
                <h1 class="h5 mb-0 text-dark">@yield('heading', 'Dashboard')</h1>
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted">{{ auth()->user()->name }}</span>
                    <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary" target="_blank">View site</a>
                    <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">Logout</a>
                    <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </div>
            </header>
            <div class="admin-content">
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
