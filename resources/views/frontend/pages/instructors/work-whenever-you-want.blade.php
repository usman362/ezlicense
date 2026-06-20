@extends('layouts.frontend')
@section('title', 'Work Whenever You Want — Driving Instructor Hours — Secure Licence')
@section('meta_description', 'Set your own hours. Weekends only, weekday afternoons, 5 hours a week or 50 — you pick. We only send learners whose availability overlaps yours.')

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
            <span class="text-dark">Work Whenever You Want</span>
        </nav>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="lg-eyebrow mb-3">Work Whenever You Want</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">Choose your hours. Work around your life, not a franchise roster.</h1>
                <p class="text-muted mb-4">
                    Weekends only. Weekday afternoons. School-run windows. 5 hours a week or 50 — you pick.
                    We only send you learners whose availability actually overlaps yours, so no more
                    "sorry, I can't do Tuesdays" back-and-forth.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Set mornings, nights, weekends — any combo you want.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>No minimum hours. Change in two taps. Blackout any date.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>The algorithm only sends learners who fit your schedule.</span></li>
                </ul>
                <a href="{{ route('instructor-application.show') }}" class="btn btn-warning btn-lg fw-semibold">Set your hours and start earning</a>
            </div>

            <div class="col-lg-6">
                <div class="lg-panel">
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <div class="small text-muted text-uppercase fw-semibold" style="letter-spacing:.05em;">Your working hours</div>
                            <div class="fw-bold fs-5">This week</div>
                        </div>
                        <span class="lg-badge new ms-auto">Live</span>
                    </div>
                    @foreach([
                        ['Mon', true, '9am–6pm', 12, 78],
                        ['Tue', true, '9am–9pm', 5, 95],
                        ['Wed', true, '9am–6pm', 12, 78],
                        ['Thu', true, '4pm–9pm', 55, 40],
                        ['Fri', false, 'Off', 0, 0],
                        ['Sat', true, '7am–5pm', 0, 88],
                        ['Sun', false, 'Off', 0, 0],
                    ] as [$day, $on, $label, $left, $width])
                        <div class="lg-row">
                            <span class="fw-semibold small" style="width:34px;">{{ $day }}</span>
                            <span class="lg-toggle {{ $on ? '' : 'off' }}"></span>
                            <span class="lg-bar">@if($on)<span style="left:{{ $left }}%;width:{{ $width }}%;"></span>@endif</span>
                            <span class="small text-muted" style="width:64px;text-align:right;">{{ $label }}</span>
                        </div>
                    @endforeach
                    <div class="d-flex align-items-center gap-2 mt-3 small text-success fw-semibold">
                        <i class="bi bi-broadcast"></i> 33 hours available · matching 4 learners
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
            <span class="lg-eyebrow neg mb-3">Without us</span>
            <h2 class="fw-bolder mt-3">Flexibility was supposed to be the whole point.</h2>
            <p class="text-muted mx-auto" style="max-width:640px;">
                You became an instructor to own your time. Most setups hand it straight back.
            </p>
        </div>
        <div class="row g-4">
            @foreach([
                ['Franchise roster, not yours','Mandatory Saturday shifts. "Compulsory" 6am starts. Refuse a slot and your leads get rerouted. You\'re an employee wearing a contractor costume.'],
                ['Solo means "say yes to everything"','Going independent sounds free, until every learner who messages is rent money. You teach Sundays you didn\'t want, before school runs that wreck your morning.'],
                ['Calendars without geography','Other platforms match by time slot alone. You accept, then realise the learner\'s a 45-min drive each way. Your unpaid hours bookending a 1-hour lesson.'],
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
            <h2 class="fw-bolder mt-3">You set the rules. We respect them.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lg-step-num">1</div>
                <h5 class="fw-bold">Draw your week</h5>
                <p class="text-muted small">Toggle days on. Drag to set hours. Set a travel radius. Takes 60 seconds and you can change it any time from your phone.</p>
                <div class="lg-statbox mt-3">
                    <div class="row-line"><span class="text-muted">Minimum hours</span><span class="fw-bold text-success">None</span></div>
                    <div class="row-line"><span class="text-muted">Travel radius</span><span class="fw-bold">Up to 30 min</span></div>
                    <div class="row-line"><span class="text-muted">Edits allowed</span><span class="fw-bold">Unlimited</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">2</div>
                <h5 class="fw-bold">Algorithm filters leads</h5>
                <p class="text-muted small">Learners whose availability doesn't overlap yours never see you. No "could you do Tuesday?" DMs. No awkward declines.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">Matched only if</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Their hours overlap yours</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> They're inside your radius</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Date isn't blacked out</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Transmission you teach</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">3</div>
                <h5 class="fw-bold">Life comes up? Blackout it.</h5>
                <p class="text-muted small">Kid's school holidays. Wedding in Byron. A week of footy finals. Tap a date range, hit blackout, done. No approvals, no penalties.</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i><div><div class="fw-semibold small">Blackout set · 22–30 Sep</div><div class="small text-muted">School holidays · no new bookings</div></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BENEFITS (yellow) ─────────── --}}
<section class="py-5 lg-yellow">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">A driving business shaped around your life.</h2>
            <p class="mb-0">Reclaim your weekends. Or fill them. Your call — and you can change your mind every Monday.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-clock','Work 5 hours or 50','No minimum commitment. Side-hustle it around a day job, or scale up to full-time. The platform doesn\'t care and neither do we.'],
                ['bi-hand-index-thumb','Change anytime, 2 taps','Kid got sick? Back surgery? Moving interstate next month? Edit your hours from the pub car park. It\'s live in 10 seconds.'],
                ['bi-geo-alt','Geography-aware matching','You say "30 min max" — we only send learners inside that radius. No more 90-min round trips for a 60-minute lesson.'],
                ['bi-emoji-smile','No pressure to say yes','Because we feed leads continually, you\'re never one lost booking away from a bad week. Decline what doesn\'t fit. The next match is already coming.'],
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
            <h2 class="fw-bolder">Three instructors. Three totally different weeks.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Martin · 847 lessons · Gold Coast','"I kept my Mon–Fri electrician job and run Secure Licence weekends only. 14 lessons Sat–Sun, $1,400 extra a week, and I never touched my weekdays. Tried to do this with a franchise — they laughed."'],
                ['Priya · 412 lessons · Sydney','"School pickups at 3, so I work 9:30–2:30 four days a week. No evenings, no weekends. The algorithm just never sends me a 4pm slot. That alone is worth it."'],
                ['Angelo · 600 lessons','"Full business, 45 hours a week, 6 days. But I close shop every January, 4 weeks off, zero penalty. Try asking a franchise for that. I run this like I own it — because I do."'],
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
                ['bi-hand-thumbs-up','Flexible Commitment','Pause or leave anytime', route('instruct-with-us')],
                ['bi-person-vcard','Your Listing','How learners see you', route('instruct-with-us')],
                ['bi-star','Reviews & Reputation','Ratings drive more leads', route('instruct-with-us')],
                ['bi-gem','Concierge Support','We handle the hard calls', route('instruct-with-us')],
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
            <a href="{{ route('instructor-application.show') }}" class="btn btn-warning btn-lg fw-semibold">Apply to join Secure Licence</a>
            <a href="{{ route('instruct-with-us') }}" class="btn btn-outline-light btn-lg">Back to overview</a>
        </div>
    </div>
</section>

@endsection
