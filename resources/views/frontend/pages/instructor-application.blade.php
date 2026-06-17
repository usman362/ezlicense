@extends('layouts.frontend')
@section('title', 'Apply to be an instructor — Secure Licence')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="text-center mb-4">
                    <h1 class="display-6 fw-bold mb-2">Apply to teach with Secure Licence</h1>
                    <p class="text-muted mb-0">
                        Fill in the form below. Our team reviews every application within <strong>2 business days</strong>
                        and emails you the outcome — no account is created until you're approved.
                    </p>
                </div>

                @if (session('message'))
                    <div class="alert alert-success">{!! session('message') !!}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Please fix the following:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <form method="post" action="{{ route('instructor-application.store') }}" enctype="multipart/form-data" novalidate>
                            @csrf

                            {{-- honeypot --}}
                            <input type="text" name="website" tabindex="-1" autocomplete="off"
                                   style="position:absolute; left:-9999px; height:0; width:0;" aria-hidden="true">

                            <h5 class="mb-3 text-warning">1. Your details</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">First name *</label>
                                    <input type="text" name="first_name" class="form-control" required maxlength="100"
                                           value="{{ old('first_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last name</label>
                                    <input type="text" name="last_name" class="form-control" maxlength="100"
                                           value="{{ old('last_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control" required maxlength="191"
                                           value="{{ old('email') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mobile *</label>
                                    <input type="tel" name="phone" class="form-control" required maxlength="30"
                                           placeholder="04XX XXX XXX" value="{{ old('phone') }}">
                                </div>
                            </div>

                            <h5 class="mb-3 text-warning">2. Teaching experience</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Years instructing</label>
                                    <input type="number" name="years_experience" class="form-control" min="0" max="60"
                                           value="{{ old('years_experience') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Transmission you teach</label>
                                    <select name="transmission" class="form-select">
                                        <option value="">— select —</option>
                                        <option value="auto"   @selected(old('transmission')==='auto')>Auto only</option>
                                        <option value="manual" @selected(old('transmission')==='manual')>Manual only</option>
                                        <option value="both"   @selected(old('transmission')==='both')>Auto & Manual</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Lesson price ($)</label>
                                    <input type="number" name="lesson_price" class="form-control" min="0" max="500" step="0.01"
                                           value="{{ old('lesson_price') }}" placeholder="e.g. 60.00">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Tell learners a bit about yourself</label>
                                    <textarea name="bio" rows="4" class="form-control" maxlength="2000"
                                              placeholder="What's your teaching style? Where do you operate? Any specialities?">{{ old('bio') }}</textarea>
                                    <small class="text-muted">Optional — this becomes your public profile bio if approved.</small>
                                </div>
                            </div>

                            <h5 class="mb-3 text-warning">3. Your vehicle (optional)</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Make</label>
                                    <input type="text" name="vehicle_make" class="form-control" maxlength="60"
                                           value="{{ old('vehicle_make') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Model</label>
                                    <input type="text" name="vehicle_model" class="form-control" maxlength="60"
                                           value="{{ old('vehicle_model') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Year</label>
                                    <input type="number" name="vehicle_year" class="form-control" min="1990" max="2100"
                                           value="{{ old('vehicle_year') }}">
                                </div>
                            </div>

                            <h5 class="mb-3 text-warning">4. Compliance documents</h5>
                            <p class="text-muted small mb-3">
                                Accepted formats: JPG, PNG, PDF. Max 8 MB each. All uploads are private and only visible to admin reviewers.
                            </p>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Driver licence (front + back) *</label>
                                    <input type="file" name="driver_licence" class="form-control" required
                                           accept=".jpg,.jpeg,.png,.pdf">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Driving instructor certificate *</label>
                                    <input type="file" name="instructor_certificate" class="form-control" required
                                           accept=".jpg,.jpeg,.png,.pdf">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Working With Children Check</label>
                                    <input type="file" name="wwcc" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                    <small class="text-muted">Required in most states.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Vehicle registration</label>
                                    <input type="file" name="vehicle_rego" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Insurance certificate</label>
                                    <input type="file" name="insurance" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input type="checkbox" class="form-check-input" name="accept_terms" id="accept_terms" value="1"
                                       @checked(old('accept_terms')) required>
                                <label for="accept_terms" class="form-check-label">
                                    I confirm the information above is correct and I agree to the
                                    <a href="{{ route('policies.instructor-conduct') }}" target="_blank">Instructor Code of Conduct</a>
                                    and <a href="{{ route('terms') }}" target="_blank">Terms</a>. *
                                </label>
                            </div>

                            <button type="submit" class="btn btn-warning btn-lg fw-semibold">
                                Submit application
                            </button>
                            <small class="d-block text-muted mt-2">
                                We'll email you a confirmation with your reference number once your application is received.
                            </small>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
