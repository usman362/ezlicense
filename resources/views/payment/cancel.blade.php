@extends('layouts.frontend')

@section('title', 'Payment Cancelled')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <div style="width:72px; height:72px; background:#fef3c7; border-radius:50%; display:inline-flex; align-items:center; justify-content:center;">
                            <i class="bi bi-arrow-counterclockwise" style="font-size:34px; color:#92400e;"></i>
                        </div>
                    </div>

                    <h2 class="fw-bold mb-2">Payment cancelled</h2>
                    <p class="text-muted mb-4">
                        Your booking <strong>#{{ $booking->id }}</strong> is on hold — no charge was made.
                        You can resume payment anytime from your dashboard, or pick a different lesson.
                    </p>

                    @auth
                        <a href="{{ route('stripe.checkout', ['booking' => $booking->id]) }}" class="btn btn-warning fw-bold w-100 mb-2">
                            <i class="bi bi-credit-card me-1"></i>Resume payment
                        </a>
                        <a href="{{ route('learner.dashboard') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-left me-1"></i>Back to dashboard
                        </a>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-warning fw-bold w-100">
                            <i class="bi bi-house me-1"></i>Back to home
                        </a>
                    @endauth

                    <p class="small text-muted mt-4 mb-0">
                        Need help? Email <a href="mailto:support@securelicence.com">support@securelicence.com</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
