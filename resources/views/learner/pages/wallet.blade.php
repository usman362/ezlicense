@extends('layouts.learner')

@section('title', 'Wallet')
@section('heading', 'Wallet')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Wallet</li>
    </ol>
</nav>

<h5 class="mb-4">Wallet</h5>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted small mb-2">Payment Method</h6>
                <p class="mb-0" id="wallet-payment-method">No saved payment method.</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="text-muted small mb-0">My Wallet</h6>
                    <a href="{{ route('learner.wallet.add-credit') }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-wallet2 me-1"></i> Add Credit
                    </a>
                </div>
                <p class="mb-1 fs-4 fw-bold" id="wallet-balance">$0</p>
                <p class="mb-0 small text-muted">Includes <span id="wallet-non-refundable">$0.00</span> of non refundable credit.</p>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <ul class="nav nav-tabs border-0 small mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-all-transactions" data-bs-toggle="tab" data-bs-target="#panel-transactions" type="button" role="tab">All Transactions</button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="panel-transactions" role="tabpanel">
                <div id="transactions-loading" class="text-muted py-3">Loading…</div>
                <div id="transactions-wrap" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody id="transactions-tbody"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0 mt-2" id="transactions-summary"></p>
                    <nav id="transactions-pagination" class="mt-2" aria-label="Transactions pagination"></nav>
                </div>
                <div id="transactions-empty" class="py-4 text-center text-muted" style="display: none;">No transactions yet.</div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-tabs .nav-link { color: #333; }
.nav-tabs .nav-link.active { border-bottom: 2px solid #f0ad4e; font-weight: 500; color: #333; }
.amount-debit { color: #dc3545; }
.amount-credit { color: #28a745; }
</style>
@push('scripts')
<script>
(function() {
  var csrf = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').content;
  var opts = { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf || '', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' };

  function esc(s) {
    if (s == null || s === '') return '—';
    var d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
  }

  function loadWallet() {
    fetch('/api/learner/wallet', opts)
      .then(function(r) { return r.json(); })
      .then(function(res) {
        var d = res.data || {};
        document.getElementById('wallet-balance').textContent = d.balance_display || '$0.00';
        document.getElementById('wallet-non-refundable').textContent = d.non_refundable_credit_display || '$0.00';
        document.getElementById('wallet-payment-method').textContent = d.saved_payment_method ? 'Saved card' : 'No saved payment method.';
      })
      .catch(function() {});
  }

  function renderTransactions(data) {
    var loading = document.getElementById('transactions-loading');
    var wrap = document.getElementById('transactions-wrap');
    var tbody = document.getElementById('transactions-tbody');
    var empty = document.getElementById('transactions-empty');
    var summary = document.getElementById('transactions-summary');
    var pagination = document.getElementById('transactions-pagination');
    loading.style.display = 'none';
    var items = data.data || [];
    if (items.length === 0) {
      wrap.style.display = 'none';
      empty.style.display = 'block';
      summary.textContent = '';
      pagination.innerHTML = '';
      return;
    }
    empty.style.display = 'none';
    wrap.style.display = 'block';
    tbody.innerHTML = items.map(function(t) {
      var amountClass = (t.amount >= 0) ? 'amount-credit' : 'amount-debit';
      var amountDisplay = (t.amount >= 0 ? '+' : '') + (t.amount_display || ('$' + Math.abs(t.amount).toFixed(2)));
      return '<tr>' +
        '<td>' + esc(t.transaction_id || '#' + t.id) + '</td>' +
        '<td>' + esc(t.description) + '</td>' +
        '<td>' + esc(t.date) + '</td>' +
        '<td class="' + amountClass + '">' + amountDisplay + '</td>' +
        '<td>' + esc(t.balance_after_display) + '</td>' +
      '</tr>';
    }).join('');
    var total = data.total || items.length;
    var from = data.from || 1;
    var to = data.to || items.length;
    summary.textContent = 'Displaying ' + (total <= 0 ? 0 : (from + ' to ' + to + ' of ' + total + ' transactions'));
    if (data.last_page > 1) {
      var cur = data.current_page || 1;
      var last = data.last_page;
      var parts = [];
      if (cur > 1) parts.push('<li class="page-item"><a class="page-link" href="#" data-page="' + (cur - 1) + '">Prev</a></li>');
      for (var i = 1; i <= Math.min(last, 5); i++) {
        if (i === cur) parts.push('<li class="page-item active"><span class="page-link">' + i + '</span></li>');
        else parts.push('<li class="page-item"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>');
      }
      if (cur < last) parts.push('<li class="page-item"><a class="page-link" href="#" data-page="' + (cur + 1) + '">Next</a></li>');
      pagination.innerHTML = '<ul class="pagination pagination-sm mb-0">' + parts.join('') + '</ul>';
      pagination.querySelectorAll('a[data-page]').forEach(function(a) {
        a.addEventListener('click', function(e) { e.preventDefault(); loadTransactions(parseInt(a.getAttribute('data-page'), 10)); });
      });
    } else {
      pagination.innerHTML = '';
    }
  }

  function loadTransactions(page) {
    page = page || 1;
    document.getElementById('transactions-loading').style.display = 'block';
    document.getElementById('transactions-wrap').style.display = 'none';
    document.getElementById('transactions-empty').style.display = 'none';
    fetch('/api/learner/wallet/transactions?page=' + page, opts)
      .then(function(r) { return r.json(); })
      .then(function(data) { renderTransactions(data); })
      .catch(function() {
        document.getElementById('transactions-loading').style.display = 'none';
        document.getElementById('transactions-empty').textContent = 'Could not load transactions.';
        document.getElementById('transactions-empty').style.display = 'block';
      });
  }

  loadWallet();
  loadTransactions(1);
})();
</script>
@endpush
@endsection
