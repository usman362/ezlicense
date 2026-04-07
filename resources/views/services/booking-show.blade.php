@extends('layouts.frontend')

@section('title', 'Booking ' . $booking->reference)

@section('content')
<div class="container py-5" style="max-width: 700px;">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Booking reference</p>
                    <h1 class="h3 fw-bold mb-0">{{ $booking->reference }}</h1>
                </div>
                <span class="badge bg-warning text-dark fs-6">{{ ucfirst($booking->status) }}</span>
            </div>

            <hr>

            <dl class="row mb-0">
                <dt class="col-sm-4">Provider</dt>
                <dd class="col-sm-8">{{ $booking->provider->business_name ?: $booking->provider->user->name }}</dd>

                <dt class="col-sm-4">Category</dt>
                <dd class="col-sm-8">{{ $booking->category->name }}</dd>

                <dt class="col-sm-4">When</dt>
                <dd class="col-sm-8">{{ $booking->scheduled_at->format('D, d M Y g:i a') }}</dd>

                <dt class="col-sm-4">Duration</dt>
                <dd class="col-sm-8">{{ $booking->duration_minutes }} min</dd>

                <dt class="col-sm-4">Address</dt>
                <dd class="col-sm-8">{{ $booking->address_line }}, {{ $booking->suburb }} {{ $booking->postcode }} {{ $booking->state }}</dd>

                @if($booking->job_description)
                    <dt class="col-sm-4">Job description</dt>
                    <dd class="col-sm-8">{{ $booking->job_description }}</dd>
                @endif
            </dl>

            <hr>

            <dl class="mb-0">
                <div class="d-flex justify-content-between"><dt class="fw-normal">Hourly rate</dt><dd class="mb-1">${{ number_format($booking->hourly_rate, 2) }}</dd></div>
                @if($booking->callout_fee > 0)
                    <div class="d-flex justify-content-between"><dt class="fw-normal">Call-out fee</dt><dd class="mb-1">${{ number_format($booking->callout_fee, 2) }}</dd></div>
                @endif
                <div class="d-flex justify-content-between fs-5 fw-bold pt-2 border-top"><dt>Total</dt><dd class="mb-0">${{ number_format($booking->total_amount, 2) }}</dd></div>
            </dl>
        </div>
    </div>
</div>
@endsection
