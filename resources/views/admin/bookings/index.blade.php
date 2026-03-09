@extends('layouts.admin')

@section('title', 'Bookings')
@section('heading', 'Bookings')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">All bookings</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Learner</th>
                        <th>Instructor</th>
                        <th>Type</th>
                        <th>Scheduled</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                        <tr>
                            <td>{{ $b->id }}</td>
                            <td>{{ $b->learner->name ?? '—' }}</td>
                            <td>{{ $b->instructor->name ?? '—' }}</td>
                            <td>{{ $b->type ?? '—' }}</td>
                            <td>{{ $b->scheduled_at ? $b->scheduled_at->format('d M Y H:i') : '—' }}</td>
                            <td><span class="badge bg-secondary">{{ $b->status ?? '—' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted">No bookings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())
            <div class="card-footer bg-white">{{ $bookings->links() }}</div>
        @endif
    </div>
</div>
@endsection
