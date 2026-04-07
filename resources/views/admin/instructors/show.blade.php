@extends('layouts.admin')

@section('title', ($instructor->user->name ?? 'Instructor') . ' — Profile')
@section('heading')
    <a href="{{ route('admin.instructors.index') }}" class="text-decoration-none text-muted me-2"><i class="bi bi-arrow-left"></i></a>
    {{ $instructor->user->name ?? 'Instructor' }} — Full Profile
@endsection

@section('content')
{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h3 fw-bold text-primary mb-0">{{ $stats['total_bookings'] }}</div>
            <div class="small text-muted">Total Bookings</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h3 fw-bold text-success mb-0">{{ $stats['completed_bookings'] }}</div>
            <div class="small text-muted">Completed</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h3 fw-bold text-danger mb-0">{{ $stats['cancelled_bookings'] }}</div>
            <div class="small text-muted">Cancelled</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h3 fw-bold text-warning mb-0">
                @if($stats['average_rating'] > 0)
                    {{ number_format($stats['average_rating'], 1) }} <i class="bi bi-star-fill small"></i>
                @else
                    —
                @endif
            </div>
            <div class="small text-muted">Avg Rating ({{ $stats['reviews_count'] }})</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="h3 fw-bold text-info mb-0">${{ number_format($stats['total_earnings'], 2) }}</div>
            <div class="small text-muted">Total Earnings</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
            @php
                $verColors = ['pending'=>'warning','documents_submitted'=>'info','verified'=>'success','rejected'=>'danger'];
                $verColor = $verColors[$instructor->verification_status ?? 'pending'] ?? 'secondary';
            @endphp
            <div class="mb-1"><span class="badge bg-{{ $verColor }} fs-6">{{ ucfirst(str_replace('_', ' ', $instructor->verification_status ?? 'pending')) }}</span></div>
            <div class="small text-muted">Verification</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Left Column: Profile Details --}}
    <div class="col-lg-8">
        {{-- Personal & Profile Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-person me-2"></i>Profile Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            @if($instructor->profile_photo)
                                <img src="{{ asset('storage/' . $instructor->profile_photo) }}" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;" alt="Profile">
                            @else
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                                    <span class="fw-bold text-primary fs-5">{{ strtoupper(substr($instructor->user->name ?? 'I', 0, 1)) }}</span>
                                </div>
                            @endif
                            <div>
                                <h5 class="fw-bold mb-0">{{ $instructor->user->name ?? '—' }}</h5>
                                <div class="text-muted small">{{ $instructor->user->email ?? '—' }}</div>
                            </div>
                        </div>
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted" style="width:140px">Phone</td><td>{{ $instructor->user->phone ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Member since</td><td>{{ $instructor->created_at->format('d M Y') }}</td></tr>
                            <tr><td class="text-muted">Profile Status</td><td>
                                @if($instructor->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                @endif
                            </td></tr>
                            <tr><td class="text-muted">Languages</td><td>
                                @if($instructor->languages)
                                    @foreach($instructor->languages as $lang)
                                        <span class="badge bg-light text-dark me-1">{{ $lang }}</span>
                                    @endforeach
                                @else — @endif
                            </td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted small fw-bold">Bio</h6>
                        <p class="small">{{ $instructor->bio ?? 'No bio provided.' }}</p>
                        <h6 class="text-muted small fw-bold mt-3">Profile Description</h6>
                        <p class="small">{{ $instructor->profile_description ?? 'No description.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Vehicle & Pricing --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-car-front me-2"></i>Vehicle & Pricing</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted" style="width:140px">Vehicle</td><td>{{ $instructor->vehicle_make ? "{$instructor->vehicle_year} {$instructor->vehicle_make} {$instructor->vehicle_model}" : '—' }}</td></tr>
                            <tr><td class="text-muted">Safety Rating</td><td>{{ $instructor->vehicle_safety_rating ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Transmission</td><td><span class="badge bg-light text-dark">{{ ucfirst($instructor->transmission ?? 'N/A') }}</span></td></tr>
                        </table>
                        @if($instructor->vehicle_photo)
                            <img src="{{ asset('storage/' . $instructor->vehicle_photo) }}" class="rounded mt-2" style="max-height:100px;" alt="Vehicle">
                        @endif
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted" style="width:160px">Lesson Price</td><td class="fw-bold">${{ number_format($instructor->lesson_price ?? 0, 2) }}</td></tr>
                            <tr><td class="text-muted">Test Package Price</td><td>{{ $instructor->test_package_price ? '$'.number_format($instructor->test_package_price, 2) : '—' }}</td></tr>
                            <tr><td class="text-muted">Private Lesson</td><td>{{ $instructor->lesson_price_private ? '$'.number_format($instructor->lesson_price_private, 2) : '—' }}</td></tr>
                            <tr><td class="text-muted">Private Test Pkg</td><td>{{ $instructor->test_package_price_private ? '$'.number_format($instructor->test_package_price_private, 2) : '—' }}</td></tr>
                            <tr><td class="text-muted">Lesson Duration</td><td>{{ $instructor->lesson_duration_minutes ?? '—' }} min</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Documents --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2"></i>Documents</h6>
            </div>
            <div class="card-body p-0">
                @if($instructor->documents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="small">Type</th>
                                    <th class="small">Side</th>
                                    <th class="small">Expires</th>
                                    <th class="small">Status</th>
                                    <th class="small">Uploaded</th>
                                    <th class="small text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($instructor->documents as $doc)
                                    @php $dColors = ['pending'=>'warning','verified'=>'success','rejected'=>'danger']; @endphp
                                    <tr>
                                        <td class="small fw-semibold">{{ ucfirst(str_replace('_', ' ', $doc->type)) }}</td>
                                        <td class="small">{{ $doc->side ? ucfirst($doc->side) : '—' }}</td>
                                        <td class="small">{{ $doc->expires_at ? $doc->expires_at->format('d M Y') : '—' }}</td>
                                        <td><span class="badge bg-{{ $dColors[$doc->status] ?? 'secondary' }}">{{ ucfirst($doc->status) }}</span></td>
                                        <td class="small text-muted">{{ $doc->created_at->format('d M Y') }}</td>
                                        <td class="text-end">
                                            @if($doc->file_path)
                                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2"><i class="bi bi-eye me-1"></i>View</a>
                                            @endif
                                            @if($doc->status !== 'verified')
                                                <form method="POST" action="{{ route('admin.instructors.update-document-status', $doc) }}" class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="verified">
                                                    <button class="btn btn-sm btn-outline-success py-0 px-2"><i class="bi bi-check-lg"></i> Verify</button>
                                                </form>
                                            @endif
                                            @if($doc->status !== 'rejected')
                                                <form method="POST" action="{{ route('admin.instructors.update-document-status', $doc) }}" class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button class="btn btn-sm btn-outline-danger py-0 px-2"><i class="bi bi-x-lg"></i> Reject</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-muted text-center py-4">No documents uploaded.</div>
                @endif
            </div>
        </div>

        {{-- Pending Reviews (Require Moderation) --}}
        @php
            $pendingReviews = $instructor->reviews->where('status', 'pending');
            $moderatedReviews = $instructor->reviews->where('status', '!=', 'pending');
        @endphp
        @if($pendingReviews->count() > 0)
        <div class="card border-0 shadow-sm mb-4 border-start border-warning border-3">
            <div class="card-header bg-warning bg-opacity-10 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-exclamation-triangle me-2 text-warning"></i>Pending Reviews
                    <span class="badge bg-warning text-dark ms-1">{{ $pendingReviews->count() }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="small">Learner</th>
                                <th class="small">Rating</th>
                                <th class="small">Comment</th>
                                <th class="small">Booking</th>
                                <th class="small">Date</th>
                                <th class="small text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingReviews->sortByDesc('created_at') as $review)
                                <tr>
                                    <td class="small fw-semibold">{{ $review->learner->name ?? '—' }}</td>
                                    <td>
                                        <div class="d-flex gap-0">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : ' text-muted' }}" style="font-size:0.85rem;"></i>
                                            @endfor
                                        </div>
                                    </td>
                                    <td class="small" style="max-width:300px;">
                                        {{ $review->comment ?? '—' }}
                                    </td>
                                    <td class="small text-muted">#{{ $review->booking_id }}</td>
                                    <td class="small text-muted">{{ $review->created_at->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <form method="POST" action="{{ route('admin.instructors.approve-review', $review) }}" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-success btn-sm py-0 px-2" title="Approve">
                                                    <i class="bi bi-check-lg me-1"></i>Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.instructors.reject-review', $review) }}" class="d-inline" onsubmit="var r = prompt('Rejection reason (optional):'); if(r !== null) { this.querySelector('[name=rejection_reason]').value = r; return true; } return false;">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="rejection_reason" value="">
                                                <button class="btn btn-danger btn-sm py-0 px-2" title="Reject">
                                                    <i class="bi bi-x-lg me-1"></i>Reject
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.instructors.delete-review', $review) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review permanently?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm py-0 px-2" title="Delete"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- All Reviews & Ratings --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-star me-2"></i>Reviews & Ratings ({{ $moderatedReviews->count() }})
                    @if($stats['pending_reviews_count'] > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $stats['pending_reviews_count'] }} pending</span>
                    @endif
                </h6>
            </div>
            <div class="card-body p-0">
                @if($moderatedReviews->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="small">Learner</th>
                                    <th class="small">Rating</th>
                                    <th class="small">Comment</th>
                                    <th class="small">Booking</th>
                                    <th class="small">Date</th>
                                    <th class="small">Status</th>
                                    <th class="small text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($moderatedReviews->sortByDesc('created_at') as $review)
                                    @php
                                        $rowClass = match($review->status) {
                                            'rejected' => 'table-danger opacity-75',
                                            'approved' => $review->is_hidden ? 'table-secondary opacity-75' : '',
                                            default => ''
                                        };
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td class="small fw-semibold">{{ $review->learner->name ?? '—' }}</td>
                                        <td>
                                            <div class="d-flex gap-0">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : ' text-muted' }}" style="font-size:0.85rem;"></i>
                                                @endfor
                                            </div>
                                        </td>
                                        <td class="small" style="max-width:250px;">
                                            <span class="d-inline-block text-truncate" style="max-width:250px;">{{ $review->comment ?? '—' }}</span>
                                            @if($review->rejection_reason)
                                                <br><small class="text-danger"><i class="bi bi-info-circle"></i> {{ $review->rejection_reason }}</small>
                                            @endif
                                        </td>
                                        <td class="small text-muted">#{{ $review->booking_id }}</td>
                                        <td class="small text-muted">{{ $review->created_at->format('d M Y') }}</td>
                                        <td>
                                            @if($review->status === 'approved' && !$review->is_hidden)
                                                <span class="badge bg-success-subtle text-success">Approved</span>
                                            @elseif($review->status === 'approved' && $review->is_hidden)
                                                <span class="badge bg-secondary">Hidden</span>
                                            @elseif($review->status === 'rejected')
                                                <span class="badge bg-danger-subtle text-danger">Rejected</span>
                                            @endif
                                            @if($review->google_review_prompted)
                                                <span class="badge bg-info-subtle text-info" title="Learner was prompted to post on Google"><i class="bi bi-google"></i></span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                @if($review->status === 'approved')
                                                    <form method="POST" action="{{ route('admin.instructors.toggle-review-visibility', $review) }}" class="d-inline">
                                                        @csrf @method('PATCH')
                                                        <button class="btn btn-outline-{{ $review->is_hidden ? 'success' : 'warning' }} btn-sm py-0 px-2" title="{{ $review->is_hidden ? 'Show' : 'Hide' }}">
                                                            <i class="bi bi-{{ $review->is_hidden ? 'eye' : 'eye-slash' }}"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('admin.instructors.delete-review', $review) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review permanently?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-sm py-0 px-2" title="Delete"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-muted text-center py-4">No moderated reviews yet.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column: Quick Actions & Business Info --}}
    <div class="col-lg-4">
        {{-- Quick Actions --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.instructors.update-verification', $instructor) }}" class="mb-3">
                    @csrf @method('PATCH')
                    <label class="small fw-bold text-muted mb-1">Verification Status</label>
                    <select name="verification_status" class="form-select form-select-sm mb-2">
                        @foreach(['pending','documents_submitted','verified','rejected'] as $vs)
                            <option value="{{ $vs }}" {{ ($instructor->verification_status ?? 'pending') === $vs ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $vs)) }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="admin_notes" class="form-control form-control-sm mb-2" placeholder="Admin notes..." value="{{ $instructor->admin_notes }}">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Update Verification</button>
                </form>
                <hr>
                <form method="POST" action="{{ route('admin.instructors.toggle-active', $instructor) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-{{ $instructor->is_active ? 'warning' : 'success' }} btn-sm w-100">
                        <i class="bi bi-{{ $instructor->is_active ? 'pause-circle' : 'play-circle' }} me-1"></i>
                        {{ $instructor->is_active ? 'Deactivate' : 'Activate' }} Profile
                    </button>
                </form>
            </div>
        </div>

        {{-- Business Details --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-building me-2"></i>Business Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0 small">
                    <tr><td class="text-muted">Business Name</td><td>{{ $instructor->business_name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">ABN</td><td>{{ $instructor->abn ?? '—' }}</td></tr>
                    <tr><td class="text-muted">GST Registered</td><td>{{ $instructor->gst_registered ? 'Yes' : 'No' }}</td></tr>
                    <tr><td class="text-muted">WWCC Number</td><td>{{ $instructor->wwcc_number ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Bank Details</td><td>{{ $instructor->bank_details_submitted_at ? 'Submitted '.$instructor->bank_details_submitted_at->format('d M Y') : 'Not submitted' }}</td></tr>
                    <tr><td class="text-muted">Payout Freq.</td><td>{{ ucfirst($instructor->payout_frequency ?? '—') }}</td></tr>
                </table>
            </div>
        </div>

        {{-- Service Areas --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-geo-alt me-2"></i>Service Areas ({{ $instructor->serviceAreas->count() }})</h6>
            </div>
            <div class="card-body">
                @if($instructor->serviceAreas->count() > 0)
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($instructor->serviceAreas as $area)
                            <span class="badge bg-light text-dark border">{{ $area->name }} {{ $area->postcode }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="small text-muted mb-0">No service areas set.</p>
                @endif
            </div>
        </div>

        {{-- Calendar Settings --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-calendar3 me-2"></i>Calendar Settings</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0 small">
                    <tr><td class="text-muted">Travel Buffer (Same)</td><td>{{ $instructor->travel_buffer_same_mins ?? '—' }} min</td></tr>
                    <tr><td class="text-muted">Travel Buffer (Synced)</td><td>{{ $instructor->travel_buffer_synced_mins ?? '—' }} min</td></tr>
                    <tr><td class="text-muted">Min Prior Notice</td><td>{{ $instructor->min_prior_notice_hours ?? '—' }} hrs</td></tr>
                    <tr><td class="text-muted">Max Advance</td><td>{{ $instructor->max_advance_notice_days ?? '—' }} days</td></tr>
                    <tr><td class="text-muted">Smart Scheduling</td><td>{{ $instructor->smart_scheduling_enabled ? 'Enabled' : 'Disabled' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
