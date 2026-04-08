<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Warnings History</h6>
                <button class="btn btn-warning btn-sm text-dark" data-bs-toggle="modal" data-bs-target="#addWarningModal">
                    <i class="bi bi-plus-lg me-1"></i>Issue Warning
                </button>
            </div>
            <div class="card-body p-0">
                @if($instructor->warnings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="small">Date</th>
                                    <th class="small">Severity</th>
                                    <th class="small">Category</th>
                                    <th class="small">Subject</th>
                                    <th class="small">Issued By</th>
                                    <th class="small">Notified</th>
                                    <th class="small text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($instructor->warnings as $w)
                                    @php $sevColors = ['low'=>'secondary','medium'=>'warning','high'=>'danger','critical'=>'dark']; @endphp
                                    <tr>
                                        <td class="small">{{ $w->created_at->format('d M Y') }}</td>
                                        <td><span class="badge bg-{{ $sevColors[$w->severity] ?? 'secondary' }}">{{ strtoupper($w->severity) }}</span></td>
                                        <td class="small">{{ \App\Models\InstructorWarning::categories()[$w->category] ?? ($w->category ?? '—') }}</td>
                                        <td class="small">
                                            <strong>{{ $w->subject }}</strong>
                                            <br><span class="text-muted">{{ Str::limit($w->description, 120) }}</span>
                                            @if($w->internal_notes)
                                                <br><small class="text-info"><i class="bi bi-lock"></i> {{ $w->internal_notes }}</small>
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $w->admin->name ?? '—' }}</td>
                                        <td>
                                            @if($w->notified_instructor)
                                                <span class="badge bg-success-subtle text-success"><i class="bi bi-check"></i> Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <form method="POST" action="{{ route('admin.instructors.warnings.destroy', $w) }}" class="d-inline" onsubmit="return confirm('Delete this warning?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm py-0 px-2"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
                        No warnings issued.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-info-circle me-2"></i>Warning Levels</h6>
            </div>
            <div class="card-body small">
                <div class="mb-2"><span class="badge bg-secondary">LOW</span> Minor issue, verbal reminder</div>
                <div class="mb-2"><span class="badge bg-warning text-dark">MEDIUM</span> Formal written warning</div>
                <div class="mb-2"><span class="badge bg-danger">HIGH</span> Serious issue, repeat offenders</div>
                <div class="mb-0"><span class="badge bg-dark">CRITICAL</span> Immediate action required</div>
            </div>
        </div>
    </div>
</div>

{{-- Add Warning Modal --}}
<div class="modal fade" id="addWarningModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('admin.instructors.warnings.store', $instructor) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Issue Warning</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Severity <span class="text-danger">*</span></label>
                            <select name="severity" class="form-select" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Category</label>
                            <select name="category" class="form-select">
                                <option value="">— none —</option>
                                @foreach(\App\Models\InstructorWarning::categories() as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Internal Notes (admin only)</label>
                            <textarea name="internal_notes" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Related Booking ID</label>
                            <input type="number" name="related_booking_id" class="form-control">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="notified_instructor" value="1" class="form-check-input" id="notifyInstructorCheck">
                                <label class="form-check-label small" for="notifyInstructorCheck">Instructor has been notified</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning btn-sm"><i class="bi bi-save me-1"></i>Issue Warning</button>
                </div>
            </div>
        </form>
    </div>
</div>
