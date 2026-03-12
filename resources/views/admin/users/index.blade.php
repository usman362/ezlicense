@extends('layouts.admin')

@section('title', 'Users')
@section('heading', 'Users')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center g-2">
            <div class="col-md-4">
                <form method="GET" class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search name, email, phone..." value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                    @if(request('search') || request('role') || request('status'))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                    @endif
                </form>
            </div>
            <div class="col-md-8 d-flex gap-2 justify-content-md-end flex-wrap">
                @php $currentRole = request('role'); $currentStatus = request('status'); @endphp
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.users.index', array_merge(request()->except('role','page'), [])) }}"
                       class="btn {{ !$currentRole ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                    <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role'=>'learner'])) }}"
                       class="btn {{ $currentRole==='learner' ? 'btn-primary' : 'btn-outline-primary' }}">Learners</a>
                    <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role'=>'instructor'])) }}"
                       class="btn {{ $currentRole==='instructor' ? 'btn-primary' : 'btn-outline-primary' }}">Instructors</a>
                    <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role'=>'admin'])) }}"
                       class="btn {{ $currentRole==='admin' ? 'btn-primary' : 'btn-outline-primary' }}">Admins</a>
                </div>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.users.index', array_merge(request()->except('status','page'), [])) }}"
                       class="btn {{ !$currentStatus ? 'btn-success' : 'btn-outline-success' }}">All</a>
                    <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['status'=>'active'])) }}"
                       class="btn {{ $currentStatus==='active' ? 'btn-success' : 'btn-outline-success' }}">Active</a>
                    <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['status'=>'inactive'])) }}"
                       class="btn {{ $currentStatus==='inactive' ? 'btn-success' : 'btn-outline-success' }}">Inactive</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="small text-muted">{{ $user->id }}</td>
                            <td>
                                <div class="fw-semibold small">{{ $user->name }}</div>
                                <div class="text-muted small">{{ $user->email }}</div>
                            </td>
                            <td class="small">{{ $user->phone ?? '—' }}</td>
                            <td>
                                @php $roleColors = ['admin'=>'danger','instructor'=>'success','learner'=>'primary']; @endphp
                                <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">{{ ucfirst($user->role) }}</span>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger"><i class="bi bi-x-circle me-1"></i>Inactive</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#userModal{{ $user->id }}" title="View">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }} btn-sm" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi bi-{{ $user->is_active ? 'person-slash' : 'person-check' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted text-center py-4">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-white">{{ $users->links() }}</div>
    @endif
</div>

{{-- User Detail Modals --}}
@foreach($users as $user)
<div class="modal fade" id="userModal{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Email</label>
                        <div class="small">{{ $user->email }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Phone</label>
                        <div class="small">{{ $user->phone ?? '—' }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Role</label>
                        <div class="small"><span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">{{ ucfirst($user->role) }}</span></div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Status</label>
                        <div class="small">{{ $user->is_active ? 'Active' : 'Inactive' }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Gender</label>
                        <div class="small">{{ $user->gender ? ucfirst(str_replace('_', ' ', $user->gender)) : '—' }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Postcode</label>
                        <div class="small">{{ $user->postcode ?? '—' }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Registered</label>
                        <div class="small">{{ $user->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Last Login</label>
                        <div class="small">{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d M Y, H:i') : 'Never' }}</div>
                    </div>
                </div>
                <hr>
                <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="d-flex align-items-center gap-2">
                    @csrf @method('PATCH')
                    <label class="form-label mb-0 small fw-semibold">Change Role:</label>
                    <select name="role" class="form-select form-select-sm" style="width:auto">
                        <option value="learner" {{ $user->role==='learner' ? 'selected' : '' }}>Learner</option>
                        <option value="instructor" {{ $user->role==='instructor' ? 'selected' : '' }}>Instructor</option>
                        <option value="admin" {{ $user->role==='admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                </form>
            </div>
            <div class="modal-footer">
                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-{{ $user->is_active ? 'warning' : 'success' }}">
                        <i class="bi bi-{{ $user->is_active ? 'person-slash' : 'person-check' }} me-1"></i>
                        {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                    </button>
                </form>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
