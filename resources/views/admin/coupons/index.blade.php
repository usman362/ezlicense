@extends('layouts.admin')

@section('title', 'Coupons')
@section('heading', 'Coupons & Promo Codes')

@section('content')
@if(session('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- KPI cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Total Coupons</div>
            <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Active</div>
            <div class="fs-3 fw-bold text-success">{{ $stats['active'] }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Total Redemptions</div>
            <div class="fs-3 fw-bold">{{ $stats['redemptions'] }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Total Discount Given</div>
            <div class="fs-3 fw-bold text-warning">${{ number_format($stats['discount_given'], 2) }}</div>
        </div></div>
    </div>
</div>

{{-- Filters + create --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Code or description...">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-outline-secondary w-100"><i class="bi bi-funnel"></i> Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-sm btn-primary w-100"><i class="bi bi-plus-lg"></i> New Coupon</a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Discount</th>
                    <th>Min Order</th>
                    <th>Used</th>
                    <th>Validity</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $c)
                    <tr>
                        <td>
                            <strong style="font-family: monospace;">{{ $c->code }}</strong>
                            @if($c->description)
                                <div class="small text-muted">{{ $c->description }}</div>
                            @endif
                            @if($c->first_booking_only)
                                <span class="badge bg-info-subtle text-info-emphasis small">First booking only</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $c->type === 'percent' ? 'primary' : 'success' }}-subtle text-{{ $c->type === 'percent' ? 'primary' : 'success' }}-emphasis">
                                {{ ucfirst($c->type) }}
                            </span>
                        </td>
                        <td>
                            <strong>
                                @if($c->type === 'percent')
                                    {{ rtrim(rtrim(number_format($c->amount, 2), '0'), '.') }}%
                                @else
                                    ${{ number_format($c->amount, 2) }}
                                @endif
                            </strong>
                            @if($c->max_discount_amount)
                                <div class="small text-muted">max ${{ number_format($c->max_discount_amount, 2) }}</div>
                            @endif
                        </td>
                        <td>${{ number_format($c->min_order_amount, 2) }}</td>
                        <td>
                            {{ $c->used_count }} / {{ $c->max_uses ?? '∞' }}
                            <div class="small text-muted">{{ $c->redemptions_count }} redemptions</div>
                        </td>
                        <td class="small">
                            @if($c->starts_at) From {{ $c->starts_at->format('d M Y') }}<br> @endif
                            @if($c->expires_at) Until {{ $c->expires_at->format('d M Y') }} @else <span class="text-muted">No expiry</span> @endif
                        </td>
                        <td>
                            @if($c->is_active && (!$c->expires_at || $c->expires_at->isFuture()))
                                <span class="badge bg-success">Active</span>
                            @elseif($c->expires_at && $c->expires_at->isPast())
                                <span class="badge bg-secondary">Expired</span>
                            @else
                                <span class="badge bg-warning text-dark">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('admin.coupons.edit', $c) }}"><i class="bi bi-pencil me-1"></i> Edit</a></li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.coupons.toggle', $c) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-{{ $c->is_active ? 'pause-circle' : 'play-circle' }} me-1"></i>
                                                {{ $c->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.coupons.destroy', $c) }}" class="d-inline" onsubmit="return confirm('Delete coupon {{ $c->code }} permanently?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-1"></i> Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-ticket-detailed fs-1 d-block mb-2"></i>
                        No coupons yet. <a href="{{ route('admin.coupons.create') }}">Create your first coupon</a>.
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($coupons->hasPages())
        <div class="card-footer bg-white">{{ $coupons->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
