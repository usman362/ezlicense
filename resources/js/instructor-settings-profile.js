import { getInstructorDashboardProfile, updateInstructorProfile } from './ezlicense-api.js';

let profileLanguages = [];

function renderLanguageTags() {
  const container = document.getElementById('profile-language-tags');
  if (!container) return;
  container.innerHTML = profileLanguages.map((lang, i) =>
    '<span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">' +
    escapeHtml(lang) +
    ' <button type="button" class="btn btn-link p-0 border-0 ms-1 text-danger" data-lang-index="' + i + '" aria-label="Remove">×</button></span>'
  ).join('');
  container.querySelectorAll('[data-lang-index]').forEach(btn => {
    btn.addEventListener('click', () => {
      profileLanguages.splice(parseInt(btn.getAttribute('data-lang-index'), 10), 1);
      renderLanguageTags();
    });
  });
}

function escapeHtml(s) {
  if (s == null || s === '') return '';
  const div = document.createElement('div');
  div.textContent = s;
  return div.innerHTML;
}

function updateBioCount() {
  const bio = document.getElementById('profile-bio');
  const countEl = document.getElementById('profile-bio-count');
  if (bio && countEl) countEl.textContent = (bio.value || '').length;
}

async function load() {
  const data = await getInstructorDashboardProfile();
  document.getElementById('profile-loading').style.display = 'none';
  const form = document.getElementById('profile-form');
  form.style.display = 'block';

  form.bio.value = data.bio || '';
  updateBioCount();
  form.bio.addEventListener('input', updateBioCount);

  const baseUrl = window.location.origin;
  const profileUrl = data.id ? baseUrl + '/instructors/' + data.id : '';
  const linkInput = document.getElementById('profile-link-input');
  if (linkInput) linkInput.value = profileUrl;

  profileLanguages = Array.isArray(data.languages) ? [...data.languages] : [];
  renderLanguageTags();

  const langInput = document.getElementById('profile-language-input');
  if (langInput) {
    langInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        const v = (langInput.value || '').trim();
        if (v && !profileLanguages.includes(v) && profileLanguages.length < 20) {
          profileLanguages.push(v);
          renderLanguageTags();
          langInput.value = '';
        }
      }
    });
  }

  form.association_member.value = data.association_member ? '1' : '0';
  form.instructing_start_month.value = data.instructing_start_month || '';
  form.instructing_start_year.value = data.instructing_start_year || '';
  form.service_test_existing.checked = !!data.service_test_existing;
  form.service_test_new.checked = !!data.service_test_new;
  form.service_manual_no_vehicle.checked = !!data.service_manual_no_vehicle;
  form.notification_email_marketing.checked = data.notification_email_marketing !== false;
  form.notification_sms_marketing.checked = data.notification_sms_marketing !== false;
  form.is_active.checked = data.is_active !== false;

}

document.getElementById('profile-copy-link')?.addEventListener('click', () => {
  const input = document.getElementById('profile-link-input');
  if (!input?.value) return;
  input.select();
  navigator.clipboard.writeText(input.value).then(() => {
    const btn = document.getElementById('profile-copy-link');
    const orig = btn.textContent;
    btn.textContent = 'Copied!';
    setTimeout(() => { btn.textContent = orig; }, 2000);
  });
});

document.getElementById('profile-form')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const msg = document.getElementById('profile-message');
  let profileData = {};
  try {
    profileData = await getInstructorDashboardProfile();
  } catch (_) {}
  try {
    await updateInstructorProfile({
      ...profileData,
      bio: form.bio.value,
      languages: profileLanguages,
      association_member: form.association_member.value === '1',
      instructing_start_month: form.instructing_start_month.value ? parseInt(form.instructing_start_month.value, 10) : null,
      instructing_start_year: form.instructing_start_year.value ? parseInt(form.instructing_start_year.value, 10) : null,
      service_test_existing: form.service_test_existing.checked,
      service_test_new: form.service_test_new.checked,
      service_manual_no_vehicle: form.service_manual_no_vehicle.checked,
      notification_email_marketing: form.notification_email_marketing.checked,
      notification_sms_marketing: form.notification_sms_marketing.checked,
      transmission: profileData.transmission || 'both',
      lesson_duration_minutes: profileData.lesson_duration_minutes ?? 60,
      lesson_price: profileData.lesson_price ?? 0,
      is_active: form.is_active.checked,
    });
    msg.textContent = 'Saved.';
    msg.className = 'me-3 align-self-center text-success';
  } catch (err) {
    msg.textContent = err.response?.data?.message || 'Error saving.';
    msg.className = 'me-3 align-self-center text-danger';
  }
});

load();
