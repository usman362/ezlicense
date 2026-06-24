@extends('layouts.admin')

@section('title', 'Practice Test Questions')
@section('heading', 'Practice Test Questions')

@section('content')
@if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <div class="text-muted small">General Knowledge</div>
            <h3 class="mb-0">{{ $stats['general'] }}</h3>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <div class="text-muted small">Road Safety</div>
            <h3 class="mb-0">{{ $stats['road_safety'] }}</h3>
        </div></div>
    </div>
</div>

{{-- ─── State filter pills: click a state to see only its questions ─── --}}
@php
    $curState = request('state', '');
    $pills = [
        '' => 'All',
        'all' => 'Common',
    ];
    foreach (\App\Models\PracticeQuestion::STATES as $slug => $name) {
        $pills[$slug] = strtoupper($slug);
    }
@endphp
<div class="mb-2 small text-muted fw-semibold text-uppercase" style="letter-spacing:.04em;">Filter by state</div>
<div class="d-flex flex-wrap gap-2 mb-3">
    @foreach($pills as $slug => $label)
        @php
            $isActive = (string) $curState === (string) $slug;
            $count = $stateCounts[$slug] ?? 0;
            $qs = array_filter(['q' => request('q'), 'section' => request('section'), 'state' => $slug === '' ? null : $slug]);
        @endphp
        <a href="{{ route('admin.practice-questions.index', $qs) }}"
           class="btn btn-sm {{ $isActive ? 'btn-warning text-dark fw-semibold' : 'btn-outline-secondary' }}"
           @if($slug !== '' && $slug !== 'all') title="{{ \App\Models\PracticeQuestion::STATES[$slug] }}" @elseif($slug === 'all') title="Shown in every state's test" @endif>
            {{ $label }}
            <span class="badge rounded-pill {{ $isActive ? 'text-bg-dark' : 'text-bg-light' }} ms-1">{{ $count }}</span>
        </a>
    @endforeach
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <form method="get" class="d-flex flex-wrap gap-2">
        <input type="hidden" name="state" value="{{ request('state') }}">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search questions…" style="min-width:220px;">
        <select name="section" class="form-select" style="width:auto;">
            <option value="">All sections</option>
            <option value="general" @selected(request('section')==='general')>General Knowledge</option>
            <option value="road_safety" @selected(request('section')==='road_safety')>Road Safety</option>
        </select>
        <button class="btn btn-outline-secondary">Filter</button>
        @if(request('q') || request('section') || request('state'))
            <a href="{{ route('admin.practice-questions.index') }}" class="btn btn-link text-muted">Clear</a>
        @endif
    </form>
    <a href="{{ route('admin.practice-questions.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Question</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Question</th>
                        <th>State</th>
                        <th>Section</th>
                        <th>Img</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $pq)
                        <tr>
                            <td class="text-muted">{{ $pq->sort_order }}</td>
                            <td>
                                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($pq->question, 80) }}</div>
                                <div class="small text-muted">{{ count($pq->options) }} options · correct: {{ $pq->options[$pq->correct_index] ?? '—' }}</div>
                            </td>
                            <td>
                                @if($pq->state)
                                    <span class="badge text-bg-warning text-dark">{{ strtoupper($pq->state) }}</span>
                                @else
                                    <span class="badge text-bg-light text-muted">All</span>
                                @endif
                            </td>
                            <td>{{ \App\Models\PracticeQuestion::sectionLabel($pq->section) }}</td>
                            <td>
                                @if($pq->image_path)
                                    <i class="bi bi-image text-success" title="Has image"></i>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($pq->is_active)
                                    <span class="badge text-bg-success">Active</span>
                                @else
                                    <span class="badge text-bg-secondary">Off</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <form method="post" action="{{ route('admin.practice-questions.toggle', $pq) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-secondary" title="{{ $pq->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi {{ $pq->is_active ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.practice-questions.edit', $pq) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <form method="post" action="{{ route('admin.practice-questions.destroy', $pq) }}" onsubmit="return confirm('Delete this question?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No questions yet. <a href="{{ route('admin.practice-questions.create') }}">Add one</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($questions->hasPages())
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                <span class="text-muted small">Showing {{ $questions->firstItem() }}–{{ $questions->lastItem() }} of {{ $questions->total() }}</span>
                <nav aria-label="Question pages">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item {{ $questions->onFirstPage() ? 'disabled' : '' }}"><a class="page-link" href="{{ $questions->previousPageUrl() ?: '#' }}">&laquo;</a></li>
                        @for($p = 1; $p <= $questions->lastPage(); $p++)
                            <li class="page-item {{ $p == $questions->currentPage() ? 'active' : '' }}"><a class="page-link" href="{{ $questions->url($p) }}">{{ $p }}</a></li>
                        @endfor
                        <li class="page-item {{ $questions->hasMorePages() ? '' : 'disabled' }}"><a class="page-link" href="{{ $questions->nextPageUrl() ?: '#' }}">&raquo;</a></li>
                    </ul>
                </nav>
            </div>
        @endif
    </div>
</div>
@endsection
