@extends('layouts.admin')

@section('title', 'Support Requests')
@section('heading', 'Support › Requests Inbox')

@section('content')
@if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif

<ul class="nav nav-tabs mb-3">
    @foreach(['all' => 'All', 'new' => 'New', 'open' => 'Open', 'pending' => 'Pending', 'resolved' => 'Resolved'] as $k => $label)
        <li class="nav-item">
            <a class="nav-link {{ request('status', 'all') === $k ? 'active' : '' }}" href="{{ route('admin.support.requests', $k === 'all' ? [] : ['status' => $k]) }}">
                {{ $label }} <span class="badge text-bg-light">{{ $counts[$k] }}</span>
            </a>
        </li>
    @endforeach
</ul>

<div class="d-flex mb-3">
    <form method="GET" class="d-flex gap-2">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search ref / name / email / subject" class="form-control form-control-sm" style="width:320px;">
        <button class="btn btn-sm btn-outline-secondary">Search</button>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th class="ps-3">Reference</th><th>From</th><th>Topic</th><th>Subject</th><th>Status</th><th>When</th><th class="text-end pe-3"></th></tr></thead>
            <tbody>
                @forelse($requests as $r)
                    <tr class="{{ $r->status === 'new' ? 'table-warning bg-opacity-50' : '' }}">
                        <td class="ps-3"><code>{{ $r->reference }}</code></td>
                        <td>{{ $r->name }}<br><span class="small text-muted">{{ $r->email }}</span></td>
                        <td><span class="badge text-bg-light">{{ $r->topic }}</span></td>
                        <td class="text-truncate" style="max-width:280px;">{{ $r->subject }}</td>
                        <td><span class="badge text-bg-{{ $r->statusBadge() }}">{{ ucfirst($r->status) }}</span></td>
                        <td><span class="small text-muted">{{ $r->created_at->diffForHumans() }}</span></td>
                        <td class="text-end pe-3"><a href="{{ route('admin.support.request.show', $r) }}" class="btn btn-sm btn-warning fw-bold"><i class="bi bi-eye"></i> View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">No requests in this view.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $requests->links() }}</div>
@endsection
