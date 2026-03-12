@extends('layouts.frontend')
@section('title', 'Blog – Driving Tips, News & Resources')

@section('content')
{{-- Hero --}}
<section style="background: var(--ez-dark); color: #fff; padding: 3rem 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-2">EzLicence Blog</h1>
                <p class="lead mb-0" style="opacity:.85;">Driving tips, industry news, learner resources and expert advice to help you on the road.</p>
            </div>
            <div class="col-lg-4">
                <form action="{{ route('blog.index') }}" method="GET" class="mt-3 mt-lg-0">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Search articles..." value="{{ request('q') }}">
                        <button class="btn btn-warning text-dark fw-semibold" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- Featured posts --}}
@if($featured->count() > 0 && !request('q') && !request('category'))
<section class="py-4" style="background:#f8f9fa;">
    <div class="container">
        <h5 class="fw-bold mb-3"><i class="bi bi-star-fill text-warning me-1"></i> Featured Articles</h5>
        <div class="row g-3">
            @foreach($featured as $fp)
            <div class="col-md-4">
                <a href="{{ route('blog.show', $fp->slug) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden" style="transition: transform .2s;">
                        @if($fp->getImageUrl())
                            <img src="{{ $fp->getImageUrl() }}" class="card-img-top" alt="{{ $fp->title }}" style="height:180px;object-fit:cover;">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center" style="height:180px;background:linear-gradient(135deg, var(--ez-dark), #34495e);">
                                <i class="bi bi-journal-text text-white" style="font-size:2.5rem;opacity:.5;"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            @if($fp->category)
                                <span class="badge bg-warning text-dark mb-2">{{ $fp->category->name }}</span>
                            @endif
                            <h6 class="card-title fw-bold text-dark mb-1">{{ $fp->title }}</h6>
                            <small class="text-muted">{{ $fp->published_at->format('d M Y') }} · {{ $fp->readTime() }} min read</small>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Main content --}}
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            {{-- Posts grid --}}
            <div class="col-lg-8">
                @if(request('q'))
                    <div class="mb-3">
                        <p class="text-muted">Showing results for "<strong>{{ request('q') }}</strong>"
                            <a href="{{ route('blog.index') }}" class="ms-2"><i class="bi bi-x-circle"></i> Clear</a>
                        </p>
                    </div>
                @endif
                @if(request('category'))
                    <div class="mb-3">
                        <p class="text-muted">Category: <strong>{{ request('category') }}</strong>
                            <a href="{{ route('blog.index') }}" class="ms-2"><i class="bi bi-x-circle"></i> All posts</a>
                        </p>
                    </div>
                @endif

                @if($posts->count() === 0)
                    <div class="text-center py-5">
                        <i class="bi bi-journal-x" style="font-size:3rem;color:#ccc;"></i>
                        <p class="text-muted mt-2">No articles found.</p>
                        <a href="{{ route('blog.index') }}" class="btn btn-outline-primary btn-sm">View all posts</a>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($posts as $post)
                        <div class="col-md-6">
                            <article class="card border-0 shadow-sm h-100 overflow-hidden">
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none">
                                    @if($post->getImageUrl())
                                        <img src="{{ $post->getImageUrl() }}" class="card-img-top" alt="{{ $post->title }}" style="height:200px;object-fit:cover;">
                                    @else
                                        <div class="card-img-top d-flex align-items-center justify-content-center" style="height:200px;background:linear-gradient(135deg, #667eea, #764ba2);">
                                            <i class="bi bi-journal-text text-white" style="font-size:3rem;opacity:.4;"></i>
                                        </div>
                                    @endif
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        @if($post->category)
                                            <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="badge bg-warning text-dark text-decoration-none">{{ $post->category->name }}</a>
                                        @endif
                                        <small class="text-muted">{{ $post->readTime() }} min read</small>
                                    </div>
                                    <h5 class="card-title fw-bold mb-2">
                                        <a href="{{ route('blog.show', $post->slug) }}" class="text-dark text-decoration-none">{{ $post->title }}</a>
                                    </h5>
                                    @if($post->excerpt)
                                        <p class="card-text text-muted small flex-grow-1">{{ Str::limit($post->excerpt, 120) }}</p>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                        <small class="text-muted">
                                            <i class="bi bi-person-circle me-1"></i>{{ $post->author?->name ?? 'Admin' }}
                                        </small>
                                        <small class="text-muted">{{ $post->published_at->format('d M Y') }}</small>
                                    </div>
                                </div>
                            </article>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Categories --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="mb-0 fw-semibold"><i class="bi bi-tags me-1"></i> Categories</h6></div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('blog.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ !request('category') ? 'active' : '' }}">
                            All Posts <span class="badge bg-primary rounded-pill">{{ $posts->total() }}</span>
                        </a>
                        @foreach($categories as $cat)
                            @if($cat->posts_count > 0)
                            <a href="{{ route('blog.index', ['category' => $cat->slug]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request('category') === $cat->slug ? 'active' : '' }}">
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
                        <p class="small mb-3" style="opacity:.85;">Find verified instructors near you and book your first lesson online.</p>
                        <a href="{{ route('find-instructor') }}" class="btn btn-warning text-dark fw-semibold btn-sm">Find an Instructor</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
