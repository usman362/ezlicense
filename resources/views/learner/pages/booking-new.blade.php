@extends('layouts.learner')

@section('title', 'Make a Booking')
@section('heading', 'Make a Booking')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Make a Booking</li>
    </ol>
</nav>

<h5 class="mb-4">Make a Booking</h5>

<div class="row">
    <div class="col-lg-8">
        {{-- My Instructor --}}
        @php
            $instructor = $instructorProfile->user;
            $profile = $instructorProfile;
            $rate = $profile->lesson_price !== null ? '$' . number_format((float) $profile->lesson_price, 0) . '/hr' : null;
            $vehicle = trim(implode(' ', array_filter([$profile->vehicle_make, $profile->vehicle_model, $profile->vehicle_year])));
        @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">My Instructor</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.1rem;">{{ strtoupper(substr($instructor->name ?? 'H', 0, 1)) }}</div>
                            <div>
                                <strong>{{ $instructor->name }}</strong><br>
                                <a href="tel:{{ $instructor->phone }}">{{ $instructor->phone }}</a><br>
                                @if($rate)<span class="small text-muted">{{ $rate }}</span>@endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-light rounded p-2"><i class="bi bi-car-front text-muted fs-4"></i></div>
                            <div class="small">
                                {{ $vehicle ?: 'Vehicle' }} ({{ ucfirst($profile->transmission ?? 'Auto') }})<br>
                                <span class="text-muted">5-star ANCAP rating · Dual controls fitted</span>
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
                        <input type="text" class="form-control" id="pickup_address" placeholder="Enter a location">
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">* Suburb</label>
                            <select class="form-select form-select-sm" id="pickup_suburb">
                                <option value="">Select suburb</option>
                                @foreach($states as $state)
                                    @foreach($suburbsByState[$state->id] ?? [] as $sub)
                                        <option value="{{ $sub['id'] }}" data-state="{{ $state->id }}">{{ $sub['name'] }}, {{ $sub['postcode'] }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">* State</label>
                            <select class="form-select form-select-sm" id="pickup_state">
                                <option value="">Select state</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div id="dropoff-section" class="border rounded p-3 mb-3" style="display: none;">
                    <h6 class="small fw-bold mb-2">Drop Off Location</h6>
                    <div class="mb-2">
                        <label class="form-label small">Drop off address</label>
                        <input type="text" class="form-control" id="dropoff_address" placeholder="Enter a location">
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">* Suburb</label>
                            <select class="form-select form-select-sm" id="dropoff_suburb">
                                <option value="">Select suburb</option>
                                @foreach($states as $state)
                                    @foreach($suburbsByState[$state->id] ?? [] as $sub)
                                        <option value="{{ $sub['id'] }}" data-state="{{ $state->id }}">{{ $sub['name'] }}, {{ $sub['postcode'] }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">* State</label>
                            <select class="form-select form-select-sm" id="dropoff_state">
                                <option value="">Select state</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
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

  function syncStateFromSuburb(suburbSelect, stateSelect) {
    var opt = suburbSelect.selectedOptions[0];
    if (opt && opt.getAttribute('data-state')) stateSelect.value = opt.getAttribute('data-state');
  }
  document.getElementById('pickup_suburb').addEventListener('change', function() { syncStateFromSuburb(this, document.getElementById('pickup_state')); });
  document.getElementById('dropoff_suburb').addEventListener('change', function() { syncStateFromSuburb(this, document.getElementById('dropoff_state')); });

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
        document.getElementById('pickup_suburb').value = item.pickup_suburb_id || '';
        document.getElementById('pickup_state').value = item.pickup_state_id || '';
        if (item.booking_type === 'test_package') {
          document.getElementById('type-test').checked = true;
          document.getElementById('dropoff_suburb').value = item.dropoff_suburb_id || '';
          document.getElementById('dropoff_state').value = item.dropoff_state_id || '';
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
      alert('Please select pick up suburb and state.');
      return;
    }
    var isTest = document.getElementById('type-test').checked;
    var dropoffSuburb = document.getElementById('dropoff_suburb').value;
    var dropoffState = document.getElementById('dropoff_state').value;
    if (isTest && (!dropoffSuburb || !dropoffState)) {
      alert('Please select drop off suburb and state for test package.');
      return;
    }
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
      pickupAddress: pickupAddr || document.getElementById('pickup_suburb').selectedOptions[0]?.text,
      dropoff_address: isTest ? document.getElementById('dropoff_address').value.trim() : null,
      dropoff_suburb_id: isTest ? dropoffSuburb : null,
      dropoff_state_id: isTest ? dropoffState : null,
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
    var pickupSuburb = document.getElementById('pickup_suburb');
    var pickupState = document.getElementById('pickup_state');
    var dropoffSuburb = document.getElementById('dropoff_suburb');
    var dropoffState = document.getElementById('dropoff_state');
    function setupAutocomplete(input, suburbSelect, stateSelect) {
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
        if (suburbSelect && stateSelect) {
          for (var j = 0; j < suburbSelect.options.length; j++) {
            var opt = suburbSelect.options[j];
            if (opt.value && opt.text.indexOf(suburb) !== -1 && (postcode === '' || opt.text.indexOf(postcode) !== -1)) {
              suburbSelect.value = opt.value;
              if (opt.getAttribute('data-state')) stateSelect.value = opt.getAttribute('data-state');
              break;
            }
          }
        }
      });
    }
    setupAutocomplete(pickupInput, pickupSuburb, pickupState);
    setupAutocomplete(dropoffInput, dropoffSuburb, dropoffState);
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
