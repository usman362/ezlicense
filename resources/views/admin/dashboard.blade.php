@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
{{-- KPI Cards Row 1 --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3"><i class="bi bi-people fs-4 text-primary"></i></div>
                    <div>
                        <h3 class="mb-0">{{ $stats['users_count'] }}</h3>
                        <span class="text-muted small">Total Users</span>
                    </div>
                </div>
                <div class="mt-2 small text-muted"><i class="bi bi-arrow-up text-success"></i> {{ $stats['new_users_this_month'] }} this month</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3"><i class="bi bi-person-badge fs-4 text-success"></i></div>
                    <div>
                        <h3 class="mb-0">{{ $stats['instructors_count'] }}</h3>
                        <span class="text-muted small">Instructors</span>
                    </div>
                </div>
                <div class="mt-2 small">
                    <span class="text-success"><i class="bi bi-check-circle"></i> {{ $stats['verified_instructors'] }} verified</span>
                    @if($stats['pending_verification'] > 0)
                        <span class="text-warning ms-2"><i class="bi bi-clock"></i> {{ $stats['pending_verification'] }} pending</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3"><i class="bi bi-calendar-check fs-4 text-warning"></i></div>
                    <div>
                        <h3 class="mb-0">{{ $stats['bookings_count'] }}</h3>
                        <span class="text-muted small">Total Bookings</span>
                    </div>
                </div>
                <div class="mt-2 small text-muted">{{ $stats['bookings_this_month'] }} this month</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3"><i class="bi bi-currency-dollar fs-4 text-info"></i></div>
                    <div>
                        <h3 class="mb-0">${{ number_format($stats['revenue_this_month'], 0) }}</h3>
                        <span class="text-muted small">Revenue (This Month)</span>
                    </div>
                </div>
                <div class="mt-2 small text-muted">{{ $stats['learners_count'] }} learners registered</div>
            </div>
        </div>
    </div>
</div>

{{-- Booking Status & Chart Row --}}
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3"><h6 class="mb-0">Booking Status Breakdown</h6></div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span><i class="bi bi-circle-fill text-warning me-2" style="font-size:0.5rem"></i>Pending</span>
                    <span class="badge bg-warning text-dark">{{ $stats['pending_bookings'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span><i class="bi bi-circle-fill text-primary me-2" style="font-size:0.5rem"></i>Confirmed</span>
                    <span class="badge bg-primary">{{ $stats['confirmed_bookings'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span><i class="bi bi-circle-fill text-success me-2" style="font-size:0.5rem"></i>Completed</span>
                    <span class="badge bg-success">{{ $stats['completed_bookings'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span><i class="bi bi-circle-fill text-danger me-2" style="font-size:0.5rem"></i>Cancelled</span>
                    <span class="badge bg-danger">{{ $stats['cancelled_bookings'] }}</span>
                </div>
                @if($stats['inactive_users'] > 0)
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="bi bi-person-slash me-2"></i>Inactive Users</span>
                        <span class="badge bg-secondary">{{ $stats['inactive_users'] }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3"><h6 class="mb-0">Bookings &amp; Revenue (Last 6 Months)</h6></div>
            <div class="card-body">
                <canvas id="adminChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Recent Activity Row --}}
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Recent Bookings</h6>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Learner</th>
                                <th>Instructor</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $b)
                                <tr>
                                    <td class="small">{{ $b->learner->name ?? '—' }}</td>
                                    <td class="small">{{ $b->instructor->name ?? '—' }}</td>
                                    <td><span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $b->type)) }}</span></td>
                                    <td class="small">${{ number_format($b->amount, 2) }}</td>
                                    <td>
                                        @php
                                            $statusColors = ['pending'=>'warning','proposed'=>'info','confirmed'=>'primary','completed'=>'success','cancelled'=>'danger','no_show'=>'dark'];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$b->status] ?? 'secondary' }}">{{ ucfirst($b->status) }}</span>
                                    </td>
                                    <td class="small text-muted">{{ $b->created_at->format('d M H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-muted text-center py-3">No bookings yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Recent Users</h6>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $u)
                                <tr>
                                    <td>
                                        <div class="small fw-semibold">{{ $u->name }}</div>
                                        <div class="small text-muted">{{ $u->email }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $roleColors = ['admin'=>'danger','instructor'=>'success','learner'=>'primary'];
                                        @endphp
                                        <span class="badge bg-{{ $roleColors[$u->role] ?? 'secondary' }}">{{ ucfirst($u->role) }}</span>
                                    </td>
                                    <td class="small text-muted">{{ $u->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted text-center py-3">No users yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const data = @json($chartData);
    const ctx = document.getElementById('adminChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.label),
            datasets: [
                {
                    label: 'Bookings',
                    data: data.map(d => d.count),
                    backgroundColor: 'rgba(255,132,0,0.7)',
                    borderRadius: 4,
                    yAxisID: 'y'
                },
                {
                    label: 'Revenue ($)',
                    data: data.map(d => d.revenue),
                    type: 'line',
                    borderColor: '#f0ad4e',
                    backgroundColor: 'rgba(240,173,78,0.1)',
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y:  { beginAtZero: true, position: 'left',  title: { display: true, text: 'Bookings' } },
                y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'Revenue ($)' }, grid: { drawOnChartArea: false } }
            },
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
@endpush
@endsection
