import { getInstructorDashboardProfile, updateInstructorAvailability } from './ezlicense-api.js';

// Display order: Monday (1) ... Sunday (0) to match backend day_of_week 0-6
const DAY_ORDER = [1, 2, 3, 4, 5, 6, 0];
const DAY_NAMES = { 0: 'Sunday', 1: 'Monday', 2: 'Tuesday', 3: 'Wednesday', 4: 'Thursday', 5: 'Friday', 6: 'Saturday' };

let slots = [];
let initialSlots = [];

function toTimeStr(hhmm) {
  if (!hhmm || hhmm.length < 5) return '09:00';
  return hhmm.substring(0, 5);
}

function formatTimeForInput(hhmm) {
  return toTimeStr(hhmm);
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
      ? `<tr><td colspan="4" class="text-muted small">No slots. Click + to add.</td></tr>`
      : daySlots.map((slot, slotIdx) => {
          const tip = getTip(slot.start_time, slot.end_time);
          return `<tr class="slot-row" data-day="${dayKey}" data-slot-index="${slotIdx}">
            <td><input type="time" class="form-control form-control-sm slot-start" value="${formatTimeForInput(slot.start_time)}"></td>
            <td><input type="time" class="form-control form-control-sm slot-end" value="${formatTimeForInput(slot.end_time)}"></td>
            <td class="text-nowrap">
              <button type="button" class="btn btn-sm btn-link p-0 text-danger slot-remove" aria-label="Remove">×</button>
              <button type="button" class="btn btn-sm btn-link p-0 slot-add" aria-label="Add slot">+</button>
              <button type="button" class="btn btn-sm btn-link p-0 slot-copy-all" aria-label="Copy to all days" title="Copy to all">⎘</button>
            </td>
            <td class="small text-muted">${tip || ''}</td>
          </tr>`;
        }).join('');
    return `<div class="mb-4">
      <h6 class="mb-2">${dayName}</h6>
      <table class="table table-sm table-borderless mb-0">
        <thead><tr><th>Start</th><th>End</th><th></th><th></th></tr></thead>
        <tbody>${rows}</tbody>
      </table>
      <button type="button" class="btn btn-sm btn-outline-secondary add-day-slot mt-1" data-day="${dayKey}">+ Add slot</button>
    </div>`;
  }).join('');

  container.querySelectorAll('.slot-start').forEach((input) => {
    input.addEventListener('change', () => {
      const row = input.closest('.slot-row');
      const day = parseInt(row.getAttribute('data-day'), 10);
      const slotIndex = parseInt(row.getAttribute('data-slot-index'), 10);
      const byDay = slots.filter((s) => s.day_of_week === day);
      const slot = byDay[slotIndex];
      if (slot) {
        slot.start_time = input.value ? toTimeStr(input.value) : '09:00';
        render();
      }
    });
  });
  container.querySelectorAll('.slot-end').forEach((input) => {
    input.addEventListener('change', () => {
      const row = input.closest('.slot-row');
      const day = parseInt(row.getAttribute('data-day'), 10);
      const slotIndex = parseInt(row.getAttribute('data-slot-index'), 10);
      const byDay = slots.filter((s) => s.day_of_week === day);
      const slot = byDay[slotIndex];
      if (slot) {
        slot.end_time = input.value ? toTimeStr(input.value) : '17:00';
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
  container.querySelectorAll('.slot-add').forEach((btn) => {
    btn.addEventListener('click', () => {
      const row = btn.closest('.slot-row');
      const day = parseInt(row.getAttribute('data-day'), 10);
      addSlot(day);
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
      start_time: toTimeStr(s.start_time),
      end_time: toTimeStr(s.end_time),
    }));
  } else {
    slots = [];
  }
  initialSlots = slots.map((s) => ({ day_of_week: s.day_of_week, start_time: s.start_time, end_time: s.end_time }));
  render();
})();
