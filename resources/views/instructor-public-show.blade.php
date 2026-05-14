@extends('layouts.frontend')

@section('title', ($instructorProfile->user->name ?? 'Instructor') . ' — Driving Instructor')

@section('content')
@php
    $p          = $instructorProfile;
    $u          = $p->user;
    $firstName  = $u->first_name ?? explode(' ', $u->name)[0];
    $avgRating  = $p->averageRating();
    $reviewCount = $p->reviewsCount();
    $reviews    = $p->reviews()->where('status', 'approved')->where('is_hidden', false)
                    ->with('learner:id,name')->latest()->paginate(5);
    $lessonPrice    = (float) ($p->lesson_price ?? 0);
    $testPackagePrice = $p->test_package_price ? (float) $p->test_package_price : (float) \App\Models\SiteSetting::get('default_test_package_price', 225);
    $instructingMonths = $u->created_at ? (int) $u->created_at->diffInMonths(now()) : null;
    $languages  = is_array($p->languages) ? $p->languages : ($p->languages ? explode(',', $p->languages) : []);
    $languages  = array_filter(array_map('trim', $languages));
    if (empty($languages)) $languages = ['English'];
    $transmissionLabel = match (strtolower($p->transmission ?? '')) {
        'manual' => 'Manual',
        'both'   => 'Auto & Manual',
        default  => 'Auto',
    };
    $wwccVerified   = !empty($p->wwcc_number);
    $vehicleParts   = array_filter([$p->vehicle_make, $p->vehicle_model, $p->vehicle_year]);
    $vehicleLabel   = implode(' ', $vehicleParts) ?: 'Vehicle details on request';
    $hasDualControls = (bool) ($p->vehicle_has_dual_controls ?? true);
    $vehicleSafety  = $p->vehicle_safety_rating ? $p->vehicle_safety_rating . '-star ANCAP rating' : '5-star ANCAP rating';
    $googleMapsKey  = \App\Models\SiteSetting::get('google_maps_api_key') ?: config('services.google.maps_api_key');
    $suburbsCount   = $p->serviceAreas->count();
    $serviceAreaJson = $p->serviceAreas->map(fn($s) => [
        'name'     => $s->name,
        'postcode' => $s->postcode,
        'state'    => $s->state?->code,
    ])->values()->toJson();
@endphp

{{-- ── Yellow hero banner ── --}}
<section class="ipp-hero">
    <div class="container ipp-hero-inner">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('find-instructor') }}" class="ipp-back">
            <i class="bi bi-chevron-left"></i> Back
        </a>
    </div>
    <div class="ipp-hero-bg-pattern" aria-hidden="true"></div>
</section>

{{-- ── Profile header (overlaps hero) ── --}}
<div class="container ipp-profile-header">
    <div class="d-flex flex-column flex-md-row align-items-md-center gap-4">
        <div class="ipp-photos">
            <div class="ipp-photo-circle">
                @if($p->profile_photo)
                    <img src="{{ asset('storage/' . $p->profile_photo) }}" alt="{{ $u->name }}">
                @else
                    <div class="ipp-photo-initials">{{ strtoupper(substr($firstName, 0, 1)) }}</div>
                @endif
            </div>
            <div class="ipp-photo-circle ipp-photo-vehicle">
                @if($p->vehicle_photo)
                    <img src="{{ asset('storage/' . $p->vehicle_photo) }}" alt="{{ $vehicleLabel }}">
                @else
                    <div class="ipp-photo-vehicle-icon"><i class="bi bi-car-front-fill"></i></div>
                @endif
            </div>
        </div>
        <div class="flex-grow-1">
            <h1 class="ipp-name">{{ $u->name }}</h1>
            <div class="ipp-rating-row">
                @php $full = floor($avgRating); $half = ($avgRating - $full) >= 0.3 && ($avgRating - $full) < 0.8; @endphp
                <div class="ipp-stars">
                    @for($i = 0; $i < 5; $i++)
                        @if($i < $full)<i class="bi bi-star-fill"></i>
                        @elseif($i === (int)$full && $half)<i class="bi bi-star-half"></i>
                        @else<i class="bi bi-star-fill ipp-star-empty"></i>
                        @endif
                    @endfor
                </div>
                <span class="ipp-rating-text">{{ $avgRating > 0 ? number_format($avgRating, 1) : '—' }} · {{ $reviewCount }} {{ Str::plural('rating', $reviewCount) }}</span>
            </div>
        </div>
    </div>
    <hr class="ipp-divider">
</div>

<div class="container ipp-body">
    <div class="row g-4">
        {{-- ──────────────────── LEFT COLUMN ──────────────────── --}}
        <div class="col-lg-8">
            {{-- Instructor Bio --}}
            <section class="ipp-section">
                <h2 class="ipp-section-title">Instructor Bio</h2>
                <div class="ipp-bio">{!! nl2br(e($p->bio ?? "Hi, I'm {$firstName}. I'm a verified driving instructor here to help you build confidence and pass your test.")) !!}</div>

                <ul class="ipp-quick-info">
                    <li>
                        <i class="bi bi-car-front-fill"></i>
                        {{ $transmissionLabel }} Lessons
                        @if($p->offers_test_package)
                            &amp; <a href="{{ route('driving-test-packages') }}">Test Packages</a>
                        @endif
                    </li>
                    @if($wwccVerified)
                        <li><i class="bi bi-shield-check"></i> Verified Working with Children Check</li>
                    @endif
                    <li><i class="bi bi-card-checklist"></i> Driving Instructor's Licence</li>
                    @if($instructingMonths !== null)
                        <li>
                            <i class="bi bi-clock-history"></i>
                            @if($instructingMonths < 12)
                                Instructed for {{ $instructingMonths }} mo.
                            @else
                                Instructing for {{ floor($instructingMonths / 12) }}+ years
                            @endif
                        </li>
                    @endif
                </ul>
            </section>

            {{-- Spoken language(s) --}}
            <section class="ipp-section">
                <h2 class="ipp-section-title">Spoken language(s)</h2>
                <div class="ipp-lang-pills">
                    @foreach($languages as $lang)
                        <span class="ipp-lang-pill">{{ $lang }}</span>
                    @endforeach
                </div>
            </section>

            {{-- Reviews --}}
            <section class="ipp-section ipp-reviews">
                <h2 class="ipp-section-title">Reviews</h2>
                @forelse($reviews as $review)
                    <article class="ipp-review-item">
                        <div class="ipp-review-head">
                            <div>
                                <strong class="ipp-review-name">{{ $review->learner->name ?? 'Anonymous learner' }}</strong>
                                <span class="ipp-review-date">Posted on {{ $review->created_at->format('j M Y') }}</span>
                            </div>
                            <div class="ipp-stars ipp-stars-small">
                                @for($i = 0; $i < 5; $i++)
                                    @if($i < (int) $review->rating)<i class="bi bi-star-fill"></i>
                                    @else<i class="bi bi-star-fill ipp-star-empty"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="ipp-review-text">{{ $review->comment }}</p>
                        @endif
                    </article>
                @empty
                    <p class="text-muted small mb-0">No reviews yet — be the first to book a lesson with {{ $firstName }}.</p>
                @endforelse
                @if($reviews->hasPages())
                    <div class="ipp-review-pagination">
                        {{ $reviews->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </section>
        </div>

        {{-- ──────────────────── RIGHT SIDEBAR ──────────────────── --}}
        <div class="col-lg-4">
            {{-- Pricing card --}}
            <aside class="ipp-card ipp-pricing">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="ipp-pricing-title">Hourly Price</div>
                        <div class="ipp-pricing-sub">Offers 1 &amp; 2hr lessons</div>
                    </div>
                    <div class="ipp-pricing-amount">${{ number_format($lessonPrice, 0) }}<span class="ipp-pricing-unit">/hr</span></div>
                </div>

                @php
                    $tiers = (new \App\Services\PricingService())->getDiscountTiers();
                @endphp
                @foreach($tiers as $tier)
                    <div class="ipp-pricing-tier">
                        <span>{{ (int) $tier['hours'] }}hrs or more</span>
                        <span class="ipp-save-badge">SAVE {{ rtrim(rtrim(number_format($tier['discount_pct'], 2), '0'), '.') }}%</span>
                    </div>
                @endforeach

                @if($p->offers_test_package)
                    <div class="ipp-pricing-row">
                        <span>Test Package (2.5 hrs) <i class="bi bi-question-circle ipp-help-icon" title="Includes warm-up lesson, vehicle for test, drop-off after"></i></span>
                        <span class="fw-bold">${{ number_format($testPackagePrice, 0) }}</span>
                    </div>
                @endif

                <a href="{{ auth()->check() && auth()->user()->isLearner() ? route('learner.bookings.new', ['instructor_profile_id' => $p->id]) : route('learner.bookings.amount', ['instructor_profile_id' => $p->id]) }}"
                   class="btn btn-warning fw-bolder w-100 ipp-book-btn">
                    Book Now <i class="bi bi-chevron-right small"></i>
                </a>
                <button type="button" class="btn btn-outline-secondary fw-semibold w-100 ipp-availability-btn" id="ipp-check-availability">
                    Check Availability
                </button>

                <div class="ipp-payment-icons">
                    <span class="ipp-pay-icon" title="Visa"><i class="bi bi-credit-card-2-front-fill"></i> VISA</span>
                    <span class="ipp-pay-icon" title="Mastercard"><i class="bi bi-credit-card-fill"></i> MC</span>
                    <span class="ipp-pay-icon" title="American Express"><i class="bi bi-credit-card-fill"></i> AMEX</span>
                    <span class="ipp-pay-icon" title="PayPal"><i class="bi bi-paypal"></i></span>
                </div>
            </aside>

            {{-- Features card --}}
            <aside class="ipp-card ipp-features">
                <div class="ipp-feature">
                    <div class="ipp-feature-icon"><i class="bi bi-calendar-event"></i></div>
                    <div>
                        <h3>Reschedule online</h3>
                        <p>Reschedule online up to 24 hours before a booking.</p>
                    </div>
                </div>
                <div class="ipp-feature">
                    <div class="ipp-feature-icon"><i class="bi bi-person-check"></i></div>
                    <div>
                        <h3>Instructor choice</h3>
                        <p>Choose your instructor, change online anytime.</p>
                    </div>
                </div>
                <div class="ipp-feature">
                    <div class="ipp-feature-icon"><i class="bi bi-calendar-plus"></i></div>
                    <div>
                        <h3>Book now or later</h3>
                        <p>Buy a package, make bookings now or later.</p>
                    </div>
                </div>
                <div class="ipp-feature">
                    <div class="ipp-feature-icon"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <h3>Real-time availability</h3>
                        <p>Book directly into your instructor's calendar.</p>
                    </div>
                </div>
                <a href="{{ route('support') }}" class="ipp-more-info">
                    <i class="bi bi-info-circle"></i> More info about bookings <i class="bi bi-chevron-down ms-1 small"></i>
                </a>
            </aside>

            {{-- Vehicle card --}}
            <aside class="ipp-card ipp-vehicle">
                <h3 class="ipp-card-heading">{{ $firstName }}'s vehicle</h3>
                <div class="d-flex gap-3 align-items-start">
                    <div class="ipp-vehicle-thumb">
                        @if($p->vehicle_photo)
                            <img src="{{ asset('storage/' . $p->vehicle_photo) }}" alt="{{ $vehicleLabel }}">
                        @else
                            <div class="ipp-vehicle-thumb-icon"><i class="bi bi-car-front-fill"></i></div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold mb-1">{{ $vehicleLabel }} ({{ $transmissionLabel }})</div>
                        <div class="small text-muted mb-1"><i class="bi bi-shield-fill-check text-success me-1"></i>{{ $vehicleSafety }}</div>
                        @if($hasDualControls)
                            <div class="small text-muted"><i class="bi bi-check-circle-fill text-success me-1"></i>Dual controls fitted</div>
                        @endif
                    </div>
                </div>
            </aside>

            {{-- Service areas (Google Map) --}}
            <aside class="ipp-card ipp-areas">
                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <h3 class="ipp-card-heading mb-0">{{ $firstName }} services {{ $suburbsCount }} {{ Str::plural('suburb', $suburbsCount) }}</h3>
                    <a href="#" class="small fw-semibold text-decoration-underline" id="ipp-view-suburbs">View full list</a>
                </div>
                <div class="ipp-map-wrap">
                    <div id="ipp-map" class="ipp-map"></div>
                    @if(empty($googleMapsKey))
                        <div class="ipp-map-fallback">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>Service area map will appear here once Google Maps is configured.</span>
                        </div>
                    @endif
                </div>
                <p class="small text-muted mb-0 mt-2 text-center">Instructor service area in yellow.</p>
            </aside>
        </div>
    </div>
</div>

{{-- Suburbs full-list modal --}}
<div class="modal fade" id="suburbsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Service areas ({{ $suburbsCount }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 small">
                    @foreach($p->serviceAreas as $area)
                        <div class="col-6">
                            <i class="bi bi-geo-alt-fill text-warning me-1"></i>{{ $area->name }} {{ $area->postcode }} {{ $area->state?->code }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('ipp-view-suburbs')?.addEventListener('click', function(e) {
        e.preventDefault();
        var el = document.getElementById('suburbsModal');
        if (window.bootstrap && el) new window.bootstrap.Modal(el).show();
    });
    document.getElementById('ipp-check-availability')?.addEventListener('click', function() {
        // Scroll the user to the pricing card / book button (simple UX for now)
        document.querySelector('.ipp-book-btn')?.scrollIntoView({behavior:'smooth', block:'center'});
    });

    @if(!empty($googleMapsKey))
    (function() {
        var serviceAreas = {!! $serviceAreaJson !!};
        if (!serviceAreas.length) return;

        function initMap() {
            if (typeof google === 'undefined' || !google.maps) return;
            var mapEl = document.getElementById('ipp-map');
            if (!mapEl) return;

            var geocoder = new google.maps.Geocoder();
            var map = new google.maps.Map(mapEl, {
                zoom: 10,
                center: { lat: -33.8688, lng: 151.2093 }, // Sydney default
                disableDefaultUI: false,
                streetViewControl: false,
                mapTypeControl: true,
                styles: [
                    { featureType: 'poi', stylers: [{ visibility: 'off' }] },
                ],
            });

            // Geocode + cluster service area polygons (we don't have precise polygons,
            // so we show coloured circle overlays around each suburb's geocoded centre)
            var bounds = new google.maps.LatLngBounds();
            var processed = 0;
            serviceAreas.forEach(function(area) {
                var query = area.name + ' ' + (area.postcode || '') + ' ' + (area.state || '') + ' Australia';
                geocoder.geocode({ address: query }, function(results, status) {
                    processed++;
                    if (status === 'OK' && results[0]) {
                        var loc = results[0].geometry.location;
                        new google.maps.Circle({
                            map: map,
                            center: loc,
                            radius: 1500,
                            fillColor: '#fbbf24',
                            fillOpacity: 0.45,
                            strokeColor: '#f59e0b',
                            strokeOpacity: 0.8,
                            strokeWeight: 1,
                        });
                        bounds.extend(loc);
                    }
                    if (processed === serviceAreas.length && !bounds.isEmpty()) {
                        map.fitBounds(bounds, 40);
                    }
                });
            });
        }

        // Wait for Google library
        var waited = 0;
        var int = setInterval(function() {
            if (typeof google !== 'undefined' && google.maps) { clearInterval(int); initMap(); }
            else if (++waited > 30) { clearInterval(int); }
        }, 200);
    })();
    @endif
</script>
@if(!empty($googleMapsKey))
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&libraries=places&loading=async&callback=Function.prototype" async defer></script>
@endif
@endpush
