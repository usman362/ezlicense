/**
 * Instructor calendar: Day / Week / Month views. Self-contained (uses fetch, no imports).
 */
(function () {
  const MONTH_NAMES = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  const DAY_NAMES_SHORT = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  const ROW_HEIGHT = 48;
  const HOURS_START = 7;
  const HOURS_END = 20;

  /** Status color map */
  const STATUS_COLORS = {
    confirmed:          { bg: '#dcfce7', border: '#16a34a', text: '#166534', badge: '#16a34a' },
    instructor_arrived: { bg: '#dbeafe', border: '#2563eb', text: '#1e40af', badge: '#2563eb' },
    in_progress:        { bg: '#f3e8ff', border: '#9333ea', text: '#6b21a8', badge: '#9333ea' },
    completed:          { bg: '#f3f4f6', border: '#6b7280', text: '#374151', badge: '#6b7280' },
    proposed:           { bg: '#fef3c7', border: '#d97706', text: '#92400e', badge: '#d97706' },
    pending:            { bg: '#fefce8', border: '#ca8a04', text: '#854d0e', badge: '#ca8a04' }
  };
  const DEFAULT_STATUS_COLOR = { bg: '#fff3cd', border: '#856404', text: '#856404', badge: '#6c757d' };

  function getStatusColor(status) {
    return STATUS_COLORS[status] || DEFAULT_STATUS_COLOR;
  }

  /** Cancellable statuses */
  var CANCELLABLE = ['confirmed', 'proposed', 'pending'];

  let profileData = null;
  let calendarBookings = [];
  let currentView = 'week';
  let viewDate = new Date();
  let calendarYear = viewDate.getFullYear();
  let calendarMonth = viewDate.getMonth();
  let nowLineInterval = null;

  function getCsrf() {
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }

  function fetchJson(url) {
    const opts = {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    };
    return fetch(url, opts).then(function (r) { return r.json(); });
  }

  function escapeHtml(s) {
    if (s == null) return '';
    var div = document.createElement('div');
    div.textContent = s;
    return div.innerHTML;
  }

  function getWeekStart(d) {
    var date = new Date(d.getTime());
    var day = date.getDay();
    var diff = date.getDate() - day + (day === 0 ? -6 : 1);
    date.setDate(diff);
    return date;
  }

  function getBookingsByDate(bookings) {
    var byDate = {};
    (bookings || []).forEach(function (b) {
      var iso = b.scheduled_at;
      if (!iso) return;
      var d = new Date(iso);
      var key = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
      if (!byDate[key]) byDate[key] = [];
      byDate[key].push(b);
    });
    return byDate;
  }

  function isAvailableAtHour(dayOfWeek, hour) {
    if (!profileData || !profileData.availability_slots || !profileData.availability_slots.length) return true;
    var slots = profileData.availability_slots.filter(function (s) { return Number(s.day_of_week) === dayOfWeek; });
    if (!slots.length) return false;
    var h = hour;
    return slots.some(function (s) {
      var startStr = (s.start_time || '00:00').toString().split(':');
      var endStr = (s.end_time || '23:59').toString().split(':');
      var start = (parseInt(startStr[0], 10) || 0) + (parseInt(startStr[1], 10) || 0) / 60;
      var end = (parseInt(endStr[0], 10) || 23) + (parseInt(endStr[1], 10) || 59) / 60;
      return h >= start && h < end;
    });
  }

  /* ─── Popover helpers ─── */

  function closePopover() {
    var pop = document.getElementById('cal-popover');
    if (pop) pop.remove();
    var menu = document.getElementById('cal-slot-menu');
    if (menu) menu.remove();
  }

  /** Position a floating element next to an anchor, keeping it in viewport */
  function positionFloating(floating, anchor) {
    var rect = anchor.getBoundingClientRect();
    var fw = floating.offsetWidth || 320;
    var fh = floating.offsetHeight || 200;
    var left = rect.right + 8;
    var top = rect.top;
    if (left + fw > window.innerWidth - 16) {
      left = rect.left - fw - 8;
    }
    if (left < 8) left = 8;
    if (top + fh > window.innerHeight - 16) {
      top = window.innerHeight - fh - 16;
    }
    if (top < 8) top = 8;
    floating.style.left = left + 'px';
    floating.style.top = top + 'px';
  }

  /** Build and show booking detail popover */
  function showBookingPopover(booking, anchorEl) {
    closePopover();
    var sc = getStatusColor(booking.status);
    var startTime = booking.scheduled_at ? new Date(booking.scheduled_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '--';
    var endTime = '';
    if (booking.scheduled_at && booking.duration_minutes) {
      var e = new Date(new Date(booking.scheduled_at).getTime() + booking.duration_minutes * 60000);
      endTime = ' - ' + e.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    var learnerName = (booking.learner && booking.learner.name) || 'Unknown';
    var learnerPhone = (booking.learner && booking.learner.phone) || '';
    var lessonType = booking.type || booking.lesson_type || 'Lesson';
    var duration = booking.duration_minutes ? booking.duration_minutes + ' min' : '';
    var suburb = (booking.suburb && ((booking.suburb.name || '') + ' ' + (booking.suburb.postcode || '')).trim()) || '';
    var transmission = booking.transmission ? booking.transmission.charAt(0).toUpperCase() + booking.transmission.slice(1) : '';

    var actionsHtml = '';
    if (booking.status === 'confirmed') {
      actionsHtml += '<button class="btn btn-sm btn-primary cal-action-btn" data-action="arrived" data-id="' + booking.id + '"><i class="bi bi-geo-alt me-1"></i>Mark Arrived</button>';
    } else if (booking.status === 'instructor_arrived') {
      actionsHtml += '<button class="btn btn-sm btn-primary cal-action-btn" data-action="start-lesson" data-id="' + booking.id + '"><i class="bi bi-play-fill me-1"></i>Start Lesson</button>';
    } else if (booking.status === 'in_progress') {
      actionsHtml += '<button class="btn btn-sm btn-primary cal-action-btn" data-action="end-lesson" data-id="' + booking.id + '"><i class="bi bi-stop-fill me-1"></i>End Lesson</button>';
    }
    if (CANCELLABLE.indexOf(booking.status) !== -1) {
      actionsHtml += '<button class="btn btn-sm btn-outline-danger cal-action-btn ms-1" data-action="cancel" data-id="' + booking.id + '"><i class="bi bi-x-circle me-1"></i>Cancel</button>';
    }
    actionsHtml += '<a href="/instructor/learners/' + (booking.learner_id || '') + '" class="btn btn-sm btn-outline-secondary ms-1"><i class="bi bi-arrow-repeat me-1"></i>Reschedule</a>';

    var pop = document.createElement('div');
    pop.id = 'cal-popover';
    pop.className = 'cal-popover';
    pop.innerHTML = '<div class="cal-popover-header">'
      + '<strong>' + escapeHtml(learnerName) + '</strong>'
      + '<button class="cal-popover-close" title="Close">&times;</button>'
      + '</div>'
      + '<div class="cal-popover-body">'
      + (learnerPhone ? '<div class="mb-1"><i class="bi bi-telephone me-1"></i>' + escapeHtml(learnerPhone) + '</div>' : '')
      + '<div class="mb-1"><i class="bi bi-clock me-1"></i>' + startTime + endTime + (duration ? ' (' + duration + ')' : '') + '</div>'
      + '<div class="mb-1"><i class="bi bi-book me-1"></i>' + escapeHtml(lessonType) + (transmission ? ' &middot; ' + escapeHtml(transmission) : '') + '</div>'
      + (suburb ? '<div class="mb-1"><i class="bi bi-geo me-1"></i>' + escapeHtml(suburb) + '</div>' : '')
      + '<div class="mb-2"><span class="cal-status-badge" style="background:' + sc.badge + '">' + escapeHtml(booking.status ? booking.status.replace(/_/g, ' ') : '') + '</span></div>'
      + '<div class="cal-popover-actions">' + actionsHtml + '</div>'
      + '</div>';

    document.body.appendChild(pop);
    positionFloating(pop, anchorEl);

    pop.querySelector('.cal-popover-close').addEventListener('click', closePopover);
    pop.querySelectorAll('.cal-action-btn').forEach(function (btn) {
      btn.addEventListener('click', function () { handleQuickAction(btn.dataset.action, btn.dataset.id, btn); });
    });
  }

  /** Handle quick action API call */
  function handleQuickAction(action, bookingId, btn) {
    var urlMap = {
      'arrived': '/api/bookings/' + bookingId + '/arrived',
      'start-lesson': '/api/bookings/' + bookingId + '/start-lesson',
      'end-lesson': '/api/bookings/' + bookingId + '/end-lesson',
      'cancel': '/api/bookings/' + bookingId + '/cancel'
    };
    var methodMap = {
      'arrived': 'POST',
      'start-lesson': 'POST',
      'end-lesson': 'POST',
      'cancel': 'PUT'
    };
    var url = urlMap[action];
    if (!url) return;

    if (action === 'cancel' && !confirm('Are you sure you want to cancel this booking?')) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Working...';

    fetch(url, {
      method: methodMap[action],
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrf(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin'
    }).then(function (r) {
      if (!r.ok) throw new Error('Request failed: ' + r.status);
      return r.json();
    }).then(function (data) {
      closePopover();
      // Refresh just the bookings and re-render
      fetchJson(getBookingsUrl()).then(function (r) {
        var d = r && r.data !== undefined ? r.data : r;
        calendarBookings = Array.isArray(d) ? d : (d && d.data) || [];
        render();
      });
    }).catch(function (err) {
      console.error('Calendar action error:', err);
      btn.disabled = false;
      btn.innerHTML = 'Error - Try Again';
    });
  }

  /** Show empty slot context menu */
  function showSlotMenu(dateKey, hour, anchorEl) {
    closePopover();
    var menu = document.createElement('div');
    menu.id = 'cal-slot-menu';
    menu.className = 'cal-slot-menu';
    var timeLabel = hour === 12 ? '12:00 pm' : hour > 12 ? (hour - 12) + ':00 pm' : hour + ':00 am';
    menu.innerHTML = '<div class="cal-slot-menu-header">' + dateKey + ' at ' + timeLabel + '</div>'
      + '<a href="/instructor/settings/opening-hours" class="cal-slot-menu-item"><i class="bi bi-slash-circle me-2"></i>Block This Time</a>'
      + '<a href="/instructor/learners?propose=1&date=' + dateKey + '&time=' + String(hour).padStart(2, '0') + ':00" class="cal-slot-menu-item"><i class="bi bi-calendar-plus me-2"></i>Propose a Booking</a>';
    document.body.appendChild(menu);
    positionFloating(menu, anchorEl);
  }

  /* ─── Now-line (current time indicator) ─── */

  function drawNowLine() {
    // Remove old lines
    document.querySelectorAll('.cal-now-line').forEach(function (el) { el.remove(); });
    var now = new Date();
    var nowHour = now.getHours() + now.getMinutes() / 60;
    if (nowHour < HOURS_START || nowHour >= HOURS_END) return;

    if (currentView === 'week') {
      var grid = document.getElementById('calendar-week-grid');
      if (!grid) return;
      var todayKey = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
      var cell = grid.querySelector('.week-cell[data-date="' + todayKey + '"][data-hour="' + Math.floor(nowHour) + '"]');
      if (!cell) return;
      cell.style.position = 'relative';
      var line = document.createElement('div');
      line.className = 'cal-now-line';
      var minuteOffset = (now.getMinutes() / 60) * ROW_HEIGHT;
      line.style.cssText = 'position:absolute;left:0;right:0;top:' + minuteOffset + 'px;height:2px;background:#ef4444;z-index:5;pointer-events:none;';
      var dot = document.createElement('div');
      dot.style.cssText = 'position:absolute;left:-4px;top:-3px;width:8px;height:8px;background:#ef4444;border-radius:50%;';
      line.appendChild(dot);
      cell.appendChild(line);
    } else if (currentView === 'day') {
      var container = document.getElementById('calendar-day-grid');
      if (!container) return;
      var rowIndex = Math.floor(nowHour) - HOURS_START;
      var slots = container.querySelectorAll('.day-time-slot');
      var slot = slots[rowIndex];
      if (!slot) return;
      slot.style.position = 'relative';
      var line = document.createElement('div');
      line.className = 'cal-now-line';
      var minuteOffset = (now.getMinutes() / 60) * ROW_HEIGHT;
      line.style.cssText = 'position:absolute;left:0;right:0;top:' + minuteOffset + 'px;height:2px;background:#ef4444;z-index:5;pointer-events:none;';
      var dot = document.createElement('div');
      dot.style.cssText = 'position:absolute;left:-4px;top:-3px;width:8px;height:8px;background:#ef4444;border-radius:50%;';
      line.appendChild(dot);
      slot.appendChild(line);
    }
  }

  function startNowLineTimer() {
    if (nowLineInterval) clearInterval(nowLineInterval);
    drawNowLine();
    nowLineInterval = setInterval(drawNowLine, 60000); // update every minute
  }

  /* ─── Close popover on outside click ─── */
  document.addEventListener('click', function (e) {
    var pop = document.getElementById('cal-popover');
    var menu = document.getElementById('cal-slot-menu');
    if (pop && !pop.contains(e.target) && !e.target.closest('.week-event') && !e.target.closest('.day-booking-event')) {
      pop.remove();
    }
    if (menu && !menu.contains(e.target) && !e.target.closest('.week-cell') && !e.target.closest('.day-time-slot')) {
      menu.remove();
    }
  });

  function renderWeekView() {
    var gridEl = document.getElementById('calendar-week-grid');
    if (!gridEl) return;

    var weekStart = getWeekStart(viewDate);
    var days = [];
    for (var i = 0; i < 7; i++) {
      var d = new Date(weekStart.getTime());
      d.setDate(weekStart.getDate() + i);
      days.push(d);
    }

    var today = new Date();
    var todayKey = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
    var totalRows = HOURS_END - HOURS_START;

    gridEl.style.display = 'grid';
    gridEl.style.gridTemplateColumns = '56px repeat(7, 1fr)';
    gridEl.style.gridTemplateRows = 'auto repeat(' + totalRows + ', ' + ROW_HEIGHT + 'px)';

    var html = '<div class="week-time-col" style="grid-row:1"></div>';
    days.forEach(function (d, col) {
      var dateKey = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
      var isToday = dateKey === todayKey;
      html += '<div class="week-day-header ' + (isToday ? 'today' : '') + '" style="grid-column:' + (col + 2) + '">' + DAY_NAMES_SHORT[d.getDay()] + ' ' + d.getDate() + '</div>';
    });

    for (var hour = HOURS_START; hour < HOURS_END; hour++) {
      var row = hour - HOURS_START + 2;
      var timeLabel = hour === 12 ? '12pm' : hour > 12 ? (hour - 12) + 'pm' : hour + 'am';
      html += '<div class="week-time-col" style="grid-row:' + row + '">' + timeLabel + '</div>';
      days.forEach(function (d, col) {
        var dateKey = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        var available = isAvailableAtHour(d.getDay(), hour);
        html += '<div class="week-cell ' + (available ? '' : 'unavailable') + '" data-date="' + dateKey + '" data-hour="' + hour + '" style="grid-column:' + (col + 2) + ';grid-row:' + row + '"></div>';
      });
    }
    gridEl.innerHTML = html;

    calendarBookings.forEach(function (b) {
      if (!b.scheduled_at || b.status === 'cancelled') return;
      var start = new Date(b.scheduled_at);
      var duration = b.duration_minutes || 60;
      var dateKey = start.getFullYear() + '-' + String(start.getMonth() + 1).padStart(2, '0') + '-' + String(start.getDate()).padStart(2, '0');
      var dayIndex = -1;
      for (var i = 0; i < days.length; i++) {
        var dk = days[i].getFullYear() + '-' + String(days[i].getMonth() + 1).padStart(2, '0') + '-' + String(days[i].getDate()).padStart(2, '0');
        if (dk === dateKey) { dayIndex = i; break; }
      }
      if (dayIndex === -1) return;
      var startHour = start.getHours() + start.getMinutes() / 60;
      if (startHour < HOURS_START || startHour >= HOURS_END) return;
      var minutesOffset = (start.getMinutes() / 60) * ROW_HEIGHT;
      var height = Math.max(24, (duration / 60) * ROW_HEIGHT - 2);
      var cell = gridEl.querySelector('.week-cell[data-date="' + dateKey + '"][data-hour="' + Math.floor(startHour) + '"]');
      if (!cell) return;
      cell.style.position = 'relative';
      var sc = getStatusColor(b.status);
      var el = document.createElement('div');
      el.className = 'week-event booking';
      el.style.cssText = 'position:absolute;left:2px;right:2px;top:' + minutesOffset + 'px;height:' + height + 'px;z-index:2;background:' + sc.bg + ';color:' + sc.text + ';border-left:3px solid ' + sc.border + ';cursor:pointer;';
      var addr = (b.suburb && (b.suburb.name || '') + ' ' + (b.suburb.postcode || '')).trim() || '';
      var trans = b.transmission ? '(' + String(b.transmission).charAt(0).toUpperCase() + ')' : '';
      el.innerHTML = '<strong>' + escapeHtml(b.learner && b.learner.name ? b.learner.name : 'Booking') + '</strong> ' + trans + '<br><small>' + (addr ? escapeHtml(addr) : '—') + '</small>';
      // Booking click -> popover
      (function (booking, element) {
        element.addEventListener('click', function (e) {
          e.stopPropagation();
          showBookingPopover(booking, element);
        });
      })(b, el);
      cell.appendChild(el);
    });

    // Attach empty-slot click handlers
    gridEl.querySelectorAll('.week-cell').forEach(function (cell) {
      cell.addEventListener('click', function (e) {
        if (e.target.closest('.week-event')) return; // ignore if clicking on a booking
        var dateKey = cell.getAttribute('data-date');
        var hour = parseInt(cell.getAttribute('data-hour'), 10);
        showSlotMenu(dateKey, hour, cell);
      });
    });

    var rangeLabel = document.getElementById('calendar-range-label');
    var breadcrumbRange = document.getElementById('calendar-breadcrumb-range');
    if (rangeLabel) rangeLabel.textContent = days[0].getDate() + ' – ' + days[6].getDate() + ' ' + MONTH_NAMES[days[0].getMonth()] + ' ' + days[0].getFullYear();
    if (breadcrumbRange) breadcrumbRange.textContent = MONTH_NAMES[weekStart.getMonth()] + ' ' + weekStart.getFullYear();
  }

  function renderDayView() {
    var container = document.getElementById('calendar-day-grid');
    if (!container) return;
    var d = new Date(viewDate.getTime());
    var dateKey = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    var byDate = getBookingsByDate(calendarBookings);
    var dayBookings = (byDate[dateKey] || []).filter(function (b) { return b.status !== 'cancelled'; });
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
      el.className = 'week-event booking day-booking-event';
      el.style.cssText = 'position:absolute;left:4px;right:4px;top:' + minutesOffset + 'px;height:' + ((duration / 60) * ROW_HEIGHT - 4) + 'px;background:' + sc.bg + ';color:' + sc.text + ';border-left:3px solid ' + sc.border + ';cursor:pointer;';
      var addr = (b.suburb && (b.suburb.name || '') + ' ' + (b.suburb.postcode || '')).trim() || '';
      el.innerHTML = '<strong>' + escapeHtml(b.learner && b.learner.name ? b.learner.name : '') + '</strong><br><small>' + (addr ? escapeHtml(addr) : '—') + '</small>';
      // Booking click -> popover
      (function (booking, element) {
        element.addEventListener('click', function (e) {
          e.stopPropagation();
          showBookingPopover(booking, element);
        });
      })(b, el);
      slot.appendChild(el);
    });

    // Attach empty-slot click handlers for day view
    container.querySelectorAll('.day-time-slot').forEach(function (slot) {
      slot.addEventListener('click', function (e) {
        if (e.target.closest('.week-event')) return;
        var hour = parseInt(slot.getAttribute('data-hour'), 10);
        showSlotMenu(dateKey, hour, slot);
      });
    });
    var rangeLabel = document.getElementById('calendar-range-label');
    var breadcrumbRange = document.getElementById('calendar-breadcrumb-range');
    if (rangeLabel) rangeLabel.textContent = d.toLocaleDateString(undefined, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    if (breadcrumbRange) breadcrumbRange.textContent = d.toLocaleDateString(undefined, { month: 'long', year: 'numeric' });
  }

  function renderMonthView() {
    var labelEl = document.getElementById('calendar-range-label');
    var gridEl = document.getElementById('calendar-grid');
    var weekdaysEl = document.getElementById('calendar-weekdays');
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
    var todayKey = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
    var bookingsByDate = getBookingsByDate(calendarBookings);

    labelEl.textContent = MONTH_NAMES[calendarMonth] + ' ' + calendarYear;
    var breadcrumbRange = document.getElementById('calendar-breadcrumb-range');
    if (breadcrumbRange) breadcrumbRange.textContent = MONTH_NAMES[calendarMonth] + ' ' + calendarYear;
    weekdaysEl.innerHTML = DAY_NAMES_SHORT.map(function (day) { return '<div>' + day + '</div>'; }).join('');

    var html = '';
    for (var i = 0; i < totalCells; i++) {
      var dateKey, dayNum, isOtherMonth = false;
      if (i < startPad) {
        dayNum = prevMonthLast - startPad + i + 1;
        dateKey = prevYear + '-' + String(prevMonth + 1).padStart(2, '0') + '-' + String(dayNum).padStart(2, '0');
        isOtherMonth = true;
      } else if (i < startPad + daysInMonth) {
        dayNum = i - startPad + 1;
        dateKey = calendarYear + '-' + String(calendarMonth + 1).padStart(2, '0') + '-' + String(dayNum).padStart(2, '0');
      } else {
        dayNum = i - startPad - daysInMonth + 1;
        var nextMonth = calendarMonth === 11 ? 0 : calendarMonth + 1;
        var nextYear = calendarMonth === 11 ? calendarYear + 1 : calendarYear;
        dateKey = nextYear + '-' + String(nextMonth + 1).padStart(2, '0') + '-' + String(dayNum).padStart(2, '0');
        isOtherMonth = true;
      }
      var dayBookings = bookingsByDate[dateKey] || [];
      var hasBooking = dayBookings.length > 0;
      var isPast = new Date(dateKey) < new Date(today.getFullYear(), today.getMonth(), today.getDate());
      var isToday = dateKey === todayKey;
      var classes = 'instructor-calendar-day' + (isOtherMonth ? ' other-month' : '') + (isToday ? ' today' : '') + (isPast ? ' past' : '') + (hasBooking ? ' has-booking' : '') + (isOtherMonth ? '' : ' clickable');
      var eventsHtml = hasBooking ? '<div class="day-events">' + dayBookings.slice(0, 2).map(function (b) {
        var t = b.scheduled_at ? new Date(b.scheduled_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
        var sc = getStatusColor(b.status);
        return '<div style="color:' + sc.text + '"><span style="color:' + sc.border + '">●</span> ' + t + ' ' + escapeHtml(b.learner && b.learner.name ? b.learner.name : '') + '</div>';
      }).join('') + '</div>' : '';
      html += '<div class="' + classes + '" data-date="' + dateKey + '"><span class="day-num">' + dayNum + '</span>' + eventsHtml + '</div>';
    }
    gridEl.innerHTML = html;
    gridEl.querySelectorAll('.instructor-calendar-day.clickable').forEach(function (el) {
      el.addEventListener('click', function () {
        showDayDetail(el.getAttribute('data-date'), bookingsByDate[el.getAttribute('data-date')] || []);
      });
    });
  }

  function showDayDetail(dateKey, dayBookings) {
    var detail = document.getElementById('calendar-day-detail');
    var dateLabel = document.getElementById('calendar-day-detail-date');
    var body = document.getElementById('calendar-day-detail-body');
    if (!detail || !dateLabel || !body) return;
    var d = new Date(dateKey + 'T12:00:00');
    dateLabel.textContent = d.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    if (dayBookings.length === 0) {
      body.innerHTML = '<p class="text-muted mb-0">No bookings this day.</p>';
    } else {
      body.innerHTML = dayBookings.map(function (b) {
        var time = b.scheduled_at ? new Date(b.scheduled_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '—';
        var addr = (b.suburb && (b.suburb.name || '') + ' ' + (b.suburb.postcode || '')).trim() || '';
        var sc = getStatusColor(b.status);
        return '<p class="mb-2"><strong>' + time + '</strong> ' + escapeHtml(b.learner && b.learner.name ? b.learner.name : '—') + ' — ' + (b.type || '') + (addr ? '<br><small class="text-muted">' + escapeHtml(addr) + '</small>' : '') + ' <span class="badge" style="background:' + sc.badge + ';color:#fff">' + escapeHtml(b.status ? b.status.replace(/_/g, ' ') : '') + '</span></p>';
      }).join('');
    }
    detail.style.display = 'block';
  }

  function render() {
    var weekContainer = document.getElementById('calendar-week-container');
    var dayContainer = document.getElementById('calendar-day-container');
    var monthContainer = document.getElementById('calendar-month-container');
    if (weekContainer) weekContainer.style.display = currentView === 'week' ? 'block' : 'none';
    if (dayContainer) dayContainer.style.display = currentView === 'day' ? 'block' : 'none';
    if (monthContainer) monthContainer.style.display = currentView === 'month' ? 'block' : 'none';
    if (currentView === 'week') renderWeekView();
    else if (currentView === 'day') renderDayView();
    else renderMonthView();
    startNowLineTimer();
  }

  /**
   * Build a date-range query string based on the current view.
   * Fetches ±60 days from today to cover any view (week/day/month).
   */
  function getBookingsUrl() {
    var from = new Date(viewDate.getTime());
    var to = new Date(viewDate.getTime());
    from.setDate(from.getDate() - 60);
    to.setDate(to.getDate() + 60);
    var pad = function (n) { return String(n).padStart(2, '0'); };
    var fromStr = from.getFullYear() + '-' + pad(from.getMonth() + 1) + '-' + pad(from.getDate());
    var toStr = to.getFullYear() + '-' + pad(to.getMonth() + 1) + '-' + pad(to.getDate());
    return '/api/bookings?calendar=1&from=' + fromStr + '&to=' + toStr;
  }

  function loadCalendar() {
    var gridEl = document.getElementById('calendar-week-grid');
    var rangeLabel = document.getElementById('calendar-range-label');
    if (!gridEl) return;

    Promise.all([
      fetchJson('/api/instructor/profile').then(function (r) { return r.data || r; }).catch(function () { return {}; }),
      fetchJson(getBookingsUrl()).then(function (r) {
        var d = r && r.data !== undefined ? r.data : r;
        return Array.isArray(d) ? d : (d && d.data) || [];
      }).catch(function () { return []; })
    ]).then(function (results) {
      profileData = results[0] || null;
      calendarBookings = results[1] || [];
      render();
    }).catch(function (err) {
      console.error('Calendar error:', err);
      gridEl.innerHTML = '<p class="p-3 text-muted">Could not load calendar. Check that you are logged in as an instructor.</p>';
      if (rangeLabel) rangeLabel.textContent = 'Error';
    });
  }

  function init() {
    if (!document.getElementById('calendar-week-grid')) return;
    var prevBtn = document.getElementById('calendar-prev');
    var nextBtn = document.getElementById('calendar-next');
    var todayBtn = document.getElementById('calendar-today');
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
    document.querySelectorAll('input[name="calendar-view"]').forEach(function (radio) {
      radio.addEventListener('change', function () {
        currentView = radio.value;
        render();
      });
    });
    loadCalendar();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
