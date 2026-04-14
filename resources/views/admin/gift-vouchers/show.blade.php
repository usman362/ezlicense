@extends('layouts.admin')
@section('title', 'Voucher ' . $voucher->code)
@section('heading', 'Gift Voucher Details')

@section('content')
<a href="{{ route('admin.gift-vouchers.index') }}" class="text-decoration-none small">&larr; Back to vouchers</a>

<div class="card border-0 shadow-sm mt-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="fw-bold mb-1">{{ $voucher->code }}</h4>
                @php
                    $statusColors = ['active'=>'success','redeemed'=>'primary','partially_redeemed'=>'info','pending'=>'warning','cancelled'=>'danger','expired'=>'secondary'];
                @endphp
                <span class="badge bg-{{ $statusColors[$voucher->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $voucher->status)) }}</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.gift-vouchers.edit', $voucher) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
                @if(!in_array($voucher->status, ['redeemed', 'cancelled']))
                    <form method="POST" action="{{ route('admin.gift-vouchers.cancel', $voucher) }}" onsubmit="return confirm('Cancel this voucher?')">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle"></i> Cancel</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <h6 class="text-muted fw-semibold small text-uppercase mb-3">Voucher Info</h6>
                <table class="table table-sm">
                    <tr><td class="text-muted" style="width:40%">Amount</td><td class="fw-semibold">${{ number_format($voucher->amount, 2) }}</td></tr>
                    <tr><td class="text-muted">Remaining</td><td class="fw-semibold">${{ number_format($voucher->remaining_amount, 2) }}</td></tr>
                    <tr><td class="text-muted">Type</td><td>{{ ucfirst(str_replace('_', ' ', $voucher->voucher_type)) }}</td></tr>
                    <tr><td class="text-muted">Payment Method</td><td>{{ ucfirst($voucher->payment_method ?? '—') }}</td></tr>
                    <tr><td class="text-muted">Payment Ref</td><td class="small">{{ $voucher->payment_reference ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Paid At</td><td>{{ $voucher->paid_at?->format('d M Y, H:i') ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Expires</td><td>{{ $voucher->expires_at?->format('d M Y') ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Created</td><td>{{ $voucher->created_at->format('d M Y, H:i') }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted fw-semibold small text-uppercase mb-3">People</h6>
                <table class="table table-sm">
                    <tr><td class="text-muted" style="width:40%">Purchaser Name</td><td>{{ $voucher->purchaser_name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Purchaser Email</td><td>{{ $voucher->purchaser_email ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Purchaser User</td><td>{{ $voucher->purchaser?->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Recipient Name</td><td>{{ $voucher->recipient_name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Recipient Email</td><td>{{ $voucher->recipient_email ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Redeemed By</td><td>{{ $voucher->redeemer?->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Redeemed At</td><td>{{ $voucher->redeemed_at?->format('d M Y, H:i') ?? '—' }}</td></tr>
                </table>

                @if($voucher->personal_message)
                    <h6 class="text-muted fw-semibold small text-uppercase mb-2 mt-3">Personal Message</h6>
                    <div class="bg-light p-3 rounded small">{{ $voucher->personal_message }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
