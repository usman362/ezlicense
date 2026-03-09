@extends('layouts.auth-split')

@section('title', 'Admin Login')

@section('content')
<h1 class="auth-form-title">Admin Login</h1>
<p class="text-muted small mb-4">Use this page to sign in as an administrator.</p>
@include('auth.partials.login-form')
@endsection
