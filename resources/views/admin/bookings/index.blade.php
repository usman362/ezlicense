@extends('layouts.admin')

@section('title', 'Bookings')
@section('heading', 'Bookings')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center g-2">
            <div class="col-md-4">
                <form method="GET" class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search learner, instructor, or booking ID..." value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                    @if(request()->hasAny(['search','status','type']))
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                    @endif
                </form>
            </div>
            <div class="col-md-8 d-flex gap-2 justify-content-md-end flex-wrap">
                @php $cs = request('status'); @endphp
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.bookings.index', request()->except(['status','page'])) }}" class="btn {{ !$cs ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                    @foreach(['pending'=>'warning','confirmed'=>'primary','instructor_arrived'=>'info','in_progress'=>'indigo','completed'=>'success','cancelled'=>'danger','no_show'=>'dark'] as $s => $c)
                        <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['status'=>$s])) }}"
                           class="btn {{ $cs===$s ? "btn-{$c}" : "btn-outline-{$c}" }}">{{ $s === 'no_show' ? 'No Show' : ($s === 'instructor_arrived' ? 'Arrived' : ($s === 'in_progress' ? 'In Progress' : ucfirst($s))) }}</a>
                    @endforeach
                </div>
                @php $ct = request('type'); @endphp
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.bookings.index', request()->except(['type','page'])) }}" class="btn {{ !$ct ? 'btn-info' : 'btn-outline-info' }}">All Types</a>
                    <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['type'=>'lesson'])) }}" class="btn {{ $ct==='lesson' ? 'btn-info' : 'btn-outline-info' }}">Lesson</a>
                    <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['type'=>'test_package'])) }}" class="btn {{ $ct==='test_package' ? 'btn-info' : 'btn-outline-info' }}">Test Package</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Learner</th>
                        <th>Instructor</th>
                        <th>Type</th>
                        <th>Scheduled</th>
                        <th>Duration</th>
                        <th>Amount</th>
                        <th>Instr. Net</th>
                        <th>Status</th>
                        <th>Confirmed</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusColors = ['pending'=>'warning','proposed'=>'info','confirmed'=>'primary','instructor_arrived'=>'info','in_progress'=>'indigo','completed'=>'success','cancelled'=>'danger','no_show'=>'dark'];
                    @endphp
                    @forelse($bookings as $b)
                        <tr>
                            <td class="small text-muted">#{{ $b->id }}</td>
                            <td>
                                <div class="small fw-semibold">{{ $b->learner->name ?? '—' }}</div>
                                <div class="text-muted small">{{ $b->learner->email ?? '' }}</div>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $b->instructor->name ?? '—' }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $b->type)) }}</span></td>
                            <td class="small">
                                @if($b->scheduled_at)
                                    <div>{{ $b->scheduled_at->format('d M Y') }}</div>
                                    <div class="text-muted">{{ $b->scheduled_at->format('H:i') }}</div>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small">{{ $b->duration_minutes }} min</td>
                            <td class="small fw-semibold">${{ number_format($b->amount, 2) }}</td>
                            <td class="small fw-semibold text-success">
                                ${{ number_format($b->instructor_net_amount ?? max($b->amount - 7, 0), 2) }}
                                @if($b->instructor_payout_id)
                                    <i class="bi bi-check-circle-fill text-success" title="Paid out in payout #{{ $b->instructor_payout_id }}"></i>
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $statusColors[$b->status] ?? 'secondary' }}">{{ $b->status === 'instructor_arrived' ? 'Arrived' : ($b->status === 'in_progress' ? 'In Progress' : ($b->status === 'no_show' ? 'No Show' : ucfirst($b->status))) }}</span></td>
                            <td>
                                @if($b->status === 'completed')
                                    @if($b->learner_confirmed_at)
                                        <span class="badge bg-success" title="Confirmed {{ $b->learner_confirmed_at->format('d M Y H:i') }} from {{ $b->learner_confirmed_ip ?? 'unknown IP' }}">
                                            <i class="bi bi-shield-check"></i> Yes
                                        </span>
                                    @elseif($b->confirmation_sent_at)
                                        <span class="badge bg-warning text-dark" title="Sent {{ $b->confirmation_sent_at->format('d M Y H:i') }}{{ $b->confirmation_reminder_count > 0 ? ', ' . $b->confirmation_reminder_count . ' reminder(s)' : '' }}">
                                            <i class="bi bi-clock-history"></i> Pending
                                        </span>
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-dash"></i> N/A</span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bookingModal{{ $b->id }}" title="Manage">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-muted text-center py-4">No bookings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($bookings->hasPages())
        <div class="card-footer bg-white">{{ $bookings->links() }}</div>
    @endif
</div>

{{-- Booking Detail Modals --}}
@foreach($bookings as $b)
<div class="modal fade" id="bookingModal{{ $b->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking #{{ $b->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Learner</label>
                        <div class="small fw-semibold">{{ $b->learner->name ?? '—' }}</div>
                        <div class="small text-muted">{{ $b->learner->email ?? '' }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Instructor</label>
                        <div class="small fw-semibold">{{ $b->instructor->name ?? '—' }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Type</label>
                        <div class="small">{{ ucfirst(str_replace('_', ' ', $b->type)) }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Transmission</label>
                        <div class="small">{{ ucfirst($b->transmission) }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Scheduled</label>
                        <div class="small">{{ $b->scheduled_at ? $b->scheduled_at->format('d M Y, H:i') : '—' }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Duration</label>
                        <div class="small">{{ $b->duration_minutes }} minutes</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Location</label>
                        <div class="small">{{ $b->suburb->name ?? '—' }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-0">Payment Method</label>
                        <div class="small">
                            <span class="badge bg-light text-dark">{{ ucfirst($b->payment_method ?? '—') }}</span>
                            @php
                                $payStatus = $b->payment_status ?? ($b->status === 'completed' ? 'paid' : ($b->status === 'cancelled' ? 'refunded' : 'pending'));
                                $payColor = ['paid' => 'success', 'pending' => 'warning', 'refunded' => 'danger', 'failed' => 'danger'][$payStatus] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $payColor }}-subtle text-{{ $payColor }}">{{ ucfirst($payStatus) }}</span>
                        </div>
                    </div>

                    {{-- Payment Breakdown --}}
                    @php
                        $serviceFee = (float) \App\Models\SiteSetting::get('platform_service_fee', 5.00);
                        $processingFee = (float) \App\Models\SiteSetting::get('payment_processing_fee', 2.00);
                        $feePercent = (float) \App\Models\SiteSetting::get('platform_fee_percent', 4);

                        $gross = (float) $b->amount;
                        $platformFee = (float) ($b->platform_fee ?? round($gross * $feePercent / 100, 2));
                        $learnerPaid = $gross + $platformFee;
                        $totalDeductions = $serviceFee + $processingFee;
                        $instructorNet = (float) ($b->instructor_net_amount ?? max($gross - $totalDeductions, 0));
                        $gstOnFees = $b->instructorProfile?->gst_registered ? round($totalDeductions / 11, 2) : 0;
                    @endphp
                    <div class="col-12">
                        <div class="border rounded p-3" style="background:var(--sl-gray-50);">
                            <h6 class="fw-bold small text-muted text-uppercase mb-3" style="letter-spacing:0.06em;">
                                <i class="bi bi-cash-coin me-1"></i>Payment Breakdown
                            </h6>
                            <table class="table table-sm mb-0 small">
                                <tbody>
                                    <tr>
                                        <td class="text-muted">Booking Amount (gross)</td>
                                        <td class="text-end fw-semibold">${{ number_format($gross, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">+ Platform Fee ({{ $feePercent }}%)</td>
                                        <td class="text-end">${{ number_format($platformFee, 2) }}</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="fw-bold">Total Learner Paid</td>
                                        <td class="text-end fw-bold text-primary">${{ number_format($learnerPaid, 2) }}</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="text-muted" colspan="2" style="padding-top:0.75rem;">
                                            <small class="text-uppercase fw-semibold" style="letter-spacing:0.06em;">Instructor Payout Calculation</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Booking Amount</td>
                                        <td class="text-end">${{ number_format($gross, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">− Service Fee</td>
                                        <td class="text-end text-danger">−${{ number_format($serviceFee, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">− Processing Fee</td>
                                        <td class="text-end text-danger">−${{ number_format($processingFee, 2) }}</td>
                                    </tr>
                                    @if($gstOnFees > 0)
                                        <tr>
                                            <td class="text-muted">GST on fees (1/11)</td>
                                            <td class="text-end">${{ number_format($gstOnFees, 2) }}</td>
                                        </tr>
                                    @endif
                                    <tr class="border-top">
                                        <td class="fw-bold">Instructor Net Payout</td>
                                        <td class="text-end fw-bold text-success">${{ number_format($instructorNet, 2) }}</td>
                                    </tr>
                                    @if($b->instructor_payout_id)
                                        <tr class="border-top">
                                            <td class="text-muted">Payout Status</td>
                                            <td class="text-end">
                                                <span class="badge bg-success-subtle text-success">
                                                    <i class="bi bi-check-circle me-1"></i>Paid — Payout #{{ $b->instructor_payout_id }}
                                                </span>
                                            </td>
                                        </tr>
                                    @else
                                        <tr class="border-top">
                                            <td class="text-muted">Payout Status</td>
                                            <td class="text-end">
                                                <span class="badge bg-warning-subtle text-warning">
                                                    <i class="bi bi-clock me-1"></i>Pending next payout cycle
                                                </span>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($b->learner_notes)
                        <div class="col-12">
                            <label class="form-label text-muted small mb-0">Learner Notes</label>
                            <div class="small">{{ $b->learner_notes }}</div>
                        </div>
                    @endif
                    @if($b->instructor_arrived_at || $b->lesson_started_at || $b->lesson_ended_at)
                        <div class="col-12">
                            <label class="form-label text-muted small mb-0">Lesson Tracking</label>
                            <div class="small">
                                @if($b->instructor_arrived_at)
                                    <div><i class="bi bi-geo-alt-fill text-info"></i> Instructor arrived: {{ $b->instructor_arrived_at->format('d M Y, H:i') }}</div>
                                @endif
                                @if($b->lesson_started_at)
                                    <div><i class="bi bi-play-fill text-indigo"></i> Lesson started: {{ $b->lesson_started_at->format('d M Y, H:i') }}</div>
                                @endif
                                @if($b->lesson_ended_at)
                                    <div><i class="bi bi-stop-fill text-success"></i> Lesson ended: {{ $b->lesson_ended_at->format('d M Y, H:i') }}</div>
                                @endif
                                @if($b->lesson_started_at && $b->lesson_ended_at)
                                    @php $actualMinutes = (int) $b->lesson_started_at->diffInMinutes($b->lesson_ended_at); @endphp
                                    <div class="mt-1 fw-semibold">
                                        <i class="bi bi-clock"></i> Actual duration: {{ $actualMinutes }} min
                                        @if($actualMinutes !== $b->duration_minutes)
                                            <span class="text-{{ $actualMinutes > $b->duration_minutes ? 'warning' : 'danger' }}">(scheduled: {{ $b->duration_minutes }} min)</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if($b->cancellation_reason)
                        <div class="col-12">
                            <label class="form-label text-muted small mb-0">Cancellation Reason</label>
                            <div class="small text-danger">{{ $b->cancellation_reason }}</div>
                        </div>
                    @endif
                    @if($b->status === 'completed')
                        <div class="col-12">
                            <label class="form-label text-muted small mb-0">Lesson Confirmation (Anti-Chargeback)</label>
                            @if($b->learner_confirmed_at)
                                <div class="small text-success fw-semibold">
                                    <i class="bi bi-shield-check"></i> Confirmed by learner on {{ $b->learner_confirmed_at->format('d M Y \a\t H:i') }}
                                </div>
                                <div class="text-muted small">
                                    IP: {{ $b->learner_confirmed_ip ?? 'N/A' }}
                                    @if($b->learner_confirmed_user_agent)
                                        | UA: {{ Str::limit($b->learner_confirmed_user_agent, 60) }}
                                    @endif
                                </div>
                            @elseif($b->confirmation_sent_at)
                                <div class="small text-warning fw-semibold">
                                    <i class="bi bi-clock-history"></i> Awaiting confirmation (sent {{ $b->confirmation_sent_at->diffForHumans() }})
                                    @if($b->confirmation_reminder_count > 0)
                                        — {{ $b->confirmation_reminder_count }} reminder(s) sent
                                    @endif
                                </div>
                            @else
                                <div class="small text-muted">No confirmation requested yet</div>
                            @endif
                        </div>
                    @endif
                </div>
                <hr>
                <form method="POST" action="{{ route('admin.bookings.update-status', $b) }}">
                    @csrf @method('PATCH')
                    <h6>Update Status</h6>
                    <div class="row g-2">
                        <div class="col-md-5">
                            <select name="status" class="form-select form-select-sm">
                                @foreach(['pending','proposed','confirmed','instructor_arrived','in_progress','completed','cancelled','no_show'] as $st)
                                    <option value="{{ $st }}" {{ $b->status === $st ? 'selected' : '' }}>{{ $st === 'instructor_arrived' ? 'Instructor Arrived' : ($st === 'in_progress' ? 'In Progress' : ($st === 'no_show' ? 'No Show' : ucfirst($st))) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="reason" class="form-control form-control-sm" placeholder="Reason (if cancelling)">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
