@extends('layouts.admin')
@section('title', 'New Service Provider')
@section('content')
<div class="container-fluid p-4" style="max-width: 900px;">
    <a href="{{ route('admin.service-providers.index') }}" class="text-decoration-none small">&larr; Back to providers</a>
    <h1 class="h3 mt-2 mb-4">New Service Provider</h1>

    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('admin.service-providers.store') }}">
        @csrf

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">1. User account</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="user_mode" id="mode_new" value="new" checked onclick="toggleUserMode()">
                        <label class="form-check-label" for="mode_new">Create new user</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="user_mode" id="mode_existing" value="existing" onclick="toggleUserMode()">
                        <label class="form-check-label" for="mode_existing">Link to existing user</label>
                    </div>
                </div>

                <div id="new_user_fields">
                    <div class="row g-3">
                        <div class="col-md-4"><input type="text" name="new_name" class="form-control" placeholder="Full name" value="{{ old('new_name') }}"></div>
                        <div class="col-md-4"><input type="email" name="new_email" class="form-control" placeholder="Email" value="{{ old('new_email') }}"></div>
                        <div class="col-md-4"><input type="text" name="new_password" class="form-control" placeholder="Temporary password"></div>
                    </div>
                </div>

                <div id="existing_user_fields" class="d-none">
                    <label class="form-label">Existing user ID</label>
                    <input type="number" name="user_id" class="form-control" placeholder="e.g. 42" value="{{ old('user_id') }}">
                    <small class="text-muted">Find the user ID on the Users page.</small>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">2. Provider details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Service category *</label>
                        <select name="service_category_id" required class="form-select">
                            <option value="">Select...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('service_category_id')==$cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Business name</label>
                        <input type="text" name="business_name" class="form-control" value="{{ old('business_name') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ABN</label>
                        <input type="text" name="abn" class="form-control" value="{{ old('abn') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Years experience</label>
                        <input type="number" name="years_experience" class="form-control" value="{{ old('years_experience') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">License number</label>
                        <input type="text" name="license_number" class="form-control" value="{{ old('license_number') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Short bio</label>
                        <textarea name="bio" rows="2" class="form-control">{{ old('bio') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Service description</label>
                        <textarea name="service_description" rows="3" class="form-control">{{ old('service_description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">3. Pricing & service area</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Hourly rate ($) *</label>
                        <input type="number" step="0.01" name="hourly_rate" required class="form-control" value="{{ old('hourly_rate') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Call-out fee ($)</label>
                        <input type="number" step="0.01" name="callout_fee" class="form-control" value="{{ old('callout_fee', 0) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Default duration (min) *</label>
                        <input type="number" name="default_duration_minutes" required class="form-control" value="{{ old('default_duration_minutes', 60) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Radius (km) *</label>
                        <input type="number" name="service_radius_km" required class="form-control" value="{{ old('service_radius_km', 20) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Base suburb</label>
                        <input type="text" name="base_suburb" class="form-control" value="{{ old('base_suburb') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Postcode</label>
                        <input type="text" name="base_postcode" class="form-control" value="{{ old('base_postcode') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">State</label>
                        <input type="text" name="base_state" class="form-control" value="{{ old('base_state') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-check mb-4">
            <input type="hidden" name="auto_approve" value="0">
            <input type="checkbox" id="auto_approve" name="auto_approve" value="1" class="form-check-input" checked>
            <label for="auto_approve" class="form-check-label">Auto-approve and activate immediately</label>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-lg">Create Provider</button>
            <a href="{{ route('admin.service-providers.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>

<script>
function toggleUserMode() {
    const mode = document.querySelector('input[name="user_mode"]:checked').value;
    document.getElementById('new_user_fields').classList.toggle('d-none', mode !== 'new');
    document.getElementById('existing_user_fields').classList.toggle('d-none', mode !== 'existing');
}
</script>
@endsection
