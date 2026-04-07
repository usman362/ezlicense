@extends('layouts.frontend')

@section('title', 'Book ' . ($provider->business_name ?: $provider->user->name))

@section('content')
<div class="container py-5" style="max-width: 700px;">
    <h1 class="h3 fw-bold mb-1">Book {{ $provider->business_name ?: $provider->user->name }}</h1>
    <p class="text-muted mb-4">{{ $provider->category->name }} &middot; ${{ number_format($provider->hourly_rate, 2) }}/hr</p>

    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('service-bookings.store', $provider) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Date &amp; Time</label>
                    <input type="datetime-local" name="scheduled_at" required class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" min="15" step="15" value="{{ $provider->default_duration_minutes }}" required class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address_line" required class="form-control" placeholder="Street address">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-5"><input type="text" name="suburb" placeholder="Suburb" class="form-control"></div>
                    <div class="col-md-4"><input type="text" name="postcode" placeholder="Postcode" class="form-control"></div>
                    <div class="col-md-3"><input type="text" name="state" placeholder="State" class="form-control"></div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Job description</label>
                    <textarea name="job_description" rows="4" class="form-control" placeholder="Describe the work needed..."></textarea>
                </div>
                <button class="btn btn-primary btn-lg w-100">Confirm Booking Request</button>
            </form>
        </div>
    </div>
</div>
@endsection
