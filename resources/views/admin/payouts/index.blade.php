@extends('layouts.admin')
@section('title', 'Payouts')
@section('heading', 'Instructor Payouts')

@section('content')
{{-- KPI Summary --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="kpi-card kpi-accent h-100">
            <div class="kpi-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="kpi-label">Pending</div>
            <div class="kpi-value">${{ number_format($summaryPending, 0) }}</div>
            <div class="small text-muted mt-1">{{ $pendingCount }} payout(s)</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card h-100">
            <div class="kpi-icon"><i class="bi bi-check2-circle"></i></div>
            <div class="kpi-label">Approved</div>
            <div class="kpi-value">${{ number_format($summaryApproved, 0) }}</div>
            <div class="small text-muted mt-1">Ready to pay</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card kpi-success h-100">
            <div class="kpi-icon"><i class="bi bi-cash-coin"></i></div>
            <div class="kpi-label">Paid This Week</div>
            <div class="kpi-value">${{ number_format($summaryPaidWeek, 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card {{ $missingBank > 0 ? 'kpi-danger' : 'kpi-teal' }} h-100">
            <div class="kpi-icon"><i class="bi bi-{{ $missingBank > 0 ? 'exclamation-triangle-fill' : 'bank' }}"></i></div>
            <div class="kpi-label">Missing Bank Details</div>
            <div class="kpi-value">{{ $missingBank }}</div>
            <div class="small text-muted mt-1">Cannot be paid</div>
        </div>
    </div>
</div>

{{-- Actions Row --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h6 class="fw-bold mb-0">All Payouts</h6>
                {{-- Status filter --}}
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.payouts.index') }}" class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                    @foreach(\App\Models\InstructorPayout::statuses() as $sk => $sl)
                        <a href="{{ route('admin.payouts.index', ['status' => $sk]) }}" class="btn {{ request('status') === $sk ? 'btn-primary' : 'btn-outline-primary' }}">{{ $sl }}</a>
                    @endforeach
                </div>
            </div>
            <div class="d-flex gap-2">
                <form method="GET" action="{{ route('admin.payouts.index') }}" class="input-group input-group-sm" style="width:220px;">
                    <input type="text" name="search" class="form-control" placeholder="Search instructor..." value="{{ request('search') }}">
                    <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                </form>
                <form method="POST" action="{{ route('admin.payouts.generate') }}" onsubmit="return confirm('Generate payouts for the most recent completed week?')">
                    @csrf
                    <button class="btn btn-warning btn-sm fw-bold"><i class="bi bi-lightning-charge me-1"></i>Generate Now</button>
                </form>
                <a href="{{ route('admin.payouts.export-csv', request()->all()) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-download me-1"></i>CSV</a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        {{-- Bulk actions --}}
        <form id="bulk-form" method="POST">
            @csrf
            <div class="d-none px-3 py-2 bg-primary-subtle d-flex align-items-center gap-2" id="bulk-bar">
                <span class="small fw-bold" id="bulk-count">0 selected</span>
                <button type="submit" formaction="{{ route('admin.payouts.bulk-approve') }}" class="btn btn-sm btn-outline-success">Approve Selected</button>
                <button type="submit" formaction="{{ route('admin.payouts.bulk-mark-paid') }}" class="btn btn-sm btn-outline-primary">Mark Selected Paid</button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px;"><input type="checkbox" class="form-check-input" id="bulk-check-all"></th>
                            <th class="small">Reference</th>
                            <th class="small">Instructor</th>
                            <th class="small">Period</th>
                            <th class="small text-center">Bookings</th>
                            <th class="small text-end">Gross</th>
                            <th class="small text-end">Fees</th>
                            <th class="small text-end">Net</th>
                            <th class="small">Status</th>
                            <th class="small text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payouts as $p)
                            @php $sc = \App\Models\InstructorPayout::statusColor($p->status); @endphp
                            <tr>
                                <td><input type="checkbox" class="form-check-input bulk-check" name="payout_ids[]" value="{{ $p->id }}"></td>
                                <td class="small fw-semibold"><a href="{{ route('admin.payouts.show', $p) }}" class="text-decoration-none">{{ $p->reference }}</a></td>
                                <td class="small">{{ $p->instructorProfile?->user?->name ?? '—' }}</td>
                                <td class="small text-muted">{{ $p->periodLabel() }}</td>
                                <td class="small text-center">{{ $p->bookings_count }}</td>
                                <td class="small text-end">${{ number_format($p->gross_amount, 2) }}</td>
                                <td class="small text-end text-danger">-${{ number_format($p->totalDeductions(), 2) }}</td>
                                <td class="small text-end fw-bold">${{ number_format($p->net_amount, 2) }}</td>
                                <td><span class="badge bg-{{ $sc }}-subtle text-{{ $sc }}">{{ ucfirst($p->status) }}</span></td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.payouts.show', $p) }}" class="btn btn-outline-primary py-0 px-2"><i class="bi bi-eye"></i></a>
                                        @if($p->isPending())
                                            <form method="POST" action="{{ route('admin.payouts.approve', $p) }}" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-outline-success py-0 px-2" title="Approve"><i class="bi bi-check-lg"></i></button>
                                            </form>
                                        @endif
                                        @if($p->isApproved())
                                            <form method="POST" action="{{ route('admin.payouts.mark-paid', $p) }}" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-outline-primary py-0 px-2" title="Mark Paid"><i class="bi bi-cash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted py-5">No payouts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    @if($payouts->hasPages())
        <div class="card-footer bg-white py-3">{{ $payouts->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
(function() {
    var all = document.getElementById('bulk-check-all');
    var bar = document.getElementById('bulk-bar');
    var countEl = document.getElementById('bulk-count');
    var checks = document.querySelectorAll('.bulk-check');

    function update() {
        var n = document.querySelectorAll('.bulk-check:checked').length;
        countEl.textContent = n + ' selected';
        bar.classList.toggle('d-none', n === 0);
        bar.classList.toggle('d-flex', n > 0);
    }
    all?.addEventListener('change', function() {
        checks.forEach(function(c) { c.checked = all.checked; });
        update();
    });
    checks.forEach(function(c) { c.addEventListener('change', update); });
})();
</script>
@endpush
@endsection
