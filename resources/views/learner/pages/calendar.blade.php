@extends('layouts.learner')

@section('title', 'My Calendar')
@section('heading', 'My Calendar')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i></a></li>
        <li class="breadcrumb-item active" aria-current="page">Calendar</li>
        <li class="breadcrumb-item active" id="lc-cal-breadcrumb-range" aria-current="page"></li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <h5 class="mb-0"><i class="bi bi-calendar3 me-2 text-primary"></i>My Lessons</h5>
    <a href="/find-instructor" class="btn btn-primary lc-book-btn">
        <i class="bi bi-plus-lg me-1"></i> Book a New Lesson
    </a>
</div>

{{-- View switcher --}}
<div class="btn-group btn-group-sm mb-3" role="group">
    <input type="radio" class="btn-check" name="lc-view" id="lc-view-day" value="day" autocomplete="off">
    <label class="btn btn-outline-secondary" for="lc-view-day">Day</label>
    <input type="radio" class="btn-check" name="lc-view" id="lc-view-week" value="week" autocomplete="off" checked>
    <label class="btn btn-outline-secondary" for="lc-view-week">Week</label>
    <input type="radio" class="btn-check" name="lc-view" id="lc-view-month" value="month" autocomplete="off">
    <label class="btn btn-outline-secondary" for="lc-view-month">Month</label>
</div>

{{-- Empty state container (shown when no bookings) --}}
<div id="lc-empty-container" style="display:none;"></div>

{{-- Navigation card --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span id="lc-range-label" class="fw-semibold"></span>
        <div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="lc-prev" title="Previous"><i class="bi bi-chevron-left"></i></button>
            <button type="button" class="btn btn-sm btn-outline-primary mx-1" id="lc-today">Today</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="lc-next" title="Next"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        {{-- Week view (default) --}}
        <div id="lc-week-container" class="calendar-view">
            <div id="lc-week-grid" class="lc-week-grid"></div>
        </div>
        {{-- Day view --}}
        <div id="lc-day-container" class="calendar-view" style="display:none;">
            <div id="lc-day-grid" class="lc-day-grid"></div>
        </div>
        {{-- Month view --}}
        <div id="lc-month-container" class="calendar-view p-3" style="display:none;">
            <div id="lc-weekdays" class="lc-weekdays"></div>
            <div id="lc-grid" class="lc-month-grid"></div>
        </div>
    </div>
</div>

{{-- Day detail popout --}}
<div class="card border-0 shadow-sm mt-2" id="lc-day-detail" style="display:none;">
    <div class="card-header">Bookings on <span id="lc-day-detail-date"></span></div>
    <div class="card-body" id="lc-day-detail-body"></div>
</div>

{{-- Upcoming lessons summary --}}
<div class="card border-0 shadow-sm mt-3" id="lc-upcoming-card">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Upcoming Lessons</h6>
    </div>
    <div class="card-body p-0" id="lc-upcoming-body">
        <p class="text-muted p-3 mb-0">Loading...</p>
    </div>
</div>

<style>
/* ===== Book a Lesson button ===== */
.lc-book-btn {
    background: var(--sl-primary-500);
    border-color: var(--sl-primary-500);
    color: #fff;
    font-weight: 600;
}
.lc-book-btn:hover {
    background: var(--sl-primary-600);
    border-color: var(--sl-primary-600);
    color: #fff;
}

/* ===== Week grid ===== */
.lc-week-grid { display: grid; font-size: 0.8rem; }
.lc-week-grid .week-time-col { background: var(--sl-gray-50); border-right: 1px solid var(--sl-gray-200); padding: 2px 8px; text-align: right; color: var(--sl-gray-500); }
.lc-week-grid .week-day-header { padding: 8px; text-align: center; border-bottom: 1px solid var(--sl-gray-200); font-weight: 600; background: #fff; }
.lc-week-grid .week-day-header.today { background: var(--sl-primary-50); color: var(--sl-primary-700); }
.lc-week-grid .week-cell { min-height: 48px; border-right: 1px solid var(--sl-gray-100); border-bottom: 1px solid var(--sl-gray-100); position: relative; background: #fff; overflow: visible; }
.lc-week-grid .week-event {
    position: absolute; left: 2px; right: 2px;
    border-radius: var(--sl-radius-sm);
    padding: 4px 6px;
    overflow: hidden;
    font-size: 0.7rem;
    cursor: pointer;
    transition: box-shadow 0.15s ease, transform 0.15s ease;
}
.lc-week-grid .week-event:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transform: translateY(-1px);
    z-index: 3 !important;
}

/* ===== Status-specific event styles ===== */
.week-event.lc-status-confirmed { border-left: 3px solid #22c55e; background: #dcfce7; color: #166534; }
.week-event.lc-status-instructor_arrived { border-left: 3px solid #3b82f6; background: #dbeafe; color: #1e40af; }
.week-event.lc-status-in_progress { border-left: 3px solid #a855f7; background: #f3e8ff; color: #6b21a8; }
.week-event.lc-status-completed { border-left: 3px solid #9ca3af; background: #f3f4f6; color: #374151; }
.week-event.lc-status-proposed { border-left: 3px solid #f59e0b; background: #fef3c7; color: #92400e; }
.week-event.lc-status-pending { border-left: 3px solid #eab308; background: #fefce8; color: #854d0e; }

/* ===== Month grid ===== */
.lc-weekdays { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; padding: 0 8px; font-size: 0.75rem; font-weight: 600; color: var(--sl-gray-500); background: var(--sl-gray-50); }
.lc-month-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; padding: 8px; }
.lc-day-cell { min-height: 70px; padding: 4px; border: 1px solid var(--sl-gray-200); border-radius: var(--sl-radius-sm); font-size: 0.875rem; background: #fff; }
.lc-day-cell.other-month { opacity: 0.5; background: var(--sl-gray-50); }
.lc-day-cell.today { border-color: var(--sl-primary-500); background: var(--sl-primary-50); }
.lc-day-cell.has-booking { background: #fff7ed; }
.lc-day-cell.clickable { cursor: pointer; }
.lc-day-cell .day-events { font-size: 0.7rem; color: var(--sl-primary-700); }

/* ===== Day grid ===== */
.lc-day-grid .day-time-row { display: grid; grid-template-columns: 60px 1fr; border-bottom: 1px solid var(--sl-gray-100); min-height: 48px; }
.lc-day-grid .day-time-label { background: var(--sl-gray-50); padding: 4px 8px; text-align: right; font-size: 0.8rem; color: var(--sl-gray-500); }
.lc-day-grid .day-time-slot { position: relative; background: #fff; }

/* ===== Day detail items ===== */
.lc-day-detail-item {
    padding: 8px 0;
    border-bottom: 1px solid var(--sl-gray-100);
    cursor: pointer;
    transition: background 0.15s;
}
.lc-day-detail-item:last-child { border-bottom: none; }
.lc-day-detail-item:hover { background: var(--sl-gray-50); }

/* ===== Status badge (shared) ===== */
.lc-status-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 600;
    white-space: nowrap;
}

/* ===== Upcoming list ===== */
.lc-upcoming-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.25rem;
    border-bottom: 1px solid var(--sl-gray-100);
    transition: background 0.15s;
}
.lc-upcoming-row:last-child { border-bottom: none; }
.lc-upcoming-row:hover { background: var(--sl-gray-50); }
.lc-upcoming-row .lc-date-badge {
    min-width: 48px;
    text-align: center;
    padding: 0.4rem;
    border-radius: var(--sl-radius-sm);
    background: var(--sl-primary-50);
    color: var(--sl-primary-700);
    font-weight: 700;
    font-size: 0.75rem;
    line-height: 1.2;
}
.lc-upcoming-row .lc-date-badge .day-num { font-size: 1.25rem; display: block; }

/* Upcoming avatar */
.lc-upcoming-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 0.85rem;
    flex-shrink: 0;
}

/* Relative time text */
.lc-relative-time {
    font-size: 0.75rem;
    color: var(--sl-gray-500);
    margin-top: 1px;
}

/* Today highlight glow */
.lc-upcoming-today {
    background: #fff7ed;
    border-left: 3px solid var(--sl-primary-500);
    box-shadow: 0 0 0 1px var(--sl-primary-200);
}

/* ===== Today time indicator ===== */
.lc-today-line {
    pointer-events: none;
    z-index: 5;
}

/* ===== Booking popover ===== */
.lc-booking-popover {
    position: absolute;
    z-index: 1050;
    width: 320px;
    max-width: calc(100vw - 16px);
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.18), 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid var(--sl-gray-200);
    overflow: hidden;
    animation: lcPopoverIn 0.15s ease-out;
}
@keyframes lcPopoverIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
.lc-popover-header {
    padding: 14px 16px;
    position: relative;
}
.lc-popover-close {
    position: absolute;
    top: 8px;
    right: 10px;
    border: none;
    background: none;
    font-size: 1.4rem;
    line-height: 1;
    color: var(--sl-gray-500);
    cursor: pointer;
    padding: 2px 6px;
    border-radius: 4px;
}
.lc-popover-close:hover { background: rgba(0,0,0,0.06); color: var(--sl-gray-800); }
.lc-avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 1rem;
    flex-shrink: 0;
}
.lc-popover-body {
    padding: 12px 16px 16px;
}
.lc-popover-detail-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 3px 0;
    font-size: 0.85rem;
    color: var(--sl-gray-700);
}
.lc-popover-detail-row i {
    color: var(--sl-gray-400);
    width: 16px;
    text-align: center;
}
.lc-popover-status {
    text-align: left;
}
.lc-popover-actions {
    margin-top: 12px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}
.lc-popover-actions .btn { font-size: 0.8rem; }

/* Status-specific action messages */
.lc-status-message {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    width: 100%;
}
.lc-status-arrived {
    background: #dcfce7;
    color: #166534;
}
.lc-status-arrived i { color: #22c55e; font-size: 1.1rem; }
.lc-status-inprogress {
    background: #f3e8ff;
    color: #6b21a8;
}
.lc-status-inprogress i { color: #a855f7; }
.lc-elapsed-timer {
    font-family: monospace;
    font-weight: 700;
    margin-left: auto;
    font-size: 0.95rem;
}

/* ===== Empty state ===== */
.lc-empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
    background: #fff;
    border: 2px dashed var(--sl-gray-200);
    border-radius: 12px;
    margin-bottom: 1rem;
}
.lc-empty-icon {
    font-size: 3rem;
    color: var(--sl-gray-300);
    margin-bottom: 0.75rem;
}
.lc-empty-state h6 {
    font-weight: 600;
    color: var(--sl-gray-700);
    margin-bottom: 0.25rem;
}
.lc-empty-state p {
    font-size: 0.9rem;
    margin-bottom: 1rem;
}
.lc-empty-state .btn {
    background: var(--sl-primary-500);
    border-color: var(--sl-primary-500);
}
.lc-empty-state .btn:hover {
    background: var(--sl-primary-600);
    border-color: var(--sl-primary-600);
}

/* ===== Responsive tweaks ===== */
@media (max-width: 576px) {
    .lc-booking-popover { width: calc(100vw - 16px); }
}
</style>

@push('scripts')
    @vite('resources/js/learner-calendar.js')
@endpush
@endsection
