@extends('layouts.learner')

@section('title', 'Make a Booking')
@section('heading', 'Make a Booking')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Make a Booking</li>
    </ol>
</nav>

<div class="mb-4">
    <h3 class="fw-bolder mb-1" style="letter-spacing:-0.02em;">Make a Booking</h3>
    <p class="text-muted mb-0">Choose your lesson type, pick a time, and confirm — it takes less than a minute.</p>
</div>

{{-- Stepper --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="sl-stepper">
            <div class="step active">
                <div class="step-circle"><span>1</span></div>
                <div class="step-label d-none d-md-inline">Details</div>
            </div>
            <div class="step-connector"></div>
            <div class="step">
                <div class="step-circle"><span>2</span></div>
                <div class="step-label d-none d-md-inline">Review</div>
            </div>
            <div class="step-connector"></div>
            <div class="step">
                <div class="step-circle"><span>3</span></div>
                <div class="step-label d-none d-md-inline">Payment</div>
            </div>
            <div class="step-connector"></div>
            <div class="step">
                <div class="step-circle"><span>4</span></div>
                <div class="step-label d-none d-md-inline">Confirmed</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- My Instructor --}}
        @php
            $instructor = $instructorProfile->user;
            $profile = $instructorProfile;
            $rate = $profile->lesson_price !== null ? '$' . number_format((float) $profile->lesson_price, 0) . '/hr' : null;
            $vehicle = trim(implode(' ', array_filter([$profile->vehicle_make, $profile->vehicle_model, $profile->vehicle_year])));
        @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="text-muted small text-uppercase mb-1" style="letter-spacing:0.08em;">Booking with</h6>
                <h5 class="fw-bold mb-3">Your Instructor</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 h-100" style="background: var(--sl-gray-50);">
                            @if($profile->profile_photo)
                                <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="{{ $instructor->name }}" class="rounded-circle" style="width:56px;height:56px;object-fit:cover;flex-shrink:0;">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bolder text-white" style="width:56px;height:56px;font-size:1.2rem;background:linear-gradient(135deg, var(--sl-primary-500), var(--sl-teal-500));flex-shrink:0;">{{ strtoupper(substr($instructor->name ?? 'I', 0, 1)) }}</div>
                            @endif
                            <div class="min-w-0 flex-grow-1">
                                <div class="fw-bolder text-truncate">{{ $instructor->name }}</div>
                                <a href="tel:{{ $instructor->phone }}" class="small text-decoration-none"><i class="bi bi-telephone me-1"></i>{{ $instructor->phone }}</a>
                                @if($rate)<div class="small text-muted">{{ $rate }}</div>@endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 h-100" style="background: var(--sl-gray-50);">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:#fff;border:1px solid var(--sl-gray-200);flex-shrink:0;"><i class="bi bi-car-front-fill" style="font-size:1.4rem;color:var(--sl-primary-600);"></i></div>
                            <div class="small flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">{{ $vehicle ?: 'Vehicle' }}</div>
                                <div class="text-muted">{{ ucfirst($profile->transmission ?? 'Auto') }} · 5-star ANCAP · Dual controls</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bookings (saved items - shown after first Save) --}}
        <div class="card border-0 shadow-sm mb-4" id="bookings-list-card" style="display: none;">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Bookings</h6>
                <div id="bookings-list"></div>
            </div>
        </div>

        {{-- New Booking form (add more below saved bookings) --}}
        <div class="card border-0 shadow-sm mb-4" id="new-booking-form-card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">New Booking</h6>

                {{-- Booking type --}}
                <div class="btn-group w-100 mb-3" role="group">
                    <input type="radio" class="btn-check" name="booking_type" id="type-1hr" value="1hr" checked>
                    <label class="btn btn-outline-secondary" for="type-1hr">1-Hour Lesson</label>
                    <input type="radio" class="btn-check" name="booking_type" id="type-2hr" value="2hr">
                    <label class="btn btn-outline-secondary" for="type-2hr">2-Hour Lesson</label>
                    <input type="radio" class="btn-check" name="booking_type" id="type-test" value="test_package">
                    <label class="btn btn-outline-secondary" for="type-test">Driving Test Package</label>
                </div>

                <div id="test-package-warning" class="alert alert-warning small mb-3" style="display: none;">
                    WARNING: You must book your own driving test with local roads authority. Our Test Package only books the instructor and vehicle.
                </div>

                <div id="field-test-location" class="mb-3" style="display: none;">
                    <label class="form-label">Driving test location</label>
                    <select class="form-select" id="test_location">
                        <option value="">Select the test location</option>
                        @foreach($instructorProfile->serviceAreas ?? [] as $suburb)
                            <option value="{{ $suburb->id }}">{{ $suburb->name }}, {{ $suburb->postcode }} {{ $suburb->state?->code }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Available Dates</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                            <input type="text" class="form-control" id="booking_date" placeholder="Select date" readonly>
                            <input type="hidden" id="booking_date_iso">
                        </div>
                        <div id="date-dropdown" class="border rounded bg-white shadow-sm mt-1" style="display: none; max-height: 200px; overflow-y: auto; position: absolute; z-index: 10;"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Available Times</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-clock"></i></span>
                            <select class="form-select" id="booking_time">
                                <option value="">Select time</option>
                            </select>
                        </div>
                    </div>
                </div>

                <p id="pickup-time-note" class="small text-muted mb-3" style="display: none;">The pickup time will be 1 hour prior to the test start time. You can reschedule this package any time up until 24 hrs before the pick up time.</p>

                <div class="border rounded p-3 mb-3">
                    <h6 class="small fw-bold mb-2">Pick Up Location</h6>
                    <div class="mb-2">
                        <label class="form-label small">* Pick up address</label>
                        <input type="text" class="form-control" id="pickup_address" placeholder="Enter a street address">
                    </div>
                    {{-- Typable suburb/postcode search (EasyLicence-style) --}}
                    <div class="mb-2 position-relative">
                        <label class="form-label small">* Suburb or postcode</label>
                        <input type="text" class="form-control" id="pickup_suburb_search"
                               placeholder="Type a suburb name or postcode (e.g. Parramatta or 2150)"
                               autocomplete="off" data-ac-target="pickup">
                        <div class="suburb-ac-dropdown" id="pickup_suburb_ac"></div>
                        <input type="hidden" id="pickup_suburb" name="pickup_suburb">
                        <input type="hidden" id="pickup_state" name="pickup_state">
                        <div class="small text-muted mt-1" id="pickup_selected_display" style="display:none;">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span id="pickup_selected_label"></span>
                            <button type="button" class="btn btn-link btn-sm p-0 ms-2 text-danger" id="pickup_clear">Change</button>
                        </div>
                    </div>
                </div>

                <div id="dropoff-section" class="border rounded p-3 mb-3" style="display: none;">
                    <h6 class="small fw-bold mb-2">Drop Off Location</h6>
                    <div class="mb-2">
                        <label class="form-label small">Drop off address</label>
                        <input type="text" class="form-control" id="dropoff_address" placeholder="Enter a street address">
                    </div>
                    <div class="mb-2 position-relative">
                        <label class="form-label small">* Suburb or postcode</label>
                        <input type="text" class="form-control" id="dropoff_suburb_search"
                               placeholder="Type a suburb name or postcode"
                               autocomplete="off" data-ac-target="dropoff">
                        <div class="suburb-ac-dropdown" id="dropoff_suburb_ac"></div>
                        <input type="hidden" id="dropoff_suburb" name="dropoff_suburb">
                        <input type="hidden" id="dropoff_state" name="dropoff_state">
                        <div class="small text-muted mt-1" id="dropoff_selected_display" style="display:none;">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span id="dropoff_selected_label"></span>
                            <button type="button" class="btn btn-link btn-sm p-0 ms-2 text-danger" id="dropoff_clear">Change</button>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="button" class="btn btn-warning" id="btn-save-booking">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Order Summary</h6>
                <div id="order-items" class="mb-2 small"></div>
                <div id="order-empty" class="text-muted small mb-2">No bookings added yet. Fill the form and click Save.</div>
                <div id="order-totals" class="mb-3" style="display: none;">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Platform Processing Fee</span>
                        <span id="order-fee">$0.00</span>
                        <i class="bi bi-info-circle text-muted ms-1" title="4% processing fee" style="cursor: help;"></i>
                    </div>
                    <div class="d-flex justify-content-between fw-bold pt-2 border-top">
                        <span>Total Payment Due</span>
                        <span id="order-total">$0.00</span>
                    </div>
                    <p class="small text-muted mb-0 mt-1">Or 4 payments of <span id="order-instalment">$0.00</span></p>
                </div>
                <button type="button" class="btn btn-outline-secondary w-100 mb-2" id="btn-add-another">
                    <i class="bi bi-plus-lg me-1"></i> Add Another Booking
                </button>
                <a href="#" class="btn btn-warning w-100" id="btn-continue">
                    Continue <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="instructor_profile_id" value="{{ $instructorProfile->id }}">
<input type="hidden" id="instructor_transmission" value="{{ strtolower($profile->transmission ?? 'auto') }}">
<input type="hidden" id="lesson_price" value="{{ (float) ($profile->lesson_price ?? 65) }}">
<input type="hidden" id="test_package_price" value="{{ (float) ($profile->test_package_price ?? 225) }}">
@if(!empty($googleMapsApiKey))
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places&callback=Function.prototype" async defer></script>
@endif

{{-- Suburb autocomplete + dropdown styling --}}
@push('styles')
<style>
    /* ── Suburb autocomplete dropdown (EasyLicence-style) ── */
    .suburb-ac-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        background: #fff;
        border: 1px solid var(--sl-gray-200, #e5e7eb);
        border-radius: 0.5rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        max-height: 320px;
        overflow-y: auto;
        display: none;
        margin-top: 2px;
    }
    .suburb-ac-dropdown.show { display: block; }
    .suburb-ac-item {
        padding: 0.6rem 0.875rem;
        cursor: pointer;
        border-bottom: 1px solid var(--sl-gray-100, #f3f4f6);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.1s;
    }
    .suburb-ac-item:last-child { border-bottom: none; }
    .suburb-ac-item:hover, .suburb-ac-item.active {
        background: var(--sl-primary-50, #fff7ed);
    }
    .suburb-ac-item .suburb-name { font-weight: 600; color: var(--sl-gray-900, #111827); }
    .suburb-ac-item .suburb-meta { color: var(--sl-gray-500, #6b7280); font-size: 0.85rem; margin-left: auto; }
    .suburb-ac-item .state-badge {
        display: inline-block;
        font-size: 0.7rem;
        padding: 0.15rem 0.45rem;
        background: var(--sl-gray-100, #f3f4f6);
        color: var(--sl-gray-700, #374151);
        border-radius: 4px;
        font-weight: 600;
    }
    .suburb-ac-empty, .suburb-ac-loading {
        padding: 0.75rem 1rem;
        text-align: center;
        color: var(--sl-gray-500, #6b7280);
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
  var instructorProfileId = document.getElementById('instructor_profile_id').value;
  var transmission = document.getElementById('instructor_transmission').value;
  var lessonPrice = parseFloat(document.getElementById('lesson_price').value) || 65;
  var testPackagePrice = parseFloat(document.getElementById('test_package_price').value) || 225;
  var orderItems = [];
  var csrf = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').content;
  var opts = { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf || '', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' };
  var PLATFORM_FEE_PERCENT = 4;

  function showHideTestPackage() {
    var isTest = document.getElementById('type-test').checked;
    document.getElementById('test-package-warning').style.display = isTest ? 'block' : 'none';
    document.getElementById('field-test-location').style.display = isTest ? 'block' : 'none';
    document.getElementById('pickup-time-note').style.display = isTest ? 'block' : 'none';
    document.getElementById('dropoff-section').style.display = isTest ? 'block' : 'none';
  }
  document.querySelectorAll('input[name="booking_type"]').forEach(function(r) {
    r.addEventListener('change', showHideTestPackage);
  });
  showHideTestPackage();

  // ── EasyLicence-style suburb autocomplete ──
  // Queries /api/suburbs/search as user types
  var STATES_MAP = @json($states->mapWithKeys(fn($s) => [$s->code => $s->id])->toArray());

  function initSuburbAutocomplete(prefix) {
    var input = document.getElementById(prefix + '_suburb_search');
    var dropdown = document.getElementById(prefix + '_suburb_ac');
    var suburbHidden = document.getElementById(prefix + '_suburb');
    var stateHidden = document.getElementById(prefix + '_state');
    var selectedDisplay = document.getElementById(prefix + '_selected_display');
    var selectedLabel = document.getElementById(prefix + '_selected_label');
    var clearBtn = document.getElementById(prefix + '_clear');

    if (!input || !dropdown) return;

    var debounceTimer = null;
    var currentRequest = null;
    var activeIndex = -1;
    var currentResults = [];

    function render(items) {
      currentResults = items || [];
      activeIndex = -1;
      if (items.length === 0) {
        dropdown.innerHTML = '<div class="suburb-ac-empty">No suburbs match — try a postcode or nearby area</div>';
      } else {
        dropdown.innerHTML = items.map(function(item, i) {
          return '<div class="suburb-ac-item" data-idx="' + i + '">' +
            '<span class="suburb-name">' + escapeHtml(item.name) + '</span>' +
            '<span class="suburb-meta">' + escapeHtml(item.postcode || '') + '</span>' +
            '<span class="state-badge">' + escapeHtml(item.state || '') + '</span>' +
          '</div>';
        }).join('');
        // Click handlers
        Array.prototype.slice.call(dropdown.querySelectorAll('.suburb-ac-item')).forEach(function(el) {
          el.addEventListener('mousedown', function(e) {
            e.preventDefault();
            var idx = parseInt(el.getAttribute('data-idx'), 10);
            selectItem(currentResults[idx]);
          });
        });
      }
      dropdown.classList.add('show');
    }

    function selectItem(item) {
      if (!item) return;
      suburbHidden.value = item.id;
      stateHidden.value = STATES_MAP[item.state] || '';
      input.value = '';
      dropdown.classList.remove('show');
      selectedLabel.textContent = item.label || (item.name + ', ' + (item.postcode || '') + ' ' + (item.state || ''));
      selectedDisplay.style.display = 'block';
      input.style.display = 'none';
      // Fire change event for other listeners
      suburbHidden.dispatchEvent(new Event('change'));
    }

    function clearSelection() {
      suburbHidden.value = '';
      stateHidden.value = '';
      selectedDisplay.style.display = 'none';
      input.style.display = '';
      input.value = '';
      input.focus();
    }

    function escapeHtml(s) {
      return String(s || '').replace(/[&<>"']/g, function(c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
      });
    }

    input.addEventListener('input', function() {
      var q = input.value.trim();
      if (q.length < 2) { dropdown.classList.remove('show'); return; }
      dropdown.innerHTML = '<div class="suburb-ac-loading"><span class="spinner-border spinner-border-sm me-1"></span>Searching...</div>';
      dropdown.classList.add('show');

      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function() {
        if (currentRequest && currentRequest.abort) currentRequest.abort();
        fetch('/api/suburbs/search?q=' + encodeURIComponent(q), {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin'
        })
        .then(function(r) { return r.json(); })
        .then(function(res) { render(res.data || []); })
        .catch(function() {
          dropdown.innerHTML = '<div class="suburb-ac-empty text-danger">Search failed — please try again</div>';
        });
      }, 220);
    });

    input.addEventListener('keydown', function(e) {
      if (!dropdown.classList.contains('show')) return;
      var items = dropdown.querySelectorAll('.suburb-ac-item');
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        activeIndex = Math.min(activeIndex + 1, items.length - 1);
        updateActive(items);
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        activeIndex = Math.max(activeIndex - 1, 0);
        updateActive(items);
      } else if (e.key === 'Enter' && activeIndex >= 0) {
        e.preventDefault();
        selectItem(currentResults[activeIndex]);
      } else if (e.key === 'Escape') {
        dropdown.classList.remove('show');
      }
    });

    function updateActive(items) {
      Array.prototype.slice.call(items).forEach(function(el, i) {
        el.classList.toggle('active', i === activeIndex);
      });
      if (items[activeIndex]) items[activeIndex].scrollIntoView({ block: 'nearest' });
    }

    input.addEventListener('blur', function() {
      setTimeout(function() { dropdown.classList.remove('show'); }, 200);
    });

    if (clearBtn) clearBtn.addEventListener('click', clearSelection);
  }

  initSuburbAutocomplete('pickup');
  initSuburbAutocomplete('dropoff');

  function getBookingTypeLabel() {
    if (document.getElementById('type-test').checked) return 'Driving Test Package';
    if (document.getElementById('type-2hr').checked) return '2-Hour Lesson';
    return '1-Hour Lesson';
  }

  function getPriceAndDuration(type) {
    if (type === 'test_package') return { price: testPackagePrice, durationLabel: '2.5hr Test Package' };
    if (type === '2hr') return { price: lessonPrice * 2, durationLabel: '2 Hour' };
    return { price: lessonPrice, durationLabel: '1 Hour' };
  }

  function renderBookingsList() {
    var card = document.getElementById('bookings-list-card');
    var list = document.getElementById('bookings-list');
    if (orderItems.length === 0) {
      card.style.display = 'none';
      return;
    }
    card.style.display = 'block';
    list.innerHTML = orderItems.map(function(item, i) {
      return '<div class="d-flex justify-content-between align-items-center py-3 border-bottom">' +
        '<div><span class="d-block">' + (item.dateLabel || '') + '</span>' +
        '<span class="text-muted small">' + (item.timeLabel || '') + ', ' + (item.durationLabel || '') + '</span></div>' +
        '<div class="d-flex align-items-center gap-2">' +
        '<span class="fw-bold">$' + (item.price != null ? item.price.toFixed(2) : '0') + '</span>' +
        '<button type="button" class="btn btn-link btn-sm p-0 text-secondary" title="Edit" data-edit="' + i + '"><i class="bi bi-pencil"></i></button>' +
        '<button type="button" class="btn btn-link btn-sm p-0 text-danger" title="Delete" data-remove-booking="' + i + '"><i class="bi bi-trash"></i></button>' +
        '</div></div>';
    }).join('');
    list.querySelectorAll('[data-remove-booking]').forEach(function(btn) {
      btn.addEventListener('click', function() {
        orderItems.splice(parseInt(btn.getAttribute('data-remove-booking'), 10), 1);
        renderBookingsList();
        renderOrderSummary();
      });
    });
    list.querySelectorAll('[data-edit]').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var idx = parseInt(btn.getAttribute('data-edit'), 10);
        var item = orderItems[idx];
        if (!item) return;
        orderItems.splice(idx, 1);
        renderBookingsList();
        renderOrderSummary();
        document.getElementById('booking_date').value = item.dateLabel || '';
        document.getElementById('booking_date_iso').value = item.date_iso || '';
        document.getElementById('pickup_address').value = item.pickup_address || '';

        // Restore suburb autocomplete selection (new typeable flow)
        function restoreSuburb(prefix, suburbId, stateId, label) {
          document.getElementById(prefix + '_suburb').value = suburbId || '';
          document.getElementById(prefix + '_state').value = stateId || '';
          var display = document.getElementById(prefix + '_selected_display');
          var lbl = document.getElementById(prefix + '_selected_label');
          var input = document.getElementById(prefix + '_suburb_search');
          if (suburbId && label) {
            lbl.textContent = label;
            display.style.display = 'block';
            input.style.display = 'none';
          } else {
            display.style.display = 'none';
            input.style.display = '';
            input.value = '';
          }
        }
        restoreSuburb('pickup', item.pickup_suburb_id, item.pickup_state_id, item.pickup_label);
        if (item.booking_type === 'test_package') {
          document.getElementById('type-test').checked = true;
          restoreSuburb('dropoff', item.dropoff_suburb_id, item.dropoff_state_id, item.dropoff_label);
          document.getElementById('dropoff_address').value = item.dropoff_address || '';
        } else {
          document.getElementById('type-1hr').checked = item.booking_type === '1hr';
          document.getElementById('type-2hr').checked = item.booking_type === '2hr';
        }
        showHideTestPackage();
        if (item.date_iso) loadTimeSlots(item.date_iso);
      });
    });
  }

  function loadAvailableDates() {
    fetch('/api/instructors/' + instructorProfileId + '/availability/dates?days=60', opts)
      .then(function(r) { return r.json(); })
      .then(function(res) {
        var dates = res.data || [];
        var dropdown = document.getElementById('date-dropdown');
        dropdown.innerHTML = dates.length ? dates.map(function(d) {
          var dObj = new Date(d.date + 'T12:00:00');
          var days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
          var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
          var label = days[dObj.getDay()] + ', ' + dObj.getDate() + ' ' + months[dObj.getMonth()] + ' ' + dObj.getFullYear();
          return '<div class="px-3 py-2 border-bottom" style="cursor:pointer" data-date="' + d.date + '" data-label="' + label + '">' + label + '</div>';
        }).join('') : '<div class="px-3 py-2 text-muted">No dates available</div>';
        dropdown.style.display = 'block';
        dropdown.querySelectorAll('[data-date]').forEach(function(el) {
          el.addEventListener('click', function() {
            document.getElementById('booking_date').value = el.getAttribute('data-label');
            document.getElementById('booking_date_iso').value = el.getAttribute('data-date');
            dropdown.style.display = 'none';
            loadTimeSlots(el.getAttribute('data-date'));
          });
        });
      });
  }

  function loadTimeSlots(date) {
    var timeSelect = document.getElementById('booking_time');
    timeSelect.innerHTML = '<option value="">Loading…</option>';
    fetch('/api/instructors/' + instructorProfileId + '/availability/slots?date=' + encodeURIComponent(date), opts)
      .then(function(r) { return r.json(); })
      .then(function(res) {
        var slots = res.data || [];
        var dateStr = date;
        timeSelect.innerHTML = '<option value="">Select time</option>' + slots.map(function(s) {
          var dt = s.datetime || s.time;
          var t = (dt && dt.length >= 16) ? dt.substr(11, 5) : (s.time || '');
          var label = t;
          if (t && t.indexOf(':') !== -1) {
            var parts = t.split(':');
            var h = parseInt(parts[0], 10);
            var am = h < 12;
            if (h === 0) h = 12; else if (h > 12) h -= 12;
            label = h + ':' + parts[1] + (am ? ' am' : ' pm');
          }
          var val = dt || (dateStr + ' ' + t + ':00');
          return '<option value="' + val + '">' + label + '</option>';
        }).join('');
      });
  }

  document.getElementById('booking_date').addEventListener('focus', function() {
    if (!document.getElementById('booking_date_iso').value) loadAvailableDates();
    else document.getElementById('date-dropdown').style.display = 'block';
  });
  document.addEventListener('click', function(e) {
    if (!e.target.closest('#booking_date') && !e.target.closest('#date-dropdown')) {
      document.getElementById('date-dropdown').style.display = 'none';
    }
  });

  function renderOrderSummary() {
    var wrap = document.getElementById('order-items');
    var empty = document.getElementById('order-empty');
    var totals = document.getElementById('order-totals');
    if (orderItems.length === 0) {
      wrap.innerHTML = '';
      empty.style.display = 'block';
      totals.style.display = 'none';
      document.getElementById('btn-continue').style.opacity = '0.6';
      return;
    }
    empty.style.display = 'none';
    totals.style.display = 'block';
    document.getElementById('btn-continue').style.opacity = '1';
    var subtotal = 0;
    orderItems.forEach(function(item) { subtotal += item.price != null ? item.price : 0; });
    var fee = Math.round(subtotal * PLATFORM_FEE_PERCENT) / 100;
    var total = subtotal + fee;
    var instalment = total / 4;

    wrap.innerHTML = orderItems.map(function(item, i) {
      var shortDate = (item.dateLabel || '').replace(/^[^,]+, (\d+)/, '$1').trim();
    if (shortDate && item.date_iso) {
      var d = new Date(item.date_iso + 'T12:00:00');
      var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      shortDate = d.getDate() + ' ' + months[d.getMonth()];
    }
    var lineLabel = (item.booking_type === 'test_package' ? 'Test Package' : 'Lesson') + ' - ' + shortDate + ', ' + (item.timeLabel || '');
      return '<div class="d-flex justify-content-between align-items-start py-2 border-bottom">' +
        '<div class="small">' + lineLabel + '</div>' +
        '<div class="d-flex align-items-center gap-1"><span>$' + (item.price != null ? item.price.toFixed(2) : '0') + '</span>' +
        '<button type="button" class="btn btn-link btn-sm text-danger p-0" data-remove="' + i + '" title="Remove"><i class="bi bi-x"></i></button></div></div>';
    }).join('');
    document.getElementById('order-fee').textContent = '$' + fee.toFixed(2);
    document.getElementById('order-total').textContent = '$' + total.toFixed(2);
    document.getElementById('order-instalment').textContent = '$' + instalment.toFixed(2);

    wrap.querySelectorAll('[data-remove]').forEach(function(btn) {
      btn.addEventListener('click', function() {
        orderItems.splice(parseInt(btn.getAttribute('data-remove'), 10), 1);
        renderBookingsList();
        renderOrderSummary();
      });
    });
  }

  document.getElementById('btn-save-booking').addEventListener('click', function() {
    var dateIso = document.getElementById('booking_date_iso').value;
    var timeVal = document.getElementById('booking_time').value;
    var pickupAddr = document.getElementById('pickup_address').value.trim();
    var pickupSuburb = document.getElementById('pickup_suburb').value;
    var pickupState = document.getElementById('pickup_state').value;
    if (!dateIso || !timeVal) {
      alert('Please select date and time.');
      return;
    }
    if (!pickupSuburb || !pickupState) {
      alert('Please type and select a pickup suburb.');
      return;
    }
    var isTest = document.getElementById('type-test').checked;
    var dropoffSuburb = document.getElementById('dropoff_suburb').value;
    var dropoffState = document.getElementById('dropoff_state').value;
    if (isTest && (!dropoffSuburb || !dropoffState)) {
      alert('Please type and select a drop-off suburb for test package.');
      return;
    }
    var pickupLabel = document.getElementById('pickup_selected_label').textContent || '';
    var dropoffLabel = isTest ? (document.getElementById('dropoff_selected_label').textContent || '') : null;
    var type = isTest ? 'test_package' : (document.getElementById('type-2hr').checked ? '2hr' : '1hr');
    var timeLabel = document.getElementById('booking_time').selectedOptions[0] ? document.getElementById('booking_time').selectedOptions[0].text : timeVal;
    var pd = getPriceAndDuration(type);
    orderItems.push({
      booking_type: type,
      typeLabel: getBookingTypeLabel(),
      durationLabel: pd.durationLabel,
      price: pd.price,
      date_iso: dateIso,
      dateLabel: document.getElementById('booking_date').value,
      scheduled_at: timeVal,
      timeLabel: timeLabel,
      pickup_address: pickupAddr,
      pickup_suburb_id: pickupSuburb,
      pickup_state_id: pickupState,
      pickup_label: pickupLabel,
      pickupAddress: pickupAddr || pickupLabel,
      dropoff_address: isTest ? document.getElementById('dropoff_address').value.trim() : null,
      dropoff_suburb_id: isTest ? dropoffSuburb : null,
      dropoff_state_id: isTest ? dropoffState : null,
      dropoff_label: dropoffLabel,
    });
    renderBookingsList();
    renderOrderSummary();
    document.getElementById('btn-add-another').click();
    var newBookingCard = document.getElementById('new-booking-form-card');
    if (newBookingCard) newBookingCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  document.getElementById('btn-add-another').addEventListener('click', function() {
    document.getElementById('booking_date').value = '';
    document.getElementById('booking_date_iso').value = '';
    document.getElementById('booking_time').innerHTML = '<option value="">Select time</option>';
    document.getElementById('date-dropdown').style.display = 'none';
  });

  document.getElementById('btn-continue').addEventListener('click', function(e) {
    e.preventDefault();
    if (orderItems.length === 0) {
      alert('Add at least one booking before continuing.');
      return;
    }
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("learner.bookings.continue") }}';
    var csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrf || document.querySelector('meta[name="csrf-token"]').content;
    form.appendChild(csrfInput);
    var idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'instructor_profile_id';
    idInput.value = instructorProfileId;
    form.appendChild(idInput);
    var itemsInput = document.createElement('input');
    itemsInput.type = 'hidden';
    itemsInput.name = 'items';
    itemsInput.value = JSON.stringify(orderItems);
    form.appendChild(itemsInput);
    document.body.appendChild(form);
    form.submit();
  });

  renderOrderSummary();

  @if(!empty($googleMapsApiKey))
  function initPlaces() {
    if (typeof google === 'undefined' || !google.maps || !google.maps.places) return;
    var pickupInput = document.getElementById('pickup_address');
    var dropoffInput = document.getElementById('dropoff_address');
    function setupAutocomplete(input, prefix) {
      if (!input) return;
      var autocomplete = new google.maps.places.Autocomplete(input, { types: ['address'], componentRestrictions: { country: 'au' } });
      autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.address_components) return;
        var suburb = '', postcode = '', state = '';
        for (var i = 0; i < place.address_components.length; i++) {
          var c = place.address_components[i];
          if (c.types.indexOf('postal_code') !== -1) postcode = c.long_name;
          if (c.types.indexOf('administrative_area_level_1') !== -1) state = c.short_name;
          if (c.types.indexOf('locality') !== -1) suburb = c.long_name;
        }
        // Look up matching suburb in our DB via the API
        if (suburb || postcode) {
          var q = postcode || suburb;
          fetch('/api/suburbs/search?q=' + encodeURIComponent(q))
            .then(function(r) { return r.json(); })
            .then(function(res) {
              var items = res.data || [];
              // Prefer an exact match on suburb+postcode
              var match = items.find(function(i) {
                return (i.name || '').toLowerCase() === suburb.toLowerCase()
                  && (!postcode || i.postcode === postcode);
              }) || items[0];
              if (match) {
                document.getElementById(prefix + '_suburb').value = match.id;
                document.getElementById(prefix + '_state').value = STATES_MAP[match.state] || '';
                var lbl = match.label || (match.name + ', ' + match.postcode + ' ' + match.state);
                document.getElementById(prefix + '_selected_label').textContent = lbl;
                document.getElementById(prefix + '_selected_display').style.display = 'block';
                document.getElementById(prefix + '_suburb_search').style.display = 'none';
              }
            });
        }
      });
    }
    setupAutocomplete(pickupInput, 'pickup');
    setupAutocomplete(dropoffInput, 'dropoff');
  }
  if (typeof google !== 'undefined' && google.maps && google.maps.places) {
    initPlaces();
  } else {
    window.addEventListener('load', function() { setTimeout(initPlaces, 500); });
  }
  @endif
})();
</script>
@endpush
@endsection
