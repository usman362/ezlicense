@extends('layouts.frontend')
@section('title', 'Calendar & Scheduling — 2-Way Google Sync — Secure Licence')
@section('meta_description', 'Real 2-way Google Calendar sync, service-area aware, with holiday and exception handling built in. Learners only see slots you actually have free.')

@push('styles')
    @include('frontend.pages.instructors._feature-styles')
    <style>
        .lg-cal{display:grid;grid-template-columns:repeat(5,1fr);gap:4px;}
        .lg-cal .hd{font-size:.65rem;color:#8b929c;text-align:center;font-weight:600;}
        .lg-cal .slot{background:#ffd500;color:#1a1d21;border-radius:.35rem;padding:.3rem;font-size:.6rem;font-weight:600;line-height:1.2;}
        .lg-cal .slot.muted{background:#f1f3f5;color:#adb5bd;}
        .lg-cal .slot.blocked{background:repeating-linear-gradient(45deg,#f1f3f5,#f1f3f5 4px,#e9ecef 4px,#e9ecef 8px);color:#868e96;}
    </style>
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
            <span class="text-dark">Calendar &amp; Scheduling</span>
        </nav>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="lg-eyebrow mb-3">Smart Calendar &amp; Scheduling</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">Your Google Calendar, working harder.</h1>
                <p class="text-muted mb-4">
                    Real 2-way sync. Service-area aware. Holiday &amp; exception handling built in. Your learners
                    only see the slots you actually have free — and your weekends stop getting double-booked.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Real-time 2-way Google Calendar sync (not import/export).</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Service-area polygons — learners outside your zone can't book.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Working hours + exception dates (sick days, holidays, public events).</span></li>
                </ul>
                <a href="{{ route('support.request.show', ['topic' => 'instructor']) }}" class="btn btn-warning btn-lg fw-semibold">Get started</a>
            </div>

            <div class="col-lg-6">
                <div class="lg-panel">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-calendar3 text-primary me-2"></i>
                        <div class="fw-bold">April 2026</div>
                    </div>
                    <div class="lg-cal mb-2">
                        @foreach(['Mon 20','Tue 21','Wed 22','Thu 23','Fri 24'] as $d)
                            <div class="hd">{{ $d }}</div>
                        @endforeach
                        <div class="slot">Ella A.<br>9:00–10:00</div>
                        <div class="slot muted">&nbsp;</div>
                        <div class="slot blocked">Blocked</div>
                        <div class="slot">Zoe F.<br>18:30–19:30</div>
                        <div class="slot muted">&nbsp;</div>

                        <div class="slot">Hudson T.<br>11:00–12:00</div>
                        <div class="slot">Aisha M.<br>13:30–14:30</div>
                        <div class="slot muted">&nbsp;</div>
                        <div class="slot">Noah M.<br>14:00–15:30</div>
                        <div class="slot">Ethan B.<br>10:00–11:00</div>

                        <div class="slot">Oliver B.<br>16:00–17:00</div>
                        <div class="slot muted">&nbsp;</div>
                        <div class="slot muted">&nbsp;</div>
                        <div class="slot muted">&nbsp;</div>
                        <div class="slot">Isla S.<br>16:00–17:00</div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-3 small text-success fw-semibold">
                        <i class="bi bi-arrow-repeat"></i> Synced with Google Calendar · updated 2s ago
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
            <h2 class="fw-bolder mt-3">Every independent instructor has the same three nightmares.</h2>
            <p class="text-muted mx-auto" style="max-width:640px;">You probably live one of these weekly. We've built this feature specifically to kill each one.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['The double-booking','You wrote the lesson in your diary. You didn\'t put it in Google Calendar. Your partner books a family dinner on the same slot. Awkward call incoming.'],
                ['The 45-min drive','A new learner books a lesson. They live 45 minutes outside your service area. You didn\'t check. You drive, you lose, you can\'t say no now.'],
                ['The forgotten sick day','You\'re unwell Tuesday. You tell three learners. You forget the fourth. They show up, you don\'t. Reputation dinged.'],
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
            <h2 class="fw-bolder mt-3">Three-minute setup. Lifetime peace.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lg-step-num">1</div>
                <h5 class="fw-bold">Connect your Google Calendar</h5>
                <p class="text-muted small">One click. Your existing calendar becomes the source of truth. Everything else (booking, reminders, payments) references it.</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-calendar3 text-primary"></i><div><div class="fw-semibold small">Google Calendar</div><div class="small text-muted">Connected · tobi@example.com</div></div><i class="bi bi-check-circle-fill text-success ms-auto"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">2</div>
                <h5 class="fw-bold">Define your working hours + service area</h5>
                <p class="text-muted small">Draw your area on a map. Set Mon–Fri 9–5 (or whatever you want). Add exceptions for holidays or gym days.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">Working hours</div>
                    <div class="row-line"><span class="text-muted">Mon – Fri</span><span class="fw-bold">08:00 – 18:00</span></div>
                    <div class="row-line"><span class="text-muted">Sat</span><span class="fw-bold">09:00 – 13:00</span></div>
                    <div class="row-line"><span class="text-muted">Sun</span><span class="fw-bold text-muted">Closed</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">3</div>
                <h5 class="fw-bold">Send your booking link</h5>
                <p class="text-muted small">Your learners click it. They only see valid slots. They book. Your calendar updates in real time. You never get interrupted.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">Your booking link</div>
                    <div class="small font-monospace text-truncate">securelicence.com.au/book/tobi-d</div>
                    <div class="d-flex gap-2 mt-2"><span class="btn btn-warning btn-sm flex-fill">Copy link</span><span class="btn btn-outline-dark btn-sm flex-fill">Share</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BENEFITS (yellow) ─────────── --}}
<section class="py-5 lg-yellow">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">What this actually gives you</h2>
            <p class="mb-0">Not features. Outcomes.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-shield-check','Zero double-bookings','Your Google Calendar is checked every time someone tries to book. Conflicts are prevented at the gate.'],
                ['bi-geo-alt','No more long drives','Learners outside your service area literally cannot book. They see "no availability" and move on.'],
                ['bi-clock','3 hrs/week back','No more re-typing your diary into three places. No more "when are you free?" text threads.'],
                ['bi-calendar-x','Take a sick day properly','Block out a day in 2 taps. Everyone gets auto-notified. You actually rest.'],
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
            <h2 class="fw-bolder">Real instructors. Real wins.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Emma · 12 years · Sydney North','"I used to triple-book myself every month. Now my calendar is my calendar. I haven\'t double-booked in 8 months."'],
                ['Raj · 4 years · Melbourne SE','"Service-area polygons killed the 40-minute drives. I teach more lessons closer to home. My day feels lighter."'],
                ['Holly · New instructor · Gold Coast','"I set it up on a Sunday. By Wednesday I had 14 lessons booked. Haven\'t sent a single \'when are you free\' text."'],
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

@include('frontend.pages.instructors._ecosystem', ['exclude' => 'calendar-scheduling'])
@include('frontend.pages.instructors._business-cta')

@endsection
