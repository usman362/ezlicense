@extends('layouts.admin')

@section('title', 'Support Articles')
@section('heading', 'Support › Articles')

@section('content')
@if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2 flex-wrap">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search title" class="form-control form-control-sm" style="width:200px;">
        <select name="section_id" class="form-select form-select-sm">
            <option value="">All sections</option>
            @foreach($sections as $s)<option value="{{ $s->id }}" {{ request('section_id') == $s->id ? 'selected' : '' }}>{{ $s->category->name }} › {{ $s->name }}</option>@endforeach
        </select>
        <select name="published" class="form-select form-select-sm">
            <option value="">All</option>
            <option value="yes" {{ request('published') === 'yes' ? 'selected' : '' }}>Published</option>
            <option value="no" {{ request('published') === 'no' ? 'selected' : '' }}>Draft</option>
        </select>
        <button class="btn btn-sm btn-outline-secondary">Filter</button>
    </form>
    <a href="{{ route('admin.support.article.create') }}" class="btn btn-warning fw-bold"><i class="bi bi-plus-lg"></i> New Article</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th class="ps-3">Title</th><th>Section</th><th>Views</th><th>Helpful</th><th>Status</th><th class="text-end pe-3">Actions</th></tr></thead>
            <tbody>
                @foreach($articles as $a)
                    <tr>
                        <td class="ps-3"><strong>{{ Str::limit($a->title, 60) }}</strong></td>
                        <td><span class="small text-muted">{{ $a->section->category->name }} ›</span> {{ $a->section->name }}</td>
                        <td>{{ number_format($a->views_count) }}</td>
                        <td>
                            @php $total = $a->helpful_yes_count + $a->helpful_no_count; @endphp
                            @if($total > 0)
                                {{ $a->helpfulPercent() }}%
                                <span class="small text-muted">({{ $total }})</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>@if($a->is_published)<span class="badge text-bg-success">Published</span>@else<span class="badge text-bg-secondary">Draft</span>@endif</td>
                        <td class="text-end pe-3">
                            <a href="{{ route('admin.support.article.edit', $a) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('admin.support.article.destroy', $a) }}" class="d-inline" onsubmit="return confirm('Delete this article?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $articles->links() }}</div>
@endsection
