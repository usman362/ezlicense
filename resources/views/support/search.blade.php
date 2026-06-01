@extends('support.layout')

@section('title', 'Search: ' . $q)

@section('hero')
<section class="hero">
    <h2>Search results</h2>
    <form role="search" class="search-wrap" action="{{ route('support.search') }}" method="GET" autocomplete="off">
        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
            <circle cx="7" cy="7" r="5.25" stroke="currentColor" stroke-width="1.5"/>
            <path d="M14.5 14.5L11 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <input type="search" name="q" placeholder="Search" aria-label="Search" value="{{ $q }}">
    </form>
</section>
@endsection

@section('content')
<div class="container">
    @if($q === '')
        <div class="empty-state">
            <i class="bi bi-search"></i>
            <h4>Type a search term above</h4>
            <p>Find help articles by keyword.</p>
        </div>
    @else
        <div class="search-results-summary">
            {{ $results->count() }} {{ Str::plural('result', $results->count()) }} for <strong>"{{ $q }}"</strong>
        </div>

        @if($results->isEmpty())
            <div class="empty-state">
                <i class="bi bi-search"></i>
                <h4>No results found</h4>
                <p>Try different keywords, or <a href="{{ route('support.request.show') }}">contact our support team</a>.</p>
            </div>
        @else
            <ul class="article-list" style="border-top: 1px solid var(--sl-border);">
                @foreach($results as $a)
                    <li>
                        <a href="{{ route('support.article', $a->slug) }}" style="display: block; padding: 16px 4px;">
                            <div style="font-weight: 600; color: var(--sl-ink); margin-bottom: 4px;">{{ $a->title }}</div>
                            <div style="color: var(--sl-text-muted); font-size: 13px; margin-bottom: 4px;">
                                {{ $a->section->category->name ?? '' }} › {{ $a->section->name ?? '' }}
                            </div>
                            @if($a->excerpt)
                                <div style="color: var(--sl-text-muted); font-size: 14px;">{{ Str::limit($a->excerpt, 160) }}</div>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    @endif
</div>
@endsection
