@extends('layouts.learner')

@section('title', 'Make a Booking')
@section('heading', 'Make a Booking')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('learner.bookings.new', ['instructor_profile_id' => $order['instructor_profile_id'] ?? '']) }}">Make a Booking</a></li>
        <li class="breadcrumb-item active" aria-current="page">Payment</li>
    </ol>
</nav>

<h5 class="mb-4">Make a Booking</h5>

<div class="row">
    <div class="col-lg-8">
        <form id="payment-form" action="#" method="post">
            @csrf

            @if($isGuest ?? false)
                {{-- ── Guest account details ── --}}
                <div class="card border-0 shadow-sm mb-4" style="border-left: 4px solid var(--sl-primary-500, #ff8400) !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-2 mb-3">
                            <i class="bi bi-person-plus-fill text-primary fs-5"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Your Details</h6>
                                <p class="small text-muted mb-0">We'll create an account for you automatically after payment, so you can manage your bookings.</p>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-12">
                                <label class="form-label small">Full name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="guest_name" required value="{{ $guestName ?? '' }}" placeholder="e.g. Aaron Smith">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="guest_email" required value="{{ $guestEmail ?? '' }}" placeholder="you@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Mobile <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="guest_phone" required value="{{ $guestPhone ?? '' }}" placeholder="04XX XXX XXX">
                            </div>
                        </div>
                        <p class="small text-muted mt-3 mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Already have an account? <a href="{{ route('learner.login') }}?redirect={{ urlencode(route('learner.bookings.payment')) }}">Log in</a> to use saved payment methods.
                        </p>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Payment Method</h6>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="pay-card" value="card" checked>
                            <label class="form-check-label fw-bold" for="pay-card">Credit/Debit card</label>
                        </div>
                        <div id="card-fields" class="ps-4 mt-2">
                            <div class="mb-2">
                                <label class="form-label small">Card number</label>
                                <input type="text" class="form-control" name="card_number" placeholder="1234 1234 1234 1234" maxlength="19" autocomplete="cc-number">
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">Expiry</label>
                                    <input type="text" class="form-control" name="card_expiry" placeholder="MM/YY" maxlength="5" autocomplete="cc-exp">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">CVC</label>
                                    <div class="d-flex align-items-center gap-1">
                                        <input type="text" class="form-control" name="card_cvc" placeholder="CVC" maxlength="4" autocomplete="cc-csc">
                                        <span class="small text-muted"><i class="bi bi-credit-card-2-front"></i> VISA</span>
                                        <span class="small text-muted">Mastercard</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="save_payment_method" id="save-card" value="1" checked>
                                <label class="form-check-label small" for="save-card">Save this payment method</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay-paypal" value="paypal">
                        <label class="form-check-label" for="pay-paypal">
                            <span class="d-inline-block align-middle">PayPal</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <button type="button" class="btn btn-link p-0 d-flex align-items-center justify-content-between w-100 text-dark text-decoration-none" data-bs-toggle="collapse" data-bs-target="#billing-details" aria-expanded="true">
                        <span class="fw-bold">Billing Details</span>
                        <i class="bi bi-chevron-up"></i>
                    </button>
                    <div class="collapse show mt-3" id="billing-details">
                        <div class="mb-2">
                            <label class="form-label small">Billing name</label>
                            <input type="text" class="form-control" name="billing_name" value="{{ $billingName }}" placeholder="Full name">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">* Billing address</label>
                            <input type="text" class="form-control" name="billing_address" placeholder="Street address">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small">* Suburb</label>
                                <select class="form-select form-select-sm" name="billing_suburb_id" id="billing_suburb">
                                    <option value="">Select suburb</option>
                                    @foreach($states as $state)
                                        @foreach($suburbsByState[$state->id] ?? [] as $sub)
                                            <option value="{{ $sub['id'] }}" data-state="{{ $state->id }}">{{ $sub['name'] }}, {{ $sub['postcode'] }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">* State</label>
                                <select class="form-select form-select-sm" name="billing_state_id" id="billing_state">
                                    <option value="">Select state</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning border-0 d-flex align-items-start gap-2 mb-4">
                <i class="bi bi-shield-check fs-4"></i>
                <p class="mb-0 small">To protect your payment, never transfer money or communicate about lesson payments outside of the Secure Licences website.</p>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Order Summary</h6>
                @foreach($order['items'] as $item)
                    @php
                        $label = ($item['booking_type'] ?? '') === 'test_package' ? 'Test Package' : 'Lesson';
                        $dateShort = '';
                        if (!empty($item['date_iso'])) {
                            try {
                                $d = \Carbon\Carbon::parse($item['date_iso']);
                                $dateShort = $d->format('j M');
                            } catch (\Exception $e) {
                                $dateShort = $item['dateLabel'] ?? '';
                            }
                        }
                        $lineLabel = $label . ' - ' . $dateShort . ', ' . ($item['timeLabel'] ?? '');
                        $price = $item['price'] ?? 0;
                    @endphp
                    <div class="d-flex justify-content-between align-items-start py-2 border-bottom small">
                        <span><i class="bi bi-calendar3 me-1 text-muted"></i>{{ $lineLabel }}</span>
                        <span>${{ number_format((float) $price, 2) }}</span>
                    </div>
                @endforeach
                <div class="d-flex justify-content-between small mb-1 mt-2">
                    <span>Platform Processing Fee</span>
                    <span>${{ number_format((float) ($order['fee'] ?? 0), 2) }}</span>
                    <i class="bi bi-info-circle text-muted ms-1" title="4% processing fee" style="cursor: help;"></i>
                </div>
                <div class="d-flex justify-content-between fw-bold pt-2 border-top mt-2">
                    <span>Total Payment Due</span>
                    <span id="order-total">${{ number_format((float) ($order['total'] ?? 0), 2) }}</span>
                </div>
                <p class="small text-muted mb-3 mt-1">Or 4 payments of <span id="order-instalment">${{ number_format(((float) ($order['total'] ?? 0)) / 4, 2) }}</span></p>
                <button type="submit" form="payment-form" class="btn btn-warning w-100" id="btn-pay">
                    Pay ${{ number_format((float) ($order['total'] ?? 0), 2) }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-wrapper.form-select-sm { min-height: calc(1.5em + 0.5rem + 2px); }
    .ts-wrapper.form-select-sm .ts-control { padding: 0.25rem 0.5rem; font-size: 0.875rem; min-height: calc(1.5em + 0.5rem + 2px); }
    .ts-wrapper .ts-control { border-radius: var(--sl-radius, 0.375rem); border-color: var(--sl-gray-300, #dee2e6); }
    .ts-wrapper.focus .ts-control { border-color: var(--sl-primary-500, #ff8400); box-shadow: 0 0 0 0.2rem rgba(255,132,0,0.15); }
    .ts-dropdown .option.active { background-color: var(--sl-primary-500, #ff8400); color: #fff; }
</style>
@endpush
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

@push('scripts')
<script>
(function() {
  // Searchable suburb/state dropdowns
  function initTs(id) {
    var el = document.getElementById(id);
    if (!el || el.tomselect) return;
    new TomSelect(el, {
      create: false,
      allowEmptyOption: true,
      maxOptions: 1000,
      placeholder: el.options[0] ? el.options[0].text : 'Select...',
    });
  }
  initTs('billing_suburb');
  initTs('billing_state');

  document.getElementById('billing_suburb').addEventListener('change', function() {
    var opt = this.selectedOptions[0];
    if (opt && opt.getAttribute('data-state')) {
      var stateEl = document.getElementById('billing_state');
      if (stateEl.tomselect) stateEl.tomselect.setValue(opt.getAttribute('data-state'), true);
      else stateEl.value = opt.getAttribute('data-state');
    }
  });

  document.getElementById('payment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = document.getElementById('btn-pay');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';

    var method = document.querySelector('input[name="payment_method"]:checked').value;
    var csrf = document.querySelector('meta[name="csrf-token"]');

    var payload = {
      payment_method: method,
      billing_name: document.querySelector('[name="billing_name"]').value,
      billing_address: document.querySelector('[name="billing_address"]').value
    };

    // Include guest fields if present
    var guestName = document.querySelector('[name="guest_name"]');
    var guestEmail = document.querySelector('[name="guest_email"]');
    var guestPhone = document.querySelector('[name="guest_phone"]');
    if (guestName && guestEmail && guestPhone) {
      if (!guestName.value.trim() || !guestEmail.value.trim() || !guestPhone.value.trim()) {
        alert('Please fill in your name, email and mobile so we can create your account.');
        btn.disabled = false;
        btn.textContent = 'Pay ${{ number_format((float) ($order["total"] ?? 0), 2) }}';
        return;
      }
      payload.guest_name = guestName.value.trim();
      payload.guest_email = guestEmail.value.trim();
      payload.guest_phone = guestPhone.value.trim();
    }

    fetch('/api/learner/bookings/pay', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrf ? csrf.content : '',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin',
      body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
    .then(function(result) {
      if (result.ok) {
        btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Booking Confirmed!';
        btn.classList.remove('btn-warning');
        btn.classList.add('btn-success');
        if (result.data.data && result.data.data.account_created) {
          alert('Success! Your account has been created. Check your email for a password-reset link.');
        }
        var redirect = (result.data.data && result.data.data.redirect) || '{{ route("find-instructor") }}';
        setTimeout(function() { window.location.href = redirect; }, 2000);
      } else {
        var msg = result.data.message || 'Payment failed. Please try again.';
        if (result.data.errors) {
          msg = Object.values(result.data.errors).flat().join('\n');
        }
        alert(msg);
        btn.disabled = false;
        btn.textContent = 'Pay ${{ number_format((float) ($order["total"] ?? 0), 2) }}';
      }
    })
    .catch(function() {
      alert('Something went wrong. Please try again.');
      btn.disabled = false;
      btn.textContent = 'Pay ${{ number_format((float) ($order["total"] ?? 0), 2) }}';
    });
  });
})();
</script>
@endpush
@endsection
