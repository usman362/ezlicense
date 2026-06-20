{{-- Business-tools ecosystem strip. Pass $exclude = current slug. --}}
@php
    $businessFeatures = [
        'calendar-scheduling'   => ['bi-calendar3',     'Calendar & Scheduling', '2-way Google sync'],
        'payments-payouts'      => ['bi-cash-coin',      'Payments & Payouts',    'Flexible bank deposits (7, 14, or 28-day)'],
        'automated-reminders'   => ['bi-bell',           'Automated Reminders',   'SMS + email, automatic'],
        'no-show-protection'    => ['bi-shield-check',    'No-Show Protection',    'Fees auto-enforced'],
        'lesson-catalog'        => ['bi-journal-text',    'Lesson Catalog',        '1hr–5hr + test packs'],
        'learner-management'    => ['bi-people',          'Learner Management',    'Every learner, one place'],
        'website-booking-link'  => ['bi-globe',           'Website + Booking Link','Free site in 60 seconds'],
    ];
    $items = collect($businessFeatures)->except($exclude ?? null)->take(6);
@endphp
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.06em;">Works seamlessly with</div>
            <h2 class="h4 fw-bolder">Every other part of the platform</h2>
        </div>
        <div class="row g-3">
            @foreach($items as $slug => [$ic, $title, $desc])
                @php $routeName = 'for-instructors.' . $slug; $url = \Illuminate\Support\Facades\Route::has($routeName) ? route($routeName) : route('instruct-with-us'); @endphp
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ $url }}" class="text-decoration-none text-reset">
                        <div class="lg-mini">
                            <div class="lg-mini-ic"><i class="bi {{ $ic }}"></i></div>
                            <div class="fw-semibold small">{{ $title }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $desc }}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
