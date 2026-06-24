{{-- Webmail folder navigation. Expects $folder ('inbox'|'sent'|'compose') and $unread. --}}
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <a href="{{ route('admin.webmail.compose') }}" class="btn btn-primary">
        <i class="bi bi-pencil-square me-1"></i> Compose
    </a>
    <div class="btn-group" role="group">
        <a href="{{ route('admin.webmail.inbox') }}"
           class="btn {{ ($folder ?? '')==='inbox' ? 'btn-dark' : 'btn-outline-secondary' }}">
            <i class="bi bi-inbox me-1"></i> Inbox
            @if(($unread ?? 0) > 0)<span class="badge text-bg-warning text-dark ms-1">{{ $unread }}</span>@endif
        </a>
        <a href="{{ route('admin.webmail.sent') }}"
           class="btn {{ ($folder ?? '')==='sent' ? 'btn-dark' : 'btn-outline-secondary' }}">
            <i class="bi bi-send me-1"></i> Sent
        </a>
    </div>
    <span class="text-muted small ms-auto">
        <i class="bi bi-envelope-at me-1"></i>{{ config('services.webmail.address') }}
    </span>
</div>
