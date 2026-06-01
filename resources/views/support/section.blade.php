@extends('support.layout')

@section('title', $section->name)
@section('meta_description', $section->description ?? 'Articles in ' . $section->name)

@section('subnav')
    <ol class="breadcrumbs">
        <li><a href="{{ route('support.home') }}">Secure Licence Support</a></li>
        <li><a href="{{ route('support.category', $section->category->slug) }}">{{ $section->category->name }}</a></li>
        <li>{{ $section->name }}</li>
    </ol>
    <form role="search" class="inline-search" action="{{ route('support.search') }}" method="GET">
        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="none">
            <circle cx="7" cy="7" r="5.25" stroke="currentColor" stroke-width="1.5"/>
            <path d="M14.5 14.5L11 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <input type="search" name="q" placeholder="Search" aria-label="Search">
    </form>
@endsection

@section('content')
<div class="container">
    <div class="section-block">
        <h1 style="font-size: 28px; margin-bottom: 8px;">{{ $section->name }}</h1>
        <p style="color: var(--sl-text-muted); font-size: 14px; margin: 0 0 24px;">
            {{ $articles->total() }} {{ Str::plural('article', $articles->total()) }} in this section
        </p>

        @if($articles->isEmpty())
            <div class="empty-state">
                <i class="bi bi-file-text"></i>
                <h4>No articles yet</h4>
            </div>
        @else
            <ul class="article-list">
                @foreach($articles as $a)
                    <li><a href="{{ route('support.article', $a->slug) }}">{{ $a->title }}</a></li>
                @endforeach
            </ul>

            @if($articles->hasPages())
                <div style="margin-top: 24px;">{{ $articles->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
