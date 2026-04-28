<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Book Your Driving Lessons') – Secure Licences</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body {
            background: #f7f8fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        /* ── Minimal header (EasyLicence-style) ────────────────────── */
        .booking-header {
            background: #fff;
            border-bottom: 1px solid var(--sl-gray-200);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .booking-header .brand {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--sl-gray-900);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .booking-header .brand:hover { color: var(--sl-gray-900); }
        .booking-header .brand .ez-l {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--sl-accent-500);
            color: var(--sl-gray-900);
            font-weight: 800;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            margin: 0 2px;
            box-shadow: 0 3px 10px rgba(255,213,0,0.35);
        }
        .booking-header .login-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--sl-gray-700);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.45rem 0.9rem;
            border-radius: 8px;
            transition: all 0.15s;
        }
        .booking-header .login-btn:hover {
            background: var(--sl-gray-50);
            color: var(--sl-gray-900);
        }

        /* ── Stepper ───────────────────────────────────────────────── */
        .booking-stepper {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            max-width: 900px;
            margin: 2rem auto 2.5rem;
            padding: 0 1rem;
            position: relative;
        }
        .booking-stepper .step {
            flex: 1;
            text-align: center;
            position: relative;
            min-width: 0;
        }
        .booking-stepper .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--sl-gray-200);
            color: var(--sl-gray-500);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
            border: 3px solid #fff;
        }
        .booking-stepper .step.completed .step-circle {
            background: var(--sl-accent-500);
            color: var(--sl-gray-900);
        }
        .booking-stepper .step.active .step-circle {
            background: var(--sl-gray-900);
            color: #fff;
        }
        .booking-stepper .step-label {
            font-size: 0.85rem;
            color: var(--sl-gray-500);
            font-weight: 500;
            display: block;
            padding: 0 0.25rem;
        }
        .booking-stepper .step.active .step-label,
        .booking-stepper .step.completed .step-label {
            color: var(--sl-gray-900);
            font-weight: 600;
        }
        /* Connector line between steps */
        .booking-stepper .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 20px;
            left: calc(50% + 20px);
            right: calc(-50% + 20px);
            height: 2px;
            background: var(--sl-gray-200);
            z-index: 1;
        }
        .booking-stepper .step.completed:not(:last-child)::after {
            background: var(--sl-accent-500);
        }

        /* ── Main content wrapper ──────────────────────────────────── */
        .booking-main {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1rem 3rem;
        }
        .booking-content-row { gap: 1.5rem; }

        /* ── Trust sidebar panel ───────────────────────────────────── */
        .trust-panel {
            background: #fff;
            border: 1px solid var(--sl-gray-200);
            border-radius: 14px;
            padding: 1.25rem;
            margin-top: 1rem;
        }
        .trust-panel h6 {
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
            color: var(--sl-gray-900);
        }
        .trust-panel p {
            font-size: 0.82rem;
            color: var(--sl-gray-500);
            margin-bottom: 1rem;
            line-height: 1.45;
        }
        .trust-panel p:last-child { margin-bottom: 0; }

        .bnpl-panel {
            background: #fff;
            border: 1px solid var(--sl-gray-200);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            margin-top: 1rem;
        }
        .bnpl-panel .bnpl-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--sl-gray-900);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 0.25rem;
        }
        .bnpl-panel .bnpl-amount {
            font-size: 0.85rem;
            color: var(--sl-gray-500);
            margin-bottom: 0.75rem;
        }
        .bnpl-badges {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.4rem;
        }
        .bnpl-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .bnpl-badge.paypal { background: #f5f7ff; color: #003087; }
        .bnpl-badge.afterpay { background: #d6f5e8; color: #000; }
        .bnpl-badge.klarna { background: #ffb3c7; color: #17120f; }

        /* ── Footer ────────────────────────────────────────────────── */
        .booking-footer {
            text-align: center;
            padding: 1.5rem 1rem;
            color: var(--sl-gray-500);
            font-size: 0.85rem;
            border-top: 1px solid var(--sl-gray-200);
            background: #fff;
            margin-top: 2rem;
        }

        @media (max-width: 767px) {
            .booking-stepper .step-label { font-size: 0.72rem; }
            .booking-stepper .step-circle { width: 32px; height: 32px; font-size: 0.85rem; }
            .booking-stepper .step:not(:last-child)::after { top: 16px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- ── Minimal Header ──────────────────────────────────────── --}}
    <header class="booking-header">
        <div class="container-xxl d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="brand">
                Secure<span class="ez-l">L</span>icences
            </a>
            @auth
                <div class="dropdown">
                    <button class="login-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle" style="font-size:1.2rem;"></i>
                        <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                        <i class="bi bi-chevron-down small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if(auth()->user()->isLearner())
                            <li><a class="dropdown-item" href="{{ route('learner.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                        @elseif(auth()->user()->isInstructor())
                            <li><a class="dropdown-item" href="{{ route('instructor.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                        @elseif(auth()->user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                        @endif
                        <li><a class="dropdown-item" href="{{ route('find-instructor') }}"><i class="bi bi-search me-2"></i>Find Instructor</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('booking-logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
                <form id="booking-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            @else
                <a href="{{ route('learner.login') }}" class="login-btn">
                    <i class="bi bi-person" style="font-size:1.15rem;"></i>
                    <span>Log in</span>
                </a>
            @endauth
        </div>
    </header>

    {{-- ── Stepper ─────────────────────────────────────────────── --}}
    @php
        $currentStep = (int) ($step ?? 1); // 1..5
        // Matches EasyLicence 5-step flow
        $steps = [
            1 => 'Instructor',
            2 => 'Amount',
            3 => 'Book your lessons',
            4 => 'Learner Registration',
            5 => 'Payment',
        ];
    @endphp
    <div class="booking-stepper">
        @foreach($steps as $num => $label)
            @php
                $state = $num < $currentStep ? 'completed' : ($num === $currentStep ? 'active' : '');
            @endphp
            <div class="step {{ $state }}">
                <div class="step-circle">
                    @if($num < $currentStep)
                        <i class="bi bi-check-lg"></i>
                    @else
                        {{ $num }}
                    @endif
                </div>
                <span class="step-label">{{ $label }}</span>
            </div>
        @endforeach
    </div>

    {{-- ── Main Content ────────────────────────────────────────── --}}
    <main class="booking-main">
        @if(session('message'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">{{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @yield('content')
    </main>

    {{-- ── Footer ──────────────────────────────────────────────── --}}
    <footer class="booking-footer">
        <div>&copy; {{ date('Y') }} Secure Licences Pty Ltd · <a href="{{ route('terms') }}" class="text-muted">Terms</a> · <a href="{{ route('privacy') }}" class="text-muted">Privacy</a> · <a href="{{ route('contact') }}" class="text-muted">Support</a></div>
    </footer>

    @stack('scripts')
</body>
</html>
