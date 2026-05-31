<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Help Center') · Secure Licence Support</title>
    @if(View::hasSection('meta_description'))
        <meta name="description" content="@yield('meta_description')">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sl-yellow: #ffd500;
            --sl-yellow-dark: #f59e0b;
            --sl-ink: #121110;
            --sl-gray-50: #f9fafb;
            --sl-gray-100: #f3f4f6;
            --sl-gray-200: #e5e7eb;
            --sl-gray-500: #6b7280;
            --sl-gray-700: #374151;
            --sl-gray-900: #111827;
        }
        body { background: var(--sl-gray-50); color: var(--sl-gray-900); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }

        /* ── Top header ── */
        .sup-header { background: #fff; border-bottom: 1px solid var(--sl-gray-200); padding: 16px 0; }
        .sup-header .brand-logo { font-size: 22px; font-weight: 800; color: var(--sl-gray-900); text-decoration: none; }
        .sup-header .brand-logo .l-badge { background: var(--sl-yellow); color: var(--sl-ink); padding: 2px 8px; border-radius: 4px; margin: 0 1px; font-weight: 800; }
        .sup-header .brand-logo:hover { color: var(--sl-gray-900); }
        .sup-header .brand-suffix { color: var(--sl-gray-500); font-weight: 500; margin-left: 6px; font-size: 14px; }
        .sup-header .contact-btn { background: var(--sl-yellow); border-color: var(--sl-yellow); color: var(--sl-ink); font-weight: 700; }
        .sup-header .contact-btn:hover { background: var(--sl-yellow-dark); border-color: var(--sl-yellow-dark); color: #fff; }

        /* ── Hero ── */
        .sup-hero { background: linear-gradient(135deg, #2c2c2c, #1a1a1a); color: #fff; padding: 60px 0; }
        .sup-hero h1 { font-size: 38px; font-weight: 800; margin-bottom: 8px; }
        .sup-hero .lead { color: rgba(255,255,255,0.75); font-size: 17px; }
        .sup-hero .search-box { max-width: 640px; margin: 28px auto 0; }
        .sup-hero .search-box .form-control { height: 56px; font-size: 16px; border: none; border-radius: 12px 0 0 12px; padding-left: 22px; }
        .sup-hero .search-box .btn { height: 56px; padding: 0 26px; font-weight: 700; background: var(--sl-yellow); border-color: var(--sl-yellow); color: var(--sl-ink); border-radius: 0 12px 12px 0; }
        .sup-hero .search-box .btn:hover { background: var(--sl-yellow-dark); border-color: var(--sl-yellow-dark); color: #fff; }

        /* ── Breadcrumbs ── */
        .sup-breadcrumb { background: #fff; padding: 16px 0; border-bottom: 1px solid var(--sl-gray-200); font-size: 14px; }
        .sup-breadcrumb a { color: var(--sl-gray-500); text-decoration: none; }
        .sup-breadcrumb a:hover { color: var(--sl-yellow-dark); }

        /* ── Cards & sections ── */
        .sup-section-title { font-size: 22px; font-weight: 700; color: var(--sl-gray-900); margin-bottom: 20px; }

        .cat-card { background: #fff; border: 1px solid var(--sl-gray-200); border-radius: 12px; padding: 28px; transition: all .15s; height: 100%; text-decoration: none; color: inherit; display: block; }
        .cat-card:hover { border-color: var(--sl-yellow); box-shadow: 0 6px 24px rgba(0,0,0,.08); transform: translateY(-2px); color: inherit; }
        .cat-card .cat-icon { width: 52px; height: 52px; background: var(--sl-yellow); color: var(--sl-ink); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 18px; }
        .cat-card h3 { font-size: 18px; font-weight: 700; margin-bottom: 8px; color: var(--sl-gray-900); }
        .cat-card p { color: var(--sl-gray-500); font-size: 14px; margin-bottom: 14px; }
        .cat-card .cat-meta { color: var(--sl-gray-500); font-size: 13px; }

        .sec-card { background: #fff; border: 1px solid var(--sl-gray-200); border-radius: 10px; padding: 20px; height: 100%; }
        .sec-card h4 { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
        .sec-card h4 i { color: var(--sl-yellow-dark); margin-right: 6px; }
        .sec-card .sec-articles a { display: block; padding: 6px 0; color: var(--sl-gray-700); text-decoration: none; font-size: 14px; border-bottom: 1px solid var(--sl-gray-100); }
        .sec-card .sec-articles a:last-child { border-bottom: none; }
        .sec-card .sec-articles a:hover { color: var(--sl-yellow-dark); }
        .sec-card .sec-more { display: inline-block; margin-top: 10px; font-size: 13px; font-weight: 600; color: var(--sl-yellow-dark); text-decoration: none; }

        .article-list-item { background: #fff; border: 1px solid var(--sl-gray-200); border-radius: 8px; padding: 14px 18px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; text-decoration: none; color: inherit; transition: border-color .15s; }
        .article-list-item:hover { border-color: var(--sl-yellow); color: inherit; }
        .article-list-item h5 { font-size: 16px; font-weight: 600; margin: 0; color: var(--sl-gray-900); }
        .article-list-item .arrow { color: var(--sl-gray-500); }

        /* ── Article body ── */
        .article-body { background: #fff; border-radius: 12px; padding: 40px; font-size: 16px; line-height: 1.75; color: var(--sl-gray-900); }
        .article-body h1, .article-body h2, .article-body h3 { font-weight: 700; margin-top: 1.4em; margin-bottom: 0.6em; color: var(--sl-gray-900); }
        .article-body h2 { font-size: 22px; }
        .article-body h3 { font-size: 18px; }
        .article-body p { margin-bottom: 1em; }
        .article-body ul, .article-body ol { padding-left: 1.6em; margin-bottom: 1em; }
        .article-body li { margin-bottom: 0.4em; }
        .article-body img { max-width: 100%; height: auto; border-radius: 8px; margin: 16px 0; border: 1px solid var(--sl-gray-200); }
        .article-body a { color: var(--sl-yellow-dark); text-decoration: underline; }

        /* ── Feedback widget ── */
        .feedback-box { background: var(--sl-gray-50); border: 1px solid var(--sl-gray-200); border-radius: 10px; padding: 20px; margin-top: 30px; text-align: center; }
        .feedback-box h6 { font-weight: 600; margin-bottom: 14px; }
        .feedback-box .btn { padding: 8px 22px; font-weight: 600; }

        /* ── Footer ── */
        .sup-footer { background: #1a1a1a; color: rgba(255,255,255,0.7); padding: 36px 0; margin-top: 60px; font-size: 14px; }
        .sup-footer a { color: rgba(255,255,255,0.85); text-decoration: none; }
        .sup-footer a:hover { color: var(--sl-yellow); }
    </style>
    @stack('head')
</head>
<body>

<header class="sup-header">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('support.home') }}" class="brand-logo">
            Secure<span class="l-badge">L</span>icence<span class="brand-suffix">Support</span>
        </a>
        <a href="{{ route('support.request.show') }}" class="btn contact-btn">
            <i class="bi bi-envelope-fill me-1"></i> Contact Us
        </a>
    </div>
</header>

@yield('hero')

@hasSection('breadcrumb')
<div class="sup-breadcrumb">
    <div class="container">
        <a href="{{ route('support.home') }}"><i class="bi bi-house"></i> Help Centre</a>
        @yield('breadcrumb')
    </div>
</div>
@endif

@if(session('message'))
    <div class="container mt-3"><div class="alert alert-success">{!! session('message') !!}</div></div>
@endif

<main class="container my-5">
    @yield('content')
</main>

<footer class="sup-footer">
    <div class="container d-flex justify-content-between flex-wrap gap-3">
        <div>
            <strong>Secure Licence</strong> · Help Centre<br>
            <a href="{{ url('/') }}">Main site</a> ·
            <a href="{{ route('support.request.show') }}">Contact us</a>
        </div>
        <div>© {{ date('Y') }} Secure Licence Pty Ltd</div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
