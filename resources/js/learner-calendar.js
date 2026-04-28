/**
 * Learner calendar — shows learner's booked lessons in Day / Week / Month views.
 * Self-contained (uses fetch, no imports).
 */
(function () {
  var MONTH_NAMES = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  var DAY_NAMES_SHORT = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  var ROW_HEIGHT = 48;
  var HOURS_START = 6;
  var HOURS_END = 21;

  var STATUS_COLORS = {
    confirmed:          { bg: '#dcfce7', border: '#22c55e', text: '#166534', label: 'Confirmed' },
    instructor_arrived: { bg: '#dbeafe', border: '#3b82f6', text: '#1e40af', label: 'Instructor Arrived' },
    in_progress:        { bg: '#f3e8ff', border: '#a855f7', text: '#6b21a8', label: 'In Progress' },
    completed:          { bg: '#f3f4f6', border: '#9ca3af', text: '#374151', label: 'Completed' },
    proposed:           { bg: '#fef3c7', border: '#f59e0b', text: '#92400e', label: 'Proposed' },
    pending:            { bg: '#fefce8', border: '#eab308', text: '#854d0e', label: 'Pending' }
  };

  var bookings = [];
  var currentView = 'week';
  var viewDate = new Date();
  var calendarYear = viewDate.getFullYear();
  var calendarMonth = viewDate.getMonth();
  var activePopover = null;
  var elapsedTimerInterval = null;

  function getCsrf() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }

  function fetchJson(url) {
    return fetch(url, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    }).then(function (r) {
      if (!r.ok) throw new Error('HTTP ' + r.status);
      var ct = r.headers.get('content-type') || '';
      if (ct.indexOf('application/json') === -1) throw new Error('Expected JSON but got ' + ct);
      return r.json();
    });
  }

  function postJson(url, method, body) {
    return fetch(url, {
      method: method || 'PUT',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrf()
      },
      credentials: 'same-origin',
      body: JSON.stringify(body || {})
    }).then(function (r) {
      if (!r.ok) return r.json().then(function (d) { throw d; });
      return r.json();
    });
  }

  function esc(s) {
    if (s == null) return '';
    var d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
  }

  // ─────────────────────────────────────────────────────────────
  // Cancel Modal — properly sends cancellation_reason_code (REQUIRED)
  // ─────────────────────────────────────────────────────────────
  window.openLearnerCancelModal = function (booking, opts) {
    opts = opts || {};
    var modalEl = document.getElementById('learnerCancelModal');
    if (!modalEl) { alert('Cancel modal not found'); return; }

    document.getElementById('learner-cancel-booking-id').value = booking.id;
    var form = document.getElementById('learner-cancel-form');
    form.reset();
    document.getElementById('learner-cancel-error').classList.add('d-none');
    document.getElementById('learner-cancel-reason-other-wrap').style.display = 'none';

    // 24-hour cutoff warning
    var sched = new Date(booking.scheduled_at);
    var hoursUntil = (sched.getTime() - Date.now()) / 36e5;
    document.getElementById('learner-cancel-cutoff-warning').style.display = (hoursUntil < 24 ? 'block' : 'none');

    // For decline of proposed: pre-select "other" + sensible message
    if (opts.isDecline) {
      document.getElementById('learner-cancel-reason-code').value = 'other';
      document.getElementById('learner-cancel-reason-other-wrap').style.display = 'block';
      document.getElementById('learner-cancel-reason-text').value = 'Declined the proposed reschedule';
      document.querySelector('#learnerCancelModal .modal-title').innerHTML = '<i class="bi bi-x-circle text-danger me-2"></i>Decline Proposed Booking';
      document.getElementById('learner-cancel-submit').innerHTML = '<i class="bi bi-x-circle me-1"></i>Decline';
    } else {
      document.querySelector('#learnerCancelModal .modal-title').innerHTML = '<i class="bi bi-x-circle text-danger me-2"></i>Cancel Booking';
      document.getElementById('learner-cancel-submit').innerHTML = '<i class="bi bi-x-circle me-1"></i>Cancel Booking';
    }

    var modal = new bootstrap.Modal(modalEl);
    modal.show();
  };

  // Wire reason code → toggle "other" text field
  document.addEventListener('change', function (e) {
    if (e.target && e.target.id === 'learner-cancel-reason-code') {
      var wrap = document.getElementById('learner-cancel-reason-other-wrap');
      if (wrap) wrap.style.display = (e.target.value === 'other' ? 'block' : 'none');
    }
  });

  // Wire cancel form submission
  document.addEventListener('submit', function (e) {
    if (e.target && e.target.id === 'learner-cancel-form') {
      e.preventDefault();
      var bookingId = document.getElementById('learner-cancel-booking-id').value;
      var reasonCode = document.getElementById('learner-cancel-reason-code').value;
      var reasonText = document.getElementById('learner-cancel-reason-text').value.trim();
      var message = document.getElementById('learner-cancel-message').value.trim();
      var policy = document.getElementById('learner-cancel-policy').checked;
      var submitBtn = document.getElementById('learner-cancel-submit');
      var errorEl = document.getElementById('learner-cancel-error');

      if (!reasonCode || !policy) {
        errorEl.textContent = 'Please pick a reason and accept the cancellation policy.';
        errorEl.classList.remove('d-none');
        return;
      }

      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Cancelling...';

      postJson('/api/bookings/' + bookingId + '/cancel', 'PUT', {
        cancellation_reason_code: reasonCode,
        cancellation_reason: reasonText || null,
        cancellation_message: message || null,
        cancellation_policy_accepted: true
      })
      .then(function () {
        bootstrap.Modal.getInstance(document.getElementById('learnerCancelModal')).hide();
        if (typeof loadCalendar === 'function') loadCalendar();
        if (typeof window.__loadLearnerDashboard === 'function') window.__loadLearnerDashboard();
      })
      .catch(function (err) {
        var msg = (err && err.errors) ? Object.values(err.errors).flat().join(' ') : (err.message || 'Failed to cancel booking.');
        errorEl.textContent = msg;
        errorEl.classList.remove('d-none');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-x-circle me-1"></i>Cancel Booking';
      });
    }
  });

  // ─────────────────────────────────────────────────────────────
  // Reschedule Modal — date + time picker, posts to /reschedule
  // ─────────────────────────────────────────────────────────────
  window.openLearnerRescheduleModal = function (booking) {
    var modalEl = document.getElementById('learnerRescheduleModal');
    if (!modalEl) { alert('Reschedule modal not found'); return; }

    var form = document.getElementById('learner-reschedule-form');
    form.reset();
    document.getElementById('learner-reschedule-error').classList.add('d-none');
    document.getElementById('learner-reschedule-booking-id').value = booking.id;
    document.getElementById('learner-reschedule-instructor-profile-id').value = booking.instructor_profile_id || '';
    document.getElementById('learner-reschedule-time').innerHTML = '<option value="">Select a date first</option>';

    var modal = new bootstrap.Modal(modalEl);
    modal.show();
  };

  // Date change → load time slots
  document.addEventListener('change', function (e) {
    if (e.target && e.target.id === 'learner-reschedule-date') {
      var date = e.target.value;
      var profileId = document.getElementById('learner-reschedule-instructor-profile-id').value;
      var timeSelect = document.getElementById('learner-reschedule-time');
      if (!date || !profileId) return;

      timeSelect.innerHTML = '<option value="">Loading...</option>';
      fetch('/api/instructors/' + profileId + '/availability/slots?date=' + encodeURIComponent(date), {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        var slots = res.data || [];
        if (!slots.length) {
          timeSelect.innerHTML = '<option value="">No times available — try another date</option>';
          return;
        }
        timeSelect.innerHTML = '<option value="">Select time</option>' + slots.map(function (s) {
          var dt = s.datetime || (date + ' ' + s.time + ':00');
          var t = (dt && dt.length >= 16) ? dt.substr(11, 5) : (s.time || '');
          var parts = t.split(':');
          var h = parseInt(parts[0], 10);
          var am = h < 12;
          if (h === 0) h = 12; else if (h > 12) h -= 12;
          var label = h + ':' + parts[1] + (am ? ' am' : ' pm');
          return '<option value="' + dt + '">' + label + '</option>';
        }).join('');
      })
      .catch(function () {
        timeSelect.innerHTML = '<option value="">Could not load times</option>';
      });
    }
  });

  // Reschedule form submission
  document.addEventListener('submit', function (e) {
    if (e.target && e.target.id === 'learner-reschedule-form') {
      e.preventDefault();
      var bookingId = document.getElementById('learner-reschedule-booking-id').value;
      var newDateTime = document.getElementById('learner-reschedule-time').value;
      var reasonCode = document.getElementById('learner-reschedule-reason-code').value;
      var policy = document.getElementById('learner-reschedule-policy').checked;
      var submitBtn = document.getElementById('learner-reschedule-submit');
      var errorEl = document.getElementById('learner-reschedule-error');

      if (!newDateTime || !reasonCode || !policy) {
        errorEl.textContent = 'Please pick a new time, a reason, and accept the policy.';
        errorEl.classList.remove('d-none');
        return;
      }

      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Rescheduling...';

      postJson('/api/bookings/' + bookingId + '/reschedule', 'PUT', {
        scheduled_at: newDateTime,
        cancellation_reason_code: reasonCode,
        cancellation_policy_accepted: true
      })
      .then(function () {
        bootstrap.Modal.getInstance(document.getElementById('learnerRescheduleModal')).hide();
        if (typeof loadCalendar === 'function') loadCalendar();
        if (typeof window.__loadLearnerDashboard === 'function') window.__loadLearnerDashboard();
      })
      .catch(function (err) {
        var msg = (err && err.errors) ? Object.values(err.errors).flat().join(' ') : (err.message || 'Failed to reschedule booking.');
        errorEl.textContent = msg;
        errorEl.classList.remove('d-none');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Reschedule';
      });
    }
  });

  function pad(n) { return String(n).padStart(2, '0'); }

  function dateKey(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }

  function getWeekStart(d) {
    var date = new Date(d.getTime());
    var day = date.getDay();
    var diff = date.getDate() - day + (day === 0 ? -6 : 1);
    date.setDate(diff);
    return date;
  }

  function getBookingsByDate(list) {
    var byDate = {};
    (list || []).forEach(function (b) {
      if (!b.scheduled_at) return;
      var d = new Date(b.scheduled_at);
      var key = dateKey(d);
      if (!byDate[key]) byDate[key] = [];
      byDate[key].push(b);
    });
    return byDate;
  }

  function getStatusColor(status) {
    return STATUS_COLORS[status] || STATUS_COLORS.confirmed;
  }

  function avatarInitial(name) {
    if (!name) return '?';
    var parts = name.trim().split(/\s+/);
    return parts.length >= 2 ? (parts[0][0] + parts[1][0]).toUpperCase() : parts[0][0].toUpperCase();
  }

  function avatarColor(name) {
    if (!name) return '#6b7280';
    var hash = 0;
    for (var i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
    var colors = ['#ef4444','#f97316','#eab308','#22c55e','#14b8a6','#3b82f6','#8b5cf6','#ec4899'];
    return colors[Math.abs(hash) % colors.length];
  }

  function relativeTime(date) {
    var now = new Date();
    var diff = date.getTime() - now.getTime();
    if (diff < 0) return 'started';
    var minutes = Math.floor(diff / 60000);
    var hours = Math.floor(minutes / 60);
    var days = Math.floor(hours / 24);
    if (days > 1) return 'Starts in ' + days + ' days';
    if (days === 1) return 'Starts tomorrow';
    if (hours >= 1) return 'Starts in ' + hours + 'h ' + (minutes % 60) + 'm';
    if (minutes > 0) return 'Starts in ' + minutes + 'm';
    return 'Starting now';
  }

  function isToday(date) {
    var now = new Date();
    return date.getFullYear() === now.getFullYear() && date.getMonth() === now.getMonth() && date.getDate() === now.getDate();
  }

  function elapsedStr(startDate) {
    var now = new Date();
    var diff = now.getTime() - startDate.getTime();
    if (diff < 0) diff = 0;
    var mins = Math.floor(diff / 60000);
    var hrs = Math.floor(mins / 60);
    mins = mins % 60;
    return pad(hrs) + ':' + pad(mins);
  }

  // ==================== POPOVER ====================
  function closePopover() {
    if (activePopover) {
      activePopover.remove();
      activePopover = null;
    }
    if (elapsedTimerInterval) {
      clearInterval(elapsedTimerInterval);
      elapsedTimerInterval = null;
    }
  }

  function showBookingPopover(booking, anchorEl) {
    closePopover();
    var sc = getStatusColor(booking.status);
    var instrName = booking.instructor && booking.instructor.name ? booking.instructor.name : 'Instructor';
    var initial = avatarInitial(instrName);
    var color = avatarColor(instrName);
    var loc = booking.suburb ? ((booking.suburb.name || '') + ' ' + (booking.suburb.postcode || '')).trim() : '';
    var typeLabel = booking.type === 'test_package' ? 'Test Package' : (booking.type || 'Lesson');
    var duration = booking.duration_minutes || 60;
    var transmission = booking.transmission ? (booking.transmission.charAt(0).toUpperCase() + booking.transmission.slice(1)) : '';
    var amount = booking.amount != null ? '$' + Number(booking.amount).toFixed(2) : '';
    var startDate = new Date(booking.scheduled_at);
    var timeStr = startDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    var dateStr = startDate.toLocaleDateString(undefined, { weekday: 'short', day: 'numeric', month: 'short' });
    var isFuture = startDate > new Date();
    var hasReview = booking.review_id || booking.has_review;

    var popover = document.createElement('div');
    popover.className = 'lc-booking-popover';

    var html = '<div class="lc-popover-header" style="border-left:4px solid ' + sc.border + '; background:' + sc.bg + '">';
    html += '<button class="lc-popover-close" aria-label="Close">&times;</button>';
    html += '<div style="display:flex;align-items:center;gap:10px">';
    html += '<div class="lc-avatar-circle" style="background:' + color + '">' + esc(initial) + '</div>';
    html += '<div><div class="fw-semibold">' + esc(instrName) + '</div>';
    html += '<div class="text-muted" style="font-size:0.8rem">' + dateStr + ' at ' + timeStr + '</div></div>';
    html += '</div></div>';

    html += '<div class="lc-popover-body">';
    html += '<div class="lc-popover-detail-row"><i class="bi bi-book"></i> <span>' + esc(typeLabel) + '</span></div>';
    html += '<div class="lc-popover-detail-row"><i class="bi bi-clock"></i> <span>' + duration + ' minutes</span></div>';
    if (transmission) {
      html += '<div class="lc-popover-detail-row"><i class="bi bi-car-front"></i> <span>' + esc(transmission) + '</span></div>';
    }
    if (loc) {
      html += '<div class="lc-popover-detail-row"><i class="bi bi-geo-alt"></i> <span>' + esc(loc) + '</span></div>';
    }
    if (amount) {
      html += '<div class="lc-popover-detail-row"><i class="bi bi-wallet2"></i> <span>' + esc(amount) + '</span></div>';
    }
    html += '<div class="lc-popover-status" style="margin-top:8px"><span class="lc-status-badge" style="background:' + sc.bg + ';color:' + sc.text + ';border:1px solid ' + sc.border + '">' + esc(sc.label) + '</span></div>';

    // Status-specific content
    html += '<div class="lc-popover-actions">';

    if (booking.status === 'confirmed' && isFuture) {
      html += '<button class="btn btn-sm btn-outline-danger lc-action-cancel" data-id="' + booking.id + '"><i class="bi bi-x-circle me-1"></i>Cancel Booking</button>';
      html += '<button class="btn btn-sm btn-outline-primary lc-action-reschedule" data-id="' + booking.id + '" data-instructor-profile-id="' + (booking.instructor_profile_id || '') + '"><i class="bi bi-arrow-repeat me-1"></i>Reschedule</button>';
    } else if (booking.status === 'instructor_arrived') {
      html += '<div class="lc-status-message lc-status-arrived"><i class="bi bi-check-circle-fill"></i> Your instructor has arrived!</div>';
    } else if (booking.status === 'in_progress') {
      html += '<div class="lc-status-message lc-status-inprogress"><i class="bi bi-activity"></i> Lesson in progress... <span class="lc-elapsed-timer">' + elapsedStr(startDate) + '</span></div>';
    } else if (booking.status === 'completed' && !hasReview) {
      html += '<a href="/learner/reviews?booking=' + booking.id + '" class="btn btn-sm btn-outline-primary"><i class="bi bi-star me-1"></i>Leave a Review</a>';
    } else if (booking.status === 'proposed') {
      html += '<button class="btn btn-sm btn-success lc-action-accept" data-id="' + booking.id + '"><i class="bi bi-check-lg me-1"></i>Accept</button>';
      html += '<button class="btn btn-sm btn-outline-danger lc-action-decline" data-id="' + booking.id + '"><i class="bi bi-x-lg me-1"></i>Decline</button>';
    }

    html += '</div></div>';
    popover.innerHTML = html;

    // Position the popover near the anchor
    document.body.appendChild(popover);
    positionPopover(popover, anchorEl);
    activePopover = popover;

    // Start elapsed timer for in_progress
    if (booking.status === 'in_progress') {
      elapsedTimerInterval = setInterval(function () {
        var timerEl = popover.querySelector('.lc-elapsed-timer');
        if (timerEl) timerEl.textContent = elapsedStr(startDate);
      }, 1000);
    }

    // Event listeners
    popover.querySelector('.lc-popover-close').addEventListener('click', function (e) {
      e.stopPropagation();
      closePopover();
    });

    // ── Cancel button → open shared modal (proper required fields) ──
    var cancelBtn = popover.querySelector('.lc-action-cancel');
    if (cancelBtn) {
      cancelBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        closePopover();
        openLearnerCancelModal(booking);
      });
    }

    // ── Reschedule button → open reschedule modal ──
    var rescheduleBtn = popover.querySelector('.lc-action-reschedule');
    if (rescheduleBtn) {
      rescheduleBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        closePopover();
        openLearnerRescheduleModal(booking);
      });
    }

    // ── Accept proposed reschedule → uses dedicated /accept endpoint ──
    var acceptBtn = popover.querySelector('.lc-action-accept');
    if (acceptBtn) {
      acceptBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        acceptBtn.disabled = true;
        acceptBtn.textContent = 'Accepting...';
        postJson('/api/bookings/' + booking.id + '/accept', 'PUT', {})
          .then(function () { closePopover(); loadCalendar(); })
          .catch(function (err) {
            alert(err.message || 'Failed to accept booking.');
            acceptBtn.disabled = false;
            acceptBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Accept';
          });
      });
    }

    // ── Decline proposed booking → uses cancel modal with sensible defaults ──
    var declineBtn = popover.querySelector('.lc-action-decline');
    if (declineBtn) {
      declineBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        closePopover();
        openLearnerCancelModal(booking, { isDecline: true });
      });
    }

    // Click outside to close
    setTimeout(function () {
      document.addEventListener('click', onClickOutside);
    }, 10);
  }

  function positionPopover(popover, anchor) {
    var rect = anchor.getBoundingClientRect();
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

    // Temporarily show to measure
    popover.style.visibility = 'hidden';
    popover.style.display = 'block';
    var popH = popover.offsetHeight;
    var popW = popover.offsetWidth;

    var top, left;
    var spaceBelow = window.innerHeight - rect.bottom;
    var spaceAbove = rect.top;

    if (spaceBelow >= popH + 8 || spaceBelow >= spaceAbove) {
      top = rect.bottom + scrollTop + 6;
    } else {
      top = rect.top + scrollTop - popH - 6;
    }

    left = rect.left + scrollLeft + (rect.width / 2) - (popW / 2);
    if (left < 8) left = 8;
    if (left + popW > window.innerWidth - 8) left = window.innerWidth - popW - 8;

    popover.style.top = top + 'px';
    popover.style.left = left + 'px';
    popover.style.visibility = 'visible';
  }

  function onClickOutside(e) {
    if (activePopover && !activePopover.contains(e.target)) {
      closePopover();
      document.removeEventListener('click', onClickOutside);
    }
  }

  // ==================== TODAY INDICATOR ====================
  function renderTodayIndicator(gridEl, days, isWeekView) {
    var now = new Date();
    var todayK = dateKey(now);
    var currentHour = now.getHours() + now.getMinutes() / 60;
    if (currentHour < HOURS_START || currentHour >= HOURS_END) return;

    if (isWeekView) {
      var dayIndex = -1;
      for (var i = 0; i < days.length; i++) {
        if (dateKey(days[i]) === todayK) { dayIndex = i; break; }
      }
      if (dayIndex === -1) return;

      var hourRow = Math.floor(currentHour);
      var cell = gridEl.querySelector('.week-cell[data-date="' + todayK + '"][data-hour="' + hourRow + '"]');
      if (!cell) return;
      cell.style.position = 'relative';
      var minuteOffset = (now.getMinutes() / 60) * ROW_HEIGHT;
      var line = document.createElement('div');
      line.className = 'lc-today-line';
      line.style.cssText = 'position:absolute;left:0;right:0;top:' + minuteOffset + 'px;height:2px;background:#ef4444;z-index:5;pointer-events:none;';
      var dot = document.createElement('div');
      dot.style.cssText = 'position:absolute;left:-4px;top:-3px;width:8px;height:8px;border-radius:50%;background:#ef4444;';
      line.appendChild(dot);
      cell.appendChild(line);
    } else {
      // Day view
      var dayGrid = gridEl;
      if (dateKey(viewDate) !== todayK) return;
      var hourFloor = Math.floor(currentHour);
      var slot = dayGrid.querySelector('.day-time-slot[data-hour="' + hourFloor + '"]');
      if (!slot) return;
      slot.style.position = 'relative';
      var minuteOff = (now.getMinutes() / 60) * ROW_HEIGHT;
      var lineEl = document.createElement('div');
      lineEl.className = 'lc-today-line';
      lineEl.style.cssText = 'position:absolute;left:0;right:0;top:' + minuteOff + 'px;height:2px;background:#ef4444;z-index:5;pointer-events:none;';
      var dotEl = document.createElement('div');
      dotEl.style.cssText = 'position:absolute;left:-4px;top:-3px;width:8px;height:8px;border-radius:50%;background:#ef4444;';
      lineEl.appendChild(dotEl);
      slot.appendChild(lineEl);
    }
  }

  // ==================== EMPTY STATE ====================
  function renderEmptyState(container) {
    container.innerHTML = '<div class="lc-empty-state">' +
      '<div class="lc-empty-icon"><i class="bi bi-calendar-plus"></i></div>' +
      '<h6>No lessons scheduled yet</h6>' +
      '<p class="text-muted">Book your first driving lesson to get started.</p>' +
      '<a href="/find-instructor" class="btn btn-primary"><i class="bi bi-search me-1"></i>Find an Instructor</a>' +
      '</div>';
  }

  // ==================== WEEK VIEW ====================
  function renderWeekView() {
    var gridEl = document.getElementById('lc-week-grid');
    if (!gridEl) return;

    var weekStart = getWeekStart(viewDate);
    var days = [];
    for (var i = 0; i < 7; i++) {
      var d = new Date(weekStart.getTime());
      d.setDate(weekStart.getDate() + i);
      days.push(d);
    }

    var today = new Date();
    var todayK = dateKey(today);
    var totalRows = HOURS_END - HOURS_START;

    // Check for empty state
    var activeBookings = bookings.filter(function (b) { return b.status !== 'cancelled'; });
    var emptyEl = document.getElementById('lc-empty-container');
    if (activeBookings.length === 0 && emptyEl) {
      renderEmptyState(emptyEl);
      emptyEl.style.display = 'block';
    } else if (emptyEl) {
      emptyEl.style.display = 'none';
    }

    gridEl.style.display = 'grid';
    gridEl.style.gridTemplateColumns = '56px repeat(7, 1fr)';
    gridEl.style.gridTemplateRows = 'auto repeat(' + totalRows + ', ' + ROW_HEIGHT + 'px)';

    var html = '<div class="week-time-col" style="grid-row:1"></div>';
    days.forEach(function (d, col) {
      var dk = dateKey(d);
      var isDayToday = dk === todayK;
      html += '<div class="week-day-header ' + (isDayToday ? 'today' : '') + '" style="grid-column:' + (col + 2) + '">' + DAY_NAMES_SHORT[d.getDay()] + ' ' + d.getDate() + '</div>';
    });

    for (var hour = HOURS_START; hour < HOURS_END; hour++) {
      var row = hour - HOURS_START + 2;
      var timeLabel = hour === 12 ? '12pm' : hour > 12 ? (hour - 12) + 'pm' : hour + 'am';
      html += '<div class="week-time-col" style="grid-row:' + row + '">' + timeLabel + '</div>';
      days.forEach(function (d, col) {
        html += '<div class="week-cell" data-date="' + dateKey(d) + '" data-hour="' + hour + '" style="grid-column:' + (col + 2) + ';grid-row:' + row + '"></div>';
      });
    }
    gridEl.innerHTML = html;

    // Place booking events
    bookings.forEach(function (b) {
      if (!b.scheduled_at || b.status === 'cancelled') return;
      var start = new Date(b.scheduled_at);
      var duration = b.duration_minutes || 60;
      var dk = dateKey(start);
      var dayIndex = -1;
      for (var i = 0; i < days.length; i++) {
        if (dateKey(days[i]) === dk) { dayIndex = i; break; }
      }
      if (dayIndex === -1) return;
      var startHour = start.getHours() + start.getMinutes() / 60;
      if (startHour < HOURS_START || startHour >= HOURS_END) return;
      var minutesOffset = (start.getMinutes() / 60) * ROW_HEIGHT;
      var height = Math.max(24, (duration / 60) * ROW_HEIGHT - 2);
      var cell = gridEl.querySelector('.week-cell[data-date="' + dk + '"][data-hour="' + Math.floor(startHour) + '"]');
      if (!cell) return;
      cell.style.position = 'relative';
      var sc = getStatusColor(b.status);
      var el = document.createElement('div');
      el.className = 'week-event lc-status-' + b.status;
      el.style.cssText = 'position:absolute;left:2px;right:2px;top:' + minutesOffset + 'px;height:' + height + 'px;z-index:1;background:' + sc.bg + ';border-left:3px solid ' + sc.border + ';color:' + sc.text + ';';
      var instrName = b.instructor && b.instructor.name ? b.instructor.name : 'Instructor';
      var loc = (b.suburb && (b.suburb.name || '')).trim() || '';
      el.innerHTML = '<strong>' + esc(instrName) + '</strong><br><small>' + (loc ? esc(loc) : b.type || 'Lesson') + '</small>';
      el.addEventListener('click', function (e) {
        e.stopPropagation();
        showBookingPopover(b, el);
      });
      cell.appendChild(el);
    });

    // Today indicator
    renderTodayIndicator(gridEl, days, true);

    var label = document.getElementById('lc-range-label');
    var breadcrumb = document.getElementById('lc-cal-breadcrumb-range');
    if (label) label.textContent = days[0].getDate() + ' – ' + days[6].getDate() + ' ' + MONTH_NAMES[days[0].getMonth()] + ' ' + days[0].getFullYear();
    if (breadcrumb) breadcrumb.textContent = MONTH_NAMES[weekStart.getMonth()] + ' ' + weekStart.getFullYear();
  }

  // ==================== DAY VIEW ====================
  function renderDayView() {
    var container = document.getElementById('lc-day-grid');
    if (!container) return;
    var d = new Date(viewDate.getTime());
    var dk = dateKey(d);
    var byDate = getBookingsByDate(bookings);
    var dayBookings = (byDate[dk] || []).filter(function (b) { return b.status !== 'cancelled'; });
    var html = '';
    for (var hour = HOURS_START; hour < HOURS_END; hour++) {
      var timeLabel = hour === 12 ? '12pm' : hour > 12 ? (hour - 12) + 'pm' : hour + 'am';
      html += '<div class="day-time-row"><div class="day-time-label">' + timeLabel + '</div><div class="day-time-slot" data-hour="' + hour + '" style="position:relative;min-height:' + ROW_HEIGHT + 'px"></div></div>';
    }
    container.innerHTML = html;
    dayBookings.forEach(function (b) {
      var start = new Date(b.scheduled_at);
      var duration = b.duration_minutes || 60;
      var startHour = start.getHours() + start.getMinutes() / 60;
      var rowIndex = Math.floor(startHour - HOURS_START);
      var slots = container.querySelectorAll('.day-time-slot');
      var slot = slots[rowIndex];
      if (!slot) return;
      var minutesOffset = (start.getMinutes() / 60) * ROW_HEIGHT;
      var sc = getStatusColor(b.status);
      var el = document.createElement('div');
      el.className = 'week-event lc-status-' + b.status;
      el.style.cssText = 'position:absolute;left:4px;right:4px;top:' + minutesOffset + 'px;height:' + ((duration / 60) * ROW_HEIGHT - 4) + 'px;background:' + sc.bg + ';border-left:3px solid ' + sc.border + ';color:' + sc.text + ';';
      var instrName = b.instructor && b.instructor.name ? b.instructor.name : '';
      el.innerHTML = '<strong>' + esc(instrName) + '</strong><br><small>' + (b.type || 'Lesson') + '</small>';
      el.addEventListener('click', function (e) {
        e.stopPropagation();
        showBookingPopover(b, el);
      });
      slot.appendChild(el);
    });

    // Today indicator for day view
    renderTodayIndicator(container, null, false);

    var label = document.getElementById('lc-range-label');
    if (label) label.textContent = d.toLocaleDateString(undefined, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
  }

  // ==================== MONTH VIEW ====================
  function renderMonthView() {
    var labelEl = document.getElementById('lc-range-label');
    var gridEl = document.getElementById('lc-grid');
    var weekdaysEl = document.getElementById('lc-weekdays');
    if (!labelEl || !gridEl || !weekdaysEl) return;

    var first = new Date(calendarYear, calendarMonth, 1);
    var last = new Date(calendarYear, calendarMonth + 1, 0);
    var startPad = first.getDay();
    var daysInMonth = last.getDate();
    var totalCells = startPad + daysInMonth;
    var remainder = totalCells % 7;
    if (remainder) totalCells += 7 - remainder;
    var prevMonth = calendarMonth === 0 ? 11 : calendarMonth - 1;
    var prevYear = calendarMonth === 0 ? calendarYear - 1 : calendarYear;
    var prevMonthLast = new Date(prevYear, prevMonth + 1, 0).getDate();

    var today = new Date();
    var todayK = dateKey(today);
    var byDate = getBookingsByDate(bookings);

    labelEl.textContent = MONTH_NAMES[calendarMonth] + ' ' + calendarYear;
    weekdaysEl.innerHTML = DAY_NAMES_SHORT.map(function (day) { return '<div>' + day + '</div>'; }).join('');

    var html = '';
    for (var i = 0; i < totalCells; i++) {
      var dk, dayNum, isOtherMonth = false;
      if (i < startPad) {
        dayNum = prevMonthLast - startPad + i + 1;
        dk = prevYear + '-' + pad(prevMonth + 1) + '-' + pad(dayNum);
        isOtherMonth = true;
      } else if (i < startPad + daysInMonth) {
        dayNum = i - startPad + 1;
        dk = calendarYear + '-' + pad(calendarMonth + 1) + '-' + pad(dayNum);
      } else {
        dayNum = i - startPad - daysInMonth + 1;
        var nextMo = calendarMonth === 11 ? 0 : calendarMonth + 1;
        var nextYr = calendarMonth === 11 ? calendarYear + 1 : calendarYear;
        dk = nextYr + '-' + pad(nextMo + 1) + '-' + pad(dayNum);
        isOtherMonth = true;
      }
      var dayBookings = byDate[dk] || [];
      var hasBooking = dayBookings.length > 0;
      var isDayToday = dk === todayK;
      var classes = 'lc-day-cell' + (isOtherMonth ? ' other-month' : '') + (isDayToday ? ' today' : '') + (hasBooking ? ' has-booking' : '') + (isOtherMonth ? '' : ' clickable');
      var eventsHtml = hasBooking ? '<div class="day-events">' + dayBookings.filter(function (b) { return b.status !== 'cancelled'; }).slice(0, 2).map(function (b) {
        var t = b.scheduled_at ? new Date(b.scheduled_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
        var name = b.instructor && b.instructor.name ? b.instructor.name.split(' ')[0] : '';
        var sc = getStatusColor(b.status);
        return '<div style="margin-top:2px;color:' + sc.text + '"><span style="color:' + sc.border + '">&#9679;</span> ' + t + ' ' + esc(name) + '</div>';
      }).join('') + (dayBookings.length > 2 ? '<div class="text-muted">+' + (dayBookings.length - 2) + ' more</div>' : '') + '</div>' : '';
      html += '<div class="' + classes + '" data-date="' + dk + '"><span class="day-num">' + dayNum + '</span>' + eventsHtml + '</div>';
    }
    gridEl.innerHTML = html;
    gridEl.querySelectorAll('.lc-day-cell.clickable').forEach(function (el) {
      el.addEventListener('click', function () {
        showDayDetail(el.getAttribute('data-date'), byDate[el.getAttribute('data-date')] || []);
      });
    });
  }

  function showDayDetail(dk, dayBookings) {
    var detail = document.getElementById('lc-day-detail');
    var dateLabel = document.getElementById('lc-day-detail-date');
    var body = document.getElementById('lc-day-detail-body');
    if (!detail || !dateLabel || !body) return;
    var d = new Date(dk + 'T12:00:00');
    dateLabel.textContent = d.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    var filtered = dayBookings.filter(function (b) { return b.status !== 'cancelled'; });
    if (filtered.length === 0) {
      body.innerHTML = '<p class="text-muted mb-0">No lessons on this day.</p>';
    } else {
      body.innerHTML = filtered.map(function (b) {
        var time = b.scheduled_at ? new Date(b.scheduled_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
        var instrName = b.instructor && b.instructor.name ? b.instructor.name : 'Instructor';
        var loc = b.suburb ? (b.suburb.name || '') + ' ' + (b.suburb.postcode || '') : '';
        var sc = getStatusColor(b.status);
        var statusBadge = '<span class="lc-status-badge ms-2" style="background:' + sc.bg + ';color:' + sc.text + ';border:1px solid ' + sc.border + '">' + esc(sc.label) + '</span>';
        return '<div class="lc-day-detail-item" data-booking-id="' + b.id + '">' +
          '<strong>' + time + '</strong> with ' + esc(instrName) + statusBadge +
          (loc ? '<br><small class="text-muted"><i class="bi bi-geo-alt"></i> ' + esc(loc.trim()) + '</small>' : '') +
          '</div>';
      }).join('');

      // Add click handlers to day detail items to show popover
      body.querySelectorAll('.lc-day-detail-item').forEach(function (el) {
        var bId = el.getAttribute('data-booking-id');
        var booking = bookings.find(function (b) { return String(b.id) === bId; });
        if (booking) {
          el.style.cursor = 'pointer';
          el.addEventListener('click', function (e) {
            e.stopPropagation();
            showBookingPopover(booking, el);
          });
        }
      });
    }
    detail.style.display = 'block';
  }

  // ==================== UPCOMING LIST ====================
  function renderUpcoming() {
    var body = document.getElementById('lc-upcoming-body');
    if (!body) return;
    var now = new Date();
    var upcoming = bookings.filter(function (b) {
      return b.scheduled_at && new Date(b.scheduled_at) > now && b.status !== 'cancelled';
    }).sort(function (a, b) {
      return new Date(a.scheduled_at) - new Date(b.scheduled_at);
    }).slice(0, 5);

    if (upcoming.length === 0) {
      body.innerHTML = '<p class="text-muted p-3 mb-0">No upcoming lessons. <a href="/find-instructor">Book a lesson now!</a></p>';
      return;
    }

    body.innerHTML = upcoming.map(function (b) {
      var d = new Date(b.scheduled_at);
      var dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      var instrName = b.instructor && b.instructor.name ? b.instructor.name : 'Instructor';
      var timeStr = d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      var loc = b.suburb ? (b.suburb.name || '') : '';
      var typeLabel = b.type === 'test_package' ? 'Test Package' : 'Lesson';
      var sc = getStatusColor(b.status);
      var initial = avatarInitial(instrName);
      var aColor = avatarColor(instrName);
      var lessonIsToday = isToday(d);
      var relTime = relativeTime(d);

      return '<div class="lc-upcoming-row' + (lessonIsToday ? ' lc-upcoming-today' : '') + '">' +
        '<div class="lc-upcoming-avatar" style="background:' + aColor + '">' + esc(initial) + '</div>' +
        '<div class="lc-date-badge"><span class="day-num">' + d.getDate() + '</span>' + monthNames[d.getMonth()] + '</div>' +
        '<div class="flex-grow-1">' +
          '<div class="fw-semibold small">' + esc(instrName) + '</div>' +
          '<div class="text-muted" style="font-size:0.8rem">' + dayNames[d.getDay()] + ' at ' + timeStr + ' &middot; ' + typeLabel + (loc ? ' &middot; ' + esc(loc) : '') + '</div>' +
          '<div class="lc-relative-time' + (lessonIsToday ? ' text-primary fw-semibold' : '') + '">' + esc(relTime) + '</div>' +
        '</div>' +
        '<span class="lc-status-badge" style="background:' + sc.bg + ';color:' + sc.text + ';border:1px solid ' + sc.border + '">' + esc(sc.label) + '</span>' +
      '</div>';
    }).join('');
  }

  // ==================== RENDER ====================
  function render() {
    var weekC = document.getElementById('lc-week-container');
    var dayC = document.getElementById('lc-day-container');
    var monthC = document.getElementById('lc-month-container');
    if (weekC) weekC.style.display = currentView === 'week' ? 'block' : 'none';
    if (dayC) dayC.style.display = currentView === 'day' ? 'block' : 'none';
    if (monthC) monthC.style.display = currentView === 'month' ? 'block' : 'none';
    closePopover();
    if (currentView === 'week') renderWeekView();
    else if (currentView === 'day') renderDayView();
    else renderMonthView();
  }

  function getBookingsUrl() {
    var from = new Date(viewDate.getTime());
    var to = new Date(viewDate.getTime());
    from.setDate(from.getDate() - 60);
    to.setDate(to.getDate() + 90);
    var fromStr = from.getFullYear() + '-' + pad(from.getMonth() + 1) + '-' + pad(from.getDate());
    var toStr = to.getFullYear() + '-' + pad(to.getMonth() + 1) + '-' + pad(to.getDate());
    return '/api/bookings?calendar=1&from=' + fromStr + '&to=' + toStr;
  }

  function loadCalendar() {
    var gridEl = document.getElementById('lc-week-grid');
    if (!gridEl) return;

    render();

    fetchJson(getBookingsUrl()).then(function (r) {
      var d = r && r.data !== undefined ? r.data : r;
      bookings = Array.isArray(d) ? d : (d && d.data) || [];
      render();
      renderUpcoming();
    }).catch(function (err) {
      console.error('Learner calendar error:', err);
      bookings = [];
      render();
      renderUpcoming();
      var upcomingBody = document.getElementById('lc-upcoming-body');
      if (upcomingBody) {
        upcomingBody.innerHTML = '<p class="text-danger p-3 mb-0"><i class="bi bi-exclamation-triangle me-1"></i>Could not load bookings. Please try refreshing.</p>';
      }
    });
  }

  function init() {
    if (!document.getElementById('lc-week-grid')) return;

    var prevBtn = document.getElementById('lc-prev');
    var nextBtn = document.getElementById('lc-next');
    var todayBtn = document.getElementById('lc-today');
    if (!prevBtn || !nextBtn || !todayBtn) return;

    prevBtn.addEventListener('click', function () {
      if (currentView === 'week') viewDate.setDate(viewDate.getDate() - 7);
      else if (currentView === 'day') viewDate.setDate(viewDate.getDate() - 1);
      else {
        if (calendarMonth === 0) { calendarYear--; calendarMonth = 11; } else calendarMonth--;
        viewDate = new Date(calendarYear, calendarMonth, 1);
      }
      calendarYear = viewDate.getFullYear();
      calendarMonth = viewDate.getMonth();
      loadCalendar();
    });

    nextBtn.addEventListener('click', function () {
      if (currentView === 'week') viewDate.setDate(viewDate.getDate() + 7);
      else if (currentView === 'day') viewDate.setDate(viewDate.getDate() + 1);
      else {
        if (calendarMonth === 11) { calendarYear++; calendarMonth = 0; } else calendarMonth++;
        viewDate = new Date(calendarYear, calendarMonth, 1);
      }
      calendarYear = viewDate.getFullYear();
      calendarMonth = viewDate.getMonth();
      loadCalendar();
    });

    todayBtn.addEventListener('click', function () {
      viewDate = new Date();
      calendarYear = viewDate.getFullYear();
      calendarMonth = viewDate.getMonth();
      loadCalendar();
    });

    document.querySelectorAll('input[name="lc-view"]').forEach(function (radio) {
      radio.addEventListener('change', function () {
        currentView = radio.value;
        render();
      });
    });

    loadCalendar();

    // Refresh the today indicator every minute
    setInterval(function () {
      if (currentView === 'week' || currentView === 'day') {
        render();
      }
    }, 60000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
