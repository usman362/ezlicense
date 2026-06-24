@extends('layouts.frontend')
@section('title', 'Automated Reminders — SMS & Email — Secure Licence')
@section('meta_description', 'Every booking confirmed, every 24-hour reminder sent, every reschedule communicated — from your name, your number, automatically. Australian SMS delivery.')

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
            <a href="{{ route('instruct-with-us') }}" class="text-muted text-decoration-none">For Business</a>
            <span class="mx-1">/</span>
            <span class="text-dark">Automated Reminders</span>
        </nav>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="lg-eyebrow mb-3">Automated Reminders</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">The professionalism you already have, finally visible.</h1>
                <p class="text-muted mb-4">
                    Every booking confirmed. Every 24-hour reminder sent. Every reschedule communicated. From your
                    name, your number, in full sentences. Learners think you're running a franchise. You're not —
                    you just look like one.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>SMS + email, unified, no Zapier stitching.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Australian SMS delivery (not US-routed with 30% drop).</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Branded from your name, not "Secure Licence" or a short code.</span></li>
                </ul>
                <a href="{{ route('support.request.show', ['topic' => 'instructor']) }}" class="btn btn-warning btn-lg fw-semibold">Automate your reminders</a>
            </div>

            <div class="col-lg-6">
                <div class="lg-panel">
                    <div class="lg-row align-items-start" style="border-bottom:0;">
                        <span class="lg-avatar" style="background:#1a7f43;color:#fff;"><i class="bi bi-chat-fill"></i></span>
                        <div>
                            <div class="fw-semibold small">Aria, Driving Instructor <span class="text-muted fw-normal">· SMS · Now</span></div>
                            <div class="small text-muted p-2 rounded mt-1" style="background:#f1f3f5;">Hi Ella 👋 Just a reminder, your 1hr driving lesson is tomorrow (Fri 19 Apr) at 9:00am. I'll pick you up from 14 Waverly St. Reply STOP to cancel. See you there!</div>
                        </div>
                    </div>
                    <div class="lg-row align-items-start" style="border-bottom:0;">
                        <span class="lg-avatar">CF</span>
                        <div>
                            <div class="fw-semibold small">Caleb Foster <span class="text-muted fw-normal">· jamiereid@example.com · 2 min ago</span></div>
                            <div class="small text-muted p-2 rounded mt-1" style="background:#f1f3f5;">Hi Ella, your 1-hour driving lesson is confirmed. I'll pick you up from 14 Waverly St at 9:00am sharp.</div>
                        </div>
                    </div>
                    <div class="mt-2 p-2 rounded small d-flex align-items-center gap-2" style="background:#fff8e1;">
                        <i class="bi bi-calendar-plus text-warning"></i> Add to calendar: Google · Apple · Outlook
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── PROBLEM ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="lg-eyebrow neg mb-3">Without it</span>
            <h2 class="fw-bolder mt-3">You're typing the same message 30 times a week.</h2>
            <p class="text-muted mx-auto" style="max-width:640px;">And missing a few. And looking inconsistent. And burning evening hours that should be yours.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['10pm reminder thread','You spend Sunday night texting reminders to tomorrow\'s learners. Your partner glares. You forget one. They don\'t show.'],
                ['Inconsistent voice','Each reminder is slightly different. Some have the address. Some don\'t. Some are three words. You look amateur.'],
                ['The missed reschedule','You moved a lesson at 8am. At 10am you realise you forgot to tell them. They\'re at the original address. You\'re at the new one.'],
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
            <h2 class="fw-bolder mt-3">Set once. Forget forever.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lg-step-num">1</div>
                <h5 class="fw-bold">Built-in for every key event</h5>
                <p class="text-muted small">Confirmation at booking, 24hr reminder, 2hr heads-up, reschedule notice, cancellation receipt, payment confirmation. Sent automatically to learners — and you get a copy too, so you always know what's gone out.</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex gap-2 small mb-1"><i class="bi bi-check-circle-fill text-warning"></i> Booking confirmed</div>
                    <div class="d-flex gap-2 small mb-1"><i class="bi bi-check-circle-fill text-warning"></i> 24hr reminder</div>
                    <div class="d-flex gap-2 small mb-1"><i class="bi bi-check-circle-fill text-warning"></i> 2hr heads-up</div>
                    <div class="d-flex gap-2 small mb-1"><i class="bi bi-check-circle-fill text-warning"></i> Reschedule notice</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Late-cancel fee</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">2</div>
                <h5 class="fw-bold">Customise the wording (optional)</h5>
                <p class="text-muted small">Defaults work great. But if you want to sound more you, edit every template. SMS and email, separately.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">SMS template</div>
                    <div class="small font-monospace text-muted">Hi {learner_name} 👋 Just a reminder, your {duration} driving lesson is tomorrow ({date}) at {time}. I'll pick you up from {pickup_address}.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">3</div>
                <h5 class="fw-bold">Every event fires automatically</h5>
                <p class="text-muted small">You literally do nothing. The system knows when a lesson is in 24hr. It sends. Learner replies? You see it in your dashboard.</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex gap-2 small mb-1"><span class="text-muted">9:00am</span> Sent SMS to Ella T.</div>
                    <div class="d-flex gap-2 small mb-1"><span class="text-muted">9:00am</span> Sent email to Ella T.</div>
                    <div class="d-flex gap-2 small"><span class="text-muted">9:14am</span> Ella replied "Cheers see you then!"</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BENEFITS (yellow) ─────────── --}}
<section class="py-5 lg-yellow">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Less typing. Fewer no-shows. Better vibes.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-clock','5 hrs/wk back','No more typing reminders. No more checking if you sent them.'],
                ['bi-graph-up-arrow','40% fewer no-shows','SMS is the highest-engagement channel. A well-timed one kills the "I forgot" excuse.'],
                ['bi-heart','Professional feel','Parents of new drivers see the SMS trail and relax. Referrals go up.'],
                ['bi-shield-check','Audit trail','Every message logged. Dispute over a late-cancel? You\'ve got the proof.'],
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
            <h2 class="fw-bolder">Instructors who got their evenings back.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Brooke · 9 years · Adelaide','"I used to spend 45 mins every Sunday night texting reminders. Now I don\'t. My Sundays belong to me again."'],
                ['Jake · 3 years · Newcastle','"No-shows dropped from 4–5 a month to maybe 1. That\'s literally $400/mo extra income for doing nothing different."'],
                ['Bianca · New instructor · Canberra','"A mum told me my reminder SMS was the most professional thing she\'d seen from an instructor. I literally do nothing."'],
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

@include('frontend.pages.instructors._ecosystem', ['exclude' => 'automated-reminders'])
@include('frontend.pages.instructors._business-cta')

@endsection
