<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-person me-2"></i>Personal Details</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr><td class="text-muted" style="width:160px">First name</td><td>{{ $user->first_name ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Last name</td><td>{{ $user->last_name ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Preferred name</td><td>{{ $user->preferred_first_name ?? '—' }}</td></tr>
                            <tr><td class="text-muted">Gender</td><td>{{ $user->gender ? ucfirst(str_replace('_', ' ', $user->gender)) : '—' }}</td></tr>
                            <tr><td class="text-muted">Postcode</td><td>{{ $user->postcode ?? '—' }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr><td class="text-muted" style="width:160px">Email verified</td><td>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success-subtle text-success">Yes</span>
                                    <span class="text-muted small ms-1">{{ $user->email_verified_at->format('d M Y') }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">No</span>
                                @endif
                            </td></tr>
                            <tr><td class="text-muted">Created</td><td>{{ $user->created_at->format('d M Y H:i') }}</td></tr>
                            <tr><td class="text-muted">Last login</td><td>{{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : '—' }}</td></tr>
                            @if($user->deactivated_at)
                                <tr><td class="text-muted">Deactivated</td><td class="text-danger">{{ $user->deactivated_at->format('d M Y') }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($stats['complaints_filed'] >= 3)
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Heads up:</strong> This learner has filed <strong>{{ $stats['complaints_filed'] }}</strong> complaints against instructors.
                Review the <a href="#u-complaints" class="alert-link" onclick="new bootstrap.Tab(document.querySelector('[data-bs-target=\'#u-complaints\']')).show(); return false;">Complaints Filed tab</a> to check the pattern before acting on any new complaint.
            </div>
        @endif

        {{-- Pinned notes quick view --}}
        @php $pinnedNotes = $user->adminNotes->where('pinned', true); @endphp
        @if($pinnedNotes->count() > 0)
            <div class="card border-0 shadow-sm mb-4 border-start border-warning border-3">
                <div class="card-header bg-warning bg-opacity-10 py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-pin-angle-fill me-2 text-warning"></i>Pinned Notes</h6>
                </div>
                <div class="card-body">
                    @foreach($pinnedNotes as $note)
                        <div class="small mb-2 {{ !$loop->last ? 'border-bottom pb-2' : '' }}">
                            <div class="text-body" style="white-space:pre-wrap;">{{ $note->note }}</div>
                            <div class="text-muted mt-1" style="font-size:0.75rem;">
                                — {{ $note->admin->name ?? '—' }} · {{ $note->created_at->format('d M Y') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        {{-- Wallet --}}
        @if($user->isLearner())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-wallet2 me-2"></i>Wallet</h6>
                </div>
                <div class="card-body text-center">
                    <div class="h2 fw-bold text-success mb-1">${{ number_format($stats['wallet_balance'] ?? 0, 2) }}</div>
                    <div class="small text-muted">Current balance</div>
                </div>
            </div>
        @endif

        {{-- Quick actions --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                @if($user->is_active)
                    <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}"
                          onsubmit="var r = prompt('Reason for deactivating this user:'); if(r) { this.querySelector('[name=reason]').value = r; return true; } return false;">
                        @csrf @method('PATCH')
                        <input type="hidden" name="reason" value="">
                        <button class="btn btn-warning btn-sm w-100"><i class="bi bi-pause-circle me-1"></i>Deactivate User</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                        @csrf @method('PATCH')
                        <button class="btn btn-success btn-sm w-100"><i class="bi bi-play-circle me-1"></i>Reactivate User</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
