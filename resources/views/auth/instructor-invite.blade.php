@extends('layouts.auth-split')

@section('title', 'Accept your instructor invitation')

@section('brand_content')
    <span class="auth-brand-eyebrow"><i class="bi bi-envelope-paper-fill me-1"></i>You're invited</span>
    <h1 class="auth-brand-headline">
        Welcome to <span class="highlight">Secure Licence</span>.
    </h1>
    <p class="auth-brand-sub">
        Set your password below to create your instructor account. Next, you'll upload your documents for verification — most instructors finish in under 10 minutes.
    </p>
    <ul class="auth-brand-features">
        <li><i class="bi bi-1-circle"></i><span>Set your password (just a few clicks)</span></li>
        <li><i class="bi bi-2-circle"></i><span>Upload your driving instructor licence, WWCC &amp; insurance</span></li>
        <li><i class="bi bi-3-circle"></i><span>Admin verifies your documents within 24–48 hours</span></li>
        <li><i class="bi bi-4-circle"></i><span>Set your service areas and start accepting bookings</span></li>
    </ul>
@endsection

@section('content')

<h1 class="auth-form-title">Create your instructor account</h1>
<p class="text-muted mb-4">
    Invited as <strong>{{ $invite->email }}</strong>.
    Link expires {{ $invite->expires_at->diffForHumans() }}.
</p>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('instructor.invite.register', ['token' => $invite->token]) }}">
    @csrf

    <div class="row g-3">
        <div class="col-6">
            <div class="auth-input-wrap">
                <i class="bi bi-person"></i>
                <input type="text" name="first_name" class="form-control" placeholder="First name" required value="{{ old('first_name', $invite->first_name) }}">
            </div>
        </div>
        <div class="col-6">
            <div class="auth-input-wrap">
                <i class="bi bi-person"></i>
                <input type="text" name="last_name" class="form-control" placeholder="Last name" required value="{{ old('last_name', $invite->last_name) }}">
            </div>
        </div>
    </div>

    {{-- Email is locked — derived from the invite server-side --}}
    <div class="auth-input-wrap mt-3">
        <i class="bi bi-envelope-check"></i>
        <input type="email" class="form-control" value="{{ $invite->email }}" disabled readonly style="background: var(--sl-gray-100); cursor: not-allowed;">
    </div>

    <div class="auth-input-wrap">
        <i class="bi bi-telephone"></i>
        <input type="tel" name="phone" class="form-control" placeholder="Mobile phone" required value="{{ old('phone', $invite->phone) }}">
    </div>

    {{-- ── Pre-filled bio (collapsible) — instructor can quickly review/edit ── --}}
    @php
        $hasPrefill = $invite->years_experience || $invite->transmission || $invite->bio || $invite->lesson_price || $invite->vehicle_make;
    @endphp
    @if($hasPrefill)
        <div class="my-3 p-3 rounded" style="background: #fef9c3; border: 1px solid #fde047;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong style="font-size: 14px;">
                    <i class="bi bi-stars text-warning"></i> We've pre-built your profile
                </strong>
                <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="collapse" data-bs-target="#prefillBio">
                    Review &amp; edit
                </button>
            </div>
            <div class="small text-muted">Admin already added your basic info. Review below or just submit if it looks right — you can edit anything anytime in Settings.</div>
        </div>

        <div class="collapse" id="prefillBio">
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label small fw-semibold">Years experience</label>
                    <input type="number" name="years_experience" min="0" max="60" class="form-control form-control-sm" value="{{ old('years_experience', $invite->years_experience) }}">
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">Transmission</label>
                    <select name="transmission" class="form-select form-select-sm">
                        <option value="">Choose…</option>
                        @foreach(['auto' => 'Auto only', 'manual' => 'Manual only', 'both' => 'Both'] as $k => $l)
                            <option value="{{ $k }}" {{ old('transmission', $invite->transmission) === $k ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">Lesson price ($)</label>
                    <input type="number" step="0.01" name="lesson_price" class="form-control form-control-sm" value="{{ old('lesson_price', $invite->lesson_price) }}">
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">Vehicle</label>
                    <input type="text" name="vehicle_make" class="form-control form-control-sm mb-1" placeholder="Make" value="{{ old('vehicle_make', $invite->vehicle_make) }}">
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Bio</label>
                    <textarea name="bio" class="form-control form-control-sm" rows="3" maxlength="2000">{{ old('bio', $invite->bio) }}</textarea>
                </div>
            </div>
        </div>
    @endif

    <div class="auth-input-wrap">
        <i class="bi bi-lock"></i>
        <input type="password" name="password" class="form-control" placeholder="Choose a password (min 8 chars)" required minlength="8">
    </div>

    <div class="auth-input-wrap">
        <i class="bi bi-shield-lock"></i>
        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required minlength="8">
    </div>

    <div class="form-check auth-remember mb-4">
        <input class="form-check-input" type="checkbox" name="accept_terms" id="accept_terms" value="1" required>
        <label class="form-check-label small" for="accept_terms">
            I agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a>.
        </label>
    </div>

    <button type="submit" class="btn auth-btn-login mb-3">
        <i class="bi bi-rocket-takeoff-fill me-2"></i>Create account &amp; continue
    </button>

    <p class="text-muted small text-center mb-0">
        Already have an account? <a href="{{ route('instructor.login') }}" class="auth-register-link">Log in instead</a>
    </p>
</form>

@endsection
