<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-flag me-2"></i>Complaints Filed by This User ({{ $complaintsFiled->count() }})</h6>
    </div>
    <div class="card-body p-0">
        @if($complaintsFiled->count() >= 3)
            <div class="alert alert-danger rounded-0 mb-0">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Repeat complainant pattern detected.</strong> This user has filed {{ $complaintsFiled->count() }} complaints across
                {{ $complaintsFiled->pluck('instructor_profile_id')->unique()->count() }} instructor(s). Review carefully before acting on any new report.
            </div>
        @endif

        @if($complaintsFiled->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small">Date</th>
                            <th class="small">Against Instructor</th>
                            <th class="small">Category</th>
                            <th class="small">Severity</th>
                            <th class="small">Subject</th>
                            <th class="small">Status</th>
                            <th class="small text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complaintsFiled as $c)
                            @php
                                $sevColors = ['low'=>'secondary','medium'=>'warning','high'=>'danger','critical'=>'dark'];
                                $statusColors = ['open'=>'danger','investigating'=>'warning','resolved'=>'success','dismissed'=>'secondary','escalated'=>'dark'];
                            @endphp
                            <tr>
                                <td class="small text-muted">{{ $c->created_at->format('d M Y') }}</td>
                                <td class="small">
                                    @if($c->instructorProfile)
                                        <a href="{{ route('admin.instructors.show', $c->instructorProfile) }}" class="text-decoration-none">
                                            {{ $c->instructorProfile->user->name ?? 'Instructor #' . $c->instructor_profile_id }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="small">{{ \App\Models\InstructorComplaint::categories()[$c->category] ?? $c->category }}</td>
                                <td><span class="badge bg-{{ $sevColors[$c->severity] ?? 'secondary' }}">{{ strtoupper($c->severity) }}</span></td>
                                <td class="small">{{ $c->subject }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusColors[$c->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$c->status] ?? 'secondary' }}">{{ ucfirst($c->status) }}</span>
                                    @if($c->status === 'dismissed')
                                        <br><small class="text-danger" title="Complaint was dismissed">false?</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($c->instructorProfile)
                                        <a href="{{ route('admin.instructors.show', $c->instructorProfile) }}#complaints" class="btn btn-sm btn-outline-primary py-0 px-2">
                                            <i class="bi bi-eye"></i>
                                        </a>
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
                This user has not filed any complaints.
            </div>
        @endif
    </div>
</div>
