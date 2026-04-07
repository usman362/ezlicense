import { getInstructorDashboardProfile, updateInstructorServiceAreas, searchSuburbs } from './securelicence-api.js';

let serviceAreaIds = [];
let serviceAreaLabels = [];
let initialIds = [];
let initialLabels = [];

const stateColors = {
  'NSW': 'primary', 'VIC': 'info', 'QLD': 'danger', 'WA': 'success',
  'SA': 'warning', 'TAS': 'secondary', 'ACT': 'dark', 'NT': 'primary'
};

function escapeHtml(s) {
  if (s == null || s === '') return '';
  const div = document.createElement('div');
  div.textContent = s;
  return div.innerHTML;
}

function extractState(label) {
  if (!label) return '';
  const parts = label.split(/[\s,]+/);
  return parts[parts.length - 1] || '';
}

function updateStats() {
  const stateSet = new Set();
  serviceAreaLabels.forEach(label => {
    const state = extractState(label);
    if (state) stateSet.add(state);
  });
  const suburbsEl = document.getElementById('stat-suburbs');
  const statesEl = document.getElementById('stat-states');
  if (suburbsEl) suburbsEl.textContent = serviceAreaIds.length;
  if (statesEl) statesEl.textContent = stateSet.size;
}

function updateSummary() {
  const n = serviceAreaIds.length;
  const el = document.getElementById('service-area-summary');
  if (el) el.innerHTML = 'You are servicing <strong>' + n + '</strong> suburb' + (n === 1 ? '' : 's') + ' across Australia';
}

function updateViewAllDropdown() {
  const list = document.getElementById('view-all-suburbs-list');
  if (!list) return;
  if (serviceAreaLabels.length === 0) {
    list.innerHTML = '<li><span class="dropdown-item text-muted">No suburbs selected</span></li>';
    return;
  }
  // Group by state
  const grouped = {};
  serviceAreaLabels.forEach((label) => {
    const state = extractState(label);
    if (!grouped[state]) grouped[state] = [];
    grouped[state].push(label);
  });
  let html = '';
  Object.keys(grouped).sort().forEach((state) => {
    html += '<li><h6 class="dropdown-header">' + escapeHtml(state) + '</h6></li>';
    grouped[state].forEach((label) => {
      html += '<li><span class="dropdown-item small">' + escapeHtml(label) + '</span></li>';
    });
  });
  list.innerHTML = html;
}

function hasChanges() {
  if (initialIds.length !== serviceAreaIds.length) return true;
  const set = new Set(initialIds);
  for (let i = 0; i < serviceAreaIds.length; i++) {
    if (!set.has(serviceAreaIds[i])) return true;
  }
  return false;
}

function toggleUnsavedBanner() {
  const banner = document.getElementById('unsaved-banner');
  if (banner) {
    banner.style.display = hasChanges() ? 'flex' : 'none';
    banner.style.setProperty('display', hasChanges() ? 'flex' : 'none', 'important');
  }
}

function renderChips() {
  const container = document.getElementById('service-area-chips');
  const emptyEl = document.getElementById('service-area-chips-empty');
  if (!container) return;

  container.innerHTML = serviceAreaIds.map((id, i) => {
    const label = serviceAreaLabels[i] || 'ID ' + id;
    const state = extractState(label);
    const color = stateColors[state] || 'secondary';
    return '<span class="badge bg-' + color + ' bg-opacity-10 text-' + color + ' border border-' + color + ' border-opacity-25 d-inline-flex align-items-center gap-1 px-2 py-2" style="font-weight:500;">' +
      escapeHtml(label) +
      ' <button type="button" class="btn-close btn-close-sm ms-1 remove-area" data-id="' + id + '" aria-label="Remove" style="font-size:0.6rem;"></button></span>';
  }).join('');

  if (emptyEl) emptyEl.style.display = serviceAreaIds.length ? 'none' : 'block';
  updateSummary();
  updateViewAllDropdown();
  updateStats();
  toggleUnsavedBanner();

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

async function saveChanges() {
  const msg = document.getElementById('areas-message');
  try {
    await updateInstructorServiceAreas(serviceAreaIds);
    initialIds = serviceAreaIds.slice();
    initialLabels = serviceAreaLabels.slice();
    toggleUnsavedBanner();
    if (msg) {
      msg.textContent = 'Changes saved successfully!';
      msg.className = 'small text-success fw-semibold';
      setTimeout(() => { msg.textContent = ''; }, 3000);
    }
  } catch (err) {
    if (msg) {
      msg.textContent = err.response?.data?.message || 'Error saving changes.';
      msg.className = 'small text-danger fw-semibold';
    }
  }
}

const suburbAddInput = document.getElementById('suburb-add-input');
const suburbSuggestions = document.getElementById('suburb-suggestions');

function showSuggestions(items) {
  if (!suburbSuggestions) return;
  suburbSuggestions.innerHTML = (items || []).map((s) => {
    const label = s.label || (s.name + ', ' + (s.postcode || '') + ' ' + (s.state || ''));
    const already = serviceAreaIds.includes(s.id);
    return '<li class="list-group-item list-group-item-action suburb-suggestion d-flex justify-content-between align-items-center' + (already ? ' bg-light' : '') + '" data-id="' + s.id + '" data-label="' + escapeHtml(label).replace(/"/g, '&quot;') + '">' +
      '<span>' + escapeHtml(label) + '</span>' +
      (already ? '<span class="badge bg-success-subtle text-success">Added</span>' : '<span class="badge bg-primary-subtle text-primary">Add</span>') +
      '</li>';
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

// Quick add by postcode
document.getElementById('postcode-add-btn')?.addEventListener('click', async () => {
  const postcode = (document.getElementById('postcode-add-input')?.value || '').trim();
  if (postcode.length < 3) return;
  const data = await searchSuburbs(postcode);
  let added = 0;
  (data || []).forEach((s) => {
    if (!serviceAreaIds.includes(s.id)) {
      const label = s.label || (s.name + ', ' + (s.postcode || '') + ' ' + (s.state || ''));
      serviceAreaIds.push(s.id);
      serviceAreaLabels.push(label);
      added++;
    }
  });
  if (added > 0) renderChips();
  const msg = document.getElementById('areas-message');
  if (msg) {
    msg.textContent = added > 0 ? added + ' suburb' + (added > 1 ? 's' : '') + ' added for postcode ' + postcode : 'No new suburbs found for ' + postcode;
    msg.className = 'small ' + (added > 0 ? 'text-success' : 'text-muted');
    setTimeout(() => { msg.textContent = ''; }, 3000);
  }
  document.getElementById('postcode-add-input').value = '';
});

document.getElementById('discard-areas-btn')?.addEventListener('click', () => {
  discardChanges();
});

document.getElementById('save-areas-btn')?.addEventListener('click', saveChanges);
document.getElementById('save-areas-btn-top')?.addEventListener('click', saveChanges);

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
