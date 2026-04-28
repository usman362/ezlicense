@extends('layouts.admin')

@section('title', 'Email Logs')
@section('heading', 'Email Logs')

@section('content')
{{-- KPI cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted small mb-1">Total Sent</h6>
                <p class="mb-0 fs-4 fw-bold">{{ number_format($stats['total']) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted small mb-1">Today</h6>
                <p class="mb-0 fs-4 fw-bold text-primary">{{ number_format($stats['today']) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted small mb-1">Last 7 Days</h6>
                <p class="mb-0 fs-4 fw-bold text-success">{{ number_format($stats['last_7_days']) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted small mb-1">Failed</h6>
                <p class="mb-0 fs-4 fw-bold text-danger">{{ number_format($stats['failed']) }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Email, subject, name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Notification Type</label>
                <select name="notification" class="form-select form-select-sm">
                    <option value="">All types</option>
                    @foreach($notificationTypes as $t)
                        <option value="{{ $t }}" {{ request('notification') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Period</label>
                <select name="days" class="form-select form-select-sm">
                    <option value="">All time</option>
                    <option value="1" {{ request('days') == '1' ? 'selected' : '' }}>Last 24h</option>
                    <option value="7" {{ request('days') == '7' ? 'selected' : '' }}>Last 7 days</option>
                    <option value="30" {{ request('days') == '30' ? 'selected' : '' }}>Last 30 days</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill"><i class="bi bi-search"></i></button>
                @if(request()->hasAny(['search','status','notification','days']))
                    <a href="{{ route('admin.email-logs.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Sent</th>
                        <th>To</th>
                        <th>Subject</th>
                        <th>Type</th>
                        <th>User</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="small text-muted">
                                {{ $log->created_at->format('d M Y') }}
                                <div class="text-muted">{{ $log->created_at->format('H:i') }}</div>
                            </td>
                            <td class="small">
                                <div class="fw-semibold">{{ $log->to_address }}</div>
                                @if($log->to_name)<div class="text-muted">{{ $log->to_name }}</div>@endif
                            </td>
                            <td class="small">{{ $log->subject }}</td>
                            <td class="small">
                                @if($log->notification_class)
                                    <span class="badge bg-light text-dark border">{{ class_basename($log->notification_class) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="small">
                                @if($log->user)
                                    <a href="{{ route('admin.users.show', $log->user) }}" class="text-decoration-none">
                                        {{ $log->user->name }}
                                    </a>
                                    <div class="text-muted">{{ ucfirst($log->user->role) }}</div>
                                @else
                                    <span class="text-muted">Guest / N/A</span>
                                @endif
                            </td>
                            <td class="small">
                                @if($log->status === 'failed')
                                    <span class="badge bg-danger" title="{{ $log->error_message }}"><i class="bi bi-x-circle"></i> Failed</span>
                                @else
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Sent</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted text-center py-4">
                            @if(request()->hasAny(['search','status','notification','days']))
                                No emails match your filters.
                            @else
                                No emails sent yet. Once emails go out, they'll appear here.
                            @endif
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
        <div class="card-footer bg-white">{{ $logs->links() }}</div>
    @endif
</div>

<div class="alert alert-info small mt-3 mb-0">
    <i class="bi bi-info-circle me-1"></i>
    <strong>Note:</strong> Email logs only capture emails sent through the Laravel mail system (notifications + Mailables). System-level emails (e.g. from the database directly) are not tracked.
    If <code>MAIL_MAILER=log</code> is set in .env, emails are written to <code>storage/logs/laravel.log</code> but still tracked here as "sent".
</div>
@endsection
