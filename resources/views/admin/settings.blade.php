@extends('layouts.admin')

@section('title', 'Settings')
@section('heading', 'Platform Settings')

@section('content')
@php
    $groups = [
        'general'    => ['icon' => 'bi-gear',           'label' => 'General'],
        'payment'    => ['icon' => 'bi-credit-card',    'label' => 'Payment Gateway'],
        'commission' => ['icon' => 'bi-percent',        'label' => 'Fees & Commission'],
        'email'      => ['icon' => 'bi-envelope',       'label' => 'Email / SMTP'],
        'sms'        => ['icon' => 'bi-phone',          'label' => 'SMS (Twilio)'],
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
</script>
@endpush
@endsection
