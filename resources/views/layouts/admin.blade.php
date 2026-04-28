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
        body { background: #fff; }
        /* Custom Bootstrap color extensions */
        .bg-indigo { background-color: #6610f2 !important; }
        .text-indigo { color: #6610f2 !important; }
        .btn-indigo { background-color: #6610f2; border-color: #6610f2; color: #fff; }
        .btn-indigo:hover { background-color: #520dc2; border-color: #520dc2; color: #fff; }
        .btn-outline-indigo { color: #6610f2; border-color: #6610f2; }
        .btn-outline-indigo:hover { background-color: #6610f2; border-color: #6610f2; color: #fff; }
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
            padding: 1.5rem 1.5rem 1rem;
            font-size: 1.25rem;
            font-weight: 800;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            letter-spacing: -0.02em;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            text-decoration: none;
        }
        .admin-sidebar .logo:hover { color: #fff; }
        .admin-sidebar .logo .brand {
            display: flex;
            align-items: center;
        }
        .admin-sidebar .logo .ez-l {
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
        .admin-sidebar .logo .role-badge {
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
            background: linear-gradient(135deg, var(--sl-primary-500), var(--sl-primary-700));
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(255,132,0,0.3);
        }
        .admin-sidebar .nav-link i {
            font-size: 1.1rem;
            width: 1.25rem;
            text-align: center;
        }
        .admin-main {
            flex: 1;
            overflow: auto;
            background: #fff;
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

        /* ── Topbar dropdown ─────────────────────────── */
        .admin-header .user-dropdown .dropdown-toggle {
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
        .admin-header .user-dropdown .dropdown-toggle:hover {
            border-color: var(--sl-gray-300);
            background: var(--sl-gray-50);
        }
        .admin-header .user-dropdown .dropdown-toggle::after { display: none; }
        .admin-header .user-dropdown .avatar-sm {
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
        .admin-header .user-dropdown .dropdown-menu {
            min-width: 220px;
            border: 1px solid var(--sl-gray-200);
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            padding: 0.5rem;
            margin-top: 0.5rem;
        }
        .admin-header .user-dropdown .dropdown-menu .dropdown-item {
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
        .admin-header .user-dropdown .dropdown-menu .dropdown-item:hover {
            background: var(--sl-gray-50);
            color: var(--sl-gray-900);
        }
        .admin-header .user-dropdown .dropdown-menu .dropdown-item i {
            font-size: 1rem;
            width: 1.25rem;
            text-align: center;
            color: var(--sl-gray-500);
        }
        .admin-header .user-dropdown .dropdown-menu .dropdown-item.text-danger i { color: #dc3545; }
        .admin-header .user-dropdown .dropdown-divider { margin: 0.35rem 0; border-color: var(--sl-gray-100); }
        .admin-header .user-dropdown .dropdown-header {
            padding: 0.5rem 0.75rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--sl-gray-400);
            font-weight: 600;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar { width: 72px; min-width: 72px; }
            .admin-sidebar .logo .brand span:not(.ez-l) { display: none !important; }
            .admin-sidebar .logo .role-badge { display: none !important; }
            .admin-sidebar .nav-link { justify-content: center; padding: 0.75rem; font-size: 0; }
            .admin-sidebar .nav-link i { font-size: 1.1rem; }
            .admin-sidebar .nav-link .badge { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="logo">
                <span class="brand">Secure<span class="ez-l">L</span>icences</span>
                <span class="role-badge">Admin Panel</span>
            </a>
            @php
                $sidebarPendingVerify = \App\Models\InstructorProfile::where('verification_status', 'pending')->count();
                $sidebarPendingBookings = \App\Models\Booking::where('status', 'pending')->count();
                $sidebarPendingPayouts = \App\Models\InstructorPayout::where('status', 'pending')->count();
            @endphp
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('admin.calendar') ? 'active' : '' }}" href="{{ route('admin.calendar') }}">
                    <i class="bi bi-calendar3"></i> Calendar
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
                <a class="nav-link {{ request()->routeIs('admin.email-logs*') ? 'active' : '' }}" href="{{ route('admin.email-logs.index') }}">
                    <i class="bi bi-envelope-paper"></i> Email Logs
                </a>
                <a class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </nav>
        </aside>
        <main class="admin-main">
            <header class="admin-header">
                <h1 class="h5 mb-0 text-dark">@yield('heading', 'Dashboard')</h1>
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
                            <span class="avatar-sm">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down" style="font-size:0.7rem; color: var(--sl-gray-400);"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-header">Signed in as Admin</li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="bi bi-gear"></i> Settings</a></li>
                            <li><a class="dropdown-item" href="{{ url('/') }}" target="_blank"><i class="bi bi-globe"></i> View Public Site</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
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
