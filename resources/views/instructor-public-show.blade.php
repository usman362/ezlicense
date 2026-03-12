@extends('layouts.frontend')

@section('title', ($instructorProfile->user->name ?? 'Instructor') . ' — Driving Instructor')

@section('content')
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('find-instructor') }}">Find Instructor</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $instructorProfile->user->name ?? 'Instructor' }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Instructor Header --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex gap-4 flex-wrap">
                        {{-- Avatar --}}
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:100px;height:100px;background:var(--ez-accent);font-size:2.5rem;font-weight:700;color:#333;">
                                {{ strtoupper(substr($instructorProfile->user->first_name ?? $instructorProfile->user->name ?? 'I', 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h1 class="h3 fw-bold mb-1">{{ $instructorProfile->user->name ?? 'Instructor' }}</h1>
                            <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                                <span class="text-warning">
                                    @for($i = 0; $i < 5; $i++)
                                        <i class="bi bi-star-fill"></i>
                                    @endfor
                                </span>
                                <span class="text-muted small">({{ $instructorProfile->reviews_count ?? 0 }} reviews)</span>
                                @if($instructorProfile->verification_status === 'verified')
                                    <span class="badge bg-success"><i class="bi bi-patch-check me-1"></i>Verified</span>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-3 small text-muted">
                                <span><i class="bi bi-geo-alt me-1"></i>{{ $instructorProfile->serviceAreas->pluck('name')->take(3)->join(', ') }}{{ $instructorProfile->serviceAreas->count() > 3 ? ' +' . ($instructorProfile->serviceAreas->count() - 3) . ' more' : '' }}</span>
                                @if($instructorProfile->languages)
                                    <span><i class="bi bi-translate me-1"></i>{{ is_array($instructorProfile->languages) ? implode(', ', $instructorProfile->languages) : $instructorProfile->languages }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- About --}}
            @if($instructorProfile->bio)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">About {{ $instructorProfile->user->first_name ?? 'this instructor' }}</h2>
                    <p class="mb-0">{{ $instructorProfile->bio }}</p>
                </div>
            </div>
            @endif

            {{-- Vehicle Details --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3"><i class="bi bi-car-front me-2 text-warning"></i>Vehicle Details</h2>
                    <div class="row g-3">
                        @if($instructorProfile->vehicle_make)
                        <div class="col-6 col-md-3">
                            <div class="small text-muted">Make</div>
                            <div class="fw-semibold">{{ $instructorProfile->vehicle_make }}</div>
                        </div>
                        @endif
                        @if($instructorProfile->vehicle_model)
                        <div class="col-6 col-md-3">
                            <div class="small text-muted">Model</div>
                            <div class="fw-semibold">{{ $instructorProfile->vehicle_model }}</div>
                        </div>
                        @endif
                        @if($instructorProfile->vehicle_year)
                        <div class="col-6 col-md-3">
                            <div class="small text-muted">Year</div>
                            <div class="fw-semibold">{{ $instructorProfile->vehicle_year }}</div>
                        </div>
                        @endif
                        @if($instructorProfile->vehicle_safety_rating)
                        <div class="col-6 col-md-3">
                            <div class="small text-muted">Safety Rating</div>
                            <div class="fw-semibold">
                                @for($i = 0; $i < min((int)$instructorProfile->vehicle_safety_rating, 5); $i++)
                                    <i class="bi bi-star-fill text-warning"></i>
                                @endfor
                                ANCAP
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="mt-3 small">
                        <span class="badge bg-light text-dark border"><i class="bi bi-gear me-1"></i>{{ ucfirst($instructorProfile->transmission ?? 'Auto') }}</span>
                        <span class="badge bg-light text-dark border ms-1"><i class="bi bi-shield-check me-1"></i>Dual Controls</span>
                    </div>
                </div>
            </div>

            {{-- Service Areas --}}
            @if($instructorProfile->serviceAreas->isNotEmpty())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3"><i class="bi bi-geo-alt me-2 text-warning"></i>Service Areas</h2>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($instructorProfile->serviceAreas as $area)
                            <span class="badge bg-light text-dark border">{{ $area->name }} {{ $area->postcode }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Qualifications --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3"><i class="bi bi-patch-check me-2 text-warning"></i>Qualifications & Safety</h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Licensed driving instructor</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Valid Working With Children Check (WWCC)</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Dual-controlled vehicle</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Fully insured for driving instruction</li>
                        @if($instructorProfile->abn)
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Registered ABN: {{ $instructorProfile->abn }}</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        {{-- Sidebar —— Pricing & Booking --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 1rem;">
                <div class="card-body p-4">
                    <h3 class="h5 fw-bold mb-3">Lesson Pricing</h3>

                    {{-- 1hr Lesson --}}
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <div class="fw-semibold">1 Hour Lesson</div>
                            <div class="small text-muted">{{ ucfirst($instructorProfile->transmission ?? 'auto') }} transmission</div>
                        </div>
                        <div class="fw-bold fs-5" style="color: var(--ez-dark);">${{ number_format($instructorProfile->lesson_price ?? 0, 0) }}</div>
                    </div>

                    {{-- 2hr Lesson --}}
                    @if($instructorProfile->two_hour_lesson_price)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <div class="fw-semibold">2 Hour Lesson</div>
                            <div class="small text-muted">{{ ucfirst($instructorProfile->transmission ?? 'auto') }} transmission</div>
                        </div>
                        <div class="fw-bold fs-5" style="color: var(--ez-dark);">${{ number_format($instructorProfile->two_hour_lesson_price, 0) }}</div>
                    </div>
                    @endif

                    {{-- Test Package --}}
                    @if($instructorProfile->test_package_price)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <div class="fw-semibold">Test Package</div>
                            <div class="small text-muted">1hr warm-up + test + drop-off</div>
                        </div>
                        <div class="fw-bold fs-5" style="color: var(--ez-dark);">${{ number_format($instructorProfile->test_package_price, 0) }}</div>
                    </div>
                    @endif

                    <div class="mt-4">
                        @auth
                            @if(auth()->user()->isLearner())
                                <a href="{{ route('learner.bookings.new', ['instructor_profile_id' => $instructorProfile->id]) }}" class="btn btn-warning btn-lg w-100 fw-bold mb-2">
                                    <i class="bi bi-calendar-check me-1"></i> Book Online Now
                                </a>
                            @endif
                        @else
                            <a href="{{ route('learner.login') }}" class="btn btn-warning btn-lg w-100 fw-bold mb-2">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Log in to Book
                            </a>
                            <p class="small text-muted text-center mb-0">New to EzLicence? <a href="{{ route('register') }}">Create an account</a></p>
                        @endauth
                    </div>
                </div>

                {{-- Trust badges --}}
                <div class="card-footer bg-light p-3">
                    <div class="d-flex flex-wrap justify-content-center gap-3 small text-muted">
                        <span><i class="bi bi-shield-check text-success me-1"></i>Verified</span>
                        <span><i class="bi bi-lock text-success me-1"></i>Secure Booking</span>
                        <span><i class="bi bi-arrow-repeat text-success me-1"></i>Free Reschedule</span>
                    </div>
                </div>
            </div>

            {{-- Back to search --}}
            <div class="mt-3 text-center">
                <a href="{{ route('find-instructor') }}" class="text-muted small"><i class="bi bi-arrow-left me-1"></i>Back to search results</a>
            </div>
        </div>
    </div>
</div>
@endsection
