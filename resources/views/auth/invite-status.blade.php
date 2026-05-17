@extends('layouts.auth-split')

@section('title', $heading)

@section('brand_content')
    <span class="auth-brand-eyebrow"><i class="bi bi-info-circle-fill me-1"></i>Invite status</span>
    <h1 class="auth-brand-headline">
        Need a fresh <span class="highlight">invitation?</span>
    </h1>
    <p class="auth-brand-sub">
        For security, instructor invites are single-use and expire after 7 days. Contact our team and we'll send you a new one straight away.
    </p>
    <ul class="auth-brand-features">
        <li><i class="bi bi-envelope"></i><span>Email <strong>instructors@securelicences.com.au</strong></span></li>
        <li><i class="bi bi-shield-check"></i><span>Single-use links protect your account from misuse</span></li>
        <li><i class="bi bi-clock"></i><span>New invites typically sent within 1 business day</span></li>
    </ul>
@endsection

@section('content')

@php
    $iconMap = [
        'not_found' => ['bi-question-circle-fill', '#ef4444'],
        'expired'   => ['bi-clock-history',         '#f59e0b'],
        'cancelled' => ['bi-x-circle-fill',          '#ef4444'],
        'accepted'  => ['bi-check-circle-fill',      '#10b981'],
    ];
    [$icon, $color] = $iconMap[$state] ?? ['bi-info-circle-fill', '#6b7280'];
@endphp

<div class="text-center">
    <div style="width: 88px; height: 88px; border-radius: 50%; background: {{ $color }}1a; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
        <i class="bi {{ $icon }}" style="font-size: 2.75rem; color: {{ $color }};"></i>
    </div>

    <h1 class="auth-form-title">{{ $heading }}</h1>
    <p class="text-muted mb-4">{{ $message }}</p>

    @if(! empty($loginUrl))
        <a href="{{ $loginUrl }}" class="btn auth-btn-login mb-3 d-inline-flex align-items-center justify-content-center gap-2" style="text-decoration: none;">
            <i class="bi bi-box-arrow-in-right"></i> Go to login
        </a>
    @endif

    <div class="mt-3">
        <a href="mailto:instructors@securelicences.com.au" class="auth-register-link">
            <i class="bi bi-envelope me-1"></i>Request a new invite
        </a>
    </div>
</div>

@endsection
