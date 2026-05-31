@extends('support.layout')

@section('title', $category->name)
@section('meta_description', $category->description ?? 'Help articles about ' . $category->name)

@section('breadcrumb')
<span class="text-muted mx-2">/</span><span>{{ $category->name }}</span>
@endsection

@section('content')
<div class="mb-4">
    <h1 class="fw-bold mb-2">{{ $category->name }}</h1>
    @if($category->description)
        <p class="text-muted lead">{{ $category->description }}</p>
    @endif
</div>

<div class="row g-4">
    @forelse($sections as $section)
        <div class="col-md-6">
            <div class="sec-card">
                <h4><i class="bi {{ $section->icon ?? 'bi-folder' }}"></i> {{ $section->name }}</h4>
                <div class="sec-articles">
                    @foreach($section->articles as $a)
                        <a href="{{ route('support.article', $a->slug) }}">{{ $a->title }}</a>
                    @endforeach
                </div>
                @if($section->articles_count > $section->articles->count())
                    <a href="{{ route('support.section', $section->slug) }}" class="sec-more">
                        See all {{ $section->articles_count }} articles <i class="bi bi-arrow-right"></i>
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-light text-center">No articles in this category yet.</div>
        </div>
    @endforelse
</div>
@endsection
