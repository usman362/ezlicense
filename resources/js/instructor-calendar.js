/**
 * Instructor calendar: Day / Week / Month views. Self-contained (uses fetch, no imports).
 */
(function () {
  const MONTH_NAMES = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  const DAY_NAMES_SHORT = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  const ROW_HEIGHT = 48;
  const HOURS_START = 7;
  const HOURS_END = 20;

  let profileData = null;
  let calendarBookings = [];
  let currentView = 'week';
  let viewDate = new Date();
  let calendarYear = viewDate.getFullYear();
  let calendarMonth = viewDate.getMonth();

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
      var el = document.createElement('div');
      el.className = 'week-event booking';
      el.style.cssText = 'position:absolute;left:2px;right:2px;top:' + minutesOffset + 'px;height:' + height + 'px;z-index:1';
      var addr = (b.suburb && (b.suburb.name || '') + ' ' + (b.suburb.postcode || '')).trim() || '';
      var trans = b.transmission ? '(' + String(b.transmission).charAt(0).toUpperCase() + ')' : '';
      el.innerHTML = '<strong>' + escapeHtml(b.learner && b.learner.name ? b.learner.name : 'Booking') + '</strong> ' + trans + '<br><small>' + (addr ? escapeHtml(addr) : '—') + '</small>';
      cell.appendChild(el);
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
      var el = document.createElement('div');
      el.className = 'week-event booking';
      el.style.cssText = 'position:absolute;left:4px;right:4px;top:' + minutesOffset + 'px;height:' + ((duration / 60) * ROW_HEIGHT - 4) + 'px';
      var addr = (b.suburb && (b.suburb.name || '') + ' ' + (b.suburb.postcode || '')).trim() || '';
      el.innerHTML = '<strong>' + escapeHtml(b.learner && b.learner.name ? b.learner.name : '') + '</strong><br><small>' + (addr ? escapeHtml(addr) : '—') + '</small>';
      slot.appendChild(el);
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
        return '<div>● ' + t + ' ' + escapeHtml(b.learner && b.learner.name ? b.learner.name : '') + '</div>';
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
        return '<p class="mb-2"><strong>' + time + '</strong> ' + escapeHtml(b.learner && b.learner.name ? b.learner.name : '—') + ' — ' + b.type + (addr ? '<br><small class="text-muted">' + escapeHtml(addr) + '</small>' : '') + ' <span class="badge bg-success">' + b.status + '</span></p>';
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
  }

  function loadCalendar() {
    var gridEl = document.getElementById('calendar-week-grid');
    var rangeLabel = document.getElementById('calendar-range-label');
    if (!gridEl) return;

    Promise.all([
      fetchJson('/api/instructor/profile').then(function (r) { return r.data || r; }).catch(function () { return {}; }),
      fetchJson('/api/bookings').then(function (r) {
        var d = r && r.data !== undefined ? r.data : r;
        return Array.isArray(d) ? d : (d && d.data) || [];
      }).catch(function () { return []; })
    ]).then(function (results) {
      profileData = results[0] || null;
      calendarBookings = results[1] || [];
      viewDate = new Date();
      calendarYear = viewDate.getFullYear();
      calendarMonth = viewDate.getMonth();
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
      render();
    });
    nextBtn.addEventListener('click', function () {
      if (currentView === 'week') viewDate.setDate(viewDate.getDate() + 7);
      else if (currentView === 'day') viewDate.setDate(viewDate.getDate() + 1);
      else {
        if (calendarMonth === 11) { calendarYear++; calendarMonth = 0; } else calendarMonth++;
        viewDate = new Date(calendarYear, calendarMonth, 1);
      }
      render();
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
