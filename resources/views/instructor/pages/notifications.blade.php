@extends('layouts.instructor')

@section('title', 'Notifications')
@section('heading', 'Notifications')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item active">Notifications</li>
    </ol>
</nav>

@if(session('success'))
    <div class="alert alert-success border-0 d-flex gap-2 align-items-center" role="alert">
        <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
    </div>
@endif

@php
    $unreadCount = Auth::user()?->unreadNotifications()->count() ?? 0;
    $proposalsCount = \App\Models\Booking::where('instructor_id', Auth::id())
        ->where('status', \App\Models\Booking::STATUS_PROPOSED)->count();
@endphp

{{-- Tabs --}}
<div class="bk-tabs-wrap mb-3">
    <ul class="nav bk-pill-tabs" role="tablist">
        <li class="nav-item">
            <a href="{{ route('instructor.notifications') }}" class="nav-link {{ $tab === 'notifications' ? 'active' : '' }}">
                <i class="bi bi-bell-fill me-1"></i>Booking Notifications
                @if($unreadCount > 0)
                    <span class="bk-tab-count bk-tab-count-amber">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('instructor.notifications', ['tab' => 'proposals']) }}" class="nav-link {{ $tab === 'proposals' ? 'active' : '' }}">
                <i class="bi bi-hourglass-split me-1"></i>Booking Proposals
                @if($proposalsCount > 0)
                    <span class="bk-tab-count">{{ $proposalsCount }}</span>
                @endif
            </a>
        </li>
    </ul>
</div>

@if($tab === 'notifications')

    {{-- ─── BOOKING NOTIFICATIONS TAB ─── --}}
    @if($notifications->total() === 0)
        <div class="bk-empty">
            <i class="bi bi-bell-slash bk-empty-icon"></i>
            <h5>You're all caught up</h5>
            <p>No notifications yet. Booking requests, learner messages, payouts and system alerts will show up here.</p>
            <a href="{{ route('instructor.dashboard') }}" class="btn btn-warning fw-bold btn-sm">
                <i class="bi bi-house me-1"></i>Back to dashboard
            </a>
        </div>
    @else
        <div class="sett-card">
            {{-- Bulk action bar --}}
            <div class="notif-toolbar">
                <label class="d-flex align-items-center gap-2 mb-0 small fw-semibold">
                    <input type="checkbox" class="form-check-input m-0" id="notif-select-all">
                    Select all on this page
                </label>
                <div class="d-flex align-items-center gap-2">
                    <form action="{{ route('instructor.notifications.mark-selected-read') }}" method="POST" id="notif-bulk-form" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary" id="notif-mark-selected" disabled>
                            <i class="bi bi-check2 me-1"></i>Mark selected as read
                        </button>
                    </form>
                    @if($unreadCount > 0)
                        <form action="{{ route('instructor.notifications.mark-all-read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-link text-warning-emphasis fw-bold p-0">
                                Mark all as read
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card-body p-0">
                <ul class="list-unstyled mb-0 notif-list">
                    @foreach($notifications as $n)
                        @php
                            $data = $n->data ?? [];
                            $type = $n->type ?? '';

                            // Pick a friendly badge + colour based on notification class name
                            if (str_contains($type, 'Cancel') || str_contains($type, 'NoShow')) {
                                $badge = 'Booking Cancelled';
                                $badgeClass = 'notif-badge-red';
                                $icon = 'bi-x-circle-fill';
                                $iconClass = 'notif-icon-red';
                            } elseif (str_contains($type, 'Proposed')) {
                                $badge = 'Booking Proposal';
                                $badgeClass = 'notif-badge-purple';
                                $icon = 'bi-hourglass-split';
                                $iconClass = 'notif-icon-purple';
                            } elseif (str_contains($type, 'Reschedul') || str_contains($type, 'Updated')) {
                                $badge = 'Booking Updated';
                                $badgeClass = 'notif-badge-blue';
                                $icon = 'bi-pencil-square';
                                $iconClass = 'notif-icon-blue';
                            } elseif (str_contains($type, 'NewBooking') || str_contains($type, 'InstructorNewBooking')) {
                                $badge = 'New Booking';
                                $badgeClass = 'notif-badge-amber';
                                $icon = 'bi-star-fill';
                                $iconClass = 'notif-icon-amber';
                            } elseif (str_contains($type, 'Payout')) {
                                $badge = 'Payout Update';
                                $badgeClass = 'notif-badge-green';
                                $icon = 'bi-cash-stack';
                                $iconClass = 'notif-icon-green';
                            } elseif (str_contains($type, 'Review')) {
                                $badge = 'New Review';
                                $badgeClass = 'notif-badge-amber';
                                $icon = 'bi-star-fill';
                                $iconClass = 'notif-icon-amber';
                            } elseif (str_contains($type, 'Document') || str_contains($type, 'Verification')) {
                                $badge = 'Document Update';
                                $badgeClass = 'notif-badge-blue';
                                $icon = 'bi-file-earmark-text-fill';
                                $iconClass = 'notif-icon-blue';
                            } elseif (str_contains($type, 'Welcome')) {
                                $badge = 'Welcome';
                                $badgeClass = 'notif-badge-green';
                                $icon = 'bi-emoji-smile-fill';
                                $iconClass = 'notif-icon-green';
                            } else {
                                $badge = $data['title'] ?? class_basename($type);
                                $badgeClass = 'notif-badge-gray';
                                $icon = 'bi-info-circle-fill';
                                $iconClass = '';
                            }

                            $message = $data['message'] ?? ($data['title'] ?? class_basename($type));
                            $url = $data['url'] ?? null;
                            $isUnread = ! $n->read_at;
                        @endphp
                        <li class="notif-item {{ $isUnread ? 'notif-unread' : 'notif-read' }}">
                            <input type="checkbox" class="form-check-input notif-checkbox" form="notif-bulk-form" name="ids[]" value="{{ $n->id }}">
                            <div class="notif-body">
                                <div class="notif-head">
                                    <span class="notif-badge {{ $badgeClass }}">
                                        @if($isUnread)<i class="bi bi-star-fill"></i>@else<i class="bi bi-{{ str_contains($badge, 'Cancel') ? 'x-circle' : 'circle' }}"></i>@endif
                                        {{ $badge }}
                                    </span>
                                </div>
                                <div class="notif-title">{{ $message }}</div>
                                <div class="notif-meta">
                                    <span class="notif-avatar"><i class="bi {{ $icon }}"></i></span>
                                    Notification received {{ $n->created_at->diffForHumans() }}
                                    @if($n->read_at)<span class="ms-2 text-muted small"><i class="bi bi-check2-all me-1"></i>Read</span>@endif
                                </div>
                            </div>
                            <a href="{{ $url ?: '#' }}" class="notif-arrow" aria-label="View"><i class="bi bi-chevron-right"></i></a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="notif-footer">
                <span class="text-muted small">
                    Displaying notification <strong>{{ $notifications->firstItem() }}</strong>–<strong>{{ $notifications->lastItem() }}</strong> of <strong>{{ $notifications->total() }}</strong> in total
                </span>
                <div>{{ $notifications->onEachSide(1)->links() }}</div>
            </div>
        </div>
    @endif

@else

    {{-- ─── BOOKING PROPOSALS TAB ─── --}}
    <div class="sett-card">
        <div class="sett-card-body">
            <form action="{{ route('instructor.notifications') }}" method="GET" class="row g-3 align-items-end mb-2">
                <input type="hidden" name="tab" value="proposals">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Learner</label>
                    <input type="text" name="learner_q" class="form-control" placeholder="Search by name, email or phone" value="{{ request('learner_q') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Proposal status</label>
                    <select name="proposal_status" class="form-select">
                        <option value="">Any status</option>
                        <option value="fresh"    {{ request('proposal_status') === 'fresh'    ? 'selected' : '' }}>Fresh (24h+ left)</option>
                        <option value="expiring" {{ request('proposal_status') === 'expiring' ? 'selected' : '' }}>Expiring soon (&lt;24h)</option>
                        <option value="expired"  {{ request('proposal_status') === 'expired'  ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sort by</label>
                    <select name="sort" class="form-select">
                        <option value="recent" {{ request('sort', 'recent') === 'recent' ? 'selected' : '' }}>Recently sent</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest first</option>
                        <option value="expiry" {{ request('sort') === 'expiry' ? 'selected' : '' }}>Expiry soonest</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-warning fw-bold flex-grow-1"><i class="bi bi-search me-1"></i>Search</button>
                    @if(request()->hasAny(['learner_q', 'proposal_status', 'sort']))
                        <a href="{{ route('instructor.notifications', ['tab' => 'proposals']) }}" class="btn btn-link text-secondary p-0 small text-nowrap">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($proposals->total() === 0)
        <div class="bk-empty">
            <i class="bi bi-hourglass bk-empty-icon"></i>
            <h5>No proposals found</h5>
            <p>Booking proposals you've sent to learners (still awaiting their response) will show here. Try clearing your filters if you expected results.</p>
            <a href="{{ route('instructor.learners') }}?open=propose" class="btn btn-warning fw-bold btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Create a Proposal
            </a>
        </div>
    @else
        <div class="d-flex flex-column gap-2">
            @foreach($proposals as $b)
                @php
                    $learner = $b->learner;
                    $loc = $b->suburb?->name ?? '—';
                    $time = $b->scheduled_at;
                    $expiresAt = $b->proposal_expires_at;
                    $hoursLeft = $expiresAt ? max(0, round((strtotime($expiresAt) - time()) / 3600)) : null;
                @endphp
                <div class="bk-card bk-card-pending" data-booking-id="{{ $b->id }}">
                    <div class="bk-date-block">
                        <div class="bk-date-month">{{ strtoupper($time?->format('M') ?? '—') }}</div>
                        <div class="bk-date-day">{{ $time?->format('j') ?? '—' }}</div>
                        <div class="bk-date-wd">{{ strtoupper($time?->format('D') ?? '') }}</div>
                    </div>
                    <div class="bk-card-body">
                        <div class="bk-card-head">
                            <div class="bk-card-time">
                                <i class="bi bi-clock me-1"></i>{{ $time?->format('H:i') ?? '' }}
                                @if($b->duration_minutes)
                                    – {{ $time?->copy()->addMinutes($b->duration_minutes)->format('H:i') }}
                                @endif
                            </div>
                            <span class="bk-status bk-status-pending"><i class="bi bi-hourglass-split"></i>Awaiting learner</span>
                        </div>
                        <div class="bk-card-learner">
                            <span class="bk-avatar">{{ strtoupper(substr($learner->name ?? '?', 0, 1)) }}</span>
                            <div class="bk-learner-info">
                                <div class="bk-learner-name">{{ $learner->name ?? '—' }}</div>
                                <div class="bk-card-meta">
                                    <span><i class="bi bi-geo-alt-fill"></i>{{ $loc }}</span>
                                    @if($learner?->phone)
                                        <a href="tel:{{ $learner->phone }}" class="bk-card-meta-link"><i class="bi bi-telephone-fill"></i>{{ $learner->phone }}</a>
                                    @endif
                                </div>
                                @if($hoursLeft !== null)
                                    <div class="mt-1">
                                        <span class="bk-pending-expiry"><i class="bi bi-stopwatch me-1"></i>{{ $hoursLeft > 0 ? "Expires in {$hoursLeft}h" : 'Expired' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="bk-card-actions">
                            <a href="{{ route('instructor.learners') }}" class="btn btn-sm btn-warning fw-bold">
                                <i class="bi bi-eye me-1"></i>View Proposal
                            </a>
                            <span class="bk-card-id">#{{ $b->id }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="notif-footer mt-3">
            <span class="text-muted small">
                Showing <strong>{{ $proposals->firstItem() }}</strong>–<strong>{{ $proposals->lastItem() }}</strong> of <strong>{{ $proposals->total() }}</strong>
            </span>
            <div>{{ $proposals->onEachSide(1)->links() }}</div>
        </div>
    @endif

@endif

@push('scripts')
<style>
/* Notification list */
.notif-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    padding: 0.85rem 1.25rem;
    border-bottom: 1px solid var(--sl-gray-100, #f3f4f6);
    background: var(--sl-gray-50, #f9fafb);
    border-radius: 16px 16px 0 0;
}
.notif-list { display: flex; flex-direction: column; }
.notif-item {
    display: flex;
    align-items: flex-start;
    gap: 0.85rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--sl-gray-100, #f3f4f6);
    transition: background 0.15s ease;
}
.notif-list .notif-item:last-child { border-bottom: 0; }
.notif-item:hover { background: var(--sl-gray-50, #f9fafb); }
.notif-unread .notif-title { color: var(--sl-gray-900, #111); font-weight: 700; }
.notif-unread .notif-badge { opacity: 1; }
.notif-read .notif-title { color: var(--sl-gray-500, #6b7280); font-weight: 500; }
.notif-read .notif-badge { opacity: 0.55; }
.notif-read .notif-meta { color: var(--sl-gray-400, #9ca3af); }

.notif-checkbox {
    margin-top: 0.3rem;
    cursor: pointer;
    flex-shrink: 0;
}
.notif-body { flex: 1; min-width: 0; }
.notif-head { margin-bottom: 0.3rem; }
.notif-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.22rem 0.65rem;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.04em;
}
.notif-badge i { font-size: 0.75rem; }
.notif-badge-amber  { background: #fef3c7; color: var(--sl-accent-700, #b45309); }
.notif-badge-blue   { background: #dbeafe; color: #1e40af; }
.notif-badge-green  { background: #d1fae5; color: #065f46; }
.notif-badge-red    { background: #fee2e2; color: #991b1b; }
.notif-badge-purple { background: #ede9fe; color: #5b21b6; }
.notif-badge-gray   { background: var(--sl-gray-100, #f3f4f6); color: var(--sl-gray-700, #374151); }

.notif-title {
    font-size: 0.98rem;
    color: var(--sl-gray-900, #111);
    margin: 0.25rem 0;
    line-height: 1.4;
}
.notif-meta {
    font-size: 0.78rem;
    color: var(--sl-gray-500, #6b7280);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.notif-avatar {
    width: 22px; height: 22px; border-radius: 50%;
    background: var(--sl-gray-100, #f3f4f6); color: var(--sl-gray-500, #6b7280);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 0.72rem;
}
.notif-icon-amber  { background: #fef3c7 !important; color: var(--sl-accent-700, #b45309) !important; }
.notif-icon-blue   { background: #dbeafe !important; color: #1e40af !important; }
.notif-icon-green  { background: #d1fae5 !important; color: #065f46 !important; }
.notif-icon-red    { background: #fee2e2 !important; color: #991b1b !important; }
.notif-icon-purple { background: #ede9fe !important; color: #5b21b6 !important; }

.notif-arrow {
    width: 32px; height: 32px; border-radius: 8px;
    background: transparent; color: var(--sl-gray-400, #9ca3af);
    display: inline-flex; align-items: center; justify-content: center;
    flex-shrink: 0; text-decoration: none; transition: all 0.15s ease;
}
.notif-arrow:hover { background: var(--sl-gray-100, #f3f4f6); color: var(--sl-gray-900, #111); }

.notif-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    padding: 0.85rem 1.25rem;
    border-top: 1px solid var(--sl-gray-100, #f3f4f6);
    background: var(--sl-gray-50, #f9fafb);
    border-radius: 0 0 16px 16px;
}
.notif-footer .pagination { margin: 0; }

@media (max-width: 575.98px) {
    .notif-item { padding: 0.85rem 1rem; }
    .notif-arrow { display: none; }
    .notif-footer { flex-direction: column; align-items: flex-start; }
}
</style>

<script>
(function () {
    var selectAll = document.getElementById('notif-select-all');
    var checkboxes = document.querySelectorAll('.notif-checkbox');
    var markBtn = document.getElementById('notif-mark-selected');

    function updateMarkBtn() {
        var any = Array.from(checkboxes).some(function (cb) { return cb.checked; });
        if (markBtn) markBtn.disabled = !any;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(function (cb) { cb.checked = selectAll.checked; });
            updateMarkBtn();
        });
    }
    checkboxes.forEach(function (cb) {
        cb.addEventListener('change', updateMarkBtn);
    });
    updateMarkBtn();
})();
</script>
@endpush
@endsection
