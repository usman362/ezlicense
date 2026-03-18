@extends('layouts.instructor')

@section('title', 'Personal Details')
@section('heading', 'Settings › Personal Details')

@section('content')
@php
    $u = Auth::user();
    $first = $u->first_name ?? (trim($u->name) !== '' ? explode(' ', $u->name, 2)[0] ?? '' : '');
    $last = $u->last_name ?? (trim($u->name) !== '' && count(explode(' ', $u->name, 2)) > 1 ? explode(' ', $u->name, 2)[1] : '');
@endphp
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.settings.personal-details') }}">Settings</a></li>
        <li class="breadcrumb-item active" aria-current="page">Personal Details</li>
    </ol>
</nav>

<ul class="nav nav-tabs border-0 small mb-4">
    <li class="nav-item"><a class="nav-link active" href="{{ route('instructor.settings.personal-details') }}">Personal Details</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.profile') }}">Profile</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.vehicle') }}">Vehicle</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.service-area') }}">Service Area</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.opening-hours') }}">Opening Hours</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.calendar-settings') }}">Calendar Settings</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.pricing') }}">Pricing</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.documents') }}">Documents</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.banking') }}">Banking</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.guide') }}">Guide</a></li>
</ul>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form id="personal-details-form">
            <h6 class="fw-bold mb-1">Personal info</h6>
            <p class="small text-muted mb-3">Provide personal details and how we can reach you.</p>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">First name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $first) }}" required maxlength="100">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Preferred first name</label>
                    <input type="text" name="preferred_first_name" class="form-control" value="{{ old('preferred_first_name', $u->preferred_first_name ?? '') }}" maxlength="100">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $last) }}" required maxlength="100">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select</option>
                        <option value="male" {{ old('gender', $u->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $u->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $u->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        <option value="prefer_not_to_say" {{ old('gender', $u->gender) === 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email address</label>
                    <input type="email" class="form-control" value="{{ $u->email }}" disabled>
                    <small class="text-muted">Contact support to change email.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone number <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $u->phone ?? '') }}" required placeholder="e.g. 0405544322" maxlength="20">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Postcode <span class="text-danger">*</span></label>
                    <select name="postcode" class="form-select" required>
                        <option value="">Select postcode</option>
                        @foreach($postcodes ?? [] as $pc)
                            <option value="{{ $pc }}" {{ old('postcode', $u->postcode ?? '') == $pc ? 'selected' : '' }}>{{ $pc }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="my-4">

            <h6 class="fw-bold mb-1">Change password</h6>
            <p class="small text-muted mb-3">Leave this blank if you don't want to change your password.</p>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">New password</label>
                    <input type="password" name="new_password" class="form-control" autocomplete="new-password" minlength="8" placeholder="••••••••">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm new password</label>
                    <input type="password" name="new_password_confirmation" class="form-control" autocomplete="new-password" placeholder="••••••••">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Enter your current password to confirm the changes <span class="text-danger">*</span></label>
                <input type="password" name="current_password" class="form-control" required autocomplete="current-password" placeholder="••••••••">
            </div>

            <div class="d-flex justify-content-end">
                <span id="personal-details-message" class="me-3 align-self-center"></span>
                <button type="submit" class="btn btn-warning text-dark fw-medium">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
(function() {
  var form = document.getElementById('personal-details-form');
  var msg = document.getElementById('personal-details-message');
  if (!form) return;

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    var newPw = form.querySelector('[name="new_password"]').value;
    var confirmPw = form.querySelector('[name="new_password_confirmation"]').value;
    if (newPw && newPw !== confirmPw) {
      msg.textContent = 'New password and confirmation do not match.';
      msg.className = 'me-3 align-self-center text-danger';
      return;
    }
    msg.textContent = '';
    var csrf = document.querySelector('meta[name="csrf-token"]');
    var token = csrf ? csrf.getAttribute('content') : '';
    var body = {
      first_name: form.querySelector('[name="first_name"]').value,
      last_name: form.querySelector('[name="last_name"]').value,
      preferred_first_name: form.querySelector('[name="preferred_first_name"]').value || null,
      gender: form.querySelector('[name="gender"]').value,
      phone: form.querySelector('[name="phone"]').value,
      postcode: form.querySelector('[name="postcode"]').value,
      current_password: form.querySelector('[name="current_password"]').value
    };
    if (newPw) body.new_password = newPw;
    if (newPw) body.new_password_confirmation = confirmPw;

    fetch('/user/profile', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
      body: JSON.stringify(body)
    })
    .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
    .then(function(result) {
      if (result.ok) {
        msg.textContent = 'Saved.';
        msg.className = 'me-3 align-self-center text-success';
        form.querySelector('[name="new_password"]').value = '';
        form.querySelector('[name="new_password_confirmation"]').value = '';
        form.querySelector('[name="current_password"]').value = '';
      } else {
        var err = result.data.errors || result.data.message;
        msg.textContent = typeof err === 'string' ? err : (err && err.current_password ? err.current_password[0] : (err && err.first_name ? err.first_name[0] : 'Please fix the errors below.'));
        msg.className = 'me-3 align-self-center text-danger';
      }
    })
    .catch(function() {
      msg.textContent = 'Error saving.';
      msg.className = 'me-3 align-self-center text-danger';
    });
  });
})();
</script>
@endpush
@endsection
