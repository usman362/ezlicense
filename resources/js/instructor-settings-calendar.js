import { getInstructorDashboardProfile, updateInstructorCalendarSettings } from './securelicence-api.js';

let initialData = null;

function getFormData() {
  const form = document.getElementById('calendar-settings-form');
  if (!form) return null;
  return {
    travel_buffer_same_mins: parseInt(form.travel_buffer_same_mins?.value, 10) || 30,
    travel_buffer_synced_mins: parseInt(form.travel_buffer_synced_mins?.value, 10) || 30,
    min_prior_notice_hours: parseInt(form.min_prior_notice_hours?.value, 10) ?? 5,
    max_advance_notice_days: parseInt(form.max_advance_notice_days?.value, 10) ?? 75,
    smart_scheduling_enabled: form.smart_scheduling_enabled?.checked ?? true,
    smart_scheduling_buffer_hrs: parseInt(form.smart_scheduling_buffer_hrs?.value, 10) || 1,
    attach_ics_to_emails: form.attach_ics_to_emails?.value === '1',
    default_calendar_view: form.default_calendar_view?.value || 'day',
  };
}

function setFormData(data) {
  const form = document.getElementById('calendar-settings-form');
  if (!form || !data) return;
  const setVal = (name, val) => {
    const els = form.querySelectorAll('[name="' + name + '"]');
    els.forEach((el) => {
      if (el.type === 'radio') el.checked = (String(el.value) === String(val));
      else el.value = val;
    });
  };
  const setCheck = (name, checked) => {
    const el = form.querySelector('[name="' + name + '"]');
    if (el) el.checked = !!checked;
  };
  setVal('travel_buffer_same_mins', data.travel_buffer_same_mins ?? 30);
  setVal('travel_buffer_synced_mins', data.travel_buffer_synced_mins ?? 30);
  setVal('min_prior_notice_hours', data.min_prior_notice_hours ?? 5);
  setVal('max_advance_notice_days', data.max_advance_notice_days ?? 75);
  setCheck('smart_scheduling_enabled', data.smart_scheduling_enabled !== false);
  setVal('smart_scheduling_buffer_hrs', data.smart_scheduling_buffer_hrs ?? 1);
  setVal('attach_ics_to_emails', data.attach_ics_to_emails !== false ? '1' : '0');
  setVal('default_calendar_view', data.default_calendar_view || 'day');
}

function hasChanges() {
  const current = getFormData();
  if (!initialData || !current) return false;
  return JSON.stringify(current) !== JSON.stringify(initialData);
}

function updateDiscardButton() {
  const btn = document.getElementById('discard-calendar-btn');
  if (btn) btn.style.display = hasChanges() ? 'inline' : 'none';
}

async function load() {
  const data = await getInstructorDashboardProfile();
  document.getElementById('calendar-settings-loading').style.display = 'none';
  document.getElementById('calendar-settings-form').style.display = 'block';
  initialData = {
    travel_buffer_same_mins: data.travel_buffer_same_mins ?? 30,
    travel_buffer_synced_mins: data.travel_buffer_synced_mins ?? 30,
    min_prior_notice_hours: data.min_prior_notice_hours ?? 5,
    max_advance_notice_days: data.max_advance_notice_days ?? 75,
    smart_scheduling_enabled: data.smart_scheduling_enabled !== false,
    smart_scheduling_buffer_hrs: data.smart_scheduling_buffer_hrs ?? 1,
    attach_ics_to_emails: data.attach_ics_to_emails !== false,
    default_calendar_view: data.default_calendar_view || 'day',
  };
  setFormData(initialData);
  updateDiscardButton();

  const form = document.getElementById('calendar-settings-form');
  form.querySelectorAll('select, input').forEach((el) => {
    el.addEventListener('change', updateDiscardButton);
    el.addEventListener('input', updateDiscardButton);
  });
}

document.getElementById('discard-calendar-btn')?.addEventListener('click', () => {
  setFormData(initialData);
  updateDiscardButton();
});

document.getElementById('save-calendar-btn')?.addEventListener('click', async () => {
  const msg = document.getElementById('calendar-settings-message');
  const payload = getFormData();
  if (!payload) return;
  try {
    await updateInstructorCalendarSettings({
      travel_buffer_same_mins: payload.travel_buffer_same_mins,
      travel_buffer_synced_mins: payload.travel_buffer_synced_mins,
      min_prior_notice_hours: payload.min_prior_notice_hours,
      max_advance_notice_days: payload.max_advance_notice_days,
      smart_scheduling_enabled: payload.smart_scheduling_enabled,
      smart_scheduling_buffer_hrs: payload.smart_scheduling_buffer_hrs,
      attach_ics_to_emails: payload.attach_ics_to_emails,
      default_calendar_view: payload.default_calendar_view,
    });
    initialData = { ...payload };
    updateDiscardButton();
    msg.textContent = 'Saved.';
    msg.className = 'ms-3 text-success';
  } catch (err) {
    msg.textContent = err.response?.data?.message || 'Error saving.';
    msg.className = 'ms-3 text-danger';
  }
});

load();

// Calendar sync section
(async function loadCalendarUrls() {
    try {
        const resp = await fetch('/api/calendar/subscribe-urls', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
        });
        if (!resp.ok) return;
        const data = await resp.json();

        const appleBtn = document.getElementById('apple-cal-btn');
        const googleBtn = document.getElementById('google-cal-btn');
        const feedUrl = document.getElementById('calendar-feed-url');

        if (appleBtn) appleBtn.href = data.webcal_url || '#';
        if (googleBtn) googleBtn.href = data.google_url || '#';
        if (feedUrl) feedUrl.textContent = data.https_url || 'Not available';
    } catch (e) {
        console.log('Calendar sync not loaded:', e);
    }
})();

document.getElementById('copy-cal-url-btn')?.addEventListener('click', function() {
    const url = document.getElementById('calendar-feed-url')?.textContent;
    if (url && url !== 'Loading...' && url !== 'Not available') {
        navigator.clipboard.writeText(url).then(() => {
            this.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
            setTimeout(() => { this.innerHTML = '<i class="bi bi-clipboard me-1"></i>Copy URL'; }, 2000);
        });
    }
});

document.getElementById('regenerate-cal-btn')?.addEventListener('click', async function() {
    if (!confirm('Regenerating will invalidate any previously subscribed calendars. Continue?')) return;
    try {
        const resp = await fetch('/api/calendar/regenerate-token', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
        });
        if (!resp.ok) return;
        const data = await resp.json();

        const appleBtn = document.getElementById('apple-cal-btn');
        const googleBtn = document.getElementById('google-cal-btn');
        const feedUrl = document.getElementById('calendar-feed-url');

        if (appleBtn) appleBtn.href = data.webcal_url || '#';
        if (googleBtn) googleBtn.href = data.google_url || '#';
        if (feedUrl) feedUrl.textContent = data.https_url || 'Not available';

        alert('Calendar URL regenerated. You will need to re-subscribe on your devices.');
    } catch (e) {
        alert('Error regenerating URL.');
    }
});
