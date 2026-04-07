@extends('layouts.frontend')

@section('title', 'My Service Bookings')

@section('content')
<div class="container py-5" style="max-width: 900px;">
    <h1 class="h3 fw-bold mb-4">My Service Bookings</h1>

    <div class="card shadow-sm">
        <div class="list-group list-group-flush">
            @forelse($bookings as $booking)
                <a href="{{ route('service-bookings.show', $booking) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <p class="fw-semibold mb-1">{{ $booking->provider->category->name }} &middot; {{ $booking->provider->business_name ?: $booking->provider->user->name }}</p>
                        <p class="text-muted small mb-0">{{ $booking->scheduled_at->format('D, d M Y g:i a') }}</p>
                    </div>
                    <div class="text-end">
                        <p class="fw-bold mb-1">${{ number_format($booking->total_amount, 2) }}</p>
                        <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                    </div>
                </a>
            @empty
                <p class="p-4 text-muted text-center mb-0">No bookings yet.</p>
            @endforelse
        </div>
    </div>

    <div class="mt-4">{{ $bookings->links() }}</div>
</div>
@endsection
