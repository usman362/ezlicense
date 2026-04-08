@extends('layouts.admin')

@section('title', ($user->name ?? 'User') . ' — Profile')
@section('heading')
    <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted me-2"><i class="bi bi-arrow-left"></i></a>
    {{ $user->name ?? 'User' }} — Learner Profile
@endsection

@section('content')
{{-- Header / Biodata Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex align-items-start gap-4 flex-wrap">
            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:96px;height:96px;">
                <span class="fw-bold text-primary fs-2">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap">
                    <div>
                        <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                        <div class="text-muted mb-2">
                            <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                            @if($user->phone) <span class="ms-3"><i class="bi bi-telephone me-1"></i>{{ $user->phone }}</span> @endif
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-{{ $user->role === 'learner' ? 'info' : ($user->role === 'admin' ? 'dark' : 'primary') }}">{{ ucfirst($user->role) }}</span>
                            @if($user->is_active)
                                <span class="badge bg-success-subtle text-success">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                @if($user->deactivation_reason)
                                    <span class="badge bg-light text-dark" title="{{ $user->deactivation_reason }}">{{ Str::limit($user->deactivation_reason, 40) }}</span>
                                @endif
                            @endif
                            @if($stats['complaints_filed'] >= 3)
                                <span class="badge bg-danger" title="This user has filed {{ $stats['complaints_filed'] }} complaints">
                                    <i class="bi bi-flag-fill me-1"></i>{{ $stats['complaints_filed'] }} complaints filed
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-end small text-muted">
                        <div>Member since</div>
                        <div class="fw-bold text-body">{{ $user->created_at->format('d M Y') }}</div>
                        @if($user->last_login_at)
                            <div class="mt-1">Last login: {{ $user->last_login_at->diffForHumans() }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h3 fw-bold text-primary mb-0">{{ $stats['total_bookings'] }}</div>
            <div class="small text-muted">Total Bookings</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h3 fw-bold text-success mb-0">{{ $stats['completed_bookings'] }}</div>
            <div class="small text-muted">Completed</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h3 fw-bold text-danger mb-0">{{ $stats['cancelled_bookings'] }}</div>
            <div class="small text-muted">Cancelled</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h3 fw-bold text-info mb-0">${{ number_format($stats['total_spent'], 0) }}</div>
            <div class="small text-muted">Total Spent</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h3 fw-bold text-warning mb-0">{{ $stats['reviews_given'] }}</div>
            <div class="small text-muted">Reviews Given</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h3 fw-bold {{ $stats['complaints_filed'] >= 3 ? 'text-danger' : 'text-secondary' }} mb-0">{{ $stats['complaints_filed'] }}</div>
            <div class="small text-muted">Complaints Filed</div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" id="userTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="u-overview-tab" data-bs-toggle="tab" data-bs-target="#u-overview" type="button">
            <i class="bi bi-person me-1"></i>Overview
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="u-bookings-tab" data-bs-toggle="tab" data-bs-target="#u-bookings" type="button">
            <i class="bi bi-calendar-check me-1"></i>Bookings
            @if($bookings->count() > 0)<span class="badge bg-secondary ms-1">{{ $bookings->count() }}</span>@endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="u-reviews-tab" data-bs-toggle="tab" data-bs-target="#u-reviews" type="button">
            <i class="bi bi-star me-1"></i>Reviews Given
            @if($reviewsGiven->count() > 0)<span class="badge bg-secondary ms-1">{{ $reviewsGiven->count() }}</span>@endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $complaintsFiled->count() >= 3 ? 'text-danger' : '' }}" id="u-complaints-tab" data-bs-toggle="tab" data-bs-target="#u-complaints" type="button">
            <i class="bi bi-flag me-1"></i>Complaints Filed
            @if($complaintsFiled->count() > 0)
                <span class="badge bg-{{ $complaintsFiled->count() >= 3 ? 'danger' : 'warning text-dark' }} ms-1">{{ $complaintsFiled->count() }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="u-notes-tab" data-bs-toggle="tab" data-bs-target="#u-notes" type="button">
            <i class="bi bi-sticky me-1"></i>Admin Notes
            @if($user->adminNotes->count() > 0)<span class="badge bg-primary ms-1">{{ $user->adminNotes->count() }}</span>@endif
        </button>
    </li>
</ul>

<div class="tab-content" id="userTabContent">
    <div class="tab-pane fade show active" id="u-overview" role="tabpanel">
        @include('admin.users.partials.overview', compact('user', 'stats'))
    </div>
    <div class="tab-pane fade" id="u-bookings" role="tabpanel">
        @include('admin.users.partials.bookings', compact('bookings'))
    </div>
    <div class="tab-pane fade" id="u-reviews" role="tabpanel">
        @include('admin.users.partials.reviews-given', compact('reviewsGiven'))
    </div>
    <div class="tab-pane fade" id="u-complaints" role="tabpanel">
        @include('admin.users.partials.complaints-filed', compact('complaintsFiled'))
    </div>
    <div class="tab-pane fade" id="u-notes" role="tabpanel">
        @include('admin.users.partials.notes', compact('user'))
    </div>
</div>

<script>
(function() {
    var hash = window.location.hash;
    if (hash) {
        var tab = document.querySelector('[data-bs-target="' + hash + '"]');
        if (tab) { new bootstrap.Tab(tab).show(); }
    }
    document.querySelectorAll('#userTabs button[data-bs-toggle="tab"]').forEach(function(btn) {
        btn.addEventListener('shown.bs.tab', function(e) {
            history.replaceState(null, null, e.target.getAttribute('data-bs-target'));
        });
    });
})();
</script>
@endsection
