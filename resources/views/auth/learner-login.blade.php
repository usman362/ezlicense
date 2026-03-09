@extends('layouts.auth-split')

@section('title', 'Learner Login')

@section('content')
<h1 class="auth-form-title">Learner Login</h1>
@include('auth.partials.login-form')
@endsection
