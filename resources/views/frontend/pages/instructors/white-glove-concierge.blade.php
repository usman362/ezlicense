@extends('layouts.frontend')
@section('title', 'White-Glove Concierge Support — Secure Licence')
@section('meta_description', 'A real human team taking the awkward calls. Disputes, refunds, cancellations and no-shows handled end-to-end — over 95% resolved without contacting you.')

@push('styles')
    @include('frontend.pages.instructors._feature-styles')
@endpush

@section('content')

{{-- ─────────── HERO ─────────── --}}
<section class="py-5">
    <div class="container">
        <nav class="small text-muted mb-3">
            <a href="{{ url('/') }}" class="text-muted text-decoration-none">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('instruct-with-us') }}" class="text-muted text-decoration-none">For Instructors</a>
            <span class="mx-1">/</span>
            <span class="text-dark">White-Glove Concierge</span>
        </nav>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="lg-eyebrow mb-3">White-Glove Concierge</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">A real human team, on your side, taking the awkward calls.</h1>
                <p class="text-muted mb-4">
                    Highly responsive resolutions for every dispute, refund request, angry parent, and no-show
                    fee — directly with the learner. You don't mediate. You don't make the call. We've resolved
                    over 95% of learner disputes without the instructor ever being contacted.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Highly responsive support across every learner interaction.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Disputes, refunds, cancellations — handled end-to-end without you.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>48-hour active onboarding with a real phone call, not just a form.</span></li>
                </ul>
                <a href="{{ route('support.request.show', ['topic' => 'instructor']) }}" class="btn btn-warning btn-lg fw-semibold">Get concierge support on your side</a>
            </div>

            <div class="col-lg-6">
                <div class="lg-panel">
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <div class="small text-muted text-uppercase fw-semibold" style="letter-spacing:.05em;">Your concierge desk</div>
                            <div class="fw-bold fs-5">Live activity</div>
                        </div>
                        <span class="lg-badge new ms-auto"><i class="bi bi-circle-fill" style="font-size:.5rem;"></i> Online</span>
                    </div>
                    <div class="lg-row align-items-start" style="background:#e7f7ee;border-radius:.6rem;padding:.7rem .85rem;border-bottom:0;margin-bottom:.5rem;">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <div><div class="fw-semibold small">Refund request · Jamie M. <span class="lg-badge booked" style="background:#cdeedd;color:#1a7f43;">Resolved</span></div><div class="small text-muted">Resolved by concierge team. You not contacted.</div></div>
                    </div>
                    <div class="lg-row align-items-start" style="background:#fff8e1;border-radius:.6rem;padding:.7rem .85rem;border-bottom:0;">
                        <i class="bi bi-hourglass-split text-warning"></i>
                        <div><div class="fw-semibold small">New learner disagreement <span class="lg-badge" style="background:#ffe8a3;color:#8a6d00;">In progress</span></div><div class="small text-muted">Our team is handling. We'll update you only if action is needed.</div></div>
                    </div>
                    <div class="small fw-semibold text-uppercase text-muted mt-3 mb-1" style="letter-spacing:.05em;">Local team online</div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex">
                            <span class="lg-avatar" style="width:28px;height:28px;font-size:.65rem;">SB</span>
                            <span class="lg-avatar" style="width:28px;height:28px;font-size:.65rem;margin-left:-8px;">SK</span>
                            <span class="lg-avatar" style="width:28px;height:28px;font-size:.65rem;margin-left:-8px;">DP</span>
                        </div>
                        <span class="small text-muted">3 agents · avg response 4min</span>
                    </div>
                    <div class="small text-muted fst-italic mt-3">"Over 95% of learner disputes resolved without contacting the instructor."</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── PROBLEM ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="lg-eyebrow neg mb-3">Without us</span>
            <h2 class="fw-bolder mt-3">Every awkward call lands on you. And your evening is gone.</h2>
            <p class="text-muted mx-auto" style="max-width:640px;">
                Going solo means being the support desk, the mediator, and the refund department,
                all after a 10-hour day in the car. Here's what eats independent instructors alive.
            </p>
        </div>
        <div class="row g-4">
            @foreach([
                ['The angry parent call','Kid fails the test. Mum rings you demanding a refund for "wasted lessons". One call, 45 minutes, and she\'s still angry. Your Tuesday night is over.'],
                ['The lateness dispute','Learner claims you were 20 minutes late. You weren\'t. Now it\'s your word against theirs, you have no GPS logs, and the refund comes out of your pocket.'],
                ['The no-show chase','Teenager is a no-show for their lesson. You drove 40 minutes. Now you have to ring them, enforce the cancellation fee, and have uncomfortable conversations.'],
            ] as [$t,$d])
                <div class="col-md-4">
                    <div class="lg-card">
                        <div class="lg-x"><i class="bi bi-x-lg"></i></div>
                        <h5 class="fw-bold">{{ $t }}</h5>
                        <p class="text-muted small mb-0">{{ $d }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── HOW IT WORKS ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="lg-eyebrow mb-3">How it works</span>
            <h2 class="fw-bolder mt-3">You teach. We handle the rest.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lg-step-num">1</div>
                <h5 class="fw-bold">Learner raises an issue</h5>
                <p class="text-muted small">Refund request, complaint, lateness claim, no-show, it goes straight to our experienced team. You're not copied in. You're not notified. You're teaching.</p>
                <div class="lg-statbox mt-3">
                    <div class="row-line"><span class="text-muted">Channel</span><span class="fw-bold">Phone · email · chat</span></div>
                    <div class="row-line"><span class="text-muted">Team location</span><span class="fw-bold">Local Team</span></div>
                    <div class="row-line"><span class="text-muted">Avg first response</span><span class="fw-bold">4 minutes</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">2</div>
                <h5 class="fw-bold">We investigate and decide</h5>
                <p class="text-muted small">We pull the booking data, lesson notes, and prior history. We make the call on the refund. We take the responsibility.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">What we handle</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Refund approvals + processing</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Behaviour complaints from learners</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> No-show fee enforcement</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Cancellations + rescheduling</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">3</div>
                <h5 class="fw-bold">You find out it happened. Maybe.</h5>
                <p class="text-muted small">More than 95% of disputes are resolved without you hearing about it. For the rest, we brief you with context, and find a path that works for all parties.</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i><div><div class="fw-semibold small">Weekly recap · 4 items handled</div><div class="small text-muted">0 required your input</div></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BENEFITS (yellow) ─────────── --}}
<section class="py-5 lg-yellow">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Your evenings back. Your weekends back.</h2>
            <p class="mb-0">Outcomes you'd otherwise pay a franchise 40% of your income to get — and they'd still outsource it.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-people','Real People','Australian local timezone, English speakers, named agents. Not a chatbot.'],
                ['bi-shield-check','Refunds aren\'t your call','We decide. We process. You don\'t negotiate with an upset parent at 8pm. You don\'t wear the bad review. We own the outcome.'],
                ['bi-telephone','48hr active onboarding','Real phone call, not a form that times out. We verify, set up your profile, and help write copy that converts. Live in 48 hours.'],
                ['bi-pencil-square','Profile copy, ongoing','As you grow, we help rewrite your bio, update your pitch, and tune your listing. No extra cost. No "premium tier". It just happens.'],
            ] as [$ic,$t,$d])
                <div class="col-md-6 col-lg-3">
                    <div class="lg-yellow-card">
                        <div class="lg-yellow-ic"><i class="bi {{ $ic }}"></i></div>
                        <h6 class="fw-bold">{{ $t }}</h6>
                        <p class="text-muted small mb-0">{{ $d }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── SOCIAL PROOF ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Three instructors. Zero awkward phone calls.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Martin · 847 lessons · Gold Coast','"A learner claimed I was 20 minutes late. I wasn\'t. The concierge team pulled my GPS data, refuted it, denied the refund. I found out in a weekly recap. I never had to make the call."'],
                ['Priya · 412 lessons · Sydney','"A learner\'s mum wanted a full refund after her daughter failed the test. That used to be my nightmare call. Secure Licence handled the whole thing. I wasn\'t copied in once."'],
                ['Angelo · 600 lessons','"My franchise \'support\' was a phone tree in another country. Secure Licence support is Jess, Sam, and Dan, in Adelaide, with names, who pick up. Not even close."'],
            ] as [$name,$quote])
                <div class="col-md-4">
                    <div class="lg-card">
                        <div class="lg-avatar mb-3"><i class="bi bi-person-fill"></i></div>
                        <div class="fw-bold mb-2">{{ $name }}</div>
                        <p class="text-muted small mb-0">{{ $quote }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── ECOSYSTEM ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="h4 fw-bolder">Works seamlessly with every other part of the marketplace</h2>
        </div>
        <div class="row g-3">
            @foreach([
                ['bi-megaphone','Lead Generation','300k+ learners ready to book', route('for-instructors.lead-generation')],
                ['bi-clock-history','Work Whenever You Want','Mornings, nights, weekends', route('for-instructors.work-whenever-you-want')],
                ['bi-hand-thumbs-up','Flexible Commitment','Pause or leave anytime', route('for-instructors.flexible-commitment')],
                ['bi-person-vcard','Your Listing','How learners see you', route('for-instructors.your-listing-profile')],
                ['bi-star','Reviews & Reputation','Ratings drive more leads', route('for-instructors.reputation-management')],
                ['bi-tools','Tools included','Calendar, payments, SMS, free', route('instruct-with-us')],
            ] as [$ic,$t,$d,$url])
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ $url }}" class="text-decoration-none text-reset">
                        <div class="lg-mini">
                            <div class="lg-mini-ic"><i class="bi {{ $ic }}"></i></div>
                            <div class="fw-semibold small">{{ $t }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $d }}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── FINAL CTA ─────────── --}}
<section class="py-5 text-white" style="background:#14171c;">
    <div class="container text-center">
        <div class="text-warning fw-bold small text-uppercase mb-2" style="letter-spacing:.08em;">Zero upfront. No contract. Live in 48 hours.</div>
        <h2 class="fw-bolder mb-2 text-white">Start getting bookings this week.</h2>
        <p class="text-white-50 mb-4 mx-auto" style="max-width:560px;">
            Join instructors earning on Secure Licence. 15 minutes to apply, reviewed within 2 business days. Leave whenever you want.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="{{ route('support.request.show', ['topic' => 'instructor']) }}" class="btn btn-warning btn-lg fw-semibold">Apply to join Secure Licence</a>
            <a href="{{ route('instruct-with-us') }}" class="btn btn-outline-light btn-lg">Back to overview</a>
        </div>
    </div>
</section>

@endsection
