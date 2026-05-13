@extends('layouts.frontend')

@section('content')
<div class="container py-4 results-page">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted text-decoration-none"><i class="bi bi-house"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('find-instructor') }}" class="text-muted text-decoration-none">Search</a></li>
            <li class="breadcrumb-item active fw-semibold" aria-current="page">{{ $q ?: 'Results' }}</li>
        </ol>
    </nav>

    {{-- Quick filter pills (EzLicence-style: icon + label, scrollable on mobile) + Filters/Sort buttons on right --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 results-toolbar">
        <div class="filter-pills d-flex flex-nowrap flex-md-wrap align-items-start gap-3 overflow-auto pb-1">
            <button type="button" class="filter-pill" data-sort="rating" data-pill="highest_rated">
                <span class="filter-pill-icon"><i class="bi bi-star"></i></span>
                <span class="filter-pill-label">Highest<br>Rated</span>
            </button>
            <button type="button" class="filter-pill" data-sort="next_available" data-pill="next_available">
                <span class="filter-pill-icon"><i class="bi bi-calendar3"></i></span>
                <span class="filter-pill-label">Next<br>Available</span>
            </button>
            <button type="button" class="filter-pill" data-sort="price" data-pill="lowest_price">
                <span class="filter-pill-icon filter-pill-icon-money"><i class="bi bi-currency-dollar"></i></span>
                <span class="filter-pill-label">Lowest<br>Price</span>
            </button>
            <button type="button" class="filter-pill" data-filter="available_4_days" data-pill="available_4_days">
                <span class="filter-pill-icon"><i class="bi bi-calendar-week"></i></span>
                <span class="filter-pill-label">Available<br>Next 4 Days</span>
            </button>
            <button type="button" class="filter-pill" data-filter="female_only" data-pill="female_only">
                <span class="filter-pill-icon"><i class="bi bi-person"></i></span>
                <span class="filter-pill-label">Female<br>Instructor</span>
            </button>
        </div>
        <div class="d-flex gap-2 ms-md-auto results-toolbar-actions">
            <button type="button" class="btn btn-outline-secondary results-toolbar-btn" id="open-filters-btn">
                <i class="bi bi-sliders me-1"></i>Filters
                <span class="filter-badge ms-1" id="active-filter-count" style="display:none;">0</span>
            </button>
            <button type="button" class="btn btn-outline-secondary results-toolbar-btn" id="open-sort-btn">
                <i class="bi bi-arrow-down-up me-1"></i>Sort
            </button>
        </div>
    </div>

    {{-- Heading --}}
    <div class="mb-4" id="results-header">
        <h2 class="fw-bolder mb-1" id="results-heading" style="display:none; font-size:1.85rem; letter-spacing:-0.02em;"></h2>
        <p class="text-muted mb-0" id="results-from-price" style="display:none; font-size:1rem;"></p>
    </div>

    {{-- Instructor grid (4 cols desktop, 1 col mobile) --}}
    <div id="results" class="row g-3 mb-4"></div>
    <div id="results-loading" class="text-center py-5 text-muted">
        <div class="spinner-border text-warning mb-2" role="status"><span class="visually-hidden">Loading…</span></div>
        <div>Loading instructors…</div>
    </div>
    <div id="results-empty" class="text-muted py-5 text-center" style="display:none;">
        <i class="bi bi-search fs-1 d-block mb-3"></i>
        <p class="mb-2">No instructors found.</p>
        <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-sm fw-bold">Try a different search</a>
    </div>

</div>

{{-- Checked, Verified, Trusted! — with illustration --}}
<section class="cvt-section py-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-5 text-center">
                <svg viewBox="0 0 240 200" xmlns="http://www.w3.org/2000/svg" class="cvt-illustration" aria-hidden="true">
                    {{-- Simple flat-line illustration: instructor with clipboard --}}
                    <g fill="none" stroke="#1f2937" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="120" cy="55" r="22"/>
                        <path d="M105 50 Q115 45 135 50"/>
                        <circle cx="113" cy="55" r="1.5" fill="#1f2937"/>
                        <circle cx="127" cy="55" r="1.5" fill="#1f2937"/>
                        <path d="M114 64 Q120 67 126 64"/>
                        {{-- Body --}}
                        <path d="M90 85 Q90 80 100 78 L140 78 Q150 80 150 85 L150 160 L90 160 Z"/>
                        {{-- Arms --}}
                        <path d="M90 100 L70 130"/>
                        <path d="M150 100 L175 130"/>
                        {{-- Clipboard --}}
                        <rect x="155" y="100" width="48" height="62" rx="3" fill="#fef3c7" stroke="#1f2937"/>
                        <line x1="163" y1="115" x2="195" y2="115"/>
                        <line x1="163" y1="125" x2="195" y2="125"/>
                        <line x1="163" y1="135" x2="190" y2="135"/>
                        {{-- Check marks --}}
                        <polyline points="165,115 168,118 172,113" stroke="#10b981" fill="none"/>
                        <polyline points="165,125 168,128 172,123" stroke="#10b981" fill="none"/>
                    </g>
                </svg>
            </div>
            <div class="col-md-7">
                <h2 class="fw-bolder mb-3" style="font-size: 1.85rem; letter-spacing:-0.02em;">Checked, Verified, Trusted!</h2>
                <p class="text-muted mb-3" style="font-size: 1rem;">All instructors:</p>
                <ul class="list-unstyled mb-0 cvt-checks">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Have a valid Working with Children Check</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Are fully insured</li>
                    <li class="mb-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Have dual controls</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- Learn to drive today! — search CTA section --}}
<section class="learn-cta-section py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bolder mb-2" style="font-size: 1.85rem; letter-spacing:-0.02em;">Learn to drive today!</h2>
            <p class="text-muted mb-0">Join over 100,000+ learners driving with Secure Licences.</p>
        </div>
        <form action="{{ route('find-instructor.results') }}" method="get" id="cta-find-form" class="row g-3 align-items-end">
            <input type="hidden" name="suburb_id" id="cta-suburb-id" value="">
            <input type="hidden" name="q" id="cta-q" value="">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold mb-1">Pick-up Location <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="cta-suburb-input" placeholder="Enter your suburb" autocomplete="off" data-list-id="cta-suburb-list">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold mb-1">Transmission <span class="text-danger">*</span></label>
                <select name="transmission" class="form-select">
                    <option value="">Auto</option>
                    <option value="auto">Auto</option>
                    <option value="manual">Manual</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold mb-1">Test pre-booked?</label>
                <input type="date" name="test_date" class="form-control">
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-warning fw-bold w-100"><i class="bi bi-search me-1"></i>Search</button>
            </div>
        </form>
    </div>
</section>

{{-- Availability modal --}}
<div class="modal fade" id="availabilityModal" tabindex="-1" aria-labelledby="availabilityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="availabilityModalLabel">To begin the booking process please select 'Book with <span id="availability-instructor-heading"></span>'.</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="availability-loading" class="text-center py-3">Loading availability…</div>
                <div id="availability-content" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning fw-bold" id="availability-book-btn">
                    Book with <span id="availability-instructor-name"></span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Sort modal --}}
<div class="modal fade" id="sortModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Sort by</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-0">
                <div class="list-group list-group-flush">
                    <button type="button" class="list-group-item list-group-item-action sort-option" data-sort="best_match"><i class="bi bi-stars me-2"></i>Best match</button>
                    <button type="button" class="list-group-item list-group-item-action sort-option" data-sort="rating"><i class="bi bi-star-fill me-2 text-warning"></i>Highest rated</button>
                    <button type="button" class="list-group-item list-group-item-action sort-option" data-sort="price"><i class="bi bi-currency-dollar me-2 text-success"></i>Lowest price</button>
                    <button type="button" class="list-group-item list-group-item-action sort-option" data-sort="price_high"><i class="bi bi-currency-dollar me-2"></i>Highest price</button>
                    <button type="button" class="list-group-item list-group-item-action sort-option" data-sort="experience"><i class="bi bi-clock-history me-2"></i>Most experience</button>
                    <button type="button" class="list-group-item list-group-item-action sort-option" data-sort="lessons"><i class="bi bi-graph-up me-2"></i>Most lessons completed</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        window.findInstructorResultsParams = {
            suburbId: {!! json_encode($suburb_id) !!},
            transmission: {!! json_encode($transmission) !!},
            testPreBooked: {{ $test_pre_booked ? 'true' : 'false' }},
            locationLabel: {!! json_encode($q) !!},
        };
        window.isLearner = {{ auth()->check() && auth()->user()->isLearner() ? 'true' : 'false' }};
        window.learnerBookingNewUrl = "{{ auth()->check() && auth()->user()->isLearner() ? route('learner.bookings.new') : '' }}";
    </script>
    @vite('resources/js/find-instructor-results.js')
@endpush
