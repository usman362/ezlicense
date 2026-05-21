@extends('layouts.instructor')

@section('title', 'Documents')
@section('heading', 'Settings › Documents')

@section('content')

<div class="sett-page">
@include('instructor.settings.partials.header', [
    'current'     => 'documents',
    'title'       => 'Verification Documents',
    'description' => 'Upload your driving instructor licence, WWCC and insurance. Stored privately on encrypted cloud storage.',
])

@php
    $sl_profile = Auth::user()?->instructorProfile;
    $sl_vstatus = $sl_profile?->verification_status ?? 'pending';
    $sl_isRejected = $sl_vstatus === 'rejected';
    $sl_adminNotes = $sl_profile?->admin_notes;
@endphp

@if(session('onboarding_notice') || in_array($sl_vstatus, ['pending', 'rejected'], true))
    <div class="onb-banner {{ $sl_isRejected ? 'onb-banner-danger' : 'onb-banner-warning' }} mb-3">
        <div class="onb-banner-icon">
            <i class="bi {{ $sl_isRejected ? 'bi-exclamation-triangle-fill' : 'bi-rocket-takeoff-fill' }}"></i>
        </div>
        <div class="onb-banner-body">
            <h3 class="onb-banner-title">
                {{ $sl_isRejected ? 'Your documents need attention' : 'Welcome! Upload your documents to get started' }}
            </h3>
            <p class="onb-banner-text">
                @if($sl_isRejected)
                    Our admin has flagged something with your verification. Please review the feedback below and re-upload the affected documents to continue.
                @else
                    {{ session('onboarding_notice') ?: 'Until your driver licence, instructor licence and WWCC are uploaded and approved, your account stays in setup mode. You can\'t accept bookings or use other portal features yet.' }}
                @endif
            </p>
            @if($sl_isRejected && $sl_adminNotes)
                <div class="onb-banner-feedback">
                    <strong><i class="bi bi-chat-left-text me-1"></i>Admin feedback:</strong>
                    <p class="mb-0">{{ $sl_adminNotes }}</p>
                </div>
            @endif
        </div>
    </div>
    <style>
    .onb-banner {
        display: flex; gap: 1rem; align-items: flex-start;
        padding: 1.1rem 1.4rem; border-radius: 14px;
    }
    .onb-banner-warning { background: linear-gradient(135deg, #fef3c7, #fde68a); border: 1px solid #fcd34d; color: #92400e; }
    .onb-banner-danger  { background: linear-gradient(135deg, #fee2e2, #fecaca); border: 1px solid #fca5a5; color: #991b1b; }
    .onb-banner-icon {
        width: 48px; height: 48px; border-radius: 12px;
        background: rgba(255,255,255,0.5);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.5rem; flex-shrink: 0;
    }
    .onb-banner-body { flex: 1; min-width: 0; }
    .onb-banner-title { font-size: 1.05rem; font-weight: 800; margin: 0 0 0.25rem; }
    .onb-banner-text { font-size: 0.92rem; line-height: 1.55; margin: 0; }
    .onb-banner-feedback {
        margin-top: 0.85rem; padding: 0.7rem 0.9rem;
        background: rgba(255,255,255,0.55); border-radius: 8px;
        font-size: 0.88rem; line-height: 1.5;
    }
    </style>
@endif

<div class="sett-callout">
    <i class="bi bi-shield-fill-check"></i>
    <div>
        All documents are stored privately on encrypted cloud storage and only viewable by our verification team via signed time-limited URLs.
        Verification typically completes within <strong>24-48 hours</strong>.
    </div>
</div>

<div class="sett-doc-subtabs mb-3">
    <button type="button" class="sett-doc-subtab active" id="tab-your-documents" data-bs-toggle="tab">
        <i class="bi bi-folder-fill me-1"></i>Your Documents
    </button>
    <button type="button" class="sett-doc-subtab" id="tab-submissions-history" data-bs-toggle="tab">
        <i class="bi bi-clock-history me-1"></i>Submissions History
    </button>
</div>

<div id="documents-loading" class="sett-loading">
    <div class="spinner-border spinner-border-sm text-warning me-2"></div>Loading your documents…
</div>

<div id="panel-your-documents" class="tab-pane-content">

    {{-- Driver's Licence --}}
    <div class="sett-doc-card" data-status-target="drivers">
        <div class="sett-doc-stripe" id="doc-drivers-stripe"></div>
        <div class="sett-doc-body">
            <div class="sett-doc-icon"><i class="bi bi-person-vcard-fill"></i></div>
            <div class="sett-doc-main">
                <div class="sett-doc-head">
                    <h3 class="sett-doc-title">Driver's Licence (C)</h3>
                    <span class="sett-doc-status" id="doc-drivers-overall"><i class="bi bi-circle"></i>Not submitted</span>
                </div>
                <div class="sett-doc-meta">
                    <span class="sett-doc-meta-item"><i class="bi bi-calendar-event"></i>Expires <span id="doc-drivers-expiry">—</span></span>
                </div>
                <div class="sett-doc-parts">
                    <div class="sett-doc-part">
                        <span class="sett-doc-part-label">Front</span>
                        <span id="doc-drivers-front-status" class="sett-doc-part-status text-muted">—</span>
                    </div>
                    <div class="sett-doc-part">
                        <span class="sett-doc-part-label">Back</span>
                        <span id="doc-drivers-back-status" class="sett-doc-part-status text-muted">—</span>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-warning fw-bold sett-doc-btn" data-doc-modal="drivers_licence">
                <i class="bi bi-upload me-1"></i>Upload
            </button>
        </div>
    </div>

    {{-- Instructor Licence --}}
    <div class="sett-doc-card" data-status-target="instructor">
        <div class="sett-doc-stripe" id="doc-instructor-stripe"></div>
        <div class="sett-doc-body">
            <div class="sett-doc-icon sett-doc-icon-blue"><i class="bi bi-mortarboard-fill"></i></div>
            <div class="sett-doc-main">
                <div class="sett-doc-head">
                    <h3 class="sett-doc-title">Driving Instructor's Licence (C)</h3>
                    <span class="sett-doc-status" id="doc-instructor-overall"><i class="bi bi-circle"></i>Not submitted</span>
                </div>
                <div class="sett-doc-meta">
                    <span class="sett-doc-meta-item"><i class="bi bi-calendar-event"></i>Expires <span id="doc-instructor-expiry">—</span></span>
                </div>
                <div class="sett-doc-parts">
                    <div class="sett-doc-part">
                        <span class="sett-doc-part-label">Document</span>
                        <span id="doc-instructor-status" class="sett-doc-part-status text-muted">—</span>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-warning fw-bold sett-doc-btn" data-doc-modal="instructor_licence">
                <i class="bi bi-upload me-1"></i>Upload
            </button>
        </div>
    </div>

    {{-- WWCC --}}
    <div class="sett-doc-card" data-status-target="wwcc">
        <div class="sett-doc-stripe" id="doc-wwcc-stripe"></div>
        <div class="sett-doc-body">
            <div class="sett-doc-icon sett-doc-icon-green"><i class="bi bi-shield-check"></i></div>
            <div class="sett-doc-main">
                <div class="sett-doc-head">
                    <h3 class="sett-doc-title">Working With Children Check (WWCC)</h3>
                    <span class="sett-doc-status" id="doc-wwcc-overall"><i class="bi bi-circle"></i>Not submitted</span>
                </div>
                <div class="sett-doc-meta">
                    <span class="sett-doc-meta-item"><i class="bi bi-calendar-event"></i>Expires <span id="doc-wwcc-expiry">—</span></span>
                </div>
                <div class="sett-doc-parts">
                    <div class="sett-doc-part">
                        <span class="sett-doc-part-label">Document</span>
                        <span id="doc-wwcc-status" class="sett-doc-part-status text-muted">—</span>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-warning fw-bold sett-doc-btn" data-doc-modal="wwcc">
                <i class="bi bi-upload me-1"></i>Upload
            </button>
        </div>
    </div>
</div>

<div id="panel-submissions-history" class="tab-pane-content" style="display: none;">
    <div class="sett-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 bk-history-table">
                    <thead>
                        <tr>
                            <th>Submitted</th>
                            <th>Document</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="submissions-tbody"></tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="submissions-empty" class="bk-empty" style="display: none;">
        <i class="bi bi-folder2-open bk-empty-icon"></i>
        <h5>No submissions yet</h5>
        <p>Once you upload documents above, your submission history will appear here.</p>
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

</div> {{-- /.sett-page --}}
@endsection
