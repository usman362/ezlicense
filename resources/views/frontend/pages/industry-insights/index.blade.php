@extends('layouts.frontend')
@section('title', 'Industry Insights — Driving Industry News, Trends & Analysis | Secure Licence')

@section('content')

{{-- ─────────── HERO (driver photo background) ─────────── --}}
<section class="blog-hero">
    <div class="blog-hero-bg">
        <img src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=2000&q=80&auto=format&fit=crop"
             srcset="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=1200&q=80&auto=format&fit=crop 1200w,
                     https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=2000&q=80&auto=format&fit=crop 2000w"
             alt="">
        <div class="blog-hero-overlay"></div>
    </div>
    <div class="container blog-hero-inner">
        <nav aria-label="breadcrumb" class="blog-hero-breadcrumb mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Industry Insights</li>
            </ol>
        </nav>
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="blog-hero-eyebrow"><i class="bi bi-graph-up-arrow me-1"></i>Market Trends · Analysis · Reports</span>
                <h1 class="blog-hero-title">
                    Industry <span class="blog-hero-title-accent">Insights</span>
                </h1>
                <p class="blog-hero-sub">
                    Stay informed with the latest news, market trends, and expert analysis from across Australia's driving instruction industry.
                </p>
                <form action="{{ route('industry-insights') }}" method="GET" class="blog-hero-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" class="form-control" placeholder="Search insights, reports, trends…" value="{{ request('q') }}">
                    <button type="submit" class="btn btn-warning fw-bold">Search</button>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── CATEGORY PILLS ─────────── --}}
<section class="blog-cat-bar">
    <div class="container">
        <div class="blog-cat-pills">
            <a href="{{ route('industry-insights') }}" class="blog-cat-pill {{ ! request('category') ? 'active' : '' }}">
                <i class="bi bi-grid-fill me-1"></i>All Insights
                <span class="blog-cat-count">{{ $posts->total() }}</span>
            </a>
            @foreach($categories as $cat)
                @if($cat->insights_count > 0)
                    <a href="{{ route('industry-insights', ['category' => $cat->slug]) }}"
                       class="blog-cat-pill {{ request('category') === $cat->slug ? 'active' : '' }}">
                        {{ $cat->name }}
                        <span class="blog-cat-count">{{ $cat->insights_count }}</span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── FEATURED INSIGHTS (only on landing without filters) ─────────── --}}
@if($featured->count() > 0 && !request('q') && !request('category'))
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="blog-eyebrow"><i class="bi bi-star-fill me-1"></i>Spotlight</span>
                <h2 class="cl-section-title mb-0">Featured Insights</h2>
            </div>
            <a href="#latest" class="text-decoration-none fw-semibold text-warning-emphasis small">Browse all →</a>
        </div>

        <div class="row g-4">
            @foreach($featured as $idx => $fp)
                @if($idx === 0)
                    <div class="col-lg-7">
                        <a href="{{ route('industry-insights.show', $fp->slug) }}" class="blog-feature-card blog-feature-card-lg">
                            <div class="blog-feature-img">
                                @if($fp->getImageUrl())
                                    <img src="{{ $fp->getImageUrl() }}" alt="{{ $fp->title }}" loading="lazy">
                                @else
                                    <div class="blog-img-placeholder"><i class="bi bi-graph-up"></i></div>
                                @endif
                                @if($fp->category)
                                    <span class="blog-feature-tag">{{ $fp->category->name }}</span>
                                @endif
                            </div>
                            <div class="blog-feature-body">
                                <h3>{{ $fp->title }}</h3>
                                @if($fp->excerpt)
                                    <p class="blog-feature-excerpt">{{ Str::limit($fp->excerpt, 140) }}</p>
                                @endif
                                <div class="blog-meta">
                                    <span><i class="bi bi-person-circle me-1"></i>{{ $fp->author?->name ?? 'Admin' }}</span>
                                    <span class="blog-meta-dot">·</span>
                                    <span><i class="bi bi-calendar3 me-1"></i>{{ $fp->published_at->format('d M Y') }}</span>
                                    <span class="blog-meta-dot">·</span>
                                    <span><i class="bi bi-clock me-1"></i>{{ $fp->readTime() }} min read</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach

            <div class="col-lg-5">
                <div class="d-flex flex-column gap-4 h-100">
                    @foreach($featured->skip(1)->take(2) as $fp)
                        <a href="{{ route('industry-insights.show', $fp->slug) }}" class="blog-feature-card blog-feature-card-sm">
                            <div class="blog-feature-img-sm">
                                @if($fp->getImageUrl())
                                    <img src="{{ $fp->getImageUrl() }}" alt="{{ $fp->title }}" loading="lazy">
                                @else
                                    <div class="blog-img-placeholder"><i class="bi bi-graph-up"></i></div>
                                @endif
                            </div>
                            <div class="blog-feature-body-sm">
                                @if($fp->category)
                                    <span class="ilc-blog-tag">{{ $fp->category->name }}</span>
                                @endif
                                <h4>{{ $fp->title }}</h4>
                                <div class="blog-meta small">
                                    <span>{{ $fp->published_at->format('d M Y') }}</span>
                                    <span class="blog-meta-dot">·</span>
                                    <span>{{ $fp->readTime() }} min read</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ─────────── LATEST GRID ─────────── --}}
<section id="latest" class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="blog-eyebrow"><i class="bi bi-newspaper me-1"></i>Fresh analysis</span>
                <h2 class="cl-section-title mb-0">
                    @if(request('q'))
                        Results for "{{ request('q') }}"
                    @elseif(request('category'))
                        {{ optional($categories->firstWhere('slug', request('category')))->name ?? 'Category' }}
                    @else
                        Latest Insights
                    @endif
                </h2>
            </div>
            @if(request('q') || request('category'))
                <a href="{{ route('industry-insights') }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                    <i class="bi bi-x-circle me-1"></i>Clear filters
                </a>
            @endif
        </div>

        @if($posts->count() === 0)
            <div class="blog-empty">
                <i class="bi bi-graph-up"></i>
                <h3>No insights found</h3>
                <p>Try a different search term or browse all insights.</p>
                <a href="{{ route('industry-insights') }}" class="btn btn-warning fw-bold">View all insights</a>
            </div>
        @else
            <div class="row g-4">
                @foreach($posts as $post)
                    <div class="col-md-6 col-lg-4">
                        <article class="blog-card">
                            <a href="{{ route('industry-insights.show', $post->slug) }}" class="blog-card-img-link">
                                <div class="blog-card-img">
                                    @if($post->getImageUrl())
                                        <img src="{{ $post->getImageUrl() }}" alt="{{ $post->title }}" loading="lazy">
                                    @else
                                        <div class="blog-img-placeholder"><i class="bi bi-graph-up"></i></div>
                                    @endif
                                    @if($post->category)
                                        <span class="blog-card-tag">{{ $post->category->name }}</span>
                                    @endif
                                </div>
                            </a>
                            <div class="blog-card-body">
                                <h3 class="blog-card-title">
                                    <a href="{{ route('industry-insights.show', $post->slug) }}">{{ $post->title }}</a>
                                </h3>
                                @if($post->excerpt)
                                    <p class="blog-card-excerpt">{{ Str::limit($post->excerpt, 100) }}</p>
                                @endif
                                <div class="blog-card-meta">
                                    <span class="blog-card-author">
                                        <span class="blog-card-avatar">{{ strtoupper(substr($post->author?->name ?? 'A', 0, 1)) }}</span>
                                        {{ $post->author?->name ?? 'Admin' }}
                                    </span>
                                    <span class="blog-card-date">
                                        {{ $post->published_at->format('d M Y') }} · {{ $post->readTime() }} min
                                    </span>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center blog-pagination">
                {{ $posts->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</section>

{{-- ─────────── BOTTOM CTA ─────────── --}}
<section class="py-5 blog-cta-section">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-7">
                <h2 class="mb-2 fw-bolder text-dark">Ready to start driving?</h2>
                <p class="mb-0 text-dark">Find verified driving instructors near you and book your first lesson online — no booking fees, ever.</p>
            </div>
            <div class="col-md-5 text-md-end">
                <a href="{{ route('find-instructor') }}" class="btn btn-dark fw-bold btn-lg px-4">
                    <i class="bi bi-search me-2"></i>Find an Instructor
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
