@extends('layouts.admin')

@section('title', 'Request ' . $req->reference)
@section('heading', 'Support › Request ' . $req->reference)

@section('content')
@if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif

<a href="{{ route('admin.support.requests') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to inbox</a>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between">
                <div><strong>{{ $req->subject }}</strong> <span class="badge text-bg-{{ $req->statusBadge() }}">{{ ucfirst($req->status) }}</span></div>
                <div class="small text-muted">{{ $req->created_at->format('j M Y, H:i') }}</div>
            </div>
            <div class="card-body">
                <div class="mb-3" style="white-space:pre-wrap;">{{ $req->message }}</div>
            </div>
        </div>

        @if($req->response)
            <div class="card border-0 shadow-sm mt-3 border-start border-warning border-4">
                <div class="card-header bg-warning bg-opacity-10"><strong>Your response</strong> <span class="small text-muted">— {{ $req->responder?->name }} on {{ $req->responded_at?->format('j M Y, H:i') }}</span></div>
                <div class="card-body" style="white-space:pre-wrap;">{{ $req->response }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.support.request.update', $req) }}" class="card border-0 shadow-sm mt-3">
            @csrf @method('PUT')
            <div class="card-body">
                <h6>Reply / update</h6>
                <div class="mb-3"><label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['new','open','pending','resolved','closed'] as $s)
                            <option value="{{ $s }}" {{ $req->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Response (will save — manual email send for now)</label>
                    <textarea name="response" class="form-control" rows="6">{{ $req->response }}</textarea>
                </div>
                <div class="mb-3"><label class="form-label">Internal notes (only admins see)</label>
                    <textarea name="admin_notes" class="form-control" rows="3">{{ $req->admin_notes }}</textarea>
                </div>
                <button class="btn btn-warning fw-bold"><i class="bi bi-check-lg"></i> Save</button>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Contact</strong></div>
            <div class="card-body small">
                <div><strong>{{ $req->name }}</strong></div>
                <div><a href="mailto:{{ $req->email }}">{{ $req->email }}</a></div>
                @if($req->phone)<div>{{ $req->phone }}</div>@endif
                <hr>
                <div><strong>Reference:</strong> <code>{{ $req->reference }}</code></div>
                <div><strong>Role:</strong> {{ $req->role ?: '—' }}</div>
                <div><strong>Topic:</strong> {{ $req->topic }}</div>
                <div><strong>Submitted:</strong> {{ $req->created_at->format('j M Y, H:i') }}</div>
                @if($req->user_id)<div class="mt-2"><span class="badge text-bg-info">Logged-in user</span></div>@endif
            </div>
        </div>
    </div>
</div>
@endsection
