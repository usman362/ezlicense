@extends('layouts.admin')
@section('title', 'Instructor Invites')
@section('heading', 'Instructor Invites')

@section('content')

{{-- Status summary cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-muted">Pending</div>
                        <div class="h4 fw-bolder mb-0">{{ $stats['pending'] }}</div>
                    </div>
                    <i class="bi bi-hourglass-split fs-3 text-warning"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-muted">Accepted</div>
                        <div class="h4 fw-bolder mb-0">{{ $stats['accepted'] }}</div>
                    </div>
                    <i class="bi bi-check-circle-fill fs-3 text-success"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-muted">Expired</div>
                        <div class="h4 fw-bolder mb-0">{{ $stats['expired'] }}</div>
                    </div>
                    <i class="bi bi-clock-history fs-3 text-secondary"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-muted">Cancelled</div>
                        <div class="h4 fw-bolder mb-0">{{ $stats['cancelled'] }}</div>
                    </div>
                    <i class="bi bi-x-circle-fill fs-3 text-danger"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Flash messages --}}
@if(session('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filters + New Invite button --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <form action="{{ route('admin.instructor-invites.index') }}" method="GET" class="d-flex gap-2 align-items-center flex-wrap">
        <div class="input-group" style="max-width:260px;">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" name="q" class="form-control" placeholder="Search by name or email…" value="{{ request('q') }}">
        </div>
        <select name="status" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
            <option value="">All statuses</option>
            <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
            <option value="accepted"  {{ request('status') === 'accepted'  ? 'selected' : '' }}>Accepted</option>
            <option value="expired"   {{ request('status') === 'expired'   ? 'selected' : '' }}>Expired</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
    </form>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newInviteModal">
        <i class="bi bi-plus-lg me-1"></i>New Invite
    </button>
</div>

{{-- Invites table --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Invitee</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Sent</th>
                    <th>Expires</th>
                    <th>Invited by</th>
                    <th style="width: 250px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invites as $inv)
                    <tr>
                        <td>
                            <strong>{{ $inv->fullName() }}</strong>
                            @if($inv->phone)
                                <br><small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $inv->phone }}</small>
                            @endif
                        </td>
                        <td><span class="small">{{ $inv->email }}</span></td>
                        <td>
                            @if($inv->status === \App\Models\InstructorInvite::STATUS_PENDING)
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($inv->status === \App\Models\InstructorInvite::STATUS_ACCEPTED)
                                <span class="badge bg-success">Accepted</span>
                                @if($inv->accepted_at)
                                    <br><small class="text-muted">{{ $inv->accepted_at->diffForHumans() }}</small>
                                @endif
                            @elseif($inv->status === \App\Models\InstructorInvite::STATUS_EXPIRED)
                                <span class="badge bg-secondary">Expired</span>
                            @else
                                <span class="badge bg-danger">Cancelled</span>
                            @endif
                        </td>
                        <td class="small">
                            {{ $inv->last_sent_at?->format('d M Y H:i') ?? '—' }}
                            @if($inv->send_count > 1)
                                <span class="badge bg-light text-dark border ms-1">×{{ $inv->send_count }}</span>
                            @endif
                        </td>
                        <td class="small {{ $inv->isExpired() ? 'text-danger' : 'text-muted' }}">
                            {{ $inv->expires_at->format('d M Y') }}
                            <br><span class="x-small">{{ $inv->expires_at->diffForHumans() }}</span>
                        </td>
                        <td class="small">{{ $inv->inviter?->name ?? '—' }}</td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                {{-- Copy link --}}
                                @if($inv->status === \App\Models\InstructorInvite::STATUS_PENDING && ! $inv->isExpired())
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-link-btn" data-link="{{ $inv->url() }}" title="Copy invite link">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                @endif

                                {{-- Resend (any non-accepted) --}}
                                @if($inv->status !== \App\Models\InstructorInvite::STATUS_ACCEPTED)
                                    <form action="{{ route('admin.instructor-invites.resend', $inv) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Resend invite">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- Cancel (only pending) --}}
                                @if($inv->status === \App\Models\InstructorInvite::STATUS_PENDING)
                                    <form action="{{ route('admin.instructor-invites.cancel', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this invite?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Cancel invite">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- Delete --}}
                                <form action="{{ route('admin.instructor-invites.destroy', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this invite permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                                {{-- View profile (if accepted) --}}
                                @if($inv->registeredUser)
                                    <a href="/admin/instructors/{{ $inv->registeredUser->id }}" class="btn btn-sm btn-outline-success" title="View instructor profile">
                                        <i class="bi bi-person-check"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-envelope-open fs-2 d-block mb-2"></i>
                            No invites yet. Click <strong>New Invite</strong> to onboard your first instructor.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $invites->onEachSide(1)->links() }}
</div>

{{-- New Invite Modal --}}
<div class="modal fade" id="newInviteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.instructor-invites.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-envelope-paper me-2"></i>Invite an Instructor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Send a magic-link invitation. The instructor will receive an email, click the link, set their password and upload documents. Link is single-use and expires in 7 days.</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Last Name <span class="text-muted">(optional)</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">Phone <span class="text-muted">(optional)</span></label>
                            <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="04xx xxx xxx">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Personal note <span class="text-muted">(optional, shown in the email)</span></label>
                            <textarea name="personal_note" class="form-control" rows="2" maxlength="500" placeholder="e.g. Hi Asman, looking forward to having you on the team!">{{ old('personal_note') }}</textarea>
                        </div>

                        {{-- ── Pre-fill bio so the instructor lands on a mostly-ready profile ── --}}
                        <div class="col-12">
                            <div class="alert alert-light border mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Pre-fill profile bio</strong> <span class="badge text-bg-info ms-1">Optional</span>
                                        <div class="small text-muted">Fill in what you know — the instructor only needs to review &amp; submit on accept. Leave blank to let them fill it themselves.</div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#bioFields">
                                        <i class="bi bi-chevron-down"></i> Show fields
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="collapse col-12" id="bioFields">
                            <div class="row g-3 p-3 bg-light rounded">
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Years experience</label>
                                    <input type="number" name="years_experience" min="0" max="60" class="form-control" value="{{ old('years_experience') }}" placeholder="e.g. 8">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Transmission</label>
                                    <select name="transmission" class="form-select">
                                        <option value="">Choose…</option>
                                        @foreach(['auto' => 'Auto only', 'manual' => 'Manual only', 'both' => 'Both'] as $k => $l)
                                            <option value="{{ $k }}" {{ old('transmission') === $k ? 'selected' : '' }}>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Lesson price ($)</label>
                                    <input type="number" step="0.01" name="lesson_price" class="form-control" value="{{ old('lesson_price') }}" placeholder="e.g. 65.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Primary suburb</label>
                                    <select name="suburb_id" class="form-select">
                                        <option value="">— None —</option>
                                        @foreach(\App\Models\Suburb::orderBy('name')->limit(500)->get(['id','name','postcode']) as $s)
                                            <option value="{{ $s->id }}" {{ old('suburb_id') == $s->id ? 'selected' : '' }}>{{ $s->name }} {{ $s->postcode }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Vehicle make</label>
                                    <input type="text" name="vehicle_make" class="form-control" value="{{ old('vehicle_make') }}" placeholder="Toyota">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Vehicle model</label>
                                    <input type="text" name="vehicle_model" class="form-control" value="{{ old('vehicle_model') }}" placeholder="Corolla">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Year</label>
                                    <input type="number" name="vehicle_year" min="1990" max="2100" class="form-control" value="{{ old('vehicle_year') }}" placeholder="2022">
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-semibold">Bio / About</label>
                                    <textarea name="bio" class="form-control" rows="4" maxlength="2000" placeholder="A short bio shown on instructor's public profile. e.g. Friendly, patient instructor with 8 years experience teaching learners in Western Sydney...">{{ old('bio') }}</textarea>
                                    <div class="form-text">Auto-saved to instructor's profile. They can edit anytime.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Send Invite</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.copy-link-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const link = this.dataset.link;
        navigator.clipboard.writeText(link).then(() => {
            const orig = this.innerHTML;
            this.innerHTML = '<i class="bi bi-check-lg text-success"></i>';
            setTimeout(() => { this.innerHTML = orig; }, 1500);
        });
    });
});

// Auto-open modal if there were validation errors
@if($errors->any() && old('email'))
    new bootstrap.Modal(document.getElementById('newInviteModal')).show();
@endif
</script>
@endpush
