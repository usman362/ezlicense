@extends('support.layout')

@section('title', 'Search: ' . $q)

@section('hero')
<section class="sup-hero">
    <div class="container text-center">
        <h1>Search results</h1>
        <form class="search-box" action="{{ route('support.search') }}" method="GET">
            <div class="input-group">
                <input type="search" name="q" class="form-control" placeholder="Search for help articles…" value="{{ $q }}" autocomplete="off">
                <button type="submit" class="btn"><i class="bi bi-search me-1"></i>Search</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('content')
@if($q === '')
    <div class="alert alert-light text-center">Type a search term above to find help articles.</div>
@else
    <p class="text-muted">{{ $results->count() }} {{ Str::plural('result', $results->count()) }} for <strong>"{{ $q }}"</strong></p>

    @forelse($results as $a)
        <a href="{{ route('support.article', $a->slug) }}" class="article-list-item">
            <div>
                <h5 class="mb-1">{{ $a->title }}</h5>
                <div class="text-muted small mb-1">
                    <i class="bi bi-folder"></i> {{ $a->section->category->name ?? '' }} › {{ $a->section->name ?? '' }}
                </div>
                @if($a->excerpt)
                    <div class="text-muted small">{{ Str::limit($a->excerpt, 160) }}</div>
                @endif
            </div>
            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
        </a>
    @empty
        <div class="alert alert-light text-center py-5">
            <i class="bi bi-search display-4 d-block mb-3 opacity-50"></i>
            <h4>No results found</h4>
            <p class="text-muted">Try different keywords, or <a href="{{ route('support.request.show') }}">contact our support team</a>.</p>
        </div>
    @endforelse
@endif
@endsection
