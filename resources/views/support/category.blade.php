@extends('support.layout')

@section('title', $category->name)
@section('meta_description', $category->description ?? 'Help articles about ' . $category->name)

@section('subnav')
    <ol class="breadcrumbs">
        <li><a href="{{ route('support.home') }}">Secure Licence Support</a></li>
        <li>{{ $category->name }}</li>
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
        <h1 style="font-size: 28px; margin-bottom: 8px;">{{ $category->name }}</h1>
        @if($category->description)
            <p style="color: var(--sl-text-muted); font-size: 15px; margin: 0 0 32px;">{{ $category->description }}</p>
        @endif

        @if($sections->isEmpty())
            <div class="empty-state">
                <i class="bi bi-folder2-open"></i>
                <h4>No sections yet</h4>
                <p>Content is being added — please check back soon.</p>
            </div>
        @else
            <div class="sections-grid">
                @foreach($sections as $section)
                    <div>
                        <h3 class="section-card-title">{{ $section->name }}</h3>
                        <ul class="section-card-list">
                            @foreach($section->articles as $a)
                                <li><a href="{{ route('support.article', $a->slug) }}">{{ $a->title }}</a></li>
                            @endforeach
                        </ul>
                        @if($section->articles_count > $section->articles->count())
                            <a href="{{ route('support.section', $section->slug) }}" class="see-all-link">
                                See all {{ $section->articles_count }} articles →
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
