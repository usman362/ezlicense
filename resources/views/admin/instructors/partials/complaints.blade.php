<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-exclamation-octagon me-2"></i>Complaints History</h6>
                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#addComplaintModal">
                    <i class="bi bi-plus-lg me-1"></i>Log Complaint
                </button>
            </div>
            <div class="card-body p-0">
                @if($instructor->complaints->count() > 0)
                    <div class="accordion accordion-flush" id="complaintsAccordion">
                        @foreach($instructor->complaints as $c)
                            @php
                                $sevColors = ['low'=>'secondary','medium'=>'warning','high'=>'danger','critical'=>'dark'];
                                $statusColors = ['open'=>'danger','investigating'=>'warning','resolved'=>'success','dismissed'=>'secondary','escalated'=>'dark'];
                            @endphp
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#complaint-{{ $c->id }}">
                                        <div class="d-flex flex-wrap gap-2 align-items-center w-100 me-3">
                                            <span class="badge bg-{{ $sevColors[$c->severity] ?? 'secondary' }}">{{ strtoupper($c->severity) }}</span>
                                            <span class="badge bg-{{ $statusColors[$c->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$c->status] ?? 'secondary' }}">{{ ucfirst($c->status) }}</span>
                                            <span class="badge bg-light text-dark">{{ \App\Models\InstructorComplaint::categories()[$c->category] ?? $c->category }}</span>
                                            <strong class="me-auto">{{ $c->subject }}</strong>
                                            <small class="text-muted">{{ $c->created_at->format('d M Y') }}</small>
                                            @if($c->police_reported)
                                                <span class="badge bg-danger"><i class="bi bi-shield-exclamation"></i> Police</span>
                                            @endif
                                        </div>
                                    </button>
                                </h2>
                                <div id="complaint-{{ $c->id }}" class="accordion-collapse collapse" data-bs-parent="#complaintsAccordion">
                                    <div class="accordion-body">
                                        <div class="row g-3 small">
                                            <div class="col-md-4">
                                                <div class="text-muted fw-bold">Reporter</div>
                                                <div>{{ $c->reporterLabel() }}</div>
                                                @if($c->reporter)
                                                    <a href="{{ route('admin.users.show', $c->reporter) }}" class="small">
                                                        <i class="bi bi-person-lines-fill me-1"></i>View learner profile
                                                    </a>
                                                    @php
                                                        $priorCount = \App\Models\InstructorComplaint::where('reporter_user_id', $c->reporter->id)
                                                            ->where('id', '!=', $c->id)
                                                            ->count();
                                                    @endphp
                                                    @if($priorCount > 0)
                                                        <div class="mt-1">
                                                            <span class="badge bg-{{ $priorCount >= 2 ? 'danger' : 'warning text-dark' }}" title="This learner has filed {{ $priorCount }} other complaint(s)">
                                                                <i class="bi bi-flag-fill me-1"></i>{{ $priorCount }} prior complaint{{ $priorCount > 1 ? 's' : '' }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-muted fw-bold">Logged By</div>
                                                <div>{{ $c->creator->name ?? '—' }}</div>
                                                <div class="text-muted">{{ $c->created_at->format('d M Y H:i') }}</div>
                                            </div>
                                            <div class="col-md-4">
                                                @if($c->booking_id)
                                                    <div class="text-muted fw-bold">Related Booking</div>
                                                    <div>#{{ $c->booking_id }}</div>
                                                @endif
                                                @if($c->resolved_at)
                                                    <div class="text-muted fw-bold mt-2">Resolved</div>
                                                    <div>{{ $c->resolver->name ?? '—' }} · {{ $c->resolved_at->format('d M Y') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="mb-3">
                                            <div class="text-muted fw-bold small">Description</div>
                                            <p class="mb-0">{{ $c->description }}</p>
                                        </div>
                                        @if($c->resolution_notes)
                                            <div class="mb-3">
                                                <div class="text-muted fw-bold small">Resolution Notes</div>
                                                <p class="mb-0 small">{{ $c->resolution_notes }}</p>
                                            </div>
                                        @endif
                                        @if($c->police_reported)
                                            <div class="alert alert-danger small py-2">
                                                <i class="bi bi-shield-exclamation me-1"></i>
                                                <strong>Police Reported:</strong> {{ $c->police_reference ?? '—' }}
                                                @if($c->police_reported_at) on {{ $c->police_reported_at->format('d M Y') }} @endif
                                            </div>
                                        @endif

                                        {{-- Update status form --}}
                                        <form method="POST" action="{{ route('admin.instructors.complaints.update', $c) }}" class="row g-2 align-items-end">
                                            @csrf @method('PATCH')
                                            <div class="col-md-3">
                                                <label class="small fw-bold text-muted">Status</label>
                                                <select name="status" class="form-select form-select-sm">
                                                    @foreach(\App\Models\InstructorComplaint::statuses() as $k => $v)
                                                        <option value="{{ $k }}" {{ $c->status === $k ? 'selected' : '' }}>{{ $v }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="small fw-bold text-muted">Resolution Notes</label>
                                                <input type="text" name="resolution_notes" class="form-control form-control-sm" value="{{ $c->resolution_notes }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="small fw-bold text-muted">Police Ref</label>
                                                <input type="text" name="police_reference" class="form-control form-control-sm" value="{{ $c->police_reference }}">
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <div class="form-check mb-1">
                                                    <input type="checkbox" name="police_reported" value="1" class="form-check-input" id="police-{{ $c->id }}" {{ $c->police_reported ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="police-{{ $c->id }}">Police reported</label>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg"></i> Save</button>
                                            </div>
                                        </form>

                                        <div class="mt-2 text-end">
                                            <form method="POST" action="{{ route('admin.instructors.complaints.destroy', $c) }}" class="d-inline" onsubmit="return confirm('Delete this complaint permanently?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-shield-check fs-1 d-block mb-2"></i>
                        No complaints logged against this instructor.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-info-circle me-2"></i>About Complaints</h6>
            </div>
            <div class="card-body small text-muted">
                <p>Log any complaint received about this instructor — whether from a learner, parent, police, or any external source.</p>
                <p class="mb-0">Every complaint is timestamped, linked to the reporter (if a registered user), and included in the audit trail for legal / police reference.</p>
            </div>
        </div>
    </div>
</div>

{{-- Add Complaint Modal --}}
<div class="modal fade" id="addComplaintModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('admin.instructors.complaints.store', $instructor) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-octagon me-2"></i>Log New Complaint</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                @foreach(\App\Models\InstructorComplaint::categories() as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Severity <span class="text-danger">*</span></label>
                            <select name="severity" class="form-select" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5" required placeholder="What happened? When? Who was involved? Any additional context..."></textarea>
                        </div>
                        <div class="col-12">
                            <hr class="my-1">
                            <div class="small fw-bold text-muted mb-2">Reporter Details</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Reporter User ID (if registered learner)</label>
                            <input type="number" name="reporter_user_id" class="form-control" placeholder="Learner user ID">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Booking ID (if linked)</label>
                            <input type="number" name="booking_id" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Name</label>
                            <input type="text" name="reporter_name" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Email</label>
                            <input type="email" name="reporter_email" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Phone</label>
                            <input type="text" name="reporter_phone" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-save me-1"></i>Log Complaint</button>
                </div>
            </div>
        </form>
    </div>
</div>
