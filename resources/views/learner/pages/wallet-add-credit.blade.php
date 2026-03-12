@extends('layouts.learner')

@section('title', 'Add Credit')
@section('heading', 'Wallet')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('learner.wallet') }}">My Wallet</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Credit</li>
    </ol>
</nav>

<div class="row g-4">
    {{-- Credit Packages --}}
    <div class="col-lg-8">
        <h5 class="mb-3">Choose a Credit Package</h5>
        <p class="text-muted small mb-3">Buy more and save! Pre-purchased credit can be used for any lesson or test package booking.</p>
        <div class="row g-3" id="credit-packages">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 credit-package-card" data-amount="100" style="cursor:pointer">
                    <div class="card-body text-center py-4">
                        <h3 class="mb-1 text-primary">$100</h3>
                        <div class="text-muted small mb-2">Standard Top-up</div>
                        <div class="small text-success fw-semibold">Pay $100</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 credit-package-card" data-amount="250" style="cursor:pointer">
                    <div class="card-body text-center py-4">
                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Popular</span>
                        <h3 class="mb-1 text-primary">$250</h3>
                        <div class="text-muted small mb-2">Value Pack</div>
                        <div class="small text-success fw-semibold">Save $10 — Pay $240</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 credit-package-card" data-amount="500" style="cursor:pointer">
                    <div class="card-body text-center py-4">
                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">Best Value</span>
                        <h3 class="mb-1 text-primary">$500</h3>
                        <div class="text-muted small mb-2">Premium Pack</div>
                        <div class="small text-success fw-semibold">Save $30 — Pay $470</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h6>Or enter a custom amount</h6>
            <div class="input-group" style="max-width:300px">
                <span class="input-group-text">$</span>
                <input type="number" class="form-control" id="custom-amount" min="50" max="2000" step="10" placeholder="Min $50">
                <button class="btn btn-outline-primary" type="button" id="custom-amount-btn">Select</button>
            </div>
            <div class="form-text">Minimum top-up: $50</div>
        </div>
    </div>

    {{-- Payment Summary Sidebar --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><h6 class="mb-0">Payment Summary</h6></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Credit Amount</span>
                    <span id="summary-credit">$0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Discount</span>
                    <span class="text-success" id="summary-discount">-$0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total to Pay</span>
                    <span id="summary-total">$0.00</span>
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Payment Method</label>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay-card" value="card" checked>
                        <label class="form-check-label small" for="pay-card"><i class="bi bi-credit-card me-1"></i> Credit / Debit Card</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay-paypal" value="paypal">
                        <label class="form-check-label small" for="pay-paypal"><i class="bi bi-paypal me-1"></i> PayPal</label>
                    </div>
                </div>

                <div id="card-fields" class="mb-3">
                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm" placeholder="Card Number" maxlength="19">
                    </div>
                    <div class="row g-2">
                        <div class="col-6"><input type="text" class="form-control form-control-sm" placeholder="MM / YY" maxlength="7"></div>
                        <div class="col-6"><input type="text" class="form-control form-control-sm" placeholder="CVC" maxlength="4"></div>
                    </div>
                </div>

                <button class="btn btn-primary w-100" id="pay-btn" disabled>
                    <i class="bi bi-lock me-1"></i> Pay Now
                </button>
                <div class="text-center mt-2">
                    <small class="text-muted"><i class="bi bi-shield-check me-1"></i>Secure payment powered by Stripe</small>
                </div>
            </div>
        </div>
        <a href="{{ route('learner.wallet') }}" class="btn btn-link text-muted mt-2"><i class="bi bi-arrow-left me-1"></i>Back to Wallet</a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const packages = {100: {credit: 100, pay: 100}, 250: {credit: 250, pay: 240}, 500: {credit: 500, pay: 470}};
    let selected = null;

    function updateSummary(credit, pay) {
        document.getElementById('summary-credit').textContent = '$' + credit.toFixed(2);
        document.getElementById('summary-discount').textContent = '-$' + (credit - pay).toFixed(2);
        document.getElementById('summary-total').textContent = '$' + pay.toFixed(2);
        document.getElementById('pay-btn').disabled = pay <= 0;
        document.getElementById('pay-btn').textContent = pay > 0 ? 'Pay $' + pay.toFixed(2) + ' Now' : 'Pay Now';
    }

    document.querySelectorAll('.credit-package-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.credit-package-card').forEach(c => c.classList.remove('border-primary', 'border-2'));
            this.classList.add('border-primary', 'border-2');
            const amount = parseInt(this.dataset.amount);
            const pkg = packages[amount];
            selected = pkg;
            updateSummary(pkg.credit, pkg.pay);
        });
    });

    document.getElementById('custom-amount-btn').addEventListener('click', function() {
        const val = parseInt(document.getElementById('custom-amount').value);
        if (val >= 50) {
            document.querySelectorAll('.credit-package-card').forEach(c => c.classList.remove('border-primary', 'border-2'));
            selected = {credit: val, pay: val};
            updateSummary(val, val);
        }
    });

    document.getElementById('pay-btn').addEventListener('click', function() {
        if (!selected) return;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        const csrf = document.querySelector('meta[name="csrf-token"]');
        fetch('/api/learner/wallet/add-credit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf ? csrf.content : '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ amount: selected.credit, payment_method: method })
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(result => {
            if (result.ok) {
                btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Credit Added!';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
                document.getElementById('summary-credit').textContent = '';
                document.getElementById('summary-discount').textContent = '';
                document.getElementById('summary-total').textContent = result.data.data.new_balance_display;
                setTimeout(() => { window.location.href = '{{ route("learner.wallet") }}'; }, 1500);
            } else {
                alert(result.data.message || 'Payment failed. Please try again.');
                btn.disabled = false;
                btn.textContent = 'Pay $' + selected.pay.toFixed(2) + ' Now';
            }
        })
        .catch(() => {
            alert('Something went wrong. Please try again.');
            btn.disabled = false;
            btn.textContent = 'Pay $' + selected.pay.toFixed(2) + ' Now';
        });
    });

    document.querySelectorAll('input[name="payment_method"]').forEach(r => {
        r.addEventListener('change', function() {
            document.getElementById('card-fields').style.display = this.value === 'card' ? 'block' : 'none';
        });
    });
});
</script>
@endpush
@endsection
