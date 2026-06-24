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

{{-- ─── Per-state question counts: each state's test draws a different number ─── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex align-items-center justify-content-between" style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#stateCountsBody">
        <div>
            <strong><i class="bi bi-sliders me-1"></i> Questions per state</strong>
            <div class="small text-muted">Each state's learner test is a different length (e.g. NSW 45, QLD 30, SA 50). Set how many questions each state's practice test draws.</div>
        </div>
        <i class="bi bi-chevron-down"></i>
    </div>
    <div class="collapse show" id="stateCountsBody">
        <div class="card-body">
            <form method="post" action="{{ route('admin.practice-questions.counts') }}">
                @csrf
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-3">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>State / Test</th>
                                <th style="width:140px;">General Knowledge</th>
                                <th style="width:140px;">Road Safety</th>
                                <th style="width:90px;" class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stateCounts as $slug => $s)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $s['name'] }}</span>
                                        <span class="badge text-bg-light ms-1">{{ $s['code'] }}</span>
                                        @if($s['testName'])<div class="small text-muted">{{ $s['testName'] }}</div>@endif
                                    </td>
                                    <td>
                                        <input type="number" min="0" max="200" name="counts[{{ $slug }}][general]"
                                               value="{{ $s['counts']['general'] }}" class="form-control form-control-sm js-count" data-row="{{ $slug }}">
                                    </td>
                                    <td>
                                        <input type="number" min="0" max="200" name="counts[{{ $slug }}][road_safety]"
                                               value="{{ $s['counts']['road_safety'] }}" class="form-control form-control-sm js-count" data-row="{{ $slug }}">
                                    </td>
                                    <td class="text-end fw-bold" data-total="{{ $slug }}">{{ $s['counts']['general'] + $s['counts']['road_safety'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Save question counts</button>
                    <span class="small text-muted">If a state has fewer questions in the bank than the number set, the test simply uses all available.</span>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Live-update each state's total as the admin edits the per-section counts.
    document.querySelectorAll('.js-count').forEach(function (inp) {
        inp.addEventListener('input', function () {
            var row = this.dataset.row;
            var inputs = document.querySelectorAll('.js-count[data-row="' + row + '"]');
            var total = 0;
            inputs.forEach(function (i) { total += parseInt(i.value || 0, 10); });
            var cell = document.querySelector('[data-total="' + row + '"]');
            if (cell) cell.textContent = total;
        });
    });
</script>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <form method="get" class="d-flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search questions…" style="min-width:240px;">
        <select name="section" class="form-select" style="width:auto;">
            <option value="">All sections</option>
            <option value="general" @selected(request('section')==='general')>General Knowledge</option>
            <option value="road_safety" @selected(request('section')==='road_safety')>Road Safety</option>
        </select>
        <button class="btn btn-outline-secondary">Filter</button>
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
                        <tr><td colspan="6" class="text-center text-muted py-4">No questions yet. <a href="{{ route('admin.practice-questions.create') }}">Add one</a>.</td></tr>
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
