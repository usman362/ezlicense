@extends('layouts.instructor')

@section('title', 'Statements')
@section('heading', 'Statements')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bi bi-house"></i> Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Statements</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="mb-1">Statements & Payouts</h5>
        <div class="small text-muted">
            Your payout schedule: <strong class="text-capitalize">{{ str_replace('_', ' ', $frequency) }}</strong>.
            Change it in <a href="{{ route('instructor.settings.banking') }}">Banking Settings</a>.
        </div>
    </div>
</div>

<div class="row g-3">
    @foreach($periods as $p)
        @php
            $hasBookings = $p['bookings_count'] > 0;
            $statusCfg = match(true) {
                $p['is_current']                          => ['Current period', 'primary'],
                $p['payout_status'] === 'paid'            => ['Paid', 'success'],
                $p['payout_status'] === 'approved'        => ['Approved', 'info'],
                $p['payout_status'] === 'processing'     => ['Processing', 'info'],
                $p['payout_status'] === 'failed'         => ['Failed', 'danger'],
                $p['payout_status'] === 'no_bookings'    => ['No bookings', 'secondary'],
                default                                  => ['Pending', 'warning'],
            };
            [$statusText, $statusColor] = $statusCfg;
        @endphp

        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 {{ $p['is_current'] ? 'border-warning' : '' }}" style="{{ $p['is_current'] ? 'border-left: 4px solid var(--sl-accent-500, #f59e0b) !important;' : '' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $p['label'] }}</h6>
                            <div class="small text-muted text-capitalize">
                                {{ str_replace('_', ' ', $p['frequency']) }} statement
                            </div>
                        </div>
                        <span class="badge text-bg-{{ $statusColor }}">{{ $statusText }}</span>
                    </div>

                    <div class="row g-2 mb-3 text-center">
                        <div class="col-4">
                            <div class="small text-muted">Lessons</div>
                            <div class="fw-bold">{{ $p['bookings_count'] }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Hours</div>
                            <div class="fw-bold">{{ round($p['lesson_minutes'] / 60, 1) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Net</div>
                            <div class="fw-bold text-success">${{ number_format($p['net_amount'], 0) }}</div>
                        </div>
                    </div>

                    @if($p['payout_paid_at'])
                        <div class="small text-muted mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            Paid {{ $p['payout_paid_at']->format('j M, H:i') }}
                        </div>
                    @endif

                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('instructor.statements.show', $p['key']) }}" class="btn btn-sm btn-outline-secondary flex-fill">
                            <i class="bi bi-eye me-1"></i>View
                        </a>
                        @if($hasBookings)
                            <a href="{{ route('instructor.statements.download', $p['key']) }}" class="btn btn-sm btn-warning fw-bold flex-fill">
                                <i class="bi bi-download me-1"></i>PDF
                            </a>
                        @else
                            <button class="btn btn-sm btn-outline-secondary flex-fill" disabled>
                                <i class="bi bi-download me-1"></i>PDF
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
