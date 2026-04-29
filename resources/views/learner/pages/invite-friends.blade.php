@extends('layouts.learner')

@section('title', 'Invite Friends')
@section('heading', 'Invite Friends')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Invite Friends</li>
    </ol>
</nav>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Hero card --}}
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #fffbeb, #fff7d6); border: 1px solid var(--sl-accent-500) !important;">
    <div class="card-body p-4">
        <div class="row g-4 align-items-center">
            <div class="col-md-7">
                <h3 class="fw-bolder mb-2" style="letter-spacing: -0.02em;">
                    <i class="bi bi-gift-fill text-warning me-2"></i>Invite friends, learn together
                </h3>
                <p class="text-muted mb-0">Share Secure Licences with your friends. They'll get matched with verified instructors and book lessons online — just like you. The more friends you invite, the better!</p>
            </div>
            <div class="col-md-5 text-md-end">
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div class="bg-white rounded-3 p-2 border">
                            <div class="fw-bolder fs-4 text-dark">{{ $stats['sent'] }}</div>
                            <div class="small text-muted">Invited</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="bg-white rounded-3 p-2 border">
                            <div class="fw-bolder fs-4 text-success">{{ $stats['converted'] }}</div>
                            <div class="small text-muted">Joined</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="bg-white rounded-3 p-2 border">
                            <div class="fw-bolder fs-4 text-warning">{{ $stats['pending'] }}</div>
                            <div class="small text-muted">Pending</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Left: Share via link --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-link-45deg me-2 text-primary"></i>Share your invite link</h5>
                <p class="text-muted small mb-3">Copy your unique link and share it anywhere — WhatsApp, SMS, social media, or email.</p>

                <div class="input-group mb-3">
                    <input type="text" class="form-control bg-light" id="referral-link" value="{{ $referralLink }}" readonly>
                    <button class="btn btn-warning fw-bold" type="button" id="copy-link-btn" onclick="copyReferralLink()">
                        <i class="bi bi-clipboard me-1"></i><span id="copy-btn-text">Copy</span>
                    </button>
                </div>

                <div class="d-flex gap-2 align-items-center small text-muted mb-3">
                    <i class="bi bi-tag-fill text-warning"></i>
                    Your code: <code class="bg-light px-2 py-1 rounded fw-bolder text-dark">{{ $referralCode }}</code>
                </div>

                {{-- Share buttons --}}
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $shareText = "Hey! I've been using Secure Licences to find driving instructors. Sign up here and get matched with verified instructors near you:";
                        $whatsappUrl = 'https://wa.me/?text=' . urlencode($shareText . ' ' . $referralLink);
                        $emailUrl = 'mailto:?subject=' . urlencode('Check out Secure Licences') . '&body=' . urlencode($shareText . "\n\n" . $referralLink);
                        $smsUrl = 'sms:?&body=' . urlencode($shareText . ' ' . $referralLink);
                        $facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($referralLink);
                        $twitterUrl = 'https://twitter.com/intent/tweet?text=' . urlencode($shareText) . '&url=' . urlencode($referralLink);
                    @endphp
                    <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-success btn-sm">
                        <i class="bi bi-whatsapp me-1"></i>WhatsApp
                    </a>
                    <a href="{{ $smsUrl }}" class="btn btn-info btn-sm text-white">
                        <i class="bi bi-chat-dots me-1"></i>SMS
                    </a>
                    <a href="{{ $emailUrl }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-envelope me-1"></i>Email
                    </a>
                    <a href="{{ $facebookUrl }}" target="_blank" class="btn btn-primary btn-sm">
                        <i class="bi bi-facebook me-1"></i>Facebook
                    </a>
                    <a href="{{ $twitterUrl }}" target="_blank" class="btn btn-dark btn-sm">
                        <i class="bi bi-twitter-x me-1"></i>X (Twitter)
                    </a>
                </div>
            </div>
        </div>

        {{-- Send by email form --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-envelope-paper me-2 text-primary"></i>Send a personalised invite</h5>
                <p class="text-muted small mb-3">We'll send your friend a beautifully designed email from Secure Licences with your personal message.</p>

                <form method="POST" action="{{ route('learner.invite.send') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Friend's name (optional)</label>
                            <input type="text" name="invitee_name" class="form-control" placeholder="e.g. Sarah" maxlength="80" value="{{ old('invitee_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Friend's email <span class="text-danger">*</span></label>
                            <input type="email" name="invitee_email" class="form-control" placeholder="friend@example.com" required value="{{ old('invitee_email') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Personal message (optional)</label>
                            <textarea name="personal_message" rows="3" class="form-control" maxlength="500" placeholder="e.g. Hey Sarah, I started learning to drive with Secure Licences and it's been great. You should try it!">{{ old('personal_message') }}</textarea>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-warning fw-bold">
                            <i class="bi bi-send me-1"></i>Send Invite
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Right: How it works + history --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">How it works</h6>
                <div class="d-flex gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:32px;height:32px;background:var(--sl-accent-500);color:var(--sl-gray-900);">1</div>
                    <div class="small">
                        <strong class="d-block">Share your link</strong>
                        <span class="text-muted">Copy your unique link and send it to friends.</span>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:32px;height:32px;background:var(--sl-accent-500);color:var(--sl-gray-900);">2</div>
                    <div class="small">
                        <strong class="d-block">They sign up</strong>
                        <span class="text-muted">Your friend creates an account using your link.</span>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:32px;height:32px;background:var(--sl-accent-500);color:var(--sl-gray-900);">3</div>
                    <div class="small">
                        <strong class="d-block">You both win</strong>
                        <span class="text-muted">They get matched with great instructors. You get the bragging rights! 🎉</span>
                    </div>
                </div>
            </div>
        </div>

        @if($invites->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Recent invites</h6>
                    <div class="list-group list-group-flush small">
                        @foreach($invites->take(10) as $inv)
                            <div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                                <div class="text-truncate me-2">
                                    <div class="fw-semibold text-truncate">{{ $inv->invitee_name ?? $inv->invitee_email }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ $inv->created_at->diffForHumans() }}</div>
                                </div>
                                @if($inv->signed_up_at)
                                    <span class="badge bg-success-subtle text-success" title="Signed up {{ $inv->signed_up_at->diffForHumans() }}">
                                        <i class="bi bi-check-circle"></i> Joined
                                    </span>
                                @elseif($inv->email_sent_at)
                                    <span class="badge bg-warning-subtle text-warning">
                                        <i class="bi bi-clock"></i> Pending
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Sent</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function copyReferralLink() {
    const input = document.getElementById('referral-link');
    input.select();
    input.setSelectionRange(0, 99999);
    if (navigator.clipboard) {
        navigator.clipboard.writeText(input.value).then(() => showCopied());
    } else {
        document.execCommand('copy');
        showCopied();
    }
}
function showCopied() {
    const btn = document.getElementById('copy-link-btn');
    const text = document.getElementById('copy-btn-text');
    btn.classList.replace('btn-warning', 'btn-success');
    text.textContent = 'Copied!';
    setTimeout(() => {
        btn.classList.replace('btn-success', 'btn-warning');
        text.textContent = 'Copy';
    }, 2000);
}
</script>
@endsection
