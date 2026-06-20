@extends('layouts.admin')

@section('title', 'FAQs')
@section('heading', 'FAQs')

@section('content')
@if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <form method="get" class="d-flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search questions or category…" style="min-width:280px;">
        <button class="btn btn-outline-secondary">Filter</button>
    </form>
    <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New FAQ</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width:60px;">Order</th>
                        <th>Question</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $faq)
                        <tr>
                            <td class="text-muted">{{ $faq->sort_order }}</td>
                            <td>
                                <div class="fw-semibold">{{ $faq->question }}</div>
                                <a href="{{ route('faqs.show', $faq->slug) }}" target="_blank" class="small text-muted text-decoration-none"><code>/faqs/{{ $faq->slug }}</code> <i class="bi bi-box-arrow-up-right"></i></a>
                            </td>
                            <td>{{ $faq->category ?: '—' }}</td>
                            <td>
                                @if($faq->is_published)
                                    <span class="badge text-bg-success">Published</span>
                                @else
                                    <span class="badge text-bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $faq->updated_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <form method="post" action="{{ route('admin.faqs.toggle', $faq) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-secondary" title="{{ $faq->is_published ? 'Unpublish' : 'Publish' }}">
                                            <i class="bi {{ $faq->is_published ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <form method="post" action="{{ route('admin.faqs.destroy', $faq) }}" onsubmit="return confirm('Delete this FAQ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No FAQs yet. <a href="{{ route('admin.faqs.create') }}">Create the first one</a >.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($faqs->hasPages())
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                <span class="text-muted small">Showing {{ $faqs->firstItem() }}–{{ $faqs->lastItem() }} of {{ $faqs->total() }}</span>
                <nav aria-label="FAQ pages">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item {{ $faqs->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $faqs->previousPageUrl() ?: '#' }}">&laquo;</a>
                        </li>
                        @for($p = 1; $p <= $faqs->lastPage(); $p++)
                            <li class="page-item {{ $p == $faqs->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $faqs->url($p) }}">{{ $p }}</a>
                            </li>
                        @endfor
                        <li class="page-item {{ $faqs->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $faqs->nextPageUrl() ?: '#' }}">&raquo;</a>
                        </li>
                    </ul>
                </nav>
            </div>
        @endif
    </div>
</div>
@endsection
