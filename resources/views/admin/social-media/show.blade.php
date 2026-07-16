@extends('layouts.admin')

@section('title', 'Social Media Submission')
@section('heading', 'Social Media Submission')

@section('content')
@if (session('message'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('message') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<a href="{{ route('admin.social-media.index') }}" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to list</a>

<div class="row g-4">
    {{-- ── Media ── --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0">Media</h6>
                    {!! $submission->statusBadge() !!}
                </div>

                @if ($videoUrl)
                    <video controls preload="metadata" class="w-100 rounded mb-2" style="max-height:520px; background:#000;">
                        <source src="{{ $videoUrl }}">
                        Your browser can't play this video.
                    </video>
                    <a href="{{ $videoUrl }}" download class="btn btn-sm btn-outline-primary mb-3"><i class="bi bi-download me-1"></i>Download video</a>
                @endif

                @if (count($photoUrls))
                    <div class="row g-2">
                        @foreach ($photoUrls as $url)
                            <div class="col-4 col-md-3">
                                <a href="{{ $url }}" target="_blank" rel="noopener">
                                    <img src="{{ $url }}" class="img-fluid rounded border" style="aspect-ratio:1; object-fit:cover; width:100%;" alt="submission photo">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (!$videoUrl && !count($photoUrls))
                    <p class="text-muted mb-0">No media could be loaded for this submission.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Details + actions ── --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Details</h6>
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted">Instructor</dt><dd class="col-7">{{ $submission->instructor?->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Instructor email</dt><dd class="col-7">{{ $submission->instructor?->email ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Instructor phone</dt><dd class="col-7">{{ $submission->instructor?->phone ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Learner</dt><dd class="col-7">{{ $submission->learner_name ?: '—' }}</dd>
                    <dt class="col-5 text-muted">Occasion</dt><dd class="col-7">{{ $submission->categoryLabel() }}</dd>
                    <dt class="col-5 text-muted">Test / lesson date</dt><dd class="col-7">{{ $submission->test_date?->format('j M Y') ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Submitted</dt><dd class="col-7">{{ $submission->created_at->format('j M Y, g:i a') }}</dd>
                    @if ($submission->posted_at)<dt class="col-5 text-muted">Posted</dt><dd class="col-7">{{ $submission->posted_at->format('j M Y') }}</dd>@endif
                    @if ($submission->reviewer)<dt class="col-5 text-muted">Reviewed by</dt><dd class="col-7">{{ $submission->reviewer->name }}</dd>@endif
                </dl>
                @if ($submission->caption)
                    <hr>
                    <div class="small text-muted mb-1">Instructor's words</div>
                    <p class="mb-0">{{ $submission->caption }}</p>
                @endif
            </div>
        </div>

        {{-- Suggested caption --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0">Suggested caption</h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="copy-caption"><i class="bi bi-clipboard me-1"></i>Copy</button>
                </div>
                <textarea id="caption-text" class="form-control" rows="6" readonly>{{ $submission->suggestedCaption() }}</textarea>
            </div>
        </div>

        {{-- Status update --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Update status</h6>
                <form method="POST" action="{{ route('admin.social-media.update-status', $submission) }}">
                    @csrf @method('PATCH')
                    <div class="mb-2">
                        <select name="status" class="form-select">
                            @foreach ($statusOptions as $val => $label)
                                <option value="{{ $val }}" @selected($submission->status === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <textarea name="admin_notes" class="form-control" rows="2" placeholder="Internal notes (optional)" maxlength="1000">{{ $submission->admin_notes }}</textarea>
                    </div>
                    <button class="btn btn-warning fw-bold w-100">Save</button>
                </form>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.social-media.destroy', $submission) }}" onsubmit="return confirm('Permanently delete this submission and its media?');">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-trash me-1"></i>Delete submission</button>
        </form>
    </div>
</div>

<script>
document.getElementById('copy-caption')?.addEventListener('click', function () {
    var ta = document.getElementById('caption-text');
    ta.select();
    navigator.clipboard.writeText(ta.value).then(() => {
        this.innerHTML = '<i class="bi bi-check2 me-1"></i>Copied';
        setTimeout(() => { this.innerHTML = '<i class="bi bi-clipboard me-1"></i>Copy'; }, 1800);
    }).catch(() => { document.execCommand('copy'); });
});
</script>
@endsection
