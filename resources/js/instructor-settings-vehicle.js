import { getInstructorDashboardProfile, updateInstructorProfile } from './ezlicense-api.js';
import { getMakes, getModelsForMake } from './vehicle-makes-models.js';

let profileData = null;

function populateMakeDropdown(select, selectedMake) {
  let makes = getMakes();
  if (selectedMake && !makes.includes(selectedMake)) makes = [selectedMake].concat(makes);
  select.innerHTML = '<option value="">Select make</option>' +
    makes.map(m => '<option value="' + escapeHtml(m) + '"' + (m === selectedMake ? ' selected' : '') + '>' + escapeHtml(m) + '</option>').join('');
}

function populateModelDropdown(select, make, selectedModel) {
  let models = getModelsForMake(make);
  if (selectedModel && !models.includes(selectedModel)) models = [selectedModel].concat(models);
  select.innerHTML = '<option value="">Select model</option>' +
    models.map(m => '<option value="' + escapeHtml(m) + '"' + (m === selectedModel ? ' selected' : '') + '>' + escapeHtml(m) + '</option>').join('');
  if (!selectedModel && models.length > 0) select.value = '';
}

function escapeHtml(s) {
  if (s == null || s === '') return '';
  const div = document.createElement('div');
  div.textContent = s;
  return div.innerHTML;
}

async function load() {
  profileData = await getInstructorDashboardProfile();
  document.getElementById('vehicle-loading').style.display = 'none';
  const form = document.getElementById('vehicle-form');
  form.style.display = 'block';

  const makeSelect = document.getElementById('vehicle-make');
  const modelSelect = document.getElementById('vehicle-model');
  const yearSelect = document.getElementById('vehicle-year');
  const safetySelect = document.getElementById('vehicle-safety-rating');

  const currentMake = profileData.vehicle_make || '';
  const currentModel = profileData.vehicle_model || '';

  populateMakeDropdown(makeSelect, currentMake);
  populateModelDropdown(modelSelect, currentMake, currentModel);

  form.transmission.value = profileData.transmission || 'both';
  if (yearSelect) yearSelect.value = profileData.vehicle_year || '';
  if (safetySelect) safetySelect.value = profileData.vehicle_safety_rating || '';

  makeSelect.addEventListener('change', () => {
    const make = makeSelect.value;
    populateModelDropdown(modelSelect, make, '');
  });
}

document.getElementById('vehicle-form')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const msg = document.getElementById('vehicle-message');
  try {
    await updateInstructorProfile({
      ...profileData,
      transmission: form.transmission.value || 'both',
      lesson_price: profileData.lesson_price ?? 0,
      lesson_duration_minutes: profileData.lesson_duration_minutes ?? 60,
      vehicle_make: form.vehicle_make.value || null,
      vehicle_model: form.vehicle_model.value || null,
      vehicle_year: form.vehicle_year.value ? parseInt(form.vehicle_year.value, 10) : null,
      vehicle_safety_rating: form.vehicle_safety_rating.value || null,
    });
    msg.textContent = 'Saved.';
    msg.className = 'me-3 align-self-center text-success';
  } catch (err) {
    msg.textContent = err.response?.data?.message || 'Error saving.';
    msg.className = 'me-3 align-self-center text-danger';
  }
});

load();
