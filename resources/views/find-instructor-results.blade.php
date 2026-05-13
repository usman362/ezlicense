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

    {{-- Checked, Verified, Trusted --}}
    <section class="py-4 py-lg-5 bg-light rounded-4 mt-4 mb-4">
        <div class="container">
            <h2 class="h5 fw-bold mb-3">Checked, Verified, Trusted!</h2>
            <p class="text-muted small mb-2">All instructors are:</p>
            <ul class="list-unstyled small mb-0">
                <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Home-grown verified Working with Children Check</li>
                <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Independently Insured</li>
                <li class="mb-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Professional and safe driving instructors</li>
            </ul>
        </div>
    </section>
</div>

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
            suburbId: {{ json_encode($suburb_id) }},
            transmission: {{ json_encode($transmission) }},
            testPreBooked: {{ $test_pre_booked ? 'true' : 'false' }},
            locationLabel: {{ json_encode($q) }},
        };
        window.isLearner = {{ auth()->check() && auth()->user()->isLearner() ? 'true' : 'false' }};
        window.learnerBookingNewUrl = "{{ auth()->check() && auth()->user()->isLearner() ? route('learner.bookings.new') : '' }}";
    </script>
    @vite('resources/js/find-instructor-results.js')
@endpush
