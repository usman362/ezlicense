@extends('layouts.admin')
@section('title', 'Service Providers')
@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Service Providers</h1>
        <a href="{{ route('admin.service-providers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Provider
        </a>
    </div>

    <form method="GET" class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap gap-2">
            <select name="category" class="form-select" style="max-width:220px;">
                <option value="">All categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category')==$cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="status" class="form-select" style="max-width:200px;">
                <option value="">All statuses</option>
                <option value="pending" @selected(request('status')==='pending')>Pending</option>
                <option value="approved" @selected(request('status')==='approved')>Approved</option>
                <option value="rejected" @selected(request('status')==='rejected')>Rejected</option>
            </select>
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Rate</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($providers as $p)
                        <tr>
                            <td class="fw-semibold">{{ $p->business_name ?: $p->user->name }}</td>
                            <td>{{ $p->category->name }}</td>
                            <td>${{ number_format($p->hourly_rate, 2) }}/hr</td>
                            <td>
                                @php $cls = ['pending'=>'warning','approved'=>'success','rejected'=>'danger'][$p->verification_status] ?? 'secondary'; @endphp
                                <span class="badge bg-{{ $cls }}">{{ ucfirst($p->verification_status) }}</span>
                            </td>
                            <td>{{ $p->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.service-providers.show', $p) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No service providers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $providers->links() }}</div>
</div>
@endsection
