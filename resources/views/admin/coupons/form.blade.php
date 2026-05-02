@extends('layouts.admin')

@section('title', $coupon->exists ? 'Edit Coupon' : 'New Coupon')
@section('heading', $coupon->exists ? 'Edit Coupon: ' . $coupon->code : 'Create New Coupon')

@section('content')
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}">
    @csrf
    @if($coupon->exists) @method('PUT') @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Coupon Details</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" value="{{ old('code', $coupon->code) }}" class="form-control text-uppercase" placeholder="e.g. WELCOME10" required style="font-family:monospace;font-weight:bold;">
                            <div class="form-text">Letters, numbers, dashes, underscores only. Auto-uppercased.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Description (optional)</label>
                            <input type="text" name="description" value="{{ old('description', $coupon->description) }}" class="form-control" placeholder="e.g. New customer welcome offer">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" id="couponType">
                                <option value="percent" {{ old('type', $coupon->type) === 'percent' ? 'selected' : '' }}>Percentage off (%)</option>
                                <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed amount off ($)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" id="amountPrefix">{{ ($coupon->type ?? 'percent') === 'percent' ? '%' : '$' }}</span>
                                <input type="number" name="amount" value="{{ old('amount', $coupon->amount) }}" class="form-control" step="0.01" min="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Max Discount Cap ($)</label>
                            <input type="number" name="max_discount_amount" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}" class="form-control" step="0.01" min="0" placeholder="Optional">
                            <div class="form-text">Caps % discount at $ amount (e.g. 10% but max $50). Leave blank for no cap.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Min Order Amount ($)</label>
                            <input type="number" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount ?? 0) }}" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4 pt-2">
                                <input type="checkbox" name="first_booking_only" value="1" id="fbo" class="form-check-input" {{ old('first_booking_only', $coupon->first_booking_only) ? 'checked' : '' }}>
                                <label for="fbo" class="form-check-label">First booking only</label>
                                <div class="form-text">Only redeemable on a learner's very first paid booking.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Validity & Limits</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Starts At</label>
                            <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Expires At</label>
                            <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Max Total Uses</label>
                            <input type="number" name="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}" class="form-control" min="1" placeholder="Unlimited">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Max Uses Per User <span class="text-danger">*</span></label>
                            <input type="number" name="max_uses_per_user" value="{{ old('max_uses_per_user', $coupon->max_uses_per_user ?? 1) }}" class="form-control" min="1" max="100" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Status</strong></div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="ia" class="form-check-input" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
                        <label for="ia" class="form-check-label">Active (redeemable now)</label>
                    </div>
                    @if($coupon->exists && $coupon->used_count > 0)
                        <div class="alert alert-info mt-3 mb-0 small">
                            <i class="bi bi-info-circle me-1"></i> This coupon has been used <strong>{{ $coupon->used_count }}</strong> time(s).
                        </div>
                    @endif
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> {{ $coupon->exists ? 'Update Coupon' : 'Create Coupon' }}</button>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.getElementById('couponType').addEventListener('change', function() {
    document.getElementById('amountPrefix').textContent = this.value === 'percent' ? '%' : '$';
});
</script>
@endpush
@endsection
