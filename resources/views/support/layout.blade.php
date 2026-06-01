<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Help Centre') · Secure Licence Support</title>
    @if(View::hasSection('meta_description'))
        <meta name="description" content="@yield('meta_description')">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ────────────── Reset + tokens ────────────── */
        *, *::before, *::after { box-sizing: border-box; }
        :root {
            --sl-yellow: #ffd500;
            --sl-yellow-dark: #f59e0b;
            --sl-ink: #121110;
            --sl-bg: #ffffff;
            --sl-surface: #f7f8fa;
            --sl-border: #e6e8eb;
            --sl-text: #1a1a1a;
            --sl-text-muted: #5e6470;
            --sl-text-on-dark: #ffffff;
            --sl-hero-bg: #1a1a1a;
            --sl-link: #1a73e8;
        }
        html, body { margin: 0; padding: 0; }
        body {
            background: var(--sl-bg);
            color: var(--sl-text);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 15px;
            line-height: 1.55;
            -webkit-font-smoothing: antialiased;
        }
        a { color: var(--sl-link); text-decoration: none; }
        a:hover { text-decoration: underline; }
        h1, h2, h3, h4 { color: var(--sl-text); font-weight: 700; margin: 0; line-height: 1.25; }

        .container {
            max-width: 980px;
            margin: 0 auto;
            padding: 0 24px;
        }
        .visibility-hidden {
            position: absolute !important; clip: rect(1px, 1px, 1px, 1px);
            width: 1px; height: 1px; overflow: hidden; padding: 0; border: 0;
        }

        /* ────────────── Header ────────────── */
        .header {
            border-bottom: 1px solid var(--sl-border);
            background: #fff;
            padding: 14px 0;
        }
        .header .header-inner {
            max-width: 980px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .header .brand-logo {
            font-size: 20px; font-weight: 800; color: var(--sl-ink); text-decoration: none; letter-spacing: -0.01em;
        }
        .header .brand-logo .l-badge {
            background: var(--sl-yellow); color: var(--sl-ink);
            padding: 2px 8px; border-radius: 4px; margin: 0 1px; font-weight: 800;
        }
        .header .brand-logo:hover { text-decoration: none; }
        .header .brand-suffix {
            color: var(--sl-text-muted); font-weight: 500; margin-left: 8px; font-size: 14px;
        }
        .header .submit-a-request {
            display: inline-block;
            background: var(--sl-yellow); color: var(--sl-ink);
            padding: 9px 18px; border-radius: 999px;
            font-weight: 700; font-size: 14px;
            text-decoration: none;
            transition: background .15s;
        }
        .header .submit-a-request:hover { background: var(--sl-yellow-dark); text-decoration: none; }

        /* ────────────── Hero with search ────────────── */
        .hero {
            background: var(--sl-hero-bg);
            color: var(--sl-text-on-dark);
            padding: 56px 24px 64px;
            text-align: center;
            position: relative;
        }
        .hero h2 {
            color: #fff; font-size: 36px; font-weight: 700;
            margin: 0 0 26px; letter-spacing: -0.01em;
        }
        .hero .search-wrap {
            max-width: 580px; margin: 0 auto;
            position: relative;
        }
        .hero .search-wrap input[type="search"] {
            width: 100%;
            height: 52px;
            padding: 0 20px 0 50px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            background: #fff;
            color: var(--sl-text);
            box-shadow: 0 4px 12px rgba(0,0,0,.08);
        }
        .hero .search-wrap input[type="search"]:focus {
            outline: 2px solid var(--sl-yellow);
        }
        .hero .search-wrap .search-icon {
            position: absolute; left: 18px; top: 50%; transform: translateY(-50%);
            color: var(--sl-text-muted);
            pointer-events: none;
        }

        /* ────────────── Sub-nav (breadcrumbs + inline search on inner pages) ────────────── */
        .sub-nav {
            display: flex; align-items: center; justify-content: space-between;
            padding: 18px 0;
            border-bottom: 1px solid var(--sl-border);
            font-size: 13px;
        }
        .breadcrumbs { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; gap: 6px; }
        .breadcrumbs li { color: var(--sl-text-muted); display: flex; align-items: center; gap: 6px; }
        .breadcrumbs li:not(:last-child)::after { content: "/"; color: var(--sl-text-muted); margin-left: 6px; }
        .breadcrumbs li a { color: var(--sl-text-muted); text-decoration: none; }
        .breadcrumbs li a:hover { color: var(--sl-ink); }
        .breadcrumbs li:last-child { color: var(--sl-ink); font-weight: 600; }
        .sub-nav .inline-search {
            position: relative;
        }
        .sub-nav .inline-search input[type="search"] {
            height: 36px; padding: 0 16px 0 38px;
            border: 1px solid var(--sl-border); border-radius: 6px;
            font-size: 14px; background: #fff; width: 240px;
        }
        .sub-nav .inline-search .search-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: var(--sl-text-muted); pointer-events: none;
        }

        /* ────────────── Section headings ────────────── */
        .section-block { padding: 40px 0; }
        .section-block h2 {
            font-size: 22px; margin: 0 0 24px;
        }

        /* ────────────── Categories blocks (home) ────────────── */
        .blocks-list {
            list-style: none; margin: 0; padding: 0;
            display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
        }
        .blocks-item-link {
            display: block;
            padding: 28px 26px;
            background: #fff;
            border: 1px solid var(--sl-border);
            border-radius: 8px;
            text-decoration: none; color: var(--sl-text);
            transition: border-color .15s, transform .15s, box-shadow .15s;
        }
        .blocks-item-link:hover {
            border-color: var(--sl-yellow);
            transform: translateY(-1px);
            text-decoration: none;
            box-shadow: 0 6px 18px rgba(0,0,0,.05);
        }
        .blocks-item-title {
            display: block; font-size: 18px; font-weight: 700; color: var(--sl-ink); margin-bottom: 6px;
        }
        .blocks-item-description {
            display: block; color: var(--sl-text-muted); font-size: 14px;
        }
        @media (max-width: 720px) { .blocks-list { grid-template-columns: 1fr; } }

        /* ────────────── Article list (promoted, section list) ────────────── */
        .article-list { list-style: none; margin: 0; padding: 0; }
        .article-list li {
            border-bottom: 1px solid var(--sl-border);
        }
        .article-list li:last-child { border-bottom: none; }
        .article-list li a {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 4px;
            color: var(--sl-ink); text-decoration: none;
            font-size: 15px;
        }
        .article-list li a:hover { color: var(--sl-yellow-dark); text-decoration: none; }
        .article-list li a::after {
            content: "›"; color: var(--sl-text-muted); font-size: 18px; margin-left: 8px;
        }

        /* ────────────── Category page — sections grid ────────────── */
        .sections-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; }
        @media (max-width: 720px) { .sections-grid { grid-template-columns: 1fr; } }
        .section-card-title {
            font-size: 17px; font-weight: 700; color: var(--sl-ink); margin: 0 0 12px;
        }
        .section-card-list { list-style: none; margin: 0; padding: 0; }
        .section-card-list li a {
            display: block; padding: 8px 0;
            color: var(--sl-link); font-size: 14.5px; text-decoration: none;
            border-bottom: 1px solid var(--sl-border);
        }
        .section-card-list li:last-child a { border-bottom: none; }
        .section-card-list li a:hover { text-decoration: underline; }
        .see-all-link {
            display: inline-block; margin-top: 10px;
            color: var(--sl-yellow-dark); font-size: 13.5px; font-weight: 600; text-decoration: none;
        }

        /* ────────────── Article detail (two-col) ────────────── */
        .article-container {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 48px;
            padding: 32px 0 64px;
        }
        @media (max-width: 860px) {
            .article-container { grid-template-columns: 1fr; gap: 24px; }
        }
        .article-sidebar h3 {
            font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;
            color: var(--sl-text-muted); margin: 0 0 14px; font-weight: 700;
        }
        .article-sidebar ul { list-style: none; margin: 0; padding: 0; }
        .article-sidebar li a {
            display: block; padding: 8px 0;
            color: var(--sl-text); font-size: 14px; text-decoration: none;
            border-bottom: 1px solid var(--sl-border);
        }
        .article-sidebar li a:hover { color: var(--sl-yellow-dark); }
        .article-sidebar li a.current {
            color: var(--sl-yellow-dark); font-weight: 700;
        }

        .article-content h1 {
            font-size: 28px; margin: 0 0 8px; font-weight: 800;
        }
        .article-content .article-meta {
            color: var(--sl-text-muted); font-size: 13px; margin-bottom: 24px;
        }
        .article-content .article-body { font-size: 15.5px; line-height: 1.7; }
        .article-content .article-body h2 { font-size: 20px; margin-top: 28px; }
        .article-content .article-body h3 { font-size: 17px; margin-top: 22px; }
        .article-content .article-body p { margin: 0 0 1em; }
        .article-content .article-body ul,
        .article-content .article-body ol { padding-left: 1.6em; margin-bottom: 1em; }
        .article-content .article-body img { max-width: 100%; height: auto; border-radius: 6px; margin: 12px 0; border: 1px solid var(--sl-border); }
        .article-content .article-body a { color: var(--sl-link); text-decoration: underline; }

        /* ────────────── Feedback widget ────────────── */
        .feedback-widget {
            margin-top: 40px; padding: 24px;
            background: var(--sl-surface); border: 1px solid var(--sl-border); border-radius: 6px;
            text-align: center;
        }
        .feedback-widget .feedback-title { font-size: 15px; font-weight: 600; margin-bottom: 14px; }
        .feedback-widget button {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 20px; margin: 0 4px;
            background: #fff; border: 1px solid var(--sl-border); border-radius: 999px;
            font-size: 14px; font-weight: 600; cursor: pointer; color: var(--sl-text);
            transition: all .15s;
        }
        .feedback-widget button:hover { border-color: var(--sl-ink); }
        .feedback-widget button:disabled { opacity: 0.5; cursor: not-allowed; }
        .feedback-widget .feedback-count { color: var(--sl-text-muted); font-size: 12.5px; margin-top: 10px; }

        /* ────────────── Search results page ────────────── */
        .search-results-summary {
            color: var(--sl-text-muted); padding: 22px 0; font-size: 14px;
        }
        .search-results-summary strong { color: var(--sl-ink); }

        /* ────────────── Empty state ────────────── */
        .empty-state {
            text-align: center; padding: 60px 24px;
            color: var(--sl-text-muted);
        }
        .empty-state i { font-size: 48px; opacity: 0.4; display: block; margin-bottom: 14px; }
        .empty-state h4 { font-size: 18px; color: var(--sl-text); margin-bottom: 6px; }

        /* ────────────── Submit-a-request form ────────────── */
        .request-form-wrap { max-width: 680px; margin: 0 auto; padding: 32px 0 64px; }
        .request-form-wrap h1 { font-size: 26px; margin-bottom: 6px; }
        .request-form-wrap .lead { color: var(--sl-text-muted); margin-bottom: 24px; }
        .request-form-wrap .form-group { margin-bottom: 18px; }
        .request-form-wrap label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--sl-text); }
        .request-form-wrap label .req { color: #d93025; }
        .request-form-wrap input, .request-form-wrap select, .request-form-wrap textarea {
            width: 100%; padding: 10px 14px;
            border: 1px solid var(--sl-border); border-radius: 6px;
            font-family: inherit; font-size: 14.5px; background: #fff;
        }
        .request-form-wrap input:focus, .request-form-wrap select:focus, .request-form-wrap textarea:focus {
            outline: none; border-color: var(--sl-yellow);
            box-shadow: 0 0 0 3px rgba(255, 213, 0, 0.18);
        }
        .request-form-wrap .row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .request-form-wrap .help-text { color: var(--sl-text-muted); font-size: 12.5px; margin-top: 4px; }
        .request-form-wrap .submit-btn {
            width: 100%;
            background: var(--sl-yellow); color: var(--sl-ink);
            border: none; padding: 12px 24px; border-radius: 6px;
            font-size: 15px; font-weight: 700; cursor: pointer;
            transition: background .15s;
        }
        .request-form-wrap .submit-btn:hover { background: var(--sl-yellow-dark); }
        .request-form-wrap .errors { background: #fde7e9; border: 1px solid #f5b7bd; color: #842029; padding: 12px 16px; border-radius: 6px; margin-bottom: 18px; font-size: 14px; }
        .request-form-wrap .errors ul { margin: 4px 0 0 18px; padding: 0; }
        .request-form-wrap .flash-success { background: #def7e3; border: 1px solid #b6e2c0; color: #1b5e2c; padding: 12px 16px; border-radius: 6px; margin-bottom: 18px; font-size: 14px; }

        /* ────────────── Footer ────────────── */
        .footer {
            border-top: 1px solid var(--sl-border);
            padding: 24px 0; margin-top: 40px;
            background: #fff;
        }
        .footer-inner {
            max-width: 980px; margin: 0 auto; padding: 0 24px;
            display: flex; justify-content: space-between; align-items: center;
            color: var(--sl-text-muted); font-size: 13px;
        }
        .footer-inner a { color: var(--sl-text-muted); text-decoration: none; }
        .footer-inner a:hover { color: var(--sl-ink); }

        @media (max-width: 720px) {
            .hero h2 { font-size: 26px; }
            .hero { padding: 40px 16px 48px; }
            .sub-nav { flex-direction: column; align-items: flex-start; gap: 12px; }
            .sub-nav .inline-search input[type="search"] { width: 100%; }
            .article-container { padding: 20px 0 40px; }
        }
    </style>
    @stack('head')
</head>
<body>

<header class="header">
    <div class="header-inner">
        <a href="{{ route('support.home') }}" class="brand-logo">
            Secure<span class="l-badge">L</span>icence<span class="brand-suffix">Support</span>
        </a>
        <a href="{{ route('support.request.show') }}" class="submit-a-request">Contact Us</a>
    </div>
</header>

@yield('hero')

@hasSection('subnav')
    <div class="container">
        <nav class="sub-nav">
            @yield('subnav')
        </nav>
    </div>
@endif

@if(session('message'))
    <div class="container" style="padding-top: 20px;">
        <div class="flash-success">{!! session('message') !!}</div>
    </div>
@endif

<main role="main">
    @yield('content')
</main>

<footer class="footer">
    <div class="footer-inner">
        <a href="{{ route('support.home') }}">Secure Licence Support</a>
        <span>© {{ date('Y') }} Secure Licence Pty Ltd</span>
    </div>
</footer>

@stack('scripts')
</body>
</html>
