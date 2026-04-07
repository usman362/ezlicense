/**
 * Instructor dashboard: profile, service areas, availability, bookings.
 */
import api, {
  getInstructorDashboardProfile,
  updateInstructorProfile,
  updateInstructorServiceAreas,
  updateInstructorAvailability,
  getBookings,
  searchSuburbs,
} from './securelicence-api.js';

const DAY_NAMES = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

// ---------- Profile ----------
let profileData = null;

async function loadProfile() {
  const data = await getInstructorDashboardProfile();
  profileData = data;
  document.getElementById('profile-loading').style.display = 'none';
  const form = document.getElementById('profile-form');
  form.style.display = 'block';
  form.bio.value = data.bio || '';
  form.transmission.value = data.transmission || 'both';
  form.lesson_duration_minutes.value = data.lesson_duration_minutes || 60;
  form.vehicle_make.value = data.vehicle_make || '';
  form.vehicle_model.value = data.vehicle_model || '';
  form.vehicle_year.value = data.vehicle_year || '';
  form.vehicle_safety_rating.value = data.vehicle_safety_rating || '';
  form.lesson_price.value = data.lesson_price ?? '';
  form.test_package_price.value = data.test_package_price ?? '';
  form.offers_test_package.checked = data.offers_test_package ?? false;
  form.is_active.checked = data.is_active ?? false;
}

document.getElementById('profile-form')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const msg = document.getElementById('profile-message');
  try {
    await updateInstructorProfile({
      bio: form.bio.value,
      transmission: form.transmission.value,
      lesson_duration_minutes: parseInt(form.lesson_duration_minutes.value, 10) || 60,
      vehicle_make: form.vehicle_make.value || null,
      vehicle_model: form.vehicle_model.value || null,
      vehicle_year: form.vehicle_year.value ? parseInt(form.vehicle_year.value, 10) : null,
      vehicle_safety_rating: form.vehicle_safety_rating.value || null,
      lesson_price: parseFloat(form.lesson_price.value) || 0,
      test_package_price: form.test_package_price.value ? parseFloat(form.test_package_price.value) : null,
      offers_test_package: form.offers_test_package.checked,
      is_active: form.is_active.checked,
    });
    msg.textContent = 'Saved.';
    setTimeout(() => { msg.textContent = ''; }, 3000);
  } catch (err) {
    msg.textContent = err.response?.data?.message || 'Error saving.';
    msg.classList.remove('text-success');
    msg.classList.add('text-danger');
  }
});

// ---------- Service areas ----------
let serviceAreaIds = [];
let serviceAreaLabels = [];

function renderServiceAreas() {
  const list = document.getElementById('service-areas-list');
  const saveBtn = document.getElementById('save-areas-btn');
  list.innerHTML = serviceAreaIds.length === 0
    ? '<li class="list-group-item text-muted">No suburbs added. Search above and click Add.</li>'
    : serviceAreaIds.map((id, i) => {
        const label = serviceAreaLabels[i] || `ID ${id}`;
        return `<li class="list-group-item d-flex justify-content-between align-items-center">
          ${escapeHtml(label)}
          <button type="button" class="btn btn-sm btn-outline-danger remove-area" data-id="${id}">Remove</button>
        </li>`;
      }).join('');
  saveBtn.style.display = serviceAreaIds.length ? 'inline-block' : 'none';
  list.querySelectorAll('.remove-area').forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = parseInt(btn.dataset.id, 10);
      const idx = serviceAreaIds.indexOf(id);
      if (idx !== -1) {
        serviceAreaIds.splice(idx, 1);
        serviceAreaLabels.splice(idx, 1);
        renderServiceAreas();
      }
    });
  });
}

const suburbAddInput = document.getElementById('suburb-add-input');
const suburbSuggestions = document.getElementById('suburb-suggestions');
let suburbSearchTimeout;

function showSuburbSuggestions(items) {
  suburbSuggestions.innerHTML = items.map((s) =>
    `<li class="list-group-item list-group-item-action suburb-suggestion" data-id="${s.id}" data-label="${escapeAttr(s.label)}">${escapeHtml(s.label)}</li>`
  ).join('');
  suburbSuggestions.style.display = items.length ? 'block' : 'none';
  suburbSuggestions.querySelectorAll('.suburb-suggestion').forEach((li) => {
    li.addEventListener('click', () => {
      const id = parseInt(li.dataset.id, 10);
      const label = li.dataset.label;
      if (!serviceAreaIds.includes(id)) {
        serviceAreaIds.push(id);
        serviceAreaLabels.push(label);
        renderServiceAreas();
      }
      suburbAddInput.value = '';
      suburbSuggestions.style.display = 'none';
    });
  });
}

suburbAddInput?.addEventListener('input', () => {
  clearTimeout(suburbSearchTimeout);
  const q = suburbAddInput.value.trim();
  if (q.length < 2) {
    showSuburbSuggestions([]);
    return;
  }
  suburbSearchTimeout = setTimeout(async () => {
    const data = await searchSuburbs(q);
    showSuburbSuggestions(data);
  }, 250);
});
suburbAddInput?.addEventListener('blur', () => setTimeout(() => { suburbSuggestions.style.display = 'none'; }, 200));

document.getElementById('suburb-add-btn')?.addEventListener('click', async () => {
  const q = suburbAddInput.value.trim();
  if (q.length < 2) return;
  const data = await searchSuburbs(q);
  if (data.length && !serviceAreaIds.includes(data[0].id)) {
    serviceAreaIds.push(data[0].id);
    serviceAreaLabels.push(data[0].label);
    renderServiceAreas();
    suburbAddInput.value = '';
  }
  showSuburbSuggestions([]);
});

document.getElementById('save-areas-btn')?.addEventListener('click', async () => {
  const msg = document.getElementById('areas-message');
  try {
    await updateInstructorServiceAreas(serviceAreaIds);
    msg.textContent = 'Saved.';
    msg.classList.remove('text-danger');
    msg.classList.add('text-success');
    setTimeout(() => { msg.textContent = ''; }, 3000);
  } catch (err) {
    msg.textContent = err.response?.data?.message || 'Error saving.';
    msg.classList.add('text-danger');
  }
});

// ---------- Availability ----------
let availabilitySlots = [];

function renderAvailabilitySlots() {
  const list = document.getElementById('availability-slots-list');
  if (availabilitySlots.length === 0) {
    list.innerHTML = '<p class="text-muted small">No slots. Add below.</p>';
    return;
  }
  const byDay = {};
  availabilitySlots.forEach((s, i) => {
    if (!byDay[s.day_of_week]) byDay[s.day_of_week] = [];
    byDay[s.day_of_week].push({ ...s, _index: i });
  });
  list.innerHTML = Object.entries(byDay).sort((a, b) => +a[0] - +b[0]).map(([day, slots]) =>
    `<div class="mb-2">
      <strong>${DAY_NAMES[day]}</strong>: ${slots.map((s) =>
        `<span class="badge bg-secondary me-1">${s.start_time}–${s.end_time} <button type="button" class="btn-close btn-close-white btn-close-sm ms-1 remove-slot" data-index="${s._index}" aria-label="Remove"></button></span>`
      ).join(' ')}
    </div>`
  ).join('');
  list.querySelectorAll('.remove-slot').forEach((btn) => {
    btn.addEventListener('click', () => {
      const idx = parseInt(btn.dataset.index, 10);
      availabilitySlots.splice(idx, 1);
      renderAvailabilitySlots();
    });
  });
}

document.getElementById('add-slot-btn')?.addEventListener('click', () => {
  const day = parseInt(document.getElementById('new-slot-day').value, 10);
  const start = document.getElementById('new-slot-start').value;
  const end = document.getElementById('new-slot-end').value;
  if (!start || !end) return;
  availabilitySlots.push({ day_of_week: day, start_time: start, end_time: end });
  renderAvailabilitySlots();
});

document.getElementById('save-availability-btn')?.addEventListener('click', async () => {
  const msg = document.getElementById('availability-message');
  try {
    await updateInstructorAvailability(availabilitySlots.map((s) => ({
      day_of_week: s.day_of_week,
      start_time: s.start_time.length === 5 ? s.start_time : s.start_time.substring(0, 5),
      end_time: s.end_time.length === 5 ? s.end_time : s.end_time.substring(0, 5),
    })));
    msg.textContent = 'Saved.';
    msg.classList.remove('text-danger');
    msg.classList.add('text-success');
    setTimeout(() => { msg.textContent = ''; }, 3000);
  } catch (err) {
    msg.textContent = err.response?.data?.message || 'Error saving.';
    msg.classList.add('text-danger');
  }
});

// ---------- Calendar ----------
let calendarYear = new Date().getFullYear();
let calendarMonth = new Date().getMonth();
let calendarBookings = [];

const MONTH_NAMES = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

function getBookingsByDate(bookings) {
  const byDate = {};
  (bookings || []).forEach((b) => {
    const iso = b.scheduled_at;
    if (!iso) return;
    const d = new Date(iso);
    const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    if (!byDate[key]) byDate[key] = [];
    byDate[key].push(b);
  });
  return byDate;
}

function hasAvailabilityOnDay(dayOfWeek) {
  if (!profileData?.availability_slots?.length) return false;
  return profileData.availability_slots.some((s) => Number(s.day_of_week) === dayOfWeek);
}

function renderCalendar() {
  const labelEl = document.getElementById('calendar-month-label');
  const gridEl = document.getElementById('calendar-grid');
  const weekdaysEl = document.getElementById('calendar-weekdays');
  if (!gridEl || !weekdaysEl) return;

  labelEl.textContent = `${MONTH_NAMES[calendarMonth]} ${calendarYear}`;

  weekdaysEl.innerHTML = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map((d) => `<div>${d}</div>`).join('');

  const first = new Date(calendarYear, calendarMonth, 1);
  const last = new Date(calendarYear, calendarMonth + 1, 0);
  const startPad = first.getDay();
  const daysInMonth = last.getDate();
  const totalCells = startPad + daysInMonth + (7 - ((startPad + daysInMonth) % 7)) % 7 || 7;
  const prevMonthDays = startPad;
  const prevMonth = calendarMonth === 0 ? 11 : calendarMonth - 1;
  const prevYear = calendarMonth === 0 ? calendarYear - 1 : calendarYear;
  const prevMonthLast = new Date(prevYear, prevMonth + 1, 0).getDate();

  const today = new Date();
  const todayKey = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
  const bookingsByDate = getBookingsByDate(calendarBookings);

  let html = '';
  for (let i = 0; i < totalCells; i++) {
    let dateKey;
    let dayNum;
    let isOtherMonth = false;
    let dayOfWeek;

    if (i < startPad) {
      dayNum = prevMonthLast - startPad + i + 1;
      dateKey = `${prevYear}-${String(prevMonth + 1).padStart(2, '0')}-${String(dayNum).padStart(2, '0')}`;
      isOtherMonth = true;
      dayOfWeek = (i % 7);
    } else if (i < startPad + daysInMonth) {
      dayNum = i - startPad + 1;
      dateKey = `${calendarYear}-${String(calendarMonth + 1).padStart(2, '0')}-${String(dayNum).padStart(2, '0')}`;
      dayOfWeek = (i % 7);
    } else {
      dayNum = i - startPad - daysInMonth + 1;
      const nextMonth = calendarMonth === 11 ? 0 : calendarMonth + 1;
      const nextYear = calendarMonth === 11 ? calendarYear + 1 : calendarYear;
      dateKey = `${nextYear}-${String(nextMonth + 1).padStart(2, '0')}-${String(dayNum).padStart(2, '0')}`;
      isOtherMonth = true;
      dayOfWeek = (i % 7);
    }

    const dayBookings = bookingsByDate[dateKey] || [];
    const hasBooking = dayBookings.length > 0;
    const available = hasAvailabilityOnDay(dayOfWeek);
    const isPast = new Date(dateKey) < new Date(today.getFullYear(), today.getMonth(), today.getDate());
    const isToday = dateKey === todayKey;

    let classes = 'instructor-calendar-day';
    if (isOtherMonth) classes += ' other-month';
    if (isToday) classes += ' today';
    if (isPast) classes += ' past';
    if (available) classes += ' available';
    if (hasBooking) classes += ' has-booking';
    if (!isOtherMonth) classes += ' clickable';

    const eventsHtml = hasBooking
      ? `<div class="day-events">${dayBookings.map((b) => `<div title="${escapeHtml(b.learner?.name || '')} ${b.scheduled_at || ''}">● ${(b.scheduled_at ? new Date(b.scheduled_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '')} ${b.learner?.name || ''}</div>`).join('')}</div>`
      : '';

    html += `<div class="${classes}" data-date="${dateKey}" data-has-booking="${hasBooking ? '1' : '0'}">`;
    html += `<span class="day-num">${dayNum}</span>${eventsHtml}</div>`;
  }
  gridEl.innerHTML = html;

  gridEl.querySelectorAll('.instructor-calendar-day.clickable').forEach((el) => {
    el.addEventListener('click', () => {
      const dateKey = el.dataset.date;
      showDayDetail(dateKey, bookingsByDate[dateKey] || []);
    });
  });
}

function showDayDetail(dateKey, dayBookings) {
  const detail = document.getElementById('calendar-day-detail');
  const dateLabel = document.getElementById('calendar-day-detail-date');
  const body = document.getElementById('calendar-day-detail-body');
  if (!detail || !dateLabel || !body) return;

  const d = new Date(dateKey + 'T12:00:00');
  dateLabel.textContent = d.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

  if (dayBookings.length === 0) {
    body.innerHTML = '<p class="text-muted mb-0">No bookings this day.</p>';
  } else {
    body.innerHTML = dayBookings.map((b) => {
      const time = b.scheduled_at ? new Date(b.scheduled_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '—';
      return `<p class="mb-1"><strong>${time}</strong> ${escapeHtml(b.learner?.name || '—')} — ${b.type} <span class="badge bg-${b.status === 'confirmed' ? 'success' : b.status === 'cancelled' ? 'secondary' : 'primary'}">${b.status}</span></p>`;
    }).join('');
  }
  detail.style.display = 'block';
}

async function loadCalendar() {
  try {
    const res = await getBookings();
    calendarBookings = Array.isArray(res.data) ? res.data : (res.data?.data ?? []);
    renderCalendar();
  } catch (err) {
    console.error(err);
    document.getElementById('calendar-grid').innerHTML = '<p class="text-muted">Could not load bookings.</p>';
  }
}

document.getElementById('calendar-prev')?.addEventListener('click', () => {
  if (calendarMonth === 0) { calendarYear--; calendarMonth = 11; } else calendarMonth--;
  renderCalendar();
});
document.getElementById('calendar-next')?.addEventListener('click', () => {
  if (calendarMonth === 11) { calendarYear++; calendarMonth = 0; } else calendarMonth++;
  renderCalendar();
});
document.getElementById('calendar-today')?.addEventListener('click', () => {
  const t = new Date();
  calendarYear = t.getFullYear();
  calendarMonth = t.getMonth();
  loadCalendar();
});

// ---------- Bookings ----------
async function loadBookings() {
  const list = document.getElementById('bookings-list');
  const loading = document.getElementById('bookings-loading');
  try {
    const res = await getBookings();
    loading.style.display = 'none';
    const items = Array.isArray(res.data) ? res.data : (res.data?.data ?? []);
    if (items.length === 0) {
      list.innerHTML = '<p class="text-muted">No bookings yet.</p>';
      return;
    }
    list.innerHTML = `<table class="table table-sm">
      <thead><tr><th>Date & time</th><th>Learner</th><th>Type</th><th>Status</th></tr></thead>
      <tbody>
        ${items.map((b) => `<tr>
          <td>${formatDate(b.scheduled_at)}</td>
          <td>${escapeHtml(b.learner?.name || '—')}</td>
          <td>${b.type}</td>
          <td><span class="badge bg-${b.status === 'confirmed' ? 'success' : b.status === 'cancelled' ? 'secondary' : 'primary'}">${b.status}</span></td>
        </tr>`).join('')}
      </tbody>
    </table>`;
  } catch (err) {
    loading.textContent = 'Error loading bookings.';
  }
}

function formatDate(iso) {
  if (!iso) return '—';
  const d = new Date(iso);
  return d.toLocaleString();
}

function escapeHtml(s) {
  if (s == null) return '';
  const div = document.createElement('div');
  div.textContent = s;
  return div.innerHTML;
}
function escapeAttr(s) {
  if (s == null) return '';
  return String(s).replace(/"/g, '&quot;');
}

// ---------- Tab: load data when switching ----------
document.querySelectorAll('#dashboardTabs button[data-bs-toggle="tab"]').forEach((tab) => {
  tab.addEventListener('shown.bs.tab', (e) => {
    if (e.target.id === 'bookings-tab') loadBookings();
    if (e.target.id === 'calendar-tab') loadCalendar();
  });
});

// ---------- Init ----------
async function init() {
  try {
    await loadProfile();
    if (profileData?.service_areas?.length) {
      serviceAreaIds = profileData.service_areas.map((s) => s.id);
      serviceAreaLabels = profileData.service_areas.map((s) => `${s.name}, ${s.postcode} ${s.state || ''}`);
      renderServiceAreas();
    }
    if (profileData?.availability_slots?.length) {
      availabilitySlots = profileData.availability_slots.map((s) => ({
        day_of_week: s.day_of_week,
        start_time: typeof s.start_time === 'string' ? s.start_time.substring(0, 5) : '09:00',
        end_time: typeof s.end_time === 'string' ? s.end_time.substring(0, 5) : '17:00',
      }));
      renderAvailabilitySlots();
    }
  } catch (err) {
    document.getElementById('profile-loading').textContent = 'Error loading profile.';
    console.error(err);
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
