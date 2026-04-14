@extends('layouts.admin')
@section('title', 'Edit Voucher ' . $voucher->code)
@section('heading', 'Edit Gift Voucher')

@section('content')
<div class="container-fluid p-4" style="max-width: 700px;">
    <a href="{{ route('admin.gift-vouchers.index') }}" class="text-decoration-none small">&larr; Back to vouchers</a>
    <h1 class="h3 mt-2 mb-4">Edit Voucher {{ $voucher->code }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('admin.gift-vouchers.update', $voucher) }}">
        @csrf
        @method('PUT')

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Voucher Info (read-only)</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Code</label>
                        <p class="fw-bold mb-0">{{ $voucher->code }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Amount</label>
                        <p class="fw-bold mb-0">${{ number_format($voucher->amount, 2) }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Status</label>
                        <p class="fw-bold mb-0">{{ ucfirst(str_replace('_', ' ', $voucher->status)) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Recipient Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Recipient Name *</label>
                        <input type="text" name="recipient_name" class="form-control" required value="{{ old('recipient_name', $voucher->recipient_name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Recipient Email *</label>
                        <input type="email" name="recipient_email" class="form-control" required value="{{ old('recipient_email', $voucher->recipient_email) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Personal Message</label>
                        <textarea name="personal_message" rows="3" class="form-control" maxlength="500">{{ old('personal_message', $voucher->personal_message) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at', $voucher->expires_at?->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-lg">Save Changes</button>
            <a href="{{ route('admin.gift-vouchers.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
