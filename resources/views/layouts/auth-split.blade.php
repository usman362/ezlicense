<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') – Secure Licences</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        :root { --ez-accent: #f0ad4e; --ez-accent-dark: #ec971f; --ez-dark: #1b212c; }
        html, body { height: 100%; margin: 0; }
        .auth-split { display: flex; min-height: 100vh; }
        .auth-split-left { flex: 0 0 50%; background: #e9ecef; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem; position: relative; }
        .auth-split-right { flex: 1; background: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem; }
        .auth-logo { font-size: 2rem; font-weight: 700; color: #333; display: inline-flex; align-items: center; margin-bottom: 0.5rem; }
        .auth-logo .ez-l { width: 40px; height: 40px; background: var(--ez-accent); color: #333; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; margin: 0 2px; font-size: 1.25rem; }
        .auth-tagline { font-size: 0.75rem; letter-spacing: 0.1em; color: #495057; text-transform: uppercase; margin-bottom: 0; }
        .auth-contacts { position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%); background: var(--ez-dark); color: #fff; border: none; padding: 0.5rem 1.5rem; border-radius: 6px; font-size: 0.875rem; text-decoration: none; }
        .auth-contacts:hover { color: #fff; background: #2c3e50; }
        .auth-form-card { width: 100%; max-width: 400px; text-align: left; }
        .auth-form-title { font-size: 1.75rem; font-weight: 700; color: #333; margin-bottom: 1.5rem; }
        .auth-input-wrap { position: relative; margin-bottom: 1.25rem; }
        .auth-input-wrap .form-control { padding-left: 2.75rem; height: 48px; border: 1px solid #dee2e6; border-radius: 6px; }
        .auth-input-wrap .form-control:focus { border-color: var(--ez-accent); box-shadow: 0 0 0 0.2rem rgba(240, 173, 78, 0.25); }
        .auth-input-wrap i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #6c757d; font-size: 1rem; pointer-events: none; }
        .auth-btn-login { width: 100%; height: 48px; background: var(--ez-accent); color: #333; border: none; font-weight: 600; font-size: 1rem; border-radius: 6px; }
        .auth-btn-login:hover { background: var(--ez-accent-dark); color: #333; }
        .auth-remember .form-check-input:checked { background-color: var(--ez-accent); border-color: var(--ez-accent); }
        .auth-forgot { font-size: 0.875rem; color: #495057; text-decoration: underline; }
        .auth-forgot:hover { color: #333; }
        .auth-register-link { color: var(--ez-accent); font-weight: 600; text-decoration: underline; }
        .auth-register-link:hover { color: var(--ez-accent-dark); }
        @media (max-width: 991.98px) {
            .auth-split { flex-direction: column; }
            .auth-split-left { min-height: 200px; padding: 2rem; }
            .auth-contacts { position: static; transform: none; margin-top: 1rem; }
        }
    </style>
</head>
<body>
    <div class="auth-split">
        <div class="auth-split-left">
            <a href="{{ url('/') }}" class="auth-logo text-decoration-none">
                <span class="text-dark">Secure</span><span class="ez-l">L</span><span class="text-dark">icences</span>
            </a>
            <p class="auth-tagline">Learn safe. Learn easy.</p>
            <a href="#" class="auth-contacts">Contacts</a>
        </div>
        <div class="auth-split-right">
            <div class="auth-form-card">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
