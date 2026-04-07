@extends('layouts.admin')

@section('title', 'Gift Vouchers')
@section('heading', 'Gift Vouchers')

@section('content')
{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h4 fw-bold text-success mb-0">${{ number_format($stats['total_sold'], 2) }}</div>
            <div class="small text-muted">Total Sold</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h4 fw-bold text-primary mb-0">${{ number_format($stats['total_redeemed'], 2) }}</div>
            <div class="small text-muted">Total Redeemed</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h4 fw-bold text-warning mb-0">{{ $stats['active_count'] }}</div>
            <div class="small text-muted">Active Vouchers</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h4 fw-bold text-info mb-0">{{ $stats['pending_count'] }}</div>
            <div class="small text-muted">Pending Payment</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center g-2">
            <div class="col-md-5">
                <form method="GET" class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search code, name, email..." value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                    @if(request()->hasAny(['search','status']))
                        <a href="{{ route('admin.gift-vouchers.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                    @endif
                </form>
            </div>
            <div class="col-md-4">
                @php $cs = request('status'); @endphp
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.gift-vouchers.index', request()->except(['status','page'])) }}" class="btn {{ !$cs ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                    <a href="{{ route('admin.gift-vouchers.index', array_merge(request()->except('page'), ['status'=>'active'])) }}" class="btn {{ $cs==='active' ? 'btn-success' : 'btn-outline-success' }}">Active</a>
                    <a href="{{ route('admin.gift-vouchers.index', array_merge(request()->except('page'), ['status'=>'redeemed'])) }}" class="btn {{ $cs==='redeemed' ? 'btn-info' : 'btn-outline-info' }}">Redeemed</a>
                    <a href="{{ route('admin.gift-vouchers.index', array_merge(request()->except('page'), ['status'=>'pending'])) }}" class="btn {{ $cs==='pending' ? 'btn-warning' : 'btn-outline-warning' }}">Pending</a>
                </div>
            </div>
            <div class="col-md-3 text-end">
                <a href="{{ route('admin.gift-vouchers.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>Create Voucher</a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small">Code</th>
                        <th class="small">Type</th>
                        <th class="small">Amount</th>
                        <th class="small">Remaining</th>
                        <th class="small">Purchaser</th>
                        <th class="small">Recipient</th>
                        <th class="small">Status</th>
                        <th class="small">Created</th>
                        <th class="small text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $v)
                        @php
                            $sColors = ['pending'=>'warning','paid'=>'info','active'=>'success','redeemed'=>'primary','partially_redeemed'=>'info','expired'=>'secondary','cancelled'=>'danger'];
                            $sColor = $sColors[$v->status] ?? 'secondary';
                        @endphp
                        <tr>
                            <td><code class="fw-bold">{{ $v->code }}</code></td>
                            <td class="small">{{ \App\Models\GiftVoucher::typeLabels()[$v->voucher_type] ?? $v->voucher_type }}</td>
                            <td class="small fw-semibold">${{ number_format($v->amount, 2) }}</td>
                            <td class="small">${{ number_format($v->remaining_amount, 2) }}</td>
                            <td class="small">
                                <div>{{ $v->purchaser_name ?? '—' }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $v->purchaser_email ?? '' }}</div>
                            </td>
                            <td class="small">
                                <div>{{ $v->recipient_name ?? '—' }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $v->recipient_email ?? '' }}</div>
                            </td>
                            <td><span class="badge bg-{{ $sColor }}">{{ ucfirst(str_replace('_', ' ', $v->status)) }}</span></td>
                            <td class="small text-muted">{{ $v->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                @if(!in_array($v->status, ['redeemed', 'cancelled']))
                                    <form method="POST" action="{{ route('admin.gift-vouchers.cancel', $v) }}" class="d-inline" onsubmit="return confirm('Cancel this voucher?')">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-outline-danger btn-sm py-0 px-2"><i class="bi bi-x-circle"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-muted text-center py-4">No gift vouchers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($vouchers->hasPages())
        <div class="card-footer bg-white">{{ $vouchers->links() }}</div>
    @endif
</div>
@endsection
