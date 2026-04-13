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
        <button type="button" class="btn btn-outline-secondary btn-sm" id="calendar-sync-toggle" title="Calendar Sync">
            <i class="bi bi-arrow-repeat me-1"></i>Sync
        </button>
        <button type="button" class="btn btn-link p-0 text-muted" id="calendar-settings-btn" title="Settings"><i class="bi bi-gear"></i></button>
        <a href="{{ route('instructor.settings.opening-hours') }}" class="btn btn-warning" id="calendar-create-event-btn">
            <i class="bi bi-calendar-plus me-1"></i> Create Event
        </a>
    </div>
</div>

{{-- Calendar Sync Panel --}}
<div class="card border-0 shadow-sm mb-3" id="calendar-sync-panel" style="display: none;">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h6 class="fw-bold mb-1"><i class="bi bi-phone me-2"></i>Subscribe to Your Calendar</h6>
                <p class="text-muted small mb-2">Subscribe to this URL in your calendar app for one-way sync. New bookings, reschedules, and cancellations appear automatically.</p>
            </div>
            <button type="button" class="btn-close" id="calendar-sync-close" aria-label="Close"></button>
        </div>
        <div id="sync-urls-loading" class="text-muted small">Loading subscription URLs...</div>
        <div id="sync-urls-content" style="display: none;">
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <a href="#" id="sync-apple-btn" class="btn btn-outline-dark btn-sm" target="_blank">
                        <i class="bi bi-apple me-1"></i>Apple Calendar
                    </a>
                </div>
                <div class="col-auto">
                    <a href="#" id="sync-google-btn" class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="bi bi-google me-1"></i>Google Calendar
                    </a>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-secondary btn-sm" id="sync-copy-outlook-btn">
                        <i class="bi bi-clipboard me-1"></i>Copy URL (Outlook)
                    </button>
                </div>
            </div>
            <div class="bg-light rounded p-2">
                <code class="small text-break" id="sync-feed-url"></code>
            </div>
        </div>
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
/* ── Grid base ── */
.instructor-week-grid { display: grid; font-size: 0.8rem; }
.instructor-week-grid .week-time-col { background: #f8f9fa; border-right: 1px solid #dee2e6; padding: 2px 8px; text-align: right; color: #6c757d; }
.instructor-week-grid .week-day-header { padding: 8px; text-align: center; border-bottom: 1px solid #dee2e6; font-weight: 600; background: #fff; }
.instructor-week-grid .week-day-header.today { background: #fff9e6; color: #856404; }
.instructor-week-grid .week-cell { min-height: 48px; border-right: 1px solid #eee; border-bottom: 1px solid #eee; position: relative; background: #fff; overflow: visible; cursor: pointer; }
.instructor-week-grid .week-cell:hover { background: #f0f9ff; }
.instructor-week-grid .week-cell.unavailable { background: repeating-linear-gradient(-45deg, #f8f9fa, #f8f9fa 4px, #eee 4px, #eee 8px); }
.instructor-week-grid .week-event { position: absolute; left: 2px; right: 2px; border-radius: 4px; padding: 4px 6px; overflow: hidden; font-size: 0.7rem; cursor: pointer; transition: box-shadow 0.15s, transform 0.15s; }
.instructor-week-grid .week-event:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.15); transform: translateY(-1px); z-index: 10 !important; }
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
.instructor-day-grid .day-time-slot { position: relative; background: #fff; cursor: pointer; }
.instructor-day-grid .day-time-slot:hover { background: #f0f9ff; }
.instructor-day-grid .day-time-slot.unavailable { background: repeating-linear-gradient(-45deg, #f8f9fa, #f8f9fa 4px, #eee 4px, #eee 8px); }

/* ── Booking detail popover ── */
.cal-popover {
  position: fixed; z-index: 1050; width: 320px; max-width: 90vw;
  background: #fff; border: 1px solid #dee2e6; border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.15); font-size: 0.85rem;
  animation: calPopIn 0.15s ease-out;
}
@keyframes calPopIn { from { opacity:0; transform: scale(0.95); } to { opacity:1; transform: scale(1); } }
.cal-popover-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 10px 14px; border-bottom: 1px solid #eee; background: #f8f9fa;
  border-radius: 8px 8px 0 0; font-size: 0.9rem;
}
.cal-popover-close {
  background: none; border: none; font-size: 1.2rem; cursor: pointer;
  color: #6c757d; padding: 0 4px; line-height: 1;
}
.cal-popover-close:hover { color: #333; }
.cal-popover-body { padding: 12px 14px; }
.cal-popover-body i { color: #6c757d; width: 16px; text-align: center; }
.cal-popover-actions { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px; }
.cal-status-badge {
  display: inline-block; padding: 2px 8px; border-radius: 10px;
  font-size: 0.7rem; font-weight: 600; color: #fff; text-transform: capitalize;
}

/* ── Empty slot menu ── */
.cal-slot-menu {
  position: fixed; z-index: 1050; width: 220px;
  background: #fff; border: 1px solid #dee2e6; border-radius: 8px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.12); font-size: 0.85rem;
  animation: calPopIn 0.12s ease-out; overflow: hidden;
}
.cal-slot-menu-header {
  padding: 8px 12px; background: #f8f9fa; border-bottom: 1px solid #eee;
  font-weight: 600; font-size: 0.75rem; color: #6c757d;
}
.cal-slot-menu-item {
  display: block; padding: 10px 12px; color: #333; text-decoration: none;
  border-bottom: 1px solid #f0f0f0; transition: background 0.1s;
}
.cal-slot-menu-item:last-child { border-bottom: none; }
.cal-slot-menu-item:hover { background: #f0f9ff; color: #0d6efd; }

/* ── Status legend (for reference in month view dots) ── */
.day-events div { font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>
@push('scripts')
    @vite('resources/js/instructor-calendar.js')
    <script>
    (function() {
        var syncToggle = document.getElementById('calendar-sync-toggle');
        var syncPanel = document.getElementById('calendar-sync-panel');
        var syncClose = document.getElementById('calendar-sync-close');
        var syncLoading = document.getElementById('sync-urls-loading');
        var syncContent = document.getElementById('sync-urls-content');
        var syncLoaded = false;

        if (!syncToggle || !syncPanel) return;

        syncToggle.addEventListener('click', function() {
            var visible = syncPanel.style.display !== 'none';
            syncPanel.style.display = visible ? 'none' : 'block';
            if (!visible && !syncLoaded) loadSyncUrls();
        });

        if (syncClose) {
            syncClose.addEventListener('click', function() {
                syncPanel.style.display = 'none';
            });
        }

        function loadSyncUrls() {
            fetch('/api/calendar/subscribe-urls', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                syncLoaded = true;
                if (syncLoading) syncLoading.style.display = 'none';
                if (syncContent) syncContent.style.display = 'block';

                var appleBtn = document.getElementById('sync-apple-btn');
                var googleBtn = document.getElementById('sync-google-btn');
                var feedUrl = document.getElementById('sync-feed-url');
                var copyBtn = document.getElementById('sync-copy-outlook-btn');

                if (appleBtn && data.webcal_url) appleBtn.href = data.webcal_url;
                if (googleBtn && data.google_url) googleBtn.href = data.google_url;
                if (feedUrl && data.https_url) feedUrl.textContent = data.https_url;

                if (copyBtn && data.https_url) {
                    copyBtn.addEventListener('click', function() {
                        navigator.clipboard.writeText(data.https_url).then(function() {
                            copyBtn.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
                            setTimeout(function() {
                                copyBtn.innerHTML = '<i class="bi bi-clipboard me-1"></i>Copy URL (Outlook)';
                            }, 2000);
                        });
                    });
                }
            })
            .catch(function(err) {
                console.error('Sync URL error:', err);
                if (syncLoading) syncLoading.textContent = 'Could not load sync URLs. Please try again.';
            });
        }
    })();
    </script>
@endpush
@endsection
