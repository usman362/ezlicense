@extends('layouts.frontend')

@section('content')
{{-- Hero search section --}}
<section class="py-4 py-lg-5" style="background: linear-gradient(180deg,#f8f9fa 0,#fff 100%);">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-lg-7">
                <h1 class="display-6 fw-bold mb-2">Where do you need driving lessons?</h1>
                <p class="text-muted mb-0">Search for trusted local instructors by suburb or postcode and book online in minutes.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10">
                <form action="{{ route('find-instructor.results') }}" method="get" id="find-instructor-form">
                    <input type="hidden" name="suburb_id" id="form-suburb-id" value="">
                    <input type="hidden" name="q" id="form-q" value="{{ request('q', '') }}">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label small text-muted">Pick-up Location <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="suburb-input" placeholder="Suburb or postcode" autocomplete="off" data-list-id="suburb-list" value="{{ request('q', '') }}">
                                    <ul id="suburb-list" class="list-group position-absolute w-100" style="display: none; z-index: 1000;"></ul>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">Transmission <span class="text-danger">*</span></label>
                                    <select class="form-select" name="transmission" id="transmission">
                                        <option value="">Any</option>
                                        <option value="auto" {{ request('transmission') === 'auto' ? 'selected' : '' }}>Auto</option>
                                        <option value="manual" {{ request('transmission') === 'manual' ? 'selected' : '' }}>Manual</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="test_pre_booked" value="1" id="test-pre-booked" {{ request('test_pre_booked') ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="test-pre-booked">Test pre-booked?</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-warning w-100 fw-bold" id="search-btn">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="d-flex flex-wrap align-items-center small text-muted gap-3">
                    <span><strong>100k+</strong> learners road‑ready</span>
                    <span>·</span>
                    <span><strong>1000+</strong> instructors</span>
                    <span>·</span>
                    <span><strong>3700+</strong> suburbs serviced</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Why book through EzLicence-style platform --}}
<section class="py-4 py-lg-5 bg-light">
    <div class="container">
        <h2 class="h4 fw-bold mb-3">Book driving lessons with confidence</h2>
        <div class="row g-4">
            <div class="col-md-3">
                <h6 class="fw-bold">Browse trusted instructors</h6>
                <p class="small text-muted mb-0">Compare profiles, ratings and prices to find an instructor who suits your needs.</p>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold">Book online in minutes</h6>
                <p class="small text-muted mb-0">See real‑time availability and secure your lesson instantly, 24/7.</p>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold">Flexible packages</h6>
                <p class="small text-muted mb-0">Choose single lessons, longer sessions or a test package with your instructor’s vehicle.</p>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold">Manage everything online</h6>
                <p class="small text-muted mb-0">Reschedule, change instructors and view your bookings from your learner dashboard.</p>
            </div>
        </div>
    </div>
</section>

{{-- Popular lessons & packages --}}
<section class="py-4">
    <div class="container">
        <h2 class="h4 fw-bold mb-3">Most popular options with learners</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-secondary align-self-start mb-2">Great value</span>
                        <h6 class="fw-bold">1 hour driving lesson</h6>
                        <p class="small text-muted mb-2">Perfect for regular practice and building skills over time.</p>
                        <p class="fw-bold mb-3">From $60.00 / hr</p>
                        <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-sm mt-auto">Book a lesson</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-danger align-self-start mb-2">Most popular</span>
                        <h6 class="fw-bold">2 hour driving lesson</h6>
                        <p class="small text-muted mb-2">Extra time in the car to cover more complex manoeuvres.</p>
                        <p class="fw-bold mb-3">From $120.00</p>
                        <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-sm mt-auto">Book a lesson</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="fw-bold">2.5 hour test package</h6>
                        <p class="small text-muted mb-2">Warm‑up lesson, use of instructor’s vehicle and drop‑off after the test.</p>
                        <p class="fw-bold mb-3">From $225.00</p>
                        <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-sm mt-auto">Book test package</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Why learners choose EzLicence-style platform --}}
<section class="py-4 py-lg-5">
    <div class="container">
        <h2 class="h4 fw-bold mb-3">Why learners choose Ez Licence</h2>
        <div class="row g-4 mb-3">
            <div class="col-md-4">
                <span class="display-6 fw-bold text-warning d-block">1000+</span>
                <p class="small mb-0">Driving instructors across Australia.</p>
            </div>
            <div class="col-md-4">
                <span class="display-6 fw-bold text-warning d-block">3700+</span>
                <p class="small mb-0">Suburbs serviced in metro and regional areas.</p>
            </div>
            <div class="col-md-4">
                <span class="display-6 fw-bold text-warning d-block">#1</span>
                <p class="small mb-0">Online booking platform for driving lessons.</p>
            </div>
        </div>
        <div class="row g-2 small">
            <div class="col-6 col-md-4 col-lg-3">✔ Choose your own instructor</div>
            <div class="col-6 col-md-4 col-lg-3">✔ View real‑time availability</div>
            <div class="col-6 col-md-4 col-lg-3">✔ Manage bookings online</div>
            <div class="col-6 col-md-4 col-lg-3">✔ Change instructors anytime</div>
            <div class="col-6 col-md-4 col-lg-3">✔ Auto &amp; manual available</div>
            <div class="col-6 col-md-4 col-lg-3">✔ Eligible for bonus logbook hours*</div>
        </div>
    </div>
</section>
@push('scripts')
    <script>
        (function() {
            var form = document.getElementById('find-instructor-form');
            var suburbInput = document.getElementById('suburb-input');
            var formSuburbId = document.getElementById('form-suburb-id');
            var formQ = document.getElementById('form-q');
            if (form && formSuburbId && formQ && suburbInput) {
                suburbInput.addEventListener('input', function() {
                    if (suburbInput.dataset.selected) {
                        delete suburbInput.dataset.selected;
                        return;
                    }
                    formSuburbId.value = '';
                    formQ.value = suburbInput.value;
                });
            }
        })();
    </script>
    @vite('resources/js/find-instructor.js')
@endpush
@endsection
