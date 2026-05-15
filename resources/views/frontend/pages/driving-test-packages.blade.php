@extends('layouts.frontend')
@section('title', 'Driving Test & Driving Lesson Packages')

@section('content')
{{-- ── Dark hero banner with title + illustration ── --}}
<section class="dtp-hero">
    <div class="container dtp-hero-inner">
        <div class="row align-items-center g-4">
            <div class="col-12 text-center text-lg-start col-lg-7">
                <span class="dtp-hero-badge"><i class="bi bi-award-fill me-1"></i>2.5-hour test-day package</span>
                <h1 class="dtp-hero-title">Driving Test &amp; Driving Lesson Packages</h1>
                <p class="dtp-hero-sub">Ready for your driving test? Get the warm-up lesson, the car and the pick-up &amp; drop-off — all in one booking.</p>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-center">
                {{-- Polished SVG illustration: STOP sign + cone + L plate floating with shadow --}}
                <svg viewBox="0 0 380 280" xmlns="http://www.w3.org/2000/svg" class="dtp-hero-art" aria-hidden="true">
                    <defs>
                        <filter id="dtpShadow" x="-20%" y="-20%" width="140%" height="140%">
                            <feDropShadow dx="0" dy="6" stdDeviation="6" flood-opacity="0.25"/>
                        </filter>
                    </defs>
                    {{-- STOP sign (octagon) on top right --}}
                    <g filter="url(#dtpShadow)" transform="translate(220,40) rotate(12)">
                        <polygon points="35,0 80,0 115,35 115,80 80,115 35,115 0,80 0,35" fill="#dc2626" stroke="#fff" stroke-width="4"/>
                        <text x="57.5" y="68" font-family="Arial, sans-serif" font-size="22" font-weight="900" fill="#fff" text-anchor="middle">STOP</text>
                    </g>
                    {{-- Cone on bottom left --}}
                    <g filter="url(#dtpShadow)" transform="translate(20,140)">
                        <polygon points="60,0 100,140 20,140" fill="#f59e0b" stroke="#fff" stroke-width="2"/>
                        <rect x="32" y="65" width="56" height="10" fill="#fff" rx="2"/>
                        <rect x="26" y="95" width="68" height="10" fill="#fff" rx="2"/>
                        <ellipse cx="60" cy="148" rx="48" ry="8" fill="#000" opacity="0.15"/>
                    </g>
                    {{-- L-plate centered --}}
                    <g filter="url(#dtpShadow)" transform="translate(140,150) rotate(-10)">
                        <rect width="90" height="90" rx="10" fill="#fbbf24" stroke="#fff" stroke-width="3"/>
                        <text x="45" y="72" font-family="Arial, sans-serif" font-size="68" font-weight="900" fill="#dc2626" text-anchor="middle">L</text>
                    </g>
                </svg>
            </div>
        </div>
    </div>
</section>

{{-- ── Main content ── --}}
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                {{-- The Ultimate Driving Test Package CARD --}}
                <div class="dtp-package-card">
                    <div class="dtp-package-header">
                        <h2 class="dtp-section-title mb-1">The Ultimate Driving Test Package</h2>
                        <p class="text-muted mb-1 fw-semibold">Don't leave your test to chance!</p>
                        <p class="text-muted mb-0">Our Driving Test Package gives you everything you need for a smooth, stress-free test day:</p>
                    </div>

                    <div class="row g-4 align-items-center dtp-package-body">
                        <div class="col-md-4 text-center">
                            <div class="dtp-pplate">
                                <span>P</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <ul class="dtp-includes">
                                <li>
                                    <span class="dtp-include-icon dtp-icon-orange"><i class="bi bi-clock-fill"></i></span>
                                    <span><strong>45-minute pre-test warm-up</strong><br><span class="text-muted small">shake off nerves &amp; sharpen your skills</span></span>
                                </li>
                                <li>
                                    <span class="dtp-include-icon dtp-icon-blue"><i class="bi bi-car-front-fill"></i></span>
                                    <span><strong>Use of Instructor's car for your test</strong><br><span class="text-muted small">fully insured &amp; test-ready</span></span>
                                </li>
                                <li>
                                    <span class="dtp-include-icon dtp-icon-green"><i class="bi bi-geo-alt-fill"></i></span>
                                    <span><strong>Pick-up &amp; drop-off included</strong><br><span class="text-muted small">get to and from your test with zero hassle</span></span>
                                </li>
                                <li>
                                    <span class="dtp-include-icon dtp-icon-purple"><i class="bi bi-stopwatch-fill"></i></span>
                                    <span><strong>2.5-hour package</strong><br><span class="text-muted small">full support from start to finish</span></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="dtp-package-cta">
                        <i class="bi bi-rocket-takeoff-fill"></i>
                        Drive in prepared, drive out licensed. <strong>Book your test package today!</strong>
                    </div>
                </div>

                {{-- Disclaimer --}}
                <div class="dtp-disclaimer text-center mb-5">
                    <i class="bi bi-info-circle me-1"></i>
                    Our test package books the instructor &amp; vehicle only. You must book your own driving test with your local roads authority.<br>
                    <strong>Test package not available in ACT, SA and TAS.</strong>
                </div>

                {{-- Get started toggle --}}
                <h3 class="text-center fw-bolder mb-4 mt-5 dtp-section-title">Get started by choosing your preferred package:</h3>
                <div class="dtp-options">
                    <button type="button" class="dtp-option dtp-option-with active" data-option="with">
                        <div class="dtp-option-icon">
                            <i class="bi bi-person-badge-fill"></i>
                            <span class="dtp-option-plus">+</span>
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="dtp-option-label">
                            <strong>Driving test package</strong>
                            <span>with driving lessons</span>
                        </div>
                    </button>
                    <button type="button" class="dtp-option dtp-option-standalone" data-option="standalone">
                        <div class="dtp-option-icon">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <div class="dtp-option-label">
                            <strong>Stand alone</strong>
                            <span>driving test</span>
                        </div>
                    </button>
                </div>

                {{-- Search panel — "with driving lessons" (default) --}}
                <div class="dtp-search-panel" id="panel-with">
                    <p class="text-center text-muted mb-3">Please select your pickup suburb &amp; transmission type. You can then review our instructors in your area &amp; book online.</p>
                    <form action="{{ route('find-instructor.results') }}" method="get" class="dtp-search-form">
                        <input type="hidden" name="suburb_id" id="dtp-with-suburb-id">
                        <input type="hidden" name="q" id="dtp-with-q">
                        <input type="hidden" name="test_pre_booked" value="0">
                        <div class="dtp-form-row">
                            <div class="btn-group dtp-trans-toggle" role="group">
                                <input type="radio" class="btn-check" name="transmission" id="dtp-with-auto" value="auto" checked>
                                <label class="btn" for="dtp-with-auto"><i class="bi bi-check-lg me-1"></i>Auto</label>
                                <input type="radio" class="btn-check" name="transmission" id="dtp-with-manual" value="manual">
                                <label class="btn" for="dtp-with-manual">Manual</label>
                            </div>
                            <input type="text" id="dtp-with-suburb" class="form-control dtp-suburb-input" placeholder="Enter your suburb" autocomplete="off" data-list-id="dtp-with-list">
                            <button type="submit" class="btn btn-warning fw-bold dtp-search-btn"><i class="bi bi-search me-1"></i>Search</button>
                        </div>
                    </form>
                </div>

                {{-- Search panel — "stand alone driving test" --}}
                <div class="dtp-search-panel" id="panel-standalone" hidden>
                    <p class="dtp-step-label">1. Please enter your pickup suburb &amp; transmission type into the search tool below.</p>
                    <form action="{{ route('find-instructor.results') }}" method="get" class="dtp-search-form" id="dtp-standalone-form">
                        <input type="hidden" name="suburb_id" id="dtp-sa-suburb-id">
                        <input type="hidden" name="q" id="dtp-sa-q">
                        <input type="hidden" name="test_pre_booked" value="1">
                        <div class="dtp-form-row">
                            <div class="btn-group dtp-trans-toggle" role="group">
                                <input type="radio" class="btn-check" name="transmission" id="dtp-sa-auto" value="auto" checked>
                                <label class="btn" for="dtp-sa-auto"><i class="bi bi-check-lg me-1"></i>Auto</label>
                                <input type="radio" class="btn-check" name="transmission" id="dtp-sa-manual" value="manual">
                                <label class="btn" for="dtp-sa-manual">Manual</label>
                            </div>
                            <input type="text" id="dtp-sa-suburb" class="form-control dtp-suburb-input" placeholder="Enter your suburb" autocomplete="off" data-list-id="dtp-sa-list">
                            <button type="submit" class="btn btn-warning fw-bold dtp-search-btn"><i class="bi bi-search me-1"></i>Search</button>
                        </div>
                    </form>

                    <p class="dtp-step-label mt-4">2. Please select your test centre below, if you haven't booked your driving test please select 'Any test location'.</p>
                    <select class="form-select dtp-test-centre" id="dtp-test-centre">
                        <option value="">Any test location</option>
                        @php
                            $defaultCentres = [
                                ['code' => 'bankstown', 'name' => 'Bankstown Service Centre - Bankstown Central, Shop F'],
                                ['code' => 'parramatta', 'name' => 'Parramatta Service Centre'],
                                ['code' => 'rockdale', 'name' => 'Rockdale Service Centre'],
                                ['code' => 'auburn', 'name' => 'Auburn Service Centre'],
                                ['code' => 'blacktown', 'name' => 'Blacktown Service Centre'],
                                ['code' => 'liverpool', 'name' => 'Liverpool Service Centre'],
                                ['code' => 'campsie', 'name' => 'Campsie Service Centre'],
                            ];
                        @endphp
                        @foreach($defaultCentres as $c)
                            <option value="{{ $c['code'] }}">{{ $c['name'] }}</option>
                        @endforeach
                    </select>

                    <p class="dtp-note">Please note that only a limited number of our instructors offer stand alone driving test packages. If you are able to complete at least one ordinary driving lesson first you will gain access to greater availability.</p>
                </div>

                {{-- "Pass with confidence" with feature tiles --}}
                <div class="dtp-feature-block">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-7">
                            <span class="dtp-eyebrow"><i class="bi bi-stars me-1"></i>Test Day Ready</span>
                            <h3 class="dtp-section-title mb-3">Pass with confidence — everything you need in one package.</h3>
                            <p class="text-muted mb-3">Feel confident on test day with a 45-minute pre-test warm-up to shake off nerves and sharpen your skills, plus the use of your instructor's fully insured, test-ready car.</p>
                            <div class="dtp-trust-row">
                                <span><i class="bi bi-shield-fill-check text-success"></i> Fully insured</span>
                                <span><i class="bi bi-car-front-fill text-primary"></i> Dual controls</span>
                                <span><i class="bi bi-patch-check-fill text-warning"></i> Verified instructors</span>
                            </div>
                        </div>
                        <div class="col-md-5 text-center">
                            <div class="dtp-illustration-wrap dtp-illustration-confident">
                                <i class="bi bi-trophy-fill dtp-illu-main"></i>
                                <span class="dtp-illu-badge dtp-illu-badge-top"><i class="bi bi-check-lg"></i></span>
                                <span class="dtp-illu-badge dtp-illu-badge-bottom"><i class="bi bi-stars"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- "Not test-ready yet?" --}}
                <div class="dtp-feature-block dtp-feature-block-alt">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-5 text-center order-md-1">
                            <div class="dtp-illustration-wrap dtp-illustration-car">
                                <i class="bi bi-car-front-fill dtp-illu-main"></i>
                                <span class="dtp-illu-badge dtp-illu-badge-top"><i class="bi bi-mortarboard-fill"></i></span>
                                <span class="dtp-illu-badge dtp-illu-badge-bottom"><i class="bi bi-arrow-up-right"></i></span>
                            </div>
                        </div>
                        <div class="col-md-7 order-md-2">
                            <span class="dtp-eyebrow"><i class="bi bi-mortarboard me-1"></i>Build Your Skills</span>
                            <h3 class="dtp-section-title mb-3">Not test-ready yet? We've got you covered!</h3>
                            <p class="text-muted mb-2">Build your confidence and skills with driving lesson packages designed to help you pass. With Secure Licences, you can learn in the same instructor's car you'll use for your test — so there are no surprises on the big day.</p>
                            <p class="text-muted mb-0">Enter your suburb below to view our driving lessons packages now.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── Yellow CTA strip: Driving lesson packages & pricing ── --}}
<section class="dtp-cta-strip">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-3">
                    <div class="dtp-cta-icon"><i class="bi bi-currency-dollar"></i></div>
                    <div>
                        <h4 class="mb-0 fw-bolder">Driving lesson packages &amp; pricing</h4>
                        <small class="text-dark">Buy more lessons &amp; get more discount</small>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <label class="fw-bold mb-2"><i class="bi bi-search me-1"></i>Check suburb pricing</label>
                <form action="{{ route('find-instructor.results') }}" method="get" class="dtp-cta-form">
                    <input type="hidden" name="suburb_id" id="dtp-cta-suburb-id">
                    <input type="hidden" name="q" id="dtp-cta-q">
                    <div class="btn-group dtp-trans-toggle" role="group">
                        <input type="radio" class="btn-check" name="transmission" id="dtp-cta-auto" value="auto" checked>
                        <label class="btn" for="dtp-cta-auto"><i class="bi bi-check-lg me-1"></i>Auto</label>
                        <input type="radio" class="btn-check" name="transmission" id="dtp-cta-manual" value="manual">
                        <label class="btn" for="dtp-cta-manual">Manual</label>
                    </div>
                    <input type="text" id="dtp-cta-suburb" class="form-control" placeholder="Enter your suburb" autocomplete="off" data-list-id="dtp-cta-list">
                    <button type="submit" class="btn btn-light fw-bold"><i class="bi bi-search me-1"></i>Search</button>
                </form>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function() {
    // Toggle between "with driving lessons" and "stand alone"
    document.querySelectorAll('.dtp-option').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var target = btn.dataset.option;
            document.querySelectorAll('.dtp-option').forEach(function(b) { b.classList.toggle('active', b === btn); });
            document.getElementById('panel-with').hidden = (target !== 'with');
            document.getElementById('panel-standalone').hidden = (target !== 'standalone');
        });
    });

    // Suburb autocomplete (uses our existing /api/suburbs/search endpoint)
    function attachSuburbAutocomplete(inputId, hiddenIdSuburbId, hiddenIdQ) {
        var input = document.getElementById(inputId);
        if (!input) return;
        var list = document.createElement('div');
        list.className = 'dtp-suburb-list';
        list.hidden = true;
        input.parentNode.style.position = 'relative';
        input.parentNode.appendChild(list);
        var debounce;
        input.addEventListener('input', function() {
            clearTimeout(debounce);
            var q = input.value.trim();
            if (q.length < 2) { list.hidden = true; return; }
            debounce = setTimeout(function() {
                fetch('/api/suburbs/search?q=' + encodeURIComponent(q), { credentials: 'same-origin' })
                  .then(function(r) { return r.json(); })
                  .then(function(res) {
                      var items = res.data || [];
                      if (!items.length) { list.hidden = true; return; }
                      list.innerHTML = items.slice(0, 8).map(function(s) {
                          return '<button type="button" data-id="' + s.id + '" data-name="' + s.name + '" data-postcode="' + s.postcode + '" data-state="' + (s.state || '') + '">' + s.name + ', ' + (s.state || '') + ' ' + s.postcode + '</button>';
                      }).join('');
                      list.hidden = false;
                  })
                  .catch(function() { list.hidden = true; });
            }, 220);
        });
        list.addEventListener('mousedown', function(e) {
            var btn = e.target.closest('button[data-id]');
            if (!btn) return;
            e.preventDefault();
            input.value = btn.dataset.name + ', ' + btn.dataset.state + ' ' + btn.dataset.postcode;
            document.getElementById(hiddenIdSuburbId).value = btn.dataset.id;
            document.getElementById(hiddenIdQ).value = btn.dataset.name;
            list.hidden = true;
        });
        input.addEventListener('blur', function() { setTimeout(function() { list.hidden = true; }, 200); });
    }
    attachSuburbAutocomplete('dtp-with-suburb', 'dtp-with-suburb-id', 'dtp-with-q');
    attachSuburbAutocomplete('dtp-sa-suburb', 'dtp-sa-suburb-id', 'dtp-sa-q');
    attachSuburbAutocomplete('dtp-cta-suburb', 'dtp-cta-suburb-id', 'dtp-cta-q');

    // Stand-alone form: include test_centre in submission
    var saForm = document.getElementById('dtp-standalone-form');
    var saCentre = document.getElementById('dtp-test-centre');
    if (saForm && saCentre) {
        saForm.addEventListener('submit', function() {
            if (saCentre.value) {
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'test_centre';
                hidden.value = saCentre.value;
                saForm.appendChild(hidden);
            }
        });
    }
})();
</script>
@endpush
@endsection
