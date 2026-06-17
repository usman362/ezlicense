@extends('layouts.admin')

@section('title', 'Instructor Applications')
@section('heading', 'Instructor Applications')

@section('content')
@if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Pending</div>
                <h3 class="mb-0 text-warning">{{ $stats['pending'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Under review</div>
                <h3 class="mb-0 text-info">{{ $stats['under_review'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Approved</div>
                <h3 class="mb-0 text-success">{{ $stats['approved'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Rejected</div>
                <h3 class="mb-0 text-danger">{{ $stats['rejected'] }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="get" class="row g-2 mb-3">
            <div class="col-md-5">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Search by name, email, phone or reference…">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    @foreach (['pending' => 'Pending', 'under_review' => 'Under review', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $v => $label)
                        <option value="{{ $v }}" @selected(request('status')===$v)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.instructor-applications.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Applied</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $app)
                        <tr>
                            <td><code>{{ $app->reference }}</code></td>
                            <td>{{ $app->fullName() }}</td>
                            <td>{{ $app->email }}</td>
                            <td>{{ $app->phone }}</td>
                            <td>{!! $app->statusBadge() !!}</td>
                            <td>{{ $app->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.instructor-applications.show', $app) }}" class="btn btn-sm btn-outline-primary">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No applications found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $applications->links() }}
    </div>
</div>
@endsection
