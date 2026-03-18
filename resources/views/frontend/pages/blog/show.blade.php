@extends('layouts.frontend')
@section('title', ($post->meta_title ?: $post->title) . ' – Secure Licences Blog')

@section('content')
{{-- Breadcrumb --}}
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
                @if($post->category)
                    <li class="breadcrumb-item"><a href="{{ route('blog.index', ['category' => $post->category->slug]) }}">{{ $post->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ Str::limit($post->title, 40) }}</li>
            </ol>
        </nav>
    </div>
</div>

<article class="py-5">
    <div class="container">
        <div class="row g-4">
            {{-- Main content --}}
            <div class="col-lg-8">
                {{-- Header --}}
                <header class="mb-4">
                    @if($post->category)
                        <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="badge bg-warning text-dark text-decoration-none mb-2">{{ $post->category->name }}</a>
                    @endif
                    <h1 class="display-6 fw-bold mb-3" style="color: var(--ez-dark);">{{ $post->title }}</h1>
                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted small mb-3">
                        <span><i class="bi bi-person-circle me-1"></i>{{ $post->author?->name ?? 'Admin' }}</span>
                        <span><i class="bi bi-calendar3 me-1"></i>{{ $post->published_at->format('d F Y') }}</span>
                        <span><i class="bi bi-clock me-1"></i>{{ $post->readTime() }} min read</span>
                        <span><i class="bi bi-eye me-1"></i>{{ number_format($post->views) }} views</span>
                    </div>
                    @if($post->excerpt)
                        <p class="lead" style="color:#555;">{{ $post->excerpt }}</p>
                    @endif
                </header>

                {{-- Featured image --}}
                @if($post->getImageUrl())
                    <div class="mb-4">
                        <img src="{{ $post->getImageUrl() }}" class="img-fluid rounded shadow-sm w-100" alt="{{ $post->title }}" style="max-height:450px;object-fit:cover;">
                    </div>
                @endif

                {{-- Body --}}
                <div class="blog-content mb-5" style="font-size:1.05rem;line-height:1.8;color:#333;">
                    {!! nl2br(e($post->body)) !!}
                </div>

                {{-- Share --}}
                <div class="border-top border-bottom py-3 mb-5 d-flex flex-wrap justify-content-between align-items-center">
                    <span class="text-muted small fw-semibold">Share this article:</span>
                    <div class="d-flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary"><i class="bi bi-facebook me-1"></i>Facebook</a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info"><i class="bi bi-twitter-x me-1"></i>Twitter</a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary"><i class="bi bi-linkedin me-1"></i>LinkedIn</a>
                    </div>
                </div>

                {{-- Related posts --}}
                @if($related->count() > 0)
                <section>
                    <h4 class="fw-bold mb-3">Related Articles</h4>
                    <div class="row g-3">
                        @foreach($related as $rp)
                        <div class="col-md-4">
                            <a href="{{ route('blog.show', $rp->slug) }}" class="text-decoration-none">
                                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                                    @if($rp->getImageUrl())
                                        <img src="{{ $rp->getImageUrl() }}" class="card-img-top" alt="{{ $rp->title }}" style="height:140px;object-fit:cover;">
                                    @else
                                        <div class="card-img-top d-flex align-items-center justify-content-center" style="height:140px;background:linear-gradient(135deg, #667eea, #764ba2);">
                                            <i class="bi bi-journal-text text-white" style="font-size:2rem;opacity:.4;"></i>
                                        </div>
                                    @endif
                                    <div class="card-body py-2">
                                        <h6 class="card-title fw-bold text-dark mb-1" style="font-size:.9rem;">{{ Str::limit($rp->title, 50) }}</h6>
                                        <small class="text-muted">{{ $rp->published_at->format('d M Y') }}</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </section>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Categories --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="mb-0 fw-semibold"><i class="bi bi-tags me-1"></i> Categories</h6></div>
                    <div class="list-group list-group-flush">
                        @foreach($categories as $cat)
                            @if($cat->posts_count > 0)
                            <a href="{{ route('blog.index', ['category' => $cat->slug]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center small">
                                {{ $cat->name }} <span class="badge bg-secondary rounded-pill">{{ $cat->posts_count }}</span>
                            </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- CTA --}}
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, var(--ez-dark), #34495e); color: #fff;">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-car-front-fill" style="font-size:2rem;color:var(--ez-accent);"></i>
                        <h6 class="fw-bold mt-2">Ready to Start Driving?</h6>
                        <p class="small mb-3" style="opacity:.85;">Find verified instructors near you and book your first lesson.</p>
                        <a href="{{ route('find-instructor') }}" class="btn btn-warning text-dark fw-semibold btn-sm">Find an Instructor</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
@endsection
