@extends('support.layout')

@section('title', 'Help Centre')
@section('meta_description', 'Find answers to common questions about driving lessons, bookings, payments, and using Secure Licence.')

@section('hero')
<section class="sup-hero">
    <div class="container text-center">
        <h1>How can we help?</h1>
        <p class="lead">Browse articles or search for what you need.</p>
        <form class="search-box" action="{{ route('support.search') }}" method="GET">
            <div class="input-group">
                <input type="search" name="q" class="form-control" placeholder="Search for help articles…" autocomplete="off">
                <button type="submit" class="btn"><i class="bi bi-search me-1"></i>Search</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('content')
{{-- ── Categories grid ── --}}
<h2 class="sup-section-title">Browse by topic</h2>
<div class="row g-4 mb-5">
    @forelse($categories as $cat)
        <div class="col-md-6">
            <a href="{{ route('support.category', $cat->slug) }}" class="cat-card">
                <div class="cat-icon"><i class="bi {{ $cat->icon ?? 'bi-question-circle-fill' }}"></i></div>
                <h3>{{ $cat->name }}</h3>
                @if($cat->description)
                    <p>{{ $cat->description }}</p>
                @endif
                <div class="cat-meta">
                    <i class="bi bi-file-text"></i> {{ $cat->articles_count }} {{ Str::plural('article', $cat->articles_count) }}
                </div>
            </a>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-light text-center">No help topics available yet.</div>
        </div>
    @endforelse
</div>

{{-- ── Popular articles ── --}}
@if($popular->isNotEmpty())
    <h2 class="sup-section-title">Popular articles</h2>
    <div class="row g-3">
        @foreach($popular as $a)
            <div class="col-md-6">
                <a href="{{ route('support.article', $a->slug) }}" class="article-list-item">
                    <h5>{{ $a->title }}</h5>
                    <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                </a>
            </div>
        @endforeach
    </div>
@endif

{{-- ── Contact CTA ── --}}
<div class="text-center mt-5 py-5" style="background:#fff; border-radius:12px; border:1px solid var(--sl-gray-200);">
    <h3>Still need help?</h3>
    <p class="text-muted mb-4">Our team responds to all queries within 1 business day.</p>
    <a href="{{ route('support.request.show') }}" class="btn btn-warning fw-bold btn-lg">
        <i class="bi bi-envelope-fill me-2"></i>Submit a request
    </a>
</div>
@endsection
