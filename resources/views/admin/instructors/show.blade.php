@extends('layouts.admin')

@section('title', ($instructor->user->name ?? 'Instructor') . ' — Profile')
@section('heading')
    <a href="{{ route('admin.instructors.index') }}" class="text-decoration-none text-muted me-2"><i class="bi bi-arrow-left"></i></a>
    {{ $instructor->user->name ?? 'Instructor' }} — Full Profile
@endsection

@section('content')
{{-- Block banner --}}
@if($stats['is_blocked'] && $stats['current_block'])
    @php $cb = $stats['current_block']; @endphp
    <div class="alert alert-danger d-flex align-items-center justify-content-between mb-3">
        <div>
            <i class="bi bi-slash-circle-fill me-2"></i>
            <strong>BLOCKED</strong>
            — {{ $cb->reason }}
            <span class="ms-2 small text-muted">
                @if($cb->expires_at)
                    until {{ $cb->expires_at->format('d M Y') }} ({{ $cb->expires_at->diffForHumans() }})
                @else
                    <strong>PERMANENT</strong>
                @endif
                · by {{ $cb->admin->name ?? '—' }} on {{ $cb->started_at->format('d M Y') }}
            </span>
        </div>
        <form method="POST" action="{{ route('admin.instructors.blocks.lift', $cb) }}"
              onsubmit="var r = prompt('Reason for lifting the block (optional):'); if(r !== null) { this.querySelector('[name=lifted_reason]').value = r; return true; } return false;">
            @csrf @method('PATCH')
            <input type="hidden" name="lifted_reason" value="">
            <button class="btn btn-sm btn-outline-light"><i class="bi bi-unlock me-1"></i>Lift Block</button>
        </form>
    </div>
@endif

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="kpi-card h-100">
            <div class="kpi-icon"><i class="bi bi-calendar3"></i></div>
            <div class="kpi-label">Total Bookings</div>
            <div class="kpi-value">{{ $stats['total_bookings'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="kpi-card kpi-success h-100">
            <div class="kpi-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="kpi-label">Completed</div>
            <div class="kpi-value">{{ $stats['completed_bookings'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="kpi-card kpi-accent h-100">
            <div class="kpi-icon"><i class="bi bi-star-fill"></i></div>
            <div class="kpi-label">Avg Rating</div>
            <div class="kpi-value">
                @if($stats['average_rating'] > 0)
                    {{ number_format($stats['average_rating'], 1) }}<span class="kpi-unit">/5</span>
                @else
                    —
                @endif
            </div>
            <div class="small text-muted mt-1">{{ $stats['reviews_count'] }} reviews</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="kpi-card {{ $stats['open_complaints_count'] > 0 ? 'kpi-danger' : '' }} h-100">
            <div class="kpi-icon"><i class="bi bi-exclamation-octagon-fill"></i></div>
            <div class="kpi-label">Open Complaints</div>
            <div class="kpi-value">{{ $stats['open_complaints_count'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="kpi-card kpi-teal h-100">
            <div class="kpi-icon"><i class="bi bi-flag-fill"></i></div>
            <div class="kpi-label">Warnings</div>
            <div class="kpi-value">{{ $stats['total_warnings_count'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        @php
            $verKpi = match($instructor->verification_status ?? 'pending') {
                'verified' => 'kpi-success',
                'rejected' => 'kpi-danger',
                default    => 'kpi-accent',
            };
        @endphp
        <div class="kpi-card {{ $verKpi }} h-100">
            <div class="kpi-icon"><i class="bi bi-patch-check-fill"></i></div>
            <div class="kpi-label">Verification</div>
            <div class="kpi-value" style="font-size: var(--sl-text-lg); line-height: 1.3;">{{ ucfirst(str_replace('_', ' ', $instructor->verification_status ?? 'pending')) }}</div>
        </div>
    </div>
</div>

{{-- Tab navigation --}}
<ul class="nav nav-tabs mb-4" id="instructorTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">
            <i class="bi bi-person-badge me-1"></i>Overview
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="complaints-tab" data-bs-toggle="tab" data-bs-target="#complaints" type="button">
            <i class="bi bi-exclamation-octagon me-1"></i>Complaints
            @if($instructor->complaints->count() > 0)
                <span class="badge bg-danger ms-1">{{ $instructor->complaints->count() }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="warnings-tab" data-bs-toggle="tab" data-bs-target="#warnings" type="button">
            <i class="bi bi-exclamation-triangle me-1"></i>Warnings
            @if($instructor->warnings->count() > 0)
                <span class="badge bg-warning text-dark ms-1">{{ $instructor->warnings->count() }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="blocks-tab" data-bs-toggle="tab" data-bs-target="#blocks" type="button">
            <i class="bi bi-slash-circle me-1"></i>Blocks
            @if($instructor->blocks->count() > 0)
                <span class="badge bg-secondary ms-1">{{ $instructor->blocks->count() }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
            <i class="bi bi-star me-1"></i>Reviews
            @if($stats['pending_reviews_count'] > 0)
                <span class="badge bg-warning text-dark ms-1">{{ $stats['pending_reviews_count'] }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="correspondence-tab" data-bs-toggle="tab" data-bs-target="#correspondence" type="button">
            <i class="bi bi-envelope me-1"></i>Correspondence
            @if($instructor->correspondences->count() > 0)
                <span class="badge bg-info ms-1">{{ $instructor->correspondences->count() }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button">
            <i class="bi bi-sticky me-1"></i>Notes
            @if($instructor->adminNotes->count() > 0)
                <span class="badge bg-primary ms-1">{{ $instructor->adminNotes->count() }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="audit-tab" data-bs-toggle="tab" data-bs-target="#audit" type="button">
            <i class="bi bi-clock-history me-1"></i>Audit Trail
        </button>
    </li>
</ul>

<div class="tab-content" id="instructorTabContent">
    {{-- OVERVIEW TAB --}}
    <div class="tab-pane fade show active" id="overview" role="tabpanel">
        @include('admin.instructors.partials.overview', ['instructor' => $instructor, 'stats' => $stats])
    </div>

    {{-- COMPLAINTS TAB --}}
    <div class="tab-pane fade" id="complaints" role="tabpanel">
        @include('admin.instructors.partials.complaints', ['instructor' => $instructor])
    </div>

    {{-- WARNINGS TAB --}}
    <div class="tab-pane fade" id="warnings" role="tabpanel">
        @include('admin.instructors.partials.warnings', ['instructor' => $instructor])
    </div>

    {{-- BLOCKS TAB --}}
    <div class="tab-pane fade" id="blocks" role="tabpanel">
        @include('admin.instructors.partials.blocks', ['instructor' => $instructor, 'stats' => $stats])
    </div>

    {{-- REVIEWS TAB --}}
    <div class="tab-pane fade" id="reviews" role="tabpanel">
        @include('admin.instructors.partials.reviews', ['instructor' => $instructor, 'stats' => $stats])
    </div>

    {{-- CORRESPONDENCE TAB --}}
    <div class="tab-pane fade" id="correspondence" role="tabpanel">
        @include('admin.instructors.partials.correspondence', ['instructor' => $instructor])
    </div>

    {{-- NOTES TAB --}}
    <div class="tab-pane fade" id="notes" role="tabpanel">
        @include('admin.instructors.partials.notes', ['instructor' => $instructor])
    </div>

    {{-- AUDIT TRAIL TAB --}}
    <div class="tab-pane fade" id="audit" role="tabpanel">
        @include('admin.instructors.partials.audit', ['auditLogs' => $auditLogs])
    </div>
</div>

<script>
// Persist active tab across reloads using URL hash
(function() {
    var hash = window.location.hash;
    if (hash) {
        var tab = document.querySelector('[data-bs-target="' + hash + '"]');
        if (tab) { new bootstrap.Tab(tab).show(); }
    }
    document.querySelectorAll('#instructorTabs button[data-bs-toggle="tab"]').forEach(function(btn) {
        btn.addEventListener('shown.bs.tab', function(e) {
            history.replaceState(null, null, e.target.getAttribute('data-bs-target'));
        });
    });
})();
</script>
@endsection
