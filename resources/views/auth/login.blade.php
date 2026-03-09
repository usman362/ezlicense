@extends('layouts.auth-split')

@section('title', 'Login')

@section('content')
<h1 class="auth-form-title">Login</h1>
@include('auth.partials.login-form')
@endsection
