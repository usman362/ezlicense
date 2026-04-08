@php
    $pendingReviews = $instructor->reviews->where('status', 'pending');
    $moderatedReviews = $instructor->reviews->where('status', '!=', 'pending');
@endphp

{{-- Pending Reviews (Require Moderation) --}}
@if($pendingReviews->count() > 0)
    <div class="card border-0 shadow-sm mb-4 border-start border-warning border-3">
        <div class="card-header bg-warning bg-opacity-10 py-3">
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
                                <td class="small" style="max-width:300px;">{{ $review->comment ?? '—' }}</td>
                                <td class="small text-muted">#{{ $review->booking_id }}</td>
                                <td class="small text-muted">{{ $review->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <form method="POST" action="{{ route('admin.instructors.approve-review', $review) }}" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-success btn-sm py-0 px-2" title="Approve"><i class="bi bi-check-lg me-1"></i>Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.instructors.reject-review', $review) }}" class="d-inline" onsubmit="var r = prompt('Rejection reason (optional):'); if(r !== null) { this.querySelector('[name=rejection_reason]').value = r; return true; } return false;">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="rejection_reason" value="">
                                            <button class="btn btn-danger btn-sm py-0 px-2" title="Reject"><i class="bi bi-x-lg me-1"></i>Reject</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.instructors.delete-review', $review) }}" class="d-inline" onsubmit="return confirm('Delete this review permanently?')">
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
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
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
                                        <form method="POST" action="{{ route('admin.instructors.delete-review', $review) }}" class="d-inline" onsubmit="return confirm('Delete this review permanently?')">
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
