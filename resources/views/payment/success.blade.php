@extends('layouts.frontend')

@section('title', 'Payment Successful')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <div style="width:72px; height:72px; background:#dcfce7; border-radius:50%; display:inline-flex; align-items:center; justify-content:center;">
                            <i class="bi bi-check-lg" style="font-size:36px; color:#166534;"></i>
                        </div>
                    </div>

                    <h2 class="fw-bold mb-2">Payment received!</h2>
                    <p class="text-muted mb-4">
                        Your booking is being confirmed. We've emailed you a receipt — check your inbox in the next minute or two.
                    </p>

                    <div class="bg-light rounded p-3 mb-4 text-start">
                        <div class="row g-2 small">
                            <div class="col-6 text-muted">Booking reference:</div>
                            <div class="col-6 fw-semibold">#{{ $booking->id }}</div>

                            <div class="col-6 text-muted">Lesson date:</div>
                            <div class="col-6 fw-semibold">{{ $booking->scheduled_at?->format('l, j M Y · H:i') }}</div>

                            <div class="col-6 text-muted">Instructor:</div>
                            <div class="col-6 fw-semibold">{{ $booking->instructor?->name ?? '—' }}</div>

                            <div class="col-6 text-muted">Amount paid:</div>
                            <div class="col-6 fw-semibold text-success">${{ number_format((float) $booking->amount, 2) }}</div>

                            @if($booking->stripe_payment_intent_id)
                                <div class="col-6 text-muted">Transaction ref:</div>
                                <div class="col-6 small"><code>{{ $booking->stripe_payment_intent_id }}</code></div>
                            @endif
                        </div>
                    </div>

                    @auth
                        <a href="{{ route('learner.dashboard') }}" class="btn btn-warning fw-bold w-100 mb-2">
                            <i class="bi bi-arrow-right me-1"></i>Go to My Bookings
                        </a>
                        <a href="{{ route('learner.receipts.show', $booking->id) }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-receipt me-1"></i>View Full Receipt
                        </a>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-warning fw-bold w-100">
                            <i class="bi bi-house me-1"></i>Back to home
                        </a>
                    @endauth

                    <p class="small text-muted mt-4 mb-0">
                        Questions? Email <a href="mailto:support@securelicence.com">support@securelicence.com</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
