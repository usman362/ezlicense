@extends('layouts.admin')

@section('title', 'Reviews')
@section('heading', 'Reviews & Ratings')

@section('content')
{{-- KPI cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-2 col-sm-4 col-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body py-3">
            <h6 class="text-muted small mb-1">Total</h6>
            <p class="mb-0 fs-4 fw-bold">{{ number_format($stats['total']) }}</p>
        </div></div>
    </div>
    <div class="col-md-2 col-sm-4 col-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body py-3">
            <h6 class="text-muted small mb-1">Pending</h6>
            <p class="mb-0 fs-4 fw-bold text-warning">{{ number_format($stats['pending']) }}</p>
        </div></div>
    </div>
    <div class="col-md-2 col-sm-4 col-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body py-3">
            <h6 class="text-muted small mb-1">Approved</h6>
            <p class="mb-0 fs-4 fw-bold text-success">{{ number_format($stats['approved']) }}</p>
        </div></div>
    </div>
    <div class="col-md-2 col-sm-4 col-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body py-3">
            <h6 class="text-muted small mb-1">Rejected</h6>
            <p class="mb-0 fs-4 fw-bold text-danger">{{ number_format($stats['rejected']) }}</p>
        </div></div>
    </div>
    <div class="col-md-2 col-sm-4 col-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body py-3">
            <h6 class="text-muted small mb-1">5★ Approved</h6>
            <p class="mb-0 fs-4 fw-bold" style="color: #ffd500;">{{ number_format($stats['five_star']) }}</p>
        </div></div>
    </div>
    <div class="col-md-2 col-sm-4 col-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body py-3">
            <h6 class="text-muted small mb-1">Avg Rating</h6>
            <p class="mb-0 fs-4 fw-bold">{{ number_format($stats['avg_rating'], 2) }} <i class="bi bi-star-fill text-warning small"></i></p>
        </div></div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="alert mb-0 d-flex align-items-center gap-2 py-2" style="background: #e7f5ff; border: 1px solid #4dabf7; color: #1971c2;">
            <i class="bi bi-google fs-5"></i>
            <div>
                <strong>{{ number_format($stats['google_prompted']) }}</strong>
                <span class="small d-block">Learners prompted to Google</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="alert mb-0 d-flex align-items-center gap-2 py-2 alert-secondary">
            <i class="bi bi-eye-slash fs-5"></i>
            <div>
                <strong>{{ number_format($stats['hidden']) }}</strong>
                <span class="small d-block">Hidden from public</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="alert mb-0 d-flex align-items-center gap-2 py-2 alert-info">
            <i class="bi bi-clock-history fs-5"></i>
            <div>
                <strong>{{ number_format($stats['last_7_days']) }}</strong>
                <span class="small d-block">New reviews (7 days)</span>
            </div>
        </div>
    </div>
</div>

@if(session('message'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('message') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm">
    {{-- Filters --}}
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Comment, learner, instructor..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label small text-muted mb-1">Rating</label>
                <select name="rating" class="form-select form-select-sm">
                    <option value="">All</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ (int) request('rating') === $i ? 'selected' : '' }}>{{ $i }}★</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Visibility</label>
                <select name="visibility" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="visible" {{ request('visibility') === 'visible' ? 'selected' : '' }}>Visible</option>
                    <option value="hidden" {{ request('visibility') === 'hidden' ? 'selected' : '' }}>Hidden</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Google Prompt</label>
                <select name="google_prompt" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="yes" {{ request('google_prompt') === 'yes' ? 'selected' : '' }}>Prompted</option>
                    <option value="no" {{ request('google_prompt') === 'no' ? 'selected' : '' }}>Not prompted</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label small text-muted mb-1">Period</label>
                <select name="days" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="7" {{ request('days') === '7' ? 'selected' : '' }}>7d</option>
                    <option value="30" {{ request('days') === '30' ? 'selected' : '' }}>30d</option>
                    <option value="90" {{ request('days') === '90' ? 'selected' : '' }}>90d</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button class="btn btn-primary btn-sm flex-fill"><i class="bi bi-search"></i></button>
                @if(request()->hasAny(['search','status','rating','visibility','google_prompt','days']))
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bulk action bar (hidden until rows checked) --}}
    <form method="POST" action="{{ route('admin.reviews.bulk') }}" id="bulk-form">
        @csrf
        <div class="alert alert-warning rounded-0 mb-0 py-2 px-3 d-none" id="bulk-bar">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="small fw-semibold"><span id="bulk-count">0</span> selected</span>
                <select name="action" class="form-select form-select-sm" style="width:auto;" required>
                    <option value="">Bulk action...</option>
                    <option value="approve">Approve</option>
                    <option value="reject">Reject</option>
                    <option value="hide">Hide</option>
                    <option value="unhide">Unhide</option>
                    <option value="delete">Delete (permanent)</option>
                </select>
                <input type="text" name="rejection_reason" class="form-control form-control-sm" placeholder="Rejection reason (optional)" style="max-width: 300px;">
                <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Apply bulk action to selected reviews?')">Apply</button>
                <button type="button" class="btn btn-link btn-sm" id="bulk-clear">Clear</button>
            </div>
        </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th style="width:30px;"><input type="checkbox" id="bulk-select-all"></th>
                        <th>Date</th>
                        <th>Learner</th>
                        <th>Instructor</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Status</th>
                        <th>Flags</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $r)
                        @php
                            $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
                            $color = $statusColors[$r->status] ?? 'secondary';
                        @endphp
                        <tr>
                            <td><input type="checkbox" name="review_ids[]" value="{{ $r->id }}" form="bulk-form" class="row-check"></td>
                            <td class="small text-muted">
                                {{ $r->created_at->format('d M Y') }}
                                <div>{{ $r->created_at->format('H:i') }}</div>
                            </td>
                            <td class="small">
                                @if($r->learner)
                                    <div class="fw-semibold">{{ $r->learner->name }}</div>
                                    <div class="text-muted">{{ $r->learner->email }}</div>
                                @else
                                    <span class="text-muted">— deleted —</span>
                                @endif
                            </td>
                            <td class="small">
                                @if($r->instructor)
                                    @if($r->instructor->instructorProfile)
                                        <a href="{{ route('admin.instructors.show', $r->instructor->instructorProfile) }}" class="fw-semibold text-decoration-none">{{ $r->instructor->name }}</a>
                                    @else
                                        <span class="fw-semibold">{{ $r->instructor->name }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">— deleted —</span>
                                @endif
                            </td>
                            <td class="small">
                                <span class="text-warning">
                                    @for($s = 1; $s <= 5; $s++)
                                        <i class="bi bi-star{{ $s <= $r->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </span>
                                <div class="small fw-semibold">{{ $r->rating }}/5</div>
                            </td>
                            <td class="small" style="max-width: 280px;">
                                <div class="text-truncate" title="{{ $r->comment }}">
                                    {{ $r->comment ? \Illuminate\Support\Str::limit($r->comment, 80) : '—' }}
                                </div>
                                @if($r->rejection_reason)
                                    <div class="small text-danger" title="{{ $r->rejection_reason }}">
                                        <i class="bi bi-x-circle"></i> {{ \Illuminate\Support\Str::limit($r->rejection_reason, 60) }}
                                    </div>
                                @endif
                            </td>
                            <td class="small">
                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">{{ ucfirst($r->status) }}</span>
                                @if($r->moderator)
                                    <div class="text-muted" style="font-size:0.7rem;">by {{ $r->moderator->name }}</div>
                                @endif
                            </td>
                            <td class="small">
                                @if($r->is_hidden)
                                    <span class="badge bg-secondary-subtle text-secondary mb-1" title="Hidden from public"><i class="bi bi-eye-slash"></i></span>
                                @endif
                                @if($r->google_review_prompted)
                                    <span class="badge bg-info-subtle text-info mb-1" title="Learner prompted to leave Google review"><i class="bi bi-google"></i></span>
                                @endif
                                @if($r->rating === 5 && $r->status === 'approved')
                                    <span class="badge bg-warning-subtle text-warning mb-1" title="5-star approved review"><i class="bi bi-star-fill"></i></span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end small">
                                        @if($r->status !== 'approved')
                                            <li>
                                                <form method="POST" action="{{ route('admin.reviews.approve', $r) }}" class="d-inline">@csrf
                                                    <button class="dropdown-item text-success"><i class="bi bi-check-circle me-2"></i>Approve</button>
                                                </form>
                                            </li>
                                        @endif
                                        @if($r->status === 'approved')
                                            <li>
                                                <form method="POST" action="{{ route('admin.reviews.toggle-visibility', $r) }}" class="d-inline">@csrf
                                                    <button class="dropdown-item">
                                                        <i class="bi bi-{{ $r->is_hidden ? 'eye' : 'eye-slash' }} me-2"></i>
                                                        {{ $r->is_hidden ? 'Show publicly' : 'Hide from public' }}
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        @if($r->status !== 'rejected')
                                            <li>
                                                <button type="button" class="dropdown-item text-warning" data-bs-toggle="modal" data-bs-target="#reject-modal-{{ $r->id }}">
                                                    <i class="bi bi-x-circle me-2"></i>Reject
                                                </button>
                                            </li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#view-modal-{{ $r->id }}">
                                                <i class="bi bi-eye me-2"></i>View full
                                            </button>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" class="d-inline" onsubmit="return confirm('Permanently delete this review? This cannot be undone.')">@csrf @method('DELETE')
                                                <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete permanently</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        {{-- View modal --}}
                        <div class="modal fade" id="view-modal-{{ $r->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Review #{{ $r->id }}</h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-sm small">
                                            <tr><td class="text-muted" style="width:35%">Submitted</td><td>{{ $r->created_at->format('d M Y, H:i') }} ({{ $r->created_at->diffForHumans() }})</td></tr>
                                            <tr><td class="text-muted">Learner</td><td>{{ $r->learner?->name ?? '—' }} ({{ $r->learner?->email ?? '—' }})</td></tr>
                                            <tr><td class="text-muted">Instructor</td><td>{{ $r->instructor?->name ?? '—' }}</td></tr>
                                            <tr><td class="text-muted">Booking</td><td>#{{ $r->booking_id }} on {{ $r->booking?->scheduled_at?->format('d M Y, H:i') }}</td></tr>
                                            <tr><td class="text-muted">Rating</td><td>
                                                <span class="text-warning">@for($s=1;$s<=5;$s++)<i class="bi bi-star{{ $s <= $r->rating ? '-fill' : '' }}"></i>@endfor</span>
                                                <strong class="ms-2">{{ $r->rating }}/5</strong>
                                            </td></tr>
                                            <tr><td class="text-muted">Status</td><td><span class="badge bg-{{ $color }}-subtle text-{{ $color }}">{{ ucfirst($r->status) }}</span></td></tr>
                                            @if($r->moderator)
                                                <tr><td class="text-muted">Moderated by</td><td>{{ $r->moderator->name }} on {{ $r->moderated_at?->format('d M Y, H:i') }}</td></tr>
                                            @endif
                                            @if($r->google_review_prompted)
                                                <tr><td class="text-muted">Google review</td><td><span class="badge bg-info-subtle text-info"><i class="bi bi-google"></i> Learner prompted</span></td></tr>
                                            @endif
                                        </table>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Comment</label>
                                            <div class="p-3 bg-light rounded small" style="white-space: pre-wrap;">{{ $r->comment ?: '(no comment provided)' }}</div>
                                        </div>
                                        @if($r->rejection_reason)
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-danger">Rejection reason</label>
                                                <div class="p-3 rounded small" style="background: #fff5f5; border-left: 3px solid #dc3545;">{{ $r->rejection_reason }}</div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Reject modal --}}
                        @if($r->status !== 'rejected')
                            <div class="modal fade" id="reject-modal-{{ $r->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.reviews.reject', $r) }}">@csrf
                                            <div class="modal-header bg-danger-subtle">
                                                <h5 class="modal-title"><i class="bi bi-x-circle text-danger me-2"></i>Reject Review #{{ $r->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="small text-muted mb-3">This review will be marked as rejected and hidden from the public. You can optionally include a reason for the audit log.</p>
                                                <div class="p-2 mb-3 small bg-light rounded">
                                                    <strong>{{ $r->learner?->name }}</strong> rated {{ $r->rating }}/5: <em>"{{ \Illuminate\Support\Str::limit($r->comment ?: '(no comment)', 100) }}"</em>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small">Rejection reason (optional)</label>
                                                    <textarea name="rejection_reason" class="form-control form-control-sm" rows="2" maxlength="500" placeholder="e.g. Off-topic / Profanity / Not relevant to the lesson"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-x-circle me-1"></i>Reject</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr><td colspan="9" class="text-muted text-center py-4">
                            @if(request()->hasAny(['search','status','rating','visibility','google_prompt','days']))
                                No reviews match your filters.
                            @else
                                No reviews yet. They'll appear here as learners submit them.
                            @endif
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </form>

    @if($reviews->hasPages())
        <div class="card-footer bg-white">{{ $reviews->links() }}</div>
    @endif
</div>

<script>
(function() {
    var checks = document.querySelectorAll('.row-check');
    var selectAll = document.getElementById('bulk-select-all');
    var bar = document.getElementById('bulk-bar');
    var counter = document.getElementById('bulk-count');
    var clearBtn = document.getElementById('bulk-clear');

    function update() {
        var n = document.querySelectorAll('.row-check:checked').length;
        if (counter) counter.textContent = n;
        if (bar) bar.classList.toggle('d-none', n === 0);
    }

    checks.forEach(c => c.addEventListener('change', update));
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checks.forEach(c => { c.checked = selectAll.checked; });
            update();
        });
    }
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            checks.forEach(c => { c.checked = false; });
            if (selectAll) selectAll.checked = false;
            update();
        });
    }
})();
</script>
@endsection
