<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') – Secure Licences</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        html, body { height: 100%; margin: 0; }
        body { background: var(--sl-gray-50); }

        .auth-split { display: flex; min-height: 100vh; }

        /* ===== LEFT (branding) ===== */
        .auth-split-left {
            flex: 0 0 50%;
            background: linear-gradient(135deg, var(--sl-primary-700) 0%, var(--sl-primary-900) 50%, var(--sl-gray-900) 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        /* Decorative glows */
        .auth-split-left::before {
            content: '';
            position: absolute;
            top: -150px;
            right: -150px;
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(245,158,11,0.25) 0%, transparent 65%);
            pointer-events: none;
        }
        .auth-split-left::after {
            content: '';
            position: absolute;
            bottom: -180px;
            left: -120px;
            width: 440px;
            height: 440px;
            background: radial-gradient(circle, rgba(59,130,246,0.3) 0%, transparent 65%);
            pointer-events: none;
        }

        .auth-brand-logo {
            position: relative;
            z-index: 2;
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            letter-spacing: -0.025em;
        }
        .auth-brand-logo .ez-l {
            width: 42px; height: 42px;
            background: var(--sl-accent-500);
            color: var(--sl-gray-900);
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 3px;
            font-size: 1.4rem;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(245,158,11,0.4);
        }

        .auth-brand-content {
            position: relative;
            z-index: 2;
            max-width: 480px;
        }
        .auth-brand-eyebrow {
            display: inline-block;
            font-size: 0.75rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--sl-accent-400);
            font-weight: 700;
            padding: 0.4rem 0.9rem;
            background: rgba(245,158,11,0.15);
            border: 1px solid rgba(245,158,11,0.25);
            border-radius: 999px;
            margin-bottom: 1.25rem;
        }
        .auth-brand-headline {
            font-size: clamp(1.75rem, 3vw, 2.75rem);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.03em;
            color: #fff;
            margin-bottom: 1rem;
        }
        .auth-brand-headline .highlight { color: var(--sl-accent-400); }
        .auth-brand-sub {
            font-size: 1rem;
            line-height: 1.55;
            color: rgba(255,255,255,0.75);
            margin-bottom: 2rem;
        }
        .auth-brand-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .auth-brand-features li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
            color: rgba(255,255,255,0.9);
            font-size: 0.95rem;
        }
        .auth-brand-features i {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            color: var(--sl-accent-400);
            flex-shrink: 0;
            font-size: 0.85rem;
        }

        .auth-brand-footer {
            position: relative;
            z-index: 2;
            color: rgba(255,255,255,0.6);
            font-size: var(--sl-text-xs);
        }
        .auth-brand-footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }
        .auth-brand-footer a:hover { color: #fff; }

        /* ===== RIGHT (form) ===== */
        .auth-split-right {
            flex: 1;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
        }
        .auth-form-card {
            width: 100%;
            max-width: 440px;
            text-align: left;
        }
        .auth-form-title {
            font-size: 1.875rem;
            font-weight: 800;
            color: var(--sl-gray-900);
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }
        .auth-form-subtitle {
            color: var(--sl-gray-500);
            margin-bottom: 2rem;
            font-size: var(--sl-text-sm);
        }
        .auth-input-wrap {
            position: relative;
            margin-bottom: 1.25rem;
        }
        .auth-input-wrap .form-control {
            padding-left: 2.875rem;
            height: 52px;
            border: 1px solid var(--sl-gray-200);
            border-radius: var(--sl-radius-md);
            background: var(--sl-gray-50);
            font-size: 0.95rem;
            transition: all var(--sl-transition);
        }
        .auth-input-wrap .form-control:focus {
            border-color: var(--sl-primary-500);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(59,130,246,0.15);
        }
        .auth-input-wrap i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--sl-gray-400);
            font-size: 1.05rem;
            pointer-events: none;
            transition: color var(--sl-transition);
        }
        .auth-input-wrap .form-control:focus + i,
        .auth-input-wrap:focus-within i {
            color: var(--sl-primary-600);
        }

        .auth-btn-login {
            width: 100%;
            height: 52px;
            background: var(--sl-primary-600);
            color: #fff;
            border: none;
            font-weight: 700;
            font-size: 1rem;
            border-radius: var(--sl-radius-md);
            transition: all var(--sl-transition);
            box-shadow: var(--sl-shadow-sm);
        }
        .auth-btn-login:hover {
            background: var(--sl-primary-700);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: var(--sl-shadow-primary);
        }
        .auth-btn-login:active { transform: translateY(0); }

        .auth-remember .form-check-input:checked {
            background-color: var(--sl-primary-600);
            border-color: var(--sl-primary-600);
        }
        .auth-forgot {
            font-size: 0.875rem;
            color: var(--sl-primary-600);
            text-decoration: none;
            font-weight: 600;
        }
        .auth-forgot:hover {
            color: var(--sl-primary-700);
            text-decoration: underline;
        }
        .auth-register-link {
            color: var(--sl-primary-600);
            font-weight: 700;
            text-decoration: none;
        }
        .auth-register-link:hover {
            color: var(--sl-primary-700);
            text-decoration: underline;
        }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--sl-gray-400);
            font-size: var(--sl-text-sm);
            margin: 1.5rem 0;
        }
        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            border-top: 1px solid var(--sl-gray-200);
        }

        @media (max-width: 991.98px) {
            .auth-split { flex-direction: column; }
            .auth-split-left { min-height: 260px; padding: 2rem; }
            .auth-split-right { padding: 2rem 1.5rem; }
            .auth-brand-features { display: none; }
            .auth-brand-headline { font-size: 1.5rem; }
            .auth-brand-sub { font-size: 0.875rem; margin-bottom: 0.5rem; }
        }
    </style>
</head>
<body>
    <div class="auth-split">
        <div class="auth-split-left">
            <a href="{{ url('/') }}" class="auth-brand-logo">
                <span>Secure</span><span class="ez-l">L</span><span>icences</span>
            </a>

            <div class="auth-brand-content">
                <span class="auth-brand-eyebrow"><i class="bi bi-star-fill me-1"></i>Australia's #1 Platform</span>
                <h1 class="auth-brand-headline">
                    Learn to drive with <span class="highlight">confidence</span>.
                </h1>
                <p class="auth-brand-sub">
                    Join 100,000+ learners who found their perfect instructor on Secure Licences.
                </p>
                <ul class="auth-brand-features">
                    <li><i class="bi bi-check-lg"></i><span>Verified, WWCC-checked instructors in every suburb</span></li>
                    <li><i class="bi bi-check-lg"></i><span>Instant online booking with real-time availability</span></li>
                    <li><i class="bi bi-check-lg"></i><span>Transparent pricing, no hidden fees</span></li>
                    <li><i class="bi bi-check-lg"></i><span>Switch instructors any time — no questions asked</span></li>
                </ul>
            </div>

            <div class="auth-brand-footer">
                <a href="{{ route('contact') }}"><i class="bi bi-headset me-1"></i>Need help? Contact support</a>
            </div>
        </div>
        <div class="auth-split-right">
            <div class="auth-form-card">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
