import { getInstructorDashboardProfile, updateInstructorProfile } from './ezlicense-api.js';

let profileData = null;

function formatPrice(val) {
  if (val == null || val === '' || isNaN(Number(val))) return '$0.00';
  return '$' + Number(val).toFixed(2);
}

function showEdit(displayId, editWrapId, inputId, value) {
  document.getElementById(displayId).style.display = 'none';
  document.querySelector('.' + displayId.replace('-display', '-edit')).style.display = 'none';
  const wrap = document.getElementById(editWrapId);
  wrap.classList.remove('d-none');
  wrap.classList.add('d-inline-flex');
  document.getElementById(inputId).value = value ?? '';
}

function hideEdit(displayId, editWrapId) {
  document.getElementById(displayId).style.display = '';
  document.querySelector('.' + displayId.replace('-display', '-edit')).style.display = '';
  const wrap = document.getElementById(editWrapId);
  wrap.classList.add('d-none');
  wrap.classList.remove('d-inline-flex');
}

function renderPrices() {
  const lp = profileData.lesson_price ?? 0;
  const lpp = profileData.lesson_price_private ?? lp;
  const tp = profileData.test_package_price ?? 0;
  const tpp = profileData.test_package_price_private ?? tp;

  document.getElementById('lesson-ezlicence-display').textContent = formatPrice(lp);
  document.getElementById('lesson-private-display').textContent = formatPrice(lpp);
  document.getElementById('test-ezlicence-display').textContent = formatPrice(tp);
  document.getElementById('test-private-display').textContent = formatPrice(tpp);
}

async function savePrice(field, value, displayId, editWrapId, inputId) {
  const msg = document.getElementById('pricing-message');
  const num = parseFloat(value);
  if (isNaN(num) || num < 0) {
    msg.textContent = 'Enter a valid price.';
    msg.className = 'text-danger';
    return;
  }
  try {
    await updateInstructorProfile({
      ...profileData,
      [field]: num,
      transmission: profileData.transmission || 'both',
      lesson_duration_minutes: profileData.lesson_duration_minutes ?? 60,
    });
    profileData[field] = num;
    renderPrices();
    hideEdit(displayId, editWrapId);
    msg.textContent = 'Saved.';
    msg.className = 'text-success';
  } catch (err) {
    msg.textContent = err.response?.data?.message || 'Error saving.';
    msg.className = 'text-danger';
  }
}

async function load() {
  profileData = await getInstructorDashboardProfile();
  document.getElementById('pricing-loading').style.display = 'none';
  document.getElementById('pricing-content').style.display = 'block';
  renderPrices();

  document.querySelector('.lesson-ezlicence-edit').addEventListener('click', () => {
    showEdit('lesson-ezlicence-display', 'lesson-ezlicence-edit-wrap', 'lesson-ezlicence-input', profileData.lesson_price);
  });
  document.querySelector('.lesson-ezlicence-save').addEventListener('click', () => {
    savePrice('lesson_price', document.getElementById('lesson-ezlicence-input').value, 'lesson-ezlicence-display', 'lesson-ezlicence-edit-wrap', 'lesson-ezlicence-input');
  });
  document.querySelector('.lesson-ezlicence-cancel').addEventListener('click', () => {
    hideEdit('lesson-ezlicence-display', 'lesson-ezlicence-edit-wrap');
  });

  document.querySelector('.lesson-private-edit').addEventListener('click', () => {
    showEdit('lesson-private-display', 'lesson-private-edit-wrap', 'lesson-private-input', profileData.lesson_price_private ?? profileData.lesson_price);
  });
  document.querySelector('.lesson-private-save').addEventListener('click', () => {
    savePrice('lesson_price_private', document.getElementById('lesson-private-input').value, 'lesson-private-display', 'lesson-private-edit-wrap', 'lesson-private-input');
  });
  document.querySelector('.lesson-private-cancel').addEventListener('click', () => {
    hideEdit('lesson-private-display', 'lesson-private-edit-wrap');
  });

  document.querySelector('.test-ezlicence-edit').addEventListener('click', () => {
    showEdit('test-ezlicence-display', 'test-ezlicence-edit-wrap', 'test-ezlicence-input', profileData.test_package_price);
  });
  document.querySelector('.test-ezlicence-save').addEventListener('click', () => {
    savePrice('test_package_price', document.getElementById('test-ezlicence-input').value, 'test-ezlicence-display', 'test-ezlicence-edit-wrap', 'test-ezlicence-input');
  });
  document.querySelector('.test-ezlicence-cancel').addEventListener('click', () => {
    hideEdit('test-ezlicence-display', 'test-ezlicence-edit-wrap');
  });

  document.querySelector('.test-private-edit').addEventListener('click', () => {
    showEdit('test-private-display', 'test-private-edit-wrap', 'test-private-input', profileData.test_package_price_private ?? profileData.test_package_price);
  });
  document.querySelector('.test-private-save').addEventListener('click', () => {
    savePrice('test_package_price_private', document.getElementById('test-private-input').value, 'test-private-display', 'test-private-edit-wrap', 'test-private-input');
  });
  document.querySelector('.test-private-cancel').addEventListener('click', () => {
    hideEdit('test-private-display', 'test-private-edit-wrap');
  });
}

load();
