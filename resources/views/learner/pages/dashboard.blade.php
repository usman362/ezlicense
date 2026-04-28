@extends('layouts.learner')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="letter-spacing:-0.02em;">Welcome back, <span id="welcome-name" style="background: linear-gradient(135deg, var(--sl-primary-600), var(--sl-teal-500)); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">{{ Auth::user()->first_name ?? Auth::user()->name }}</span></h3>
        <p class="text-muted mb-0">Here's what's happening with your learning journey.</p>
    </div>
    <a href="{{ route('find-instructor') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Book a New Lesson
    </a>
</div>

{{-- KPI strip --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="kpi-card h-100">
            <div class="kpi-icon"><i class="bi bi-calendar-check-fill"></i></div>
            <div class="kpi-label">Upcoming</div>
            <div class="kpi-value" id="kpi-upcoming">—</div>
            <div class="small text-muted mt-1">Scheduled lessons</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card kpi-success h-100">
            <div class="kpi-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="kpi-label">Completed</div>
            <div class="kpi-value" id="kpi-completed">—</div>
            <div class="small text-muted mt-1">Lessons finished</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card kpi-accent h-100">
            <div class="kpi-icon"><i class="bi bi-wallet2"></i></div>
            <div class="kpi-label">Wallet Balance</div>
            <div class="kpi-value" id="wallet-balance">$0</div>
            <div class="small text-muted mt-1">Incl. <span id="wallet-non-refundable">$0.00</span> non-refundable</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card kpi-teal h-100">
            <div class="kpi-icon"><i class="bi bi-stars"></i></div>
            <div class="kpi-label">Hours Logged</div>
            <div class="kpi-value" id="kpi-hours">—</div>
            <div class="small text-muted mt-1">Behind the wheel</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- My Instructor --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted small text-uppercase mb-1" style="letter-spacing:0.08em;">Your Instructor</h6>
                        <h5 class="fw-bold mb-0">My Instructor</h5>
                    </div>
                    <a href="{{ route('find-instructor') }}" class="btn btn-outline-primary btn-sm" id="instructor-book-now-btn">
                        <i class="bi bi-calendar-plus me-1"></i>Book Now
                    </a>
                </div>
                <div id="instructor-content">
                    <div id="instructor-loading" class="text-muted small py-3">Loading…</div>
                    <div id="instructor-loaded" style="display: none;">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background: var(--sl-gray-50);">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bolder text-white" style="width: 64px; height: 64px; font-size: 1.5rem; background: linear-gradient(135deg, var(--sl-primary-500), var(--sl-teal-500)); flex-shrink: 0;" id="instructor-avatar">—</div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-bolder text-truncate" style="font-size: 1.1rem;" id="instructor-name">—</div>
                                <a href="#" id="instructor-phone" class="small text-primary text-decoration-none d-block"><i class="bi bi-telephone me-1"></i></a>
                                <span class="small text-muted" id="instructor-rate"></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: var(--sl-gray-50);">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 48px; background: #fff; border: 1px solid var(--sl-gray-200); flex-shrink: 0;"><i class="bi bi-car-front-fill" style="color: var(--sl-primary-600); font-size: 1.25rem;"></i></div>
                            <div class="small flex-grow-1">
                                <div class="fw-semibold" id="instructor-vehicle">—</div>
                                <div class="text-muted" id="instructor-vehicle-details">5-star ANCAP · Dual controls fitted</div>
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <a href="{{ route('find-instructor') }}" class="small text-decoration-none fw-semibold">Switch instructor <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                    <div id="instructor-empty" class="text-center py-4" style="display: none;">
                        <div class="icon-bubble mx-auto mb-3"><i class="bi bi-person-plus"></i></div>
                        <p class="text-muted small mb-2">You don't have an instructor yet.</p>
                        <a href="{{ route('find-instructor') }}" class="btn btn-primary btn-sm">Find an Instructor</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h6 class="text-muted small text-uppercase mb-1" style="letter-spacing:0.08em;">Quick Actions</h6>
                <h5 class="fw-bold mb-3">What would you like to do?</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('find-instructor') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-search me-2"></i>Find a new instructor
                    </a>
                    <a href="{{ route('learner.wallet.add-credit') }}" class="btn btn-outline-secondary text-start">
                        <i class="bi bi-credit-card me-2"></i>Top up wallet
                    </a>
                    <a href="{{ route('gift-vouchers') }}" class="btn btn-outline-secondary text-start">
                        <i class="bi bi-gift me-2"></i>Buy a gift voucher
                    </a>
                    <a href="{{ route('practice-test') }}" class="btn btn-outline-secondary text-start">
                        <i class="bi bi-journal-text me-2"></i>Free practice test
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Driving Test Package (dynamic from my instructor) --}}
<div class="card border-0 shadow-sm mb-4" id="test-package-card" style="display:none;">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <h6 class="fw-bold mb-0">Driving Test Package</h6>
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold" id="test-package-price">$0.00</span>
                <a href="#" class="btn btn-warning btn-sm" id="test-package-book-btn">Book Now</a>
            </div>
        </div>
        <ul class="list-unstyled mb-0 small">
            <li class="mb-1"><span class="text-danger me-1">P</span> 2.5hr Test Package</li>
            <li class="mb-1"><i class="bi bi-clock me-1 text-muted"></i> Use instructor's vehicle for test</li>
            <li class="mb-1"><i class="bi bi-geo-alt me-1 text-muted"></i> Pick up &amp; Drop off included</li>
            <li class="mb-0"><i class="bi bi-check2-square me-1 text-muted"></i> 45 minute pre-test warm up lesson</li>
        </ul>
    </div>
</div>

{{-- Calendar Sync Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-2"><i class="bi bi-phone me-2"></i>Sync with your phone</h6>
        <p class="text-muted small mb-3">Subscribe to your booking calendar on your phone. New bookings, reschedules, and cancellations will automatically appear in your phone's calendar app.</p>

        <div id="learner-calendar-sync-section">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="#" id="learner-apple-cal-btn" class="btn btn-outline-dark btn-sm" target="_blank">
                    <i class="bi bi-apple me-1"></i>Apple Calendar
                </a>
                <a href="#" id="learner-google-cal-btn" class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="bi bi-google me-1"></i>Google Calendar
                </a>
                <button class="btn btn-outline-secondary btn-sm" id="learner-copy-cal-url-btn">
                    <i class="bi bi-clipboard me-1"></i>Copy URL
                </button>
            </div>

            <div class="bg-light rounded p-2 mb-3">
                <code class="small text-break" id="learner-calendar-feed-url">Loading...</code>
            </div>
        </div>
    </div>
</div>

{{-- Bookings --}}
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Bookings</h6>
        <ul class="nav nav-tabs border-0 small mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-upcoming" data-bs-toggle="tab" data-bs-target="#panel-upcoming" type="button" role="tab">Upcoming</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-services" data-bs-toggle="tab" data-bs-target="#panel-services" type="button" role="tab">Services</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-history" data-bs-toggle="tab" data-bs-target="#panel-history" type="button" role="tab">History</button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="panel-upcoming" role="tabpanel">
                <div id="upcoming-loading" class="text-muted small py-3">Loading…</div>
                <div id="upcoming-list" style="display: none;"></div>
                <div id="upcoming-empty" class="text-muted small py-3" style="display: none;">You have no upcoming bookings to view at this time.</div>
            </div>
            <div class="tab-pane fade" id="panel-services" role="tabpanel">
                <div id="services-loading" class="text-muted small py-3">Loading…</div>
                <div id="services-cards" class="row g-3" style="display: none;"></div>
                <div id="services-empty" class="text-muted small py-3" style="display: none;">You don't have an instructor yet. <a href="{{ route('find-instructor') }}">Find an instructor</a> to book lessons and packages.</div>
            </div>
            <div class="tab-pane fade" id="panel-history" role="tabpanel">
                <div id="history-loading" class="text-muted small py-3">Loading…</div>
                <div id="history-wrap" style="display: none;">
                    <h6 class="fw-bold mb-2">Booking History</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Booking</th>
                                    <th>Instructor</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Review</th>
                                    <th class="text-end"></th>
                                </tr>
                            </thead>
                            <tbody id="history-tbody"></tbody>
                        </table>
                    </div>
                    <nav id="history-pagination" class="mt-2" aria-label="History pagination"></nav>
                </div>
                <div id="history-empty" class="text-muted small py-3" style="display: none;">You have no booking history.</div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-tabs .nav-link { color: #333; }
.nav-tabs .nav-link.active { border-bottom: 2px solid #f0ad4e; font-weight: 500; color: #333; }
#review-stars i { cursor: pointer; transition: transform 0.1s; }
#review-stars i:hover { transform: scale(1.15); }
</style>

{{-- ============================================ --}}
{{--  REVIEW SUBMISSION MODAL                       --}}
{{-- ============================================ --}}
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-star me-2"></i>Leave a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="review-booking-id" value="">
                <p class="text-muted small mb-3">How was your lesson with <strong id="review-instructor-name">your instructor</strong>?</p>

                <div class="text-center mb-3">
                    <div id="review-stars">
                        <i class="bi bi-star text-muted fs-3 me-1" data-value="1"></i>
                        <i class="bi bi-star text-muted fs-3 me-1" data-value="2"></i>
                        <i class="bi bi-star text-muted fs-3 me-1" data-value="3"></i>
                        <i class="bi bi-star text-muted fs-3 me-1" data-value="4"></i>
                        <i class="bi bi-star text-muted fs-3 me-1" data-value="5"></i>
                    </div>
                    <div class="small text-muted mt-2">Tap a star to rate</div>
                </div>

                <div class="mb-2">
                    <label for="review-comment" class="form-label small fw-semibold">Comment (optional)</label>
                    <textarea id="review-comment" class="form-control" rows="4" maxlength="2000" placeholder="Tell others about your experience — what went well, what could improve..."></textarea>
                </div>

                <div class="alert alert-danger py-2 small mb-0" id="review-error" style="display:none;"></div>

                <div class="alert alert-light border small mb-0 mt-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Your review is private until an admin approves it, to keep the platform safe and fair for everyone. See our <a href="{{ route('policies.learner-conduct') }}" target="_blank">Learner Code of Conduct</a>.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="review-submit-btn">
                    <i class="bi bi-send me-1"></i>Submit Review
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{--  GOOGLE REVIEW PROMPT MODAL                    --}}
{{-- ============================================ --}}
<div class="modal fade" id="googleReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-success"><i class="bi bi-check-circle-fill me-2"></i>Thanks for your review!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Your review has been submitted and will be visible after a quick check by our team.</p>

                <div class="p-3 bg-light rounded border mb-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-google text-primary fs-4"></i>
                        <strong>Would you share it on Google too?</strong>
                    </div>
                    <p class="small text-muted mb-3">
                        A Google review helps other learners find great instructors — and takes just a few seconds.
                    </p>

                    <div id="google-review-copy-wrap" style="display:none;">
                        <label class="small fw-semibold text-muted">Your comment (tap to copy):</label>
                        <pre id="google-review-comment" class="small bg-white border rounded p-2" style="white-space:pre-wrap; max-height:140px; overflow-y:auto;"></pre>
                        <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="google-review-copy-btn">
                            <i class="bi bi-clipboard me-1"></i>Copy my comment
                        </button>
                    </div>

                    <a href="#" id="google-review-url" target="_blank" rel="noopener" class="btn btn-primary w-100">
                        <i class="bi bi-google me-1"></i>Open Google Reviews
                    </a>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Maybe later</button>
            </div>
        </div>
    </div>
</div>

{{-- Thank-you toast (fallback for low-star or no Google Place ID) --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
    <div id="review-thanks-toast" class="toast" role="alert" aria-live="polite" aria-atomic="true">
        <div class="toast-header">
            <i class="bi bi-check-circle-fill text-success me-2"></i>
            <strong class="me-auto">Review submitted</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body small">
            Thanks for your feedback! Your review will be visible after an admin check.
        </div>
    </div>
</div>
@include('learner.partials.booking-action-modals')

@push('scripts')
{{-- Loads the cancel/reschedule modal helpers used by the dashboard list --}}
@vite('resources/js/learner-calendar.js')
<script>
window.learnerBookingNewUrl = "{{ route('learner.bookings.new') }}";
// Expose the dashboard's load function so cancel/reschedule modal can refresh it
window.__loadLearnerDashboard = function() {
  if (typeof loadDashboardData === 'function') loadDashboardData();
};
</script>
<script>
(function() {
  var csrf = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').content;
  var opts = { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf || '', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' };
  var bookingNewUrl = window.learnerBookingNewUrl || '';

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

  fetch('/api/learner/dashboard', opts)
    .then(function(r) { return r.json(); })
    .then(function(res) {
      var data = res.data || {};
      var instructor = data.my_instructor;
      var wallet = data.wallet || {};
      var upcoming = data.upcoming_bookings || [];

      // Welcome name already in DOM
      var welcomeEl = document.getElementById('welcome-name');
      if (welcomeEl && !welcomeEl.textContent) welcomeEl.textContent = 'there';

      // My Instructor
      document.getElementById('instructor-loading').style.display = 'none';
      if (instructor) {
        document.getElementById('instructor-empty').style.display = 'none';
        document.getElementById('instructor-loaded').style.display = 'block';
        var initial = (instructor.name || '').trim().split(/\s+/).map(function(s) { return s.charAt(0); }).join('').slice(0, 2).toUpperCase() || '?';
        document.getElementById('instructor-avatar').textContent = initial;
        document.getElementById('instructor-name').textContent = instructor.name || '—';
        var phoneEl = document.getElementById('instructor-phone');
        phoneEl.textContent = instructor.phone || '—';
        phoneEl.href = instructor.phone ? ('tel:' + instructor.phone.replace(/\s/g, '')) : '#';
        document.getElementById('instructor-rate').textContent = instructor.rate || '';
        document.getElementById('instructor-vehicle').textContent = instructor.vehicle || '—';
        if (instructor.instructor_profile_id) {
          document.getElementById('instructor-book-now-btn').href = bookingNewUrl ? (bookingNewUrl + '?instructor_profile_id=' + instructor.instructor_profile_id) : ('/instructors/' + instructor.instructor_profile_id);
        }
      } else {
        document.getElementById('instructor-loaded').style.display = 'none';
        document.getElementById('instructor-empty').style.display = 'block';
      }

      // Wallet
      document.getElementById('wallet-balance').textContent = wallet.balance_display || '$0.00';
      document.getElementById('wallet-non-refundable').textContent = wallet.non_refundable_credit_display || '$0.00';

      // KPI stats
      var stats = data.stats || {};
      var kpiUpcoming = document.getElementById('kpi-upcoming');
      var kpiCompleted = document.getElementById('kpi-completed');
      var kpiHours = document.getElementById('kpi-hours');
      if (kpiUpcoming) kpiUpcoming.textContent = stats.upcoming_count != null ? stats.upcoming_count : '0';
      if (kpiCompleted) kpiCompleted.textContent = stats.completed_count != null ? stats.completed_count : '0';
      if (kpiHours) kpiHours.innerHTML = (stats.total_hours != null ? stats.total_hours : '0') + '<span class="kpi-unit">hrs</span>';

      // Upcoming bookings
      document.getElementById('upcoming-loading').style.display = 'none';
      if (upcoming.length === 0) {
        document.getElementById('upcoming-list').style.display = 'none';
        document.getElementById('upcoming-empty').style.display = 'block';
      } else {
        document.getElementById('upcoming-empty').style.display = 'none';
        document.getElementById('upcoming-list').style.display = 'block';
        document.getElementById('upcoming-list').innerHTML = '<ul class="list-unstyled mb-0">' + upcoming.map(function(b) {
          var typeLabel = (b.type === 'test_package') ? 'Test Package' : ((b.duration_minutes || 60) / 60) + ' hr Lesson';
          var canModify = (b.status === 'confirmed' || b.status === 'proposed' || b.status === 'pending')
                          && new Date(b.scheduled_at).getTime() > Date.now();
          return '<li class="border-bottom py-2 small d-flex justify-content-between align-items-start gap-2">' +
            '<div>' +
              '<strong>' + formatDate(b.scheduled_at) + '</strong> ' + formatTime(b.scheduled_at, b.duration_minutes) + '<br>' +
              (b.instructor_name ? esc(b.instructor_name) + ' · ' : '') + typeLabel + (b.location ? ' · ' + esc(b.location) : '') +
            '</div>' +
            (canModify ? (
              '<div class="dropdown flex-shrink-0">' +
                '<button class="btn btn-sm btn-outline-secondary border-0 px-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Manage">' +
                  '<i class="bi bi-three-dots-vertical"></i>' +
                '</button>' +
                '<ul class="dropdown-menu dropdown-menu-end small">' +
                  '<li><button type="button" class="dropdown-item learner-action-reschedule" data-booking=\'' + JSON.stringify(b).replace(/'/g, '&#39;') + '\'><i class="bi bi-arrow-repeat me-2"></i>Reschedule</button></li>' +
                  '<li><button type="button" class="dropdown-item text-danger learner-action-cancel" data-booking=\'' + JSON.stringify(b).replace(/'/g, '&#39;') + '\'><i class="bi bi-x-circle me-2"></i>Cancel</button></li>' +
                '</ul>' +
              '</div>'
            ) : '') +
          '</li>';
        }).join('') + '</ul>';

        // Wire up cancel/reschedule buttons (uses modals from learner-calendar.js)
        document.querySelectorAll('.learner-action-cancel').forEach(function(btn) {
          btn.addEventListener('click', function() {
            var b = JSON.parse(btn.getAttribute('data-booking'));
            if (typeof window.openLearnerCancelModal === 'function') window.openLearnerCancelModal(b);
            else window.location.href = '/learner/calendar';
          });
        });
        document.querySelectorAll('.learner-action-reschedule').forEach(function(btn) {
          btn.addEventListener('click', function() {
            var b = JSON.parse(btn.getAttribute('data-booking'));
            if (typeof window.openLearnerRescheduleModal === 'function') window.openLearnerRescheduleModal(b);
            else window.location.href = '/learner/calendar';
          });
        });
      }

      // Services tab: build cards from my_instructor
      document.getElementById('services-loading').style.display = 'none';
      if (instructor && instructor.instructor_profile_id) {
        document.getElementById('services-empty').style.display = 'none';
        document.getElementById('services-cards').style.display = 'flex';
        var price1 = instructor.lesson_price != null ? Math.round(instructor.lesson_price) : 65;
        var price2 = price1 * 2;
        var priceTest = instructor.test_package_price != null ? Math.round(instructor.test_package_price) : 225;
        var trans = instructor.transmission || 'Auto';
        var bookUrl = bookingNewUrl ? (bookingNewUrl + '?instructor_profile_id=' + instructor.instructor_profile_id) : ('/instructors/' + instructor.instructor_profile_id);
        var name = esc(instructor.name || 'Instructor');
        var cards = [
          { title: '1 hour Driving Lesson', price: price1, popular: false, icon: 'bi-emoji-sunglasses' },
          { title: '2 hour Driving Lesson', price: price2, popular: true, icon: 'bi-people' },
          { title: '2.5 hours Test Package', price: priceTest, popular: false, icon: 'bi-clipboard-check' }
        ];
        document.getElementById('services-cards').innerHTML = cards.map(function(c) {
          return '<div class="col-md-4">' +
            '<div class="card border-0 shadow-sm h-100">' +
              '<div class="card-body d-flex flex-column">' +
                (c.popular ? '<span class="badge bg-danger text-white align-self-start mb-2">MOST POPULAR</span>' : '') +
                '<h6 class="fw-bold">' + c.title + '</h6>' +
                '<p class="text-muted small mb-1">Price $' + c.price + '</p>' +
                '<p class="text-muted small mb-3">Transmission ' + trans + '</p>' +
                '<div class="mb-3 flex-grow-1 d-flex align-items-center justify-content-center"><i class="bi ' + c.icon + ' display-4 text-muted"></i></div>' +
                '<a href="' + bookUrl + '" class="btn btn-warning btn-sm">Book with ' + name + '</a>' +
              '</div></div></div>';
        }).join('');

        // Driving Test Package card
        var tpCard = document.getElementById('test-package-card');
        var tpPriceEl = document.getElementById('test-package-price');
        var tpBookBtn = document.getElementById('test-package-book-btn');
        if (tpCard && tpPriceEl && tpBookBtn && instructor.offers_test_package && priceTest) {
          tpPriceEl.textContent = '$' + priceTest.toFixed(2);
          tpBookBtn.href = bookUrl;
          tpCard.style.display = '';
        } else if (tpCard) {
          tpCard.style.display = 'none';
        }
      } else {
        document.getElementById('services-cards').style.display = 'none';
        document.getElementById('services-empty').style.display = 'block';
        var tpCardEmpty = document.getElementById('test-package-card');
        if (tpCardEmpty) tpCardEmpty.style.display = 'none';
      }
    })
    .catch(function() {
      document.getElementById('instructor-loading').style.display = 'none';
      document.getElementById('instructor-empty').style.display = 'block';
      document.getElementById('instructor-empty').innerHTML = 'Unable to load. <a href="{{ route('find-instructor') }}">Find an instructor</a>.';
      document.getElementById('upcoming-loading').style.display = 'none';
      document.getElementById('upcoming-empty').style.display = 'block';
      document.getElementById('upcoming-empty').textContent = 'Unable to load upcoming bookings.';
    });

  // History tab: load when shown
  function loadHistory(page) {
    page = page || 1;
    document.getElementById('history-loading').style.display = 'block';
    document.getElementById('history-wrap').style.display = 'none';
    document.getElementById('history-empty').style.display = 'none';
    fetch('/api/bookings?tab=history&page=' + page, opts)
      .then(function(r) { return r.json(); })
      .then(function(data) {
        document.getElementById('history-loading').style.display = 'none';
        var items = data.data || [];
        if (items.length === 0) {
          document.getElementById('history-empty').style.display = 'block';
          document.getElementById('history-wrap').style.display = 'none';
          return;
        }
        document.getElementById('history-empty').style.display = 'none';
        document.getElementById('history-wrap').style.display = 'block';
        var tbody = document.getElementById('history-tbody');
        tbody.innerHTML = items.map(function(b) {
          // ── Status badge: Completed / Cancelled / Rescheduled ──
          var isRescheduled = b.status === 'cancelled'
            && (b.cancellation_reason_code === 'rescheduled'
                || (b.cancellation_reason && /resched/i.test(b.cancellation_reason)));
          var status, statusClass;
          if (isRescheduled) { status = 'RESCHEDULED'; statusClass = 'bg-secondary text-white'; }
          else if (b.status === 'cancelled') { status = 'CANCELLED'; statusClass = 'bg-danger text-white'; }
          else { status = 'COMPLETED'; statusClass = 'bg-success text-white'; }

          // ── Payment badge with amount ──
          var ps = (b.payment_status || '').toLowerCase();
          var payLabel = ps ? ps.charAt(0).toUpperCase() + ps.slice(1) : '—';
          var amount = b.amount != null ? '$' + Number(b.amount).toFixed(2) : '';
          var payClass = ps === 'refunded' ? 'bg-primary text-white'
                        : ps === 'paid' ? 'bg-success text-white'
                        : ps === 'pending' ? 'bg-warning text-dark'
                        : ps === 'failed' ? 'bg-danger text-white'
                        : '';
          var payCell = ps
            ? '<span class="badge ' + payClass + '">' + payLabel + '</span>' +
              (amount ? '<div class="text-muted" style="font-size:0.72rem;line-height:1;margin-top:2px;">' + amount + '</div>' : '')
            : '—';

          var loc = (b.suburb && b.suburb.location) ? b.suburb.location : (b.suburb ? (b.suburb.name + ' ' + (b.suburb.postcode || '')) : '—');
          var instrName = (b.instructor && b.instructor.name) ? esc(b.instructor.name) : '—';
          var initial = instrName !== '—' ? instrName.charAt(0) : '?';
          var instructorProfileId = b.instructor_profile_id || '';

          // ── Cancellation reason (shown inline under status badge) ──
          var cancelReasonLine = '';
          if (b.status === 'cancelled' && b.cancellation_reason && !isRescheduled) {
            var reason = b.cancellation_reason.length > 50 ? b.cancellation_reason.substr(0, 50) + '…' : b.cancellation_reason;
            cancelReasonLine = '<div class="text-muted" style="font-size:0.72rem;line-height:1.1;margin-top:3px;" title="' + esc(b.cancellation_reason) + '">' + esc(reason) + '</div>';
          } else if (isRescheduled) {
            cancelReasonLine = '<div class="text-muted" style="font-size:0.72rem;line-height:1.1;margin-top:3px;">Replaced by new booking</div>';
          }

          // ── Review cell ──
          var reviewCell = '—';
          if (b.status === 'completed') {
            if (b.review && b.review.id) {
              var starsHtml = '';
              for (var s = 1; s <= 5; s++) {
                starsHtml += '<i class="bi bi-star' + (s <= b.review.rating ? '-fill text-warning' : ' text-muted') + '"></i>';
              }
              reviewCell = '<span title="You rated ' + b.review.rating + '/5" class="small">' + starsHtml + '</span>';
            } else {
              reviewCell = '<button type="button" class="btn btn-outline-warning btn-sm py-0 px-2 leave-review-btn" ' +
                'data-booking-id="' + b.id + '" ' +
                'data-instructor-name="' + instrName + '">' +
                '<i class="bi bi-star me-1"></i>Leave Review</button>';
            }
          }

          // ── Actions menu (Rebook + View Details) ──
          var rebookUrl = window.learnerBookingNewUrl + (instructorProfileId ? ('?instructor_profile_id=' + instructorProfileId) : '');
          var actionsCell =
            '<div class="dropdown">' +
              '<button class="btn btn-sm btn-outline-secondary border-0 px-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="More">' +
                '<i class="bi bi-three-dots-vertical"></i>' +
              '</button>' +
              '<ul class="dropdown-menu dropdown-menu-end small">' +
                (instructorProfileId ? '<li><a class="dropdown-item" href="' + rebookUrl + '"><i class="bi bi-arrow-clockwise me-2"></i>Rebook</a></li>' : '') +
                (instructorProfileId ? '<li><a class="dropdown-item" href="/instructors/' + instructorProfileId + '" target="_blank"><i class="bi bi-person me-2"></i>View Instructor</a></li>' : '') +
              '</ul>' +
            '</div>';

          return '<tr>' +
            '<td>#' + b.id + '</td>' +
            '<td><span class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center me-1" style="width:24px;height:24px;font-size:0.7rem;">' + initial + '</span>' + instrName + '</td>' +
            '<td>' + formatDate(b.scheduled_at) + '</td>' +
            '<td>' + formatTime(b.scheduled_at, b.duration_minutes) + '</td>' +
            '<td>' + esc(loc) + '</td>' +
            '<td><span class="badge ' + statusClass + '">' + status + '</span>' + cancelReasonLine + '</td>' +
            '<td>' + payCell + '</td>' +
            '<td>' + reviewCell + '</td>' +
            '<td class="text-end">' + actionsCell + '</td>' +
          '</tr>';
        }).join('');

        // Delegate click for Leave Review buttons
        tbody.querySelectorAll('.leave-review-btn').forEach(function(btn) {
          btn.addEventListener('click', function() {
            openReviewModal(btn.getAttribute('data-booking-id'), btn.getAttribute('data-instructor-name'));
          });
        });
        var pagination = document.getElementById('history-pagination');
        if (data.last_page > 1) {
          var cur = data.current_page || 1;
          var last = data.last_page;
          var parts = [];

          // Prev
          if (cur > 1) parts.push('<li class="page-item"><a class="page-link" href="#" data-page="' + (cur - 1) + '">‹</a></li>');
          else parts.push('<li class="page-item disabled"><span class="page-link">‹</span></li>');

          // Windowed page numbers: current ± 2 with first/last shortcuts and ellipses
          var windowStart = Math.max(1, cur - 2);
          var windowEnd = Math.min(last, cur + 2);

          if (windowStart > 1) {
            parts.push('<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>');
            if (windowStart > 2) parts.push('<li class="page-item disabled"><span class="page-link">…</span></li>');
          }
          for (var i = windowStart; i <= windowEnd; i++) {
            if (i === cur) parts.push('<li class="page-item active"><span class="page-link">' + i + '</span></li>');
            else parts.push('<li class="page-item"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>');
          }
          if (windowEnd < last) {
            if (windowEnd < last - 1) parts.push('<li class="page-item disabled"><span class="page-link">…</span></li>');
            parts.push('<li class="page-item"><a class="page-link" href="#" data-page="' + last + '">' + last + '</a></li>');
          }

          // Next
          if (cur < last) parts.push('<li class="page-item"><a class="page-link" href="#" data-page="' + (cur + 1) + '">›</a></li>');
          else parts.push('<li class="page-item disabled"><span class="page-link">›</span></li>');

          pagination.innerHTML = '<ul class="pagination pagination-sm mb-0">' + parts.join('') + '</ul>';
          pagination.querySelectorAll('a[data-page]').forEach(function(a) {
            a.addEventListener('click', function(e) { e.preventDefault(); loadHistory(parseInt(a.getAttribute('data-page'), 10)); });
          });
        } else {
          pagination.innerHTML = '';
        }
      })
      .catch(function() {
        document.getElementById('history-loading').style.display = 'none';
        document.getElementById('history-empty').textContent = 'Unable to load booking history.';
        document.getElementById('history-empty').style.display = 'block';
      });
  }
  document.getElementById('tab-history').addEventListener('shown.bs.tab', function() { loadHistory(1); });

  // Calendar sync section for learner
  (async function loadLearnerCalendarUrls() {
    try {
      const resp = await fetch('/api/calendar/subscribe-urls', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf || '' }
      });
      if (!resp.ok) return;
      const data = await resp.json();

      const appleBtn = document.getElementById('learner-apple-cal-btn');
      const googleBtn = document.getElementById('learner-google-cal-btn');
      const feedUrl = document.getElementById('learner-calendar-feed-url');

      if (appleBtn) appleBtn.href = data.webcal_url || '#';
      if (googleBtn) googleBtn.href = data.google_url || '#';
      if (feedUrl) feedUrl.textContent = data.https_url || 'Not available';
    } catch (e) {
      console.log('Learner calendar sync not loaded:', e);
    }
  })();

  document.getElementById('learner-copy-cal-url-btn')?.addEventListener('click', function() {
    const url = document.getElementById('learner-calendar-feed-url')?.textContent;
    if (url && url !== 'Loading...' && url !== 'Not available') {
      navigator.clipboard.writeText(url).then(() => {
        this.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
        setTimeout(() => { this.innerHTML = '<i class="bi bi-clipboard me-1"></i>Copy URL'; }, 2000);
      });
    }
  });

  // =======================================================
  //  REVIEW SUBMISSION FLOW
  // =======================================================
  var currentBookingId = null;
  var currentRating = 0;

  function openReviewModal(bookingId, instructorName) {
    currentBookingId = bookingId;
    currentRating = 0;

    // Reset form state
    document.getElementById('review-booking-id').value = bookingId;
    document.getElementById('review-instructor-name').textContent = instructorName || 'your instructor';
    document.getElementById('review-comment').value = '';
    document.getElementById('review-error').style.display = 'none';
    document.getElementById('review-error').textContent = '';
    document.getElementById('review-submit-btn').disabled = false;
    document.getElementById('review-submit-btn').innerHTML = '<i class="bi bi-send me-1"></i>Submit Review';

    // Reset stars
    document.querySelectorAll('#review-stars i').forEach(function(el) {
      el.className = 'bi bi-star text-muted fs-3 me-1';
    });

    var modal = new bootstrap.Modal(document.getElementById('reviewModal'));
    modal.show();
  }
  window.openReviewModal = openReviewModal;

  // Star click handler
  document.querySelectorAll('#review-stars i').forEach(function(star) {
    star.addEventListener('click', function() {
      var val = parseInt(star.getAttribute('data-value'), 10);
      currentRating = val;
      document.querySelectorAll('#review-stars i').forEach(function(s, idx) {
        if (idx < val) {
          s.className = 'bi bi-star-fill text-warning fs-3 me-1';
        } else {
          s.className = 'bi bi-star text-muted fs-3 me-1';
        }
      });
    });

    star.addEventListener('mouseenter', function() {
      var val = parseInt(star.getAttribute('data-value'), 10);
      document.querySelectorAll('#review-stars i').forEach(function(s, idx) {
        if (idx < val) {
          s.classList.add('text-warning');
          s.classList.remove('text-muted');
          s.classList.remove('bi-star');
          s.classList.add('bi-star-fill');
        }
      });
    });
  });
  document.getElementById('review-stars')?.addEventListener('mouseleave', function() {
    document.querySelectorAll('#review-stars i').forEach(function(s, idx) {
      if (idx < currentRating) {
        s.className = 'bi bi-star-fill text-warning fs-3 me-1';
      } else {
        s.className = 'bi bi-star text-muted fs-3 me-1';
      }
    });
  });

  // Submit review
  document.getElementById('review-submit-btn')?.addEventListener('click', function() {
    var err = document.getElementById('review-error');
    err.style.display = 'none';
    err.textContent = '';

    if (!currentRating || currentRating < 1) {
      err.textContent = 'Please select a star rating.';
      err.style.display = 'block';
      return;
    }

    var comment = document.getElementById('review-comment').value.trim();
    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Submitting...';

    fetch('/api/reviews', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf || '',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        booking_id: parseInt(currentBookingId, 10),
        rating: currentRating,
        comment: comment || null
      })
    })
    .then(function(r) {
      return r.json().then(function(body) { return { ok: r.ok, status: r.status, body: body }; });
    })
    .then(function(res) {
      if (!res.ok) {
        err.textContent = (res.body && res.body.message) ? res.body.message : 'Could not submit review. Please try again.';
        err.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send me-1"></i>Submit Review';
        return;
      }

      // Close review modal
      bootstrap.Modal.getInstance(document.getElementById('reviewModal'))?.hide();

      // Refresh history so the row shows the stars now
      loadHistory(1);

      // If a Google Reviews link was returned AND rating is positive (4-5 stars), prompt the learner
      var gUrl = res.body && res.body.google_review_url;
      var reviewData = res.body && res.body.data;
      if (gUrl && currentRating >= 4) {
        showGoogleReviewPrompt(gUrl, comment, reviewData ? reviewData.id : null);
      } else {
        showThankYouToast();
      }
    })
    .catch(function() {
      err.textContent = 'Network error. Please try again.';
      err.style.display = 'block';
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-send me-1"></i>Submit Review';
    });
  });

  // Google review prompt
  function showGoogleReviewPrompt(googleUrl, commentText, reviewId) {
    document.getElementById('google-review-url').href = googleUrl;
    var pre = document.getElementById('google-review-comment');
    if (commentText && commentText.length > 0) {
      pre.textContent = commentText;
      document.getElementById('google-review-copy-wrap').style.display = 'block';
    } else {
      document.getElementById('google-review-copy-wrap').style.display = 'none';
    }

    // Track on click
    var openBtn = document.getElementById('google-review-url');
    openBtn.onclick = function() {
      if (reviewId) {
        fetch('/api/reviews/' + reviewId + '/google-prompted', {
          method: 'PATCH',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf || '',
            'X-Requested-With': 'XMLHttpRequest'
          },
          credentials: 'same-origin'
        }).catch(function() { /* silent */ });
      }
    };

    var modal = new bootstrap.Modal(document.getElementById('googleReviewModal'));
    modal.show();
  }

  // Copy comment helper
  document.getElementById('google-review-copy-btn')?.addEventListener('click', function() {
    var text = document.getElementById('google-review-comment').textContent;
    navigator.clipboard.writeText(text).then(() => {
      this.innerHTML = '<i class="bi bi-check-lg me-1"></i>Copied!';
      var btn = this;
      setTimeout(function() { btn.innerHTML = '<i class="bi bi-clipboard me-1"></i>Copy my comment'; }, 2000);
    });
  });

  function showThankYouToast() {
    var toastEl = document.getElementById('review-thanks-toast');
    if (toastEl) {
      new bootstrap.Toast(toastEl, { delay: 4000 }).show();
    }
  }
})();
</script>
@endpush
@endsection
