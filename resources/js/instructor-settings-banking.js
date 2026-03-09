import { getInstructorDashboardProfile, updateInstructorBanking } from './ezlicense-api.js';

let profileData = null;

function escapeHtml(s) {
  if (s == null || s === '') return '—';
  const div = document.createElement('div');
  div.textContent = s;
  return div.innerHTML;
}

function render() {
  const d = profileData || {};
  document.getElementById('billing-business-name').textContent = d.business_name || '—';
  document.getElementById('billing-abn').textContent = d.abn || '—';
  document.getElementById('billing-address').textContent = d.billing_address || '—';
  document.getElementById('billing-gst').textContent = d.gst_registered === true ? 'Yes' : (d.gst_registered === false ? 'No' : '—');
  document.getElementById('billing-suburb').textContent = d.billing_suburb || '—';
  document.getElementById('billing-postcode').textContent = d.billing_postcode || '—';
  document.getElementById('billing-state').textContent = d.billing_state || '—';

  const payoutLabels = { weekly: 'Weekly', fortnightly: 'Fortnightly', every_four_weeks: 'Every 4 weeks' };
  document.getElementById('payout-frequency-display').textContent = payoutLabels[d.payout_frequency] || d.payout_frequency || '—';

  const bankSubmitted = !!d.bank_details_submitted_at;
  document.getElementById('bank-view').style.display = bankSubmitted ? 'block' : 'none';
  document.getElementById('bank-form').classList.toggle('d-none', bankSubmitted);
  if (bankSubmitted) {
    document.getElementById('bank-account-name').textContent = d.bank_account_name || '—';
    document.getElementById('bank-bsb').textContent = d.bank_bsb || '—';
    document.getElementById('bank-account-masked').textContent = d.bank_account_number_masked || '—';
  }
}

function fillForms() {
  const d = profileData || {};
  const billingForm = document.getElementById('billing-form');
  if (billingForm) {
    billingForm.business_name.value = d.business_name || '';
    billingForm.billing_address.value = d.billing_address || '';
    billingForm.abn.value = d.abn || '';
    billingForm.billing_suburb.value = d.billing_suburb || '';
    billingForm.billing_postcode.value = d.billing_postcode || '';
    billingForm.billing_state.value = d.billing_state || '';
    const gstRadios = billingForm.querySelectorAll('input[name="gst_registered"]');
    gstRadios.forEach((r) => { r.checked = (r.value === '1' ? d.gst_registered === true : d.gst_registered === false); });
  }
  const payoutForm = document.getElementById('payout-form');
  if (payoutForm) {
    payoutForm.querySelectorAll('input[name="payout_frequency"]').forEach((r) => {
      r.checked = r.value === (d.payout_frequency || 'weekly');
    });
  }
  const bankForm = document.getElementById('bank-form');
  if (bankForm && !d.bank_details_submitted_at) {
    bankForm.bank_account_name.value = d.bank_account_name || '';
    bankForm.bank_bsb.value = d.bank_bsb || '';
    bankForm.bank_account_number.value = ''; // don't pre-fill for security
  }
}

async function load() {
  profileData = await getInstructorDashboardProfile();
  document.getElementById('banking-loading').style.display = 'none';
  document.getElementById('banking-content').style.display = 'block';
  render();
  fillForms();
}

document.getElementById('billing-edit-btn').addEventListener('click', () => {
  document.getElementById('billing-view').classList.add('d-none');
  document.getElementById('billing-form').classList.remove('d-none');
});

document.getElementById('billing-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const msg = document.getElementById('banking-message');
  try {
    const gstChecked = form.querySelector('input[name="gst_registered"]:checked');
    await updateInstructorBanking({
      business_name: form.business_name.value || null,
      abn: form.abn.value || null,
      billing_address: form.billing_address.value || null,
      gst_registered: gstChecked ? gstChecked.value === '1' : null,
      billing_suburb: form.billing_suburb.value || null,
      billing_postcode: form.billing_postcode.value || null,
      billing_state: form.billing_state.value || null,
    });
    profileData = await getInstructorDashboardProfile();
    render();
    fillForms();
    document.getElementById('billing-view').classList.remove('d-none');
    document.getElementById('billing-form').classList.add('d-none');
    msg.textContent = 'Saved.';
    msg.className = 'text-success';
  } catch (err) {
    msg.textContent = err.response?.data?.message || 'Error saving.';
    msg.className = 'text-danger';
  }
});

document.getElementById('payout-edit-btn').addEventListener('click', () => {
  document.getElementById('payout-view').classList.add('d-none');
  document.getElementById('payout-form').classList.remove('d-none');
});

document.getElementById('payout-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const val = form.payout_frequency.value;
  if (!val) return;
  const msg = document.getElementById('banking-message');
  try {
    await updateInstructorBanking({ payout_frequency: val });
    profileData = await getInstructorDashboardProfile();
    render();
    fillForms();
    document.getElementById('payout-view').classList.remove('d-none');
    document.getElementById('payout-form').classList.add('d-none');
    msg.textContent = 'Saved.';
    msg.className = 'text-success';
  } catch (err) {
    msg.textContent = err.response?.data?.message || 'Error saving.';
    msg.className = 'text-danger';
  }
});

document.getElementById('bank-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const msg = document.getElementById('banking-message');
  try {
    await updateInstructorBanking({
      bank_account_name: form.bank_account_name.value || null,
      bank_bsb: form.bank_bsb.value || null,
      bank_account_number: form.bank_account_number.value || null,
    });
    profileData = await getInstructorDashboardProfile();
    render();
    fillForms();
    msg.textContent = 'Saved.';
    msg.className = 'text-success';
  } catch (err) {
    msg.textContent = err.response?.data?.message || 'Error saving.';
    msg.className = 'text-danger';
  }
});

load();
