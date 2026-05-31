@extends('support.layout')

@section('title', $section->name)
@section('meta_description', $section->description ?? 'Articles in ' . $section->name)

@section('breadcrumb')
<span class="text-muted mx-2">/</span><a href="{{ route('support.category', $section->category->slug) }}">{{ $section->category->name }}</a>
<span class="text-muted mx-2">/</span><span>{{ $section->name }}</span>
@endsection

@section('content')
<div class="mb-4">
    <h1 class="fw-bold mb-2">
        <i class="bi {{ $section->icon ?? 'bi-folder' }} text-warning"></i>
        {{ $section->name }}
    </h1>
    <div class="text-muted small">{{ $articles->total() }} {{ Str::plural('article', $articles->total()) }} in this section</div>
</div>

@forelse($articles as $a)
    <a href="{{ route('support.article', $a->slug) }}" class="article-list-item">
        <div>
            <h5 class="mb-1">{{ $a->title }}</h5>
            @if($a->excerpt)
                <div class="text-muted small">{{ Str::limit($a->excerpt, 140) }}</div>
            @endif
        </div>
        <span class="arrow"><i class="bi bi-chevron-right"></i></span>
    </a>
@empty
    <div class="alert alert-light text-center">No articles in this section yet.</div>
@endforelse

@if($articles->hasPages())
    <div class="mt-4">{{ $articles->links() }}</div>
@endif
@endsection
