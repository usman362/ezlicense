@extends('layouts.instructor')

@section('title', 'Learners')
@section('heading', 'Learners')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Learners</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h4 class="fw-bolder mb-0">My Learners</h4>
        <p class="text-muted small mb-0">Everyone you've taught or invited.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary fw-semibold" id="invite-learner-btn">
            <i class="bi bi-person-plus-fill me-1"></i>Invite Learner
        </button>
        <button type="button" class="btn btn-warning fw-bold" id="propose-booking-btn" data-learner-id="" data-learner-name="">
            <i class="bi bi-car-front-fill me-1"></i>Propose Booking
        </button>
    </div>
</div>

{{-- Pill tabs with count badges --}}
<div class="bk-tabs-wrap mb-3">
    <ul class="nav bk-pill-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-my-learners" data-bs-toggle="tab" data-bs-target="#my-learners" type="button">
                <i class="bi bi-people-fill me-1"></i>My Learners
                <span class="bk-tab-count" id="count-my-learners">0</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-pending" data-bs-toggle="tab" data-bs-target="#pending-invites" type="button">
                <i class="bi bi-envelope-paper-fill me-1"></i>Pending Invites
                <span class="bk-tab-count bk-tab-count-amber" id="count-pending-invites">0</span>
            </button>
        </li>
    </ul>
</div>

{{-- Toolbar — search + per-page + results info --}}
<div class="bk-toolbar mb-3" id="learners-toolbar">
    <div class="bk-perpage">
        <span class="bk-perpage-label">Show</span>
        <select class="bk-perpage-select" id="learners-perpage">
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <span class="bk-perpage-label">per page</span>
    </div>
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="bk-results-info" id="learners-results-info"></div>
        <div class="input-group" style="max-width: 280px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" class="form-control border-start-0 ps-0" id="learners-search" placeholder="Search name, phone, email…">
        </div>
    </div>
</div>

<div id="learners-loading" class="bk-loading">
    <div class="spinner-border spinner-border-sm text-warning me-2"></div>Loading learners…
</div>

<div id="learners-table-wrap" class="card border-0 shadow-sm bk-history-card" style="display: none;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 bk-history-table">
                <thead>
                    <tr>
                        <th>Learner</th>
                        <th>Contact</th>
                        <th>Guardian</th>
                        <th class="text-center">Hours</th>
                        <th class="text-center">Upcoming</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="learners-tbody"></tbody>
            </table>
        </div>
    </div>
</div>

<div id="learners-empty" class="bk-empty" style="display: none;">
    <i class="bi bi-people bk-empty-icon"></i>
    <h5>No learners yet</h5>
    <p>Once learners book lessons or accept your invites, they'll appear here. Send your first invite to get started.</p>
    <button type="button" class="btn btn-warning fw-bold btn-sm" id="invite-learner-empty-btn">
        <i class="bi bi-person-plus-fill me-1"></i>Invite a Learner
    </button>
</div>

<nav id="learners-pagination" class="bk-pagination-wrap mt-3" style="display: none;" aria-label="Learners pagination"></nav>

{{-- New Booking Proposal Modal --}}
<div class="modal fade" id="proposal-modal" tabindex="-1" aria-labelledby="proposal-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="proposal-modal-title">New Booking Proposal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="mb-3">
                    <label class="form-label">Learner</label>
                    <div class="d-flex gap-2">
                        <select class="form-select flex-grow-1" id="proposal-learner-select">
                            <option value="">Select a learner</option>
                        </select>
                        <button type="button" class="btn btn-outline-secondary" id="proposal-add-new-learner">+ Add New</button>
                    </div>
                </div>

                <div id="proposal-selected-learner-card" class="border rounded p-3 bg-light mb-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="mb-1"><i class="bi bi-person me-1 text-muted"></i><strong id="proposal-selected-learner-name"></strong> <span class="badge badge-draft ms-1">DRAFT</span></div>
                            <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i><span id="proposal-selected-learner-address">—</span></div>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary p-1" id="proposal-selected-learner-edit" title="Edit"><i class="bi bi-pencil"></i></button>
                            <button type="button" class="btn btn-outline-secondary p-1 text-danger" id="proposal-selected-learner-remove" title="Remove"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>

                <div id="proposal-cards-container" class="mb-3"></div>

                <div id="proposal-form-container" class="border rounded p-3 bg-light mb-3" style="display: none;">
                    <h6 class="mb-3">Add booking</h6>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">Booking type</label>
                            <select class="form-select form-select-sm" id="proposal-type">
                                <option value="lesson">Driving Lesson - 1hr</option>
                                <option value="test_package">Test Package</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Lesson date</label>
                            <input type="date" class="form-control form-control-sm" id="proposal-date" min="">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Recurrence</label>
                            <select class="form-select form-select-sm" id="proposal-recurrence">
                                <option value="none">Do not repeat</option>
                                <option value="weekly">Weekly</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Occurrences</label>
                            <input type="number" class="form-control form-control-sm" id="proposal-occurrences" value="2" min="1" max="52">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Lesson time</label>
                            <select class="form-select form-select-sm" id="proposal-time"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Location (suburb)</label>
                            <select class="form-select form-select-sm" id="proposal-suburb">
                                <option value="">Select suburb</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Transmission</label>
                            <select class="form-select form-select-sm" id="proposal-transmission">
                                <option value="auto">Auto</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="proposal-form-cancel">Cancel</button>
                        <button type="button" class="btn btn-sm btn-warning" id="proposal-form-review">Review &gt;</button>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mb-2">
                    <button type="button" class="btn btn-outline-secondary" id="proposal-add-booking-btn">
                        <i class="bi bi-plus me-1"></i> Add Another Booking
                    </button>
                    <button type="button" class="btn btn-success" id="proposal-send-btn" style="display: none;">
                        Send Booking Proposal
                    </button>
                </div>
                <p class="small text-muted mb-0">Booking proposals will 'hold' the selected time slot(s) until confirmed by the learner. Proposals remain valid for 24 hours OR the proposed booking start time (whichever arrives first).</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <span class="text-muted small">Secure Licence</span>
            </div>
        </div>
    </div>
</div>

{{-- Learner Detail Modal --}}
<div class="modal fade" id="learner-detail-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="learner-detail-title">Learner Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="learner-detail-body">
                <div class="text-center text-muted py-3">Loading…</div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning btn-sm" id="learner-detail-propose-btn">Propose Booking</button>
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
                <p class="text-muted small">Send an email invitation to a learner to join Secure Licence and book with you.</p>
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
.proposal-card { background: #fff; border: 1px solid #dee2e6; border-radius: 8px; padding: 12px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: flex-start; }
.proposal-card .badge-draft { background: #6f42c1; color: #fff; }
</style>

<style>
.nav-tabs .nav-link.active { border-bottom: 2px solid #f0ad4e; font-weight: 500; }
#learners-tbody tr:hover { background-color: #f8f9fa; }
.learner-name-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #f0ad4e; margin-right: 6px; vertical-align: middle; }
</style>
@push('scripts')
<script>
(function() {
  var searchInput = document.getElementById('learners-search');
  var loadingEl   = document.getElementById('learners-loading');
  var tableWrap   = document.getElementById('learners-table-wrap');
  var tbody       = document.getElementById('learners-tbody');
  var emptyEl     = document.getElementById('learners-empty');
  var toolbarEl   = document.getElementById('learners-toolbar');
  var paginationEl= document.getElementById('learners-pagination');
  var infoEl      = document.getElementById('learners-results-info');
  var perPageSel  = document.getElementById('learners-perpage');
  var perPage     = 10;
  var currentPage = 1;
  var currentQuery= '';

  function escapeHtml(s) {
    if (s == null || s === '') return '—';
    var div = document.createElement('div');
    div.textContent = s;
    return div.innerHTML;
  }
  function escAttr(s) { return (s || '').replace(/"/g, '&quot;'); }
  function initialOf(name) {
    if (!name) return '?';
    var parts = name.trim().split(/\s+/);
    return ((parts[0][0] || '') + (parts.length > 1 ? parts[parts.length - 1][0] : '')).toUpperCase();
  }

  function setTabCount(id, n) {
    var el = document.getElementById(id);
    if (el) el.textContent = (n || 0);
  }

  function render(data) {
    var list = data.data || [];
    var total = data.total || 0;

    // Tab count badge
    setTabCount('count-my-learners', total);

    if (list.length === 0) {
      tableWrap.style.display = 'none';
      paginationEl.style.display = 'none';
      toolbarEl.style.display = currentQuery ? 'flex' : 'none';
      emptyEl.style.display = 'flex';
      // Customise empty copy based on whether it's a search no-result vs truly empty
      if (currentQuery) {
        emptyEl.querySelector('h5').textContent = 'No learners match your search';
        emptyEl.querySelector('p').textContent = 'Try a different name, phone or email — or clear the search.';
      } else {
        emptyEl.querySelector('h5').textContent = 'No learners yet';
        emptyEl.querySelector('p').textContent = "Once learners book lessons or accept your invites, they'll appear here. Send your first invite to get started.";
      }
      return;
    }

    emptyEl.style.display = 'none';
    tableWrap.style.display = 'block';
    toolbarEl.style.display = 'flex';

    // Results info "Showing 1–10 of 47"
    if (infoEl && total > 0) {
      var from = data.from || 1;
      var to   = data.to   || list.length;
      infoEl.innerHTML = 'Showing <strong>' + from + '</strong>–<strong>' + to + '</strong> of <strong>' + total + '</strong>';
    }

    tbody.innerHTML = list.map(function(row) {
      var learner = row.learner || {};
      var name = escapeHtml(learner.name || '—');
      var phone = learner.phone ? '<i class="bi bi-telephone-fill text-muted me-1"></i>' + escapeHtml(learner.phone) : '';
      var email = learner.email ? '<i class="bi bi-envelope-fill text-muted me-1"></i>' + escapeHtml(learner.email) : '';
      var contact = phone || email || '<span class="text-muted">—</span>';
      var guardian = row.guardian && row.guardian.name
        ? '<span class="fw-semibold">' + escapeHtml(row.guardian.name) + '</span>'
        : '<span class="text-muted">—</span>';
      var hours = row.hours_completed != null ? row.hours_completed : 0;
      var upcoming = row.upcoming_bookings != null ? row.upcoming_bookings : 0;
      var hoursPill = hours > 0
        ? '<span class="bk-pay bk-pay-paid">' + hours + 'h</span>'
        : '<span class="text-muted small">0h</span>';
      var upcomingPill = upcoming > 0
        ? '<span class="bk-status bk-status-confirmed">' + upcoming + '</span>'
        : '<span class="text-muted small">0</span>';
      var learnerId = learner.id;
      var nameAttr = escAttr(learner.name || '');
      return '<tr>' +
        '<td><div class="d-flex align-items-center gap-2">' +
          '<span class="bk-avatar bk-avatar-sm">' + initialOf(learner.name) + '</span>' +
          '<div><div class="fw-bold">' + name + '</div>' +
          (row.has_bookings ? '' : '<small class="text-muted"><i class="bi bi-envelope-paper me-1"></i>Invited (no bookings yet)</small>') +
          '</div></div></td>' +
        '<td class="small">' + contact + '</td>' +
        '<td class="small">' + guardian + '</td>' +
        '<td class="text-center">' + hoursPill + '</td>' +
        '<td class="text-center">' + upcomingPill + '</td>' +
        '<td class="text-end">' +
          '<div class="d-inline-flex gap-1">' +
            '<a href="#" class="btn btn-sm btn-outline-secondary learner-details-link" data-learner-id="' + learnerId + '" data-learner-name="' + nameAttr + '" title="View details"><i class="bi bi-eye"></i></a>' +
            '<button type="button" class="btn btn-sm btn-warning fw-bold propose-booking-row" data-learner-id="' + learnerId + '" data-learner-name="' + nameAttr + '"><i class="bi bi-car-front me-1"></i>Propose</button>' +
          '</div>' +
        '</td></tr>';
    }).join('');

    renderPagination(data);
  }

  // ── DataTable-style pagination (First / Prev / numbers w/ ellipsis / Next / Last) ──
  function renderPagination(data) {
    var cur  = data.current_page || 1;
    var last = data.last_page    || 1;
    if (last <= 1) { paginationEl.style.display = 'none'; return; }

    function pageList() {
      var pages = [];
      if (last <= 7) { for (var i = 1; i <= last; i++) pages.push(i); return pages; }
      pages.push(1);
      var start = Math.max(2, cur - 2);
      var end   = Math.min(last - 1, cur + 2);
      if (start > 2) pages.push('…');
      for (var j = start; j <= end; j++) pages.push(j);
      if (end < last - 1) pages.push('…');
      pages.push(last);
      return pages;
    }

    var btns = [];
    btns.push('<button type="button" class="bk-page-btn" data-page="1" ' + (cur === 1 ? 'disabled' : '') + ' aria-label="First page"><i class="bi bi-chevron-double-left"></i></button>');
    btns.push('<button type="button" class="bk-page-btn" data-page="' + Math.max(1, cur - 1) + '" ' + (cur === 1 ? 'disabled' : '') + ' aria-label="Previous page"><i class="bi bi-chevron-left"></i></button>');
    pageList().forEach(function(p) {
      if (p === '…') btns.push('<span class="bk-page-ellipsis">…</span>');
      else btns.push('<button type="button" class="bk-page-btn bk-page-num ' + (p === cur ? 'active' : '') + '" data-page="' + p + '">' + p + '</button>');
    });
    btns.push('<button type="button" class="bk-page-btn" data-page="' + Math.min(last, cur + 1) + '" ' + (cur === last ? 'disabled' : '') + ' aria-label="Next page"><i class="bi bi-chevron-right"></i></button>');
    btns.push('<button type="button" class="bk-page-btn" data-page="' + last + '" ' + (cur === last ? 'disabled' : '') + ' aria-label="Last page"><i class="bi bi-chevron-double-right"></i></button>');

    paginationEl.innerHTML = '<div class="bk-pagination-controls">' + btns.join('') + '</div>';
    paginationEl.style.display = 'flex';
    paginationEl.querySelectorAll('button[data-page]').forEach(function(b) {
      b.addEventListener('click', function() {
        if (b.disabled) return;
        var p = parseInt(b.getAttribute('data-page'), 10);
        if (!p || p === cur) return;
        load(currentQuery, p);
        tableWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    });
  }

  function load(q, page) {
    currentQuery = q || '';
    currentPage  = page || 1;
    loadingEl.style.display = 'block';
    tableWrap.style.display = 'none';
    emptyEl.style.display = 'none';
    paginationEl.style.display = 'none';

    var url = '/api/instructor/learners?page=' + currentPage + '&per_page=' + perPage;
    if (currentQuery) url += '&q=' + encodeURIComponent(currentQuery);

    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        loadingEl.style.display = 'none';
        render(res || {});
      })
      .catch(function() {
        loadingEl.style.display = 'none';
        emptyEl.querySelector('h5').textContent = 'Could not load learners';
        emptyEl.querySelector('p').textContent  = 'Please refresh the page and try again.';
        emptyEl.style.display = 'flex';
      });
  }

  // Per-page selector
  if (perPageSel) {
    perPageSel.addEventListener('change', function() {
      perPage = parseInt(this.value, 10) || 10;
      load(currentQuery, 1);
    });
  }

  // Empty-state invite button (delegates to main invite button)
  var emptyInviteBtn = document.getElementById('invite-learner-empty-btn');
  if (emptyInviteBtn) {
    emptyInviteBtn.addEventListener('click', function() {
      var main = document.getElementById('invite-learner-btn');
      if (main) main.click();
    });
  }

  var searchTimeout;
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() { load(searchInput.value.trim(), 1); }, 300);
    });
  }

  // ── Pending Invites tab — loads real data + supports resend/cancel ──
  var MY_LEARNERS_HEADERS = '<th>Learner</th><th>Contact</th><th>Guardian</th><th class="text-center">Hours</th><th class="text-center">Upcoming</th><th class="text-end">Actions</th>';
  var PENDING_HEADERS = '<th>Invitee</th><th>Sent</th><th>Personal Message</th><th>Status</th><th class="text-end">Actions</th>';

  function loadPendingInvites() {
    loadingEl.style.display = 'block';
    tableWrap.style.display = 'none';
    emptyEl.style.display = 'none';
    paginationEl.style.display = 'none';
    toolbarEl.style.display = 'none'; // hide per-page on pending — no pagination needed

    fetch('/api/instructor/learners/pending-invites', {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      loadingEl.style.display = 'none';
      var invites = res.data || [];
      setTabCount('count-pending-invites', invites.length);

      if (invites.length === 0) {
        emptyEl.querySelector('h5').textContent = 'No pending invites';
        emptyEl.querySelector('p').textContent = 'Click "Invite Learner" to send your first invite — they\'ll get an email with a link to join your roster.';
        emptyEl.style.display = 'flex';
        return;
      }

      tableWrap.style.display = 'block';

      // Swap to pending-invites headers
      var thead = tableWrap.querySelector('thead tr');
      thead.innerHTML = PENDING_HEADERS;

      tbody.innerHTML = invites.map(function(inv) {
        var sentDate = new Date(inv.sent_at);
        var sentLabel = sentDate.toLocaleDateString() + ' ' + sentDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        var msg = inv.personal_message
          ? escapeHtml(inv.personal_message.length > 60 ? inv.personal_message.substr(0, 60) + '…' : inv.personal_message)
          : '<span class="text-muted">—</span>';
        var statusBadge = inv.is_expired
          ? '<span class="bk-status bk-status-cancelled"><i class="bi bi-clock-history"></i>Expired</span>'
          : '<span class="bk-status bk-status-pending"><i class="bi bi-hourglass-split"></i>Awaiting</span>';
        var displayName = inv.invitee_name || inv.invitee_email;
        var emailLine = inv.invitee_name
          ? '<small class="text-muted"><i class="bi bi-envelope me-1"></i>' + escapeHtml(inv.invitee_email) + '</small>'
          : '';

        return '<tr>' +
          '<td><div class="d-flex align-items-center gap-2">' +
            '<span class="bk-avatar bk-avatar-sm">' + initialOf(displayName) + '</span>' +
            '<div><div class="fw-bold">' + escapeHtml(displayName) + '</div>' + emailLine + '</div></div></td>' +
          '<td class="small text-nowrap"><i class="bi bi-clock me-1 text-muted"></i>' + sentLabel + '</td>' +
          '<td class="small">' + msg + '</td>' +
          '<td>' + statusBadge + '</td>' +
          '<td class="text-end">' +
            '<div class="d-inline-flex gap-1">' +
              (!inv.is_expired
                ? '<button class="btn btn-sm btn-outline-secondary invite-resend-btn" data-id="' + inv.id + '" title="Resend invite"><i class="bi bi-arrow-clockwise"></i></button>'
                : '') +
              '<button class="btn btn-sm btn-outline-danger invite-cancel-btn" data-id="' + inv.id + '" data-email="' + escAttr(inv.invitee_email) + '" title="Cancel invite"><i class="bi bi-trash"></i></button>' +
            '</div>' +
          '</td></tr>';
      }).join('');
    })
    .catch(function() {
      loadingEl.style.display = 'none';
      emptyEl.querySelector('h5').textContent = 'Could not load pending invites';
      emptyEl.querySelector('p').textContent = 'Please refresh the page and try again.';
      emptyEl.style.display = 'flex';
    });
  }

  document.getElementById('tab-pending').addEventListener('shown.bs.tab', function() {
    loadPendingInvites();
  });
  document.getElementById('tab-my-learners').addEventListener('shown.bs.tab', function() {
    // Restore my-learners table headers
    var thead = tableWrap.querySelector('thead tr');
    thead.innerHTML = MY_LEARNERS_HEADERS;
    load(searchInput ? searchInput.value.trim() : '', 1);
  });

  // Resend / cancel invite handlers (delegated)
  tbody.addEventListener('click', function(e) {
    var resendBtn = e.target.closest('.invite-resend-btn');
    var cancelBtn = e.target.closest('.invite-cancel-btn');
    var csrf = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrf ? csrf.getAttribute('content') : '';

    if (resendBtn) {
      e.preventDefault();
      var id = resendBtn.getAttribute('data-id');
      resendBtn.disabled = true;
      resendBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
      fetch('/api/instructor/learners/invite/' + id + '/resend', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      })
      .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
      .then(function(res) {
        if (res.ok) {
          alert(res.data.message || 'Invite resent.');
        } else {
          alert(res.data.message || 'Failed to resend.');
        }
        loadPendingInvites();
      });
    }

    if (cancelBtn) {
      e.preventDefault();
      var id = cancelBtn.getAttribute('data-id');
      var email = cancelBtn.getAttribute('data-email');
      if (!confirm('Cancel invite to ' + email + '?')) return;
      fetch('/api/instructor/learners/invite/' + id, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      })
      .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
      .then(function(res) {
        if (res.ok) loadPendingInvites();
        else alert(res.data.message || 'Failed to cancel.');
      });
    }
  });
  window.addEventListener('learners-refresh', function() {
    load(searchInput ? searchInput.value.trim() : '');
  });

  tbody.addEventListener('click', function(e) {
    if (e.target.classList.contains('learner-details-link')) {
      e.preventDefault();
      var id = e.target.getAttribute('data-learner-id');
      var name = e.target.getAttribute('data-learner-name') || 'Learner';
      showLearnerDetail(id, name);
    }
    if (e.target.closest && e.target.closest('.propose-booking-row')) {
      e.preventDefault();
      var btn = e.target.closest('.propose-booking-row');
      openProposalModal(btn.getAttribute('data-learner-id'), btn.getAttribute('data-learner-name') || '');
    }
  });

  function showLearnerDetail(learnerId, learnerName) {
    var body = document.getElementById('learner-detail-body');
    body.innerHTML = '<div class="text-center text-muted py-3">Loading…</div>';
    document.getElementById('learner-detail-title').textContent = learnerName;
    var modal = new bootstrap.Modal(document.getElementById('learner-detail-modal'));
    modal.show();

    fetch('/api/instructor/learners/' + learnerId, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        var d = res.data || {};
        var html = '<div class="row g-2 small mb-3">' +
          '<div class="col-6"><strong>Name</strong><br>' + escapeHtml(d.name) + '</div>' +
          '<div class="col-6"><strong>Email</strong><br>' + escapeHtml(d.email) + '</div>' +
          '<div class="col-6"><strong>Phone</strong><br>' + (d.phone ? '<a href="tel:' + d.phone + '">' + escapeHtml(d.phone) + '</a>' : '—') + '</div>' +
          '<div class="col-6"><strong>Joined</strong><br>' + escapeHtml(d.joined) + '</div>' +
          '<div class="col-6"><strong>Hours Completed</strong><br>' + (d.hours_completed || 0) + '</div>' +
          '<div class="col-6"><strong>Total Bookings</strong><br>' + (d.total_bookings || 0) + '</div>' +
        '</div>';
        if (d.upcoming_bookings && d.upcoming_bookings.length > 0) {
          html += '<h6 class="fw-bold small">Upcoming Bookings</h6><ul class="list-unstyled small">';
          d.upcoming_bookings.forEach(function(b) {
            var dt = b.scheduled_at ? new Date(b.scheduled_at) : null;
            html += '<li class="border-bottom py-1">#' + b.id + ' — ' + (dt ? dt.toLocaleDateString() : '—') + ' · ' + (b.type === 'test_package' ? 'Test Package' : 'Lesson') + ' <span class="badge bg-success">' + b.status + '</span></li>';
          });
          html += '</ul>';
        }
        if (d.recent_bookings && d.recent_bookings.length > 0) {
          html += '<h6 class="fw-bold small mt-2">Recent Bookings</h6><ul class="list-unstyled small">';
          d.recent_bookings.forEach(function(b) {
            var dt = b.scheduled_at ? new Date(b.scheduled_at) : null;
            var sc = b.status === 'cancelled' ? 'bg-danger' : (b.status === 'completed' ? 'bg-success' : 'bg-warning');
            html += '<li class="border-bottom py-1">#' + b.id + ' — ' + (dt ? dt.toLocaleDateString() : '—') + ' <span class="badge ' + sc + '">' + b.status + '</span></li>';
          });
          html += '</ul>';
        }
        body.innerHTML = html;

        document.getElementById('learner-detail-propose-btn').onclick = function() {
          bootstrap.Modal.getInstance(document.getElementById('learner-detail-modal')).hide();
          setTimeout(function() { openProposalModal(d.id, d.name || ''); }, 300);
        };
      })
      .catch(function() { body.innerHTML = '<p class="text-danger small">Failed to load learner details.</p>'; });
  }

  document.getElementById('propose-booking-btn').addEventListener('click', function() {
    openProposalModal('', '');
  });

  load();
})();

function openProposalModal(preselectedLearnerId, preselectedLearnerName) {
  var modal = new bootstrap.Modal(document.getElementById('proposal-modal'));
  var learnerSelect = document.getElementById('proposal-learner-select');
  var cardsContainer = document.getElementById('proposal-cards-container');
  var formContainer = document.getElementById('proposal-form-container');
  var addBookingBtn = document.getElementById('proposal-add-booking-btn');
  var sendBtn = document.getElementById('proposal-send-btn');
  proposalItems = [];
  instructorProfileId = null;
  instructorSuburbs = [];

  learnerSelect.innerHTML = '<option value="">Select a learner</option>';
  cardsContainer.innerHTML = '';
  formContainer.style.display = 'none';
  sendBtn.style.display = 'none';

  var today = new Date().toISOString().slice(0, 10);
  document.getElementById('proposal-date').setAttribute('min', today);

  fetch('/api/instructor/learners', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      var list = res.data || res || [];
      proposalLearnersList = list;
      list.forEach(function(row) {
        var learner = row.learner || {};
        var opt = document.createElement('option');
        opt.value = learner.id;
        opt.textContent = learner.name || 'Learner #' + learner.id;
        if (String(learner.id) === String(preselectedLearnerId)) opt.selected = true;
        learnerSelect.appendChild(opt);
      });
      onProposalLearnerChange();
    });

  fetch('/api/instructor/profile', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      var p = res.data || res;
      instructorProfileId = p.id;
      instructorSuburbs = p.service_areas || [];
      var suburbSelect = document.getElementById('proposal-suburb');
      suburbSelect.innerHTML = '<option value="">Select suburb</option>';
      instructorSuburbs.forEach(function(s) {
        var opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = (s.name || '') + ' ' + (s.postcode || '') + ' ' + (s.state || '');
        suburbSelect.appendChild(opt);
      });
    });

  modal.show();
}

var proposalItems = [];
var instructorProfileId = null;
var instructorSuburbs = [];
var proposalLearnersList = [];

function onProposalLearnerChange() {
  var learnerSelect = document.getElementById('proposal-learner-select');
  var card = document.getElementById('proposal-selected-learner-card');
  var formContainer = document.getElementById('proposal-form-container');
  var nameEl = document.getElementById('proposal-selected-learner-name');
  var addressEl = document.getElementById('proposal-selected-learner-address');
  var val = learnerSelect ? learnerSelect.value : '';
  if (!val) {
    if (card) card.style.display = 'none';
    if (formContainer) formContainer.style.display = 'none';
    return;
  }
  var opt = learnerSelect.selectedOptions[0];
  var learnerName = opt ? opt.textContent : 'Learner';
  if (nameEl) nameEl.textContent = learnerName;
  if (addressEl) addressEl.textContent = '—';
  if (card) card.style.display = 'block';
  if (formContainer) formContainer.style.display = 'block';
  document.getElementById('proposal-time').innerHTML = '<option value="">Select time</option>';
}

document.getElementById('proposal-learner-select').addEventListener('change', onProposalLearnerChange);

document.getElementById('proposal-selected-learner-remove').addEventListener('click', function() {
  document.getElementById('proposal-learner-select').value = '';
  onProposalLearnerChange();
});

document.getElementById('proposal-selected-learner-edit').addEventListener('click', function() {
  document.getElementById('proposal-form-container').scrollIntoView({ behavior: 'smooth' });
});

document.getElementById('proposal-suburb').addEventListener('change', function() {
  var opt = this.selectedOptions[0];
  var addressEl = document.getElementById('proposal-selected-learner-address');
  if (addressEl) addressEl.textContent = opt ? opt.textContent.trim() || '—' : '—';
});

document.getElementById('proposal-add-booking-btn').addEventListener('click', function() {
  var learnerSelect = document.getElementById('proposal-learner-select');
  if (!learnerSelect.value) {
    alert('Please select a learner first.');
    return;
  }
  document.getElementById('proposal-form-container').style.display = 'block';
  document.getElementById('proposal-time').innerHTML = '<option value="">Select time</option>';
});

document.getElementById('proposal-form-cancel').addEventListener('click', function() {
  document.getElementById('proposal-form-container').style.display = 'none';
});

document.getElementById('proposal-date').addEventListener('change', function() {
  var date = this.value;
  var timeSelect = document.getElementById('proposal-time');
  timeSelect.innerHTML = '<option value="">Loading…</option>';
  if (!date || !instructorProfileId) { timeSelect.innerHTML = '<option value="">Select date first</option>'; return; }
  fetch('/api/instructors/' + instructorProfileId + '/availability/slots?date=' + encodeURIComponent(date), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      var slots = res.data || res || [];
      timeSelect.innerHTML = '<option value="">Select time</option>';
      slots.forEach(function(s) {
        var opt = document.createElement('option');
        opt.value = (s.datetime || s.time || '');
        opt.textContent = s.time || s.datetime || '';
        timeSelect.appendChild(opt);
      });
    })
    .catch(function() { timeSelect.innerHTML = '<option value="">No slots</option>'; });
});

document.getElementById('proposal-form-review').addEventListener('click', function() {
  var learnerId = document.getElementById('proposal-learner-select').value;
  var learnerOpt = document.getElementById('proposal-learner-select').selectedOptions[0];
  var learnerName = learnerOpt ? learnerOpt.textContent : 'Learner';
  var type = document.getElementById('proposal-type').value;
  var date = document.getElementById('proposal-date').value;
  var timeVal = document.getElementById('proposal-time').value;
  var suburbId = document.getElementById('proposal-suburb').value;
  var suburbOpt = document.getElementById('proposal-suburb').selectedOptions[0];
  var suburbLabel = suburbOpt ? suburbOpt.textContent : '—';
  var transmission = document.getElementById('proposal-transmission').value;
  if (!date || !timeVal) {
    alert('Please select date and time.');
    return;
  }
  var scheduledAt = (timeVal.indexOf(' ') >= 0) ? timeVal : (date + ' ' + (timeVal.length <= 5 ? timeVal + ':00' : timeVal));
  proposalItems.push({
    learner_id: parseInt(learnerId, 10),
    learner_name: learnerName,
    suburb_id: suburbId ? parseInt(suburbId, 10) : null,
    suburb_label: suburbLabel,
    type: type,
    transmission: transmission,
    scheduled_at: scheduledAt,
    duration_minutes: document.getElementById('proposal-type').value === 'test_package' ? 120 : 60
  });
  renderProposalCards();
  document.getElementById('proposal-form-container').style.display = 'none';
  document.getElementById('proposal-date').value = '';
  document.getElementById('proposal-time').innerHTML = '<option value="">Select time</option>';
  document.getElementById('proposal-send-btn').style.display = 'inline-block';
});

function renderProposalCards() {
  var container = document.getElementById('proposal-cards-container');
  container.innerHTML = proposalItems.map(function(item, idx) {
    var dateStr = item.scheduled_at ? new Date(item.scheduled_at).toLocaleString(undefined, { weekday: 'long', day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' }) : '';
    return '<div class="proposal-card" data-idx="' + idx + '">' +
      '<div><i class="bi bi-person me-1"></i><strong>' + (item.learner_name || '').replace(/</g, '&lt;') + '</strong> <span class="badge badge-draft ms-1">DRAFT</span><br>' +
      '<i class="bi bi-calendar3 me-1"></i> ' + dateStr + '<br>' +
      '<i class="bi bi-geo-alt me-1"></i> ' + (item.suburb_label || '—').replace(/</g, '&lt;') + '</div>' +
      '<div><button type="button" class="btn btn-sm btn-link p-0 me-1 proposal-edit" data-idx="' + idx + '" title="Edit"><i class="bi bi-pencil"></i></button>' +
      '<button type="button" class="btn btn-sm btn-link p-0 text-danger proposal-delete" data-idx="' + idx + '" title="Delete"><i class="bi bi-trash"></i></button></div></div>';
  }).join('');
  container.querySelectorAll('.proposal-edit').forEach(function(btn) {
    btn.addEventListener('click', function() { editProposalItem(parseInt(btn.getAttribute('data-idx'), 10)); });
  });
  container.querySelectorAll('.proposal-delete').forEach(function(btn) {
    btn.addEventListener('click', function() {
      proposalItems.splice(parseInt(btn.getAttribute('data-idx'), 10), 1);
      renderProposalCards();
      if (proposalItems.length === 0) document.getElementById('proposal-send-btn').style.display = 'none';
    });
  });
}

function editProposalItem(idx) {
  var item = proposalItems[idx];
  if (!item) return;
  document.getElementById('proposal-learner-select').value = item.learner_id;
  document.getElementById('proposal-type').value = item.type;
  document.getElementById('proposal-transmission').value = item.transmission;
  document.getElementById('proposal-suburb').value = item.suburb_id || '';
  var d = item.scheduled_at ? new Date(item.scheduled_at) : null;
  document.getElementById('proposal-date').value = d ? d.toISOString().slice(0, 10) : '';
  proposalItems.splice(idx, 1);
  renderProposalCards();
  document.getElementById('proposal-form-container').style.display = 'block';
  if (d) document.getElementById('proposal-date').dispatchEvent(new Event('change'));
  if (proposalItems.length === 0) document.getElementById('proposal-send-btn').style.display = 'none';
}

document.getElementById('proposal-send-btn').addEventListener('click', function() {
  if (proposalItems.length === 0) return;
  var proposals = proposalItems.map(function(item) {
    return {
      learner_id: item.learner_id,
      suburb_id: item.suburb_id,
      type: item.type,
      transmission: item.transmission,
      scheduled_at: item.scheduled_at,
      duration_minutes: item.duration_minutes || 60
    };
  });
  var csrf = document.querySelector('meta[name="csrf-token"]');
  var token = csrf ? csrf.getAttribute('content') : '';
  fetch('/api/instructor/booking-proposals', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
    credentials: 'same-origin',
    body: JSON.stringify({ proposals: proposals })
  })
  .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
  .then(function(result) {
    if (result.ok) {
      bootstrap.Modal.getInstance(document.getElementById('proposal-modal')).hide();
      window.dispatchEvent(new CustomEvent('learners-refresh'));
    } else {
      alert(result.data.message || 'Failed to send proposals.');
    }
  })
  .catch(function() { alert('Failed to send proposals.'); });
});

// ——— Invite Learner ———
function openInviteModal() {
  document.getElementById('invite-email').value = '';
  document.getElementById('invite-name').value = '';
  document.getElementById('invite-message').value = '';
  document.getElementById('invite-result').style.display = 'none';
  document.getElementById('invite-send-btn').disabled = false;
  document.getElementById('invite-send-btn').innerHTML = '<i class="bi bi-send me-1"></i>Send Invitation';
  var modal = new bootstrap.Modal(document.getElementById('invite-learner-modal'));
  modal.show();
}

document.getElementById('invite-learner-btn').addEventListener('click', openInviteModal);
document.getElementById('proposal-add-new-learner').addEventListener('click', openInviteModal);

document.getElementById('invite-send-btn').addEventListener('click', function() {
  var email = document.getElementById('invite-email').value.trim();
  if (!email) { alert('Please enter an email address.'); return; }
  var btn = this;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';
  var csrf = document.querySelector('meta[name="csrf-token"]');
  fetch('/api/instructor/learners/invite', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf ? csrf.content : '', 'X-Requested-With': 'XMLHttpRequest' },
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
      // Auto-close modal after 1.5s + switch to Pending Invites tab
      setTimeout(function() {
        bootstrap.Modal.getInstance(document.getElementById('invite-learner-modal'))?.hide();
        var pendingTab = document.getElementById('tab-pending');
        if (pendingTab) bootstrap.Tab.getOrCreateInstance(pendingTab).show();
      }, 1500);
    } else {
      resultEl.className = 'alert alert-danger small';
      resultEl.textContent = result.data.message || 'Failed to send invitation.';
      resultEl.style.display = 'block';
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-send me-1"></i>Send Invitation';
    }
  })
  .catch(function() { btn.disabled = false; btn.innerHTML = '<i class="bi bi-send me-1"></i>Send Invitation'; alert('Failed to send invitation.'); });
});

// Open proposal modal when arriving from dashboard "Propose Booking" link
if (window.location.search.indexOf('open=propose') !== -1) {
  setTimeout(function() { openProposalModal('', ''); }, 400);
}
</script>
@endpush
@endsection
