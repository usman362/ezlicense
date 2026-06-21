@extends('support.layout')

@section('title', 'Submit a Request')

@section('subnav')
    <ol class="breadcrumbs">
        <li><a href="{{ route('support.home') }}">Secure Licence Support</a></li>
        <li>Submit a Request</li>
    </ol>
@endsection

@section('content')
<div class="container">
    <div class="request-form-wrap">
        <h1>Submit a request</h1>
        <p class="lead">Fill out the form below and our support team will respond within 1 business day.</p>

        @if($errors->any())
            <div class="errors">
                <strong>Please fix these issues:</strong>
                <ul>@foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('support.request.store') }}" id="sr-form">
            @csrf

            {{-- Primary selector — nothing else shows until one is chosen --}}
            <div class="form-group">
                <label>Which best describes you? <span class="req">*</span></label>
                <select name="describes_you" id="sr-describes" required>
                    <option value="">Select…</option>
                    @foreach($describes as $key => $opt)
                        <option value="{{ $key }}" data-mode="{{ $opt['mode'] }}"
                            {{ old('describes_you', $prefillDescribes) === $key ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- ───── SUPPORT fields (learner / existing instructor / media / other) ───── --}}
            <div id="grp-support" class="sr-group" style="display:none;">
                <div class="row">
                    <div class="form-group">
                        <label>Your name <span class="req">*</span></label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Email address <span class="req">*</span></label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Phone (optional)</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}">
                </div>
                <div class="form-group">
                    <label>Subject <span class="req">*</span></label>
                    <input type="text" name="subject" maxlength="255" value="{{ old('subject') }}" placeholder="Briefly describe your issue">
                </div>
                <div class="form-group">
                    <label>Message <span class="req">*</span></label>
                    <textarea name="message" rows="6" minlength="10" maxlength="5000" placeholder="Please share as much detail as possible. Include booking references if relevant.">{{ old('message') }}</textarea>
                    <div class="help-text">Minimum 10 characters.</div>
                </div>
            </div>

            {{-- ───── INSTRUCTOR application fields (join / become) — submitted right here ───── --}}
            <div id="grp-instructor" class="sr-group" style="display:none;">
                <div class="row">
                    <div class="form-group">
                        <label>First name <span class="req">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}">
                    </div>
                    <div class="form-group">
                        <label>Surname <span class="req">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label>Email address <span class="req">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                        <label>Mobile phone number <span class="req">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label>State</label>
                        <select name="state">
                            <option value="">Select…</option>
                            @foreach($states as $st)
                                <option value="{{ $st->name }}" {{ old('state') === $st->name ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Postcode</label>
                        <input type="text" name="postcode" maxlength="10" value="{{ old('postcode') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label>Transmission you teach</label>
                        <select name="transmission">
                            <option value="">Select…</option>
                            <option value="auto"   {{ old('transmission')==='auto' ? 'selected' : '' }}>Auto only</option>
                            <option value="manual" {{ old('transmission')==='manual' ? 'selected' : '' }}>Manual only</option>
                            <option value="both"   {{ old('transmission')==='both' ? 'selected' : '' }}>Auto &amp; Manual</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Years instructing (optional)</label>
                        <input type="number" name="years_experience" min="0" max="70" value="{{ old('years_experience') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Tell us about yourself <span class="req">*</span></label>
                    <textarea name="message" rows="5" minlength="10" maxlength="3000" placeholder="Your accreditation, where you teach, and why you'd like to join Secure Licence.">{{ old('message') }}</textarea>
                    <div class="help-text">Our team will review and email you which documents to send for verification.</div>
                </div>
            </div>

            {{-- Honeypot --}}
            <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute; left:-9999px;">

            {{-- Submit — hidden until a selection is made --}}
            <button type="submit" class="submit-btn" id="sr-submit" style="display:none;">Submit request</button>
        </form>

        <script>
        (function () {
            var sel = document.getElementById('sr-describes');
            var groups = {
                support:    { el: document.getElementById('grp-support'),    fields: ['name','email','subject','message'] },
                instructor: { el: document.getElementById('grp-instructor'), fields: ['first_name','last_name','email','phone','message'] }
            };
            var submitBtn = document.getElementById('sr-submit');
            var labels = { support: 'Submit request', instructor: 'Submit application' };

            function sync() {
                var opt  = sel.options[sel.selectedIndex];
                var mode = opt ? opt.getAttribute('data-mode') : '';
                Object.keys(groups).forEach(function (key) {
                    var g = groups[key];
                    var on = (key === mode);
                    g.el.style.display = on ? 'block' : 'none';
                    g.el.querySelectorAll('[name]').forEach(function (i) { i.required = false; });
                    if (on) g.fields.forEach(function (n) {
                        var f = g.el.querySelector('[name="' + n + '"]'); if (f) f.required = true;
                    });
                });
                submitBtn.style.display = mode ? 'block' : 'none';
                if (mode) submitBtn.textContent = labels[mode] || 'Submit';
            }
            sel.addEventListener('change', sync);
            sync();
        })();
        </script>

        <p style="color: var(--sl-text-muted); font-size: 12.5px; margin-top: 18px; text-align: center;">
            By submitting this form, you agree to be contacted by our support team about your request. We typically respond within 1 business day.
        </p>
    </div>
</div>
@endsection
