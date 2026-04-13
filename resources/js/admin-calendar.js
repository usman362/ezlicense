/**
 * Admin Calendar: Day / Week / Month views for ALL bookings across ALL instructors.
 * Self-contained (uses fetch, no imports).
 */
(function () {
  'use strict';

  /* ── constants ─────────────────────────────────────────────── */
  var MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  var DAY_SHORT   = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
  var ROW_H       = 48;
  var H_START     = 6;   // 6 am
  var H_END       = 21;  // 9 pm

  var STATUS_COLOURS = {
    pending:            { bg:'#fff3cd', fg:'#856404', border:'#ffc107' },
    proposed:           { bg:'#fff3cd', fg:'#856404', border:'#ffc107' },
    confirmed:          { bg:'#d4edda', fg:'#155724', border:'#28a745' },
    instructor_arrived: { bg:'#cce5ff', fg:'#004085', border:'#007bff' },
    in_progress:        { bg:'#e8daef', fg:'#4a235a', border:'#8e44ad' },
    completed:          { bg:'#e2e3e5', fg:'#383d41', border:'#6c757d' },
    cancelled:          { bg:'#f8d7da', fg:'#721c24', border:'#dc3545' },
    no_show:            { bg:'#f8d7da', fg:'#721c24', border:'#dc3545' }
  };

  var STATUS_LABELS = {
    pending:'Pending', proposed:'Proposed', confirmed:'Confirmed',
    instructor_arrived:'Arrived', in_progress:'In Progress',
    completed:'Completed', cancelled:'Cancelled', no_show:'No Show'
  };

  /* ── state ─────────────────────────────────────────────────── */
  var allBookings   = [];
  var currentView   = 'week';
  var viewDate      = new Date();
  var calYear       = viewDate.getFullYear();
  var calMonth      = viewDate.getMonth();
  var filterInstructor = '';
  var filterStatus     = '';
  var instructorMap    = {};   // id -> name
  var bsModal          = null; // Bootstrap modal instance

  /* ── helpers ────────────────────────────────────────────────── */
  function pad(n) { return String(n).padStart(2, '0'); }

  function dateKey(d) {
    return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
  }

  function esc(s) {
    if (s == null) return '';
    var el = document.createElement('span');
    el.textContent = s;
    return el.innerHTML;
  }

  function fmtTime(iso) {
    var d = new Date(iso);
    var h = d.getHours(), m = d.getMinutes();
    var ampm = h >= 12 ? 'pm' : 'am';
    h = h % 12 || 12;
    return h + ':' + pad(m) + ampm;
  }

  function hourLabel(h) { return h === 12 ? '12pm' : h > 12 ? (h - 12) + 'pm' : h + 'am'; }

  function getCsrf() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }

  function fetchJson(url) {
    return fetch(url, {
      headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
      credentials: 'same-origin'
    }).then(function (r) { return r.json(); });
  }

  function postJson(url, body) {
    return fetch(url, {
      method: 'PATCH',
      headers: {
        'Accept':'application/json',
        'Content-Type':'application/json',
        'X-Requested-With':'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrf()
      },
      credentials: 'same-origin',
      body: JSON.stringify(body)
    }).then(function (r) { return r.json(); });
  }

  function weekStart(d) {
    var dt = new Date(d.getTime());
    var day = dt.getDay();
    dt.setDate(dt.getDate() - day + (day === 0 ? -6 : 1)); // Monday
    return dt;
  }

  /* ── filtering ─────────────────────────────────────────────── */
  function filtered() {
    return allBookings.filter(function (b) {
      if (filterInstructor && (!b.instructor || String(b.instructor.id) !== filterInstructor)) return false;
      if (filterStatus && b.status !== filterStatus) return false;
      return true;
    });
  }

  function bookingsByDate(list) {
    var m = {};
    (list || []).forEach(function (b) {
      if (!b.scheduled_at) return;
      var k = dateKey(new Date(b.scheduled_at));
      if (!m[k]) m[k] = [];
      m[k].push(b);
    });
    return m;
  }

  /* ── stats ──────────────────────────────────────────────────── */
  function updateStats() {
    var now = new Date();
    var todayK = dateKey(now);
    var ws = weekStart(now);
    var todayCount = 0, weekCount = 0, pendingCount = 0;
    allBookings.forEach(function (b) {
      if (!b.scheduled_at) return;
      var d = new Date(b.scheduled_at);
      var k = dateKey(d);
      if (k === todayK && b.status !== 'cancelled') todayCount++;
      // same week check (Mon-Sun)
      var bWs = weekStart(d);
      if (dateKey(bWs) === dateKey(ws) && b.status !== 'cancelled') weekCount++;
      if (b.status === 'pending' || b.status === 'proposed') pendingCount++;
    });
    var el1 = document.getElementById('ac-stat-today');
    var el2 = document.getElementById('ac-stat-week');
    var el3 = document.getElementById('ac-stat-pending');
    if (el1) el1.textContent = todayCount;
    if (el2) el2.textContent = weekCount;
    if (el3) el3.textContent = pendingCount;
  }

  /* ── populate instructor dropdown ──────────────────────────── */
  function populateInstructorDropdown() {
    instructorMap = {};
    allBookings.forEach(function (b) {
      if (b.instructor && b.instructor.id) {
        instructorMap[b.instructor.id] = b.instructor.name || ('Instructor #' + b.instructor.id);
      }
    });
    var sel = document.getElementById('ac-filter-instructor');
    if (!sel) return;
    var current = sel.value;
    var html = '<option value="">All Instructors</option>';
    var sorted = Object.keys(instructorMap).sort(function (a, b) {
      return (instructorMap[a] || '').localeCompare(instructorMap[b] || '');
    });
    sorted.forEach(function (id) {
      html += '<option value="' + id + '">' + esc(instructorMap[id]) + '</option>';
    });
    sel.innerHTML = html;
    sel.value = current;
  }

  /* ── event colour helpers ──────────────────────────────────── */
  function evtStyle(status, top, height) {
    var c = STATUS_COLOURS[status] || STATUS_COLOURS.confirmed;
    return 'position:absolute;left:2px;right:2px;top:' + top + 'px;height:' + height + 'px;' +
      'background:' + c.bg + ';color:' + c.fg + ';border-left:3px solid ' + c.border + ';' +
      'border-radius:4px;padding:2px 5px;overflow:hidden;font-size:0.67rem;cursor:pointer;line-height:1.2;z-index:1;';
  }

  function statusBadge(status) {
    var c = STATUS_COLOURS[status] || STATUS_COLOURS.confirmed;
    return '<span style="display:inline-block;font-size:0.7rem;padding:1px 6px;border-radius:4px;background:' + c.bg + ';color:' + c.fg + ';border:1px solid ' + c.border + '">' + esc(STATUS_LABELS[status] || status) + '</span>';
  }

  /* ── WEEK VIEW ─────────────────────────────────────────────── */
  function renderWeek() {
    var grid = document.getElementById('ac-week-grid');
    if (!grid) return;
    var ws = weekStart(viewDate);
    var days = [];
    for (var i = 0; i < 7; i++) { var d = new Date(ws.getTime()); d.setDate(ws.getDate() + i); days.push(d); }
    var todayK = dateKey(new Date());
    var rows = H_END - H_START;

    grid.style.display = 'grid';
    grid.style.gridTemplateColumns = '56px repeat(7,1fr)';
    grid.style.gridTemplateRows = 'auto repeat(' + rows + ',' + ROW_H + 'px)';

    var html = '<div class="wk-time" style="grid-row:1"></div>';
    days.forEach(function (d, col) {
      var k = dateKey(d);
      html += '<div class="wk-hdr ' + (k === todayK ? 'today' : '') + '" style="grid-column:' + (col + 2) + '">' + DAY_SHORT[d.getDay()] + ' ' + d.getDate() + '</div>';
    });
    for (var h = H_START; h < H_END; h++) {
      var row = h - H_START + 2;
      html += '<div class="wk-time" style="grid-row:' + row + '">' + hourLabel(h) + '</div>';
      days.forEach(function (d, col) {
        html += '<div class="wk-cell" data-date="' + dateKey(d) + '" data-hour="' + h + '" style="grid-column:' + (col + 2) + ';grid-row:' + row + '"></div>';
      });
    }
    grid.innerHTML = html;

    // place bookings
    var list = filtered();
    list.forEach(function (b) {
      if (!b.scheduled_at) return;
      var start = new Date(b.scheduled_at);
      var dur = b.duration_minutes || 60;
      var dk = dateKey(start);
      var dayIdx = -1;
      for (var i = 0; i < days.length; i++) { if (dateKey(days[i]) === dk) { dayIdx = i; break; } }
      if (dayIdx === -1) return;
      var sh = start.getHours() + start.getMinutes() / 60;
      if (sh < H_START || sh >= H_END) return;
      var minOff = (start.getMinutes() / 60) * ROW_H;
      var height = Math.max(22, (dur / 60) * ROW_H - 2);
      var cell = grid.querySelector('.wk-cell[data-date="' + dk + '"][data-hour="' + Math.floor(sh) + '"]');
      if (!cell) return;
      cell.style.position = 'relative';
      var el = document.createElement('div');
      el.className = 'wk-evt ac-status-' + b.status;
      el.style.cssText = evtStyle(b.status, minOff, height);
      el.setAttribute('data-booking-id', b.id);
      var instrName = b.instructor ? b.instructor.name : '';
      var learnerName = b.learner ? b.learner.name : '';
      el.innerHTML = '<strong>' + esc(learnerName) + '</strong><br><small>' + esc(instrName) + '</small>';
      el.addEventListener('click', function () { showBookingModal(b); });
      cell.appendChild(el);
    });

    // labels
    var lbl = document.getElementById('ac-range-label');
    var brc = document.getElementById('ac-breadcrumb-range');
    if (lbl) lbl.textContent = days[0].getDate() + ' - ' + days[6].getDate() + ' ' + MONTH_NAMES[days[0].getMonth()] + ' ' + days[0].getFullYear();
    if (brc) brc.textContent = MONTH_NAMES[ws.getMonth()] + ' ' + ws.getFullYear();
  }

  /* ── DAY VIEW ──────────────────────────────────────────────── */
  function renderDay() {
    var container = document.getElementById('ac-day-grid');
    if (!container) return;
    var dk = dateKey(viewDate);
    var byDate = bookingsByDate(filtered());
    var dayList = byDate[dk] || [];
    var html = '';
    for (var h = H_START; h < H_END; h++) {
      html += '<div class="dy-row"><div class="dy-label">' + hourLabel(h) + '</div><div class="dy-slot" data-hour="' + h + '" style="position:relative;min-height:' + ROW_H + 'px"></div></div>';
    }
    container.innerHTML = html;
    dayList.forEach(function (b) {
      var start = new Date(b.scheduled_at);
      var dur = b.duration_minutes || 60;
      var sh = start.getHours() + start.getMinutes() / 60;
      if (sh < H_START || sh >= H_END) return;
      var rowIdx = Math.floor(sh - H_START);
      var slots = container.querySelectorAll('.dy-slot');
      var slot = slots[rowIdx];
      if (!slot) return;
      var minOff = (start.getMinutes() / 60) * ROW_H;
      var height = Math.max(22, (dur / 60) * ROW_H - 4);
      var el = document.createElement('div');
      el.className = 'wk-evt';
      el.style.cssText = evtStyle(b.status, minOff, height);
      el.setAttribute('data-booking-id', b.id);
      var instrName = b.instructor ? b.instructor.name : '';
      var learnerName = b.learner ? b.learner.name : '';
      el.innerHTML = '<strong>' + esc(learnerName) + '</strong> <small>(' + esc(instrName) + ')</small>';
      el.addEventListener('click', function () { showBookingModal(b); });
      slot.appendChild(el);
    });
    var lbl = document.getElementById('ac-range-label');
    var brc = document.getElementById('ac-breadcrumb-range');
    if (lbl) lbl.textContent = viewDate.toLocaleDateString(undefined, { weekday:'long', day:'numeric', month:'long', year:'numeric' });
    if (brc) brc.textContent = MONTH_NAMES[viewDate.getMonth()] + ' ' + viewDate.getFullYear();
  }

  /* ── MONTH VIEW ────────────────────────────────────────────── */
  function renderMonth() {
    var lbl  = document.getElementById('ac-range-label');
    var wdEl = document.getElementById('ac-month-weekdays');
    var grid = document.getElementById('ac-month-grid');
    if (!lbl || !wdEl || !grid) return;

    var first = new Date(calYear, calMonth, 1);
    var last  = new Date(calYear, calMonth + 1, 0);
    var startPad   = first.getDay();
    var daysInMonth = last.getDate();
    var total = startPad + daysInMonth;
    var rem = total % 7;
    if (rem) total += 7 - rem;
    var prevM = calMonth === 0 ? 11 : calMonth - 1;
    var prevY = calMonth === 0 ? calYear - 1 : calYear;
    var prevLast = new Date(prevY, prevM + 1, 0).getDate();

    var todayK = dateKey(new Date());
    var byDate = bookingsByDate(filtered());

    lbl.textContent = MONTH_NAMES[calMonth] + ' ' + calYear;
    var brc = document.getElementById('ac-breadcrumb-range');
    if (brc) brc.textContent = MONTH_NAMES[calMonth] + ' ' + calYear;
    wdEl.innerHTML = DAY_SHORT.map(function (d) { return '<div>' + d + '</div>'; }).join('');

    var html = '';
    for (var i = 0; i < total; i++) {
      var dk, dayNum, isOther = false;
      if (i < startPad) {
        dayNum = prevLast - startPad + i + 1;
        dk = prevY + '-' + pad(prevM + 1) + '-' + pad(dayNum);
        isOther = true;
      } else if (i < startPad + daysInMonth) {
        dayNum = i - startPad + 1;
        dk = calYear + '-' + pad(calMonth + 1) + '-' + pad(dayNum);
      } else {
        dayNum = i - startPad - daysInMonth + 1;
        var nM = calMonth === 11 ? 0 : calMonth + 1;
        var nY = calMonth === 11 ? calYear + 1 : calYear;
        dk = nY + '-' + pad(nM + 1) + '-' + pad(dayNum);
        isOther = true;
      }
      var dayBookings = byDate[dk] || [];
      var cls = 'ac-month-day' + (isOther ? ' other-month' : '') + (dk === todayK ? ' today' : '');
      var badges = '';
      if (dayBookings.length > 0) {
        // group by status for count badges
        var counts = {};
        dayBookings.forEach(function (b) {
          counts[b.status] = (counts[b.status] || 0) + 1;
        });
        Object.keys(counts).forEach(function (st) {
          var c = STATUS_COLOURS[st] || STATUS_COLOURS.confirmed;
          badges += '<span class="ac-badge-count" style="background:' + c.bg + ';color:' + c.fg + '">' + counts[st] + ' ' + (STATUS_LABELS[st] || st) + '</span> ';
        });
      }
      html += '<div class="' + cls + '" data-date="' + dk + '"><span class="day-num">' + dayNum + '</span>' +
        (badges ? '<div style="margin-top:2px">' + badges + '</div>' : '') + '</div>';
    }
    grid.innerHTML = html;

    // click on month day -> switch to day view for that date
    grid.querySelectorAll('.ac-month-day').forEach(function (el) {
      el.addEventListener('click', function () {
        var dk = el.getAttribute('data-date');
        viewDate = new Date(dk + 'T12:00:00');
        calYear = viewDate.getFullYear();
        calMonth = viewDate.getMonth();
        currentView = 'day';
        document.getElementById('ac-view-day').checked = true;
        render();
      });
    });
  }

  /* ── render dispatcher ──────────────────────────────────────── */
  function render() {
    var wc = document.getElementById('ac-week-container');
    var dc = document.getElementById('ac-day-container');
    var mc = document.getElementById('ac-month-container');
    if (wc) wc.style.display = currentView === 'week'  ? 'block' : 'none';
    if (dc) dc.style.display = currentView === 'day'   ? 'block' : 'none';
    if (mc) mc.style.display = currentView === 'month'  ? 'block' : 'none';
    if (currentView === 'week')  renderWeek();
    else if (currentView === 'day') renderDay();
    else renderMonth();
  }

  /* ── booking detail modal ───────────────────────────────────── */
  function showBookingModal(b) {
    var body   = document.getElementById('ac-booking-modal-body');
    var footer = document.getElementById('ac-booking-modal-footer');
    if (!body || !footer) return;

    var time = b.scheduled_at ? fmtTime(b.scheduled_at) : '--';
    var date = b.scheduled_at ? new Date(b.scheduled_at).toLocaleDateString(undefined, { weekday:'short', day:'numeric', month:'short', year:'numeric' }) : '--';
    var learner = b.learner ? b.learner.name : '--';
    var learnerEmail = b.learner ? b.learner.email : '';
    var learnerPhone = b.learner ? b.learner.phone : '';
    var instructor = b.instructor ? b.instructor.name : '--';
    var location = b.suburb ? b.suburb.location : '--';
    var trans = b.transmission ? b.transmission.charAt(0).toUpperCase() + b.transmission.slice(1) : '--';
    var type = b.type ? b.type.replace(/_/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); }) : '--';
    var amount = '$' + (b.amount || 0).toFixed(2);
    var duration = (b.duration_minutes || 60) + ' min';

    body.innerHTML =
      '<table class="table table-sm table-borderless mb-3">' +
        '<tr><td>Status</td><td>' + statusBadge(b.status) + '</td></tr>' +
        '<tr><td>Date</td><td>' + esc(date) + '</td></tr>' +
        '<tr><td>Time</td><td>' + esc(time) + '</td></tr>' +
        '<tr><td>Duration</td><td>' + esc(duration) + '</td></tr>' +
        '<tr><td>Learner</td><td>' + esc(learner) + (learnerEmail ? '<br><small class="text-muted">' + esc(learnerEmail) + '</small>' : '') + (learnerPhone ? '<br><small class="text-muted">' + esc(learnerPhone) + '</small>' : '') + '</td></tr>' +
        '<tr><td>Instructor</td><td>' + esc(instructor) + '</td></tr>' +
        '<tr><td>Type</td><td>' + esc(type) + '</td></tr>' +
        '<tr><td>Transmission</td><td>' + esc(trans) + '</td></tr>' +
        '<tr><td>Location</td><td>' + esc(location) + '</td></tr>' +
        '<tr><td>Amount</td><td>' + amount + '</td></tr>' +
        (b.learner_notes ? '<tr><td>Notes</td><td>' + esc(b.learner_notes) + '</td></tr>' : '') +
        (b.cancellation_reason ? '<tr><td>Cancel Reason</td><td>' + esc(b.cancellation_reason) + '</td></tr>' : '') +
      '</table>';

    // action buttons
    var btns = '';
    if (b.status === 'pending' || b.status === 'proposed') {
      btns += '<button class="btn btn-sm btn-success ac-action-btn" data-booking="' + b.id + '" data-status="confirmed"><i class="bi bi-check-lg"></i> Approve</button> ';
      btns += '<button class="btn btn-sm btn-danger ac-action-btn" data-booking="' + b.id + '" data-status="cancelled"><i class="bi bi-x-lg"></i> Cancel</button> ';
    }
    if (b.status === 'confirmed' || b.status === 'instructor_arrived' || b.status === 'in_progress') {
      btns += '<button class="btn btn-sm btn-secondary ac-action-btn" data-booking="' + b.id + '" data-status="completed"><i class="bi bi-check-circle"></i> Mark Complete</button> ';
      if (b.status !== 'cancelled') {
        btns += '<button class="btn btn-sm btn-outline-danger ac-action-btn" data-booking="' + b.id + '" data-status="cancelled"><i class="bi bi-x-lg"></i> Cancel</button> ';
      }
    }
    btns += '<a href="/admin/bookings?search=' + b.id + '" class="btn btn-sm btn-outline-secondary"><i class="bi bi-table"></i> View in Table</a>';
    footer.innerHTML = btns;

    // bind action buttons
    footer.querySelectorAll('.ac-action-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var bookingId = btn.getAttribute('data-booking');
        var newStatus = btn.getAttribute('data-status');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        postJson('/admin/bookings/' + bookingId + '/update-status', { status: newStatus })
          .then(function () {
            // close modal and reload
            if (bsModal) bsModal.hide();
            loadCalendar();
          })
          .catch(function (err) {
            console.error('Status update failed', err);
            btn.disabled = false;
            btn.textContent = 'Error';
          });
      });
    });

    // show modal
    var modalEl = document.getElementById('ac-booking-modal');
    if (!bsModal) {
      bsModal = new bootstrap.Modal(modalEl);
    }
    bsModal.show();
  }

  /* ── data loading ───────────────────────────────────────────── */
  function getApiUrl() {
    var from = new Date(viewDate.getTime());
    var to   = new Date(viewDate.getTime());
    from.setDate(from.getDate() - 60);
    to.setDate(to.getDate() + 60);
    return '/api/admin/calendar/bookings?from=' + dateKey(from) + '&to=' + dateKey(to);
  }

  function loadCalendar() {
    var grid = document.getElementById('ac-week-grid');
    if (!grid) return;
    fetchJson(getApiUrl())
      .then(function (r) {
        var d = r && r.data !== undefined ? r.data : r;
        allBookings = Array.isArray(d) ? d : [];
        populateInstructorDropdown();
        updateStats();
        render();
      })
      .catch(function (err) {
        console.error('Admin calendar load error:', err);
        grid.innerHTML = '<p class="p-3 text-muted">Could not load calendar data.</p>';
      });
  }

  /* ── navigation ─────────────────────────────────────────────── */
  function navPrev() {
    if (currentView === 'week') viewDate.setDate(viewDate.getDate() - 7);
    else if (currentView === 'day') viewDate.setDate(viewDate.getDate() - 1);
    else {
      if (calMonth === 0) { calYear--; calMonth = 11; } else calMonth--;
      viewDate = new Date(calYear, calMonth, 1);
    }
    calYear = viewDate.getFullYear();
    calMonth = viewDate.getMonth();
    loadCalendar();
  }

  function navNext() {
    if (currentView === 'week') viewDate.setDate(viewDate.getDate() + 7);
    else if (currentView === 'day') viewDate.setDate(viewDate.getDate() + 1);
    else {
      if (calMonth === 11) { calYear++; calMonth = 0; } else calMonth++;
      viewDate = new Date(calYear, calMonth, 1);
    }
    calYear = viewDate.getFullYear();
    calMonth = viewDate.getMonth();
    loadCalendar();
  }

  function navToday() {
    viewDate = new Date();
    calYear = viewDate.getFullYear();
    calMonth = viewDate.getMonth();
    loadCalendar();
  }

  /* ── init ───────────────────────────────────────────────────── */
  function init() {
    if (!document.getElementById('ac-week-grid')) return;

    document.getElementById('ac-prev').addEventListener('click', navPrev);
    document.getElementById('ac-next').addEventListener('click', navNext);
    document.getElementById('ac-today').addEventListener('click', navToday);

    document.querySelectorAll('input[name="ac-view"]').forEach(function (radio) {
      radio.addEventListener('change', function () {
        currentView = radio.value;
        render();
      });
    });

    document.getElementById('ac-filter-instructor').addEventListener('change', function () {
      filterInstructor = this.value;
      render();
    });

    document.getElementById('ac-filter-status').addEventListener('change', function () {
      filterStatus = this.value;
      render();
    });

    loadCalendar();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
