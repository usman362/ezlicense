@extends('layouts.frontend')

@section('title', ($instructorProfile->user->name ?? 'Instructor') . ' — Driving Instructor')

@section('content')
{{-- Hero banner with profile overlay --}}
<div class="position-relative" style="background: linear-gradient(135deg, var(--sl-primary-700) 0%, var(--sl-primary-900) 50%, var(--sl-gray-900) 100%); overflow: hidden;">
    <div class="position-absolute" style="top:-120px; right:-120px; width:440px; height:440px; background: radial-gradient(circle, rgba(245,158,11,0.22) 0%, transparent 65%); pointer-events:none;"></div>
    <div class="position-absolute" style="bottom:-140px; left:-100px; width:400px; height:400px; background: radial-gradient(circle, rgba(20,184,166,0.22) 0%, transparent 65%); pointer-events:none;"></div>

    <div class="container position-relative py-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('find-instructor') }}" class="text-white-50 text-decoration-none">Find Instructor</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">{{ $instructorProfile->user->name ?? 'Instructor' }}</li>
            </ol>
        </nav>

        <div class="row g-4 align-items-center">
            <div class="col-md-auto">
                @if($instructorProfile->profile_photo)
                    <img src="{{ asset('storage/' . $instructorProfile->profile_photo) }}"
                         alt="{{ $instructorProfile->user->name }}"
                         class="rounded-circle"
                         style="width:140px;height:140px;object-fit:cover;border:5px solid rgba(255,255,255,0.25); box-shadow: var(--sl-shadow-2xl);">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bolder"
                         style="width:140px;height:140px;font-size:3.5rem;color:#fff;border:5px solid rgba(255,255,255,0.25); background: linear-gradient(135deg, var(--sl-accent-500), var(--sl-accent-600)); box-shadow: var(--sl-shadow-2xl);">
                        {{ strtoupper(substr($instructorProfile->user->first_name ?? $instructorProfile->user->name ?? 'I', 0, 1)) }}
                    </div>
                @endif
            </div>

            <div class="col">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    @if($instructorProfile->verification_status === 'verified')
                        <span class="badge px-3 py-2" style="background: rgba(16,185,129,0.2); color: #6ee7b7; border: 1px solid rgba(16,185,129,0.4); font-size: var(--sl-text-xs);">
                            <i class="bi bi-patch-check-fill me-1"></i>Verified Instructor
                        </span>
                    @endif
                    <span class="badge px-3 py-2" style="background: rgba(245,158,11,0.2); color: var(--sl-accent-400); border: 1px solid rgba(245,158,11,0.4); font-size: var(--sl-text-xs);">
                        <i class="bi bi-gear-fill me-1"></i>{{ ucfirst($instructorProfile->transmission ?? 'Auto') }}
                    </span>
                    @php $wwcc = !empty($instructorProfile->wwcc_number); @endphp
                    @if($wwcc)
                        <span class="badge px-3 py-2" style="background: rgba(20,184,166,0.2); color: #5eead4; border: 1px solid rgba(20,184,166,0.4); font-size: var(--sl-text-xs);">
                            <i class="bi bi-shield-check me-1"></i>WWCC Verified
                        </span>
                    @endif
                </div>

                <h1 class="display-5 fw-bolder text-white mb-2" style="letter-spacing:-0.03em;">{{ $instructorProfile->user->name ?? 'Instructor' }}</h1>

                @php
                    $avgRating = $instructorProfile->averageRating();
                    $reviewCount = $instructorProfile->reviewsCount();
                @endphp
                <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="sl-stars" style="font-size:1.1rem;">
                            @php $full = floor($avgRating); $half = ($avgRating - $full) >= 0.3 && ($avgRating - $full) < 0.8; @endphp
                            @for($i = 0; $i < 5; $i++)
                                @if($i < $full)
                                    <i class="bi bi-star-fill"></i>
                                @elseif($i === (int)$full && $half)
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="text-white fw-bold fs-5">{{ $avgRating > 0 ? number_format($avgRating, 1) : '—' }}</span>
                        <span class="text-white-50">({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</span>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-3 text-white-50" style="font-size: var(--sl-text-sm);">
                    @if($instructorProfile->serviceAreas->count() > 0)
                        <span><i class="bi bi-geo-alt-fill me-1" style="color: var(--sl-accent-400);"></i>{{ $instructorProfile->serviceAreas->pluck('name')->take(3)->join(', ') }}{{ $instructorProfile->serviceAreas->count() > 3 ? ' +' . ($instructorProfile->serviceAreas->count() - 3) . ' more' : '' }}</span>
                    @endif
                    @if($instructorProfile->languages)
                        <span><i class="bi bi-translate me-1" style="color: var(--sl-accent-400);"></i>{{ is_array($instructorProfile->languages) ? implode(', ', $instructorProfile->languages) : $instructorProfile->languages }}</span>
                    @endif
                    @if($instructorProfile->instructing_start_year)
                        <span><i class="bi bi-award-fill me-1" style="color: var(--sl-accent-400);"></i>Teaching since {{ $instructorProfile->instructing_start_year }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- About --}}
            @if($instructorProfile->bio)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h4 fw-bolder mb-3">About {{ $instructorProfile->user->first_name ?? 'this instructor' }}</h2>
                    <p class="mb-0" style="font-size: 1.05rem; line-height: 1.7; color: var(--sl-gray-700);">{{ $instructorProfile->bio }}</p>
                </div>
            </div>
            @endif

            {{-- Vehicle Details --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="icon-bubble icon-bubble-sm"><i class="bi bi-car-front-fill"></i></div>
                        <h2 class="h4 fw-bolder mb-0">Vehicle Details</h2>
                    </div>
                    @if($instructorProfile->vehicle_photo)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $instructorProfile->vehicle_photo) }}"
                                 alt="Vehicle"
                                 class="rounded-3"
                                 style="max-width:100%;max-height:280px;object-fit:cover; border: 1px solid var(--sl-gray-200); box-shadow: var(--sl-shadow-sm);">
                        </div>
                    @endif
                    <div class="row g-3">
                        @if($instructorProfile->vehicle_make)
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3" style="background: var(--sl-gray-50);">
                                <div class="small text-muted mb-1">Make</div>
                                <div class="fw-bold">{{ $instructorProfile->vehicle_make }}</div>
                            </div>
                        </div>
                        @endif
                        @if($instructorProfile->vehicle_model)
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3" style="background: var(--sl-gray-50);">
                                <div class="small text-muted mb-1">Model</div>
                                <div class="fw-bold">{{ $instructorProfile->vehicle_model }}</div>
                            </div>
                        </div>
                        @endif
                        @if($instructorProfile->vehicle_year)
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3" style="background: var(--sl-gray-50);">
                                <div class="small text-muted mb-1">Year</div>
                                <div class="fw-bold">{{ $instructorProfile->vehicle_year }}</div>
                            </div>
                        </div>
                        @endif
                        @if($instructorProfile->vehicle_safety_rating)
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3" style="background: var(--sl-gray-50);">
                                <div class="small text-muted mb-1">ANCAP Rating</div>
                                <div class="fw-bold">
                                    <span class="sl-stars">
                                        @for($i = 0; $i < min((int)$instructorProfile->vehicle_safety_rating, 5); $i++)
                                            <i class="bi bi-star-fill"></i>
                                        @endfor
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <span class="sl-chip"><i class="bi bi-gear-fill"></i>{{ ucfirst($instructorProfile->transmission ?? 'Auto') }}</span>
                        <span class="sl-chip"><i class="bi bi-shield-check"></i>Dual Controls Fitted</span>
                        <span class="sl-chip"><i class="bi bi-check-circle"></i>Roadworthy Certified</span>
                    </div>
                </div>
            </div>

            {{-- Service Areas --}}
            @if($instructorProfile->serviceAreas->isNotEmpty())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="icon-bubble icon-bubble-sm icon-bubble-teal"><i class="bi bi-geo-alt-fill"></i></div>
                        <h2 class="h4 fw-bolder mb-0">Service Areas <span class="text-muted fw-normal fs-6">({{ $instructorProfile->serviceAreas->count() }})</span></h2>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($instructorProfile->serviceAreas as $area)
                            <span class="sl-chip"><i class="bi bi-pin-map"></i>{{ $area->name }} {{ $area->postcode }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Qualifications --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="icon-bubble icon-bubble-sm icon-bubble-success"><i class="bi bi-patch-check-fill"></i></div>
                        <h2 class="h4 fw-bolder mb-0">Qualifications & Safety</h2>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 p-3 rounded-3 h-100" style="background: var(--sl-gray-50);">
                                <i class="bi bi-check-circle-fill text-success" style="font-size:1.25rem; margin-top:2px;"></i>
                                <div><strong>Licensed driving instructor</strong><br><small class="text-muted">Fully accredited in state</small></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 p-3 rounded-3 h-100" style="background: var(--sl-gray-50);">
                                <i class="bi bi-check-circle-fill text-success" style="font-size:1.25rem; margin-top:2px;"></i>
                                <div><strong>Working With Children Check</strong><br><small class="text-muted">WWCC verified and current</small></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 p-3 rounded-3 h-100" style="background: var(--sl-gray-50);">
                                <i class="bi bi-check-circle-fill text-success" style="font-size:1.25rem; margin-top:2px;"></i>
                                <div><strong>Dual-controlled vehicle</strong><br><small class="text-muted">Safe for learner drivers</small></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 p-3 rounded-3 h-100" style="background: var(--sl-gray-50);">
                                <i class="bi bi-check-circle-fill text-success" style="font-size:1.25rem; margin-top:2px;"></i>
                                <div><strong>Fully insured</strong><br><small class="text-muted">Comprehensive cover for instruction</small></div>
                            </div>
                        </div>
                        @if($instructorProfile->abn)
                        <div class="col-12">
                            <div class="d-flex align-items-start gap-2 p-3 rounded-3" style="background: var(--sl-gray-50);">
                                <i class="bi bi-check-circle-fill text-success" style="font-size:1.25rem; margin-top:2px;"></i>
                                <div><strong>Registered business</strong> <span class="text-muted">· ABN {{ $instructorProfile->abn }}</span></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar — Pricing & Booking --}}
        <div class="col-lg-4">
            <div class="card border-0 sticky-top" style="top: 1.5rem; box-shadow: var(--sl-shadow-xl); border-radius: var(--sl-radius-xl); overflow: hidden;">
                <div class="p-4" style="background: linear-gradient(135deg, var(--sl-primary-700), var(--sl-primary-900)); color: #fff;">
                    <div class="small mb-1" style="color: var(--sl-accent-400); font-weight:700; text-transform:uppercase; letter-spacing:0.08em;">Starting from</div>
                    <div class="d-flex align-items-baseline gap-1">
                        <span class="display-5 fw-bolder text-white">${{ number_format($instructorProfile->lesson_price ?? 0, 0) }}</span>
                        <span class="text-white-50">/hour</span>
                    </div>
                </div>

                <div class="card-body p-4">
                    <h6 class="fw-bold text-muted small text-uppercase mb-3" style="letter-spacing:0.08em;">Pricing Options</h6>

                    {{-- 1hr Lesson --}}
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <div class="fw-bold">1 Hour Lesson</div>
                            <div class="small text-muted">{{ ucfirst($instructorProfile->transmission ?? 'auto') }} transmission</div>
                        </div>
                        <div class="fw-bolder fs-4">${{ number_format($instructorProfile->lesson_price ?? 0, 0) }}</div>
                    </div>

                    @if($instructorProfile->two_hour_lesson_price ?? null)
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <div class="fw-bold">2 Hour Lesson</div>
                            <div class="small text-muted">Best value</div>
                        </div>
                        <div class="fw-bolder fs-4">${{ number_format($instructorProfile->two_hour_lesson_price, 0) }}</div>
                    </div>
                    @endif

                    @if($instructorProfile->test_package_price)
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <div class="fw-bold d-flex align-items-center gap-2">Test Package <span class="badge bg-warning text-dark" style="font-size:0.65rem;">POPULAR</span></div>
                            <div class="small text-muted">Warm-up + test + drop-off</div>
                        </div>
                        <div class="fw-bolder fs-4">${{ number_format($instructorProfile->test_package_price, 0) }}</div>
                    </div>
                    @endif

                    <div class="mt-4">
                        @auth
                            @if(auth()->user()->isLearner())
                                <a href="{{ route('learner.bookings.new', ['instructor_profile_id' => $instructorProfile->id]) }}" class="btn btn-primary btn-lg w-100 fw-bold">
                                    <i class="bi bi-calendar-check me-2"></i>Book a Lesson
                                </a>
                            @endif
                        @else
                            {{-- Guest booking: no login required — account auto-created after payment --}}
                            <a href="{{ route('learner.bookings.new', ['instructor_profile_id' => $instructorProfile->id]) }}" class="btn btn-primary btn-lg w-100 fw-bold">
                                <i class="bi bi-calendar-check me-2"></i>Book a Lesson
                            </a>
                            <p class="small text-muted text-center mb-0 mt-2">
                                Already a member? <a href="{{ route('learner.login') }}" class="fw-semibold">Log in</a>
                            </p>
                        @endauth
                    </div>
                </div>

                {{-- Trust badges --}}
                <div class="p-3 text-center" style="background: var(--sl-gray-50); border-top: 1px solid var(--sl-gray-200);">
                    <div class="d-flex flex-wrap justify-content-center gap-3 small">
                        <span class="text-muted"><i class="bi bi-shield-check-fill text-success me-1"></i>Verified</span>
                        <span class="text-muted"><i class="bi bi-lock-fill text-success me-1"></i>Secure Booking</span>
                        <span class="text-muted"><i class="bi bi-arrow-repeat text-success me-1"></i>Free Reschedule</span>
                    </div>
                </div>
            </div>

            <div class="mt-3 text-center">
                <a href="{{ route('find-instructor') }}" class="text-muted small text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Back to search results</a>
            </div>
        </div>
    </div>
</div>
@endsection
