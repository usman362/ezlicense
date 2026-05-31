@extends('layouts.instructor')

@section('title', 'Statement ' . $statement['reference'])
@section('heading', 'Statement')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bi bi-house"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.statements') }}">Statements</a></li>
        <li class="breadcrumb-item active">{{ $statement['period_label'] }}</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h5 class="mb-1">{{ ucfirst(str_replace('_', ' ', $statement['frequency'])) }} Statement</h5>
        <div class="small text-muted">
            <code>{{ $statement['reference'] }}</code> · {{ $statement['period_label'] }}
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('instructor.statements') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
        @if($statement['totals']['bookings'] > 0)
            <a href="{{ route('instructor.statements.download', $statement['period_key']) }}" class="btn btn-warning fw-bold">
                <i class="bi bi-download me-1"></i>Download PDF
            </a>
        @endif
    </div>
</div>

{{-- ── Status banner ── --}}
@php
    $payoutClsMap = [
        'paid'        => 'success',
        'approved'    => 'info',
        'processing'  => 'info',
        'failed'      => 'danger',
        'pending'     => 'warning',
        'no_bookings' => 'secondary',
    ];
    $color = $statement['is_current'] ? 'primary' : ($payoutClsMap[$statement['payout']['status']] ?? 'secondary');
@endphp
<div class="alert alert-{{ $color }} d-flex justify-content-between align-items-center">
    <div>
        <strong>Payout status:</strong> {{ $statement['payout']['status_label'] }}
    </div>
    <div class="small">
        @if($statement['payout']['paid_at'])
            Paid {{ $statement['payout']['paid_at']->format('j M Y, H:i') }}
            @if($statement['payout']['payment_ref'])
                · Ref: {{ $statement['payout']['payment_ref'] }}
            @endif
        @endif
    </div>
</div>

{{-- ── KPI cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <h6 class="text-muted small mb-2 text-uppercase" style="letter-spacing:.05em;">Lessons</h6>
            <div class="fs-2 fw-bold">{{ $statement['totals']['bookings'] }}</div>
            @if($statement['cancelled_count'] > 0)
                <div class="small text-muted">{{ $statement['cancelled_count'] }} cancelled</div>
            @endif
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <h6 class="text-muted small mb-2 text-uppercase" style="letter-spacing:.05em;">Lesson Hours</h6>
            <div class="fs-2 fw-bold">{{ $statement['totals']['lesson_hrs'] }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <h6 class="text-muted small mb-2 text-uppercase" style="letter-spacing:.05em;">Gross Earned</h6>
            <div class="fs-2 fw-bold">${{ number_format($statement['totals']['gross'], 2) }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <h6 class="text-muted small mb-2 text-uppercase" style="letter-spacing:.05em;">Net Payout</h6>
            <div class="fs-2 fw-bold text-success">${{ number_format($statement['totals']['net'], 2) }}</div>
        </div></div>
    </div>
</div>

{{-- ── Lessons table ── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0">Lessons Delivered</h6>
    </div>
    <div class="card-body p-0">
        @if(count($statement['items']) === 0)
            <div class="text-center py-5 text-muted">
                <i class="bi bi-calendar-x display-4 d-block mb-3 opacity-50"></i>
                <p class="mb-0">No completed lessons in this period.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Date / Time</th>
                            <th>Learner</th>
                            <th>Type</th>
                            <th class="text-end">Duration</th>
                            <th class="text-end">Gross</th>
                            <th class="text-end">Fees</th>
                            <th class="text-end pe-3">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statement['items'] as $row)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-semibold">{{ $row['scheduled_at']->format('D, j M') }}</div>
                                    <div class="small text-muted">{{ $row['scheduled_at']->format('H:i') }}</div>
                                </td>
                                <td>{{ $row['learner_name'] }}</td>
                                <td><span class="badge text-bg-light">{{ $row['type'] }}</span></td>
                                <td class="text-end">{{ $row['duration_mins'] }} mins</td>
                                <td class="text-end">${{ number_format($row['gross'], 2) }}</td>
                                <td class="text-end text-muted">−${{ number_format($row['fees'], 2) }}</td>
                                <td class="text-end pe-3 fw-semibold text-success">${{ number_format($row['net'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="ps-3">Totals</th>
                            <th class="text-end">{{ array_sum(array_column($statement['items'], 'duration_mins')) }} mins</th>
                            <th class="text-end">${{ number_format($statement['totals']['gross'], 2) }}</th>
                            <th class="text-end text-muted">−${{ number_format($statement['totals']['fees'], 2) }}</th>
                            <th class="text-end pe-3 text-success">${{ number_format($statement['totals']['net'], 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ── Fee breakdown + payout bank info ── --}}
<div class="row g-3 mt-1">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="mb-3">Fees Applied</h6>
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr><td class="text-muted">Platform service fee</td><td class="text-end">${{ number_format($statement['fee_breakdown']['service_fee_per_booking'], 2) }} / booking</td></tr>
                        <tr><td class="text-muted">Payment processing fee</td><td class="text-end">${{ number_format($statement['fee_breakdown']['processing_fee_per_booking'], 2) }} / booking</td></tr>
                        <tr class="border-top"><th class="ps-0">Total fees retained</th><th class="text-end">${{ number_format($statement['fee_breakdown']['total_fees'], 2) }}</th></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="mb-3">Payout Destination</h6>
                @if($statement['instructor']['bank_account_masked'])
                    <div class="small">
                        <div><strong>BSB:</strong> {{ $statement['instructor']['bank_bsb'] }}</div>
                        <div><strong>Account:</strong> {{ $statement['instructor']['bank_account_masked'] }}</div>
                        @if($statement['instructor']['business_name'])
                            <div><strong>To:</strong> {{ $statement['instructor']['business_name'] }}</div>
                        @endif
                        @if($statement['instructor']['abn'])
                            <div><strong>ABN:</strong> {{ $statement['instructor']['abn'] }}{{ $statement['instructor']['gst_registered'] ? ' (GST registered)' : '' }}</div>
                        @endif
                    </div>
                @else
                    <div class="alert alert-warning small mb-0">
                        <i class="bi bi-exclamation-triangle"></i>
                        Bank details not on file. <a href="{{ route('instructor.settings.banking') }}">Add them now</a> so we can pay you.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
