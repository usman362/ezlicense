<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-slash-circle me-2"></i>Block History</h6>
        @if(!$stats['is_blocked'])
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#blockInstructorModal">
                <i class="bi bi-slash-circle me-1"></i>Block Instructor
            </button>
        @endif
    </div>
    <div class="card-body p-0">
        @if($instructor->blocks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small">Started</th>
                            <th class="small">Duration</th>
                            <th class="small">Expires</th>
                            <th class="small">Status</th>
                            <th class="small">Reason</th>
                            <th class="small">Issued By</th>
                            <th class="small">Lifted By</th>
                            <th class="small text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($instructor->blocks as $b)
                            @php $isActive = $b->isActive(); @endphp
                            <tr class="{{ $isActive ? 'table-danger' : '' }}">
                                <td class="small">{{ $b->started_at->format('d M Y') }}</td>
                                <td class="small">{{ \App\Models\InstructorBlock::durationLabels()[$b->duration_type] ?? $b->duration_type }}</td>
                                <td class="small">
                                    @if($b->expires_at)
                                        {{ $b->expires_at->format('d M Y') }}
                                    @else
                                        <span class="badge bg-dark">Permanent</span>
                                    @endif
                                </td>
                                <td>
                                    @if($isActive)
                                        <span class="badge bg-danger">ACTIVE</span>
                                    @elseif($b->lifted_at)
                                        <span class="badge bg-secondary">Lifted</span>
                                    @else
                                        <span class="badge bg-light text-dark">Expired</span>
                                    @endif
                                </td>
                                <td class="small" style="max-width:260px;">
                                    <div>{{ $b->reason }}</div>
                                    @if($b->internal_notes)
                                        <small class="text-muted"><i class="bi bi-lock"></i> {{ $b->internal_notes }}</small>
                                    @endif
                                    @if($b->lifted_reason)
                                        <br><small class="text-success"><i class="bi bi-unlock"></i> Lifted: {{ $b->lifted_reason }}</small>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $b->admin->name ?? '—' }}</td>
                                <td class="small text-muted">
                                    @if($b->lifted_by)
                                        {{ $b->lifter->name ?? '—' }}<br>
                                        <span class="text-muted">{{ $b->lifted_at->format('d M Y') }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($isActive)
                                        <form method="POST" action="{{ route('admin.instructors.blocks.lift', $b) }}"
                                              onsubmit="var r = prompt('Reason for lifting (optional):'); if(r !== null) { this.querySelector('[name=lifted_reason]').value = r; return true; } return false;">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="lifted_reason" value="">
                                            <button class="btn btn-sm btn-outline-success"><i class="bi bi-unlock me-1"></i>Lift</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-shield-check fs-1 d-block mb-2"></i>
                No blocks in history — clean record.
            </div>
        @endif
    </div>
</div>
