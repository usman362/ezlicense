@extends('layouts.admin')

@section('title', 'Social Media')
@section('heading', 'Social Media Submissions')

@section('content')
@if (session('message'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('message') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <p class="text-muted small mb-0">Instructor-submitted learner test-pass material — preview, copy the caption, and post to the socials.</p>
    <div class="btn-group btn-group-sm">
        <a href="{{ route('admin.social-media.index') }}" class="btn btn-{{ !$status ? 'warning' : 'outline-secondary' }}">All</a>
        @foreach ($statusOptions as $val => $label)
            <a href="{{ route('admin.social-media.index', ['status' => $val]) }}" class="btn btn-{{ $status === $val ? 'warning' : 'outline-secondary' }}">
                {{ $label }}@if ($val === 'pending' && $pendingCount) <span class="badge text-bg-danger ms-1">{{ $pendingCount }}</span>@endif
            </a>
        @endforeach
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Instructor</th>
                    <th>Learner</th>
                    <th>Occasion</th>
                    <th>Media</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($submissions as $s)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $s->instructor?->name ?? '—' }}</div>
                            <div class="small text-muted">{{ $s->instructor?->email }}</div>
                        </td>
                        <td>{{ $s->learner_name ?: '—' }}</td>
                        <td class="small">{{ $s->categoryLabel() }}</td>
                        <td class="small text-muted">
                            @if ($s->video_path)<span class="me-2"><i class="bi bi-camera-video"></i> 1</span>@endif
                            @if ($s->photo_paths)<span><i class="bi bi-images"></i> {{ count($s->photo_paths) }}</span>@endif
                            @if (!$s->video_path && !$s->photo_paths)—@endif
                        </td>
                        <td class="small text-muted">{{ $s->created_at->format('j M Y') }}</td>
                        <td>{!! $s->statusBadge() !!}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.social-media.show', $s) }}" class="btn btn-sm btn-outline-primary">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No submissions{{ $status ? ' with this status' : ' yet' }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $submissions->links() }}</div>
@endsection
