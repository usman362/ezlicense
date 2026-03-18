import { getInstructorDashboardProfile, updateInstructorAvailability } from './ezlicense-api.js';

// Display order: Monday (1) ... Sunday (0) to match backend day_of_week 0-6
const DAY_ORDER = [1, 2, 3, 4, 5, 6, 0];
const DAY_NAMES = { 0: 'Sunday', 1: 'Monday', 2: 'Tuesday', 3: 'Wednesday', 4: 'Thursday', 5: 'Friday', 6: 'Saturday' };

let slots = [];
let initialSlots = [];

// Generate time options in 15-minute increments (05:00 to 22:00)
function generateTimeOptions() {
  const options = [];
  for (let h = 5; h <= 22; h++) {
    for (let m = 0; m < 60; m += 15) {
      if (h === 22 && m > 0) break; // Stop at 22:00
      const hh = String(h).padStart(2, '0');
      const mm = String(m).padStart(2, '0');
      const value = `${hh}:${mm}`;
      // Format for display: 05:00am, 01:30pm, etc.
      const hour12 = h % 12 || 12;
      const ampm = h < 12 ? 'am' : 'pm';
      const label = `${String(hour12).padStart(2, '0')}:${mm}${ampm}`;
      options.push({ value, label });
    }
  }
  return options;
}

const TIME_OPTIONS = generateTimeOptions();

function buildTimeSelect(selectedValue, cssClass) {
  const normalized = toTimeStr(selectedValue);
  return `<select class="form-select form-select-sm ${cssClass}">` +
    TIME_OPTIONS.map(opt =>
      `<option value="${opt.value}"${opt.value === normalized ? ' selected' : ''}>${opt.label}</option>`
    ).join('') +
    `</select>`;
}

function toTimeStr(hhmm) {
  if (!hhmm || hhmm.length < 5) return '09:00';
  return hhmm.substring(0, 5);
}

// Snap a time value to nearest 15-minute increment
function snapTo15(timeStr) {
  const [h, m] = timeStr.split(':').map(Number);
  const snapped = Math.round(m / 15) * 15;
  const finalH = snapped >= 60 ? h + 1 : h;
  const finalM = snapped >= 60 ? 0 : snapped;
  return `${String(finalH).padStart(2, '0')}:${String(finalM).padStart(2, '0')}`;
}

function slotDurationMinutes(start, end) {
  const [sh, sm] = start.split(':').map(Number);
  const [eh, em] = end.split(':').map(Number);
  return (eh * 60 + em) - (sh * 60 + sm);
}

function getTip(start, end) {
  const mins = slotDurationMinutes(start, end);
  if (mins <= 60) return 'Tip: Extend by 30 mins to allow for 2 lessons instead of 1 lesson';
  if (mins <= 150) return 'Tip: Extend by 30 mins to allow for more lessons';
  if (mins <= 300) return 'Tip: Extend by 60 mins to allow for more lessons';
  return null;
}

function slotsByDay() {
  const byDay = {};
  DAY_ORDER.forEach((d) => { byDay[d] = []; });
  slots.forEach((s) => {
    if (byDay[s.day_of_week]) byDay[s.day_of_week].push(s);
  });
  return byDay;
}

function hasChanges() {
  if (initialSlots.length !== slots.length) return true;
  const a = JSON.stringify(initialSlots.map((s) => ({ d: s.day_of_week, s: s.start_time, e: s.end_time })).sort((x, y) => x.d - y.d || x.s.localeCompare(y.s)));
  const b = JSON.stringify(slots.map((s) => ({ d: s.day_of_week, s: s.start_time, e: s.end_time })).sort((x, y) => x.d - y.d || x.s.localeCompare(y.s)));
  return a !== b;
}

function addSlot(dayOfWeek) {
  slots.push({ day_of_week: dayOfWeek, start_time: '09:00', end_time: '17:00' });
  render();
}

function removeSlot(dayOfWeek, slotIndex) {
  const byDay = slots.filter((s) => s.day_of_week === dayOfWeek);
  const toRemove = byDay[slotIndex];
  if (!toRemove) return;
  const idx = slots.findIndex((s) => s === toRemove);
  if (idx !== -1) slots.splice(idx, 1);
  render();
}

function copySlotToAllDays(dayOfWeek, slotIndex) {
  const byDay = slots.filter((s) => s.day_of_week === dayOfWeek);
  const source = byDay[slotIndex];
  if (!source) return;
  DAY_ORDER.forEach((d) => {
    if (d === dayOfWeek) return;
    slots.push({ day_of_week: d, start_time: source.start_time, end_time: source.end_time });
  });
  render();
}

function render() {
  const container = document.getElementById('opening-hours-container');
  const loading = document.getElementById('opening-hours-loading');
  const discardBtn = document.getElementById('discard-hours-btn');
  if (!container) return;

  const byDay = slotsByDay();
  container.innerHTML = DAY_ORDER.map((dayKey) => {
    const daySlots = byDay[dayKey];
    const dayName = DAY_NAMES[dayKey];
    const rows = (daySlots.length || 0) === 0
      ? `<div class="text-muted small py-2">No availability set for this day.</div>`
      : daySlots.map((slot, slotIdx) => {
          const tip = getTip(slot.start_time, slot.end_time);
          return `<div class="slot-row d-flex align-items-center gap-2 mb-2" data-day="${dayKey}" data-slot-index="${slotIdx}">
            <div style="width:150px;">${buildTimeSelect(slot.start_time, 'slot-start')}</div>
            <span class="text-muted small">to</span>
            <div style="width:150px;">${buildTimeSelect(slot.end_time, 'slot-end')}</div>
            <div class="d-flex gap-1 ms-2">
              <button type="button" class="btn btn-sm btn-outline-danger slot-remove" aria-label="Remove" title="Remove slot"><i class="bi bi-trash"></i></button>
              <button type="button" class="btn btn-sm btn-outline-secondary slot-copy-all" aria-label="Copy to all days" title="Copy to all days"><i class="bi bi-files"></i></button>
            </div>
            ${tip ? '<span class="small text-muted ms-2"><i class="bi bi-lightbulb me-1"></i>' + tip + '</span>' : ''}
          </div>`;
        }).join('');
    return `<div class="mb-4 pb-3 border-bottom">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0 fw-semibold">${dayName}</h6>
        <button type="button" class="btn btn-sm btn-outline-primary add-day-slot" data-day="${dayKey}"><i class="bi bi-plus-lg me-1"></i>Add Slot</button>
      </div>
      ${rows}
    </div>`;
  }).join('');

  container.querySelectorAll('.slot-start').forEach((select) => {
    select.addEventListener('change', () => {
      const row = select.closest('.slot-row');
      const day = parseInt(row.getAttribute('data-day'), 10);
      const slotIndex = parseInt(row.getAttribute('data-slot-index'), 10);
      const byDay = slots.filter((s) => s.day_of_week === day);
      const slot = byDay[slotIndex];
      if (slot) {
        slot.start_time = select.value;
        render();
      }
    });
  });
  container.querySelectorAll('.slot-end').forEach((select) => {
    select.addEventListener('change', () => {
      const row = select.closest('.slot-row');
      const day = parseInt(row.getAttribute('data-day'), 10);
      const slotIndex = parseInt(row.getAttribute('data-slot-index'), 10);
      const byDay = slots.filter((s) => s.day_of_week === day);
      const slot = byDay[slotIndex];
      if (slot) {
        slot.end_time = select.value;
        render();
      }
    });
  });
  container.querySelectorAll('.slot-remove').forEach((btn) => {
    btn.addEventListener('click', () => {
      const row = btn.closest('.slot-row');
      const day = parseInt(row.getAttribute('data-day'), 10);
      const slotIndex = parseInt(row.getAttribute('data-slot-index'), 10);
      removeSlot(day, slotIndex);
    });
  });
  container.querySelectorAll('.slot-copy-all').forEach((btn) => {
    btn.addEventListener('click', () => {
      const row = btn.closest('.slot-row');
      const day = parseInt(row.getAttribute('data-day'), 10);
      const slotIndex = parseInt(row.getAttribute('data-slot-index'), 10);
      copySlotToAllDays(day, slotIndex);
    });
  });
  container.querySelectorAll('.add-day-slot').forEach((btn) => {
    btn.addEventListener('click', () => {
      const day = parseInt(btn.getAttribute('data-day'), 10);
      addSlot(day);
    });
  });

  if (loading) loading.style.display = 'none';
  container.style.display = 'block';
  if (discardBtn) discardBtn.style.display = hasChanges() ? 'inline' : 'none';
}

document.getElementById('discard-hours-btn')?.addEventListener('click', () => {
  slots = initialSlots.map((s) => ({ day_of_week: s.day_of_week, start_time: s.start_time, end_time: s.end_time }));
  render();
});

document.getElementById('save-availability-btn')?.addEventListener('click', async () => {
  const msg = document.getElementById('availability-message');
  try {
    await updateInstructorAvailability(slots.map((s) => ({
      day_of_week: s.day_of_week,
      start_time: toTimeStr(s.start_time),
      end_time: toTimeStr(s.end_time),
    })));
    initialSlots = slots.map((s) => ({ day_of_week: s.day_of_week, start_time: s.start_time, end_time: s.end_time }));
    document.getElementById('discard-hours-btn').style.display = 'none';
    msg.textContent = 'Saved.';
    msg.className = 'ms-3 text-success';
  } catch (err) {
    msg.textContent = err.response?.data?.message || 'Error saving.';
    msg.className = 'ms-3 text-danger';
  }
});

(async () => {
  const data = await getInstructorDashboardProfile();
  if (data.availability_slots?.length) {
    slots = data.availability_slots.map((s) => ({
      day_of_week: s.day_of_week,
      start_time: snapTo15(toTimeStr(s.start_time)),
      end_time: snapTo15(toTimeStr(s.end_time)),
    }));
  } else {
    slots = [];
  }
  initialSlots = slots.map((s) => ({ day_of_week: s.day_of_week, start_time: s.start_time, end_time: s.end_time }));
  render();
})();
