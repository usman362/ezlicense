@extends('layouts.frontend')

@section('content')
<div class="container py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('find-instructor') }}">Search</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $q ?: 'Results' }}</li>
        </ol>
    </nav>

    {{-- Quick filters + Filter / Sort --}}
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <div class="d-flex flex-wrap gap-2">
            <span class="badge rounded-pill bg-light text-dark border py-2 px-3">High Rated</span>
            <span class="badge rounded-pill bg-light text-dark border py-2 px-3">Good Value</span>
            <span class="badge rounded-pill bg-light text-dark border py-2 px-3">Best Match</span>
            <span class="badge rounded-pill bg-light text-dark border py-2 px-3">Instant Book</span>
            <span class="badge rounded-pill bg-light text-dark border py-2 px-3">Flexible Days</span>
            <span class="badge rounded-pill bg-light text-dark border py-2 px-3">Female Instructor</span>
        </div>
        <div class="ms-auto d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm">Filter</button>
            <button type="button" class="btn btn-outline-secondary btn-sm">Sort</button>
        </div>
    </div>

    {{-- Heading --}}
    <h2 class="h4 fw-bold mb-1" id="results-heading" style="display:none;"></h2>
    <p class="text-muted small mb-3" id="results-from-price" style="display:none;"></p>

    {{-- Instructor grid --}}
    <div id="results" class="row g-3 mb-4"></div>
    <div id="results-loading" class="text-center py-5">Loading instructors…</div>
    <div id="results-empty" class="text-muted py-5" style="display:none;">No instructors found. <a href="{{ route('find-instructor') }}">Try a different search</a>.</div>

    {{-- More instructors (optional second row - same data for now) --}}
    <div id="more-section" class="mb-5" style="display:none;">
        <h3 class="h5 fw-bold mb-3">More Instructors Available</h3>
        <div id="more-results" class="row g-3"></div>
    </div>

    {{-- Checked, Verified, Trusted --}}
    <section class="py-4 py-lg-5 bg-light rounded-3 mb-4">
        <div class="container">
            <h2 class="h5 fw-bold mb-3">Checked, Verified, Trusted!</h2>
            <p class="text-muted small mb-2">All instructors are:</p>
            <ul class="list-unstyled small mb-0">
                <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Home grown verified Working with Children Check</li>
                <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Independently Insured</li>
                <li class="mb-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Professional and safe tools</li>
            </ul>
        </div>
    </section>

    {{-- Learn to drive today CTA --}}
    <section class="py-4 text-center">
        <h2 class="h5 fw-bold mb-2">Learn to drive today!</h2>
        <p class="text-muted small mb-3">Join over 100,000+ learners driving with Secure Licences.</p>
        <a href="{{ route('find-instructor') }}" class="btn btn-warning fw-bold">Find an Instructor</a>
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
