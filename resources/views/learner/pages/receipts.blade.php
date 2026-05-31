@extends('layouts.learner')

@section('title', 'Receipts')
@section('heading', 'My Receipts')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Receipts</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">My Receipts</h5>
    <span class="badge text-bg-light">{{ $totalCount }} bookings on record</span>
</div>

{{-- ── Summary KPI cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted small mb-2 text-uppercase" style="letter-spacing:.05em;">Total Paid</h6>
                <div class="fs-3 fw-bold text-success">${{ number_format($totalPaid, 2) }}</div>
                <div class="small text-muted mt-1">Across all your bookings</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted small mb-2 text-uppercase" style="letter-spacing:.05em;">Total Refunded</h6>
                <div class="fs-3 fw-bold text-primary">${{ number_format($totalRefunded, 2) }}</div>
                <div class="small text-muted mt-1">Returned to wallet or card</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted small mb-2 text-uppercase" style="letter-spacing:.05em;">Net Spent</h6>
                <div class="fs-3 fw-bold">${{ number_format(max(0, $totalPaid - $totalRefunded), 2) }}</div>
                <div class="small text-muted mt-1">Paid minus refunds</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Filter tabs ── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <ul class="nav nav-tabs card-header-tabs">
            @foreach([
                'all'       => ['All', 'bi-receipt'],
                'paid'      => ['Paid', 'bi-check-circle'],
                'refunded'  => ['Refunded', 'bi-arrow-counterclockwise'],
                'cancelled' => ['Cancelled', 'bi-x-circle'],
            ] as $key => [$label, $icon])
                <li class="nav-item">
                    <a class="nav-link {{ $filter === $key ? 'active' : '' }}"
                       href="{{ route('learner.receipts', ['filter' => $key]) }}">
                        <i class="bi {{ $icon }} me-1"></i>{{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="card-body p-0">
        @if($bookings->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-receipt-cutoff display-4 d-block mb-3 opacity-50"></i>
                <p class="mb-0">No receipts in this view yet.</p>
                @if($filter !== 'all')
                    <a href="{{ route('learner.receipts') }}" class="small mt-2 d-inline-block">View all receipts</a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Receipt #</th>
                            <th>Date</th>
                            <th>Instructor</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $b)
                            @php
                                $receiptNumber = 'SL-' . ($b->scheduled_at?->format('Ymd') ?? now()->format('Ymd')) . '-' . str_pad((string) $b->id, 6, '0', STR_PAD_LEFT);
                                $refundAmt = (float) ($b->refund_amount ?? 0);
                                if ($b->status === \App\Models\Booking::STATUS_CANCELLED) {
                                    $statusBadge = $refundAmt > 0 ? 'text-bg-primary' : 'text-bg-danger';
                                    $statusText  = $refundAmt > 0 ? 'Cancelled · Refunded' : 'Cancelled';
                                } elseif ($b->status === \App\Models\Booking::STATUS_COMPLETED) {
                                    $statusBadge = 'text-bg-success';
                                    $statusText  = 'Completed';
                                } elseif ($b->payment_status === \App\Models\Booking::PAYMENT_PAID) {
                                    $statusBadge = 'text-bg-success';
                                    $statusText  = 'Paid';
                                } else {
                                    $statusBadge = 'text-bg-warning';
                                    $statusText  = 'Pending';
                                }
                            @endphp
                            <tr>
                                <td class="ps-3"><code class="small">{{ $receiptNumber }}</code></td>
                                <td>
                                    <div>{{ $b->scheduled_at?->format('j M Y') }}</div>
                                    <div class="small text-muted">{{ $b->scheduled_at?->format('H:i') }}</div>
                                </td>
                                <td>{{ $b->instructor?->name ?? '—' }}</td>
                                <td>{{ $b->type === \App\Models\Booking::TYPE_TEST_PACKAGE ? 'Test Package' : 'Lesson' }}</td>
                                <td><span class="badge {{ $statusBadge }}">{{ $statusText }}</span></td>
                                <td class="text-end">
                                    <div class="fw-semibold">${{ number_format((float) $b->amount, 2) }}</div>
                                    @if($refundAmt > 0)
                                        <div class="small text-primary">−${{ number_format($refundAmt, 2) }} refunded</div>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('learner.receipts.show', $b) }}" class="btn btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('learner.receipts.download', $b) }}" class="btn btn-outline-secondary" title="Download PDF">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($bookings->hasPages())
                <div class="card-footer bg-white d-flex justify-content-between align-items-center small">
                    <div class="text-muted">
                        Showing {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} of {{ $bookings->total() }}
                    </div>
                    <div>{{ $bookings->links() }}</div>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
