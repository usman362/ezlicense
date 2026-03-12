@extends('layouts.admin')

@section('title', 'Bookings')
@section('heading', 'Bookings')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center g-2">
            <div class="col-md-4">
                <form method="GET" class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search learner, instructor, or booking ID..." value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                    @if(request()->hasAny(['search','status','type']))
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                    @endif
                </form>
            </div>
            <div class="col-md-8 d-flex gap-2 justify-content-md-end flex-wrap">
                @php $cs = request('status'); @endphp
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.bookings.index', request()->except(['status','page'])) }}" class="btn {{ !$cs ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                    @foreach(['pending'=>'warning','confirmed'=>'primary','completed'=>'success','cancelled'=>'danger','no_show'=>'dark'] as $s => $c)
                        <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['status'=>$s])) }}"
                           class="btn {{ $cs===$s ? "btn-{$c}" : "btn-outline-{$c}" }}">{{ ucfirst($s === 'no_show' ? 'No Show' : $s) }}</a>
                    @endforeach
                </div>
                @php $ct = request('type'); @endphp
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.bookings.index', request()->except(['type','page'])) }}" class="btn {{ !$ct ? 'btn-info' : 'btn-outline-info' }}">All Types</a>
                    <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['type'=>'lesson'])) }}" class="btn {{ $ct==='lesson' ? 'btn-info' : 'btn-outline-info' }}">Lesson</a>
                    <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['type'=>'test_package'])) }}" class="btn {{ $ct==='test_package' ? 'btn-info' : 'btn-outline-info' }}">Test Package</a>
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
                        <th>Learner</th>
                        <th>Instructor</th>
                        <th>Type</th>
                        <th>Scheduled</th>
                        <th>Duration</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusColors = ['pending'=>'warning','proposed'=>'info','confirmed'=>'primary','completed'=>'success','cancelled'=>'danger','no_show'=>'dark'];
                    @endphp
                    @forelse($bookings as $b)
                        <tr>
                            <td class="small text-muted">#{{ $b->id }}</td>
                            <td>
                                <div class="small fw-semibold">{{ $b->learner->name ?? '—' }}</div>
                                <div class="text-muted small">{{ $b->learner->email ?? '' }}</div>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $b->instructor->name ?? '—' }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $b->type)) }}</span></td>
                            <td class="small">
                                @if($b->scheduled_at)
                                    <div>{{ $b->scheduled_at->format('d M Y') }}</div>
                                    <div class="text-muted">{{ $b->scheduled_at->format('H:i') }}</div>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small">{{ $b->duration_minutes }} min</td>
                            <td class="small fw-semibold">${{ number_format($b->amount, 2) }}</td>
                            <td><span class="badge bg-{{ $statusColors[$b->status] ?? 'secondary' }}">{{ ucfirst($b->status) }}</span></td>
                            <td class="text-end">
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bookingModal{{ $b->id }}" title="Manage">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-muted text-center py-4">No bookings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($bookings->hasPages())
        <div class="card-footer bg-white">{{ $bookings->links() }}</div>
    @endif
</div>

{{-- Booking Detail Modals --}}
@foreach($bookings as $b)
<div class="modal fade" id="bookingModal{{ $b->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking #{{ $b->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Learner</label>
                        <div class="small fw-semibold">{{ $b->learner->name ?? '—' }}</div>
                        <div class="small text-muted">{{ $b->learner->email ?? '' }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Instructor</label>
                        <div class="small fw-semibold">{{ $b->instructor->name ?? '—' }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Type</label>
                        <div class="small">{{ ucfirst(str_replace('_', ' ', $b->type)) }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Transmission</label>
                        <div class="small">{{ ucfirst($b->transmission) }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Scheduled</label>
                        <div class="small">{{ $b->scheduled_at ? $b->scheduled_at->format('d M Y, H:i') : '—' }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Duration</label>
                        <div class="small">{{ $b->duration_minutes }} minutes</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Amount</label>
                        <div class="small fw-semibold">${{ number_format($b->amount, 2) }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Location</label>
                        <div class="small">{{ $b->suburb->name ?? '—' }}</div>
                    </div>
                    @if($b->learner_notes)
                        <div class="col-12">
                            <label class="form-label text-muted small mb-0">Learner Notes</label>
                            <div class="small">{{ $b->learner_notes }}</div>
                        </div>
                    @endif
                    @if($b->cancellation_reason)
                        <div class="col-12">
                            <label class="form-label text-muted small mb-0">Cancellation Reason</label>
                            <div class="small text-danger">{{ $b->cancellation_reason }}</div>
                        </div>
                    @endif
                </div>
                <hr>
                <form method="POST" action="{{ route('admin.bookings.update-status', $b) }}">
                    @csrf @method('PATCH')
                    <h6>Update Status</h6>
                    <div class="row g-2">
                        <div class="col-md-5">
                            <select name="status" class="form-select form-select-sm">
                                @foreach(['pending','proposed','confirmed','completed','cancelled','no_show'] as $st)
                                    <option value="{{ $st }}" {{ $b->status === $st ? 'selected' : '' }}>{{ ucfirst($st === 'no_show' ? 'No Show' : $st) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="reason" class="form-control form-control-sm" placeholder="Reason (if cancelling)">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
