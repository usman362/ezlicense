@extends('layouts.instructor')

@section('title', 'Awaiting Verification')
@section('heading', 'Awaiting Verification')

@section('content')
@php
    $profile = Auth::user()?->instructorProfile;
    $docs = $profile ? $profile->documents()->latest()->get() : collect();
    $submittedDate = $docs->max('created_at');
@endphp

<div class="onb-wrap">
    <div class="onb-card">
        <div class="onb-hero">
            <div class="onb-hero-icon">
                <i class="bi bi-shield-fill-check"></i>
            </div>
            <h1 class="onb-hero-title">Documents under review</h1>
            <p class="onb-hero-sub">
                Thanks for submitting your verification documents! Our team is reviewing them now &mdash; you'll be able to access the full instructor portal as soon as we approve your account.
            </p>
            <span class="onb-status-pill">
                <span class="onb-pulse"></span>
                Pending admin approval
            </span>
        </div>

        {{-- Progress tracker --}}
        <div class="onb-steps">
            <div class="onb-step onb-step-done">
                <div class="onb-step-dot"><i class="bi bi-check-lg"></i></div>
                <div class="onb-step-text">
                    <div class="onb-step-title">Account created</div>
                    <div class="onb-step-meta">Welcome aboard, {{ Auth::user()->first_name ?? Auth::user()->name }}</div>
                </div>
            </div>
            <div class="onb-step onb-step-done">
                <div class="onb-step-dot"><i class="bi bi-check-lg"></i></div>
                <div class="onb-step-text">
                    <div class="onb-step-title">Documents uploaded</div>
                    <div class="onb-step-meta">
                        {{ $docs->count() }} {{ Str::plural('document', $docs->count()) }} submitted
                        @if($submittedDate) &middot; {{ $submittedDate->diffForHumans() }} @endif
                    </div>
                </div>
            </div>
            <div class="onb-step onb-step-active">
                <div class="onb-step-dot"><span class="onb-step-spinner"></span></div>
                <div class="onb-step-text">
                    <div class="onb-step-title">Admin verification</div>
                    <div class="onb-step-meta">Usually takes 24-48 hours during business days</div>
                </div>
            </div>
            <div class="onb-step">
                <div class="onb-step-dot"><i class="bi bi-3-circle"></i></div>
                <div class="onb-step-text">
                    <div class="onb-step-title">Set service area &amp; pricing</div>
                    <div class="onb-step-meta">Unlocked after approval</div>
                </div>
            </div>
            <div class="onb-step">
                <div class="onb-step-dot"><i class="bi bi-4-circle"></i></div>
                <div class="onb-step-text">
                    <div class="onb-step-title">Start accepting bookings</div>
                    <div class="onb-step-meta">Learners will be able to book once you're live</div>
                </div>
            </div>
        </div>

        @if($docs->isNotEmpty())
            <div class="onb-docs">
                <h3 class="onb-docs-title">Documents submitted</h3>
                <ul class="onb-doc-list">
                    @foreach($docs as $d)
                        @php
                            $label = match ($d->type) {
                                'drivers_licence' => "Driver's Licence",
                                'instructor_licence' => "Instructor's Licence",
                                'wwcc' => 'Working with Children Check',
                                default => ucfirst(str_replace('_', ' ', $d->type)),
                            };
                            $statusClass = match ($d->status) {
                                'verified' => 'onb-doc-verified',
                                'rejected' => 'onb-doc-rejected',
                                default => 'onb-doc-pending',
                            };
                            $statusLabel = match ($d->status) {
                                'verified' => 'Verified',
                                'rejected' => 'Needs reupload',
                                default => 'Pending review',
                            };
                            $statusIcon = match ($d->status) {
                                'verified' => 'bi-check-circle-fill',
                                'rejected' => 'bi-x-circle-fill',
                                default => 'bi-hourglass-split',
                            };
                        @endphp
                        <li class="onb-doc-item">
                            <i class="bi bi-file-earmark-text-fill onb-doc-icon"></i>
                            <div class="onb-doc-info">
                                <div class="onb-doc-name">{{ $label }}@if($d->side) <span class="text-muted">({{ ucfirst($d->side) }})</span>@endif</div>
                                <div class="onb-doc-date">Uploaded {{ $d->created_at->diffForHumans() }}</div>
                            </div>
                            <span class="onb-doc-status {{ $statusClass }}">
                                <i class="bi {{ $statusIcon }}"></i>{{ $statusLabel }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="onb-actions">
            <a href="{{ route('instructor.settings.documents') }}" class="btn btn-outline-secondary">
                <i class="bi bi-eye me-1"></i>View / update documents
            </a>
            <a href="{{ route('instructor.support') }}" class="btn btn-warning fw-bold">
                <i class="bi bi-headset me-1"></i>Need help? Contact support
            </a>
        </div>

        <div class="onb-helpline">
            <i class="bi bi-info-circle-fill"></i>
            Once approved you'll receive an email and the full instructor portal will unlock automatically. No need to refresh — just check back later or use the link in the approval email.
        </div>
    </div>
</div>

@push('scripts')
<style>
.onb-wrap {
    display: flex; justify-content: center; padding: 1rem 0 3rem;
}
.onb-card {
    width: 100%; max-width: 760px;
    background: #fff; border-radius: 20px;
    border: 1px solid var(--sl-gray-100, #f3f4f6);
    box-shadow: 0 12px 36px rgba(17, 24, 39, 0.06);
    overflow: hidden;
}
.onb-hero {
    text-align: center; padding: 2.5rem 1.5rem 1.75rem;
    background: linear-gradient(180deg, #fffbeb 0%, #ffffff 100%);
}
.onb-hero-icon {
    width: 72px; height: 72px; border-radius: 50%;
    background: linear-gradient(135deg, #fbbf24, #d97706);
    color: #fff; display: inline-flex; align-items: center; justify-content: center;
    font-size: 2.25rem; box-shadow: 0 10px 26px rgba(217, 119, 6, 0.28);
    margin-bottom: 1rem;
}
.onb-hero-title {
    font-size: 1.75rem; font-weight: 900; letter-spacing: -0.02em;
    color: var(--sl-gray-900, #111); margin: 0 0 0.5rem;
}
.onb-hero-sub {
    color: var(--sl-gray-600, #4b5563); max-width: 520px; margin: 0 auto 1.25rem;
    line-height: 1.55;
}
.onb-status-pill {
    display: inline-flex; align-items: center; gap: 0.45rem;
    padding: 0.45rem 1rem; border-radius: 999px;
    background: #fef3c7; color: var(--sl-accent-800, #92400e);
    font-size: 0.85rem; font-weight: 700;
}
.onb-pulse {
    width: 10px; height: 10px; border-radius: 50%;
    background: var(--sl-accent-600, #d97706);
    box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.6);
    animation: onbPulse 1.6s infinite;
}
@keyframes onbPulse {
    0%   { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.55); }
    70%  { box-shadow: 0 0 0 10px rgba(217, 119, 6, 0); }
    100% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0); }
}

.onb-steps {
    border-top: 1px solid var(--sl-gray-100, #f3f4f6);
    padding: 1.5rem 1.75rem;
    display: flex; flex-direction: column; gap: 0.85rem;
}
.onb-step {
    display: flex; gap: 0.85rem; align-items: center;
    padding: 0.65rem 0.85rem; border-radius: 12px;
    background: var(--sl-gray-50, #f9fafb);
    opacity: 0.55;
}
.onb-step-done {
    opacity: 1;
    background: #f0fdf4; border: 1px solid #bbf7d0;
}
.onb-step-active {
    opacity: 1;
    background: #fef3c7; border: 1px solid #fcd34d;
}
.onb-step-dot {
    width: 36px; height: 36px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 1rem; font-weight: 800;
    background: #e5e7eb; color: var(--sl-gray-500, #6b7280);
}
.onb-step-done .onb-step-dot {
    background: #10b981; color: #fff;
}
.onb-step-active .onb-step-dot {
    background: #fbbf24; color: var(--sl-gray-900, #111);
}
.onb-step-spinner {
    width: 14px; height: 14px; border-radius: 50%;
    border: 2px solid rgba(17, 24, 39, 0.18);
    border-top-color: var(--sl-gray-900, #111);
    animation: onbSpin 0.9s linear infinite;
}
@keyframes onbSpin { to { transform: rotate(360deg); } }
.onb-step-text { flex: 1; min-width: 0; }
.onb-step-title { font-weight: 700; color: var(--sl-gray-900, #111); font-size: 0.95rem; }
.onb-step-meta { font-size: 0.8rem; color: var(--sl-gray-600, #4b5563); }

.onb-docs {
    padding: 0 1.75rem 1.25rem;
}
.onb-docs-title {
    font-size: 0.78rem; font-weight: 800; letter-spacing: 0.08em;
    text-transform: uppercase; color: var(--sl-gray-500, #6b7280);
    margin-bottom: 0.75rem;
}
.onb-doc-list { list-style: none; padding: 0; margin: 0; }
.onb-doc-item {
    display: flex; align-items: center; gap: 0.85rem;
    padding: 0.75rem 1rem; border-radius: 10px;
    background: var(--sl-gray-50, #f9fafb);
    margin-bottom: 0.4rem;
}
.onb-doc-icon { font-size: 1.4rem; color: var(--sl-accent-600, #d97706); }
.onb-doc-info { flex: 1; min-width: 0; }
.onb-doc-name { font-weight: 700; color: var(--sl-gray-900, #111); }
.onb-doc-date { font-size: 0.78rem; color: var(--sl-gray-500, #6b7280); }
.onb-doc-status {
    display: inline-flex; align-items: center; gap: 0.35rem;
    font-size: 0.75rem; font-weight: 800;
    padding: 0.25rem 0.6rem; border-radius: 999px;
    white-space: nowrap;
}
.onb-doc-pending  { background: #fef3c7; color: var(--sl-accent-700, #b45309); }
.onb-doc-verified { background: #d1fae5; color: #065f46; }
.onb-doc-rejected { background: #fee2e2; color: #991b1b; }

.onb-actions {
    display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: center;
    padding: 0.5rem 1.75rem 1.5rem;
}

.onb-helpline {
    display: flex; gap: 0.75rem; align-items: flex-start;
    padding: 1rem 1.75rem;
    background: var(--sl-gray-50, #f9fafb);
    border-top: 1px solid var(--sl-gray-100, #f3f4f6);
    color: var(--sl-gray-700, #374151);
    font-size: 0.88rem;
    line-height: 1.55;
}
.onb-helpline i { color: var(--sl-accent-600, #d97706); font-size: 1.1rem; flex-shrink: 0; }

@media (max-width: 575.98px) {
    .onb-hero { padding: 1.75rem 1rem 1.25rem; }
    .onb-hero-title { font-size: 1.4rem; }
    .onb-steps { padding: 1rem; }
    .onb-actions .btn { width: 100%; }
}
</style>
@endpush
@endsection
