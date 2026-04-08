<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-star me-2"></i>Reviews Given ({{ $reviewsGiven->count() }})</h6>
    </div>
    <div class="card-body p-0">
        @if($reviewsGiven->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small">Date</th>
                            <th class="small">Instructor</th>
                            <th class="small">Rating</th>
                            <th class="small">Comment</th>
                            <th class="small">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviewsGiven as $r)
                            <tr>
                                <td class="small text-muted">{{ $r->created_at->format('d M Y') }}</td>
                                <td class="small">{{ $r->instructor->name ?? '—' }}</td>
                                <td>
                                    <div class="d-flex gap-0">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $r->rating ? '-fill text-warning' : ' text-muted' }}" style="font-size:0.85rem;"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td class="small" style="max-width:400px;">{{ $r->comment ?? '—' }}</td>
                                <td>
                                    @if($r->status === 'approved' && !$r->is_hidden)
                                        <span class="badge bg-success-subtle text-success">Approved</span>
                                    @elseif($r->status === 'approved' && $r->is_hidden)
                                        <span class="badge bg-secondary">Hidden</span>
                                    @elseif($r->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($r->status === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-star fs-1 d-block mb-2"></i>
                No reviews submitted.
            </div>
        @endif
    </div>
</div>
