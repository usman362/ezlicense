@extends('layouts.admin')

@section('title', $message->subject ?: 'Message')
@section('heading', 'Webmail')

@section('content')
@php $folder = $message->direction === 'inbound' ? 'inbox' : 'sent'; @endphp
@include('admin.pages.webmail._nav')

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <a href="{{ route("admin.webmail.$folder") }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
            <div class="d-flex gap-1">
                @if($message->direction === 'inbound')
                    <a href="{{ route('admin.webmail.compose', ['reply' => $message->id]) }}" class="btn btn-sm btn-primary"><i class="bi bi-reply me-1"></i>Reply</a>
                    <form method="post" action="{{ route('admin.webmail.toggle-read', $message) }}">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm btn-outline-secondary">{{ $message->is_read ? 'Mark unread' : 'Mark read' }}</button>
                    </form>
                @endif
                <form method="post" action="{{ route('admin.webmail.destroy', $message) }}" onsubmit="return confirm('Delete this message?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>

        <h4 class="fw-bold mb-3">{{ $message->subject ?: '(no subject)' }}</h4>

        <div class="d-flex flex-wrap gap-3 pb-3 mb-3 border-bottom small">
            <div>
                <div class="text-muted">From</div>
                <div class="fw-semibold">{{ $message->from_name ?: $message->from_email }} <span class="text-muted">&lt;{{ $message->from_email }}&gt;</span></div>
            </div>
            <div>
                <div class="text-muted">To</div>
                <div class="fw-semibold">{{ $message->to_name ?: $message->to_email }}</div>
            </div>
            @if($message->cc)
                <div><div class="text-muted">Cc</div><div>{{ $message->cc }}</div></div>
            @endif
            <div class="ms-auto text-end">
                <div class="text-muted">Date</div>
                <div>{{ $message->created_at->format('D, j M Y · g:i A') }}</div>
                <span class="badge text-bg-{{ $message->direction==='inbound' ? 'info' : 'secondary' }}">{{ ucfirst($message->direction) }}</span>
            </div>
        </div>

        @if($message->body_html)
            <iframe sandbox="allow-same-origin" style="width:100%;min-height:420px;border:0;" srcdoc="{{ $message->body_html }}"></iframe>
        @elseif($message->body_text)
            <pre style="white-space:pre-wrap;font-family:inherit;font-size:.95rem;margin:0;">{{ $message->body_text }}</pre>
        @else
            <p class="text-muted fst-italic">(empty body)</p>
        @endif

        @if($message->has_attachments && is_array($message->attachments))
            <div class="mt-3 pt-3 border-top">
                <div class="small text-muted mb-1"><i class="bi bi-paperclip"></i> Attachments</div>
                @foreach($message->attachments as $att)
                    <span class="badge text-bg-light">{{ is_array($att) ? ($att['name'] ?? $att['filename'] ?? 'file') : $att }}</span>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
