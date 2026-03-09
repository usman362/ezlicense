@extends('layouts.learner')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
    </ol>
</nav>

<h4 class="mb-4">Welcome back, <span id="welcome-name">{{ Auth::user()->name }}</span>!</h4>

<div class="row g-3 mb-4">
    {{-- My Instructor --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 class="fw-bold mb-0">My Instructor</h6>
                    <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-sm" id="instructor-book-now-btn">Book Now</a>
                </div>
                <div id="instructor-content">
                    <div id="instructor-loading" class="text-muted small">Loading…</div>
                    <div id="instructor-loaded" style="display: none;">
                        <h6 class="text-muted small mb-2">Instructor</h6>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.25rem;" id="instructor-avatar">—</div>
                            <div>
                                <span id="instructor-name"></span><br>
                                <a href="#" id="instructor-phone" class="small"></a><br>
                                <span class="small text-muted" id="instructor-rate"></span>
                            </div>
                        </div>
                        <h6 class="text-muted small mb-2 mt-3">Vehicle</h6>
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-light rounded d-flex align-items-center justify-content-center p-2" style="width: 64px; height: 48px;"><i class="bi bi-car-front text-muted"></i></div>
                            <div class="small">
                                <span id="instructor-vehicle"></span><br>
                                <span class="text-muted" id="instructor-vehicle-details">5-star ANCAP rating · Dual controls fitted</span>
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <a href="{{ route('find-instructor') }}" class="small">Switch Instructor &gt;</a>
                        </div>
                    </div>
                    <div id="instructor-empty" class="text-muted small" style="display: none;">You don't have an instructor yet. <a href="{{ route('find-instructor') }}">Find an instructor</a> to get started.</div>
                </div>
            </div>
        </div>
    </div>
    {{-- My Wallet --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold mb-0">My Wallet</h6>
                    <a href="{{ route('learner.wallet.add-credit') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-credit-card me-1"></i> Add Credit
                    </a>
                </div>
                <p class="mb-1 fs-4 fw-bold" id="wallet-balance">$0</p>
                <p class="mb-2 small text-muted">Includes <span id="wallet-non-refundable">$0.00</span> of non refundable credit.</p>
                <a href="{{ route('learner.wallet') }}" class="small">View details &gt;</a>
            </div>
        </div>
    </div>
</div>

{{-- Driving Test Package (dynamic from my instructor) --}}
<div class="card border-0 shadow-sm mb-4" id="test-package-card" style="display:none;">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <h6 class="fw-bold mb-0">Driving Test Package</h6>
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold" id="test-package-price">$0.00</span>
                <a href="#" class="btn btn-warning btn-sm" id="test-package-book-btn">Book Now</a>
            </div>
        </div>
        <ul class="list-unstyled mb-0 small">
            <li class="mb-1"><span class="text-danger me-1">P</span> 2.5hr Test Package</li>
            <li class="mb-1"><i class="bi bi-clock me-1 text-muted"></i> Use instructor's vehicle for test</li>
            <li class="mb-1"><i class="bi bi-geo-alt me-1 text-muted"></i> Pick up &amp; Drop off included</li>
            <li class="mb-0"><i class="bi bi-check2-square me-1 text-muted"></i> 45 minute pre-test warm up lesson</li>
        </ul>
    </div>
</div>

{{-- Bookings --}}
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Bookings</h6>
        <ul class="nav nav-tabs border-0 small mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-upcoming" data-bs-toggle="tab" data-bs-target="#panel-upcoming" type="button" role="tab">Upcoming</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-services" data-bs-toggle="tab" data-bs-target="#panel-services" type="button" role="tab">Services</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-history" data-bs-toggle="tab" data-bs-target="#panel-history" type="button" role="tab">History</button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="panel-upcoming" role="tabpanel">
                <div id="upcoming-loading" class="text-muted small py-3">Loading…</div>
                <div id="upcoming-list" style="display: none;"></div>
                <div id="upcoming-empty" class="text-muted small py-3" style="display: none;">You have no upcoming bookings to view at this time.</div>
            </div>
            <div class="tab-pane fade" id="panel-services" role="tabpanel">
                <div id="services-loading" class="text-muted small py-3">Loading…</div>
                <div id="services-cards" class="row g-3" style="display: none;"></div>
                <div id="services-empty" class="text-muted small py-3" style="display: none;">You don't have an instructor yet. <a href="{{ route('find-instructor') }}">Find an instructor</a> to book lessons and packages.</div>
            </div>
            <div class="tab-pane fade" id="panel-history" role="tabpanel">
                <div id="history-loading" class="text-muted small py-3">Loading…</div>
                <div id="history-wrap" style="display: none;">
                    <h6 class="fw-bold mb-2">Booking History</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Booking</th>
                                    <th>Instructor</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                </tr>
                            </thead>
                            <tbody id="history-tbody"></tbody>
                        </table>
                    </div>
                    <nav id="history-pagination" class="mt-2" aria-label="History pagination"></nav>
                </div>
                <div id="history-empty" class="text-muted small py-3" style="display: none;">You have no booking history.</div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-tabs .nav-link { color: #333; }
.nav-tabs .nav-link.active { border-bottom: 2px solid #f0ad4e; font-weight: 500; color: #333; }
</style>
@push('scripts')
<script>
window.learnerBookingNewUrl = "{{ route('learner.bookings.new') }}";
</script>
<script>
(function() {
  var csrf = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').content;
  var opts = { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf || '', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' };
  var bookingNewUrl = window.learnerBookingNewUrl || '';

  function esc(s) {
    if (s == null || s === '') return '—';
    var d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
  }

  function formatDate(iso) {
    if (!iso) return '—';
    var d = new Date(iso);
    var days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return days[d.getDay()] + ', ' + ('0' + d.getDate()).slice(-2) + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
  }

  function formatTime(iso, durationMin) {
    if (!iso) return '—';
    var d = new Date(iso);
    var end = new Date(d.getTime() + (durationMin || 60) * 60000);
    var fmt = function(t) {
      var h = t.getHours(); var m = t.getMinutes();
      var am = h < 12; h = h % 12; if (h === 0) h = 12;
      return h + ':' + ('0' + m).slice(-2) + (am ? ' am' : ' pm');
    };
    return fmt(d) + ' - ' + fmt(end);
  }

  fetch('/api/learner/dashboard', opts)
    .then(function(r) { return r.json(); })
    .then(function(res) {
      var data = res.data || {};
      var instructor = data.my_instructor;
      var wallet = data.wallet || {};
      var upcoming = data.upcoming_bookings || [];

      // Welcome name already in DOM
      var welcomeEl = document.getElementById('welcome-name');
      if (welcomeEl && !welcomeEl.textContent) welcomeEl.textContent = 'there';

      // My Instructor
      document.getElementById('instructor-loading').style.display = 'none';
      if (instructor) {
        document.getElementById('instructor-empty').style.display = 'none';
        document.getElementById('instructor-loaded').style.display = 'block';
        var initial = (instructor.name || '').trim().split(/\s+/).map(function(s) { return s.charAt(0); }).join('').slice(0, 2).toUpperCase() || '?';
        document.getElementById('instructor-avatar').textContent = initial;
        document.getElementById('instructor-name').textContent = instructor.name || '—';
        var phoneEl = document.getElementById('instructor-phone');
        phoneEl.textContent = instructor.phone || '—';
        phoneEl.href = instructor.phone ? ('tel:' + instructor.phone.replace(/\s/g, '')) : '#';
        document.getElementById('instructor-rate').textContent = instructor.rate || '';
        document.getElementById('instructor-vehicle').textContent = instructor.vehicle || '—';
        if (instructor.instructor_profile_id) {
          document.getElementById('instructor-book-now-btn').href = bookingNewUrl ? (bookingNewUrl + '?instructor_profile_id=' + instructor.instructor_profile_id) : ('/instructors/' + instructor.instructor_profile_id);
        }
      } else {
        document.getElementById('instructor-loaded').style.display = 'none';
        document.getElementById('instructor-empty').style.display = 'block';
      }

      // Wallet
      document.getElementById('wallet-balance').textContent = wallet.balance_display || '$0.00';
      document.getElementById('wallet-non-refundable').textContent = wallet.non_refundable_credit_display || '$0.00';

      // Upcoming bookings
      document.getElementById('upcoming-loading').style.display = 'none';
      if (upcoming.length === 0) {
        document.getElementById('upcoming-list').style.display = 'none';
        document.getElementById('upcoming-empty').style.display = 'block';
      } else {
        document.getElementById('upcoming-empty').style.display = 'none';
        document.getElementById('upcoming-list').style.display = 'block';
        document.getElementById('upcoming-list').innerHTML = '<ul class="list-unstyled mb-0">' + upcoming.map(function(b) {
          var typeLabel = (b.type === 'test_package') ? 'Test Package' : ((b.duration_minutes || 60) / 60) + ' hr Lesson';
          return '<li class="border-bottom py-2 small">' +
            '<strong>' + formatDate(b.scheduled_at) + '</strong> ' + formatTime(b.scheduled_at, b.duration_minutes) + '<br>' +
            (b.instructor_name ? esc(b.instructor_name) + ' · ' : '') + typeLabel + (b.location ? ' · ' + esc(b.location) : '') +
            '</li>';
        }).join('') + '</ul>';
      }

      // Services tab: build cards from my_instructor
      document.getElementById('services-loading').style.display = 'none';
      if (instructor && instructor.instructor_profile_id) {
        document.getElementById('services-empty').style.display = 'none';
        document.getElementById('services-cards').style.display = 'flex';
        var price1 = instructor.lesson_price != null ? Math.round(instructor.lesson_price) : 65;
        var price2 = price1 * 2;
        var priceTest = instructor.test_package_price != null ? Math.round(instructor.test_package_price) : 225;
        var trans = instructor.transmission || 'Auto';
        var bookUrl = bookingNewUrl ? (bookingNewUrl + '?instructor_profile_id=' + instructor.instructor_profile_id) : ('/instructors/' + instructor.instructor_profile_id);
        var name = esc(instructor.name || 'Instructor');
        var cards = [
          { title: '1 hour Driving Lesson', price: price1, popular: false, icon: 'bi-emoji-sunglasses' },
          { title: '2 hour Driving Lesson', price: price2, popular: true, icon: 'bi-people' },
          { title: '2.5 hours Test Package', price: priceTest, popular: false, icon: 'bi-clipboard-check' }
        ];
        document.getElementById('services-cards').innerHTML = cards.map(function(c) {
          return '<div class="col-md-4">' +
            '<div class="card border-0 shadow-sm h-100">' +
              '<div class="card-body d-flex flex-column">' +
                (c.popular ? '<span class="badge bg-danger text-white align-self-start mb-2">MOST POPULAR</span>' : '') +
                '<h6 class="fw-bold">' + c.title + '</h6>' +
                '<p class="text-muted small mb-1">Price $' + c.price + '</p>' +
                '<p class="text-muted small mb-3">Transmission ' + trans + '</p>' +
                '<div class="mb-3 flex-grow-1 d-flex align-items-center justify-content-center"><i class="bi ' + c.icon + ' display-4 text-muted"></i></div>' +
                '<a href="' + bookUrl + '" class="btn btn-warning btn-sm">Book with ' + name + '</a>' +
              '</div></div></div>';
        }).join('');

        // Driving Test Package card
        var tpCard = document.getElementById('test-package-card');
        var tpPriceEl = document.getElementById('test-package-price');
        var tpBookBtn = document.getElementById('test-package-book-btn');
        if (tpCard && tpPriceEl && tpBookBtn && instructor.offers_test_package && priceTest) {
          tpPriceEl.textContent = '$' + priceTest.toFixed(2);
          tpBookBtn.href = bookUrl;
          tpCard.style.display = '';
        } else if (tpCard) {
          tpCard.style.display = 'none';
        }
      } else {
        document.getElementById('services-cards').style.display = 'none';
        document.getElementById('services-empty').style.display = 'block';
        var tpCardEmpty = document.getElementById('test-package-card');
        if (tpCardEmpty) tpCardEmpty.style.display = 'none';
      }
    })
    .catch(function() {
      document.getElementById('instructor-loading').style.display = 'none';
      document.getElementById('instructor-empty').style.display = 'block';
      document.getElementById('instructor-empty').innerHTML = 'Unable to load. <a href="{{ route('find-instructor') }}">Find an instructor</a>.';
      document.getElementById('upcoming-loading').style.display = 'none';
      document.getElementById('upcoming-empty').style.display = 'block';
      document.getElementById('upcoming-empty').textContent = 'Unable to load upcoming bookings.';
    });

  // History tab: load when shown
  function loadHistory(page) {
    page = page || 1;
    document.getElementById('history-loading').style.display = 'block';
    document.getElementById('history-wrap').style.display = 'none';
    document.getElementById('history-empty').style.display = 'none';
    fetch('/api/bookings?tab=history&page=' + page, opts)
      .then(function(r) { return r.json(); })
      .then(function(data) {
        document.getElementById('history-loading').style.display = 'none';
        var items = data.data || [];
        if (items.length === 0) {
          document.getElementById('history-empty').style.display = 'block';
          document.getElementById('history-wrap').style.display = 'none';
          return;
        }
        document.getElementById('history-empty').style.display = 'none';
        document.getElementById('history-wrap').style.display = 'block';
        var tbody = document.getElementById('history-tbody');
        tbody.innerHTML = items.map(function(b) {
          var status = (b.status === 'cancelled') ? 'CANCELLED' : 'COMPLETED';
          var statusClass = (b.status === 'cancelled') ? 'bg-danger text-white' : 'bg-success text-white';
          var pay = (b.payment_status === 'RETURNED') ? 'RETURNED' : (b.payment_status === 'PROCESSED' ? 'PROCESSED' : '—');
          var payClass = (b.payment_status === 'RETURNED') ? 'bg-primary text-white' : 'bg-success text-white';
          var loc = (b.suburb && b.suburb.location) ? b.suburb.location : (b.suburb ? (b.suburb.name + ' ' + (b.suburb.postcode || '')) : '—');
          var instrName = (b.instructor && b.instructor.name) ? esc(b.instructor.name) : '—';
          var initial = instrName !== '—' ? instrName.charAt(0) : '?';
          return '<tr>' +
            '<td>#' + b.id + '</td>' +
            '<td><span class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center me-1" style="width:24px;height:24px;font-size:0.7rem;">' + initial + '</span>' + instrName + '</td>' +
            '<td>' + formatDate(b.scheduled_at) + '</td>' +
            '<td>' + formatTime(b.scheduled_at, b.duration_minutes) + '</td>' +
            '<td>' + esc(loc) + '</td>' +
            '<td><span class="badge ' + statusClass + '">' + status + '</span></td>' +
            '<td>' + (pay !== '—' ? '<span class="badge ' + payClass + '">' + pay + '</span>' : '—') + '</td>' +
          '</tr>';
        }).join('');
        var pagination = document.getElementById('history-pagination');
        if (data.last_page > 1) {
          var cur = data.current_page || 1;
          var last = data.last_page;
          var parts = [];
          if (cur > 1) parts.push('<li class="page-item"><a class="page-link" href="#" data-page="' + (cur - 1) + '">Prev</a></li>');
          for (var i = 1; i <= Math.min(last, 5); i++) {
            if (i === cur) parts.push('<li class="page-item active"><span class="page-link">' + i + '</span></li>');
            else parts.push('<li class="page-item"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>');
          }
          if (cur < last) parts.push('<li class="page-item"><a class="page-link" href="#" data-page="' + (cur + 1) + '">Next</a></li>');
          pagination.innerHTML = '<ul class="pagination pagination-sm mb-0">' + parts.join('') + '</ul>';
          pagination.querySelectorAll('a[data-page]').forEach(function(a) {
            a.addEventListener('click', function(e) { e.preventDefault(); loadHistory(parseInt(a.getAttribute('data-page'), 10)); });
          });
        } else {
          pagination.innerHTML = '';
        }
      })
      .catch(function() {
        document.getElementById('history-loading').style.display = 'none';
        document.getElementById('history-empty').textContent = 'Unable to load booking history.';
        document.getElementById('history-empty').style.display = 'block';
      });
  }
  document.getElementById('tab-history').addEventListener('shown.bs.tab', function() { loadHistory(1); });
})();
</script>
@endpush
@endsection
