<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – Admin</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body { background: var(--sl-gray-50); }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar {
            width: 260px;
            min-width: 260px;
            background: var(--sl-gray-900);
            background-image: linear-gradient(180deg, var(--sl-gray-900) 0%, #0b1220 100%);
            flex-shrink: 0;
            border-right: 1px solid rgba(255,255,255,0.04);
        }
        .admin-sidebar .logo {
            padding: 1.5rem 1.5rem;
            font-size: 1.125rem;
            font-weight: 800;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            letter-spacing: -0.02em;
        }
        .admin-sidebar .logo span {
            display: inline-block;
            width: 26px;
            height: 26px;
            background: var(--sl-accent-500);
            color: var(--sl-gray-900);
            font-weight: 800;
            line-height: 26px;
            text-align: center;
            font-size: 0.9rem;
            margin: 0 2px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(245,158,11,0.35);
        }
        .admin-sidebar .nav { padding: 1rem 0.75rem; }
        .admin-sidebar .nav-link {
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
        .admin-sidebar .nav-link:hover {
            background: rgba(255,255,255,0.06);
            color: #fff;
        }
        .admin-sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--sl-primary-600), var(--sl-primary-700));
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(37,99,235,0.3);
        }
        .admin-sidebar .nav-link i {
            font-size: 1.1rem;
            width: 1.25rem;
            text-align: center;
        }
        .admin-main {
            flex: 1;
            overflow: auto;
            background: var(--sl-gray-50);
        }
        .admin-header {
            background: #fff;
            border-bottom: 1px solid var(--sl-gray-200);
            padding: 0.875rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--sl-shadow-xs);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .admin-header h1 {
            font-weight: 700;
            letter-spacing: -0.015em;
            color: var(--sl-gray-900);
        }
        .admin-content { padding: 2rem 1.75rem; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="logo d-block text-decoration-none">Secure<span>L</span>icences Admin</a>
            @php
                $sidebarPendingVerify = \App\Models\InstructorProfile::where('verification_status', 'pending')->count();
                $sidebarPendingBookings = \App\Models\Booking::where('status', 'pending')->count();
                $sidebarPendingPayouts = \App\Models\InstructorPayout::where('status', 'pending')->count();
            @endphp
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i> Users
                </a>
                <a class="nav-link {{ request()->routeIs('admin.instructors*') ? 'active' : '' }}" href="{{ route('admin.instructors.index') }}">
                    <i class="bi bi-person-badge"></i> Instructors
                    @if($sidebarPendingVerify > 0)
                        <span class="badge bg-warning text-dark ms-auto" style="font-size:0.7rem">{{ $sidebarPendingVerify }}</span>
                    @endif
                </a>
                <a class="nav-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">
                    <i class="bi bi-calendar-check"></i> Bookings
                    @if($sidebarPendingBookings > 0)
                        <span class="badge bg-info ms-auto" style="font-size:0.7rem">{{ $sidebarPendingBookings }}</span>
                    @endif
                </a>
                <a class="nav-link {{ request()->routeIs('admin.payouts*') ? 'active' : '' }}" href="{{ route('admin.payouts.index') }}">
                    <i class="bi bi-cash-stack"></i> Payouts
                    @if($sidebarPendingPayouts > 0)
                        <span class="badge bg-warning text-dark ms-auto" style="font-size:0.7rem">{{ $sidebarPendingPayouts }}</span>
                    @endif
                </a>
                <a class="nav-link {{ request()->routeIs('admin.blog*') ? 'active' : '' }}" href="{{ route('admin.blog.index') }}">
                    <i class="bi bi-journal-richtext"></i> Blog
                </a>
                <a class="nav-link {{ request()->routeIs('admin.gift-vouchers*') ? 'active' : '' }}" href="{{ route('admin.gift-vouchers.index') }}">
                    <i class="bi bi-gift"></i> Gift Vouchers
                </a>
                <a class="nav-link {{ request()->routeIs('admin.service-categories*') ? 'active' : '' }}" href="{{ route('admin.service-categories.index') }}">
                    <i class="bi bi-tags"></i> Service Categories
                </a>
                <a class="nav-link {{ request()->routeIs('admin.service-providers*') ? 'active' : '' }}" href="{{ route('admin.service-providers.index') }}">
                    <i class="bi bi-people"></i> Service Providers
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
