@extends('layouts.admin')
@section('title', $payout->reference . ' — Payout')
@section('heading')
    <a href="{{ route('admin.payouts.index') }}" class="text-decoration-none text-muted me-2"><i class="bi bi-arrow-left"></i></a>
    Payout {{ $payout->reference }}
@endsection

@section('content')
@php $sc = \App\Models\InstructorPayout::statusColor($payout->status); @endphp

{{-- Header --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <div class="mb-2">
                            <span class="badge bg-{{ $sc }}-subtle text-{{ $sc }} fs-6 px-3 py-2">{{ ucfirst($payout->status) }}</span>
                        </div>
                        <h4 class="fw-bolder mb-1">{{ $payout->instructorProfile?->user?->name ?? 'Instructor' }}</h4>
                        <div class="text-muted">{{ $payout->periodLabel() }}</div>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted">Net Payout</div>
                        <div class="display-6 fw-bolder" style="color: var(--sl-success);">${{ number_format($payout->net_amount, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Actions</h6>
                @if($payout->isPending())
                    <form method="POST" action="{{ route('admin.payouts.approve', $payout) }}" class="mb-2">
                        @csrf @method('PATCH')
                        <button class="btn btn-success btn-sm w-100 fw-bold"><i class="bi bi-check-lg me-1"></i>Approve Payout</button>
                    </form>
                @endif
                @if($payout->canMarkPaid())
                    <form method="POST" action="{{ route('admin.payouts.mark-paid', $payout) }}" class="mb-2"
                          onsubmit="var r = prompt('Payment reference (optional):'); if(r !== null) { this.querySelector('[name=payment_reference]').value = r; return true; } return false;">
                        @csrf @method('PATCH')
                        <input type="hidden" name="payment_reference" value="">
                        <button class="btn btn-primary btn-sm w-100 fw-bold"><i class="bi bi-cash me-1"></i>Mark as Paid</button>
                    </form>
                @endif
                @if(!$payout->isPaid() && !$payout->isFailed())
                    <form method="POST" action="{{ route('admin.payouts.mark-failed', $payout) }}"
                          onsubmit="var r = prompt('Reason for failure:'); if(r) { this.querySelector('[name=failure_reason]').value = r; return true; } return false;">
                        @csrf @method('PATCH')
                        <input type="hidden" name="failure_reason" value="">
                        <button class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-x-circle me-1"></i>Mark Failed</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Summary Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="kpi-card h-100"><div class="kpi-label">Bookings</div><div class="kpi-value">{{ $payout->bookings_count }}</div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-card kpi-accent h-100"><div class="kpi-label">Gross</div><div class="kpi-value" style="font-size:var(--sl-text-2xl);">${{ number_format($payout->gross_amount, 2) }}</div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-card kpi-danger h-100"><div class="kpi-label">Service Fees</div><div class="kpi-value" style="font-size:var(--sl-text-2xl);">-${{ number_format($payout->service_fee_total, 2) }}</div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-card kpi-danger h-100"><div class="kpi-label">Processing Fees</div><div class="kpi-value" style="font-size:var(--sl-text-2xl);">-${{ number_format($payout->processing_fee_total, 2) }}</div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-card kpi-teal h-100"><div class="kpi-label">GST on Fees</div><div class="kpi-value" style="font-size:var(--sl-text-2xl);">${{ number_format($payout->gst_on_fees, 2) }}</div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-card kpi-success h-100"><div class="kpi-label">Net Payout</div><div class="kpi-value" style="font-size:var(--sl-text-2xl);">${{ number_format($payout->net_amount, 2) }}</div></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Payout Items --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i>Bookings in this Payout ({{ $payout->items->count() }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="small">Booking</th>
                                <th class="small">Learner</th>
                                <th class="small">Date</th>
                                <th class="small text-end">Gross</th>
                                <th class="small text-end">Service</th>
                                <th class="small text-end">Processing</th>
                                <th class="small text-end">GST</th>
                                <th class="small text-end fw-bold">Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payout->items as $item)
                                <tr>
                                    <td class="small">#{{ $item->booking_id }}</td>
                                    <td class="small">{{ $item->booking?->learner?->name ?? '—' }}</td>
                                    <td class="small text-muted">{{ $item->booking?->scheduled_at?->format('d M Y H:i') ?? '—' }}</td>
                                    <td class="small text-end">${{ number_format($item->gross_amount, 2) }}</td>
                                    <td class="small text-end text-danger">-${{ number_format($item->service_fee, 2) }}</td>
                                    <td class="small text-end text-danger">-${{ number_format($item->processing_fee, 2) }}</td>
                                    <td class="small text-end">${{ number_format($item->gst_on_fees, 2) }}</td>
                                    <td class="small text-end fw-bold">${{ number_format($item->net_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="3" class="small">Totals</td>
                                <td class="small text-end">${{ number_format($payout->gross_amount, 2) }}</td>
                                <td class="small text-end text-danger">-${{ number_format($payout->service_fee_total, 2) }}</td>
                                <td class="small text-end text-danger">-${{ number_format($payout->processing_fee_total, 2) }}</td>
                                <td class="small text-end">${{ number_format($payout->gst_on_fees, 2) }}</td>
                                <td class="small text-end" style="color:var(--sl-success);">${{ number_format($payout->net_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Bank Details --}}
        @php $prof = $payout->instructorProfile; @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-bank me-2"></i>Bank Details</h6>
            </div>
            <div class="card-body">
                @if($prof?->bank_account_number)
                    <table class="table table-sm table-borderless mb-0 small">
                        <tr><td class="text-muted" style="width:120px;">Account Name</td><td>{{ $prof->bank_account_name ?? '—' }}</td></tr>
                        <tr><td class="text-muted">BSB</td><td>{{ $prof->bank_bsb ?? '—' }}</td></tr>
                        <tr><td class="text-muted">Account #</td><td>••••{{ substr($prof->bank_account_number, -4) }}</td></tr>
                        <tr><td class="text-muted">ABN</td><td>{{ $prof->abn ?? '—' }}</td></tr>
                        <tr><td class="text-muted">GST Registered</td><td>{!! $prof->gst_registered ? '<span class="badge bg-success-subtle text-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td></tr>
                    </table>
                @else
                    <div class="alert alert-danger mb-0 small">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Instructor has not submitted bank details. This payout cannot be processed.
                    </div>
                @endif
            </div>
        </div>

        {{-- Timeline --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Timeline</h6>
            </div>
            <div class="card-body small">
                <div class="mb-2"><i class="bi bi-dot text-muted"></i><strong>Created:</strong> {{ $payout->created_at->format('d M Y H:i') }}</div>
                @if($payout->approved_at)
                    <div class="mb-2"><i class="bi bi-dot text-success"></i><strong>Approved:</strong> {{ $payout->approved_at->format('d M Y H:i') }} by {{ $payout->approver?->name ?? '—' }}</div>
                @endif
                @if($payout->paid_at)
                    <div class="mb-2"><i class="bi bi-dot text-primary"></i><strong>Paid:</strong> {{ $payout->paid_at->format('d M Y H:i') }}
                        @if($payout->payment_reference) <br><span class="text-muted">Ref: {{ $payout->payment_reference }}</span> @endif
                    </div>
                @endif
                @if($payout->failure_reason)
                    <div class="mb-2"><i class="bi bi-dot text-danger"></i><strong>Failed:</strong> {{ $payout->failure_reason }}</div>
                @endif
            </div>
        </div>

        {{-- Notes --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-sticky me-2"></i>Admin Notes</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.payouts.add-note', $payout) }}">
                    @csrf
                    <textarea name="admin_notes" class="form-control form-control-sm mb-2" rows="3" placeholder="Internal notes...">{{ $payout->admin_notes }}</textarea>
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-save me-1"></i>Save Notes</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
