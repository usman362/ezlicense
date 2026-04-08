<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Audit Trail</h6>
        <small class="text-muted">Automatic log of every admin action on this instructor's profile</small>
    </div>
    <div class="card-body p-0">
        @if($auditLogs->count() > 0)
            @php
                $actionColors = [
                    'verification_updated' => 'primary',
                    'toggled_active' => 'info',
                    'document_verified' => 'success',
                    'document_rejected' => 'danger',
                    'review_approved' => 'success',
                    'review_rejected' => 'danger',
                    'review_deleted' => 'danger',
                    'review_visibility_toggled' => 'secondary',
                    'blocked' => 'danger',
                    'block_lifted' => 'success',
                    'warning_issued' => 'warning',
                    'warning_deleted' => 'secondary',
                    'complaint_added' => 'danger',
                    'complaint_updated' => 'primary',
                    'complaint_deleted' => 'secondary',
                    'note_added' => 'info',
                    'note_deleted' => 'secondary',
                    'correspondence_logged' => 'info',
                    'correspondence_deleted' => 'secondary',
                ];
                $actionIcons = [
                    'verification_updated' => 'shield-check',
                    'toggled_active' => 'toggle-on',
                    'document_verified' => 'file-check',
                    'document_rejected' => 'file-x',
                    'review_approved' => 'star-fill',
                    'review_rejected' => 'star',
                    'review_deleted' => 'trash',
                    'review_visibility_toggled' => 'eye-slash',
                    'blocked' => 'slash-circle',
                    'block_lifted' => 'unlock',
                    'warning_issued' => 'exclamation-triangle',
                    'warning_deleted' => 'trash',
                    'complaint_added' => 'exclamation-octagon',
                    'complaint_updated' => 'pencil',
                    'complaint_deleted' => 'trash',
                    'note_added' => 'sticky',
                    'note_deleted' => 'trash',
                    'correspondence_logged' => 'envelope',
                    'correspondence_deleted' => 'trash',
                ];
            @endphp
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small" style="width:160px;">When</th>
                            <th class="small" style="width:170px;">Action</th>
                            <th class="small">Summary</th>
                            <th class="small" style="width:160px;">Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($auditLogs as $log)
                            @php
                                $color = $actionColors[$log->action] ?? 'secondary';
                                $icon = $actionIcons[$log->action] ?? 'circle';
                            @endphp
                            <tr>
                                <td class="small text-muted">
                                    {{ $log->created_at->format('d M Y H:i') }}
                                    <br><span class="text-muted" style="font-size:0.7rem;">{{ $log->created_at->diffForHumans() }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">
                                        <i class="bi bi-{{ $icon }} me-1"></i>
                                        {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                    </span>
                                </td>
                                <td class="small">{{ $log->summary }}</td>
                                <td class="small text-muted">{{ $log->admin->name ?? 'System' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($auditLogs->count() >= 100)
                <div class="p-3 text-center small text-muted border-top">
                    Showing the 100 most recent entries.
                </div>
            @endif
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                No audit history yet. Actions will be logged here as you interact with this instructor's profile.
            </div>
        @endif
    </div>
</div>
