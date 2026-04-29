@extends('layouts.learner')

@section('title', 'Give Feedback')
@section('heading', 'Give Feedback')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Give Feedback</li>
    </ol>
</nav>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1"><i class="bi bi-chat-square-heart me-2 text-primary"></i>We'd love to hear from you</h5>
                <p class="text-muted small mb-4">Found a bug? Got an idea? Just want to say thanks? Drop us a line — we read every message.</p>

                @if ($errors->any())
                    <div class="alert alert-danger small">
                        <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('learner.feedback.store') }}">
                    @csrf
                    <input type="hidden" name="page_context" value="{{ url()->previous() }}">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">What's it about? <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            @foreach($categories as $value => $label)
                                <div class="col-sm-6">
                                    <input type="radio" class="btn-check" name="category" id="cat-{{ $value }}" value="{{ $value }}" {{ old('category') === $value ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-secondary w-100 text-start" for="cat-{{ $value }}">
                                        @php
                                            $iconMap = [
                                                'bug' => 'bug',
                                                'suggestion' => 'lightbulb',
                                                'compliment' => 'heart',
                                                'complaint' => 'exclamation-triangle',
                                                'other' => 'three-dots',
                                            ];
                                        @endphp
                                        <i class="bi bi-{{ $iconMap[$value] ?? 'circle' }} me-2"></i>{{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Overall, how would you rate Secure Licences? <span class="text-muted small">(optional)</span></label>
                        <div class="star-rating-input d-flex gap-2 align-items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <input type="radio" name="rating" id="star-{{ $i }}" value="{{ $i }}" {{ (string) old('rating') === (string) $i ? 'checked' : '' }} class="d-none">
                                <label for="star-{{ $i }}" class="star-label" data-rating="{{ $i }}" style="cursor:pointer;font-size:2rem;color:#d1d0cc;transition:all 0.15s;">
                                    <i class="bi bi-star-fill"></i>
                                </label>
                            @endfor
                            <span class="ms-2 small text-muted" id="rating-text">Click a star to rate</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Your message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="6" maxlength="2000" required minlength="10" placeholder="Be as detailed as you like — the more we know, the better we can help.">{{ old('message') }}</textarea>
                        <small class="text-muted">10–2000 characters</small>
                    </div>

                    <div class="alert alert-info small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Your feedback goes directly to our team. For urgent booking issues, please use <a href="{{ route('learner.support') }}">Support</a> for faster help.
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-warning fw-bold px-4">
                            <i class="bi bi-send me-1"></i>Send Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-stars text-warning me-1"></i>What kind of feedback helps most?</h6>
                <div class="small text-muted">
                    <div class="d-flex gap-2 mb-2"><i class="bi bi-bug text-danger flex-shrink-0 mt-1"></i><div><strong class="text-dark">Bugs:</strong> tell us what page, what you did, what happened.</div></div>
                    <div class="d-flex gap-2 mb-2"><i class="bi bi-lightbulb text-warning flex-shrink-0 mt-1"></i><div><strong class="text-dark">Suggestions:</strong> what would make your experience better?</div></div>
                    <div class="d-flex gap-2 mb-2"><i class="bi bi-heart text-danger flex-shrink-0 mt-1"></i><div><strong class="text-dark">Compliments:</strong> we love hearing what's working — share with the team!</div></div>
                    <div class="d-flex gap-2"><i class="bi bi-exclamation-triangle text-warning flex-shrink-0 mt-1"></i><div><strong class="text-dark">Complaints:</strong> we want to make it right — we'll personally follow up.</div></div>
                </div>
            </div>
        </div>

        @if($myFeedback->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Your recent feedback</h6>
                    <div class="list-group list-group-flush small">
                        @foreach($myFeedback as $fb)
                            <div class="list-group-item border-0 px-0 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-light text-dark border">{{ $categories[$fb->category] ?? ucfirst($fb->category) }}</span>
                                        <span class="text-muted ms-1" style="font-size:0.75rem;">{{ $fb->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($fb->status === 'resolved')
                                        <span class="badge bg-success-subtle text-success">Resolved</span>
                                    @elseif($fb->status === 'reviewing')
                                        <span class="badge bg-info-subtle text-info">Reviewing</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">Received</span>
                                    @endif
                                </div>
                                <p class="mb-1 mt-1 small text-muted text-truncate" title="{{ $fb->message }}">{{ \Illuminate\Support\Str::limit($fb->message, 80) }}</p>
                                @if($fb->admin_response)
                                    <div class="mt-2 p-2 small rounded" style="background:#fffbeb;border-left:3px solid var(--sl-accent-500);">
                                        <strong>Reply:</strong> {{ $fb->admin_response }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Star rating interactivity
(function() {
    const labels = document.querySelectorAll('.star-label');
    const text = document.getElementById('rating-text');
    const ratingTexts = ['', 'Hated it', 'Disliked', 'It\'s OK', 'Liked it', 'Loved it!'];

    function paint(rating) {
        labels.forEach(l => {
            const r = parseInt(l.getAttribute('data-rating'), 10);
            l.style.color = r <= rating ? '#ffd500' : '#d1d0cc';
            l.style.transform = r === rating ? 'scale(1.15)' : 'scale(1)';
        });
        if (text) text.textContent = ratingTexts[rating] || 'Click a star to rate';
    }

    // Pre-paint if already selected
    const checked = document.querySelector('input[name="rating"]:checked');
    if (checked) paint(parseInt(checked.value, 10));

    labels.forEach(l => {
        l.addEventListener('mouseenter', () => paint(parseInt(l.getAttribute('data-rating'), 10)));
        l.addEventListener('click', () => {
            const r = parseInt(l.getAttribute('data-rating'), 10);
            const radio = document.getElementById('star-' + r);
            if (radio) radio.checked = true;
            paint(r);
        });
    });

    // Reset to selected on mouseleave
    document.querySelector('.star-rating-input').addEventListener('mouseleave', () => {
        const c = document.querySelector('input[name="rating"]:checked');
        paint(c ? parseInt(c.value, 10) : 0);
    });
})();
</script>
@endsection
