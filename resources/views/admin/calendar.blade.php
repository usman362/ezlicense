@extends('layouts.admin')

@section('title', 'Calendar')
@section('heading', 'Calendar')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bi bi-house"></i></a></li>
        <li class="breadcrumb-item active" aria-current="page">Calendar</li>
        <li class="breadcrumb-item active" id="ac-breadcrumb-range" aria-current="page"></li>
    </ol>
</nav>

{{-- Stats summary --}}
<div class="row g-3 mb-3">
    <div class="col-auto">
        <div class="card border-0 shadow-sm" style="min-width:140px">
            <div class="card-body py-2 px-3 text-center">
                <div class="text-muted small">Today</div>
                <div class="fw-bold fs-5" id="ac-stat-today">--</div>
            </div>
        </div>
    </div>
    <div class="col-auto">
        <div class="card border-0 shadow-sm" style="min-width:140px">
            <div class="card-body py-2 px-3 text-center">
                <div class="text-muted small">This Week</div>
                <div class="fw-bold fs-5" id="ac-stat-week">--</div>
            </div>
        </div>
    </div>
    <div class="col-auto">
        <div class="card border-0 shadow-sm" style="min-width:140px">
            <div class="card-body py-2 px-3 text-center">
                <div class="text-muted small text-warning">Pending</div>
                <div class="fw-bold fs-5 text-warning" id="ac-stat-pending">--</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter bar --}}
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <select id="ac-filter-instructor" class="form-select form-select-sm" style="max-width:220px">
        <option value="">All Instructors</option>
    </select>
    <select id="ac-filter-status" class="form-select form-select-sm" style="max-width:180px">
        <option value="">All Statuses</option>
        <option value="pending">Pending</option>
        <option value="proposed">Proposed</option>
        <option value="confirmed">Confirmed</option>
        <option value="instructor_arrived">Arrived</option>
        <option value="in_progress">In Progress</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
    </select>

    <div class="ms-auto d-flex align-items-center gap-2">
        <div class="btn-group btn-group-sm" role="group">
            <input type="radio" class="btn-check" name="ac-view" id="ac-view-day" value="day" autocomplete="off">
            <label class="btn btn-outline-secondary" for="ac-view-day">Day</label>
            <input type="radio" class="btn-check" name="ac-view" id="ac-view-week" value="week" autocomplete="off" checked>
            <label class="btn btn-outline-secondary" for="ac-view-week">Week</label>
            <input type="radio" class="btn-check" name="ac-view" id="ac-view-month" value="month" autocomplete="off">
            <label class="btn btn-outline-secondary" for="ac-view-month">Month</label>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span id="ac-range-label" class="fw-semibold"></span>
        <div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="ac-prev" title="Previous"><i class="bi bi-chevron-left"></i></button>
            <button type="button" class="btn btn-sm btn-outline-primary mx-1" id="ac-today">Today</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="ac-next" title="Next"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        {{-- Week view (default) --}}
        <div id="ac-week-container" class="ac-view-pane">
            <div id="ac-week-grid" class="ac-week-grid"></div>
        </div>
        {{-- Day view --}}
        <div id="ac-day-container" class="ac-view-pane" style="display:none">
            <div id="ac-day-grid" class="ac-day-grid"></div>
        </div>
        {{-- Month view --}}
        <div id="ac-month-container" class="ac-view-pane p-3" style="display:none">
            <div id="ac-month-weekdays" class="ac-month-weekdays"></div>
            <div id="ac-month-grid" class="ac-month-grid"></div>
        </div>
    </div>
</div>

{{-- Booking detail modal --}}
<div class="modal fade" id="ac-booking-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Booking Details</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="ac-booking-modal-body">
                <p class="text-muted">Loading...</p>
            </div>
            <div class="modal-footer" id="ac-booking-modal-footer">
            </div>
        </div>
    </div>
</div>

<style>
/* ── Status colours ─────────────────────────────── */
.ac-status-pending      { background:#fff3cd; color:#856404; border-left:3px solid #ffc107; }
.ac-status-proposed     { background:#fff3cd; color:#856404; border-left:3px solid #ffc107; }
.ac-status-confirmed    { background:#d4edda; color:#155724; border-left:3px solid #28a745; }
.ac-status-instructor_arrived { background:#cce5ff; color:#004085; border-left:3px solid #007bff; }
.ac-status-in_progress  { background:#e8daef; color:#4a235a; border-left:3px solid #8e44ad; }
.ac-status-completed    { background:#e2e3e5; color:#383d41; border-left:3px solid #6c757d; }
.ac-status-cancelled    { background:#f8d7da; color:#721c24; border-left:3px solid #dc3545; }
.ac-status-no_show      { background:#f8d7da; color:#721c24; border-left:3px solid #dc3545; }

/* ── Week grid ──────────────────────────────────── */
.ac-week-grid { display:grid; font-size:0.78rem; overflow-x:auto; }
.ac-week-grid .wk-time { background:#f8f9fa; border-right:1px solid #dee2e6; padding:2px 8px; text-align:right; color:#6c757d; }
.ac-week-grid .wk-hdr  { padding:8px; text-align:center; border-bottom:1px solid #dee2e6; font-weight:600; background:#fff; }
.ac-week-grid .wk-hdr.today { background:#fff9e6; color:#856404; }
.ac-week-grid .wk-cell { min-height:48px; border-right:1px solid #eee; border-bottom:1px solid #eee; position:relative; background:#fff; overflow:visible; }
.ac-week-grid .wk-evt  { position:absolute; left:2px; right:2px; border-radius:4px; padding:3px 5px; overflow:hidden; font-size:0.68rem; cursor:pointer; line-height:1.25; z-index:1; }
.ac-week-grid .wk-evt:hover { filter:brightness(0.92); }

/* ── Day grid ───────────────────────────────────── */
.ac-day-grid .dy-row    { display:grid; grid-template-columns:60px 1fr; border-bottom:1px solid #eee; min-height:48px; }
.ac-day-grid .dy-label  { background:#f8f9fa; padding:4px 8px; text-align:right; font-size:0.8rem; color:#6c757d; }
.ac-day-grid .dy-slot   { position:relative; background:#fff; }

/* ── Month grid ─────────────────────────────────── */
.ac-month-weekdays { display:grid; grid-template-columns:repeat(7,1fr); gap:1px; font-size:0.75rem; font-weight:600; color:#6c757d; background:#f8f9fa; padding:0 8px; }
.ac-month-grid     { display:grid; grid-template-columns:repeat(7,1fr); gap:1px; padding:8px; }
.ac-month-day      { min-height:80px; padding:4px; border:1px solid #dee2e6; border-radius:4px; font-size:0.85rem; background:#fff; cursor:pointer; }
.ac-month-day.other-month { opacity:0.45; background:#f8f9fa; }
.ac-month-day.today { border-color:#f0ad4e; background:#fff9e6; }
.ac-month-day .day-num { font-weight:600; }
.ac-month-day .ac-badge-count { display:inline-block; font-size:0.65rem; padding:1px 6px; border-radius:10px; font-weight:600; }

/* ── Modal detail table ──────────────────────────── */
#ac-booking-modal-body table td { padding:4px 8px; font-size:0.875rem; }
#ac-booking-modal-body table td:first-child { font-weight:600; color:#6c757d; white-space:nowrap; }
</style>

@push('scripts')
    @vite('resources/js/admin-calendar.js')
@endpush
@endsection
