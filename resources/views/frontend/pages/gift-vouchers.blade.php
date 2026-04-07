@extends('layouts.frontend')

@section('title', 'Gift Vouchers - Driving Lessons')

@section('content')
<section class="bg-light py-4">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Driving Lessons</a></li>
            <li class="breadcrumb-item active">Gift Vouchers</li>
        </ol></nav>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <h1 class="display-5 fw-bold mb-3">Driving Lesson Gift Vouchers</h1>
                <p class="lead text-muted">Give the gift of independence. Driving lesson gift vouchers are the perfect present for learner drivers of all ages.</p>
                <p class="text-muted">Whether it's a birthday, Christmas, graduation, or just because, a driving lesson voucher is a gift that truly makes a difference. Redeemable with any instructor on the Secure Licences platform across Australia.</p>
            </div>
            <div class="col-lg-5 text-center">
                <div class="bg-warning bg-opacity-10 rounded-4 p-5">
                    <i class="bi bi-gift display-1 text-warning"></i>
                    <p class="fw-bold mt-3 mb-0">Available from $50</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="h3 fw-bold text-center mb-5">Choose your voucher</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-clock display-4 text-warning mb-3 d-block"></i>
                        <h5 class="fw-bold">1 Hour Lesson</h5>
                        <p class="display-6 fw-bold text-warning my-3">$65<span class="small text-muted fw-normal">.00</span></p>
                        <p class="text-muted small">One hour driving lesson with a verified instructor. Perfect starter gift.</p>
                        <a href="{{ route('contact') }}" class="btn btn-warning fw-bold w-100">Buy Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-warning border-2 shadow h-100 position-relative">
                    <span class="position-absolute top-0 start-50 translate-middle badge bg-warning text-dark px-3 py-2">Most Popular</span>
                    <div class="card-body text-center p-4 pt-5">
                        <i class="bi bi-clock-history display-4 text-warning mb-3 d-block"></i>
                        <h5 class="fw-bold">5 Hour Package</h5>
                        <p class="display-6 fw-bold text-warning my-3">$300<span class="small text-muted fw-normal">.00</span></p>
                        <p class="text-muted small">Five hours of driving lessons. Great value and enough to build real confidence.</p>
                        <a href="{{ route('contact') }}" class="btn btn-warning fw-bold w-100">Buy Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-cash-coin display-4 text-warning mb-3 d-block"></i>
                        <h5 class="fw-bold">Custom Amount</h5>
                        <p class="display-6 fw-bold text-warning my-3">$50<span class="small text-muted fw-normal">+</span></p>
                        <p class="text-muted small">Choose any amount from $50. The recipient can use it towards any lesson or package.</p>
                        <a href="{{ route('contact') }}" class="btn btn-warning fw-bold w-100">Buy Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h2 class="h3 fw-bold text-center mb-5">How gift vouchers work</h2>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;font-weight:700;font-size:1.2rem;">1</div>
                <h6 class="fw-bold">Purchase a voucher</h6>
                <p class="small text-muted">Choose a lesson package or custom amount. Pay securely online and receive the voucher by email.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;font-weight:700;font-size:1.2rem;">2</div>
                <h6 class="fw-bold">Gift it</h6>
                <p class="small text-muted">Forward the voucher email or print it out. Add a personal message to make it extra special.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;font-weight:700;font-size:1.2rem;">3</div>
                <h6 class="fw-bold">Redeem</h6>
                <p class="small text-muted">The recipient creates an account, enters the voucher code, and books lessons with any instructor.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <h2 class="h3 fw-bold mb-3">Have a voucher code?</h2>
                <p class="text-muted mb-4">If you've received a gift voucher, enter the code below to check its value or redeem it to your account.</p>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="input-group mb-3">
                            <input type="text" id="voucher-code-input" class="form-control form-control-lg text-center text-uppercase" placeholder="SL-XXXXXXXXXX" maxlength="16">
                            <button class="btn btn-warning fw-bold" id="voucher-check-btn">Check Voucher</button>
                        </div>
                        <div id="voucher-result" style="display:none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.getElementById('voucher-check-btn')?.addEventListener('click', async function() {
    const code = document.getElementById('voucher-code-input').value.trim();
    const resultEl = document.getElementById('voucher-result');
    if (!code) return;

    resultEl.style.display = 'block';
    resultEl.innerHTML = '<div class="text-muted small">Checking...</div>';

    try {
        const resp = await fetch('/api/gift-vouchers/check', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
            body: JSON.stringify({ code })
        });
        const json = await resp.json();
        if (!resp.ok) {
            resultEl.innerHTML = '<div class="alert alert-danger small mb-0"><i class="bi bi-x-circle me-1"></i>' + (json.message || 'Voucher not found.') + '</div>';
            return;
        }
        const d = json.data;
        const statusClass = d.is_active ? 'success' : 'secondary';
        resultEl.innerHTML = '<div class="alert alert-' + statusClass + ' small mb-0">' +
            '<div class="fw-bold mb-1"><i class="bi bi-gift me-1"></i>Voucher: ' + d.code + '</div>' +
            '<div>Value: $' + parseFloat(d.amount).toFixed(2) + ' | Remaining: $' + parseFloat(d.remaining_amount).toFixed(2) + '</div>' +
            '<div>Status: ' + d.status_label + '</div>' +
            (d.is_active ? '<a href="/learner/login" class="btn btn-sm btn-success mt-2">Log in to Redeem</a>' : '') +
            '</div>';
    } catch (e) {
        resultEl.innerHTML = '<div class="alert alert-danger small mb-0">Error checking voucher. Please try again.</div>';
    }
});
</script>
@endpush
