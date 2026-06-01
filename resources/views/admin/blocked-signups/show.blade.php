@extends('layouts.admin')

@section('title', 'Blocked Signup — ' . $block->email)
@section('heading', 'Blocked Signup Detail')

@section('content')
<a href="{{ route('admin.blocked-signups.index') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to list</a>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Block details</strong></div>
            <div class="card-body small">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr><th>Email</th><td><code>{{ $block->email }}</code></td></tr>
                        <tr><th>Phone</th><td><code>{{ $block->phone_normalized ?: '—' }}</code></td></tr>
                        <tr><th>Name</th><td>{{ $block->name ?: '—' }}</td></tr>
                        <tr><th>Reason</th><td>{{ $block->reason }}</td></tr>
                        <tr><th>Blocked by</th><td>{{ $block->blockedBy?->name ?? '—' }}</td></tr>
                        <tr><th>Blocked at</th><td>{{ $block->blocked_at?->format('j M Y, H:i') }}</td></tr>
                        @if($block->originalUser)
                            <tr><th>Original user</th><td><a href="{{ route('admin.instructors.show', $block->original_user_id) }}">{{ $block->originalUser->name }}</a></td></tr>
                        @endif
                        <tr><th>Status</th><td>
                            @if($block->is_active)
                                <span class="badge text-bg-warning">Active block</span>
                            @else
                                <span class="badge text-bg-success">Released</span>
                            @endif
                        </td></tr>
                    </tbody>
                </table>

                <div class="d-flex gap-2 mt-3">
                    <form method="POST" action="{{ route('admin.blocked-signups.toggle', $block) }}" class="flex-fill">@csrf @method('PUT')
                        <button class="btn btn-{{ $block->is_active ? 'success' : 'warning' }} w-100">
                            <i class="bi bi-{{ $block->is_active ? 'unlock' : 'shield-lock' }}"></i>
                            {{ $block->is_active ? 'Release block' : 'Re-activate block' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between">
                <strong>Sneak-in attempts ({{ $block->attempts->count() }})</strong>
                <span class="small text-muted">When someone matching this block tried to register</span>
            </div>
            <div class="card-body p-0">
                @if($block->attempts->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-shield-check display-5 d-block mb-3 opacity-50"></i>
                        No re-registration attempts. The block is working.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr><th class="ps-3">When</th><th>Email tried</th><th>Phone</th><th>Name</th><th>Via</th><th class="pe-3">IP</th></tr>
                            </thead>
                            <tbody>
                                @foreach($block->attempts as $a)
                                    <tr>
                                        <td class="ps-3">{{ $a->created_at->diffForHumans() }}<br><span class="text-muted">{{ $a->created_at->format('j M, H:i') }}</span></td>
                                        <td><code class="small">{{ $a->email }}</code></td>
                                        <td>{{ $a->phone ?: '—' }}</td>
                                        <td>{{ $a->attempted_name ?: '—' }}</td>
                                        <td><span class="badge text-bg-light">{{ $a->context }}</span></td>
                                        <td class="pe-3">{{ $a->ip_address ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
