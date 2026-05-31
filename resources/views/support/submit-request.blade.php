@extends('support.layout')

@section('title', 'Submit a Request')

@section('breadcrumb')
<span class="text-muted mx-2">/</span><span>Submit a Request</span>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1 class="fw-bold mb-2">Submit a Request</h1>
        <p class="text-muted mb-4">Fill out the form below and our support team will respond within 1 business day.</p>

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix these issues:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('support.request.store') }}" class="bg-white p-4 rounded-3 border">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Your name *</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', auth()->user()->name ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email address *</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email', auth()->user()->email ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone (optional)</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">I am a... (optional)</label>
                    <select name="role" class="form-select">
                        <option value="">Select…</option>
                        <option value="learner" {{ old('role') === 'learner' ? 'selected' : '' }}>Learner</option>
                        <option value="instructor" {{ old('role') === 'instructor' ? 'selected' : '' }}>Instructor</option>
                        <option value="other" {{ old('role') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Topic *</label>
                    <select name="topic" class="form-select" required>
                        <option value="">What's this about?</option>
                        @foreach($topics as $key => $label)
                            <option value="{{ $key }}" {{ old('topic', $prefillTopic) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Subject *</label>
                    <input type="text" name="subject" class="form-control" required maxlength="255" value="{{ old('subject') }}" placeholder="Briefly describe your issue">
                </div>

                <div class="col-12">
                    <label class="form-label">Message *</label>
                    <textarea name="message" class="form-control" rows="6" required minlength="10" maxlength="5000" placeholder="Please share as much detail as possible. Include booking references if relevant.">{{ old('message') }}</textarea>
                    <div class="form-text">Minimum 10 characters.</div>
                </div>

                {{-- Honeypot --}}
                <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute; left:-9999px;">

                <div class="col-12 d-grid">
                    <button type="submit" class="btn btn-warning btn-lg fw-bold">
                        <i class="bi bi-send-fill me-1"></i> Submit Request
                    </button>
                </div>
            </div>
        </form>

        <div class="alert alert-light mt-4 small">
            <i class="bi bi-info-circle"></i>
            By submitting this form, you agree to be contacted by our support team about your request.
            We typically respond within 1 business day.
        </div>
    </div>
</div>
@endsection
