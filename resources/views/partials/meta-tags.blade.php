{{--
    Site-wide SEO + social meta tags + analytics.
    Pulls from SiteSetting (admin-managed) with per-page overrides via @section.

    Per-page overrides:
        @section('meta_title', 'Custom Title')
        @section('meta_description', 'Custom description...')
        @section('meta_image', 'https://...')
        @section('meta_keywords', 'extra, keywords')
        @section('meta_robots', 'noindex,nofollow')
--}}
@php
    // Site-wide defaults from admin SiteSetting
    $siteName       = \App\Models\SiteSetting::get('site_name', config('app.name', 'Secure Licences'));
    $defaultTitle   = \App\Models\SiteSetting::get('seo_default_title', $siteName);
    $titleSuffix    = \App\Models\SiteSetting::get('seo_title_suffix', '');
    $defaultDesc    = \App\Models\SiteSetting::get('seo_default_description', '');
    $defaultKeys    = \App\Models\SiteSetting::get('seo_default_keywords', '');
    $defaultImage   = \App\Models\SiteSetting::get('seo_og_image', '');
    $twitterHandle  = \App\Models\SiteSetting::get('seo_twitter_handle', '');
    $canonicalHost  = rtrim((string) \App\Models\SiteSetting::get('seo_canonical_host', ''), '/');
    $robotsMode     = \App\Models\SiteSetting::get('seo_robots_mode', 'index_follow');
    $gaId           = \App\Models\SiteSetting::get('seo_google_analytics_id', '');
    $gscToken       = \App\Models\SiteSetting::get('seo_google_verification', '');
    $bingToken      = \App\Models\SiteSetting::get('seo_bing_verification', '');
    $fbPixelId      = \App\Models\SiteSetting::get('seo_facebook_pixel_id', '');
    $orgSchema      = \App\Models\SiteSetting::get('seo_org_schema_enabled', true);

    // Branding bits
    $faviconUrl     = \App\Models\SiteSetting::get('brand_favicon_url', '');
    $touchIconUrl   = \App\Models\SiteSetting::get('brand_touch_icon_url', '');
    $brandAddress   = \App\Models\SiteSetting::get('brand_address', '');
    $supportEmail   = \App\Models\SiteSetting::get('support_email', 'support@securelicences.com.au');
    $supportPhone   = \App\Models\SiteSetting::get('support_phone', '');
    $facebookUrl    = \App\Models\SiteSetting::get('facebook_url', '');
    $instagramUrl   = \App\Models\SiteSetting::get('instagram_url', '');

    // Per-page overrides (priority: meta_title > title section > admin default)
    // — falls back to the legacy `title` section so existing pages get auto-picked up.
    $pageTitle       = trim((string) (View::getSection('meta_title') ?: (View::getSection('title') ?: $defaultTitle)));
    $pageDescription = trim((string) (View::getSection('meta_description') ?: $defaultDesc));
    $pageImage       = trim((string) (View::getSection('meta_image') ?: $defaultImage));
    $pageKeywords    = trim((string) (View::getSection('meta_keywords') ?: $defaultKeys));
    $pageRobots      = trim((string) (View::getSection('meta_robots') ?: ''));

    // Build final <title> with suffix (avoid double-suffix if page already has it)
    if ($titleSuffix && ! str_ends_with($pageTitle, $titleSuffix)) {
        $fullTitle = $pageTitle . $titleSuffix;
    } else {
        $fullTitle = $pageTitle;
    }

    // Canonical URL — use admin-configured host if set, otherwise current request
    $canonicalUrl = $canonicalHost
        ? $canonicalHost . request()->getRequestUri()
        : url()->current();

    // Resolve robots directive
    if (! $pageRobots) {
        $pageRobots = $robotsMode === 'noindex_nofollow' ? 'noindex, nofollow' : 'index, follow, max-image-preview:large';
    }
@endphp

{{-- ─── Basic SEO ─── --}}
<title>{{ $fullTitle }}</title>
@if($pageDescription)
    <meta name="description" content="{{ $pageDescription }}">
@endif
@if($pageKeywords)
    <meta name="keywords" content="{{ $pageKeywords }}">
@endif
<meta name="robots" content="{{ $pageRobots }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

{{-- ─── Search-engine verification ─── --}}
@if($gscToken)
    <meta name="google-site-verification" content="{{ $gscToken }}">
@endif
@if($bingToken)
    <meta name="msvalidate.01" content="{{ $bingToken }}">
@endif

{{-- ─── Favicon + Apple touch icon ─── --}}
@if($faviconUrl)
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
@endif
@if($touchIconUrl)
    <link rel="apple-touch-icon" href="{{ $touchIconUrl }}">
@endif

{{-- ─── Open Graph (Facebook, LinkedIn, WhatsApp previews) ─── --}}
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="{{ View::getSection('meta_og_type') ?: 'website' }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:title" content="{{ $fullTitle }}">
@if($pageDescription)
    <meta property="og:description" content="{{ $pageDescription }}">
@endif
@if($pageImage)
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
@endif
<meta property="og:locale" content="en_AU">

{{-- ─── Twitter / X Card ─── --}}
<meta name="twitter:card" content="{{ $pageImage ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $fullTitle }}">
@if($pageDescription)
    <meta name="twitter:description" content="{{ $pageDescription }}">
@endif
@if($pageImage)
    <meta name="twitter:image" content="{{ $pageImage }}">
@endif
@if($twitterHandle)
    <meta name="twitter:site" content="{{ $twitterHandle }}">
    <meta name="twitter:creator" content="{{ $twitterHandle }}">
@endif

{{-- ─── Organisation Schema.org JSON-LD (helps Google's Knowledge Panel) ─── --}}
@if($orgSchema)
    @php
        $org = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => $siteName,
            'url'      => $canonicalHost ?: url('/'),
            'logo'     => $defaultImage ?: ($faviconUrl ?: null),
            'email'    => $supportEmail,
        ];
        if ($supportPhone) $org['telephone'] = $supportPhone;
        if ($brandAddress) {
            $org['address'] = [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $brandAddress,
                'addressCountry'  => 'AU',
            ];
        }
        $sameAs = array_filter([$facebookUrl, $instagramUrl, $twitterHandle ? 'https://twitter.com/'.ltrim($twitterHandle, '@') : null]);
        if (! empty($sameAs)) $org['sameAs'] = array_values($sameAs);
        $org = array_filter($org, fn ($v) => $v !== null && $v !== '');
    @endphp
    <script type="application/ld+json">{!! json_encode($org, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif

{{-- ─── Google Analytics (GA4) ─── --}}
@if($gaId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $gaId }}');
    </script>
@endif

{{-- ─── Facebook / Meta Pixel ─── --}}
@if($fbPixelId)
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $fbPixelId }}');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $fbPixelId }}&ev=PageView&noscript=1"/></noscript>
@endif
