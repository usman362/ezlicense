@extends('layouts.frontend')

@section('title', 'Provider Dashboard')

@section('content')
<div class="container py-5" style="max-width: 1100px;">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">{{ $provider->business_name ?: $provider->user->name }}</h1>
            <p class="text-muted mb-0">{{ $provider->category->name }}</p>
        </div>
        @php $cls = ['pending'=>'warning','approved'=>'success','rejected'=>'danger'][$provider->verification_status] ?? 'secondary'; @endphp
        <span class="badge bg-{{ $cls }} fs-6">{{ ucfirst($provider->verification_status) }}</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100"><div class="card-body">
                <p class="text-muted small mb-1">Total bookings</p>
                <p class="fs-3 fw-bold mb-0">{{ $stats['total_bookings'] }}</p>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100"><div class="card-body">
                <p class="text-muted small mb-1">Completed</p>
                <p class="fs-3 fw-bold mb-0">{{ $stats['completed'] }}</p>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100"><div class="card-body">
                <p class="text-muted small mb-1">Earnings</p>
                <p class="fs-3 fw-bold mb-0">${{ number_format($stats['earnings'], 2) }}</p>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100"><div class="card-body">
                <p class="text-muted small mb-1">Pending</p>
                <p class="fs-3 fw-bold mb-0">{{ $stats['pending_count'] }}</p>
            </div></div>
        </div>
    </div>

    <div class="mb-3">
        <a href="{{ route('service-provider.availability.index') }}" class="btn btn-primary">Manage Availability</a>
    </div>

    <h2 class="h4 fw-semibold mb-3">Upcoming Bookings</h2>
    <div class="card shadow-sm">
        <div class="list-group list-group-flush">
            @forelse($upcoming as $b)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <p class="fw-semibold mb-1">{{ $b->customer->name }} &middot; {{ $b->reference }}</p>
                        <p class="text-muted small mb-0">{{ $b->scheduled_at->format('D, d M g:i a') }} &middot; {{ $b->address_line }}, {{ $b->suburb }}</p>
                    </div>
                    <span class="fw-bold">${{ number_format($b->total_amount, 2) }}</span>
                </div>
            @empty
                <p class="p-4 text-muted text-center mb-0">No upcoming bookings.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
