import { getInstructorDashboardProfile, updateInstructorProfile } from './securelicence-api.js';

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

  document.getElementById('lesson-securelicences-display').textContent = formatPrice(lp);
  document.getElementById('lesson-private-display').textContent = formatPrice(lpp);
  document.getElementById('test-securelicences-display').textContent = formatPrice(tp);
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

  document.querySelector('.lesson-securelicences-edit').addEventListener('click', () => {
    showEdit('lesson-securelicences-display', 'lesson-securelicences-edit-wrap', 'lesson-securelicences-input', profileData.lesson_price);
  });
  document.querySelector('.lesson-securelicences-save').addEventListener('click', () => {
    savePrice('lesson_price', document.getElementById('lesson-securelicences-input').value, 'lesson-securelicences-display', 'lesson-securelicences-edit-wrap', 'lesson-securelicences-input');
  });
  document.querySelector('.lesson-securelicences-cancel').addEventListener('click', () => {
    hideEdit('lesson-securelicences-display', 'lesson-securelicences-edit-wrap');
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

  document.querySelector('.test-securelicences-edit').addEventListener('click', () => {
    showEdit('test-securelicences-display', 'test-securelicences-edit-wrap', 'test-securelicences-input', profileData.test_package_price);
  });
  document.querySelector('.test-securelicences-save').addEventListener('click', () => {
    savePrice('test_package_price', document.getElementById('test-securelicences-input').value, 'test-securelicences-display', 'test-securelicences-edit-wrap', 'test-securelicences-input');
  });
  document.querySelector('.test-securelicences-cancel').addEventListener('click', () => {
    hideEdit('test-securelicences-display', 'test-securelicences-edit-wrap');
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
