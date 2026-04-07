@extends('layouts.admin')

@section('title', 'Create Gift Voucher')
@section('heading')
    <a href="{{ route('admin.gift-vouchers.index') }}" class="text-decoration-none text-muted me-2"><i class="bi bi-arrow-left"></i></a>
    Create Gift Voucher
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.gift-vouchers.store') }}">
                    @csrf

                    <h6 class="fw-bold mb-3">Voucher Type</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="form-check card border p-3 h-100">
                                <input class="form-check-input" type="radio" name="voucher_type" value="1hour" id="type1hour" checked>
                                <label class="form-check-label w-100" for="type1hour">
                                    <div class="fw-bold">1 Hour Lesson</div>
                                    <div class="text-warning fw-bold fs-5">$65.00</div>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check card border p-3 h-100">
                                <input class="form-check-input" type="radio" name="voucher_type" value="5hour" id="type5hour">
                                <label class="form-check-label w-100" for="type5hour">
                                    <div class="fw-bold">5 Hour Package</div>
                                    <div class="text-warning fw-bold fs-5">$300.00</div>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check card border p-3 h-100">
                                <input class="form-check-input" type="radio" name="voucher_type" value="custom" id="typeCustom">
                                <label class="form-check-label w-100" for="typeCustom">
                                    <div class="fw-bold">Custom Amount</div>
                                    <div class="text-muted small">$50 minimum</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="custom-amount-section" style="display:none;" class="mb-4">
                        <label class="form-label fw-semibold">Custom Amount ($)</label>
                        <input type="number" name="custom_amount" class="form-control" min="50" max="5000" step="5" placeholder="50.00" value="{{ old('custom_amount') }}">
                        @error('custom_amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <h6 class="fw-bold mb-3">Recipient Details</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small">Recipient Name</label>
                            <input type="text" name="recipient_name" class="form-control" required value="{{ old('recipient_name') }}">
                            @error('recipient_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Recipient Email</label>
                            <input type="email" name="recipient_email" class="form-control" required value="{{ old('recipient_email') }}">
                            @error('recipient_email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small">Personal Message (optional)</label>
                        <textarea name="personal_message" class="form-control" rows="3" maxlength="500" placeholder="Add a personal message...">{{ old('personal_message') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.gift-vouchers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-gift me-1"></i>Create & Activate Voucher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('input[name="voucher_type"]').forEach(r => {
    r.addEventListener('change', () => {
        document.getElementById('custom-amount-section').style.display = r.value === 'custom' ? 'block' : 'none';
    });
});
</script>
@endpush
@endsection
