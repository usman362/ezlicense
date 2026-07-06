@extends('layouts.learner')

@section('title', 'Receipt ' . $receipt['number'])
@section('heading', 'Receipt')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('learner.receipts') }}">Receipts</a></li>
        <li class="breadcrumb-item active">{{ $receipt['number'] }}</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h5 class="mb-1">{{ $receipt['doc_title'] }}</h5>
        <div class="small text-muted">
            <code>{{ $receipt['number'] }}</code> · Issued {{ $receipt['issued_at']->format('j M Y, H:i') }}
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('learner.receipts') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
        <a href="{{ route('learner.receipts.download', $booking) }}" class="btn btn-warning fw-bold">
            <i class="bi bi-download me-1"></i>Download PDF
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        {{-- Status banner --}}
        @php
            $statusColors = [
                'paid'       => 'success',
                'pending'    => 'warning',
                'completed'  => 'success',
                'refunded'   => 'primary',
                'cancelled'  => 'danger',
            ];
            $color = $statusColors[$receipt['status_class']] ?? 'secondary';
        @endphp
        <div class="alert alert-{{ $color }} d-flex justify-content-between align-items-center mb-4">
            <div>
                <strong>Status:</strong> {{ $receipt['status_label'] }}
            </div>
            <div class="small">
                @if($receipt['paid_at'])
                    Paid {{ $receipt['paid_at']->format('j M Y, H:i') }}
                @elseif($receipt['cancelled_at'])
                    Cancelled {{ $receipt['cancelled_at']->format('j M Y, H:i') }}
                @endif
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <h6 class="text-uppercase small text-muted mb-2" style="letter-spacing:.05em;">Billed To</h6>
                <div class="fw-semibold">{{ $receipt['learner']['name'] }}</div>
                @if($receipt['learner']['email'])<div class="small text-muted">{{ $receipt['learner']['email'] }}</div>@endif
                @if($receipt['learner']['phone'])<div class="small text-muted">{{ $receipt['learner']['phone'] }}</div>@endif
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase small text-muted mb-2" style="letter-spacing:.05em;">Instructor</h6>
                <div class="fw-semibold">{{ $receipt['instructor']['name'] ?? '—' }}</div>
                @if($receipt['instructor']['phone'])<div class="small text-muted">{{ $receipt['instructor']['phone'] }}</div>@endif
            </div>
        </div>

        <div class="mb-4">
            <h6 class="text-uppercase small text-muted mb-2" style="letter-spacing:.05em;">Booking Details</h6>
            <table class="table table-sm mb-0">
                <tbody>
                    <tr><th class="ps-0" style="width:30%">Booking ref</th><td>#{{ $receipt['booking_id'] }}</td></tr>
                    <tr><th class="ps-0">Service</th><td>{{ $receipt['service_label'] }}</td></tr>
                    <tr><th class="ps-0">Scheduled</th><td>{{ $receipt['scheduled_at']->format('l, j M Y') }} at {{ $receipt['scheduled_at']->format('H:i') }}</td></tr>
                    <tr><th class="ps-0">Duration</th><td>{{ $receipt['duration_minutes'] }} mins</td></tr>
                    <tr><th class="ps-0">Transmission</th><td>{{ ucfirst($receipt['transmission']) }}</td></tr>
                    @if($receipt['location'])<tr><th class="ps-0">Pick-up</th><td>{{ $receipt['location'] }}</td></tr>@endif
                </tbody>
            </table>
        </div>

        <h6 class="text-uppercase small text-muted mb-2" style="letter-spacing:.05em;">Charges</h6>
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Unit</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $receipt['service_label'] }}</div>
                        <div class="small text-muted">with {{ $receipt['instructor']['name'] ?? 'instructor' }} on {{ $receipt['scheduled_at']->format('j M Y') }}</div>
                    </td>
                    <td class="text-end">1</td>
                    <td class="text-end">${{ number_format($receipt['amount'], 2) }}</td>
                    <td class="text-end">${{ number_format($receipt['amount'], 2) }}</td>
                </tr>
                @if($receipt['coupon_discount'] > 0)
                    <tr>
                        <td>Discount{{ $receipt['coupon_code'] ? ' — ' . $receipt['coupon_code'] : '' }}</td>
                        <td class="text-end">—</td>
                        <td class="text-end">—</td>
                        <td class="text-end">−${{ number_format($receipt['coupon_discount'], 2) }}</td>
                    </tr>
                @endif
                <tr class="border-top">
                    <td colspan="3" class="text-end fw-semibold">Subtotal</td>
                    <td class="text-end fw-semibold">${{ number_format($receipt['subtotal'], 2) }}</td>
                </tr>
                @if(!empty($receipt['platform_fee']))
                    <tr>
                        <td colspan="3" class="text-end small text-muted">Service fee</td>
                        <td class="text-end small text-muted">${{ number_format($receipt['platform_fee'], 2) }}</td>
                    </tr>
                @endif
                @if(!empty($receipt['processing_fee']))
                    <tr>
                        <td colspan="3" class="text-end small text-muted">Processing fee</td>
                        <td class="text-end small text-muted">${{ number_format($receipt['processing_fee'], 2) }}</td>
                    </tr>
                @endif
                @if($receipt['gst_amount'] > 0)
                    <tr>
                        <td colspan="3" class="text-end small text-muted">GST (included)</td>
                        <td class="text-end small text-muted">${{ number_format($receipt['gst_amount'], 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="3" class="text-end fw-bold">Total paid{{ $receipt['payment_method_label'] ? ' (' . $receipt['payment_method_label'] . ')' : '' }}</td>
                    <td class="text-end fw-bold fs-5">${{ number_format($receipt['total_paid'], 2) }}</td>
                </tr>

                @if($receipt['refund_amount'] > 0)
                    <tr class="text-primary">
                        <td colspan="3" class="text-end">Refund issued ({{ $receipt['refund_method_label'] ?? 'manual' }})</td>
                        <td class="text-end">−${{ number_format($receipt['refund_amount'], 2) }}</td>
                    </tr>
                    @if($receipt['cancellation_fee_retained'] > 0)
                        <tr class="small text-muted">
                            <td colspan="3" class="text-end">Cancellation fee retained</td>
                            <td class="text-end">${{ number_format($receipt['cancellation_fee_retained'], 2) }}</td>
                        </tr>
                    @endif
                @endif
            </tbody>
        </table>

        @if($receipt['cancellation_reason'] || $receipt['refund_reason'])
            <div class="border-top pt-3 mt-3">
                <h6 class="text-uppercase small text-muted mb-2" style="letter-spacing:.05em;">Notes</h6>
                @if($receipt['cancellation_reason'])
                    <div class="small"><strong>Cancellation reason:</strong> {{ $receipt['cancellation_reason'] }}</div>
                @endif
                @if($receipt['cancellation_message'])
                    <div class="small"><strong>Message:</strong> {{ $receipt['cancellation_message'] }}</div>
                @endif
                @if($receipt['refund_reason'])
                    <div class="small"><strong>Refund reason:</strong> {{ $receipt['refund_reason'] }}</div>
                @endif
                @if($receipt['refund_reference'])
                    <div class="small"><strong>Refund reference:</strong> {{ $receipt['refund_reference'] }}</div>
                @endif
            </div>
        @endif
    </div>
    <div class="card-footer bg-white text-muted small">
        Questions about this receipt? Contact <a href="mailto:{{ $receipt['support_email'] }}">{{ $receipt['support_email'] }}</a>.
        For disputes, please get in touch within 14 days of the lesson date.
    </div>
</div>
@endsection
