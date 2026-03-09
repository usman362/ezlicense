@extends('layouts.instructor')

@section('title', 'Documents')
@section('heading', 'Settings › Documents')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.settings.personal-details') }}">Settings</a></li>
        <li class="breadcrumb-item active" aria-current="page">Documents</li>
    </ol>
</nav>

<ul class="nav nav-tabs border-0 small mb-3">
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.personal-details') }}">Personal Details</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.profile') }}">Profile</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.vehicle') }}">Vehicles</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.service-area') }}">Service Area</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.opening-hours') }}">Opening Hours</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.calendar-settings') }}">Calendar Settings</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.pricing') }}">Pricing</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('instructor.settings.documents') }}">Documents</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('instructor.settings.banking') }}">Banking</a></li>
</ul>

<ul class="nav nav-tabs border-0 small mb-4">
    <li class="nav-item"><a class="nav-link active" href="#" id="tab-your-documents" data-bs-toggle="tab">Your Documents</a></li>
    <li class="nav-item"><a class="nav-link text-dark" href="#" id="tab-submissions-history" data-bs-toggle="tab">Submissions History</a></li>
</ul>

<div id="documents-loading" class="text-muted">Loading…</div>

<div id="panel-your-documents" class="tab-pane-content">
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-1">Driver's Licence (C)</h6>
                    <p class="small text-muted mb-1">Expiration date: <span id="doc-drivers-expiry">—</span></p>
                    <div class="small">
                        <div><span id="doc-drivers-front-status" class="text-muted">Driver's Licence (C) - Front —</span></div>
                        <div><span id="doc-drivers-back-status" class="text-muted">Driver's Licence (C) - Back —</span></div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" data-doc-modal="drivers_licence"><i class="bi bi-upload me-1"></i> Submit New Document</button>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-1">Driving Instructor's Licence (C)</h6>
                    <p class="small text-muted mb-1">Expiration date: <span id="doc-instructor-expiry">—</span></p>
                    <div class="small"><span id="doc-instructor-status" class="text-muted">Driving Instructor's Licence (C) —</span></div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" data-doc-modal="instructor_licence"><i class="bi bi-upload me-1"></i> Submit New Document</button>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-1">Working With Children Check (WWCC)</h6>
                    <p class="small text-muted mb-1">Expiration date: <span id="doc-wwcc-expiry">—</span></p>
                    <div class="small"><span id="doc-wwcc-status" class="text-muted">WWCC —</span></div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" data-doc-modal="wwcc"><i class="bi bi-upload me-1"></i> Submit New Document</button>
            </div>
        </div>
    </div>
</div>

<div id="panel-submissions-history" class="tab-pane-content" style="display: none;">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-sm mb-0">
                <thead><tr><th>Submission date</th><th>Status</th><th>Document</th></tr></thead>
                <tbody id="submissions-tbody"></tbody>
            </table>
            <p id="submissions-empty" class="text-muted small mb-0 mt-2" style="display: none;">No instructor submissions found.</p>
        </div>
    </div>
</div>

{{-- Modal: Driver's Licence (C) --}}
<div class="modal fade" id="modal-drivers-licence" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Driver's Licence (C)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border small mb-3">
                    <i class="bi bi-info-circle me-1"></i> <strong>Verification document requirements:</strong>
                    <ul class="mb-0 mt-1"><li>Image must be clear, with all edges included</li><li>Image must be in colour</li><li>Image must be in .png or .jpg format</li><li>Image must be at least 500 pixels wide</li></ul>
                </div>
                <div class="mb-3">
                    <label class="form-label">Driver's Licence (C) - Front</label>
                    <input type="file" class="form-control" name="front_file" accept=".jpg,.jpeg,.png,.pdf">
                </div>
                <div class="mb-3">
                    <label class="form-label">Driver's Licence (C) - Back</label>
                    <input type="file" class="form-control" name="back_file" accept=".jpg,.jpeg,.png,.pdf">
                </div>
                <div class="mb-3">
                    <label class="form-label">Expiration Date</label>
                    <input type="date" class="form-control" name="expires_at">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning text-dark" id="submit-drivers-licence">Submit for Review</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Driving Instructor's Licence (C) --}}
<div class="modal fade" id="modal-instructor-licence" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Driving Instructor's Licence (C)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border small mb-3">
                    <i class="bi bi-info-circle me-1"></i> <strong>Verification document requirements:</strong>
                    <ul class="mb-0 mt-1"><li>Image must be clear, with all edges included</li><li>Image must be in colour</li><li>Image must be in .png or .jpg format</li><li>Image must be at least 500 pixels wide</li></ul>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Driving Instructor's Licence (C)</label>
                    <input type="file" class="form-control" name="front_file" accept=".jpg,.jpeg,.png,.pdf">
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Instructor Licence Back</label>
                    <input type="file" class="form-control" name="back_file" accept=".jpg,.jpeg,.png,.pdf">
                </div>
                <div class="mb-3">
                    <label class="form-label">Expiration Date</label>
                    <input type="date" class="form-control" name="expires_at">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning text-dark" id="submit-instructor-licence">Submit for Review</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: WWCC --}}
<div class="modal fade" id="modal-wwcc" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Working With Children Check (WWCC)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">WWCC number</label>
                    <input type="text" class="form-control" name="wwcc_number" placeholder="e.g. WWC29996896">
                </div>
                <div class="mb-3">
                    <label class="form-label">WWCC expiry date</label>
                    <input type="date" class="form-control" name="expires_at">
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload document (optional)</label>
                    <input type="file" class="form-control" name="file" accept=".jpg,.jpeg,.png,.pdf">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning text-dark" id="submit-wwcc">Submit for Review</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/instructor-settings-documents.js')
@endpush
@endsection
