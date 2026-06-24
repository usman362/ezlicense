@extends('layouts.admin')

@section('title', $folder === 'sent' ? 'Webmail — Sent' : 'Webmail — Inbox')
@section('heading', 'Webmail')

@section('content')
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@include('admin.pages.webmail._nav')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <strong>{{ $folder === 'sent' ? 'Sent' : 'Inbox' }}</strong>
        <form method="get" class="d-flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search…" style="min-width:220px;">
            <button class="btn btn-sm btn-outline-secondary">Search</button>
        </form>
    </div>
    <div class="list-group list-group-flush">
        @forelse($messages as $m)
            @php $isUnread = $folder === 'inbox' && ! $m->is_read; @endphp
            <a href="{{ route('admin.webmail.show', $m) }}"
               class="list-group-item list-group-item-action d-flex align-items-center gap-3 {{ $isUnread ? 'bg-light' : '' }}">
                <div style="width:10px;flex-shrink:0;">
                    @if($isUnread)<span class="d-inline-block rounded-circle bg-warning" style="width:9px;height:9px;"></span>@endif
                </div>
                <div style="min-width:170px;max-width:170px;" class="text-truncate {{ $isUnread ? 'fw-bold' : '' }}">
                    @if($folder==='sent')<span class="text-muted small">To:</span> @endif
                    {{ $m->party }}
                </div>
                <div class="flex-grow-1 text-truncate">
                    <span class="{{ $isUnread ? 'fw-bold' : 'fw-semibold' }}">{{ $m->subject ?: '(no subject)' }}</span>
                    <span class="text-muted">— {{ $m->preview }}</span>
                </div>
                @if($m->has_attachments)<i class="bi bi-paperclip text-muted"></i>@endif
                @if($folder==='sent')
                    <span class="badge text-bg-{{ $m->status==='failed' ? 'danger' : 'success' }}">{{ ucfirst($m->status) }}</span>
                @endif
                <div class="text-muted small text-nowrap" style="width:120px;text-align:right;">
                    {{ $m->created_at->diffForHumans(null, true) }} ago
                </div>
            </a>
        @empty
            <div class="list-group-item text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                @if($folder==='sent')
                    No sent messages yet.
                @else
                    Inbox is empty. Incoming emails appear here once the inbound webhook is connected.
                @endif
            </div>
        @endforelse
    </div>
    @if($messages->hasPages())
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="text-muted small">Showing {{ $messages->firstItem() }}–{{ $messages->lastItem() }} of {{ $messages->total() }}</span>
            <nav aria-label="Mail pages">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item {{ $messages->onFirstPage() ? 'disabled' : '' }}"><a class="page-link" href="{{ $messages->previousPageUrl() ?: '#' }}">&laquo;</a></li>
                    @for($p = 1; $p <= $messages->lastPage(); $p++)
                        <li class="page-item {{ $p == $messages->currentPage() ? 'active' : '' }}"><a class="page-link" href="{{ $messages->url($p) }}">{{ $p }}</a></li>
                    @endfor
                    <li class="page-item {{ $messages->hasMorePages() ? '' : 'disabled' }}"><a class="page-link" href="{{ $messages->nextPageUrl() ?: '#' }}">&raquo;</a></li>
                </ul>
            </nav>
        </div>
    @endif
</div>
@endsection
