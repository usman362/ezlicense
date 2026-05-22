@extends('layouts.instructor')

@section('title', 'Support')
@section('heading', 'Support')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item active">Support</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h4 class="fw-bolder mb-0"><i class="bi bi-life-preserver text-warning me-2"></i>Submit a request</h4>
        <p class="text-muted small mb-0">Get help from our team — we usually reply within 1-2 business days.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 d-flex gap-2 align-items-center" role="alert">
        <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger border-0 d-flex gap-2 align-items-center" role="alert">
        <i class="bi bi-x-circle-fill"></i>{{ session('error') }}
    </div>
@endif

<div class="row g-4">
    {{-- Form column --}}
    <div class="col-lg-8">
        <div class="sett-card">
            <div class="sett-card-body">
                @if($errors->any())
                    <div class="alert alert-danger small">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('instructor.support.submit') }}" method="POST" enctype="multipart/form-data" id="support-form">
                    @csrf

                    {{-- Step 1: Category --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Please select your type of enquiry <span class="text-danger">*</span></label>
                        <select name="category" id="support-category" class="form-select" required>
                            <option value="">Choose a category…</option>
                            <option value="account"     {{ old('category') === 'account'     ? 'selected' : '' }}>My account</option>
                            <option value="payments"    {{ old('category') === 'payments'    ? 'selected' : '' }}>My payments &amp; payouts</option>
                            <option value="bookings"    {{ old('category') === 'bookings'    ? 'selected' : '' }}>My bookings</option>
                            <option value="learners"    {{ old('category') === 'learners'    ? 'selected' : '' }}>Learner / instructor relationship</option>
                            <option value="documents"   {{ old('category') === 'documents'   ? 'selected' : '' }}>Documents &amp; verification</option>
                            <option value="dispute"     {{ old('category') === 'dispute'     ? 'selected' : '' }}>Lodge a dispute</option>
                            <option value="feedback"    {{ old('category') === 'feedback'    ? 'selected' : '' }}>Provide feedback / complaint</option>
                            <option value="technical"   {{ old('category') === 'technical'   ? 'selected' : '' }}>Technical issue</option>
                            <option value="general"     {{ old('category') === 'general'     ? 'selected' : '' }}>General enquiry</option>
                        </select>
                    </div>

                    {{-- Step 2: Sub-category (shown based on category via JS) --}}
                    <div class="mb-4 support-sub-wrap" id="support-sub-wrap" style="display:none;">
                        <label class="form-label fw-bold">Which best describes your situation?</label>
                        <select name="sub_category" id="support-sub" class="form-select">
                            <option value="">Choose…</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        {{-- Subject --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Subject or topic <span class="text-muted">(optional)</span></label>
                            <input type="text" name="subject" class="form-control" maxlength="150" value="{{ old('subject') }}" placeholder="Brief summary, e.g. 'Reschedule booking #1234'">
                        </div>

                        {{-- Order/booking number --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Order / Booking number <span class="text-muted">(optional)</span></label>
                            <input type="text" name="order_number" class="form-control" value="{{ old('order_number') }}" placeholder="e.g. 1234">
                        </div>

                        {{-- Postcode --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Your suburb / postcode <span class="text-muted">(optional)</span></label>
                            <input type="text" name="postcode" class="form-control" value="{{ old('postcode') }}" placeholder="e.g. 2000">
                        </div>

                        {{-- Transmission --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Vehicle transmission <span class="text-muted">(optional)</span></label>
                            <select name="transmission" class="form-select">
                                <option value="">—</option>
                                <option value="auto"   {{ old('transmission') === 'auto'   ? 'selected' : '' }}>Auto</option>
                                <option value="manual" {{ old('transmission') === 'manual' ? 'selected' : '' }}>Manual</option>
                                <option value="both"   {{ old('transmission') === 'both'   ? 'selected' : '' }}>Both</option>
                            </select>
                        </div>
                    </div>

                    {{-- Message --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Tell us more <span class="text-danger">*</span></label>
                        <textarea name="message" rows="7" class="form-control" required minlength="10" maxlength="5000" placeholder="Share as much detail as you can — dates, booking IDs, learner names. The more context you provide, the faster we can help.">{{ old('message') }}</textarea>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>Some info may be considered sensitive — by submitting you consent to us processing it. See our <a href="/privacy" target="_blank">Privacy Policy</a>.
                        </div>
                    </div>

                    {{-- Attachments --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Attachments <span class="text-muted">(optional, max 5 files · 5MB each)</span></label>
                        <div class="support-upload">
                            <input type="file" name="attachments[]" id="support-files" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <small class="text-muted d-block mt-1"><i class="bi bi-paperclip me-1"></i>Screenshots, PDFs, or documents that help explain your issue.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('instructor.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-warning fw-bold">
                            <i class="bi bi-send-fill me-1"></i>Submit request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Help sidebar --}}
    <div class="col-lg-4">
        <div class="sett-card">
            <div class="sett-card-body">
                <div class="sett-rate-header mb-3">
                    <div class="sett-rate-icon sett-rate-icon-green"><i class="bi bi-lightbulb-fill"></i></div>
                    <div>
                        <h3 class="sett-section-title">Quick answers</h3>
                        <p class="sett-section-desc mb-0">Check these first — most issues have a self-serve fix.</p>
                    </div>
                </div>
                <ul class="support-quick-links">
                    <li><a href="{{ route('instructor.settings.documents') }}"><i class="bi bi-arrow-right-circle"></i>Update or re-upload my documents</a></li>
                    <li><a href="{{ route('instructor.settings.banking') }}"><i class="bi bi-arrow-right-circle"></i>Change my bank / payout details</a></li>
                    <li><a href="{{ route('instructor.settings.opening-hours') }}"><i class="bi bi-arrow-right-circle"></i>Edit my availability / opening hours</a></li>
                    <li><a href="{{ route('instructor.settings.pricing') }}"><i class="bi bi-arrow-right-circle"></i>Update my lesson rates</a></li>
                    <li><a href="{{ route('instructor.calendar') }}"><i class="bi bi-arrow-right-circle"></i>Reschedule a booking</a></li>
                    <li><a href="{{ route('instructor.settings.guide') }}"><i class="bi bi-arrow-right-circle"></i>Getting started guide</a></li>
                </ul>
            </div>
        </div>

        <div class="sett-card">
            <div class="sett-card-body">
                <div class="sett-rate-header mb-3">
                    <div class="sett-rate-icon sett-rate-icon-blue"><i class="bi bi-envelope-fill"></i></div>
                    <div>
                        <h3 class="sett-section-title">Other ways to reach us</h3>
                        <p class="sett-section-desc mb-0">Prefer email or phone? Choose the channel that suits.</p>
                    </div>
                </div>
                <ul class="support-contact-list">
                    <li>
                        <i class="bi bi-envelope"></i>
                        <div>
                            <strong>Email</strong>
                            <a href="mailto:instructors@securelicence.com">instructors@securelicence.com</a>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-chat-text"></i>
                        <div>
                            <strong>Emergency SMS</strong>
                            <a href="sms:+61490418703">0490 418 703</a>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-clock"></i>
                        <div>
                            <strong>Support hours</strong>
                            <span class="text-muted small d-block">Mon–Fri · 9 AM – 5 PM AEST</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
.support-quick-links { list-style: none; padding: 0; margin: 0; }
.support-quick-links li { margin-bottom: 0.4rem; }
.support-quick-links a {
    display: flex; align-items: center; gap: 0.55rem;
    padding: 0.55rem 0.75rem; border-radius: 8px;
    color: var(--sl-gray-700, #374151);
    text-decoration: none; font-size: 0.92rem; font-weight: 600;
    transition: all 0.15s ease;
}
.support-quick-links a:hover { background: #fef3c7; color: var(--sl-accent-800, #92400e); }
.support-quick-links i { color: var(--sl-accent-600, #d97706); }

.support-contact-list { list-style: none; padding: 0; margin: 0; }
.support-contact-list li {
    display: flex; gap: 0.85rem; align-items: flex-start;
    padding: 0.7rem 0; border-bottom: 1px solid var(--sl-gray-100, #f3f4f6);
}
.support-contact-list li:last-child { border-bottom: 0; }
.support-contact-list i {
    width: 36px; height: 36px; border-radius: 10px;
    background: #fef3c7; color: var(--sl-accent-700, #b45309);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}
.support-contact-list strong { display: block; color: var(--sl-gray-900, #111); font-weight: 800; }
.support-contact-list a { color: var(--sl-accent-700, #b45309); text-decoration: none; font-weight: 600; font-size: 0.9rem; }
.support-contact-list a:hover { text-decoration: underline; }

.support-upload .form-control { padding: 0.65rem 0.85rem; }
</style>

<script>
(function () {
    // Sub-category options keyed by parent category
    var subOptions = {
        account: [
            ['login',      "I can't log in"],
            ['settings',   'Change my account settings'],
            ['close',      'Close my account'],
            ['security',   'Security or password concern'],
        ],
        payments: [
            ['payout',     'Missing or delayed payout'],
            ['invoice',    'Request an invoice / statement'],
            ['fees',       'Question about fees'],
            ['banking',    'Update bank details (via support)'],
            ['general',    'General payment query'],
        ],
        bookings: [
            ['reschedule', 'Reschedule a booking'],
            ['edit',       'Edit or change a booking'],
            ['cancel',     'Booking cancellation'],
            ['availability', 'Availability not showing correctly'],
            ['trouble',    'Trouble with a specific booking'],
            ['general',    'General booking query'],
        ],
        learners: [
            ['no_show',    'Learner no-show'],
            ['conduct',    'Concern about learner conduct'],
            ['invite',     'Help with inviting a learner'],
            ['transfer',   'Transfer learner to another instructor'],
        ],
        documents: [
            ['rejected',   'My document was rejected'],
            ['expiring',   "I need to update an expiring document"],
            ['upload',     'Trouble uploading a file'],
            ['wwcc',       'WWCC question'],
        ],
        dispute: [
            ['booking',    'Dispute about a booking'],
            ['payment',    'Dispute about a payment'],
            ['review',     'Dispute about a review'],
            ['other',      'Other dispute'],
        ],
        feedback: [
            ['review',     'Need help with a review'],
            ['complaint',  'I have a complaint'],
            ['suggestion', 'Feature suggestion'],
            ['praise',     'Praise for the team'],
        ],
        technical: [
            ['app',        'App crashes or freezes'],
            ['calendar',   'Calendar sync not working'],
            ['notification', 'Not receiving notifications'],
            ['other_bug',  'Other bug or glitch'],
        ],
        general: [
            ['pricing',    'Products, pricing &amp; packages'],
            ['third_party','I am a third-party provider'],
            ['general',    'General enquiry'],
        ],
    };

    var catEl = document.getElementById('support-category');
    var subWrap = document.getElementById('support-sub-wrap');
    var subEl = document.getElementById('support-sub');

    function renderSub() {
        var key = catEl.value;
        var opts = subOptions[key] || [];
        if (opts.length === 0) {
            subWrap.style.display = 'none';
            subEl.innerHTML = '<option value="">Choose…</option>';
            return;
        }
        subEl.innerHTML = '<option value="">Choose…</option>' +
            opts.map(function (o) { return '<option value="' + o[0] + '">' + o[1] + '</option>'; }).join('');
        subWrap.style.display = 'block';
    }

    catEl.addEventListener('change', renderSub);
    if (catEl.value) renderSub();
})();
</script>
@endpush
@endsection
