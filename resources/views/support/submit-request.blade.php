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

        <form method="POST" action="{{ route('support.request.store') }}">
            @csrf

            <div class="row">
                <div class="form-group">
                    <label>Your name <span class="req">*</span></label>
                    <input type="text" name="name" required value="{{ old('name', auth()->user()->name ?? '') }}">
                </div>
                <div class="form-group">
                    <label>Email address <span class="req">*</span></label>
                    <input type="email" name="email" required value="{{ old('email', auth()->user()->email ?? '') }}">
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Phone (optional)</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}">
                </div>
                <div class="form-group">
                    <label>I am a... (optional)</label>
                    <select name="role">
                        <option value="">Select…</option>
                        <option value="learner" {{ old('role') === 'learner' ? 'selected' : '' }}>Learner</option>
                        <option value="instructor" {{ old('role') === 'instructor' ? 'selected' : '' }}>Instructor</option>
                        <option value="other" {{ old('role') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Topic <span class="req">*</span></label>
                <select name="topic" required>
                    <option value="">What's this about?</option>
                    @foreach($topics as $key => $label)
                        <option value="{{ $key }}" {{ old('topic', $prefillTopic) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Subject <span class="req">*</span></label>
                <input type="text" name="subject" required maxlength="255" value="{{ old('subject') }}" placeholder="Briefly describe your issue">
            </div>

            <div class="form-group">
                <label>Message <span class="req">*</span></label>
                <textarea name="message" rows="6" required minlength="10" maxlength="5000" placeholder="Please share as much detail as possible. Include booking references if relevant.">{{ old('message') }}</textarea>
                <div class="help-text">Minimum 10 characters.</div>
            </div>

            {{-- Honeypot --}}
            <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute; left:-9999px;">

            <button type="submit" class="submit-btn">Submit request</button>
        </form>

        <p style="color: var(--sl-text-muted); font-size: 12.5px; margin-top: 18px; text-align: center;">
            By submitting this form, you agree to be contacted by our support team about your request. We typically respond within 1 business day.
        </p>
    </div>
</div>
@endsection
