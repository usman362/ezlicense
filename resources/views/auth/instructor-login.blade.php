@extends('layouts.auth-split')

@section('title', 'Instructor Login')

@section('content')
<h1 class="auth-form-title">Instructor Login</h1>
<p class="text-muted mb-4">Want to instruct with EzLicence? <a href="{{ route('register') }}" class="auth-register-link">Register your interest</a></p>
@include('auth.partials.login-form')
@endsection
