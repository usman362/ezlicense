@extends('support.layout')

@section('title', $article->title)
@section('meta_description', $article->meta_description ?? $article->excerpt)

@section('breadcrumb')
<span class="text-muted mx-2">/</span><a href="{{ route('support.category', $article->section->category->slug) }}">{{ $article->section->category->name }}</a>
<span class="text-muted mx-2">/</span><a href="{{ route('support.section', $article->section->slug) }}">{{ $article->section->name }}</a>
<span class="text-muted mx-2">/</span><span class="text-truncate" style="max-width:280px;display:inline-block;vertical-align:bottom;">{{ $article->title }}</span>
@endsection

@section('content')
<div class="row">
    {{-- Main article --}}
    <div class="col-lg-8">
        <article class="article-body">
            <h1 class="mb-3" style="font-size:28px; font-weight:800;">{{ $article->title }}</h1>
            <div class="text-muted small mb-4">
                <i class="bi bi-eye"></i> {{ number_format($article->views_count) }} views ·
                Updated {{ $article->updated_at->format('j M Y') }}
            </div>

            {!! $article->content !!}

            {{-- Helpful feedback widget --}}
            <div class="feedback-box" id="feedback-widget">
                <h6>Was this article helpful?</h6>
                <button type="button" class="btn btn-outline-success me-2" data-helpful="1">
                    <i class="bi bi-hand-thumbs-up"></i> Yes
                </button>
                <button type="button" class="btn btn-outline-danger" data-helpful="0">
                    <i class="bi bi-hand-thumbs-down"></i> No
                </button>
                <div class="text-muted small mt-2">
                    {{ $article->helpful_yes_count + $article->helpful_no_count }} {{ Str::plural('person', $article->helpful_yes_count + $article->helpful_no_count) }} found this helpful
                </div>
            </div>
        </article>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4 mt-4 mt-lg-0">
        @if($related->isNotEmpty())
            <div class="sec-card mb-3">
                <h4><i class="bi bi-bookmark"></i> Related articles</h4>
                <div class="sec-articles">
                    @foreach($related as $r)
                        <a href="{{ route('support.article', $r->slug) }}">{{ $r->title }}</a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="sec-card" style="background:#fff8e1; border-color:var(--sl-yellow);">
            <h4><i class="bi bi-envelope-fill"></i> Still need help?</h4>
            <p class="small text-muted">Can't find what you're looking for? Our support team is here to help.</p>
            <a href="{{ route('support.request.show') }}" class="btn btn-warning fw-bold w-100">Contact Us</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('#feedback-widget [data-helpful]').forEach(btn => {
    btn.addEventListener('click', async () => {
        const isHelpful = btn.dataset.helpful;
        btn.disabled = true;
        try {
            const r = await fetch('{{ route('support.article.feedback', $article->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({ is_helpful: isHelpful }),
            });
            const d = await r.json();
            document.getElementById('feedback-widget').innerHTML =
                d.already_voted
                    ? '<h6>You\'ve already voted on this article. Thanks!</h6>'
                    : '<h6>Thanks for your feedback! 🙏</h6><div class="small text-muted">We use this to improve our help articles.</div>';
        } catch (e) {
            btn.disabled = false;
            alert('Could not submit feedback. Please try again.');
        }
    });
});
</script>
@endpush
@endsection
