@extends('layouts.frontend')
@section('title', 'Industry Insights')
@section('content')
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li><li class="breadcrumb-item active">Industry Insights</li></ol></nav>
    </div>
</div>
<section class="py-5">
    <div class="container">
        <h1 class="display-6 fw-bold mb-3 text-center" style="color: var(--ez-dark);">Industry Insights</h1>
        <p class="lead text-muted text-center mb-5">Stay informed with the latest news, trends, and insights from the Australian driving instruction industry.</p>

        @php
            $insights = \App\Models\BlogPost::published()->whereHas('category', fn($q) => $q->where('slug', 'industry-insights'))->orderByDesc('published_at')->paginate(9);
        @endphp

        @if($insights->count() > 0)
            <div class="row g-4">
                @foreach($insights as $post)
                <div class="col-md-6 col-lg-4">
                    <article class="card border-0 shadow-sm h-100 overflow-hidden">
                        <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none">
                            @if($post->getImageUrl())
                                <img src="{{ $post->getImageUrl() }}" class="card-img-top" alt="{{ $post->title }}" style="height:200px;object-fit:cover;">
                            @else
                                <div class="card-img-top d-flex align-items-center justify-content-center" style="height:200px;background:linear-gradient(135deg, var(--ez-dark), #34495e);">
                                    <i class="bi bi-graph-up text-white" style="font-size:3rem;opacity:.4;"></i>
                                </div>
                            @endif
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold mb-2">
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-dark text-decoration-none">{{ $post->title }}</a>
                            </h5>
                            @if($post->excerpt)
                                <p class="card-text text-muted small flex-grow-1">{{ Str::limit($post->excerpt, 120) }}</p>
                            @endif
                            <small class="text-muted">{{ $post->published_at->format('d M Y') }} · {{ $post->readTime() }} min read</small>
                        </div>
                    </article>
                </div>
                @endforeach
            </div>
            <div class="mt-4 d-flex justify-content-center">{{ $insights->links() }}</div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-graph-up" style="font-size:3rem;color:#ccc;"></i>
                <p class="text-muted mt-3">Industry insights articles are coming soon. Check back later or visit our <a href="{{ route('blog.index') }}">blog</a> for the latest posts.</p>
            </div>
        @endif
    </div>
</section>
@endsection
