@extends('layouts.admin')

@section('title', 'Instructors')
@section('heading', 'Instructors')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">All instructors</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Transmission</th>
                        <th>Lesson price</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($instructors as $ip)
                        <tr>
                            <td>{{ $ip->id }}</td>
                            <td>{{ $ip->user->name ?? '—' }}</td>
                            <td>{{ ucfirst($ip->transmission) }}</td>
                            <td>${{ number_format($ip->lesson_price ?? 0) }}</td>
                            <td>{{ $ip->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">No instructors yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($instructors->hasPages())
            <div class="card-footer bg-white">{{ $instructors->links() }}</div>
        @endif
    </div>
</div>
@endsection
