@extends('layouts.frontend')
@section('title', ($post->meta_title ?: $post->title) . ' — Industry Insights | Secure Licences')
@section('meta_description', $post->meta_description ?: Str::limit(strip_tags($post->excerpt ?: $post->body), 160))

@section('content')

{{-- ─────────── ARTICLE HERO ─────────── --}}
<section class="blog-show-hero">
    <div class="container">
        <nav aria-label="breadcrumb" class="cl-hero-breadcrumb mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('industry-insights') }}">Industry Insights</a></li>
                @if($post->category)
                    <li class="breadcrumb-item"><a href="{{ route('industry-insights', ['category' => $post->category->slug]) }}">{{ $post->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ Str::limit($post->title, 50) }}</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                @if($post->category)
                    <a href="{{ route('industry-insights', ['category' => $post->category->slug]) }}" class="blog-show-tag">
                        <i class="bi bi-tag-fill me-1"></i>{{ $post->category->name }}
                    </a>
                @endif

                <h1 class="blog-show-title">{{ $post->title }}</h1>

                @if($post->excerpt)
                    <p class="blog-show-excerpt">{{ $post->excerpt }}</p>
                @endif

                <div class="blog-show-meta">
                    <div class="blog-show-author">
                        <span class="blog-card-avatar">{{ strtoupper(substr($post->author?->name ?? 'A', 0, 1)) }}</span>
                        <div>
                            <strong>{{ $post->author?->name ?? 'Admin' }}</strong>
                            <span>Author</span>
                        </div>
                    </div>
                    <span class="blog-show-meta-divider"></span>
                    <div class="blog-show-meta-item">
                        <i class="bi bi-calendar3"></i>
                        <span>{{ $post->published_at->format('d M Y') }}</span>
                    </div>
                    <div class="blog-show-meta-item">
                        <i class="bi bi-clock"></i>
                        <span>{{ $post->readTime() }} min read</span>
                    </div>
                    <div class="blog-show-meta-item">
                        <i class="bi bi-eye"></i>
                        <span>{{ number_format($post->views) }} views</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── FEATURED IMAGE ─────────── --}}
@if($post->getImageUrl())
<div class="container blog-show-image-wrap">
    <img src="{{ $post->getImageUrl() }}" class="blog-show-image" alt="{{ $post->title }}">
</div>
@endif

{{-- ─────────── ARTICLE BODY + SIDEBAR ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <article class="blog-prose">
                    {!! nl2br(e($post->body)) !!}
                </article>

                {{-- Share row --}}
                <div class="blog-share">
                    <div class="blog-share-label">
                        <i class="bi bi-share-fill"></i>
                        <span>Share this insight</span>
                    </div>
                    <div class="blog-share-btns">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener" class="blog-share-btn blog-share-fb" aria-label="Share on Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="blog-share-btn blog-share-tw" aria-label="Share on X (Twitter)">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="blog-share-btn blog-share-li" aria-label="Share on LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' — ' . request()->url()) }}" target="_blank" rel="noopener" class="blog-share-btn blog-share-wa" aria-label="Share on WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                        <button type="button" class="blog-share-btn blog-share-copy" onclick="navigator.clipboard.writeText(window.location.href).then(function(){var b=event.currentTarget;b.classList.add('copied');setTimeout(function(){b.classList.remove('copied');},1800);}.bind(this))" aria-label="Copy link">
                            <i class="bi bi-link-45deg"></i>
                            <span class="blog-share-copied">Copied!</span>
                        </button>
                    </div>
                </div>

                {{-- Author bio card --}}
                <div class="blog-author-card">
                    <div class="blog-author-avatar">{{ strtoupper(substr($post->author?->name ?? 'A', 0, 1)) }}</div>
                    <div>
                        <h4>About {{ $post->author?->name ?? 'Admin' }}</h4>
                        <p class="mb-0">Contributor at Secure Licences — sharing market analysis, industry trends and expert commentary from across Australia's driving instructor community.</p>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="blog-sidebar">
                    <div class="blog-side-card">
                        <h5><i class="bi bi-tags me-2"></i>Categories</h5>
                        <ul class="blog-side-cats">
                            @foreach($categories as $cat)
                                @if($cat->insights_count > 0)
                                    <li>
                                        <a href="{{ route('industry-insights', ['category' => $cat->slug]) }}"
                                           class="{{ $post->category && $post->category->slug === $cat->slug ? 'active' : '' }}">
                                            <span>{{ $cat->name }}</span>
                                            <span class="blog-side-count">{{ $cat->insights_count }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>

                    <div class="blog-side-card blog-side-newsletter">
                        <i class="bi bi-envelope-paper-heart-fill"></i>
                        <h5>Industry updates in your inbox</h5>
                        <p>Subscribe to get fresh market trends, reports and analysis from the driving industry.</p>
                        <form onsubmit="event.preventDefault(); this.querySelector('input').value=''; alert('Thanks for subscribing!');">
                            <input type="email" class="form-control mb-2" placeholder="you@email.com" required>
                            <button type="submit" class="btn btn-warning fw-bold w-100">Subscribe</button>
                        </form>
                    </div>

                    <div class="blog-side-card blog-side-cta">
                        <i class="bi bi-car-front-fill"></i>
                        <h5>Ready to drive?</h5>
                        <p>Find verified driving instructors near you and book your first lesson online.</p>
                        <a href="{{ route('find-instructor') }}" class="btn btn-warning fw-bold w-100">Find an Instructor</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── RELATED ─────────── --}}
@if($related->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <span class="blog-eyebrow"><i class="bi bi-collection me-1"></i>You might also like</span>
            <h2 class="cl-section-title">Related Insights</h2>
        </div>
        <div class="row g-4">
            @foreach($related as $rp)
                <div class="col-md-6 col-lg-4">
                    <article class="blog-card">
                        <a href="{{ route('industry-insights.show', $rp->slug) }}" class="blog-card-img-link">
                            <div class="blog-card-img">
                                @if($rp->getImageUrl())
                                    <img src="{{ $rp->getImageUrl() }}" alt="{{ $rp->title }}" loading="lazy">
                                @else
                                    <div class="blog-img-placeholder"><i class="bi bi-graph-up"></i></div>
                                @endif
                                @if($rp->category)
                                    <span class="blog-card-tag">{{ $rp->category->name }}</span>
                                @endif
                            </div>
                        </a>
                        <div class="blog-card-body">
                            <h3 class="blog-card-title">
                                <a href="{{ route('industry-insights.show', $rp->slug) }}">{{ $rp->title }}</a>
                            </h3>
                            @if($rp->excerpt)
                                <p class="blog-card-excerpt">{{ Str::limit($rp->excerpt, 90) }}</p>
                            @endif
                            <div class="blog-card-meta">
                                <span class="blog-card-author">
                                    <span class="blog-card-avatar">{{ strtoupper(substr($rp->author?->name ?? 'A', 0, 1)) }}</span>
                                    {{ $rp->author?->name ?? 'Admin' }}
                                </span>
                                <span class="blog-card-date">
                                    {{ $rp->published_at->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('industry-insights') }}" class="btn btn-outline-warning fw-bold">
                <i class="bi bi-arrow-right me-1"></i>Browse all insights
            </a>
        </div>
    </div>
</section>
@endif

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
