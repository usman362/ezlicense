@extends('layouts.auth-split')

@section('title', 'Instructor Login')

@section('brand_content')
    <span class="auth-brand-eyebrow"><i class="bi bi-person-badge-fill me-1"></i>For Driving Instructors</span>
    <h1 class="auth-brand-headline">
        Manage your <span class="highlight">driving school</span> on the go.
    </h1>
    <p class="auth-brand-sub">
        Welcome back. Sign in to check bookings, accept new requests, message learners and track your weekly earnings.
    </p>
    <ul class="auth-brand-features">
        <li><i class="bi bi-calendar-check"></i><span>See today's lessons and manage your week at a glance</span></li>
        <li><i class="bi bi-chat-dots"></i><span>Message learners and accept proposals in real time</span></li>
        <li><i class="bi bi-cash-coin"></i><span>Track your earnings, payouts and pending balances</span></li>
        <li><i class="bi bi-star-fill"></i><span>Reply to reviews and grow your reputation</span></li>
    </ul>
@endsection

@section('content')
<h1 class="auth-form-title">Instructor Login</h1>
<p class="text-muted mb-4">New to Secure Licence? <a href="{{ route('instruct-with-us') }}" class="auth-register-link">Become an instructor</a></p>
@include('auth.partials.login-form', [
    'signupPrompt' => 'Want to teach with Secure Licence?',
    'signupUrl'    => route('support.request.show', ['topic' => 'instructor']),
    'signupLabel'  => 'Apply as an Instructor',
])
@endsection
