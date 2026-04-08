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
                <form method="POST" action="{{ route('admin.instructors.toggle-active', $instructor) }}" class="mb-2">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-{{ $instructor->is_active ? 'warning' : 'success' }} btn-sm w-100">
                        <i class="bi bi-{{ $instructor->is_active ? 'pause-circle' : 'play-circle' }} me-1"></i>
                        {{ $instructor->is_active ? 'Deactivate' : 'Activate' }} Profile
                    </button>
                </form>
                @if(!$stats['is_blocked'])
                    <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#blockInstructorModal">
                        <i class="bi bi-slash-circle me-1"></i>Block Instructor
                    </button>
                @endif
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

{{-- Block Instructor Modal --}}
<div class="modal fade" id="blockInstructorModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.instructors.blocks.store', $instructor) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-slash-circle me-2"></i>Block Instructor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Duration <span class="text-danger">*</span></label>
                        <select name="duration_type" class="form-select" id="blockDurationType" required>
                            <option value="30_days">30 Days</option>
                            <option value="60_days">60 Days</option>
                            <option value="90_days">90 Days</option>
                            <option value="custom">Custom (days)</option>
                            <option value="permanent">Permanent</option>
                        </select>
                    </div>
                    <div class="mb-3" id="customDaysField" style="display:none;">
                        <label class="form-label small fw-bold">Custom Days</label>
                        <input type="number" name="custom_days" class="form-control" min="1" max="3650" value="30">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Reason (visible in audit log) <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="2" required placeholder="e.g. Multiple complaints about inappropriate conduct"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Internal Notes (admin only)</label>
                        <textarea name="internal_notes" class="form-control" rows="3" placeholder="Any additional context for other admins..."></textarea>
                    </div>
                    <div class="alert alert-warning small mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Blocking will immediately deactivate the profile and prevent the instructor from logging in.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-slash-circle me-1"></i>Block Instructor</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('blockDurationType')?.addEventListener('change', function() {
    document.getElementById('customDaysField').style.display = this.value === 'custom' ? 'block' : 'none';
});
</script>
