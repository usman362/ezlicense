<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-calendar-check me-2"></i>Bookings History ({{ $bookings->count() }})</h6>
    </div>
    <div class="card-body p-0">
        @if($bookings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small">#</th>
                            <th class="small">Date</th>
                            <th class="small">Instructor</th>
                            <th class="small">Amount</th>
                            <th class="small">Status</th>
                            <th class="small">Booked</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $b)
                            @php
                                $statusColors = [
                                    'pending'   => 'warning',
                                    'confirmed' => 'info',
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                    'no_show'   => 'dark',
                                ];
                            @endphp
                            <tr>
                                <td class="small text-muted">#{{ $b->id }}</td>
                                <td class="small">
                                    {{ optional($b->start_at ?? $b->booking_date ?? $b->created_at)->format('d M Y') }}
                                    @if($b->start_at)
                                        <br><small class="text-muted">{{ $b->start_at->format('H:i') }}</small>
                                    @endif
                                </td>
                                <td class="small">{{ $b->instructor->name ?? '—' }}</td>
                                <td class="small">${{ number_format($b->amount ?? 0, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusColors[$b->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$b->status] ?? 'secondary' }}">{{ ucfirst($b->status ?? 'unknown') }}</span>
                                </td>
                                <td class="small text-muted">{{ $b->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                No bookings yet.
            </div>
        @endif
    </div>
</div>
