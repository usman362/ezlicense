@extends('layouts.instructor')

@section('title', 'Marketing / Social Media')
@section('heading', 'Marketing / Social Media')

@section('content')
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item active">Marketing / Social Media</li>
    </ol>
</nav>

<div class="mb-3">
    <h4 class="fw-bolder mb-0"><i class="bi bi-megaphone text-warning me-2"></i>Share a learner win</h4>
    <p class="text-muted small mb-0">Uploaded a learner just passed their test? Send us a short video (max 45 seconds) and a few photos — our team will post it on Secure Licence's social media. Great free advertising for you!</p>
</div>

@if (session('message'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i>{{ session('message') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-4">
    {{-- ── Upload form ── --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">New submission</h6>
                <form method="POST" action="{{ route('instructor.social-media.store') }}" enctype="multipart/form-data" id="sm-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Learner's name</label>
                        <input type="text" name="learner_name" value="{{ old('learner_name') }}" class="form-control" placeholder="e.g. Sarah M." maxlength="120">
                    </div>
                    <div class="row g-2">
                        <div class="col-sm-6 mb-3">
                            <label class="form-label small fw-semibold">Occasion</label>
                            <select name="category" class="form-select">
                                @foreach ($categories as $val => $label)
                                    <option value="{{ $val }}" @selected(old('category') === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="form-label small fw-semibold">Test / lesson date</label>
                            <input type="date" name="test_date" value="{{ old('test_date') }}" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">A few words <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea name="caption" class="form-control" rows="3" maxlength="1500" placeholder="e.g. Huge congratulations to Sarah for passing first go! Great effort over 10 lessons.">{{ old('caption') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Video <span class="text-muted fw-normal">(MP4/MOV/WEBM, max 45 seconds)</span></label>
                        <input type="file" name="video" id="sm-video" class="form-control" accept="video/mp4,video/quicktime,video/webm">
                        <div class="form-text" id="sm-video-hint">Keep it short and vertical for reels/stories.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Photos <span class="text-muted fw-normal">(up to 6, optional)</span></label>
                        <input type="file" name="photos[]" id="sm-photos" class="form-control" accept="image/jpeg,image/png,image/webp" multiple>
                    </div>

                    <button type="submit" class="btn btn-warning fw-bold" id="sm-submit"><i class="bi bi-upload me-1"></i>Send to our team</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Past submissions ── --}}
    <div class="col-lg-6">
        <h6 class="fw-bold mb-3">Your submissions</h6>
        @forelse ($submissions as $s)
            <div class="card border-0 shadow-sm mb-2">
                <div class="card-body d-flex align-items-start gap-3">
                    <div class="text-warning fs-4"><i class="bi bi-{{ $s->video_path ? 'camera-video' : 'images' }}"></i></div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="fw-semibold">{{ $s->learner_name ?: 'Learner' }} <span class="text-muted small">· {{ $s->categoryLabel() }}</span></div>
                            {!! $s->statusBadge() !!}
                        </div>
                        @if ($s->caption)<div class="small text-muted text-truncate">{{ $s->caption }}</div>@endif
                        <div class="small text-muted mt-1">
                            {{ $s->created_at->format('j M Y') }}
                            @if ($s->video_path) · <i class="bi bi-camera-video"></i> video @endif
                            @if ($s->photo_paths) · <i class="bi bi-images"></i> {{ count($s->photo_paths) }} photo(s) @endif
                        </div>
                    </div>
                    @if ($s->status === \App\Models\SocialMediaSubmission::STATUS_PENDING)
                        <form method="POST" action="{{ route('instructor.social-media.destroy', $s) }}" onsubmit="return confirm('Remove this submission?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Remove"><i class="bi bi-trash"></i></button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-muted small">No submissions yet — upload your first learner win above.</div>
        @endforelse
        <div class="mt-2">{{ $submissions->links() }}</div>
    </div>
</div>

<script>
(function () {
    var MAX_SECONDS = 45;
    var video = document.getElementById('sm-video');
    var hint = document.getElementById('sm-video-hint');
    var submit = document.getElementById('sm-submit');
    if (!video) return;

    video.addEventListener('change', function () {
        hint.classList.remove('text-danger');
        if (!video.files || !video.files.length) return;
        var file = video.files[0];
        var url = URL.createObjectURL(file);
        var probe = document.createElement('video');
        probe.preload = 'metadata';
        probe.onloadedmetadata = function () {
            URL.revokeObjectURL(url);
            if (probe.duration && probe.duration > MAX_SECONDS + 0.5) {
                hint.textContent = 'That video is ' + Math.round(probe.duration) + 's — please trim it to ' + MAX_SECONDS + ' seconds or less.';
                hint.classList.add('text-danger');
                video.value = '';
                submit.disabled = false;
            } else {
                hint.textContent = 'Looks good (' + Math.round(probe.duration || 0) + 's).';
            }
        };
        probe.onerror = function () {
            URL.revokeObjectURL(url);
            hint.textContent = 'Could not read that video file — please try a different one.';
            hint.classList.add('text-danger');
            video.value = '';
        };
        probe.src = url;
    });
})();
</script>
@endsection
