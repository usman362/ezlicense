@extends('layouts.booking', ['step' => 4])

@section('title', 'Learner Registration')

@section('content')
@php
    $isAuth = auth()->check();
    $authUser = auth()->user();

    // Pre-fill values from session (if user came back) or from auth user
    $sd = session('learner_booking_details', []);
    $firstName = $sd['first_name'] ?? ($isAuth ? trim(explode(' ', $authUser->name)[0] ?? '') : '');
    $lastName = $sd['last_name'] ?? ($isAuth ? trim(explode(' ', $authUser->name, 2)[1] ?? '') : '');
    $email = $sd['email'] ?? ($isAuth ? $authUser->email : '');
    $phone = $sd['phone'] ?? ($isAuth ? ($authUser->phone ?? '') : '');
    $dobDay = $sd['dob_day'] ?? '';
    $dobMonth = $sd['dob_month'] ?? '';
    $dobYear = $sd['dob_year'] ?? '';
    $describes = $sd['describes_as'] ?? '';
    $registeringFor = $sd['registering_for'] ?? 'myself';

    // Pickup from the first booking item
    $firstBooking = $order['items'][0] ?? [];
    $pickupAddress = $sd['pickup_address'] ?? ($firstBooking['pickup_address'] ?? '');
    $pickupSuburbId = $sd['pickup_suburb_id'] ?? ($firstBooking['pickup_suburb_id'] ?? '');
    $pickupStateId = $sd['pickup_state_id'] ?? ($firstBooking['pickup_state_id'] ?? '');
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="mb-2">
            <h3 class="fw-bolder mb-1" style="letter-spacing:-0.02em;">Learner Registration</h3>
            @guest
                <p class="text-muted mb-0">Existing learner? <a href="{{ route('learner.login') }}?redirect={{ urlencode(url()->current()) }}" class="fw-semibold text-decoration-underline">Log In</a></p>
            @else
                <p class="text-muted mb-0">Confirm your details below.</p>
            @endguest
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0 small">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4 pb-3 border-bottom">Enter your details</h5>

                <form id="reg-form" method="POST" action="{{ route('learner.bookings.details.store') }}">
                    @csrf

                    {{-- Who are you registering for? --}}
                    <h6 class="fw-bold mb-3">Who are you registering for?</h6>
                    <div class="mb-4">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="registering_for" id="reg_myself" value="myself" @checked($registeringFor === 'myself')>
                            <label class="form-check-label" for="reg_myself">Myself</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="registering_for" id="reg_other" value="other" @checked($registeringFor === 'other')>
                            <label class="form-check-label" for="reg_other">Someone else (e.g. child, partner, grandchild, other)</label>
                        </div>
                    </div>

                    {{-- Pick up details --}}
                    <h6 class="fw-bold mb-3">Please enter your pick up details</h6>
                    <div class="mb-3">
                        <label class="form-label small"><span class="text-danger">*</span> Pick up address</label>
                        <input type="text" class="form-control" name="pickup_address" required value="{{ old('pickup_address', $pickupAddress) }}">
                    </div>
                    <div class="row g-2 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small"><span class="text-danger">*</span> Suburb</label>
                            <select class="form-select" name="pickup_suburb_id" id="reg_suburb" required>
                                <option value="">Select suburb</option>
                                @foreach($states as $state)
                                    @foreach($suburbsByState[$state->id] ?? [] as $sub)
                                        <option value="{{ $sub['id'] }}" data-state="{{ $state->id }}" @selected((int) $pickupSuburbId === (int) $sub['id'])>{{ $sub['name'] }}, {{ $sub['postcode'] }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small"><span class="text-danger">*</span> State</label>
                            <select class="form-select" name="pickup_state_id" id="reg_state" required>
                                <option value="">Select state</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" @selected((int) $pickupStateId === (int) $state->id)>{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Personal details --}}
                    <h6 class="fw-bold mb-3">Please provide your personal details</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small"><span class="text-danger">*</span> First name</label>
                            <input type="text" class="form-control" name="first_name" required value="{{ old('first_name', $firstName) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small"><span class="text-danger">*</span> Last name</label>
                            <input type="text" class="form-control" name="last_name" required value="{{ old('last_name', $lastName) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small"><span class="text-danger">*</span> Email address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" name="email" required value="{{ old('email', $email) }}" {{ $isAuth ? 'readonly' : '' }}>
                            </div>
                            <small class="text-muted">We use your email to send lesson confirmation details</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small"><span class="text-danger">*</span> Phone number</label>
                            <input type="tel" class="form-control" name="phone" required placeholder="0400 000 000" value="{{ old('phone', $phone) }}">
                            <small class="text-muted">For instructor to contact at lesson pick up if needed</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label small"><span class="text-danger">*</span> Date of Birth</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <select name="dob_day" class="form-select" required>
                                        <option value="">Day</option>
                                        @for($d = 1; $d <= 31; $d++)
                                            <option value="{{ $d }}" @selected((int) $dobDay === $d)>{{ $d }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select name="dob_month" class="form-select" required>
                                        <option value="">Month</option>
                                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $i => $monthName)
                                            <option value="{{ $i + 1 }}" @selected((int) $dobMonth === $i + 1)>{{ $monthName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select name="dob_year" class="form-select" required>
                                        <option value="">Year</option>
                                        @for($y = (int) date('Y'); $y >= 1930; $y--)
                                            <option value="{{ $y }}" @selected((int) $dobYear === $y)>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small"><span class="text-danger">*</span> Which best describes you?</label>
                            <select name="describes_as" class="form-select" required>
                                <option value="">Please select</option>
                                @foreach([
                                    'new_learner' => 'New Learner — just starting out',
                                    'some_experience' => 'Some experience — had a few lessons',
                                    'refresher' => 'Refresher driver — getting back behind the wheel',
                                    'overseas' => 'Overseas licence holder — converting to local',
                                    'test_prep' => 'Preparing for my driving test',
                                    'other' => 'Other',
                                ] as $value => $label)
                                    <option value="{{ $value }}" @selected($describes === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Password section (only for guests) --}}
                    @guest
                        <h6 class="fw-bold mb-2 mt-4">Choose a password for your learning dashboard</h6>
                        <p class="small text-muted mb-3">Your dashboard allows you to make, manage &amp; view bookings online 24/7.</p>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small"><span class="text-danger">*</span> Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" name="password" required minlength="8">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small"><span class="text-danger">*</span> Password confirmation</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" name="password_confirmation" required minlength="8">
                                </div>
                            </div>
                        </div>
                    @endguest

                    {{-- Consent checkboxes --}}
                    <div class="mt-4 pt-3 border-top">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="marketing_opt_in" id="reg_marketing" value="1" @checked(old('marketing_opt_in', $sd['marketing_opt_in'] ?? false))>
                            <label class="form-check-label small" for="reg_marketing">
                                I agree to receive occasional marketing communications and offers from Secure Licences.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="terms_accepted" id="reg_terms" value="1" required @checked(old('terms_accepted', $sd['terms_accepted'] ?? false))>
                            <label class="form-check-label small" for="reg_terms">
                                I agree to the <a href="{{ route('terms') }}" target="_blank" class="fw-semibold">Learner Driver Terms &amp; Conditions</a>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Right Sidebar: Order Summary --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Order Summary</h6>

                @php
                    $hours = (int) ($order['package_hours'] ?? 0);
                    $lessonPrice = (float) ($instructorProfile->lesson_price ?? 0);
                    $hoursSubtotal = $hours > 0 ? $lessonPrice * $hours : ((float) ($order['subtotal'] ?? 0));
                    $hoursDiscount = (float) ($order['discount_amount'] ?? 0);
                    $hoursAfter = $hoursSubtotal - $hoursDiscount;
                    $bookedCount = count($order['items'] ?? []);
                @endphp

                <div class="d-flex justify-content-between align-items-start py-2 small">
                    <div>
                        <div><i class="bi bi-ticket-perforated me-1"></i> {{ $hours }} hrs Booking Credit</div>
                        @if($bookedCount > 0)
                            <div class="text-muted small ms-3">{{ $bookedCount }} hr booked</div>
                        @endif
                    </div>
                    <span class="fw-semibold">${{ number_format($hoursSubtotal, 2) }}</span>
                </div>

                @if($hoursDiscount > 0)
                    <div class="d-flex justify-content-between align-items-center py-2 small">
                        <span>
                            Credit Discount
                            <span class="ms-1" style="font-size:0.7rem;padding:0.1rem 0.45rem;background:#d1f4e1;color:#0b7b3c;font-weight:700;border-radius:12px;">{{ (int) ($order['discount_pct'] ?? 0) }}% OFF</span>
                        </span>
                        <span class="text-success fw-semibold">-${{ number_format($hoursDiscount, 2) }}</span>
                    </div>
                @endif

                @if(!empty($order['add_test_package']))
                    <div class="d-flex justify-content-between align-items-center py-2 small">
                        <span><i class="bi bi-check2-circle text-success me-1"></i> Driving Test Package</span>
                        <span class="fw-semibold">${{ number_format((float) ($order['test_package_price'] ?? 0), 2) }}</span>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center py-2 small">
                    <span>
                        Platform Processing Fee
                        <i class="bi bi-info-circle text-muted ms-1" title="{{ (int) ($order['fee_percent'] ?? 4) }}% processing fee"></i>
                    </span>
                    <span>${{ number_format((float) ($order['fee'] ?? 0), 2) }}</span>
                </div>

                <hr>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="fw-bold">Total Payment Due</span>
                    <span class="fw-bolder fs-5">${{ number_format((float) ($order['total'] ?? 0), 2) }}</span>
                </div>
                <p class="small text-muted mb-3">Or 4 payments of ${{ number_format(((float) ($order['total'] ?? 0)) / 4, 2) }}</p>

                <button type="submit" form="reg-form" class="btn btn-warning w-100 fw-semibold">
                    Continue to Payment <i class="bi bi-chevron-right ms-1"></i>
                </button>

                <div class="text-center mt-3">
                    <a href="#" class="small text-muted text-decoration-underline" data-bs-toggle="collapse" data-bs-target="#referralCollapse">
                        <i class="bi bi-ticket me-1"></i> I have a referral code
                    </a>
                    <div class="collapse mt-2" id="referralCollapse">
                        <input type="text" class="form-control form-control-sm" name="referral_code" form="reg-form" placeholder="Enter referral code" value="{{ old('referral_code', $sd['referral_code'] ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Buy Now Pay Later --}}
        <div class="bnpl-panel">
            <div class="bnpl-title">
                Buy Now Pay Later <i class="bi bi-info-circle text-muted small"></i>
            </div>
            <div class="bnpl-amount">4 payments of ${{ number_format(((float) ($order['total'] ?? 0)) / 4, 2) }}</div>
            <div class="bnpl-badges">
                <span class="bnpl-badge paypal"><i class="bi bi-paypal me-1"></i>Pay in 4</span>
                <span class="bnpl-badge afterpay">afterpay&lt;&gt;</span>
                <span class="bnpl-badge klarna">Klarna</span>
            </div>
        </div>

        {{-- Trust signals --}}
        <div class="trust-panel">
            <h6><i class="bi bi-shield-check text-success me-1"></i>Purchase With Peace Of Mind</h6>
            <p>Flexible rebooking if your plans change.</p>

            <h6><i class="bi bi-calendar2-check text-primary me-1"></i>Manage Your Lessons Online</h6>
            <p>24/7 access. Manage your account. Switch your instructor at no cost.</p>

            <h6><i class="bi bi-lock-fill text-warning me-1"></i>Secure Payments</h6>
            <p>We use 100% secure payments to provide you with a simple and safe experience.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    // Auto-fill state when suburb changes
    var suburb = document.getElementById('reg_suburb');
    var state = document.getElementById('reg_state');
    if (suburb && state) {
        suburb.addEventListener('change', function() {
            var opt = this.selectedOptions[0];
            if (opt && opt.getAttribute('data-state')) {
                state.value = opt.getAttribute('data-state');
            }
        });
    }
})();
</script>
@endpush
@endsection
