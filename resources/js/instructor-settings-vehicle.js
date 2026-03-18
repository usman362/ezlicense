import { getInstructorDashboardProfile, updateInstructorProfile, uploadInstructorVehiclePhoto } from './ezlicense-api.js';
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

function showVehiclePhoto(url) {
  const icon = document.getElementById('vehicle-photo-icon');
  const img = document.getElementById('vehicle-photo-img');
  if (url) {
    img.src = url;
    img.classList.remove('d-none');
    if (icon) icon.classList.add('d-none');
  } else {
    img.classList.add('d-none');
    if (icon) icon.classList.remove('d-none');
  }
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

  // Show existing vehicle photo
  showVehiclePhoto(profileData.vehicle_photo_url);

  makeSelect.addEventListener('change', () => {
    const make = makeSelect.value;
    populateModelDropdown(modelSelect, make, '');
  });
}

// Vehicle photo upload
const vehiclePhotoInput = document.getElementById('vehicle-photo-input');
const vehiclePhotoUploadBtn = document.getElementById('vehicle-photo-upload-btn');

vehiclePhotoInput?.addEventListener('change', () => {
  if (vehiclePhotoInput.files.length > 0) {
    vehiclePhotoUploadBtn.classList.remove('d-none');
    // Show preview
    const reader = new FileReader();
    reader.onload = (e) => showVehiclePhoto(e.target.result);
    reader.readAsDataURL(vehiclePhotoInput.files[0]);
  } else {
    vehiclePhotoUploadBtn.classList.add('d-none');
  }
});

vehiclePhotoUploadBtn?.addEventListener('click', async () => {
  const file = vehiclePhotoInput.files[0];
  if (!file) return;
  const msg = document.getElementById('vehicle-photo-message');
  vehiclePhotoUploadBtn.disabled = true;
  vehiclePhotoUploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Uploading...';
  try {
    const result = await uploadInstructorVehiclePhoto(file);
    showVehiclePhoto(result.vehicle_photo_url);
    msg.textContent = 'Photo uploaded!';
    msg.className = 'small ms-2 text-success';
    vehiclePhotoUploadBtn.classList.add('d-none');
    vehiclePhotoInput.value = '';
  } catch (err) {
    msg.textContent = err.response?.data?.message || 'Upload failed.';
    msg.className = 'small ms-2 text-danger';
  } finally {
    vehiclePhotoUploadBtn.disabled = false;
    vehiclePhotoUploadBtn.innerHTML = '<i class="bi bi-upload me-1"></i>Upload Photo';
  }
});

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
