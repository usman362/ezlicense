@extends('layouts.admin')

@section('title', 'Webmail — Compose')
@section('heading', 'Webmail')

@section('content')
@php $folder = 'compose'; @endphp
@include('admin.pages.webmail._nav')

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="fw-bold mb-3">{{ $reply ? 'Reply' : 'New message' }}</h5>

        <form method="post" action="{{ route('admin.webmail.send') }}">
            @csrf
            @if($prefill['reply_to_id'])<input type="hidden" name="reply_to_id" value="{{ $prefill['reply_to_id'] }}">@endif

            <div class="mb-3">
                <label class="form-label small fw-semibold">From</label>
                <input type="text" class="form-control" value="{{ $from }}" disabled>
            </div>
            <div class="row g-3">
                <div class="col-md-8 mb-1">
                    <label class="form-label small fw-semibold">To <span class="text-danger">*</span></label>
                    <input type="email" name="to" class="form-control" required value="{{ old('to', $prefill['to']) }}" placeholder="recipient@example.com">
                </div>
                <div class="col-md-4 mb-1">
                    <label class="form-label small fw-semibold">Cc</label>
                    <input type="text" name="cc" class="form-control" value="{{ old('cc') }}" placeholder="comma-separated">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Subject <span class="text-danger">*</span></label>
                <input type="text" name="subject" class="form-control" required maxlength="255" value="{{ old('subject', $prefill['subject']) }}">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Message <span class="text-danger">*</span></label>
                <textarea name="body" id="mail-body" class="form-control" rows="12">{{ old('body') }}</textarea>
            </div>

            @if($reply)
                <div class="border rounded bg-light p-3 mb-3 small text-muted">
                    <div class="fw-semibold mb-1">In reply to {{ $reply->from_name ?: $reply->from_email }} — {{ $reply->created_at->format('j M Y, g:i A') }}</div>
                    {{ $reply->preview }}
                </div>
            @endif

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Send</button>
                <a href="{{ route('admin.webmail.inbox') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#mail-body',
    license_key: 'gpl',
    height: 380,
    menubar: false,
    branding: false,
    promotion: false,
    plugins: 'lists link image table code autoresize searchreplace wordcount',
    toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link image table | alignleft aligncenter alignright | code removeformat',
    block_formats: 'Paragraph=p;Heading 2=h2;Heading 3=h3;Quote=blockquote',
    content_style: 'body { font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.6; color:#1a1d21; }',
    relative_urls: false,
    remove_script_host: false,
    // Keep the hidden textarea in sync as the user types (so the body is never stale).
    setup: function (editor) {
        editor.on('change keyup', function () { editor.save(); });
    },
});
// Write TinyMCE content back to the textarea before submit; block only if truly empty.
document.querySelector('form').addEventListener('submit', function (e) {
    tinymce.triggerSave();
    var body = document.getElementById('mail-body');
    if (!body.value.replace(/<[^>]*>/g, '').trim()) {
        e.preventDefault();
        alert('Please write a message before sending.');
        if (tinymce.activeEditor) tinymce.activeEditor.focus();
    }
});
</script>
@endpush
