@extends('layouts.admin')

@section('title', 'User Feedback')
@section('heading', 'User Feedback')

@section('content')
{{-- KPI cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <h6 class="text-muted small mb-1">Total</h6>
            <p class="mb-0 fs-4 fw-bold">{{ number_format($stats['total']) }}</p>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <h6 class="text-muted small mb-1">New</h6>
            <p class="mb-0 fs-4 fw-bold text-warning">{{ number_format($stats['new']) }}</p>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <h6 class="text-muted small mb-1">Reviewing</h6>
            <p class="mb-0 fs-4 fw-bold text-info">{{ number_format($stats['reviewing']) }}</p>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <h6 class="text-muted small mb-1">Resolved</h6>
            <p class="mb-0 fs-4 fw-bold text-success">{{ number_format($stats['resolved']) }}</p>
        </div></div>
    </div>
</div>

@if(session('message'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('message') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Message, user name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Category</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">All categories</option>
                    @foreach($categories as $value => $label)
                        <option value="{{ $value }}" {{ request('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="reviewing" {{ request('status') === 'reviewing' ? 'selected' : '' }}>Reviewing</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button class="btn btn-primary btn-sm flex-fill"><i class="bi bi-search"></i></button>
                @if(request()->hasAny(['search','status','category']))
                    <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Category</th>
                        <th>Rating</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedback as $fb)
                        <tr>
                            <td class="small text-muted">
                                {{ $fb->created_at->format('d M Y') }}
                                <div>{{ $fb->created_at->format('H:i') }}</div>
                            </td>
                            <td class="small">
                                @if($fb->user)
                                    <div class="fw-semibold">{{ $fb->user->name }}</div>
                                    <div class="text-muted">{{ $fb->user->email }}</div>
                                @else
                                    <span class="text-muted">— deleted user —</span>
                                @endif
                            </td>
                            <td class="small">
                                <span class="badge bg-light text-dark border">{{ $categories[$fb->category] ?? ucfirst($fb->category) }}</span>
                            </td>
                            <td class="small">
                                @if($fb->rating)
                                    <span class="text-warning">
                                        @for($s = 1; $s <= 5; $s++)
                                            <i class="bi bi-star{{ $s <= $fb->rating ? '-fill' : '' }}"></i>
                                        @endfor
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="small" style="max-width:300px;">
                                <div class="text-truncate" title="{{ $fb->message }}">{{ \Illuminate\Support\Str::limit($fb->message, 80) }}</div>
                                @if($fb->admin_response)
                                    <div class="small text-success" style="font-size:0.72rem;"><i class="bi bi-reply-fill"></i> Replied</div>
                                @endif
                            </td>
                            <td class="small">
                                @php
                                    $statusColors = ['new'=>'warning','reviewing'=>'info','resolved'=>'success','archived'=>'secondary'];
                                    $color = $statusColors[$fb->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">{{ ucfirst($fb->status) }}</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#fb-modal-{{ $fb->id }}">
                                    <i class="bi bi-eye"></i> View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted text-center py-4">No feedback found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($feedback->hasPages())
        <div class="card-footer bg-white">{{ $feedback->links() }}</div>
    @endif
</div>

{{-- Detail modals --}}
@foreach($feedback as $fb)
    <div class="modal fade" id="fb-modal-{{ $fb->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.feedback.update', $fb) }}">
                    @csrf @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Feedback #{{ $fb->id }}</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-sm mb-3">
                            <tr><td class="text-muted" style="width:30%">User</td><td>{{ $fb->user?->name ?? '—' }} ({{ $fb->user?->email ?? '—' }})</td></tr>
                            <tr><td class="text-muted">Category</td><td>{{ $categories[$fb->category] ?? ucfirst($fb->category) }}</td></tr>
                            @if($fb->rating)
                                <tr><td class="text-muted">Rating</td><td>{{ $fb->rating }}/5 ★</td></tr>
                            @endif
                            <tr><td class="text-muted">Submitted</td><td>{{ $fb->created_at->format('d M Y, H:i') }}</td></tr>
                            @if($fb->page_context)
                                <tr><td class="text-muted">From page</td><td class="small text-break">{{ $fb->page_context }}</td></tr>
                            @endif
                        </table>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Message</label>
                            <div class="p-3 bg-light rounded small" style="white-space: pre-wrap;">{{ $fb->message }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                @foreach(['new','reviewing','resolved','archived'] as $s)
                                    <option value="{{ $s }}" {{ $fb->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-bold">Admin response (internal note)</label>
                            <textarea name="admin_response" class="form-control" rows="3" maxlength="2000" placeholder="Notes about the resolution...">{{ $fb->admin_response }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
