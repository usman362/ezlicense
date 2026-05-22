@extends(auth()->check() ? 'layouts.learner' : 'layouts.booking', ['step' => 5])

@section('title', 'Payment')
@section('heading', 'Payment')

@section('content')
@auth
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('learner.bookings.new', ['instructor_profile_id' => $order['instructor_profile_id'] ?? '']) }}">Make a Booking</a></li>
            <li class="breadcrumb-item active" aria-current="page">Payment</li>
        </ol>
    </nav>
@endauth
<div class="mb-4">
    <h3 class="fw-bolder mb-1" style="letter-spacing:-0.02em;">Complete Your Booking</h3>
    <p class="text-muted mb-0">Secure payment — your lessons will be confirmed instantly.</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <form id="payment-form" action="#" method="post">
            @csrf

            {{-- Confirmation: Your details (collected in Step 4) --}}
            @if(!empty($details))
                <div class="card border-0 shadow-sm mb-4" style="border-left: 4px solid var(--sl-accent-500, #ffd500) !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0"><i class="bi bi-person-check-fill text-success me-2"></i>Your Details</h6>
                            <a href="{{ route('learner.bookings.details') }}" class="small text-decoration-underline">Edit</a>
                        </div>
                        <div class="row g-2 small">
                            <div class="col-md-6"><span class="text-muted">Name:</span> <strong>{{ $details['first_name'] ?? '' }} {{ $details['last_name'] ?? '' }}</strong></div>
                            <div class="col-md-6"><span class="text-muted">Email:</span> <strong>{{ $details['email'] ?? '' }}</strong></div>
                            <div class="col-md-6"><span class="text-muted">Phone:</span> <strong>{{ $details['phone'] ?? '' }}</strong></div>
                            <div class="col-md-6"><span class="text-muted">Pick-up:</span> <strong>{{ $details['pickup_address'] ?? '' }}</strong></div>
                        </div>
                        @if($isGuest ?? false)
                            <p class="small text-muted mt-2 mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                A learner account will be created automatically after payment.
                            </p>
                        @endif
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
                <p class="mb-0 small">To protect your payment, never transfer money or communicate about lesson payments outside of the Secure Licence website.</p>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
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
                @if(!empty($order['discount_amount']) && $order['discount_amount'] > 0)
                    <div class="d-flex justify-content-between small mb-1 mt-2">
                        <span>
                            Credit Discount
                            <span class="ms-1" style="font-size:0.7rem;padding:0.1rem 0.45rem;background:#d1f4e1;color:#0b7b3c;font-weight:700;border-radius:12px;">{{ (int) ($order['discount_pct'] ?? 0) }}% OFF</span>
                        </span>
                        <span class="text-success fw-semibold">-${{ number_format((float) $order['discount_amount'], 2) }}</span>
                    </div>
                @endif
                @if(!empty($order['add_test_package']))
                    <div class="d-flex justify-content-between small mb-1 mt-2">
                        <span><i class="bi bi-check2-circle text-success me-1"></i>Driving Test Package</span>
                        <span>${{ number_format((float) ($order['test_package_price'] ?? 0), 2) }}</span>
                    </div>
                @endif

                {{-- Referral discount (auto-applied if user qualifies) --}}
                <div class="d-flex justify-content-between small mb-1 mt-2 row-referral-discount" style="display:{{ !empty($order['referral_discount']) && $order['referral_discount'] > 0 ? 'flex' : 'none' }} !important;">
                    <span><i class="bi bi-people-fill text-primary me-1"></i>Referral discount</span>
                    <span class="text-success fw-semibold">-$<span id="row-referral-amount">{{ number_format((float) ($order['referral_discount'] ?? 0), 2) }}</span></span>
                </div>

                {{-- Coupon discount row (visible only when applied) --}}
                <div class="d-flex justify-content-between small mb-1 mt-2 row-coupon-discount" style="display:{{ !empty($order['coupon_code']) ? 'flex' : 'none' }} !important;">
                    <span><i class="bi bi-ticket-perforated text-success me-1"></i>Coupon (<span id="row-coupon-code">{{ $order['coupon_code'] ?? '' }}</span>)</span>
                    <span class="text-success fw-semibold">-$<span id="row-coupon-amount">{{ number_format((float) ($order['coupon_discount'] ?? 0), 2) }}</span></span>
                </div>

                {{-- Coupon input --}}
                <div class="mt-3 pt-3 border-top" id="coupon-input-wrap" style="display:{{ !empty($order['coupon_code']) ? 'none' : 'block' }};">
                    <label class="form-label small text-muted mb-1"><i class="bi bi-ticket-perforated me-1"></i>Have a promo code?</label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="coupon-code" class="form-control text-uppercase" placeholder="Enter code" autocomplete="off" maxlength="50">
                        <button type="button" id="coupon-apply-btn" class="btn btn-outline-primary">Apply</button>
                    </div>
                    <div id="coupon-message" class="small mt-1" style="min-height:1rem;"></div>
                </div>
                <div class="mt-3 pt-3 border-top" id="coupon-applied-wrap" style="display:{{ !empty($order['coupon_code']) ? 'block' : 'none' }};">
                    <div class="d-flex align-items-center justify-content-between bg-success-subtle p-2 rounded">
                        <span class="small text-success-emphasis">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            <strong id="coupon-applied-code">{{ $order['coupon_code'] ?? '' }}</strong> applied
                        </span>
                        <button type="button" id="coupon-remove-btn" class="btn btn-link btn-sm text-danger p-0">Remove</button>
                    </div>
                </div>

                <div class="d-flex justify-content-between small mb-1 mt-3">
                    <span>Platform Processing Fee</span>
                    <span>$<span id="order-fee">{{ number_format((float) ($order['fee'] ?? 0), 2) }}</span></span>
                </div>
                <div class="d-flex justify-content-between fw-bold pt-2 border-top mt-2">
                    <span>Total Payment Due</span>
                    <span>$<span id="order-total-amount">{{ number_format((float) ($order['total'] ?? 0), 2) }}</span></span>
                </div>
                <button type="submit" form="payment-form" class="btn btn-warning w-100 fw-semibold" id="btn-pay">
                    Pay $<span id="btn-pay-amount">{{ number_format((float) ($order['total'] ?? 0), 2) }}</span>
                </button>
            </div>
        </div>

        @guest
            {{-- Trust signals — guest-only (logged-in learners don't need them) --}}
            <div class="trust-panel">
                <h6><i class="bi bi-shield-check text-success me-1"></i>Purchase With Peace Of Mind</h6>
                <p>Flexible rebooking if your plans change.</p>

                <h6><i class="bi bi-calendar2-check text-primary me-1"></i>Manage Your Lessons Online</h6>
                <p>24/7 access. Manage your account. Switch your instructor at no cost.</p>

                <h6><i class="bi bi-lock-fill text-warning me-1"></i>Secure Payments</h6>
                <p>We use 100% secure payments to provide you with a simple and safe experience.</p>
            </div>
        @endguest
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

    // Guest details were already collected in Step 4 (Learner Registration)
    // and stored in session — no need to send them here

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

  // ── Coupon apply/remove ──
  function fmt(n) { return Number(n).toLocaleString('en-AU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
  function setMessage(text, isError) {
    var el = document.getElementById('coupon-message');
    if (!el) return;
    el.textContent = text || '';
    el.className = 'small mt-1 ' + (isError ? 'text-danger' : 'text-success');
  }
  function refreshTotalsUI(ord, code) {
    if (typeof ord.fee !== 'undefined') document.getElementById('order-fee').textContent = fmt(ord.fee);
    if (typeof ord.total !== 'undefined') {
      document.getElementById('order-total-amount').textContent = fmt(ord.total);
      document.getElementById('btn-pay-amount').textContent = fmt(ord.total);
    }
    if (typeof ord.coupon_discount !== 'undefined') {
      var row = document.querySelector('.row-coupon-discount');
      if (ord.coupon_discount > 0) {
        row.style.display = 'flex';
        document.getElementById('row-coupon-amount').textContent = fmt(ord.coupon_discount);
        document.getElementById('row-coupon-code').textContent = code || ord.coupon_code || '';
      } else {
        row.style.display = 'none';
      }
    }
  }

  var applyBtn = document.getElementById('coupon-apply-btn');
  var removeBtn = document.getElementById('coupon-remove-btn');
  var codeInput = document.getElementById('coupon-code');

  if (applyBtn) {
    applyBtn.addEventListener('click', function() {
      var code = (codeInput.value || '').trim().toUpperCase();
      if (!code) { setMessage('Please enter a code.', true); return; }
      applyBtn.disabled = true;
      applyBtn.textContent = '...';
      setMessage('', false);
      fetch('{{ route("learner.bookings.coupon.apply") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        credentials: 'same-origin',
        body: JSON.stringify({ code: code }),
      })
      .then(function(r) { return r.json().then(function(j) { return { ok: r.ok, body: j }; }); })
      .then(function(res) {
        applyBtn.disabled = false;
        applyBtn.textContent = 'Apply';
        if (!res.ok) {
          setMessage(res.body.message || 'Could not apply coupon.', true);
          return;
        }
        setMessage(res.body.message, false);
        refreshTotalsUI(res.body.order || {}, code);
        document.getElementById('coupon-input-wrap').style.display = 'none';
        document.getElementById('coupon-applied-wrap').style.display = 'block';
        document.getElementById('coupon-applied-code').textContent = code;
      })
      .catch(function() {
        applyBtn.disabled = false;
        applyBtn.textContent = 'Apply';
        setMessage('Network error — please try again.', true);
      });
    });
  }

  if (removeBtn) {
    removeBtn.addEventListener('click', function() {
      removeBtn.disabled = true;
      fetch('{{ route("learner.bookings.coupon.remove") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        credentials: 'same-origin',
        body: JSON.stringify({}),
      })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        removeBtn.disabled = false;
        if (res.ok) {
          var ord = res.order || {};
          ord.coupon_discount = 0;
          refreshTotalsUI(ord);
          document.getElementById('coupon-input-wrap').style.display = 'block';
          document.getElementById('coupon-applied-wrap').style.display = 'none';
          if (codeInput) codeInput.value = '';
          setMessage('', false);
        }
      })
      .catch(function() { removeBtn.disabled = false; });
    });
  }
})();
</script>
@endpush
@endsection
