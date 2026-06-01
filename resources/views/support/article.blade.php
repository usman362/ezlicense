@extends('support.layout')

@section('title', $article->title)
@section('meta_description', $article->meta_description ?? $article->excerpt)

@section('subnav')
    <ol class="breadcrumbs">
        <li><a href="{{ route('support.home') }}">Secure Licence Support</a></li>
        <li><a href="{{ route('support.category', $article->section->category->slug) }}">{{ $article->section->category->name }}</a></li>
        <li><a href="{{ route('support.section', $article->section->slug) }}">{{ $article->section->name }}</a></li>
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
    <div class="article-container">
        {{-- ── Left sidebar: Articles in this section ── --}}
        <aside class="article-sidebar">
            <h3>Articles in this section</h3>
            <ul>
                @foreach($article->section->articles as $sibling)
                    <li>
                        <a href="{{ route('support.article', $sibling->slug) }}"
                           class="{{ $sibling->id === $article->id ? 'current' : '' }}">
                            {{ $sibling->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        {{-- ── Right: article content ── --}}
        <div class="article-content">
            <h1>{{ $article->title }}</h1>
            <div class="article-meta">
                {{ number_format($article->views_count) }} {{ Str::plural('view', $article->views_count) }} ·
                Updated {{ $article->updated_at->format('j M Y') }}
            </div>

            <div class="article-body">
                {!! $article->content !!}
            </div>

            {{-- Feedback widget --}}
            <div class="feedback-widget" id="feedback-widget">
                <div class="feedback-title">Was this article helpful?</div>
                <button type="button" data-helpful="1">
                    <i class="bi bi-hand-thumbs-up"></i> Yes
                </button>
                <button type="button" data-helpful="0">
                    <i class="bi bi-hand-thumbs-down"></i> No
                </button>
                <div class="feedback-count">
                    {{ $article->helpful_yes_count + $article->helpful_no_count }} {{ Str::plural('person', $article->helpful_yes_count + $article->helpful_no_count) }} found this helpful
                </div>
            </div>

            {{-- "Still need help?" --}}
            <div style="margin-top: 32px; padding: 20px 24px; background: #fff8e1; border: 1px solid #ffe588; border-radius: 6px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <strong>Still need help?</strong>
                        <div style="color: var(--sl-text-muted); font-size: 14px; margin-top: 2px;">Our team responds within 1 business day.</div>
                    </div>
                    <a href="{{ route('support.request.show') }}" class="submit-a-request" style="background: var(--sl-yellow); color: var(--sl-ink); padding: 9px 18px; border-radius: 999px; font-weight: 700; font-size: 14px; text-decoration: none;">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('#feedback-widget [data-helpful]').forEach(btn => {
    btn.addEventListener('click', async () => {
        const isHelpful = btn.dataset.helpful;
        document.querySelectorAll('#feedback-widget button').forEach(b => b.disabled = true);
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
                    ? '<div class="feedback-title">You\'ve already voted on this article. Thanks!</div>'
                    : '<div class="feedback-title">Thanks for your feedback!</div><div class="feedback-count">We use this to improve our help articles.</div>';
        } catch (e) {
            document.querySelectorAll('#feedback-widget button').forEach(b => b.disabled = false);
            alert('Could not submit feedback. Please try again.');
        }
    });
});
</script>
@endpush
@endsection
