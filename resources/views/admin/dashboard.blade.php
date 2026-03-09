@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<div class="row g-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3"><i class="bi bi-people fs-4 text-primary"></i></div>
                    <div>
                        <h3 class="mb-0">{{ $stats['users_count'] ?? 0 }}</h3>
                        <span class="text-muted small">Users</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3"><i class="bi bi-person-badge fs-4 text-success"></i></div>
                    <div>
                        <h3 class="mb-0">{{ $stats['instructors_count'] ?? 0 }}</h3>
                        <span class="text-muted small">Instructors</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3"><i class="bi bi-calendar-check fs-4 text-warning"></i></div>
                    <div>
                        <h3 class="mb-0">{{ $stats['bookings_count'] ?? 0 }}</h3>
                        <span class="text-muted small">Bookings</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3"><i class="bi bi-person fs-4 text-info"></i></div>
                    <div>
                        <h3 class="mb-0">{{ $stats['learners_count'] ?? 0 }}</h3>
                        <span class="text-muted small">Learners</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Quick links</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary me-2">Manage users</a>
                <a href="{{ route('admin.instructors.index') }}" class="btn btn-outline-primary me-2">Manage instructors</a>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-primary me-2">View bookings</a>
                <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary">Settings</a>
            </div>
        </div>
    </div>
</div>
@endsection
