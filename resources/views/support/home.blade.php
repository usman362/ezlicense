@extends('support.layout')

@section('title', 'Help Centre')
@section('meta_description', 'Find answers to common questions about driving lessons, bookings, payments, and using Secure Licence.')

@section('hero')
<section class="hero">
    <h1 class="visibility-hidden">Secure Licence Support</h1>
    <h2>How can we help?</h2>
    <form role="search" class="search-wrap" action="{{ route('support.search') }}" method="GET" autocomplete="off">
        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
            <circle cx="7" cy="7" r="5.25" stroke="currentColor" stroke-width="1.5"/>
            <path d="M14.5 14.5L11 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <input type="search" name="q" placeholder="Search" aria-label="Search" value="{{ request('q') }}">
    </form>
</section>
@endsection

@section('content')
<div class="container">
    {{-- ── Categories ── --}}
    <section class="section-block">
        <h2 class="visibility-hidden">Categories</h2>
        <ul class="blocks-list">
            @foreach($categories as $cat)
                <li>
                    <a href="{{ route('support.category', $cat->slug) }}" class="blocks-item-link">
                        <span class="blocks-item-title">{{ $cat->name }}</span>
                        @if($cat->description)
                            <span class="blocks-item-description">{{ $cat->description }}</span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- ── Promoted (most-viewed) articles ── --}}
    @if($popular->isNotEmpty())
        <section class="section-block" style="border-top: 1px solid var(--sl-border);">
            <h2>Promoted articles</h2>
            <ul class="article-list">
                @foreach($popular as $a)
                    <li>
                        <a href="{{ route('support.article', $a->slug) }}">{{ $a->title }}</a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection
