(function () {
  const csrf = document.querySelector('meta[name="csrf-token"]');
  const token = csrf ? csrf.getAttribute('content') : '';

  let data = { current: {}, submissions: [] };

  function escapeHtml(s) {
    if (s == null || s === '') return '—';
    const div = document.createElement('div');
    div.textContent = s;
    return div.innerHTML;
  }

  function statusLabel(status) {
    if (status === 'verified') return '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Provided &amp; Verified</span>';
    if (status === 'pending') return '<span class="text-warning">Pending review</span>';
    if (status === 'rejected') return '<span class="text-danger">Rejected</span>';
    return '—';
  }

  function render() {
    const cur = data.current || {};
    const drivers = cur.drivers_licence || {};
    const instructor = cur.instructor_licence || {};
    const wwcc = cur.wwcc || {};

    document.getElementById('doc-drivers-expiry').innerHTML = escapeHtml(drivers.expires_at || '—');
    document.getElementById('doc-drivers-front-status').innerHTML = 'Driver\'s Licence (C) - Front ' + (drivers.front ? statusLabel(drivers.front.status) : '—');
    document.getElementById('doc-drivers-back-status').innerHTML = 'Driver\'s Licence (C) - Back ' + (drivers.back ? statusLabel(drivers.back.status) : '—');

    document.getElementById('doc-instructor-expiry').innerHTML = escapeHtml(instructor.expires_at || '—');
    document.getElementById('doc-instructor-status').innerHTML = 'Driving Instructor\'s Licence (C) ' + (instructor.front || instructor.back ? statusLabel((instructor.front || instructor.back).status) : '—');

    document.getElementById('doc-wwcc-expiry').innerHTML = escapeHtml(wwcc.expires_at || '—');
    document.getElementById('doc-wwcc-status').innerHTML = 'WWCC ' + (wwcc.status ? statusLabel(wwcc.status) : '—');

    const tbody = document.getElementById('submissions-tbody');
    const empty = document.getElementById('submissions-empty');
    const subs = data.submissions || [];
    if (subs.length === 0) {
      tbody.innerHTML = '';
      empty.style.display = 'block';
    } else {
      empty.style.display = 'none';
      tbody.innerHTML = subs.map(function (s) {
        return '<tr><td>' + escapeHtml(s.submission_date) + '</td><td>' + escapeHtml(s.status) + '</td><td>' + escapeHtml(s.document) + '</td></tr>';
      }).join('');
    }
  }

  function load() {
    fetch('/api/instructor/documents', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        data = res.data || res || {};
        document.getElementById('documents-loading').style.display = 'none';
        document.getElementById('panel-your-documents').style.display = 'block';
        render();
      })
      .catch(function () {
        document.getElementById('documents-loading').textContent = 'Could not load documents.';
      });
  }

  document.getElementById('tab-submissions-history').addEventListener('click', function (e) {
    e.preventDefault();
    document.getElementById('tab-your-documents').classList.remove('active');
    this.classList.add('active');
    document.getElementById('panel-your-documents').style.display = 'none';
    document.getElementById('panel-submissions-history').style.display = 'block';
  });
  document.getElementById('tab-your-documents').addEventListener('click', function (e) {
    e.preventDefault();
    document.getElementById('tab-submissions-history').classList.remove('active');
    this.classList.add('active');
    document.getElementById('panel-submissions-history').style.display = 'none';
    document.getElementById('panel-your-documents').style.display = 'block';
  });

  document.querySelectorAll('[data-doc-modal]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const type = this.getAttribute('data-doc-modal');
      const modal = document.getElementById('modal-' + (type === 'drivers_licence' ? 'drivers-licence' : type === 'instructor_licence' ? 'instructor-licence' : 'wwcc'));
      if (modal) {
        const Modal = typeof bootstrap !== 'undefined' && bootstrap.Modal;
        if (Modal) new Modal(modal).show();
      }
    });
  });

  function submitDoc(type, formData) {
    formData.append('type', type);
    formData.append('_token', token);
    fetch('/api/instructor/documents', {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
      body: formData
    })
      .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
      .then(function (res) {
        if (res.ok) {
          if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(function (m) { bootstrap.Modal.getInstance(m) && bootstrap.Modal.getInstance(m).hide(); });
          }
          load();
          alert(res.data.data && res.data.data.message ? res.data.data.message : 'Submitted for review.');
        } else {
          alert(res.data.message || 'Failed to submit.');
        }
      })
      .catch(function () { alert('Failed to submit.'); });
  }

  document.getElementById('submit-drivers-licence').addEventListener('click', function () {
    const modal = document.getElementById('modal-drivers-licence');
    const formData = new FormData();
    const front = modal.querySelector('input[name="front_file"]');
    const back = modal.querySelector('input[name="back_file"]');
    const expires = modal.querySelector('input[name="expires_at"]');
    if (front && front.files[0]) formData.append('front_file', front.files[0]);
    if (back && back.files[0]) formData.append('back_file', back.files[0]);
    if (expires && expires.value) formData.append('expires_at', expires.value);
    if (!front.files[0] && !back.files[0]) { alert('Please upload at least one file.'); return; }
    submitDoc('drivers_licence', formData);
  });

  document.getElementById('submit-instructor-licence').addEventListener('click', function () {
    const modal = document.getElementById('modal-instructor-licence');
    const formData = new FormData();
    const front = modal.querySelector('input[name="front_file"]');
    const back = modal.querySelector('input[name="back_file"]');
    const expires = modal.querySelector('input[name="expires_at"]');
    if (front && front.files[0]) formData.append('front_file', front.files[0]);
    if (back && back.files[0]) formData.append('back_file', back.files[0]);
    if (expires && expires.value) formData.append('expires_at', expires.value);
    if (!front.files[0] && !back.files[0]) { alert('Please upload at least one file.'); return; }
    submitDoc('instructor_licence', formData);
  });

  document.getElementById('submit-wwcc').addEventListener('click', function () {
    const modal = document.getElementById('modal-wwcc');
    const formData = new FormData();
    const num = modal.querySelector('input[name="wwcc_number"]');
    const expires = modal.querySelector('input[name="expires_at"]');
    const file = modal.querySelector('input[name="file"]');
    if (num && num.value) formData.append('wwcc_number', num.value);
    if (expires && expires.value) formData.append('expires_at', expires.value);
    if (file && file.files[0]) formData.append('file', file.files[0]);
    submitDoc('wwcc', formData);
  });

  load();
})();
