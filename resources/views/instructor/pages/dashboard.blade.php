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

{{-- ─────────── Share Profile Panel ─────────── --}}
@php
    $sl_profile = \Illuminate\Support\Facades\Auth::user()?->instructorProfile;
    $sl_shareUrl = $sl_profile?->shareUrl();
    $sl_shareText = 'Check out my driving instructor profile on Secure Licence:';
@endphp
@if($sl_shareUrl)
<div class="card border-0 shadow-sm mb-4 share-profile-card">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex align-items-start gap-3 flex-wrap">
            <div class="share-profile-icon flex-shrink-0">
                <i class="bi bi-link-45deg"></i>
            </div>
            <div class="flex-grow-1 min-w-0">
                <h6 class="mb-2 fw-bold"><i class="bi bi-share-fill me-1 text-warning"></i>Your shareable profile link</h6>
                <p class="text-muted small mb-2">
                    Share this link on your CV, WhatsApp, Facebook or anywhere — people can view your profile and book lessons directly.
                </p>

                <div class="input-group share-profile-input mb-2">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-globe text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" id="share-profile-url" value="{{ $sl_shareUrl }}" readonly>
                    <button type="button" class="btn btn-warning fw-bold" id="share-profile-copy-btn">
                        <i class="bi bi-clipboard me-1"></i><span>Copy</span>
                    </button>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="https://wa.me/?text={{ urlencode($sl_shareText . ' ' . $sl_shareUrl) }}" target="_blank" rel="noopener" class="btn btn-sm share-btn share-btn-wa">
                        <i class="bi bi-whatsapp me-1"></i>WhatsApp
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($sl_shareUrl) }}" target="_blank" rel="noopener" class="btn btn-sm share-btn share-btn-fb">
                        <i class="bi bi-facebook me-1"></i>Facebook
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($sl_shareUrl) }}" target="_blank" rel="noopener" class="btn btn-sm share-btn share-btn-li">
                        <i class="bi bi-linkedin me-1"></i>LinkedIn
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($sl_shareText) }}&url={{ urlencode($sl_shareUrl) }}" target="_blank" rel="noopener" class="btn btn-sm share-btn share-btn-tw">
                        <i class="bi bi-twitter-x me-1"></i>X
                    </a>
                    <a href="mailto:?subject={{ urlencode('My driving instructor profile') }}&body={{ urlencode($sl_shareText . ' ' . $sl_shareUrl) }}" class="btn btn-sm share-btn share-btn-email">
                        <i class="bi bi-envelope me-1"></i>Email
                    </a>
                    <button type="button" class="btn btn-sm share-btn share-btn-qr" data-bs-toggle="modal" data-bs-target="#shareQrModal">
                        <i class="bi bi-qr-code me-1"></i>QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR Code Modal --}}
<div class="modal fade" id="shareQrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-qr-code me-2"></i>Your profile QR code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="small text-muted mb-3">Print this QR code on flyers or business cards — anyone can scan it to view your profile and book lessons.</p>
                <div class="share-qr-wrap">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($sl_shareUrl) }}&size=300x300&margin=10" alt="Profile QR code" class="img-fluid" id="share-qr-img">
                </div>
                <div class="small text-muted mt-2">{{ $sl_shareUrl }}</div>
            </div>
            <div class="modal-footer">
                <a href="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($sl_shareUrl) }}&size=600x600&margin=20&format=png" download="securelicence-profile-qr.png" class="btn btn-warning fw-bold w-100">
                    <i class="bi bi-download me-1"></i>Download high-res PNG
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const btn = document.getElementById('share-profile-copy-btn');
    const input = document.getElementById('share-profile-url');
    if (!btn || !input) return;
    btn.addEventListener('click', function () {
        input.select();
        navigator.clipboard.writeText(input.value).then(() => {
            const span = btn.querySelector('span');
            const orig = span.textContent;
            span.textContent = 'Copied!';
            btn.classList.remove('btn-warning');
            btn.classList.add('btn-success');
            setTimeout(() => {
                span.textContent = orig;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-warning');
            }, 1800);
        });
    });
})();
</script>
@endpush
@endif

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

{{-- Pill-style tabs with count badges --}}
<div class="bk-tabs-wrap mb-3">
    <ul class="nav bk-pill-tabs" id="bookings-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-upcoming" data-bs-toggle="tab" data-bs-target="#panel-upcoming" type="button" role="tab">
                <i class="bi bi-calendar-event me-1"></i>Upcoming
                <span class="bk-tab-count" id="count-upcoming">0</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-pending" data-bs-toggle="tab" data-bs-target="#panel-pending" type="button" role="tab">
                <i class="bi bi-hourglass-split me-1"></i>Pending
                <span class="bk-tab-count bk-tab-count-amber" id="count-pending">0</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-history" data-bs-toggle="tab" data-bs-target="#panel-history" type="button" role="tab">
                <i class="bi bi-clock-history me-1"></i>History
                <span class="bk-tab-count" id="count-history">0</span>
            </button>
        </li>
    </ul>
</div>

<div class="tab-content" id="bookings-tab-content">

    {{-- ─── UPCOMING ─── --}}
    <div class="tab-pane fade show active" id="panel-upcoming" role="tabpanel">
        <div class="bk-toolbar mb-3" id="upcoming-toolbar" style="display: none;">
            <div class="bk-perpage">
                <span class="bk-perpage-label">Show</span>
                <select class="bk-perpage-select" data-tab="upcoming">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="bk-perpage-label">per page</span>
            </div>
            <div class="bk-results-info" id="upcoming-results-info"></div>
        </div>
        <div id="upcoming-loading" class="bk-loading">
            <div class="spinner-border spinner-border-sm text-warning me-2"></div>Loading upcoming bookings…
        </div>
        <div id="upcoming-list" style="display: none;"></div>
        <div id="upcoming-empty" class="bk-empty" style="display: none;">
            <i class="bi bi-calendar-x bk-empty-icon"></i>
            <h5>No upcoming bookings</h5>
            <p>When a learner books a lesson with you, it'll appear here. Share your profile to attract more bookings.</p>
            <a href="{{ route('instructor.learners') }}?open=propose" class="btn btn-warning fw-bold btn-sm">
                <i class="bi bi-car-front me-1"></i>Propose a Booking
            </a>
        </div>
        <nav id="upcoming-pagination" class="bk-pagination-wrap mt-3" style="display: none;" aria-label="Upcoming pagination"></nav>
    </div>

    {{-- ─── PENDING PROPOSALS ─── --}}
    <div class="tab-pane fade" id="panel-pending" role="tabpanel">
        <div class="bk-toolbar mb-3" id="pending-toolbar" style="display: none;">
            <div class="bk-perpage">
                <span class="bk-perpage-label">Show</span>
                <select class="bk-perpage-select" data-tab="pending">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="bk-perpage-label">per page</span>
            </div>
            <div class="bk-results-info" id="pending-results-info"></div>
        </div>
        <div id="pending-loading" class="bk-loading">
            <div class="spinner-border spinner-border-sm text-warning me-2"></div>Loading pending proposals…
        </div>
        <div id="pending-list" style="display: none;"></div>
        <div id="pending-empty" class="bk-empty" style="display: none;">
            <i class="bi bi-hourglass bk-empty-icon"></i>
            <h5>No pending proposals</h5>
            <p>Booking proposals you've sent to learners (waiting for them to confirm) will show here.</p>
            <a href="{{ route('instructor.learners') }}?open=propose" id="pending-create-link" class="btn btn-warning fw-bold btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Create a Proposal
            </a>
        </div>
        <nav id="pending-pagination" class="bk-pagination-wrap mt-3" style="display: none;" aria-label="Pending pagination"></nav>
    </div>

    {{-- ─── BOOKING HISTORY ─── --}}
    <div class="tab-pane fade" id="panel-history" role="tabpanel">
        {{-- Filter pills + per-page selector --}}
        <div class="bk-toolbar bk-toolbar-history mb-3">
            <div class="bk-history-filters" id="history-filters">
                <button type="button" class="bk-filter-pill active" data-filter="all">All</button>
                <button type="button" class="bk-filter-pill" data-filter="completed"><i class="bi bi-check-circle-fill me-1"></i>Completed</button>
                <button type="button" class="bk-filter-pill" data-filter="cancelled"><i class="bi bi-x-circle-fill me-1"></i>Cancelled</button>
            </div>
            <div class="bk-perpage">
                <span class="bk-perpage-label">Show</span>
                <select class="bk-perpage-select" data-tab="history">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="bk-perpage-label">per page</span>
            </div>
        </div>

        <div id="history-loading" class="bk-loading">
            <div class="spinner-border spinner-border-sm text-warning me-2"></div>Loading booking history…
        </div>
        <div id="history-wrap" class="card border-0 shadow-sm bk-history-card" style="display: none;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 bk-history-table">
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Learner</th>
                                <th>Date &amp; Time</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody id="history-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="history-empty" class="bk-empty" style="display: none;">
            <i class="bi bi-archive bk-empty-icon"></i>
            <h5>No booking history</h5>
            <p>Past bookings (completed or cancelled) will appear here. Your reputation builds with every lesson taught!</p>
        </div>
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
                <p class="text-muted small">Send an email invitation to a learner to join Secure Licence and book lessons with you.</p>
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

  // ——— Per-page state (DataTable-style) ———
  var perPageState = { upcoming: 10, pending: 10, history: 10 };

  document.querySelectorAll('.bk-perpage-select').forEach(function(sel) {
    sel.addEventListener('change', function() {
      var tab = sel.dataset.tab;
      perPageState[tab] = parseInt(sel.value, 10) || 10;
      if (tab === 'upcoming') loadUpcoming(1);
      else if (tab === 'pending') loadPending(1);
      else loadHistory(1);
    });
  });

  // ——— Helpers ———
  function setTabCount(tab, n) {
    var el = document.getElementById('count-' + tab);
    if (el) el.textContent = (n || 0);
  }
  function setResultsInfo(tab, data) {
    var el = document.getElementById(tab + '-results-info');
    if (!el) return;
    var total = data.total || 0;
    if (total === 0) { el.textContent = ''; return; }
    var from = data.from || ((data.current_page - 1) * data.per_page + 1);
    var to   = data.to   || Math.min(from + (data.data || []).length - 1, total);
    el.innerHTML = 'Showing <strong>' + from + '</strong>–<strong>' + to + '</strong> of <strong>' + total + '</strong>';
  }
  function showToolbar(tab, data) {
    var tb = document.getElementById(tab + '-toolbar');
    if (tb) tb.style.display = (data.total && data.total > 0) ? 'flex' : 'none';
  }
  function initialOf(name) {
    if (!name) return '?';
    var parts = name.trim().split(/\s+/);
    return ((parts[0][0] || '') + (parts.length > 1 ? parts[parts.length - 1][0] : '')).toUpperCase();
  }
  function relativeDayLabel(iso) {
    var d = new Date(iso);
    var now = new Date();
    var midToday = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
    var midDate = new Date(d.getFullYear(), d.getMonth(), d.getDate()).getTime();
    var diffDays = Math.round((midDate - midToday) / 86400000);
    if (diffDays < 0) return 'Past';
    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Tomorrow';
    if (diffDays <= 7) return 'This week';
    if (diffDays <= 30) return 'Later';
    return 'Upcoming';
  }
  function dateBlock(iso) {
    var d = new Date(iso);
    var months = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];
    var days = ['SUN','MON','TUE','WED','THU','FRI','SAT'];
    return '<div class="bk-date-block">' +
      '<div class="bk-date-month">' + months[d.getMonth()] + '</div>' +
      '<div class="bk-date-day">' + d.getDate() + '</div>' +
      '<div class="bk-date-wd">' + days[d.getDay()] + '</div>' +
    '</div>';
  }
  function timeRange(iso, mins) {
    var d = new Date(iso);
    var end = new Date(d.getTime() + (mins || 60) * 60000);
    function f(x) {
      var h = x.getHours(), m = x.getMinutes();
      var ap = h >= 12 ? 'pm' : 'am';
      h = h % 12; if (h === 0) h = 12;
      return h + ':' + (m < 10 ? '0' : '') + m + ap;
    }
    return f(d) + ' – ' + f(end);
  }

  // ——— Upcoming ———
  function renderUpcoming(data) {
    var list = document.getElementById('upcoming-list');
    var loading = document.getElementById('upcoming-loading');
    var empty = document.getElementById('upcoming-empty');
    var pagination = document.getElementById('upcoming-pagination');
    loading.style.display = 'none';
    var items = data.data || [];
    setTabCount('upcoming', data.total);
    if (items.length === 0) {
      list.style.display = 'none';
      empty.style.display = 'flex';
      pagination.style.display = 'none';
      return;
    }
    empty.style.display = 'none';
    list.style.display = 'block';

    // Group by relative day (Today / Tomorrow / This week / Later)
    var groups = {}; var order = [];
    items.forEach(function(b) {
      var g = relativeDayLabel(b.scheduled_at);
      if (!groups[g]) { groups[g] = []; order.push(g); }
      groups[g].push(b);
    });
    var groupOrder = ['Today', 'Tomorrow', 'This week', 'Later', 'Upcoming', 'Past'];
    order.sort(function(a, b) { return groupOrder.indexOf(a) - groupOrder.indexOf(b); });

    list.innerHTML = order.map(function(g) {
      return '<div class="bk-group-label"><span>' + g + '</span><span class="bk-group-count">' + groups[g].length + '</span></div>' +
        groups[g].map(function(b) {
          var location = (b.suburb && b.suburb.location) ? b.suburb.location : (b.suburb ? (b.suburb.name + ' ' + (b.suburb.postcode || '')) : '—');
          var learnerName = (b.learner && b.learner.name) ? esc(b.learner.name) : '—';
          var learnerPhone = (b.learner && b.learner.phone) ? esc(b.learner.phone) : '';
          var statusBadge = b.status === 'confirmed'
            ? '<span class="bk-status bk-status-confirmed"><i class="bi bi-check-circle-fill"></i>Confirmed</span>'
            : '<span class="bk-status bk-status-pending"><i class="bi bi-hourglass-split"></i>' + esc((b.status || '').toUpperCase()) + '</span>';
          var phoneHtml = learnerPhone
            ? '<a href="tel:' + learnerPhone + '" class="bk-card-meta-link"><i class="bi bi-telephone-fill"></i>' + learnerPhone + '</a>'
            : '';
          return '<div class="bk-card" data-booking-id="' + b.id + '">' +
            dateBlock(b.scheduled_at) +
            '<div class="bk-card-body">' +
              '<div class="bk-card-head">' +
                '<div class="bk-card-time"><i class="bi bi-clock me-1"></i>' + timeRange(b.scheduled_at, b.duration_minutes) + '</div>' +
                statusBadge +
              '</div>' +
              '<div class="bk-card-learner">' +
                '<span class="bk-avatar">' + initialOf(learnerName) + '</span>' +
                '<div class="bk-learner-info">' +
                  '<div class="bk-learner-name">' + learnerName + '</div>' +
                  '<div class="bk-card-meta">' +
                    '<span><i class="bi bi-geo-alt-fill"></i>' + esc(location) + '</span>' +
                    '<span><i class="bi bi-car-front-fill"></i>' + esc(lessonLabel(b)) + '</span>' +
                    phoneHtml +
                  '</div>' +
                '</div>' +
              '</div>' +
              '<div class="bk-card-actions">' +
                '<a href="#" class="btn btn-sm btn-warning fw-bold booking-manage-link" data-booking-id="' + b.id + '">' +
                  '<i class="bi bi-eye me-1"></i>Manage' +
                '</a>' +
                '<span class="bk-card-id">#' + b.id + '</span>' +
              '</div>' +
            '</div>' +
          '</div>';
        }).join('');
    }).join('');
    renderPagination(pagination, data, 'upcoming');
    pagination.style.display = (data.last_page > 1) ? 'block' : 'none';
  }

  function loadUpcoming(page) {
    page = page || 1;
    document.getElementById('upcoming-loading').style.display = 'block';
    document.getElementById('upcoming-list').style.display = 'none';
    document.getElementById('upcoming-empty').style.display = 'none';
    fetch('/api/bookings?tab=upcoming&page=' + page + '&per_page=' + perPageState.upcoming, opts)
      .then(function(r) { return r.json(); })
      .then(function(data) { renderUpcoming(data); showToolbar('upcoming', data); setResultsInfo('upcoming', data); })
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
    setTabCount('pending', data.total);
    if (items.length === 0) {
      list.style.display = 'none';
      empty.style.display = 'flex';
      pagination.style.display = 'none';
      return;
    }
    empty.style.display = 'none';
    list.style.display = 'block';
    list.innerHTML = items.map(function(b) {
      var location = (b.suburb && b.suburb.location) ? b.suburb.location : (b.suburb ? (b.suburb.name + ' ' + (b.suburb.postcode || '')) : '—');
      var learnerName = (b.learner && b.learner.name) ? esc(b.learner.name) : '—';
      var learnerPhone = (b.learner && b.learner.phone) ? esc(b.learner.phone) : '';
      var phoneHtml = learnerPhone
        ? '<a href="tel:' + learnerPhone + '" class="bk-card-meta-link"><i class="bi bi-telephone-fill"></i>' + learnerPhone + '</a>'
        : '';
      // Expiry countdown if backend supplies proposal_expires_at
      var expiryHtml = '';
      if (b.proposal_expires_at) {
        var hrs = Math.max(0, Math.round((new Date(b.proposal_expires_at) - new Date()) / 3600000));
        expiryHtml = '<span class="bk-pending-expiry"><i class="bi bi-stopwatch me-1"></i>Expires in ' + hrs + 'h</span>';
      }
      return '<div class="bk-card bk-card-pending" data-booking-id="' + b.id + '">' +
        dateBlock(b.scheduled_at) +
        '<div class="bk-card-body">' +
          '<div class="bk-card-head">' +
            '<div class="bk-card-time"><i class="bi bi-clock me-1"></i>' + timeRange(b.scheduled_at, b.duration_minutes) + '</div>' +
            '<span class="bk-status bk-status-pending"><i class="bi bi-hourglass-split"></i>Awaiting learner</span>' +
          '</div>' +
          '<div class="bk-card-learner">' +
            '<span class="bk-avatar">' + initialOf(learnerName) + '</span>' +
            '<div class="bk-learner-info">' +
              '<div class="bk-learner-name">' + learnerName + '</div>' +
              '<div class="bk-card-meta">' +
                '<span><i class="bi bi-geo-alt-fill"></i>' + esc(location) + '</span>' +
                '<span><i class="bi bi-car-front-fill"></i>' + esc(lessonLabel(b)) + '</span>' +
                phoneHtml +
              '</div>' +
              (expiryHtml ? '<div class="mt-1">' + expiryHtml + '</div>' : '') +
            '</div>' +
          '</div>' +
          '<div class="bk-card-actions">' +
            '<a href="#" class="btn btn-sm btn-warning fw-bold booking-manage-link" data-booking-id="' + b.id + '">' +
              '<i class="bi bi-eye me-1"></i>View Proposal' +
            '</a>' +
            '<span class="bk-card-id">#' + b.id + '</span>' +
          '</div>' +
        '</div>' +
      '</div>';
    }).join('');
    renderPagination(pagination, data, 'pending');
    pagination.style.display = (data.last_page > 1) ? 'block' : 'none';
  }

  function loadPending(page) {
    page = page || 1;
    document.getElementById('pending-loading').style.display = 'block';
    document.getElementById('pending-list').style.display = 'none';
    document.getElementById('pending-empty').style.display = 'none';
    fetch('/api/bookings?tab=pending&page=' + page + '&per_page=' + perPageState.pending, opts)
      .then(function(r) { return r.json(); })
      .then(function(data) { renderPending(data); showToolbar('pending', data); setResultsInfo('pending', data); })
      .catch(function() {
        document.getElementById('pending-loading').style.display = 'none';
        document.getElementById('pending-empty').style.display = 'flex';
      });
  }

  // ——— History ———
  var historyFilter = 'all';
  function renderHistory(data) {
    var wrap = document.getElementById('history-wrap');
    var tbody = document.getElementById('history-tbody');
    var loading = document.getElementById('history-loading');
    var empty = document.getElementById('history-empty');
    var pagination = document.getElementById('history-pagination');
    loading.style.display = 'none';
    var items = data.data || [];

    // Total count badge is the full count (not filtered)
    setTabCount('history', data.total);

    // Client-side filter on the page we have
    if (historyFilter !== 'all') {
      items = items.filter(function(b) { return (b.status || '').toLowerCase() === historyFilter; });
    }

    if (items.length === 0) {
      wrap.style.display = 'none';
      empty.style.display = 'flex';
      pagination.style.display = 'none';
      return;
    }
    empty.style.display = 'none';
    wrap.style.display = 'block';

    tbody.innerHTML = items.map(function(b) {
      var isCancel = b.status === 'cancelled';
      var statusBadge = isCancel
        ? '<span class="bk-status bk-status-cancelled"><i class="bi bi-x-circle-fill"></i>Cancelled</span>'
        : '<span class="bk-status bk-status-completed"><i class="bi bi-check-circle-fill"></i>Completed</span>';
      var ps = (b.payment_status || '').toLowerCase();
      var payBadge = '—';
      if (ps === 'paid')      payBadge = '<span class="bk-pay bk-pay-paid"><i class="bi bi-check-lg"></i>Paid</span>';
      else if (ps === 'refunded') payBadge = '<span class="bk-pay bk-pay-refunded"><i class="bi bi-arrow-counterclockwise"></i>Refunded</span>';
      else if (ps === 'failed')   payBadge = '<span class="bk-pay bk-pay-failed"><i class="bi bi-x-lg"></i>Failed</span>';
      else if (ps === 'pending')  payBadge = '<span class="bk-pay bk-pay-pending"><i class="bi bi-clock"></i>Pending</span>';

      var location = (b.suburb && b.suburb.location) ? b.suburb.location : (b.suburb ? (b.suburb.name + ' ' + (b.suburb.postcode || '')) : '—');
      var learnerName = (b.learner && b.learner.name) ? esc(b.learner.name) : '—';
      return '<tr>' +
        '<td class="bk-history-id">#' + b.id + '</td>' +
        '<td><div class="d-flex align-items-center gap-2"><span class="bk-avatar bk-avatar-sm">' + initialOf(learnerName) + '</span><span class="fw-semibold">' + learnerName + '</span></div></td>' +
        '<td class="small text-nowrap">' + formatDate(b.scheduled_at) + '<br><span class="text-muted">' + timeRange(b.scheduled_at, b.duration_minutes) + '</span></td>' +
        '<td class="small"><i class="bi bi-geo-alt me-1 text-muted"></i>' + esc(location) + '</td>' +
        '<td>' + statusBadge + '</td>' +
        '<td>' + payBadge + '</td>' +
        '<td class="text-end"><a href="#" class="btn btn-sm btn-outline-secondary booking-manage-link" data-booking-id="' + b.id + '"><i class="bi bi-eye"></i></a></td>' +
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
    fetch('/api/bookings?tab=history&page=' + page + '&per_page=' + perPageState.history, opts)
      .then(function(r) { return r.json(); })
      .then(function(data) { renderHistory(data); })
      .catch(function() {
        document.getElementById('history-loading').style.display = 'none';
        document.getElementById('history-empty').style.display = 'flex';
      });
  }

  // History filter pills
  document.querySelectorAll('#history-filters .bk-filter-pill').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('#history-filters .bk-filter-pill').forEach(function(b) { b.classList.remove('active'); });
      btn.classList.add('active');
      historyFilter = btn.dataset.filter;
      loadHistory(1);
    });
  });

  // ——— DataTable-style pagination ———
  // Builds: [Showing X–Y of Z]   [« First] [‹ Prev] [1] [2] ... [8] [Next ›] [Last »]
  function renderPagination(navEl, data, tab) {
    var cur  = data.current_page || 1;
    var last = data.last_page    || 1;
    var total = data.total       || 0;

    if (total === 0) { navEl.innerHTML = ''; navEl.style.display = 'none'; return; }

    // Build page-number list with ellipsis (max ~7 visible numbers)
    function pageList() {
      var pages = [];
      if (last <= 7) {
        for (var i = 1; i <= last; i++) pages.push(i);
        return pages;
      }
      pages.push(1);
      var start = Math.max(2, cur - 2);
      var end   = Math.min(last - 1, cur + 2);
      if (start > 2) pages.push('…');
      for (var j = start; j <= end; j++) pages.push(j);
      if (end < last - 1) pages.push('…');
      pages.push(last);
      return pages;
    }

    var info = '';
    if (total > 0) {
      var from = data.from || ((cur - 1) * data.per_page + 1);
      var to   = data.to   || Math.min(from + (data.data || []).length - 1, total);
      info = '<div class="bk-pagination-info">Showing <strong>' + from + '</strong>–<strong>' + to + '</strong> of <strong>' + total + '</strong></div>';
    }

    var btns = [];
    btns.push('<button type="button" class="bk-page-btn" data-page="1" ' + (cur === 1 ? 'disabled' : '') + ' aria-label="First page"><i class="bi bi-chevron-double-left"></i></button>');
    btns.push('<button type="button" class="bk-page-btn" data-page="' + Math.max(1, cur - 1) + '" ' + (cur === 1 ? 'disabled' : '') + ' aria-label="Previous page"><i class="bi bi-chevron-left"></i></button>');

    pageList().forEach(function(p) {
      if (p === '…') {
        btns.push('<span class="bk-page-ellipsis">…</span>');
      } else {
        btns.push('<button type="button" class="bk-page-btn bk-page-num ' + (p === cur ? 'active' : '') + '" data-page="' + p + '">' + p + '</button>');
      }
    });

    btns.push('<button type="button" class="bk-page-btn" data-page="' + Math.min(last, cur + 1) + '" ' + (cur === last ? 'disabled' : '') + ' aria-label="Next page"><i class="bi bi-chevron-right"></i></button>');
    btns.push('<button type="button" class="bk-page-btn" data-page="' + last + '" ' + (cur === last ? 'disabled' : '') + ' aria-label="Last page"><i class="bi bi-chevron-double-right"></i></button>');

    var infoHtml = (tab === 'history') ? info : ''; // upcoming/pending show info in top toolbar already
    navEl.innerHTML = infoHtml + '<div class="bk-pagination-controls">' + btns.join('') + '</div>';
    navEl.style.display = 'flex';

    navEl.querySelectorAll('button[data-page]').forEach(function(b) {
      b.addEventListener('click', function() {
        if (b.disabled) return;
        var p = parseInt(b.getAttribute('data-page'), 10);
        if (!p || p === cur) return;
        if (tab === 'upcoming') loadUpcoming(p);
        else if (tab === 'pending') loadPending(p);
        else loadHistory(p);
        // Scroll list back to top so user sees the new page from the top
        var listEl = document.getElementById(tab === 'history' ? 'history-wrap' : tab + '-list');
        if (listEl) listEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
