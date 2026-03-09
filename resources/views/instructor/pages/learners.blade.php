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
    <h5 class="mb-0">Learners</h5>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary" id="invite-learner-btn" title="Coming soon">
            <i class="bi bi-person-plus me-1"></i> Invite Learner
        </button>
        <button type="button" class="btn btn-warning" id="propose-booking-btn" data-learner-id="" data-learner-name="">
            <i class="bi bi-car-front me-1"></i> Propose Booking
        </button>
    </div>
</div>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-my-learners" data-bs-toggle="tab" data-bs-target="#my-learners" type="button">My Learners</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-pending" data-bs-toggle="tab" data-bs-target="#pending-invites" type="button">Pending Invites</button>
    </li>
</ul>

<div class="mb-3">
    <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control" id="learners-search" placeholder="Search by learner name or phone number">
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div id="learners-loading" class="p-4 text-muted text-center">Loading…</div>
        <div id="learners-table-wrap" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Learner Details</th>
                            <th>Guardian Details</th>
                            <th>Booking Hours Completed</th>
                            <th>Upcoming Bookings</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="learners-tbody"></tbody>
                </table>
            </div>
        </div>
        <div id="learners-empty" class="p-4 text-muted text-center" style="display: none;">No learners yet. Bookings will appear here once learners book with you.</div>
    </div>
</div>

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
                        <button type="button" class="btn btn-outline-secondary" id="proposal-add-new-learner" title="Coming soon">+ Add New</button>
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
                <span class="text-muted small">EzLicense</span>
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
  var loadingEl = document.getElementById('learners-loading');
  var tableWrap = document.getElementById('learners-table-wrap');
  var tbody = document.getElementById('learners-tbody');
  var emptyEl = document.getElementById('learners-empty');
  var cache = [];

  function escapeHtml(s) {
    if (s == null || s === '') return '—';
    var div = document.createElement('div');
    div.textContent = s;
    return div.innerHTML;
  }

  function render(list) {
    if (!list || list.length === 0) {
      tableWrap.style.display = 'none';
      emptyEl.style.display = 'block';
      return;
    }
    emptyEl.style.display = 'none';
    tableWrap.style.display = 'block';
    tbody.innerHTML = list.map(function(row) {
      var learner = row.learner || {};
      var name = escapeHtml(learner.name || '—');
      var phone = escapeHtml(learner.phone || learner.email || '—');
      var guardian = row.guardian && row.guardian.name ? escapeHtml(row.guardian.name) : '—';
      var hours = row.hours_completed != null ? row.hours_completed : 0;
      var upcoming = row.upcoming_bookings != null ? row.upcoming_bookings : 0;
      var learnerId = learner.id;
      return '<tr>' +
        '<td><span class="learner-name-dot"></span><strong>' + name + '</strong><br><small class="text-muted">' + phone + '</small></td>' +
        '<td>' + guardian + '</td>' +
        '<td>' + hours + '</td>' +
        '<td>' + upcoming + '</td>' +
        '<td class="text-end">' +
          '<a href="#" class="learner-details-link me-2" data-learner-id="' + learnerId + '" data-learner-name="' + (learner.name || '').replace(/"/g, '&quot;') + '">Learner Details</a>' +
          '<button type="button" class="btn btn-sm btn-outline-secondary propose-booking-row" data-learner-id="' + learnerId + '" data-learner-name="' + (learner.name || '').replace(/"/g, '&quot;') + '">Propose Booking</button>' +
        '</td></tr>';
    }).join('');
  }

  function load(q) {
    loadingEl.style.display = 'block';
    tableWrap.style.display = 'none';
    emptyEl.style.display = 'none';
    var url = '/api/instructor/learners';
    if (q) url += '?q=' + encodeURIComponent(q);
    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        loadingEl.style.display = 'none';
        var data = res.data || res || [];
        cache = Array.isArray(data) ? data : [];
        render(cache);
      })
      .catch(function() {
        loadingEl.style.display = 'none';
        emptyEl.textContent = 'Could not load learners.';
        emptyEl.style.display = 'block';
      });
  }

  var searchTimeout;
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() { load(searchInput.value.trim()); }, 300);
    });
  }

  document.getElementById('tab-pending').addEventListener('shown.bs.tab', function() {
    emptyEl.textContent = 'No pending invites.';
    tableWrap.style.display = 'none';
    tbody.innerHTML = '';
    emptyEl.style.display = 'block';
  });
  document.getElementById('tab-my-learners').addEventListener('shown.bs.tab', function() {
    load(searchInput ? searchInput.value.trim() : '');
  });
  window.addEventListener('learners-refresh', function() {
    load(searchInput ? searchInput.value.trim() : '');
  });

  tbody.addEventListener('click', function(e) {
    if (e.target.classList.contains('learner-details-link')) {
      e.preventDefault();
      var name = e.target.getAttribute('data-learner-name') || 'Learner';
      var id = e.target.getAttribute('data-learner-id');
      alert('Learner: ' + name + ' (ID: ' + id + '). Learner detail page can be added here.');
    }
    if (e.target.closest && e.target.closest('.propose-booking-row')) {
      e.preventDefault();
      var btn = e.target.closest('.propose-booking-row');
      openProposalModal(btn.getAttribute('data-learner-id'), btn.getAttribute('data-learner-name') || '');
    }
  });

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

// Open proposal modal when arriving from dashboard "Propose Booking" link
if (window.location.search.indexOf('open=propose') !== -1) {
  setTimeout(function() { openProposalModal('', ''); }, 400);
}
</script>
@endpush
@endsection
