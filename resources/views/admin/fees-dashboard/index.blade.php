@extends('layouts.admin')

@section('title', 'Fees Dashboard')
@section('heading', 'Fees Dashboard — Real-time P&L')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-3 flex-wrap gap-3">
    <form method="GET" class="d-flex gap-2 align-items-end">
        <div>
            <label class="form-label small fw-semibold mb-1">From</label>
            <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control form-control-sm">
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">To</label>
            <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control form-control-sm">
        </div>
        <button class="btn btn-warning btn-sm fw-bold"><i class="bi bi-funnel"></i> Apply</button>
    </form>

    <a href="{{ route('admin.fees-dashboard.export', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-download me-1"></i> Export CSV
    </a>
</div>

{{-- ── KPI cards ── --}}
<div class="row g-3 mb-4">
    @php
        $kpis = [
            ['Bookings', $totals['bookings'], 'bi-receipt', 'primary', null],
            ['Learners Paid', '$' . number_format($totals['learner_paid'], 2), 'bi-cash-stack', 'info', null],
            ['Instructors Got', '$' . number_format($totals['instructor_got'], 2), 'bi-person-arms-up', 'success', null],
            ['Stripe Took', '−$' . number_format($totals['stripe_took'], 2), 'bi-credit-card-2-front', 'warning', null],
            ['Platform NET', '$' . number_format($totals['platform_net'], 2), 'bi-wallet2', 'success', $totals['margin_pct'] . '% margin'],
        ];
    @endphp
    @foreach($kpis as [$label, $val, $icon, $color, $sub])
        <div class="col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="text-muted small mb-0 text-uppercase" style="letter-spacing:.05em;">{{ $label }}</h6>
                        <i class="bi {{ $icon }} text-{{ $color }} fs-4"></i>
                    </div>
                    <div class="fs-4 fw-bold">{{ $val }}</div>
                    @if($sub)
                        <div class="small text-muted mt-1">{{ $sub }}</div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ── Fee breakdown summary ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Where the money flowed</h6>
        <div class="row g-3 small">
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted">Service fee collected</div>
                    <div class="fs-5 fw-bold">${{ number_format($totals['service_fee'], 2) }}</div>
                    <div class="small text-muted">Flat $5 per lesson booking</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted">Processing fee collected</div>
                    <div class="fs-5 fw-bold">${{ number_format($totals['processing_fee'], 2) }}</div>
                    <div class="small text-muted">$2 per single lesson (waived on 5+ packages)</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 h-100" style="background:#fff8e1;">
                    <div class="text-muted">Net into our wallet</div>
                    <div class="fs-5 fw-bold text-success">${{ number_format($totals['platform_net'], 2) }}</div>
                    <div class="small text-muted">After Stripe takes their cut ({{ $totals['margin_pct'] }}% of total)</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Per-booking table ── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <strong>Per-booking breakdown</strong>
        <span class="small text-muted">({{ $bookings->total() }} bookings in range)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Date Paid</th>
                    <th>Learner</th>
                    <th>Instructor</th>
                    <th class="text-end">Lesson</th>
                    <th class="text-end">Service</th>
                    <th class="text-end">Proc.</th>
                    <th class="text-end">Stripe</th>
                    <th class="text-end pe-3">Net</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                    @php
                        $amount     = (float) $b->amount;
                        $service    = (float) ($b->platform_fee ?? 0);
                        $processing = (float) ($b->processing_fee ?? 0);
                        $stripe     = (float) ($b->stripe_fee_estimate ?? 0);
                        $platformNet = $service + $processing - $stripe;
                    @endphp
                    <tr>
                        <td class="ps-3"><code class="small">#{{ $b->id }}</code></td>
                        <td>
                            <div class="small">{{ $b->updated_at?->format('j M Y') }}</div>
                            <div class="small text-muted">{{ $b->updated_at?->format('H:i') }}</div>
                        </td>
                        <td class="small">{{ $b->learner?->name ?? '—' }}</td>
                        <td class="small">{{ $b->instructor?->name ?? '—' }}</td>
                        <td class="text-end">${{ number_format($amount, 2) }}</td>
                        <td class="text-end">${{ number_format($service, 2) }}</td>
                        <td class="text-end">
                            @if($processing > 0)
                                ${{ number_format($processing, 2) }}
                            @else
                                <span class="badge text-bg-success small">waived</span>
                            @endif
                        </td>
                        <td class="text-end text-muted">−${{ number_format($stripe, 2) }}</td>
                        <td class="text-end pe-3 fw-semibold {{ $platformNet >= 0 ? 'text-success' : 'text-danger' }}">
                            ${{ number_format($platformNet, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-5">No paid bookings in this date range.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
        <div class="card-footer bg-white">{{ $bookings->links() }}</div>
    @endif
</div>

<div class="alert alert-light border mt-3 small">
    <i class="bi bi-info-circle me-1"></i>
    Stripe fee is an estimate (1.7% + $0.30 per transaction — AU domestic cards).
    Actual figures may differ slightly for international cards or refunded charges. Tune in Settings → Commission.
</div>
@endsection
