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
        <button type="button" class="btn btn-outline-secondary" id="dashboard-invite-learner-btn">
            <i class="bi bi-person-plus me-1"></i> Invite Learner
        </button>
        <a href="{{ route('instructor.learners') }}?open=propose" class="btn btn-warning" id="dashboard-propose-booking-btn">
            <i class="bi bi-car-front me-1"></i> Propose Booking
        </a>
    </div>
</div>

{{-- KPI cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="kpi-card kpi-success h-100">
            <div class="kpi-icon"><i class="bi bi-currency-dollar"></i></div>
            <div class="kpi-label">Earnings</div>
            <div class="kpi-value" id="kpi-earnings">—</div>
            <div class="small text-muted mt-2">Next payout <span id="kpi-next-payout-date" class="fw-semibold">—</span></div>
            <a href="{{ route('instructor.reports') }}" class="small fw-semibold text-decoration-none" style="color: var(--sl-primary-600);">View reports <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card kpi-danger h-100">
            <div class="kpi-icon"><i class="bi bi-x-circle-fill"></i></div>
            <div class="kpi-label">Cancellation Rate</div>
            <div class="kpi-value" id="kpi-cancellation">—</div>
            <div class="small text-muted mt-2">Last 90 days</div>
            <a href="{{ route('instructor.reports') }}" class="small fw-semibold text-decoration-none" style="color: var(--sl-primary-600);">See details <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card h-100">
            <div class="kpi-icon"><i class="bi bi-clock-history"></i></div>
            <div class="kpi-label">Hours per Learner</div>
            <div class="kpi-value" id="kpi-hours-learner">—</div>
            <div class="small text-muted mt-2">Excludes new learners (90d)</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card kpi-accent h-100">
            <div class="kpi-icon"><i class="bi bi-star-fill"></i></div>
            <div class="kpi-label">Learner Rating</div>
            <div class="kpi-value" id="kpi-rating">—</div>
            <div class="small text-muted mt-2">Average from reviews</div>
            <a href="{{ route('instructor.reports') }}" class="small fw-semibold text-decoration-none" style="color: var(--sl-primary-600);">See reviews <i class="bi bi-arrow-right"></i></a>
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

{{-- Booking Detail Modal --}}
<div class="modal fade" id="booking-detail-modal" tabindex="-1" aria-labelledby="booking-detail-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
                    <li class="breadcrumb-item" id="modal-breadcrumb-booking">Booking</li>
                </ol></nav>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="booking-detail-body">
                <div class="text-center text-muted py-3">Loading…</div>
            </div>
        </div>
    </div>
</div>

{{-- Cancel Booking Modal --}}
<div class="modal fade" id="cancel-booking-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <div class="d-flex align-items-center gap-2">
                    <span class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-trash"></i></span>
                    <h5 class="modal-title fw-bold mb-0">Are you sure you want to cancel this booking?</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- 24-hour restriction warning (hidden by default) --}}
                <div id="cancel-24hr-warning" class="alert alert-warning" style="display:none;">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <strong>This booking starts within 24 hours.</strong>
                    Instructors are not permitted to modify bookings within 24 hours unless it is an emergency.
                </div>

                {{-- Cancellation rate warning --}}
                <div class="mb-3">
                    <h6 class="fw-bold">This may count towards your cancellation rate</h6>
                    <p class="text-muted small mb-0">We measure your cancellation rate to ensure that learners enjoy a consistent experience on our platform.</p>
                </div>

                {{-- Reason dropdown --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold text-warning">Reason for cancel <span class="text-danger">*</span></label>
                    <select class="form-select" id="cancel-reason-code">
                        <option value="">Please provide a reason</option>
                        <option value="illness_family_emergency">Illness/Family Emergency</option>
                        <option value="double_booked">Double booked</option>
                        <option value="car_trouble">Car trouble</option>
                        <option value="weather_conditions">Weather conditions</option>
                        <option value="requested_by_learner">Cancellation was requested by learner</option>
                        <option value="other">Other</option>
                    </select>
                    <small class="text-muted">This will be shared with your learner.</small>
                </div>

                {{-- Free text for "Other" reason --}}
                <div class="mb-3" id="cancel-reason-other-wrap" style="display:none;">
                    <label class="form-label fw-semibold">Reason details</label>
                    <textarea class="form-control" id="cancel-reason-text" rows="2" maxlength="500" placeholder="Please explain..."></textarea>
                </div>

                {{-- Message for learner --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Message for Learner</label>
                    <textarea class="form-control" id="cancel-message" rows="3" maxlength="1000" placeholder="Please provide any additional context..."></textarea>
                </div>

                {{-- Cancellation policy checkbox --}}
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="cancel-policy-check">
                    <label class="form-check-label" for="cancel-policy-check">
                        I understand and agree to the <a href="#" class="text-decoration-underline">Cancellation Policy</a>
                    </label>
                </div>

                <div id="cancel-error" class="alert alert-danger small" style="display:none;"></div>
            </div>
            <div class="modal-footer border-0 flex-column gap-2">
                <button type="button" class="btn btn-danger w-100" id="cancel-confirm-btn" disabled>
                    <i class="bi bi-trash me-1"></i> Cancel Booking
                </button>
                <button type="button" class="btn btn-success w-100" id="cancel-reschedule-btn">
                    Reschedule Booking
                </button>
                <button type="button" class="btn btn-outline-secondary w-100 btn-sm" data-bs-dismiss="modal">Go back</button>
            </div>
        </div>
    </div>
</div>

{{-- Reschedule Booking Modal --}}
<div class="modal fade" id="reschedule-booking-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
                    <li class="breadcrumb-item">Modify Booking</li>
                </ol></nav>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-bold mb-3">Reschedule Booking</h4>

                <div class="d-flex gap-2 mb-3">
                    <button type="button" class="btn btn-outline-secondary" id="reschedule-discard-btn"><i class="bi bi-x me-1"></i>Discard Changes</button>
                    <button type="button" class="btn btn-success" id="reschedule-propose-btn" disabled>Propose Booking →</button>
                </div>

                {{-- Warning --}}
                <div class="alert alert-danger border-danger small">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <strong>This action will CANCEL the current booking and propose a NEW booking</strong>
                    <p class="mb-0 mt-1" id="reschedule-notify-text">The learner will be notified that the current booking is cancelled and can either accept or decline the new booking proposal.</p>
                </div>

                {{-- Old booking (struck through) --}}
                <div class="card border-danger mb-3" id="reschedule-old-booking">
                    <div class="card-body" id="reschedule-old-details">Loading...</div>
                </div>

                {{-- New booking form --}}
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-success">NEW</span>
                            <strong>Booking</strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">New Date</label>
                            <input type="date" class="form-control" id="reschedule-date" min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Available Time Slots</label>
                            <div id="reschedule-slots-loading" style="display:none;" class="text-muted small">Loading available slots...</div>
                            <select class="form-select" id="reschedule-time" disabled>
                                <option value="">Select a date first</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Message (optional)</label>
                            <textarea class="form-control" id="reschedule-message" rows="2" placeholder="Reason for rescheduling..."></textarea>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="reschedule-policy-check">
                            <label class="form-check-label" for="reschedule-policy-check">
                                I understand and agree to the <a href="#" class="text-decoration-underline">Cancellation Policy</a>
                            </label>
                        </div>
                    </div>
                </div>

                <div id="reschedule-error" class="alert alert-danger small" style="display:none;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Invite Learner Modal --}}
<div class="modal fade" id="invite-learner-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Invite a Learner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Send an email invitation to a learner to join Secure Licences and book lessons with you.</p>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email Address *</label>
                    <input type="email" class="form-control" id="invite-email" placeholder="learner@example.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Name (optional)</label>
                    <input type="text" class="form-control" id="invite-name" placeholder="Learner's name">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Personal Message (optional)</label>
                    <textarea class="form-control" id="invite-message" rows="3" placeholder="Hi! I'd love to help you get your licence..."></textarea>
                </div>
                <div id="invite-result" style="display:none;"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="invite-send-btn"><i class="bi bi-send me-1"></i>Send Invitation</button>
            </div>
        </div>
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
            '<a href="#" class="small booking-manage-link" data-booking-id="' + b.id + '">See more / Manage</a>' +
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
            '<a href="#" class="small booking-manage-link" data-booking-id="' + b.id + '">See more / Manage</a>' +
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
      // Backend serializes lowercase: paid / refunded / pending / failed
      var ps = (b.payment_status || '').toLowerCase();
      var pay = ps ? ps.charAt(0).toUpperCase() + ps.slice(1) : '—';
      var payClass = ps === 'refunded' ? 'badge-returned'
                    : ps === 'paid' ? 'badge-processed'
                    : ps === 'failed' ? 'badge-returned'
                    : '';
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

  // ——— Current active booking for modals ———
  var activeBooking = null;
  var activeBookingInstructorProfileId = null;

  // ——— Booking Detail Modal (matching live site) ———
  function showBookingDetail(bookingId) {
    var body = document.getElementById('booking-detail-body');
    body.innerHTML = '<div class="text-center text-muted py-3">Loading…</div>';
    document.getElementById('modal-breadcrumb-booking').textContent = 'Booking #' + bookingId;
    var modal = new bootstrap.Modal(document.getElementById('booking-detail-modal'));
    modal.show();

    fetch('/api/bookings/' + bookingId, opts)
      .then(function(r) { return r.json(); })
      .then(function(res) {
        var b = res.data || res;
        activeBooking = b;
        var statusClass = b.status === 'cancelled' ? 'bg-danger' : (b.status === 'confirmed' ? 'bg-success' : (b.status === 'proposed' ? 'bg-warning' : 'bg-secondary'));
        var location = (b.suburb && b.suburb.location) ? b.suburb.location : (b.suburb ? (b.suburb.name + ' ' + (b.suburb.postcode || '')) : '—');

        var html = '<h4 class="fw-bold mb-3">Booking #' + b.id + '</h4>';

        // Modify booking dropdown (matching live site)
        if (b.can_cancel || b.can_reschedule) {
          html += '<div class="dropdown mb-3">' +
            '<button class="btn btn-outline-secondary w-100 text-start d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">' +
            '<i class="bi bi-pencil"></i> Modify Booking</button>' +
            '<ul class="dropdown-menu">';
          if (b.can_reschedule) {
            html += '<li><a class="dropdown-item" href="#" id="detail-reschedule-btn"><i class="bi bi-calendar me-2"></i>Reschedule booking</a></li>';
          }
          if (b.can_cancel) {
            html += '<li><a class="dropdown-item" href="#" id="detail-cancel-btn"><i class="bi bi-trash me-2"></i>Cancel without new booking</a></li>';
          }
          html += '</ul></div>';
        }

        // 24-hour warning
        if (b.is_within_24_hours) {
          html += '<div class="alert alert-warning small"><i class="bi bi-exclamation-triangle me-1"></i>' +
            '<strong>This booking starts within 24 hours.</strong> Instructors are not permitted to modify bookings within 24 hours unless it is an emergency.</div>';
        }

        // Booking details card
        html += '<div class="card border-0 shadow-sm mb-3"><div class="card-body">' +
          '<div class="d-flex align-items-center gap-2 mb-2"><span class="text-muted">Booking #' + b.id + '</span><span class="badge ' + statusClass + ' text-white text-uppercase">' + esc(b.status) + '</span></div>' +
          '<div class="row g-2 small">' +
            '<div class="col-12"><i class="bi bi-calendar3 me-1 text-muted"></i>' + formatDate(b.scheduled_at) + '</div>' +
            '<div class="col-12"><i class="bi bi-clock me-1 text-muted"></i>' + formatTime(b.scheduled_at, b.duration_minutes) + '</div>' +
            '<div class="col-12"><i class="bi bi-car-front me-1 text-muted"></i>' + esc(b.transmission === 'manual' ? 'Manual' : 'Auto') + '</div>' +
            '<div class="col-12"><i class="bi bi-book me-1 text-muted"></i>' + esc(lessonLabel(b)) + '</div>' +
            '<div class="col-12"><i class="bi bi-person me-1 text-muted"></i><span class="text-success">Learner</span> ' + esc(b.learner ? b.learner.name : '—') + (b.learner && b.learner.phone ? ' <a href="tel:' + esc(b.learner.phone) + '">' + esc(b.learner.phone) + '</a>' : '') + '</div>' +
            '<div class="col-12"><i class="bi bi-geo-alt me-1 text-muted"></i><a href="#">' + esc(location) + '</a></div>' +
          '</div></div></div>';

        // Payment info
        html += '<div class="card border-0 shadow-sm"><div class="card-body">' +
          '<div class="d-flex align-items-center gap-2"><strong>Payment</strong> <span class="badge ' + (b.payment_status === 'paid' ? 'bg-success' : 'bg-warning') + ' text-uppercase">' + esc(b.payment_status || 'PENDING') + '</span></div>' +
          '<div class="mt-2 small">Amount: $' + (b.amount ? parseFloat(b.amount).toFixed(2) : '0.00') + '</div>' +
          '</div></div>';

        body.innerHTML = html;

        // Attach event listeners for modify buttons
        var cancelLink = document.getElementById('detail-cancel-btn');
        if (cancelLink) {
          cancelLink.addEventListener('click', function(e) {
            e.preventDefault();
            bootstrap.Modal.getInstance(document.getElementById('booking-detail-modal')).hide();
            openCancelModal(b);
          });
        }
        var rescheduleLink = document.getElementById('detail-reschedule-btn');
        if (rescheduleLink) {
          rescheduleLink.addEventListener('click', function(e) {
            e.preventDefault();
            bootstrap.Modal.getInstance(document.getElementById('booking-detail-modal')).hide();
            openRescheduleModal(b);
          });
        }
      })
      .catch(function() { body.innerHTML = '<p class="text-danger">Failed to load booking details.</p>'; });
  }

  // ── Public helper: called from the instructor calendar popover ──
  // Lets the calendar route Cancel/Reschedule clicks through the same dashboard modals
  // (which collect cancellation_reason_code + policy_accepted properly).
  window.openInstructorActionModal = function (action, booking) {
    if (!booking || !booking.id) return;
    // Make sure booking has the is_within_24_hours hint for the modal
    if (typeof booking.is_within_24_hours === 'undefined' && booking.scheduled_at) {
      var hours = (new Date(booking.scheduled_at).getTime() - Date.now()) / 36e5;
      booking.is_within_24_hours = hours < 24;
    }
    if (action === 'cancel') openCancelModal(booking);
    else if (action === 'reschedule') openRescheduleModal(booking);
  };

  // ——— Cancel Booking Modal ———
  function openCancelModal(booking) {
    activeBooking = booking;
    // Reset form
    document.getElementById('cancel-reason-code').value = '';
    document.getElementById('cancel-reason-text').value = '';
    document.getElementById('cancel-message').value = '';
    document.getElementById('cancel-policy-check').checked = false;
    document.getElementById('cancel-reason-other-wrap').style.display = 'none';
    document.getElementById('cancel-error').style.display = 'none';
    document.getElementById('cancel-confirm-btn').disabled = true;
    document.getElementById('cancel-confirm-btn').innerHTML = '<i class="bi bi-trash me-1"></i> Cancel Booking';

    // Show 24-hour warning if applicable
    var w = document.getElementById('cancel-24hr-warning');
    w.style.display = booking.is_within_24_hours ? 'block' : 'none';

    var modal = new bootstrap.Modal(document.getElementById('cancel-booking-modal'));
    modal.show();
  }

  // Toggle "Other" reason text field
  document.getElementById('cancel-reason-code').addEventListener('change', function() {
    document.getElementById('cancel-reason-other-wrap').style.display = this.value === 'other' ? 'block' : 'none';
    updateCancelBtnState();
  });
  document.getElementById('cancel-policy-check').addEventListener('change', updateCancelBtnState);

  function updateCancelBtnState() {
    var hasReason = document.getElementById('cancel-reason-code').value !== '';
    var hasPolicy = document.getElementById('cancel-policy-check').checked;
    document.getElementById('cancel-confirm-btn').disabled = !(hasReason && hasPolicy);
  }

  // Confirm cancel
  document.getElementById('cancel-confirm-btn').addEventListener('click', function() {
    if (!activeBooking) return;
    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Cancelling...';
    document.getElementById('cancel-error').style.display = 'none';

    var payload = {
      cancellation_reason_code: document.getElementById('cancel-reason-code').value,
      cancellation_reason: document.getElementById('cancel-reason-text').value,
      cancellation_message: document.getElementById('cancel-message').value,
      cancellation_policy_accepted: true
    };

    fetch('/api/bookings/' + activeBooking.id + '/cancel', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf || '', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
      body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
    .then(function(res) {
      if (res.ok) {
        bootstrap.Modal.getInstance(document.getElementById('cancel-booking-modal')).hide();
        loadUpcoming(1);
        loadPending(1);
        loadHistory(1);
      } else {
        document.getElementById('cancel-error').textContent = res.data.message || 'Failed to cancel booking.';
        document.getElementById('cancel-error').style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-trash me-1"></i> Cancel Booking';
      }
    })
    .catch(function() {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-trash me-1"></i> Cancel Booking';
      document.getElementById('cancel-error').textContent = 'Network error. Please try again.';
      document.getElementById('cancel-error').style.display = 'block';
    });
  });

  // Switch to reschedule from cancel modal
  document.getElementById('cancel-reschedule-btn').addEventListener('click', function() {
    bootstrap.Modal.getInstance(document.getElementById('cancel-booking-modal')).hide();
    if (activeBooking) openRescheduleModal(activeBooking);
  });

  // ——— Reschedule Booking Modal ———
  function openRescheduleModal(booking) {
    activeBooking = booking;
    document.getElementById('reschedule-date').value = '';
    document.getElementById('reschedule-time').innerHTML = '<option value="">Select a date first</option>';
    document.getElementById('reschedule-time').disabled = true;
    document.getElementById('reschedule-message').value = '';
    document.getElementById('reschedule-policy-check').checked = false;
    document.getElementById('reschedule-propose-btn').disabled = true;
    document.getElementById('reschedule-error').style.display = 'none';
    document.getElementById('reschedule-propose-btn').textContent = 'Propose Booking →';

    var location = (booking.suburb && booking.suburb.location) ? booking.suburb.location : (booking.suburb ? (booking.suburb.name + ' ' + (booking.suburb.postcode || '')) : '—');
    var learnerName = booking.learner ? booking.learner.name : '—';
    var learnerPhone = (booking.learner && booking.learner.phone) ? booking.learner.phone : '';

    document.getElementById('reschedule-notify-text').textContent = learnerName + ' will be notified that the current booking is cancelled and can either accept or decline the new booking proposal.';

    // Show old booking with strikethrough
    document.getElementById('reschedule-old-details').innerHTML =
      '<div class="d-flex align-items-center gap-2 mb-2"><span class="text-muted"><s>Booking #' + booking.id + '</s></span><span class="badge bg-success text-uppercase"><s>CONFIRMED</s></span></div>' +
      '<div class="small" style="text-decoration:line-through;color:#999;">' +
        '<div><i class="bi bi-calendar3 me-1"></i>' + formatDate(booking.scheduled_at) + '</div>' +
        '<div><i class="bi bi-clock me-1"></i>' + formatTime(booking.scheduled_at, booking.duration_minutes) + '</div>' +
        '<div><i class="bi bi-car-front me-1"></i>' + esc(lessonLabel(booking)) + '</div>' +
        '<div><i class="bi bi-geo-alt me-1"></i>' + esc(location) + '</div>' +
      '</div>' +
      '<div class="mt-1 small"><span class="text-success">Learner</span> ' + esc(learnerName) + (learnerPhone ? ' <a href="tel:' + esc(learnerPhone) + '">' + esc(learnerPhone) + '</a>' : '') + '</div>';

    // Get instructor profile ID for availability API
    fetch('/api/instructor/profile', opts)
      .then(function(r) { return r.json(); })
      .then(function(res) {
        activeBookingInstructorProfileId = (res.data && res.data.id) ? res.data.id : null;
      })
      .catch(function() {});

    var modal = new bootstrap.Modal(document.getElementById('reschedule-booking-modal'));
    modal.show();
  }

  // Load available slots when date changes
  document.getElementById('reschedule-date').addEventListener('change', function() {
    var date = this.value;
    var sel = document.getElementById('reschedule-time');
    var loading = document.getElementById('reschedule-slots-loading');
    if (!date || !activeBookingInstructorProfileId) { sel.innerHTML = '<option value="">Select a date first</option>'; sel.disabled = true; return; }
    loading.style.display = 'block';
    sel.disabled = true;
    sel.innerHTML = '<option value="">Loading...</option>';

    fetch('/api/instructors/' + activeBookingInstructorProfileId + '/availability/slots?date=' + date, opts)
      .then(function(r) { return r.json(); })
      .then(function(res) {
        loading.style.display = 'none';
        var slots = res.data || res || [];
        if (slots.length === 0) {
          sel.innerHTML = '<option value="">No available slots</option>';
          sel.disabled = true;
          return;
        }
        sel.innerHTML = '<option value="">Select a time</option>' + slots.map(function(s) {
          return '<option value="' + esc(s.datetime || s.time) + '">' + esc(s.time) + '</option>';
        }).join('');
        sel.disabled = false;
        updateRescheduleBtnState();
      })
      .catch(function() { loading.style.display = 'none'; sel.innerHTML = '<option value="">Error loading slots</option>'; });
  });

  document.getElementById('reschedule-time').addEventListener('change', updateRescheduleBtnState);
  document.getElementById('reschedule-policy-check').addEventListener('change', updateRescheduleBtnState);

  function updateRescheduleBtnState() {
    var hasTime = document.getElementById('reschedule-time').value !== '';
    var hasPolicy = document.getElementById('reschedule-policy-check').checked;
    document.getElementById('reschedule-propose-btn').disabled = !(hasTime && hasPolicy);
  }

  // Discard reschedule
  document.getElementById('reschedule-discard-btn').addEventListener('click', function() {
    bootstrap.Modal.getInstance(document.getElementById('reschedule-booking-modal')).hide();
  });

  // Propose reschedule
  document.getElementById('reschedule-propose-btn').addEventListener('click', function() {
    if (!activeBooking) return;
    var btn = this;
    btn.disabled = true;
    btn.textContent = 'Proposing...';
    document.getElementById('reschedule-error').style.display = 'none';

    var payload = {
      scheduled_at: document.getElementById('reschedule-time').value,
      cancellation_message: document.getElementById('reschedule-message').value,
      cancellation_policy_accepted: true
    };

    fetch('/api/bookings/' + activeBooking.id + '/reschedule', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf || '', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
      body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
    .then(function(res) {
      if (res.ok) {
        bootstrap.Modal.getInstance(document.getElementById('reschedule-booking-modal')).hide();
        loadUpcoming(1);
        loadPending(1);
        loadHistory(1);
      } else {
        document.getElementById('reschedule-error').textContent = res.data.message || 'Failed to reschedule booking.';
        document.getElementById('reschedule-error').style.display = 'block';
        btn.disabled = false;
        btn.textContent = 'Propose Booking →';
      }
    })
    .catch(function() {
      btn.disabled = false;
      btn.textContent = 'Propose Booking →';
      document.getElementById('reschedule-error').textContent = 'Network error. Please try again.';
      document.getElementById('reschedule-error').style.display = 'block';
    });
  });

  // Delegate click on "See more / Manage" links
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('booking-manage-link')) {
      e.preventDefault();
      var id = e.target.getAttribute('data-booking-id');
      if (id) showBookingDetail(id);
    }
  });

  // ——— Invite Learner ———
  document.getElementById('dashboard-invite-learner-btn').addEventListener('click', function() {
    document.getElementById('invite-email').value = '';
    document.getElementById('invite-name').value = '';
    document.getElementById('invite-message').value = '';
    document.getElementById('invite-result').style.display = 'none';
    document.getElementById('invite-send-btn').disabled = false;
    document.getElementById('invite-send-btn').innerHTML = '<i class="bi bi-send me-1"></i>Send Invitation';
    var modal = new bootstrap.Modal(document.getElementById('invite-learner-modal'));
    modal.show();
  });

  document.getElementById('invite-send-btn').addEventListener('click', function() {
    var email = document.getElementById('invite-email').value.trim();
    if (!email) { alert('Please enter an email address.'); return; }
    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';
    fetch('/api/instructor/learners/invite', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf || '', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
      body: JSON.stringify({
        email: email,
        name: document.getElementById('invite-name').value.trim(),
        message: document.getElementById('invite-message').value.trim()
      })
    })
    .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
    .then(function(result) {
      var resultEl = document.getElementById('invite-result');
      if (result.ok) {
        resultEl.className = 'alert alert-success small';
        resultEl.textContent = result.data.message || 'Invitation sent!';
        resultEl.style.display = 'block';
        btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Sent!';
        btn.classList.remove('btn-warning');
        btn.classList.add('btn-success');
      } else {
        resultEl.className = 'alert alert-danger small';
        resultEl.textContent = result.data.message || 'Failed to send invitation.';
        resultEl.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send me-1"></i>Send Invitation';
      }
    })
    .catch(function() {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-send me-1"></i>Send Invitation';
      alert('Failed to send invitation.');
    });
  });

  document.getElementById('tab-upcoming').addEventListener('shown.bs.tab', function() { loadUpcoming(1); });
  document.getElementById('tab-pending').addEventListener('shown.bs.tab', function() { loadPending(1); });
  document.getElementById('tab-history').addEventListener('shown.bs.tab', function() { loadHistory(1); });

  loadSummary();
  loadUpcoming(1);
})();
</script>
@endpush
@endsection
