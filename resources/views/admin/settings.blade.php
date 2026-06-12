@extends('layouts.admin')

@section('title', 'Settings')
@section('heading', 'Platform Settings')

@section('content')
@php
    $groups = [
        'general'    => ['icon' => 'bi-gear',              'label' => 'General'],
        'branding'   => ['icon' => 'bi-palette',           'label' => 'Branding'],
        'seo'        => ['icon' => 'bi-search',            'label' => 'SEO & Analytics'],
        'payment'    => ['icon' => 'bi-credit-card',       'label' => 'Payment Gateway'],
        'commission' => ['icon' => 'bi-percent',           'label' => 'Fees & Commission'],
        'discounts'  => ['icon' => 'bi-tag',               'label' => 'Bulk Discounts'],
        'referral'   => ['icon' => 'bi-people',            'label' => 'Referral Program'],
        'email'      => ['icon' => 'bi-envelope',          'label' => 'Email / SMTP'],
        'sms'        => ['icon' => 'bi-phone',             'label' => 'SMS (Twilio)'],
    ];
@endphp

@if(!isset($settings) || $settings->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-gear fs-1 text-muted"></i>
            <h5 class="mt-3">No settings configured yet</h5>
            <p class="text-muted">Click the button below to initialise all default settings.</p>
            <form method="POST" action="{{ route('admin.settings.seed') }}">
                @csrf
                <button type="submit" class="btn btn-primary"><i class="bi bi-database-add me-1"></i> Initialise Default Settings</button>
            </form>
        </div>
    </div>
@else
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <ul class="nav nav-tabs mb-0" id="settingsTabs" role="tablist">
            @foreach($groups as $groupKey => $groupMeta)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }} d-flex align-items-center gap-2"
                            id="tab-{{ $groupKey }}"
                            data-bs-toggle="tab"
                            data-bs-target="#pane-{{ $groupKey }}"
                            type="button"
                            role="tab">
                        <i class="bi {{ $groupMeta['icon'] }}"></i>
                        <span class="d-none d-md-inline">{{ $groupMeta['label'] }}</span>
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content">
            @foreach($groups as $groupKey => $groupMeta)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                     id="pane-{{ $groupKey }}"
                     role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-top-0">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0"><i class="bi {{ $groupMeta['icon'] }} me-2"></i>{{ $groupMeta['label'] }}</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($settings[$groupKey]))
                                @foreach($settings[$groupKey] as $setting)
                                    @php $s = (object) $setting; @endphp
                                    <div class="row mb-3 align-items-start">
                                        <label class="col-md-3 col-form-label fw-semibold" for="setting-{{ $s->key }}">
                                            {{ $s->label ?? $s->key }}
                                        </label>
                                        <div class="col-md-6">
                                            @if($s->type === 'boolean')
                                                <div class="form-check form-switch mt-2">
                                                    <input type="hidden" name="settings[{{ $s->key }}]" value="0">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           id="setting-{{ $s->key }}"
                                                           name="settings[{{ $s->key }}]"
                                                           value="1"
                                                           {{ $s->value ? 'checked' : '' }}>
                                                    <label class="form-check-label text-muted small" for="setting-{{ $s->key }}">
                                                        {{ $s->value ? 'Enabled' : 'Disabled' }}
                                                    </label>
                                                </div>
                                            @elseif($s->type === 'secret')
                                                <div class="input-group">
                                                    <input type="password"
                                                           class="form-control"
                                                           id="setting-{{ $s->key }}"
                                                           name="settings[{{ $s->key }}]"
                                                           value="{{ $s->value ? str_repeat('*', 8) : '' }}"
                                                           onfocus="if(this.value==='{{ str_repeat('*', 8) }}'){this.value='';this.type='text';}"
                                                           autocomplete="off">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                            onclick="let i=document.getElementById('setting-{{ $s->key }}'); i.type = i.type==='password' ? 'text' : 'password';">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            @elseif($s->type === 'number')
                                                <input type="number"
                                                       class="form-control"
                                                       id="setting-{{ $s->key }}"
                                                       name="settings[{{ $s->key }}]"
                                                       value="{{ $s->value }}"
                                                       step="any">
                                            @elseif($s->type === 'json')
                                                @php
                                                    $jsonValue = is_string($s->value) ? json_decode($s->value, true) : $s->value;
                                                    if (! is_array($jsonValue)) $jsonValue = [];
                                                @endphp
                                                @if($s->key === 'hours_discount_tiers')
                                                    {{-- Tier repeater: rows of {hours, discount_pct} --}}
                                                    <div class="json-repeater" data-key="{{ $s->key }}">
                                                        <div class="row g-2 mb-1 small text-muted fw-semibold">
                                                            <div class="col-5">Buy at least (hours)</div>
                                                            <div class="col-5">Discount (%)</div>
                                                            <div class="col-2"></div>
                                                        </div>
                                                        <div class="repeater-rows">
                                                            @foreach($jsonValue as $i => $tier)
                                                                <div class="row g-2 mb-2 repeater-row">
                                                                    <div class="col-5"><input type="number" min="1" class="form-control" data-tier-hours value="{{ $tier['hours'] ?? '' }}"></div>
                                                                    <div class="col-5"><input type="number" min="0" max="100" step="0.01" class="form-control" data-tier-pct value="{{ $tier['discount_pct'] ?? '' }}"></div>
                                                                    <div class="col-2"><button type="button" class="btn btn-outline-danger btn-sm w-100 repeater-remove"><i class="bi bi-x-lg"></i></button></div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button type="button" class="btn btn-outline-primary btn-sm repeater-add"><i class="bi bi-plus-lg"></i> Add tier</button>
                                                        <input type="hidden" name="settings[{{ $s->key }}]" id="setting-{{ $s->key }}" value="{{ is_string($s->value) ? $s->value : json_encode($jsonValue) }}">
                                                    </div>
                                                @elseif($s->key === 'booking_hour_packages')
                                                    {{-- Simple list repeater (single value per row) --}}
                                                    <div class="json-list-repeater" data-key="{{ $s->key }}">
                                                        <div class="repeater-rows">
                                                            @foreach($jsonValue as $val)
                                                                <div class="row g-2 mb-2 repeater-row">
                                                                    <div class="col-10"><input type="number" min="1" class="form-control" data-list-value value="{{ $val }}"></div>
                                                                    <div class="col-2"><button type="button" class="btn btn-outline-danger btn-sm w-100 repeater-remove"><i class="bi bi-x-lg"></i></button></div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button type="button" class="btn btn-outline-primary btn-sm repeater-add"><i class="bi bi-plus-lg"></i> Add option</button>
                                                        <input type="hidden" name="settings[{{ $s->key }}]" id="setting-{{ $s->key }}" value="{{ is_string($s->value) ? $s->value : json_encode($jsonValue) }}">
                                                    </div>
                                                @else
                                                    <textarea class="form-control" rows="4" id="setting-{{ $s->key }}" name="settings[{{ $s->key }}]" style="font-family:monospace;font-size:0.85rem;">{{ is_string($s->value) ? $s->value : json_encode($jsonValue, JSON_PRETTY_PRINT) }}</textarea>
                                                    <div class="form-text text-muted">Raw JSON. Edit carefully.</div>
                                                @endif
                                            @elseif($s->type === 'textarea')
                                                <textarea class="form-control"
                                                          rows="3"
                                                          id="setting-{{ $s->key }}"
                                                          name="settings[{{ $s->key }}]"
                                                          maxlength="500">{{ $s->value }}</textarea>
                                                @if(in_array($s->key, ['seo_default_description']))
                                                    <div class="form-text text-muted">
                                                        <span id="char-count-{{ $s->key }}">{{ strlen($s->value) }}</span> / 160 recommended for search snippets
                                                    </div>
                                                    <script>
                                                    document.getElementById('setting-{{ $s->key }}').addEventListener('input', function () {
                                                        document.getElementById('char-count-{{ $s->key }}').textContent = this.value.length;
                                                    });
                                                    </script>
                                                @endif
                                            @elseif($s->key === 'stripe_mode')
                                                {{-- Test / Live segmented toggle for Stripe mode --}}
                                                <div class="btn-group" role="group" aria-label="Stripe mode">
                                                    <input type="radio" class="btn-check" name="settings[{{ $s->key }}]"
                                                           id="setting-{{ $s->key }}-test" value="test"
                                                           {{ strtolower((string) $s->value) !== 'live' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-secondary" for="setting-{{ $s->key }}-test">
                                                        <i class="bi bi-flask me-1"></i> Test mode (sandbox)
                                                    </label>

                                                    <input type="radio" class="btn-check" name="settings[{{ $s->key }}]"
                                                           id="setting-{{ $s->key }}-live" value="live"
                                                           {{ strtolower((string) $s->value) === 'live' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger" for="setting-{{ $s->key }}-live">
                                                        <i class="bi bi-lightning-charge-fill me-1"></i> Live mode (real money)
                                                    </label>
                                                </div>
                                            @else
                                                <input type="text"
                                                       class="form-control"
                                                       id="setting-{{ $s->key }}"
                                                       name="settings[{{ $s->key }}]"
                                                       value="{{ $s->value }}">
                                            @endif
                                            @if($s->hint)
                                                <div class="form-text text-muted">{{ $s->hint }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">No settings in this category yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-end mt-3 gap-2">
            <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary">Discard changes</a>
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i> Save All Settings</button>
        </div>
    </form>
@endif

@push('scripts')
<script>
    document.querySelectorAll('.form-check-input').forEach(el => {
        el.addEventListener('change', function() {
            const label = this.nextElementSibling;
            if (label) label.textContent = this.checked ? 'Enabled' : 'Disabled';
        });
    });
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector('[data-bs-target="' + hash + '"]');
        if (tab) new bootstrap.Tab(tab).show();
    }
    document.querySelectorAll('#settingsTabs button').forEach(btn => {
        btn.addEventListener('shown.bs.tab', e => {
            history.replaceState(null, '', e.target.dataset.bsTarget);
        });
    });

    // ── JSON tier repeater (hours_discount_tiers) ──
    document.querySelectorAll('.json-repeater').forEach(function(panel) {
        var rowsEl = panel.querySelector('.repeater-rows');
        var hidden = panel.querySelector('input[type=hidden]');

        function syncHidden() {
            var rows = rowsEl.querySelectorAll('.repeater-row');
            var data = [];
            rows.forEach(function(r) {
                var h = parseInt(r.querySelector('[data-tier-hours]').value, 10);
                var p = parseFloat(r.querySelector('[data-tier-pct]').value);
                if (h > 0 && p >= 0) data.push({ hours: h, discount_pct: p });
            });
            data.sort(function(a, b) { return a.hours - b.hours; });
            hidden.value = JSON.stringify(data);
        }

        function bindRow(row) {
            row.querySelectorAll('input').forEach(function(i) { i.addEventListener('input', syncHidden); });
            var rm = row.querySelector('.repeater-remove');
            if (rm) rm.addEventListener('click', function() { row.remove(); syncHidden(); });
        }

        rowsEl.querySelectorAll('.repeater-row').forEach(bindRow);

        panel.querySelector('.repeater-add').addEventListener('click', function() {
            var div = document.createElement('div');
            div.className = 'row g-2 mb-2 repeater-row';
            div.innerHTML = '<div class="col-5"><input type="number" min="1" class="form-control" data-tier-hours></div>'
                + '<div class="col-5"><input type="number" min="0" max="100" step="0.01" class="form-control" data-tier-pct></div>'
                + '<div class="col-2"><button type="button" class="btn btn-outline-danger btn-sm w-100 repeater-remove"><i class="bi bi-x-lg"></i></button></div>';
            rowsEl.appendChild(div);
            bindRow(div);
        });

        syncHidden();
    });

    // ── Simple list repeater (booking_hour_packages) ──
    document.querySelectorAll('.json-list-repeater').forEach(function(panel) {
        var rowsEl = panel.querySelector('.repeater-rows');
        var hidden = panel.querySelector('input[type=hidden]');

        function syncHidden() {
            var rows = rowsEl.querySelectorAll('.repeater-row');
            var data = [];
            rows.forEach(function(r) {
                var v = parseInt(r.querySelector('[data-list-value]').value, 10);
                if (v > 0) data.push(v);
            });
            data.sort(function(a, b) { return a - b; });
            hidden.value = JSON.stringify(data);
        }

        function bindRow(row) {
            row.querySelector('input').addEventListener('input', syncHidden);
            var rm = row.querySelector('.repeater-remove');
            if (rm) rm.addEventListener('click', function() { row.remove(); syncHidden(); });
        }

        rowsEl.querySelectorAll('.repeater-row').forEach(bindRow);

        panel.querySelector('.repeater-add').addEventListener('click', function() {
            var div = document.createElement('div');
            div.className = 'row g-2 mb-2 repeater-row';
            div.innerHTML = '<div class="col-10"><input type="number" min="1" class="form-control" data-list-value></div>'
                + '<div class="col-2"><button type="button" class="btn btn-outline-danger btn-sm w-100 repeater-remove"><i class="bi bi-x-lg"></i></button></div>';
            rowsEl.appendChild(div);
            bindRow(div);
        });

        syncHidden();
    });
</script>
@endpush
@endsection
