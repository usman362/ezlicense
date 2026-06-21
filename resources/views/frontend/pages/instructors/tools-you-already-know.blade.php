@extends('layouts.frontend')
@section('title', 'Tools You Already Know — Calendar, Payments, SMS Included — Secure Licence')
@section('meta_description', 'Works with the tools you already use. Connect Google Calendar, keep your favourite apps, and get card payments, SMS and email reminders included free.')

@push('styles')
    @include('frontend.pages.instructors._feature-styles')
    <style>
        .lg-tools-table{width:100%;border-collapse:separate;border-spacing:0;}
        .lg-tools-table th,.lg-tools-table td{padding:.85rem 1rem;vertical-align:middle;font-size:.9rem;}
        .lg-tools-table thead th{font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;color:#8b929c;border-bottom:2px solid #eef0f2;}
        .lg-tools-table tbody td{border-bottom:1px solid #f1f3f5;}
        .lg-tools-table .col-sl{background:#fffbea;}
        .lg-tools-table thead .col-sl{background:#ffd500;color:#1a1d21;border-radius:.6rem .6rem 0 0;font-weight:700;}
        .lg-tools-table tr.absorb td{background:#fff8e1;}
        .lg-tools-table tr.absorb .col-sl{background:#fff3cd;}
        .lg-absorb-tag{display:inline-block;background:#ffe8a3;color:#8a6d00;font-size:.62rem;font-weight:700;padding:.1rem .4rem;border-radius:.3rem;margin-left:.35rem;vertical-align:middle;}
        .lg-x-sm{color:#b42318;}.lg-tick-sm{color:#1a7f43;}
    </style>
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
            <span class="text-dark">Tools You Already Know</span>
        </nav>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="lg-eyebrow mb-3">Tools Included</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">Works with the tools you already use. Plus more, free.</h1>
                <p class="text-muted mb-4">
                    Secure Licence plugs in alongside your existing setup. Connect your Google Calendar, keep
                    using your favourite apps, and pick up the extras included for free — card payments, SMS and
                    email reminders, late-cancel enforcement, per-learner notes. About $1,830/year of value,
                    sitting on top of every lead we send you.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Works alongside your existing tools, no need to rip and replace.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Roughly $1,830/year of tools and fees included at no cost.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Stripe bank payouts on your chosen 7, 14 or 28-day cycle, dispute and refund fees absorbed.</span></li>
                </ul>
                <a href="{{ route('support.request.show', ['topic' => 'instructor']) }}" class="btn btn-warning btn-lg fw-semibold">Join the marketplace, free</a>
            </div>

            <div class="col-lg-6">
                <div class="lg-panel">
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <div class="small text-muted text-uppercase fw-semibold" style="letter-spacing:.05em;">Your stack</div>
                            <div class="fw-bold fs-5">Yours, plus ours</div>
                        </div>
                        <span class="lg-badge new ms-auto">Included</span>
                    </div>
                    <div class="lg-row">
                        <i class="bi bi-calendar3 text-primary"></i>
                        <div><div class="fw-semibold small">Google Cal</div><div class="small text-muted">2-way sync</div></div>
                        <span class="lg-badge booked ms-auto" style="background:#cdeedd;color:#1a7f43;">Connected</span>
                    </div>
                    <div class="lg-row">
                        <i class="bi bi-credit-card text-info"></i>
                        <div><div class="fw-semibold small">Stripe</div><div class="small text-muted">This month</div></div>
                        <span class="fw-bold ms-auto">$1,420.00</span>
                    </div>
                    <div class="lg-row">
                        <i class="bi bi-chat-dots text-warning"></i>
                        <div><div class="fw-semibold small">SMS sent</div><div class="small text-muted">Reminders + confirmations</div></div>
                        <span class="fw-bold ms-auto">214</span>
                    </div>
                    <div class="mt-3 p-3 rounded" style="background:#fff8e1;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold small">Value included</span>
                            <span class="fw-bolder text-success">~$1,830/year</span>
                        </div>
                        <div class="small text-muted">In tools and fees Secure Licence covers for you.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── COMPARISON TABLE ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <span class="lg-eyebrow mb-3">What's included</span>
            <h2 class="fw-bolder mt-3">The itemised list of tools and fees included.</h2>
            <p class="text-muted mx-auto" style="max-width:680px;">
                Here's what an independent instructor would normally pay each month or per transaction to run
                a modern lesson business, alongside what a Secure Licence instructor pays for the same.
            </p>
        </div>

        <div class="lg-card p-0" style="overflow-x:auto;">
            <table class="lg-tools-table">
                <thead>
                    <tr>
                        <th>Tool / Service</th>
                        <th class="text-center">Standalone<br><span class="fw-normal" style="text-transform:none;">what others charge</span></th>
                        <th class="text-center col-sl">Secure Licence<br><span class="fw-normal" style="text-transform:none;">marketplace</span></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $rows = [
                            ['Calendar app','e.g. Calendly, Yedo','~$15–25/mo','Included', false],
                            ['Card payments','Stripe standalone setup','1.7% + 30c per transaction','Included', false],
                            ['SMS reminders','e.g. Twilio direct','~$0.04 per SMS','Included', false],
                            ['Email reminders','e.g. Mailchimp / SendGrid','~$15/mo for low volume','Included', false],
                            ['Late-cancel enforcement & refunds','manual via PayPal','~$10–30 per dispute','Included', false],
                            ['PayPal dispute fees','chargebacks on learner refunds','$20 per chargeback','Absorbed by Secure Licence', true],
                            ['Refund processing fees','Stripe / PayPal fee on refunds','1.7% + 30c per transaction','Absorbed by Secure Licence', true],
                            ['Klarna / Afterpay BNPL fees','pay-in-4 transaction surcharges','4–6% per transaction','Absorbed by Secure Licence', true],
                            ['Test packages booking flow','custom-built technology','Custom build (~$5k+)','Included', false],
                            ['Per-learner notes / CRM','e.g. HubSpot free tier limits','~$15–25/mo','Included', false],
                        ];
                    @endphp
                    @foreach($rows as [$name,$sub,$standalone,$sl,$absorb])
                        <tr class="{{ $absorb ? 'absorb' : '' }}">
                            <td>
                                <div class="fw-semibold">{{ $name }}@if($absorb)<span class="lg-absorb-tag">SL ABSORBS</span>@endif</div>
                                <div class="small text-muted">{{ $sub }}</div>
                            </td>
                            <td class="text-center"><i class="bi bi-x-circle-fill lg-x-sm me-1"></i>{{ $standalone }}</td>
                            <td class="text-center col-sl"><i class="bi bi-check-circle-fill lg-tick-sm me-1"></i>{{ $sl }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="small text-muted text-center mt-3">
            These are costs Secure Licence absorbs from the marketplace. You don't see them. You don't pass them on. You don't pay them.
        </p>
        <div class="text-center">
            <a href="{{ route('support.request.show', ['topic' => 'instructor']) }}" class="btn btn-warning fw-semibold">Join the marketplace, free</a>
        </div>
    </div>
</section>

{{-- ─────────── HOW IT WORKS ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="lg-eyebrow mb-3">How it works</span>
            <h2 class="fw-bolder mt-3">Plug in, keep what works, add the extras.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lg-step-num">1</div>
                <h5 class="fw-bold">Connect what you already use</h5>
                <p class="text-muted small">Sync your Google Calendar in two clicks. Set the suburbs you cover, your lesson durations, your rate. Secure Licence slots into your existing routine, no rip and replace.</p>
                <div class="lg-statbox mt-3">
                    <div class="row-line"><span class="text-muted">Google Cal</span><span class="fw-bold text-success">Connected</span></div>
                    <div class="row-line"><span class="text-muted">Service area</span><span class="fw-bold">13 suburbs</span></div>
                    <div class="row-line"><span class="text-muted">Durations</span><span class="fw-bold">1hr to 10hr</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">2</div>
                <h5 class="fw-bold">We add payments, reminders, dispute cover</h5>
                <p class="text-muted small">Stripe takes the payment at booking. Confirmation SMS and email go out automatically. Late-cancel enforcement runs in the background. Refund and chargeback fees we absorb so they never hit you.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">Included automatically</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Booking confirmation</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> 24-hour reminder</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Reschedule and cancel alerts</div>
                    <div class="d-flex gap-2 small"><i class="bi bi-check-circle-fill text-warning"></i> Late-cancel fee auto-charged</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">3</div>
                <h5 class="fw-bold">You teach. Money lands on your schedule.</h5>
                <p class="text-muted small">Lesson happens. Stripe pays out on your chosen 7, 14 or 28-day cycle. Per-learner profiles track notes and progress. No invoices, no chasing, no admin tax on your week.</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i><div><div class="fw-semibold small">Payout · $1,420.00</div><div class="small text-muted">to your bank · 14-day cycle</div></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BENEFITS (yellow) ─────────── --}}
<section class="py-5 lg-yellow">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Leads, plus the tools, plus the fees absorbed.</h2>
            <p class="mb-0">We send you the learners. We bundle in the tools you'd otherwise be patching together. And the fees that normally chip away at your margin, we absorb on your behalf.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-cash-stack','~$1,830/year of value','Calendar, SMS, email, CRM, plus dispute and BNPL fees absorbed. Roughly $1,830/year of tooling and pass-through costs you don\'t pick up.'],
                ['bi-graph-up-arrow','Flexible Stripe payouts','Money lands in your bank on your chosen 7, 14 or 28-day cycle. Not full monthly, not whenever a franchise runs its drip.'],
                ['bi-shield-check','No-show protection built in','Late-cancel fees are charged automatically. You don\'t chase. You don\'t argue. The platform enforces it the same way an airline does.'],
                ['bi-plug','Works with your existing setup','Keep your Google Calendar. Keep your favourite apps. Secure Licence sits alongside, picking up the parts you don\'t want to run yourself.'],
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
            <h2 class="fw-bolder">Three instructors. Same tools bundled in.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Martin K. · 847 lessons · Gold Coast','"I kept Google Calendar and my phone. Secure Licence took over the booking page, the card payments, the reminders. The bits I never wanted to build are just there."'],
                ['Priya S. · 412 lessons · Sydney','"I\'d never used \'tech\' before joining. The concierge walked me through every setting. Now I love Stripe, it pays me before I\'ve even cashed up."'],
                ['Angelo D. · 600 lessons','"Had a chargeback come through last month. Secure Licence absorbed the $20 PayPal fee. I didn\'t even know it had happened until I checked. That alone made my year."'],
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
                ['bi-gem','Concierge Support','We handle the hard calls', route('for-instructors.white-glove-concierge')],
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
