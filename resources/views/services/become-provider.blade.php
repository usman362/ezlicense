@extends('layouts.frontend')

@section('title', 'Become a Service Provider')

@section('content')
<section class="text-white py-5" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
    <div class="container py-4 text-center" style="max-width: 800px;">
        <h1 class="display-4 fw-bold mb-3">Grow your trade business</h1>
        <p class="lead mb-4 opacity-90">Join SecureLicences as a plumber, electrician, cleaner or any other service professional. Get bookings from local customers — we handle the admin.</p>
        @auth
            <a href="{{ route('service-provider.onboarding.create') }}" class="btn btn-light btn-lg fw-bold px-5">Start your application →</a>
        @else
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-bold px-4">Sign up &amp; apply</a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg fw-bold px-4">Log in</a>
            </div>
        @endauth
    </div>
</section>

<div class="container py-5" style="max-width: 1100px;">
    <h2 class="h2 fw-bold text-center mb-5">How it works</h2>
    <div class="row g-4 mb-5">
        @foreach([
            ['1','Sign up','Create a free account in under 2 minutes.'],
            ['2','Apply','Pick your trade category and fill in your profile, rates and service area.'],
            ['3','Get approved','Our admin reviews and approves your listing (usually within 24 hours).'],
            ['4','Start earning','Receive bookings and get paid for every completed job.'],
        ] as $step)
            <div class="col-md-6 col-lg-3 text-center">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold fs-3 d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">{{ $step[0] }}</div>
                <h3 class="h5 fw-semibold">{{ $step[1] }}</h3>
                <p class="text-muted small">{{ $step[2] }}</p>
            </div>
        @endforeach
    </div>

    <h2 class="h2 fw-bold text-center mb-4">Categories we're hiring</h2>
    <div class="row g-3 mb-5">
        @foreach($categories as $cat)
            <div class="col-6 col-md-3">
                <div class="card border h-100 text-center">
                    <div class="card-body">
                        <div class="fs-2 mb-2"><i class="bi bi-tools text-primary"></i></div>
                        <div class="fw-medium">{{ $cat->name }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-center">
        @auth
            <a href="{{ route('service-provider.onboarding.create') }}" class="btn btn-success btn-lg px-5 fw-bold">Start your application →</a>
        @else
            <a href="{{ route('register') }}" class="btn btn-success btn-lg px-5 fw-bold">Sign up to apply →</a>
        @endauth
    </div>
</div>
@endsection
