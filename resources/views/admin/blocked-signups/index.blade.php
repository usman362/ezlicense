@extends('layouts.admin')

@section('title', 'Blocked Signups')
@section('heading', 'Blocked Signups — Anti-Spam')

@section('content')
@if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<div class="row g-3 mb-4">
    @php
        $kpis = [
            ['Total blocks', $counts['all'], 'bi-shield-fill-x', 'danger'],
            ['Currently active', $counts['active'], 'bi-shield-fill', 'warning'],
            ['Released', $counts['released'], 'bi-shield-check', 'success'],
            ['Sneak-in attempts', $counts['attempts'], 'bi-exclamation-triangle-fill', 'info'],
        ];
    @endphp
    @foreach($kpis as [$label, $val, $icon, $color])
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="text-muted small mb-0 text-uppercase" style="letter-spacing:.05em;">{{ $label }}</h6>
                        <i class="bi {{ $icon }} text-{{ $color }} fs-4"></i>
                    </div>
                    <div class="fs-3 fw-bold">{{ $val }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
    <ul class="nav nav-tabs">
        @foreach(['all' => 'All', 'active' => 'Active', 'released' => 'Released'] as $k => $label)
            <li class="nav-item">
                <a class="nav-link {{ ($k === 'all' && ! request('status')) || request('status') === $k ? 'active' : '' }}"
                   href="{{ route('admin.blocked-signups.index', $k === 'all' ? [] : ['status' => $k]) }}">{{ $label }}</a>
            </li>
        @endforeach
    </ul>
    <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#addBlockModal">
        <i class="bi bi-plus-lg"></i> Manually add block
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Email</th><th>Phone</th><th>Name</th><th>Reason</th>
                    <th>Attempts</th><th>Blocked by</th><th>Status</th><th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($blocked as $b)
                    <tr>
                        <td class="ps-3"><code class="small">{{ $b->email }}</code></td>
                        <td><span class="small text-muted">{{ $b->phone_normalized ?: '—' }}</span></td>
                        <td>{{ $b->name ?: '—' }}</td>
                        <td class="text-truncate" style="max-width:240px;">{{ $b->reason }}</td>
                        <td>
                            @if($b->attempts_count > 0)
                                <span class="badge text-bg-danger">{{ $b->attempts_count }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="small">{{ $b->blockedBy?->name ?? '—' }}<br>
                            <span class="text-muted">{{ $b->blocked_at?->diffForHumans() }}</span>
                        </td>
                        <td>
                            @if($b->is_active)
                                <span class="badge text-bg-warning">Active</span>
                            @else
                                <span class="badge text-bg-success">Released</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('admin.blocked-signups.show', $b) }}" class="btn btn-sm btn-outline-secondary" title="View attempts"><i class="bi bi-eye"></i></a>
                            <form method="POST" action="{{ route('admin.blocked-signups.toggle', $b) }}" class="d-inline" onsubmit="return confirm('{{ $b->is_active ? 'Release this block? They will be able to register again.' : 'Re-activate this block?' }}')">
                                @csrf @method('PUT')
                                <button class="btn btn-sm btn-outline-{{ $b->is_active ? 'success' : 'warning' }}" title="{{ $b->is_active ? 'Release' : 'Re-activate' }}">
                                    <i class="bi bi-{{ $b->is_active ? 'unlock' : 'shield-lock' }}"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.blocked-signups.destroy', $b) }}" class="d-inline" onsubmit="return confirm('Delete permanently? (Use Release instead if you might re-block later.)')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-shield-check display-4 d-block mb-3 opacity-50"></i>
                        No blocks recorded. When you block an instructor, their email + phone will appear here automatically.
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $blocked->links() }}</div>

{{-- Manual add modal --}}
<div class="modal fade" id="addBlockModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="{{ route('admin.blocked-signups.store') }}">@csrf
        <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-shield-fill-x me-2"></i>Add manual block</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <p class="small text-muted">Use this to pre-emptively block someone you know shouldn't be able to register (e.g. known scammer email).</p>
            <div class="mb-3"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" placeholder="04xx xxx xxx"></div>
            <div class="mb-3"><label class="form-label">Name (optional)</label><input type="text" name="name" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Reason *</label><textarea name="reason" class="form-control" rows="2" maxlength="500" required></textarea></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning fw-bold"><i class="bi bi-shield-fill-x me-1"></i>Block</button>
        </div>
    </form>
</div></div></div>
@endsection
