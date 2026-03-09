@extends('layouts.instructor')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <h5 class="mb-0">Bookings</h5>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary" id="dashboard-invite-learner-btn" title="Coming soon">
            <i class="bi bi-person-plus me-1"></i> Invite Learner
        </button>
        <a href="{{ route('instructor.learners') }}?open=propose" class="btn btn-warning" id="dashboard-propose-booking-btn">
            <i class="bi bi-car-front me-1"></i> Propose Booking
        </a>
    </div>
</div>

{{-- KPI cards (from reports summary) --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted small mb-1">Earnings</h6>
                <p class="mb-0 fs-5 fw-bold" id="kpi-earnings">—</p>
                <a href="{{ route('instructor.reports') }}" class="small">Your next payout: <span id="kpi-next-payout-date">—</span> &gt;</a>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted small mb-1">Cancellation Rate</h6>
                <p class="mb-0 fs-5 fw-bold" id="kpi-cancellation">—</p>
                <a href="{{ route('instructor.reports') }}" class="small">Your cancels in the last 90 days &gt;</a>
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
                <a href="{{ route('instructor.reports') }}" class="small">Your reviews &gt;</a>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs border-0 small mb-3" id="bookings-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-upcoming" data-bs-toggle="tab" data-bs-target="#panel-upcoming" type="button" role="tab">Upcoming</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-pending" data-bs-toggle="tab" data-bs-target="#panel-pending" type="button" role="tab">Pending proposals</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-history" data-bs-toggle="tab" data-bs-target="#panel-history" type="button" role="tab">Booking history</button>
    </li>
</ul>

{{-- Tab: Upcoming --}}
<div class="tab-content" id="bookings-tab-content">
    <div class="tab-pane fade show active" id="panel-upcoming" role="tabpanel">
        <div id="upcoming-loading" class="text-muted py-4">Loading…</div>
        <div id="upcoming-list" style="display: none;"></div>
        <div id="upcoming-empty" class="p-4 text-center text-muted" style="display: none;">No upcoming bookings.</div>
        <nav id="upcoming-pagination" class="mt-3" style="display: none;" aria-label="Upcoming pagination"></nav>
    </div>

    {{-- Tab: Pending proposals --}}
    <div class="tab-pane fade" id="panel-pending" role="tabpanel">
        <div id="pending-loading" class="text-muted py-4">Loading…</div>
        <div id="pending-list" style="display: none;"></div>
        <div id="pending-empty" class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center text-muted">
                No pending booking proposals. <a href="{{ route('instructor.learners') }}?open=propose" id="pending-create-link">Create a new proposal.</a>
            </div>
        </div>
        <nav id="pending-pagination" class="mt-3" style="display: none;" aria-label="Pending pagination"></nav>
    </div>

    {{-- Tab: Booking history --}}
    <div class="tab-pane fade" id="panel-history" role="tabpanel">
        <div id="history-loading" class="text-muted py-4">Loading…</div>
        <div id="history-wrap" class="card border-0 shadow-sm" style="display: none;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Booking</th>
                                <th>Learner</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Payment</th>
                            </tr>
                        </thead>
                        <tbody id="history-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="history-empty" class="p-4 text-center text-muted" style="display: none;">No booking history.</div>
        <nav id="history-pagination" class="mt-3" style="display: none;" aria-label="History pagination"></nav>
    </div>
</div>

<style>
.nav-tabs .nav-link { color: #333; }
.nav-tabs .nav-link.active { border-bottom: 2px solid #f0ad4e; font-weight: 500; color: #333; }
.badge-confirmed { background: #28a745; color: #fff; }
.badge-new-learner { background: #17a2b8; color: #fff; }
.badge-cancelled { background: #dc3545; color: #fff; }
.badge-completed { background: #28a745; color: #fff; }
.badge-returned { background: #007bff; color: #fff; }
.badge-processed { background: #28a745; color: #fff; }
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

  function formatDate(iso) {
    if (!iso) return '—';
    var d = new Date(iso);
    var days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return days[d.getDay()] + ', ' + ('0' + d.getDate()).slice(-2) + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
  }

  function formatTime(iso, durationMin) {
    if (!iso) return '—';
    var d = new Date(iso);
    var end = new Date(d.getTime() + (durationMin || 60) * 60000);
    var fmt = function(t) {
      var h = t.getHours(); var m = t.getMinutes();
      var am = h < 12; h = h % 12; if (h === 0) h = 12;
      return h + ':' + ('0' + m).slice(-2) + (am ? ' am' : ' pm');
    };
    return fmt(d) + ' - ' + fmt(end);
  }

  function lessonLabel(b) {
    var dur = b.duration_minutes || 60;
    var h = Math.floor(dur / 60);
    var type = (b.type === 'test_package') ? 'Test Package' : (h + ' Hour Driving Lesson');
    var trans = (b.transmission === 'manual') ? ' (Manual)' : ' (Auto)';
    return type + trans;
  }

  // ——— KPI from reports ———
  function loadSummary() {
    fetch('/api/instructor/reports', opts)
      .then(function(r) { return r.json(); })
      .then(function(res) {
        var s = (res.data && res.data.summary) ? res.data.summary : {};
        document.getElementById('kpi-earnings').textContent = s.earnings_display || '$0.00';
        document.getElementById('kpi-next-payout-date').textContent = s.next_payout_date || '—';
        document.getElementById('kpi-cancellation').textContent = (s.cancellation_rate != null ? (s.cancellation_rate * 100).toFixed(1) : '0') + '%';
        document.getElementById('kpi-hours-learner').textContent = s.booking_hours_per_learner != null ? String(s.booking_hours_per_learner) : '—';
        document.getElementById('kpi-rating').textContent = s.learner_rating != null ? String(s.learner_rating) : '—';
      })
      .catch(function() {});
  }

  // ——— Upcoming ———
  function renderUpcoming(data) {
    var list = document.getElementById('upcoming-list');
    var loading = document.getElementById('upcoming-loading');
    var empty = document.getElementById('upcoming-empty');
    var pagination = document.getElementById('upcoming-pagination');
    loading.style.display = 'none';
    var items = data.data || [];
    if (items.length === 0) {
      list.style.display = 'none';
      empty.style.display = 'block';
      pagination.style.display = 'none';
      return;
    }
    empty.style.display = 'none';
    list.style.display = 'block';
    list.innerHTML = items.map(function(b) {
      var statusBadge = b.status === 'confirmed' ? '<span class="badge badge-confirmed me-1">CONFIRMED</span>' : '';
      var newBadge = ''; // optional: NEW LEARNER from backend later
      var location = (b.suburb && b.suburb.location) ? b.suburb.location : (b.suburb ? (b.suburb.name + ' ' + (b.suburb.postcode || '')) : '—');
      var learnerName = (b.learner && b.learner.name) ? esc(b.learner.name) : '—';
      var learnerPhone = (b.learner && b.learner.phone) ? esc(b.learner.phone) : '—';
      return '<div class="card border-0 shadow-sm mb-2">' +
        '<div class="card-body">' +
          '<div class="d-flex justify-content-between align-items-start flex-wrap gap-2">' +
            '<div><span class="text-muted">Booking #' + b.id + '</span> ' + statusBadge + newBadge + '</div>' +
            '<a href="#" class="small">See more / Manage</a>' +
          '</div>' +
          '<div class="row mt-2 small">' +
            '<div class="col-md-4"><i class="bi bi-calendar3 me-1 text-muted"></i>' + formatDate(b.scheduled_at) + '</div>' +
            '<div class="col-md-4"><i class="bi bi-clock me-1 text-muted"></i>' + formatTime(b.scheduled_at, b.duration_minutes) + '</div>' +
            '<div class="col-md-4"><i class="bi bi-geo-alt me-1 text-muted"></i>' + esc(location) + '</div>' +
          '</div>' +
          '<div class="row mt-1 small">' +
            '<div class="col-md-4"><i class="bi bi-car-front me-1 text-muted"></i>' + esc(lessonLabel(b)) + '</div>' +
            '<div class="col-md-4"><i class="bi bi-person me-1 text-muted"></i>' + learnerName + '</div>' +
            '<div class="col-md-4"><i class="bi bi-telephone me-1 text-muted"></i><a href="tel:' + esc(learnerPhone) + '">' + learnerPhone + '</a></div>' +
          '</div>' +
        '</div></div>';
    }).join('');
    renderPagination(pagination, data, 'upcoming');
    pagination.style.display = (data.last_page > 1) ? 'block' : 'none';
  }

  function loadUpcoming(page) {
    page = page || 1;
    document.getElementById('upcoming-loading').style.display = 'block';
    document.getElementById('upcoming-list').style.display = 'none';
    document.getElementById('upcoming-empty').style.display = 'none';
    fetch('/api/bookings?tab=upcoming&page=' + page, opts)
      .then(function(r) { return r.json(); })
      .then(function(data) { renderUpcoming(data); })
      .catch(function() {
        document.getElementById('upcoming-loading').style.display = 'none';
        document.getElementById('upcoming-list').innerHTML = '<p class="text-muted">Could not load upcoming bookings.</p>';
        document.getElementById('upcoming-list').style.display = 'block';
      });
  }

  // ——— Pending ———
  function renderPending(data) {
    var list = document.getElementById('pending-list');
    var loading = document.getElementById('pending-loading');
    var empty = document.getElementById('pending-empty');
    var pagination = document.getElementById('pending-pagination');
    loading.style.display = 'none';
    var items = data.data || [];
    if (items.length === 0) {
      list.style.display = 'none';
      empty.style.display = 'block';
      pagination.style.display = 'none';
      return;
    }
    empty.style.display = 'none';
    list.style.display = 'block';
    list.innerHTML = items.map(function(b) {
      var location = (b.suburb && b.suburb.location) ? b.suburb.location : (b.suburb ? (b.suburb.name + ' ' + (b.suburb.postcode || '')) : '—');
      var learnerName = (b.learner && b.learner.name) ? esc(b.learner.name) : '—';
      return '<div class="card border-0 shadow-sm mb-2">' +
        '<div class="card-body">' +
          '<div class="d-flex justify-content-between align-items-start">' +
            '<span class="text-muted">Booking #' + b.id + '</span>' +
            '<a href="#" class="small">See more / Manage</a>' +
          '</div>' +
          '<div class="mt-2 small">' + formatDate(b.scheduled_at) + ' · ' + formatTime(b.scheduled_at, b.duration_minutes) + ' · ' + esc(location) + ' · ' + learnerName + '</div>' +
        '</div></div>';
    }).join('');
    renderPagination(pagination, data, 'pending');
    pagination.style.display = (data.last_page > 1) ? 'block' : 'none';
  }

  function loadPending(page) {
    page = page || 1;
    document.getElementById('pending-loading').style.display = 'block';
    document.getElementById('pending-list').style.display = 'none';
    document.getElementById('pending-empty').style.display = 'none';
    fetch('/api/bookings?tab=pending&page=' + page, opts)
      .then(function(r) { return r.json(); })
      .then(function(data) { renderPending(data); })
      .catch(function() {
        document.getElementById('pending-loading').style.display = 'none';
        document.getElementById('pending-empty').style.display = 'block';
      });
  }

  // ——— History ———
  function renderHistory(data) {
    var wrap = document.getElementById('history-wrap');
    var tbody = document.getElementById('history-tbody');
    var loading = document.getElementById('history-loading');
    var empty = document.getElementById('history-empty');
    var pagination = document.getElementById('history-pagination');
    loading.style.display = 'none';
    var items = data.data || [];
    if (items.length === 0) {
      wrap.style.display = 'none';
      empty.style.display = 'block';
      pagination.style.display = 'none';
      return;
    }
    empty.style.display = 'none';
    wrap.style.display = 'block';
    tbody.innerHTML = items.map(function(b) {
      var status = (b.status === 'cancelled') ? 'CANCELLED' : 'COMPLETED';
      var statusClass = (b.status === 'cancelled') ? 'badge-cancelled' : 'badge-completed';
      var pay = (b.payment_status === 'RETURNED') ? 'RETURNED' : (b.payment_status === 'PROCESSED' ? 'PROCESSED' : '—');
      var payClass = (b.payment_status === 'RETURNED') ? 'badge-returned' : 'badge-processed';
      var location = (b.suburb && b.suburb.location) ? b.suburb.location : (b.suburb ? (b.suburb.name + ' ' + (b.suburb.postcode || '')) : '—');
      var learnerName = (b.learner && b.learner.name) ? esc(b.learner.name) : '—';
      return '<tr>' +
        '<td>#' + b.id + '</td>' +
        '<td>' + learnerName + '</td>' +
        '<td>' + formatDate(b.scheduled_at) + '</td>' +
        '<td>' + formatTime(b.scheduled_at, b.duration_minutes) + '</td>' +
        '<td>' + esc(location) + '</td>' +
        '<td><span class="badge ' + statusClass + '">' + status + '</span></td>' +
        '<td>' + (pay !== '—' ? '<span class="badge ' + payClass + '">' + pay + '</span>' : '—') + '</td>' +
      '</tr>';
    }).join('');
    renderPagination(pagination, data, 'history');
    pagination.style.display = (data.last_page > 1) ? 'block' : 'none';
  }

  function loadHistory(page) {
    page = page || 1;
    document.getElementById('history-loading').style.display = 'block';
    document.getElementById('history-wrap').style.display = 'none';
    document.getElementById('history-empty').style.display = 'none';
    fetch('/api/bookings?tab=history&page=' + page, opts)
      .then(function(r) { return r.json(); })
      .then(function(data) { renderHistory(data); })
      .catch(function() {
        document.getElementById('history-loading').style.display = 'none';
        document.getElementById('history-empty').textContent = 'Could not load booking history.';
        document.getElementById('history-empty').style.display = 'block';
      });
  }

  function renderPagination(navEl, data, tab) {
    var cur = data.current_page || 1;
    var last = data.last_page || 1;
    if (last <= 1) { navEl.innerHTML = ''; return; }
    var parts = [];
    if (cur > 1) parts.push('<li class="page-item"><a class="page-link" href="#" data-page="1">First</a></li><li class="page-item"><a class="page-link" href="#" data-page="' + (cur - 1) + '">Prev</a></li>');
    for (var i = 1; i <= last; i++) {
      if (i === cur) parts.push('<li class="page-item active"><span class="page-link">' + i + '</span></li>');
      else parts.push('<li class="page-item"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>');
    }
    if (cur < last) parts.push('<li class="page-item"><a class="page-link" href="#" data-page="' + (cur + 1) + '">Next</a></li><li class="page-item"><a class="page-link" href="#" data-page="' + last + '">Last</a></li>');
    navEl.innerHTML = '<ul class="pagination pagination-sm mb-0">' + parts.join('') + '</ul>';
    navEl.querySelectorAll('a[data-page]').forEach(function(a) {
      a.addEventListener('click', function(e) {
        e.preventDefault();
        var p = parseInt(a.getAttribute('data-page'), 10);
        if (tab === 'upcoming') loadUpcoming(p);
        else if (tab === 'pending') loadPending(p);
        else loadHistory(p);
      });
    });
  }

  document.getElementById('tab-upcoming').addEventListener('shown.bs.tab', function() { loadUpcoming(1); });
  document.getElementById('tab-pending').addEventListener('shown.bs.tab', function() { loadPending(1); });
  document.getElementById('tab-history').addEventListener('shown.bs.tab', function() { loadHistory(1); });

  loadSummary();
  loadUpcoming(1);
})();
</script>
@endpush
@endsection
