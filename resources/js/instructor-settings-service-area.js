import { getInstructorDashboardProfile, updateInstructorServiceAreas, searchSuburbs } from './ezlicense-api.js';

let serviceAreaIds = [];
let serviceAreaLabels = [];
let initialIds = [];
let initialLabels = [];

function escapeHtml(s) {
  if (s == null || s === '') return '';
  const div = document.createElement('div');
  div.textContent = s;
  return div.innerHTML;
}

function updateSummary() {
  const n = serviceAreaIds.length;
  const el = document.getElementById('service-area-summary');
  if (el) el.innerHTML = 'You are servicing <strong>' + n + '</strong> suburb' + (n === 1 ? '' : 's') + '.';
}

function updateViewAllDropdown() {
  const list = document.getElementById('view-all-suburbs-list');
  if (!list) return;
  if (serviceAreaLabels.length === 0) {
    list.innerHTML = '<li><span class="dropdown-item text-muted">No suburbs selected</span></li>';
    return;
  }
  list.innerHTML = serviceAreaLabels.map((label) => '<li><span class="dropdown-item">' + escapeHtml(label) + '</span></li>').join('');
}

function hasChanges() {
  if (initialIds.length !== serviceAreaIds.length) return true;
  const set = new Set(initialIds);
  for (let i = 0; i < serviceAreaIds.length; i++) {
    if (!set.has(serviceAreaIds[i])) return true;
  }
  return false;
}

function renderChips() {
  const container = document.getElementById('service-area-chips');
  const emptyEl = document.getElementById('service-area-chips-empty');
  const discardBtn = document.getElementById('discard-areas-btn');
  if (!container) return;

  container.innerHTML = serviceAreaIds.map((id, i) => {
    const label = serviceAreaLabels[i] || 'ID ' + id;
    return '<span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 px-2 py-2">' +
      escapeHtml(label) +
      ' <button type="button" class="btn btn-link p-0 border-0 ms-1 text-danger remove-area" data-id="' + id + '" aria-label="Remove">×</button></span>';
  }).join('');

  if (emptyEl) emptyEl.style.display = serviceAreaIds.length ? 'none' : 'block';
  updateSummary();
  updateViewAllDropdown();
  if (discardBtn) discardBtn.style.display = hasChanges() ? 'inline' : 'none';

  container.querySelectorAll('.remove-area').forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = parseInt(btn.getAttribute('data-id'), 10);
      const idx = serviceAreaIds.indexOf(id);
      if (idx !== -1) {
        serviceAreaIds.splice(idx, 1);
        serviceAreaLabels.splice(idx, 1);
        renderChips();
      }
    });
  });
}

function discardChanges() {
  serviceAreaIds = initialIds.slice();
  serviceAreaLabels = initialLabels.slice();
  renderChips();
}

const suburbAddInput = document.getElementById('suburb-add-input');
const suburbSuggestions = document.getElementById('suburb-suggestions');

function showSuggestions(items) {
  if (!suburbSuggestions) return;
  suburbSuggestions.innerHTML = (items || []).map((s) => {
    const label = s.label || (s.name + ', ' + (s.postcode || '') + ' ' + (s.state || ''));
    return '<li class="list-group-item list-group-item-action suburb-suggestion" data-id="' + s.id + '" data-label="' + escapeHtml(label).replace(/"/g, '&quot;') + '">' + escapeHtml(label) + '</li>';
  }).join('');
  suburbSuggestions.style.display = (items && items.length) ? 'block' : 'none';
  suburbSuggestions.querySelectorAll('.suburb-suggestion').forEach((li) => {
    li.addEventListener('click', () => {
      const id = parseInt(li.getAttribute('data-id'), 10);
      const label = li.getAttribute('data-label') || '';
      if (!serviceAreaIds.includes(id)) {
        serviceAreaIds.push(id);
        serviceAreaLabels.push(label);
        renderChips();
      }
      suburbAddInput.value = '';
      suburbSuggestions.style.display = 'none';
    });
  });
}

suburbAddInput?.addEventListener('input', async () => {
  const q = suburbAddInput.value.trim();
  if (q.length < 2) { showSuggestions([]); return; }
  const data = await searchSuburbs(q);
  showSuggestions(data);
});
suburbAddInput?.addEventListener('blur', () => setTimeout(() => { if (suburbSuggestions) suburbSuggestions.style.display = 'none'; }, 200));

document.getElementById('suburb-add-btn')?.addEventListener('click', async () => {
  const q = suburbAddInput.value.trim();
  if (q.length < 2) return;
  const data = await searchSuburbs(q);
  if (data.length && !serviceAreaIds.includes(data[0].id)) {
    const label = data[0].label || (data[0].name + ', ' + (data[0].postcode || '') + ' ' + (data[0].state || ''));
    serviceAreaIds.push(data[0].id);
    serviceAreaLabels.push(label);
    renderChips();
    suburbAddInput.value = '';
  }
  showSuggestions([]);
});

document.getElementById('discard-areas-btn')?.addEventListener('click', () => {
  discardChanges();
});

document.getElementById('save-areas-btn')?.addEventListener('click', async () => {
  const msg = document.getElementById('areas-message');
  try {
    await updateInstructorServiceAreas(serviceAreaIds);
    initialIds = serviceAreaIds.slice();
    initialLabels = serviceAreaLabels.slice();
    document.getElementById('discard-areas-btn').style.display = 'none';
    msg.textContent = 'Saved.';
    msg.className = 'ms-3 text-success';
  } catch (err) {
    msg.textContent = err.response?.data?.message || 'Error saving.';
    msg.className = 'ms-3 text-danger';
  }
});

(async () => {
  const data = await getInstructorDashboardProfile();
  if (data.service_areas?.length) {
    serviceAreaIds = data.service_areas.map((s) => s.id);
    serviceAreaLabels = data.service_areas.map((s) => (s.name || '') + ', ' + (s.postcode || '') + ' ' + (s.state || ''));
  }
  initialIds = serviceAreaIds.slice();
  initialLabels = serviceAreaLabels.slice();
  renderChips();
})();
