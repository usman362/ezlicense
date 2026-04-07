@extends('layouts.frontend')

@section('title', 'Find a Service Provider')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-2">Find a Service Provider</h1>
            <p class="text-muted mb-0">Book trusted local plumbers, electricians, cleaners and more.</p>
        </div>
        <a href="{{ route('services.become-provider') }}" class="btn btn-success btn-lg">
            Become a Service Provider →
        </a>
    </div>

    <div class="row g-3">
        @forelse($categories as $category)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('services.browse', $category->slug) }}" class="card h-100 text-decoration-none text-dark border shadow-sm category-card">
                    <div class="card-body text-center">
                        <div class="fs-1 mb-2"><i class="bi bi-tools text-primary"></i></div>
                        <h2 class="h5 fw-semibold mb-1">{{ $category->name }}</h2>
                        <p class="text-muted small mb-0">{{ $category->providers_count }} providers</p>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">No service categories available yet.</div>
        @endforelse
    </div>
</div>

<style>
.category-card { transition: all .2s; }
.category-card:hover { border-color: #0d6efd !important; box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important; }
</style>
@endsection
