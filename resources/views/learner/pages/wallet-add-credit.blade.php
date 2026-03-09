@extends('layouts.learner')

@section('title', 'Add Credit')
@section('heading', 'Wallet')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('learner.wallet') }}">My Wallet</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Credit</li>
    </ol>
</nav>

<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <h5 class="card-title">Add Lesson Credit</h5>
        <p class="text-muted">Buy more and save! Lesson credit packages and payment integration will be available here.</p>
        <a href="{{ route('learner.wallet') }}" class="btn btn-warning">Back to Wallet</a>
    </div>
</div>
@endsection
