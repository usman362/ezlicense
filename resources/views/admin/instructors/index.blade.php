@extends('layouts.admin')

@section('title', 'Instructors')
@section('heading', 'Instructors')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center g-2">
            <div class="col-md-4">
                <form method="GET" class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search name, email..." value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                    @if(request()->hasAny(['search','verification','active']))
                        <a href="{{ route('admin.instructors.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                    @endif
                </form>
            </div>
            <div class="col-md-8 d-flex gap-2 justify-content-md-end flex-wrap">
                @php $cv = request('verification'); @endphp
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.instructors.index', request()->except(['verification','page'])) }}" class="btn {{ !$cv ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                    <a href="{{ route('admin.instructors.index', array_merge(request()->except('page'), ['verification'=>'pending'])) }}" class="btn {{ $cv==='pending' ? 'btn-warning' : 'btn-outline-warning' }}">Pending</a>
                    <a href="{{ route('admin.instructors.index', array_merge(request()->except('page'), ['verification'=>'documents_submitted'])) }}" class="btn {{ $cv==='documents_submitted' ? 'btn-info' : 'btn-outline-info' }}">Docs Submitted</a>
                    <a href="{{ route('admin.instructors.index', array_merge(request()->except('page'), ['verification'=>'verified'])) }}" class="btn {{ $cv==='verified' ? 'btn-success' : 'btn-outline-success' }}">Verified</a>
                    <a href="{{ route('admin.instructors.index', array_merge(request()->except('page'), ['verification'=>'rejected'])) }}" class="btn {{ $cv==='rejected' ? 'btn-danger' : 'btn-outline-danger' }}">Rejected</a>
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
                        <th>Instructor</th>
                        <th>Transmission</th>
                        <th>Lesson Price</th>
                        <th>Verification</th>
                        <th>Profile Status</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($instructors as $ip)
                        @php
                            $verColors = ['pending'=>'warning','documents_submitted'=>'info','verified'=>'success','rejected'=>'danger'];
                            $verColor = $verColors[$ip->verification_status ?? 'pending'] ?? 'secondary';
                        @endphp
                        <tr>
                            <td class="small text-muted">{{ $ip->id }}</td>
                            <td>
                                <div class="fw-semibold small">{{ $ip->user->name ?? '—' }}</div>
                                <div class="text-muted small">{{ $ip->user->email ?? '' }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ ucfirst($ip->transmission) }}</span></td>
                            <td class="small">${{ number_format($ip->lesson_price ?? 0, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $verColor }}">{{ ucfirst(str_replace('_', ' ', $ip->verification_status ?? 'pending')) }}</span>
                            </td>
                            <td>
                                @if($ip->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $ip->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#instrModal{{ $ip->id }}" title="Manage">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.instructors.toggle-active', $ip) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $ip->is_active ? 'warning' : 'success' }} btn-sm" title="{{ $ip->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi bi-{{ $ip->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-muted text-center py-4">No instructors found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($instructors->hasPages())
        <div class="card-footer bg-white">{{ $instructors->links() }}</div>
    @endif
</div>

{{-- Instructor Detail/Verification Modals --}}
@foreach($instructors as $ip)
<div class="modal fade" id="instrModal{{ $ip->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $ip->user->name ?? 'Instructor' }} — Profile #{{ $ip->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Profile Details</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted">Email</td><td>{{ $ip->user->email ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Phone</td><td>{{ $ip->user->phone ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Transmission</td><td>{{ ucfirst($ip->transmission) }}</td></tr>
                            <tr><td class="text-muted">Vehicle</td><td>{{ $ip->vehicle_make ? "{$ip->vehicle_year} {$ip->vehicle_make} {$ip->vehicle_model}" : '—' }}</td></tr>
                            <tr><td class="text-muted">Lesson Price</td><td>${{ number_format($ip->lesson_price ?? 0, 2) }}</td></tr>
                            <tr><td class="text-muted">Test Package</td><td>{{ $ip->test_package_price ? '$'.number_format($ip->test_package_price, 2) : '—' }}</td></tr>
                            <tr><td class="text-muted">WWCC</td><td>{{ $ip->wwcc_number ?? 'Not provided' }}</td></tr>
                            <tr><td class="text-muted">ABN</td><td>{{ $ip->abn ?? 'Not provided' }}</td></tr>
                            <tr><td class="text-muted">Bank Details</td><td>{{ $ip->bank_details_submitted_at ? 'Submitted' : 'Not submitted' }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Bio</h6>
                        <p class="small">{{ $ip->bio ?? 'No bio provided.' }}</p>

                        <h6 class="text-muted mt-3">Languages</h6>
                        <p class="small">
                            @if($ip->languages)
                                @foreach($ip->languages as $lang)
                                    <span class="badge bg-light text-dark me-1">{{ $lang }}</span>
                                @endforeach
                            @else
                                —
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Profile & Vehicle Photos --}}
                @if($ip->profile_photo || $ip->vehicle_photo)
                <hr>
                <h6 class="text-muted">Photos</h6>
                <div class="d-flex gap-3 flex-wrap mb-3">
                    @if($ip->profile_photo)
                        <div>
                            <div class="small text-muted mb-1">Profile Photo</div>
                            <img src="{{ asset('storage/' . $ip->profile_photo) }}" class="rounded" style="max-height:100px;" alt="Profile">
                        </div>
                    @endif
                    @if($ip->vehicle_photo)
                        <div>
                            <div class="small text-muted mb-1">Vehicle Photo</div>
                            <img src="{{ asset('storage/' . $ip->vehicle_photo) }}" class="rounded" style="max-height:100px;" alt="Vehicle">
                        </div>
                    @endif
                </div>
                @endif

                {{-- Documents --}}
                <hr>
                <h6 class="text-muted">Uploaded Documents</h6>
                @if($ip->documents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-3">
                            <thead class="table-light">
                                <tr>
                                    <th class="small">Type</th>
                                    <th class="small">Side</th>
                                    <th class="small">Expires</th>
                                    <th class="small">Status</th>
                                    <th class="small">File</th>
                                    <th class="small text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ip->documents as $doc)
                                    @php
                                        $docStatusColors = ['pending'=>'warning','verified'=>'success','rejected'=>'danger'];
                                    @endphp
                                    <tr>
                                        <td class="small">{{ ucfirst(str_replace('_', ' ', $doc->type)) }}</td>
                                        <td class="small">{{ $doc->side ? ucfirst($doc->side) : '—' }}</td>
                                        <td class="small">{{ $doc->expires_at ? $doc->expires_at->format('d M Y') : '—' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $docStatusColors[$doc->status] ?? 'secondary' }}">{{ ucfirst($doc->status) }}</span>
                                        </td>
                                        <td class="small">
                                            @if($doc->file_path)
                                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-primary"><i class="bi bi-file-earmark me-1"></i>View</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($doc->status !== 'verified')
                                                <form method="POST" action="{{ route('admin.instructors.update-document-status', $doc) }}" class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="verified">
                                                    <button type="submit" class="btn btn-success btn-sm py-0 px-1" title="Verify"><i class="bi bi-check-lg"></i></button>
                                                </form>
                                            @endif
                                            @if($doc->status !== 'rejected')
                                                <form method="POST" action="{{ route('admin.instructors.update-document-status', $doc) }}" class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-danger btn-sm py-0 px-1" title="Reject"><i class="bi bi-x-lg"></i></button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="small text-muted">No documents uploaded yet.</p>
                @endif

                <hr>
                <form method="POST" action="{{ route('admin.instructors.update-verification', $ip) }}">
                    @csrf @method('PATCH')
                    <h6>Update Verification Status</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select name="verification_status" class="form-select form-select-sm">
                                @foreach(['pending','documents_submitted','verified','rejected'] as $vs)
                                    <option value="{{ $vs }}" {{ ($ip->verification_status ?? 'pending') === $vs ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $vs)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="admin_notes" class="form-control form-control-sm" placeholder="Admin notes (optional)" value="{{ $ip->admin_notes }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
