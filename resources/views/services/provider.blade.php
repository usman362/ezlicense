@extends('layouts.frontend')

@section('title', $provider->business_name ?: $provider->user->name)

@section('content')
<div class="container py-5" style="max-width: 900px;">
    <a href="{{ route('services.browse', $provider->category->slug) }}" class="text-decoration-none small">&larr; Back to {{ $provider->category->name }}s</a>

    <div class="card shadow-sm mt-3">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h1 class="h2 fw-bold mb-1">{{ $provider->business_name ?: $provider->user->name }}</h1>
                    <p class="text-muted mb-1">{{ $provider->category->name }} &middot; {{ $provider->base_suburb }} {{ $provider->base_postcode }}</p>
                    @if($provider->years_experience)
                        <p class="text-muted small mb-0">{{ $provider->years_experience }} years experience</p>
                    @endif
                </div>
                <div class="text-end">
                    <div class="text-primary fw-bold fs-3">${{ number_format($provider->hourly_rate, 2) }}/hr</div>
                    @if($provider->callout_fee > 0)
                        <div class="text-muted small">+ ${{ number_format($provider->callout_fee, 2) }} call-out</div>
                    @endif
                </div>
            </div>

            <hr>

            <h2 class="h5 fw-semibold mb-2">About</h2>
            <p style="white-space: pre-line;">{{ $provider->service_description ?: $provider->bio }}</p>

            <h2 class="h5 fw-semibold mt-4 mb-2">Service Area</h2>
            <p>Within {{ $provider->service_radius_km }}km of {{ $provider->base_suburb }}</p>

            @auth
                <a href="{{ route('service-bookings.create', $provider) }}" class="btn btn-primary btn-lg mt-3">
                    Book this {{ $provider->category->name }}
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg mt-3">Log in to book</a>
            @endauth
        </div>
    </div>
</div>
@endsection
