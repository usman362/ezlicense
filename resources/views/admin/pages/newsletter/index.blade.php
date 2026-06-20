@extends('layouts.admin')

@section('title', 'Newsletter Subscribers')
@section('heading', 'Newsletter Subscribers')

@section('content')
@if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
@endif

<div class="row g-3 mb-4">
    @foreach([
        ['Total', $stats['total'], 'text-dark'],
        ['Active', $stats['active'], 'text-success'],
        ['Unsubscribed', $stats['unsubscribed'], 'text-secondary'],
        ['This month', $stats['this_month'], 'text-warning'],
    ] as [$label, $val, $cls])
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">{{ $label }}</div>
                    <h3 class="mb-0 {{ $cls }}">{{ number_format($val) }}</h3>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <form method="get" class="d-flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name, email, state…" style="min-width:260px;">
        <select name="status" class="form-select" style="width:auto;">
            <option value="">All</option>
            <option value="active" @selected(request('status')==='active')>Active</option>
            <option value="unsubscribed" @selected(request('status')==='unsubscribed')>Unsubscribed</option>
        </select>
        <button class="btn btn-outline-secondary">Filter</button>
    </form>
    <a href="{{ route('admin.newsletter.export') }}" class="btn btn-success"><i class="bi bi-download"></i> Export CSV</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Name</th>
                        <th>State</th>
                        <th>Status</th>
                        <th>Subscribed</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $sub)
                        <tr>
                            <td><a href="mailto:{{ $sub->email }}">{{ $sub->email }}</a></td>
                            <td>{{ trim($sub->first_name . ' ' . $sub->last_name) ?: '—' }}</td>
                            <td>{{ $sub->state ?: '—' }}</td>
                            <td>
                                @if($sub->is_active)
                                    <span class="badge text-bg-success">Active</span>
                                @else
                                    <span class="badge text-bg-secondary">Unsubscribed</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ optional($sub->subscribed_at)->format('d M Y') ?: '—' }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <form method="post" action="{{ route('admin.newsletter.toggle', $sub) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-secondary" title="{{ $sub->is_active ? 'Unsubscribe' : 'Re-activate' }}">
                                            <i class="bi {{ $sub->is_active ? 'bi-bell-slash' : 'bi-bell' }}"></i>
                                        </button>
                                    </form>
                                    <form method="post" action="{{ route('admin.newsletter.destroy', $sub) }}" onsubmit="return confirm('Remove this subscriber permanently?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No subscribers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subscribers->hasPages())
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                <span class="text-muted small">Showing {{ $subscribers->firstItem() }}–{{ $subscribers->lastItem() }} of {{ $subscribers->total() }}</span>
                <nav aria-label="Subscriber pages">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item {{ $subscribers->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $subscribers->previousPageUrl() ?: '#' }}">&laquo;</a>
                        </li>
                        @for($p = 1; $p <= $subscribers->lastPage(); $p++)
                            <li class="page-item {{ $p == $subscribers->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $subscribers->url($p) }}">{{ $p }}</a>
                            </li>
                        @endfor
                        <li class="page-item {{ $subscribers->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $subscribers->nextPageUrl() ?: '#' }}">&raquo;</a>
                        </li>
                    </ul>
                </nav>
            </div>
        @endif
    </div>
</div>
@endsection
