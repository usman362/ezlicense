@extends('layouts.admin')
@section('title', 'Provider Details')
@section('content')
<div class="container-fluid p-4" style="max-width: 900px;">
    <a href="{{ route('admin.service-providers.index') }}" class="text-decoration-none small">&larr; Back to providers</a>
    <div class="d-flex justify-content-between align-items-start mt-2">
        <div>
            <h1 class="h3 mb-1">{{ $provider->business_name ?: $provider->user->name }}</h1>
            <p class="text-muted">{{ $provider->category->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.service-providers.edit', $provider) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
            <form method="POST" action="{{ route('admin.service-providers.destroy', $provider) }}" onsubmit="return confirm('Delete this provider? This cannot be undone.')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Delete</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">User</dt>
                <dd class="col-sm-9">{{ $provider->user->name }} &middot; {{ $provider->user->email }}</dd>

                <dt class="col-sm-3">ABN</dt>
                <dd class="col-sm-9">{{ $provider->abn ?: '—' }}</dd>

                <dt class="col-sm-3">Hourly rate</dt>
                <dd class="col-sm-9">${{ number_format($provider->hourly_rate, 2) }}</dd>

                <dt class="col-sm-3">Call-out fee</dt>
                <dd class="col-sm-9">${{ number_format($provider->callout_fee, 2) }}</dd>

                <dt class="col-sm-3">Experience</dt>
                <dd class="col-sm-9">{{ $provider->years_experience ?? '—' }} yrs</dd>

                <dt class="col-sm-3">License</dt>
                <dd class="col-sm-9">{{ $provider->license_number ?: '—' }}</dd>

                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $provider->service_description ?: '—' }}</dd>

                <dt class="col-sm-3">Base location</dt>
                <dd class="col-sm-9">{{ $provider->base_suburb }} {{ $provider->base_postcode }} {{ $provider->base_state }} &middot; radius {{ $provider->service_radius_km }}km</dd>
            </dl>
        </div>
    </div>

    @if($provider->verification_status === 'pending')
        <div class="d-flex gap-3 align-items-start">
            <form method="POST" action="{{ route('admin.service-providers.approve', $provider) }}">
                @csrf
                <button class="btn btn-success">Approve</button>
            </form>
            <form method="POST" action="{{ route('admin.service-providers.reject', $provider) }}" class="d-flex gap-2 flex-grow-1">
                @csrf
                <input type="text" name="admin_notes" placeholder="Rejection reason (optional)" class="form-control">
                <button class="btn btn-danger">Reject</button>
            </form>
        </div>
    @else
        <div class="alert alert-info">
            Status: <strong>{{ ucfirst($provider->verification_status) }}</strong>
            @if($provider->admin_notes)<br><small>Notes: {{ $provider->admin_notes }}</small>@endif
        </div>
    @endif
</div>
@endsection
