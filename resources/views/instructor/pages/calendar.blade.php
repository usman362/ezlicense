@extends('layouts.instructor')

@section('title', 'Calendar')
@section('heading', 'Calendar')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bi bi-house"></i></a></li>
        <li class="breadcrumb-item active" aria-current="page">Calendar</li>
        <li class="breadcrumb-item active" id="calendar-breadcrumb-range" aria-current="page"></li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <h5 class="mb-0">Calendar</h5>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-link p-0 text-muted" id="calendar-settings-btn" title="Settings"><i class="bi bi-gear"></i></button>
        <a href="{{ route('instructor.settings.opening-hours') }}" class="btn btn-warning" id="calendar-create-event-btn">
            <i class="bi bi-calendar-plus me-1"></i> Create Event
        </a>
    </div>
</div>

<div class="btn-group btn-group-sm mb-3" role="group">
    <input type="radio" class="btn-check" name="calendar-view" id="view-day" value="day" autocomplete="off">
    <label class="btn btn-outline-secondary" for="view-day">Day</label>
    <input type="radio" class="btn-check" name="calendar-view" id="view-week" value="week" autocomplete="off" checked>
    <label class="btn btn-outline-secondary" for="view-week">Week</label>
    <input type="radio" class="btn-check" name="calendar-view" id="view-month" value="month" autocomplete="off">
    <label class="btn btn-outline-secondary" for="view-month">Month</label>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span id="calendar-range-label" class="fw-semibold"></span>
        <div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="calendar-prev" title="Previous"><i class="bi bi-chevron-left"></i></button>
            <button type="button" class="btn btn-sm btn-outline-primary mx-1" id="calendar-today">Today</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="calendar-next" title="Next"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        {{-- Week view (default) --}}
        <div id="calendar-week-container" class="calendar-view">
            <div id="calendar-week-grid" class="instructor-week-grid"></div>
        </div>
        {{-- Day view --}}
        <div id="calendar-day-container" class="calendar-view" style="display: none;">
            <div id="calendar-day-grid" class="instructor-day-grid"></div>
        </div>
        {{-- Month view --}}
        <div id="calendar-month-container" class="calendar-view p-3" style="display: none;">
            <div id="calendar-weekdays" class="instructor-calendar-weekdays"></div>
            <div id="calendar-grid" class="instructor-calendar-grid"></div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-2" id="calendar-day-detail" style="display: none;">
    <div class="card-header">Bookings on <span id="calendar-day-detail-date"></span></div>
    <div class="card-body" id="calendar-day-detail-body"></div>
</div>

<style>
.instructor-week-grid { display: grid; font-size: 0.8rem; }
.instructor-week-grid .week-time-col { background: #f8f9fa; border-right: 1px solid #dee2e6; padding: 2px 8px; text-align: right; color: #6c757d; }
.instructor-week-grid .week-day-header { padding: 8px; text-align: center; border-bottom: 1px solid #dee2e6; font-weight: 600; background: #fff; }
.instructor-week-grid .week-day-header.today { background: #fff9e6; color: #856404; }
.instructor-week-grid .week-cell { min-height: 48px; border-right: 1px solid #eee; border-bottom: 1px solid #eee; position: relative; background: #fff; overflow: visible; }
.instructor-week-grid .week-cell.unavailable { background: repeating-linear-gradient(-45deg, #f8f9fa, #f8f9fa 4px, #eee 4px, #eee 8px); }
.instructor-week-grid .week-event { position: absolute; left: 2px; right: 2px; border-radius: 4px; padding: 4px 6px; overflow: hidden; font-size: 0.7rem; cursor: pointer; border-left: 3px solid rgba(0,0,0,0.2); }
.instructor-week-grid .week-event.booking { background: #fff3cd; color: #856404; }
.instructor-week-grid .week-event.personal { background: #e2e3e5; color: #383d41; }
.instructor-calendar-weekdays { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; padding: 0 8px; font-size: 0.75rem; font-weight: 600; color: #6c757d; background: #f8f9fa; }
.instructor-calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; padding: 8px; }
.instructor-calendar-day { min-height: 70px; padding: 4px; border: 1px solid #dee2e6; border-radius: 4px; font-size: 0.875rem; background: #fff; }
.instructor-calendar-day.other-month { opacity: 0.5; background: #f8f9fa; }
.instructor-calendar-day.today { border-color: #f0ad4e; background: #fff9e6; }
.instructor-calendar-day.past { background: #f8f9fa; }
.instructor-calendar-day.available { background: #e8f5e9; }
.instructor-calendar-day.has-booking { background: #e3f2fd; }
.instructor-calendar-day.clickable { cursor: pointer; }
.instructor-day-grid .day-time-row { display: grid; grid-template-columns: 60px 1fr; border-bottom: 1px solid #eee; min-height: 48px; }
.instructor-day-grid .day-time-label { background: #f8f9fa; padding: 4px 8px; text-align: right; font-size: 0.8rem; color: #6c757d; }
.instructor-day-grid .day-time-slot { position: relative; background: #fff; }
.instructor-day-grid .day-time-slot.unavailable { background: repeating-linear-gradient(-45deg, #f8f9fa, #f8f9fa 4px, #eee 4px, #eee 8px); }
</style>
@push('scripts')
    @vite('resources/js/instructor-calendar.js')
@endpush
@endsection
