@extends('layouts.instructor')

@section('title', 'Reports')
@section('heading', 'Reports')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Reports</li>
        <li class="breadcrumb-item active" id="reports-breadcrumb-tab">Summary</li>
    </ol>
</nav>

<ul class="nav nav-tabs border-0 small mb-4" id="reports-tabs">
    <li class="nav-item"><a class="nav-link active" href="#" data-tab="summary">Summary</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="#" data-tab="this_fy">This Financial Year</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="#" data-tab="fy_2024_25">FY 2024/25</a></li>
</ul>

<div id="reports-loading" class="text-muted">Loading…</div>

<div id="panel-summary" class="report-panel">
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted small mb-1">Earnings</h6>
                    <p class="mb-0 fs-5 fw-bold" id="kpi-earnings">$0.00</p>
                    <a href="#" class="small">Your next payout: <span id="kpi-next-payout-date">—</span> &gt;</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted small mb-1">Cancellation Rate</h6>
                    <p class="mb-0 fs-5 fw-bold" id="kpi-cancellation">0%</p>
                    <a href="#" class="small">Your cancels in the last 90 days &gt;</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted small mb-1">Booking hours per learner</h6>
                    <p class="mb-0 fs-5 fw-bold" id="kpi-hours-learner">—</p>
                    <span class="small text-muted">Excludes new learners (within 90 days)</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted small mb-1">Learner rating</h6>
                    <p class="mb-0 fs-5 fw-bold" id="kpi-rating">—</p>
                    <a href="#" class="small">Your reviews &gt;</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-2">Earnings report</h6>
            <p class="small text-muted mb-3">Your payouts by month (last 12 months)</p>
            <div id="earnings-chart" class="d-flex align-items-end gap-1" style="min-height: 180px;"></div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="card border-0 shadow-sm"><div class="card-body small"><strong>Next Payout</strong><br><span id="sum-next-payout">$0.00</span><br><span class="text-muted" id="sum-next-date">—</span></div></div></div>
        <div class="col-6 col-md-3"><div class="card border-0 shadow-sm"><div class="card-body small"><strong>Previous Payout</strong><br><span id="sum-prev-payout">$0.00</span><br><span class="text-muted" id="sum-prev-date">—</span></div></div></div>
        <div class="col-6 col-md-3"><div class="card border-0 shadow-sm"><div class="card-body small"><strong>FYTD Payout</strong><br><span id="sum-fytd">$0.00</span><br><span class="text-muted" id="sum-fytd-fy">—</span></div></div></div>
        <div class="col-6 col-md-3"><div class="card border-0 shadow-sm"><div class="card-body small"><strong>All Time Earnings</strong><br><span id="sum-all-time">$0.00</span></div></div></div>
        <div class="col-6 col-md-3"><div class="card border-0 shadow-sm"><div class="card-body small"><strong>Ave Weekly Earnings (last 90 days)</strong><br><span id="sum-ave-weekly">$0.00</span></div></div></div>
        <div class="col-6 col-md-3"><div class="card border-0 shadow-sm"><div class="card-body small"><strong>Ave Earnings Per Hour (last 90 days)</strong><br><span id="sum-ave-hour">$0.00</span></div></div></div>
        <div class="col-6 col-md-3"><div class="card border-0 shadow-sm"><div class="card-body small"><strong>Upcoming Bookings</strong><br><span id="sum-upcoming">$0.00</span></div></div></div>
        <div class="col-6 col-md-3"><div class="card border-0 shadow-sm"><div class="card-body small"><strong>Credits Held By Your Learners</strong><br><span id="sum-credits">$0.00</span></div></div></div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Booking Activity</h6>
            <div class="row g-2 small">
                <div class="col-6 col-md-4"><span id="act-searches">0</span> Searches in your area</div>
                <div class="col-6 col-md-4"><span id="act-test-packages">0</span> Test packages</div>
                <div class="col-6 col-md-4"><span id="act-total-hrs">0</span> Total booking hrs</div>
                <div class="col-6 col-md-4"><span id="act-learners">0</span> Learners</div>
                <div class="col-6 col-md-4"><span id="act-upcoming-hrs">0</span> Upcoming hrs booked</div>
                <div class="col-6 col-md-4"><span id="act-lesson-hrs">0</span> Lesson hrs completed</div>
            </div>
        </div>
    </div>
</div>

<div id="panel-this_fy" class="report-panel" style="display: none;">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-2">Pending Payout</h6>
            <p class="small text-muted mb-2">Next Payout - <span id="pending-date-fy">—</span></p>
            <div class="form-check form-check-inline"><input type="checkbox" class="form-check-input" id="show-cancelled"><label class="form-check-label small" for="show-cancelled">Show cancelled lessons</label></div>
            <div class="form-check form-check-inline"><input type="checkbox" class="form-check-input" id="show-private"><label class="form-check-label small" for="show-private">Show private lessons</label></div>
            <div class="table-responsive mt-2">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Trainee</th><th>Date/Time</th><th>Payout</th><th>Lesson ID</th></tr></thead>
                    <tbody id="pending-tbody"></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-2">Transaction Summary (YTD)</h6>
            <p class="small text-muted mb-1"><span id="fy-current-label">—</span> <span id="fy-current-period">—</span></p>
            <p class="mb-1 fw-bold">Total Payout <span id="fy-current-total">$0.00</span></p>
            <a href="#" class="small">Transaction Summary</a>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Fortnightly Reports</h6>
            <table class="table table-sm mb-0">
                <thead><tr><th>Transaction ID</th><th>Date</th><th>Payout</th><th>Tax Invoice</th><th>Transaction Report</th></tr></thead>
                <tbody id="fortnightly-current-tbody"></tbody>
            </table>
        </div>
    </div>
</div>

<div id="panel-fy_2024_25" class="report-panel" style="display: none;">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-2">Transaction Summary (YTD)</h6>
            <p class="small text-muted mb-1"><span id="fy-prev-label">—</span> <span id="fy-prev-period">—</span></p>
            <p class="mb-1 fw-bold">Payout <span id="fy-prev-total">$0.00</span></p>
            <a href="#" class="small">Transaction Summary</a>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Fortnightly Reports</h6>
            <table class="table table-sm mb-0">
                <thead><tr><th>Report ID</th><th>Date</th><th>Payout</th><th>Tax Invoice</th><th>Transaction Report</th></tr></thead>
                <tbody id="fortnightly-prev-tbody"></tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
  var data = {};
  var loading = document.getElementById('reports-loading');
  var panels = document.querySelectorAll('.report-panel');

  function showTab(tabId) {
    document.querySelectorAll('#reports-tabs .nav-link').forEach(function(a) {
      a.classList.toggle('active', a.getAttribute('data-tab') === tabId);
      a.classList.toggle('text-dark', a.getAttribute('data-tab') !== tabId);
    });
    panels.forEach(function(p) {
      p.style.display = (p.id === 'panel-' + tabId) ? 'block' : 'none';
    });
    var labels = { summary: 'Summary', this_fy: 'This Financial Year', fy_2024_25: 'FY 2024/25' };
    var bc = document.getElementById('reports-breadcrumb-tab');
    if (bc) bc.textContent = labels[tabId] || tabId;
  }

  function formatMoney(n) {
    return '$' + (Number(n) || 0).toLocaleString('en-AU', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function renderSummary() {
    var s = data.summary || {};
    document.getElementById('kpi-earnings').textContent = s.earnings_display || '$0.00';
    document.getElementById('kpi-next-payout-date').textContent = s.next_payout_date || '—';
    document.getElementById('kpi-cancellation').textContent = (s.cancellation_rate != null ? s.cancellation_rate : 0) + '%';
    document.getElementById('kpi-hours-learner').textContent = s.booking_hours_per_learner != null ? s.booking_hours_per_learner : '—';
    document.getElementById('kpi-rating').textContent = s.learner_rating != null ? s.learner_rating : '—';

    var months = s.earnings_by_month || [];
    var chartEl = document.getElementById('earnings-chart');
    var maxVal = Math.max(1, Math.max.apply(null, months.map(function(m) { return m.amount || 0; })));
    chartEl.innerHTML = months.map(function(m) {
      var pct = maxVal > 0 ? (100 * (m.amount || 0) / maxVal) : 0;
      return '<div class="flex-grow-1 d-flex flex-column align-items-center"><div class="w-100 rounded" style="height:' + (40 + (pct * 1.2)) + 'px;background:rgba(240,173,78,0.5);" title="' + (m.month || '') + ' ' + formatMoney(m.amount) + '"></div><span class="small mt-1">' + (m.month || '') + '</span></div>';
    }).join('');

    document.getElementById('sum-next-payout').textContent = formatMoney(s.next_payout_amount);
    document.getElementById('sum-next-date').textContent = s.next_payout_date || '—';
    document.getElementById('sum-prev-payout').textContent = formatMoney(s.previous_payout);
    document.getElementById('sum-prev-date').textContent = '—';
    document.getElementById('sum-fytd').textContent = formatMoney(s.fytd_payout);
    document.getElementById('sum-fytd-fy').textContent = s.fytd_fy || '—';
    document.getElementById('sum-all-time').textContent = formatMoney(s.all_time_earnings);
    document.getElementById('sum-ave-weekly').textContent = formatMoney(s.ave_weekly_earnings_90);
    document.getElementById('sum-ave-hour').textContent = formatMoney(s.ave_earnings_per_hour_90);
    document.getElementById('sum-upcoming').textContent = formatMoney(s.upcoming_bookings);
    document.getElementById('sum-credits').textContent = formatMoney(s.credits_held);

    document.getElementById('act-searches').textContent = s.searches_in_area != null ? Number(s.searches_in_area).toLocaleString() : '0';
    document.getElementById('act-test-packages').textContent = s.test_packages != null ? s.test_packages : '0';
    document.getElementById('act-total-hrs').textContent = s.total_booking_hrs != null ? s.total_booking_hrs : '0';
    document.getElementById('act-learners').textContent = s.learners_count != null ? s.learners_count : '0';
    document.getElementById('act-upcoming-hrs').textContent = s.upcoming_hrs_booked != null ? s.upcoming_hrs_booked : '0';
    document.getElementById('act-lesson-hrs').textContent = s.lesson_hrs_completed != null ? s.lesson_hrs_completed : '0';
  }

  function renderThisFy() {
    var pp = data.pending_payout || {};
    document.getElementById('pending-date-fy').textContent = pp.next_date || '—';
    var rows = pp.rows || [];
    document.getElementById('pending-tbody').innerHTML = rows.length ? rows.map(function(r) {
      return '<tr><td>' + (r.learner_name || '—') + '</td><td>' + (r.scheduled_at || '—') + '</td><td>' + formatMoney(r.payout) + '</td><td>' + (r.lesson_id || '—') + '</td></tr>';
    }).join('') : '<tr><td colspan="4" class="text-muted">No pending payouts</td></tr>';

    var fy = data.fy_current || {};
    document.getElementById('fy-current-label').textContent = fy.label || '—';
    document.getElementById('fy-current-period').textContent = fy.period || '—';
    document.getElementById('fy-current-total').textContent = formatMoney(fy.total_payout);

    var fort = fy.fortnightly || [];
    document.getElementById('fortnightly-current-tbody').innerHTML = fort.length ? fort.map(function(r) {
      return '<tr><td>' + (r.transaction_id || '—') + '</td><td>' + (r.date || '—') + '</td><td>' + formatMoney(r.payout) + '</td><td><a href="#">Tax Invoice</a></td><td><a href="#">Transaction Report</a></td></tr>';
    }).join('') : '<tr><td colspan="5" class="text-muted">No reports</td></tr>';
  }

  function renderFyPrev() {
    var fy = data.fy_previous || {};
    document.getElementById('fy-prev-label').textContent = fy.label || '—';
    document.getElementById('fy-prev-period').textContent = fy.period || '—';
    document.getElementById('fy-prev-total').textContent = formatMoney(fy.total_payout);

    var fort = fy.fortnightly || [];
    document.getElementById('fortnightly-prev-tbody').innerHTML = fort.length ? fort.map(function(r) {
      return '<tr><td>' + (r.transaction_id || '—') + '</td><td>' + (r.date || '—') + '</td><td>' + formatMoney(r.payout) + '</td><td><a href="#">Tax Invoice</a></td><td><a href="#">Transaction Report</a></td></tr>';
    }).join('') : '<tr><td colspan="5" class="text-muted">No reports</td></tr>';
  }

  document.querySelectorAll('#reports-tabs [data-tab]').forEach(function(a) {
    a.addEventListener('click', function(e) {
      e.preventDefault();
      showTab(this.getAttribute('data-tab'));
    });
  });

  fetch('/api/instructor/reports', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      data = res.data || res || {};
      loading.style.display = 'none';
      document.getElementById('panel-summary').style.display = 'block';
      renderSummary();
      renderThisFy();
      renderFyPrev();
    })
    .catch(function() {
      loading.textContent = 'Could not load reports.';
    });
})();
</script>
@endpush
@endsection
