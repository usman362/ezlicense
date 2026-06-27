@extends('layouts.admin')

@section('title', 'Application ' . $app->reference)
@section('heading', 'Application ' . $app->reference)

@section('content')
@if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
@endif

<a href="{{ route('admin.instructor-applications.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Back to applications
</a>

<div class="row g-4">
    {{-- Left: details --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h4 class="mb-1">{{ $app->fullName() }}</h4>
                        <div class="text-muted small">
                            <code>{{ $app->reference }}</code> · applied {{ $app->created_at->format('d M Y H:i') }}
                        </div>
                    </div>
                    <div>{!! $app->statusBadge() !!}</div>
                </div>

                <dl class="row mb-0">
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8"><a href="mailto:{{ $app->email }}">{{ $app->email }}</a></dd>

                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8">{{ $app->phone }}</dd>

                    <dt class="col-sm-4">Experience</dt>
                    <dd class="col-sm-8">{{ $app->years_experience !== null ? $app->years_experience . ' years' : '—' }}</dd>

                    <dt class="col-sm-4">Transmission</dt>
                    <dd class="col-sm-8">{{ $app->transmission ? ucfirst($app->transmission) : '—' }}</dd>

                    <dt class="col-sm-4">Lesson price</dt>
                    <dd class="col-sm-8">{{ $app->lesson_price ? '$' . number_format((float)$app->lesson_price, 2) : '—' }}</dd>

                    <dt class="col-sm-4">Vehicle</dt>
                    <dd class="col-sm-8">
                        {{ trim(($app->vehicle_year ?? '') . ' ' . ($app->vehicle_make ?? '') . ' ' . ($app->vehicle_model ?? '')) ?: '—' }}
                    </dd>

                    <dt class="col-sm-4">Suburb</dt>
                    <dd class="col-sm-8">{{ optional($app->suburb)->name ?? '—' }}</dd>

                    @if ($app->bio)
                        <dt class="col-sm-4">Bio</dt>
                        <dd class="col-sm-8"><div class="border rounded p-2 bg-light small">{{ $app->bio }}</div></dd>
                    @endif
                </dl>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><strong>Documents</strong></div>
            <div class="card-body">
                @if (empty($docUrls))
                    <p class="text-muted mb-0">No documents uploaded.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($docUrls as $type => $url)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-file-earmark-text me-1"></i>
                                    {{ str_replace('_', ' ', ucfirst($type)) }}
                                </span>
                                @if ($url)
                                    <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                @else
                                    <span class="text-danger small">Unable to load</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <p class="small text-muted mt-2 mb-0">Document links expire in 30 minutes. Reload the page for fresh links.</p>
                @endif
            </div>
        </div>

        @if ($app->admin_notes || $app->rejection_reason)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    @if ($app->admin_notes)
                        <h6 class="text-muted">Admin notes</h6>
                        <p class="mb-3">{{ $app->admin_notes }}</p>
                    @endif
                    @if ($app->rejection_reason)
                        <h6 class="text-muted">Rejection reason (sent to applicant)</h6>
                        <p class="mb-0">{{ $app->rejection_reason }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Right: actions --}}
    <div class="col-lg-5">
        @if ($app->status === \App\Models\InstructorApplication::STATUS_APPROVED)
            <div class="alert alert-success">
                <strong>Approved</strong> on {{ $app->reviewed_at?->format('d M Y H:i') }}
                @if ($app->reviewer) by {{ $app->reviewer->name }}@endif.
                @if ($app->invite)
                    <hr>
                    Magic-link invite created.
                    <a href="{{ route('admin.instructor-invites.index') }}?q={{ urlencode($app->email) }}" class="alert-link">
                        View invite →
                    </a>
                @endif
            </div>
        @elseif ($app->status === \App\Models\InstructorApplication::STATUS_REJECTED)
            <div class="alert alert-danger">
                <strong>Rejected</strong> on {{ $app->reviewed_at?->format('d M Y H:i') }}
                @if ($app->reviewer) by {{ $app->reviewer->name }}@endif.
            </div>
        @else
            @if ($app->status === \App\Models\InstructorApplication::STATUS_PENDING)
                <form method="post" action="{{ route('admin.instructor-applications.under-review', $app) }}" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-info w-100">
                        <i class="bi bi-eye"></i> Mark as under review
                    </button>
                </form>
            @endif

            <div class="card border-success border-2 shadow-sm mb-3">
                <div class="card-header bg-success text-white"><strong>Approve &amp; send documents link</strong></div>
                <div class="card-body">
                    <p class="small text-muted">
                        This emails the applicant a link to set their password and <strong>upload their documents</strong>.
                        It does <strong>not</strong> activate the account. After they submit documents, you review them
                        under <em>Instructors</em> and approve there — only then does the account go live.
                    </p>
                    <div class="small text-muted mb-3">
                        <span class="badge text-bg-light">1. Approve → docs link</span>
                        <i class="bi bi-arrow-right"></i>
                        <span class="badge text-bg-light">2. Applicant submits docs</span>
                        <i class="bi bi-arrow-right"></i>
                        <span class="badge text-bg-light">3. You approve docs → live</span>
                    </div>
                    <form method="post" action="{{ route('admin.instructor-applications.approve', $app) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small">Internal notes (optional)</label>
                            <textarea name="admin_notes" rows="2" class="form-control form-control-sm">{{ old('admin_notes', $app->admin_notes) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Send {{ $app->fullName() }} a link to submit their documents? (This does NOT activate their account — you approve documents afterwards.)')">
                            <i class="bi bi-check-circle"></i> Approve &amp; send documents link
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-danger shadow-sm">
                <div class="card-header bg-danger text-white"><strong>Reject application</strong></div>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.instructor-applications.reject', $app) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small">Reason (visible to applicant) *</label>
                            <textarea name="rejection_reason" rows="3" class="form-control form-control-sm" required maxlength="1000">{{ old('rejection_reason') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Internal notes (optional)</label>
                            <textarea name="admin_notes" rows="2" class="form-control form-control-sm">{{ old('admin_notes', $app->admin_notes) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100"
                                onclick="return confirm('Reject this application and notify the applicant?')">
                            <i class="bi bi-x-circle"></i> Reject application
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body small text-muted">
                <strong>Audit</strong><br>
                Applied IP: {{ $app->applied_ip ?? '—' }}<br>
                User agent: <span class="font-monospace small">{{ \Illuminate\Support\Str::limit($app->applied_user_agent, 80) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
