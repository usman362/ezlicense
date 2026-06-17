@extends('layouts.auth-split')

@section('title', 'Create Account')

@section('content')
<h1 class="auth-form-title">Create Account</h1>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="auth-input-wrap">
        <i class="bi bi-person"></i>
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Full Name" required autocomplete="name" autofocus>
        @error('name')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="auth-input-wrap">
        <i class="bi bi-envelope"></i>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="email">
        @error('email')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="auth-input-wrap">
        <i class="bi bi-phone"></i>
        <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="Phone Number" required autocomplete="tel">
        @error('phone')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- Public signup is locked to LEARNER role only. Instructors must apply and be
         approved by admin before any account is created. See InstructorApplicationController. --}}
    <input type="hidden" name="role" value="learner">
    <p class="small text-muted mb-3" style="margin-top:-0.5rem;">
        <i class="bi bi-info-circle me-1"></i>
        Want to teach? <a href="{{ route('instructor-application.show') }}" class="auth-register-link">Apply as an instructor</a>
        — admin reviews your documents before any account is created.
    </p>
    @error('role')
        <div class="alert alert-warning small">{{ $message }}</div>
    @enderror

    <div class="auth-input-wrap">
        <i class="bi bi-lock"></i>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="new-password">
        @error('password')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="auth-input-wrap">
        <i class="bi bi-lock-fill"></i>
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
    </div>

    <button type="submit" class="btn auth-btn-login mb-3">Register</button>

    <div class="text-center mt-3 pt-3" style="border-top: 1px solid #eee;">
        <p class="text-muted mb-2">Already have an account?</p>
        <a href="{{ route('learner.login') }}" class="btn btn-outline-dark w-100">Login</a>
    </div>
</form>
@endsection
