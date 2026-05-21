{{--
    Settings page header — shared by every page in instructor/settings/*.
    Usage:
        @include('instructor.settings.partials.header', [
            'current'     => 'pricing',                  // route slug
            'title'       => 'Lesson & Package Pricing',
            'description' => 'Set your hourly rates and discount tiers for both Secure Licences and Private learners.',
            'icon'        => 'bi-tag-fill',              // optional
        ])
--}}
@php
    $sett_tabs = [
        ['slug' => 'personal-details',   'route' => 'instructor.settings.personal-details',   'label' => 'Personal',   'icon' => 'bi-person-fill'],
        ['slug' => 'profile',            'route' => 'instructor.settings.profile',            'label' => 'Profile',    'icon' => 'bi-card-text'],
        ['slug' => 'vehicle',            'route' => 'instructor.settings.vehicle',            'label' => 'Vehicle',    'icon' => 'bi-car-front-fill'],
        ['slug' => 'service-area',       'route' => 'instructor.settings.service-area',       'label' => 'Service Area','icon' => 'bi-geo-alt-fill'],
        ['slug' => 'opening-hours',      'route' => 'instructor.settings.opening-hours',      'label' => 'Hours',      'icon' => 'bi-clock-fill'],
        ['slug' => 'calendar-settings',  'route' => 'instructor.settings.calendar-settings',  'label' => 'Calendar',   'icon' => 'bi-calendar-week-fill'],
        ['slug' => 'pricing',            'route' => 'instructor.settings.pricing',            'label' => 'Pricing',    'icon' => 'bi-tag-fill'],
        ['slug' => 'documents',          'route' => 'instructor.settings.documents',          'label' => 'Documents',  'icon' => 'bi-file-earmark-text-fill'],
        ['slug' => 'banking',            'route' => 'instructor.settings.banking',            'label' => 'Banking',    'icon' => 'bi-bank'],
        ['slug' => 'guide',              'route' => 'instructor.settings.guide',              'label' => 'Guide',      'icon' => 'bi-book-fill'],
    ];
    $sett_current = $current ?? '';
    $sett_currentTab = collect($sett_tabs)->firstWhere('slug', $sett_current) ?? $sett_tabs[0];
    $sett_icon = $icon ?? $sett_currentTab['icon'];
    $sett_title = $title ?? $sett_currentTab['label'];
    $sett_desc = $description ?? null;
@endphp

<div class="sett-page-header">
    <nav aria-label="breadcrumb" class="sett-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('instructor.settings.personal-details') }}">Settings</a></li>
            <li class="breadcrumb-item active">{{ $sett_currentTab['label'] }}</li>
        </ol>
    </nav>

    <div class="sett-header-row">
        <div class="sett-header-icon">
            <i class="bi {{ $sett_icon }}"></i>
        </div>
        <div class="sett-header-text">
            <h1 class="sett-header-title">{{ $sett_title }}</h1>
            @if($sett_desc)
                <p class="sett-header-desc">{{ $sett_desc }}</p>
            @endif
        </div>
    </div>
</div>

<div class="sett-tabs-wrap">
    <div class="sett-tabs">
        @foreach($sett_tabs as $tab)
            <a href="{{ route($tab['route']) }}"
               class="sett-tab {{ $sett_current === $tab['slug'] ? 'active' : '' }}">
                <i class="bi {{ $tab['icon'] }}"></i>
                <span>{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>
