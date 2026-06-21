@extends('layouts.frontend')
@section('title', 'Learner Management — One Record Per Learner — Secure Licence')
@section('meta_description', 'Every learner, one record. Contact details, lesson history and private notes in one place. Scale from 5 learners to 50+ without losing track of anyone.')

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
            <span class="text-dark">Learner Management</span>
        </nav>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="lg-eyebrow mb-3">Learner Management</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">Every learner, one record. From enquiry to test pass.</h1>
                <p class="text-muted mb-4">
                    Contact details, lesson history, private notes, all in one place. Scale from 5 learners to 50+
                    without losing track of anyone. Every conversation, every note, every future lesson, searchable
                    in two taps.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>One profile per learner, contact, stage, suburb, emergency contact.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Private notes only you see, your mental model, externalised.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Family workflows, siblings, parents paying for kids, linked cleanly.</span></li>
                </ul>
                <a href="{{ route('support.request.show', ['topic' => 'instructor']) }}" class="btn btn-warning btn-lg fw-semibold">Organise your learners</a>
            </div>

            <div class="col-lg-6">
                <div class="lg-panel">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="lg-avatar" style="width:48px;height:48px;">OK</span>
                        <div class="flex-grow-1">
                            <div class="fw-bold">Olivia Kim</div>
                            <div class="small text-muted">L's · Stage 3 · 14 lessons in</div>
                        </div>
                        <span class="lg-badge new">Active</span>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6"><div class="small text-muted text-uppercase" style="letter-spacing:.05em;font-size:.65rem;">Phone</div><div class="small fw-semibold">0400 000 000</div></div>
                        <div class="col-6"><div class="small text-muted text-uppercase" style="letter-spacing:.05em;font-size:.65rem;">Suburb</div><div class="small fw-semibold">Marrickville</div></div>
                    </div>
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;font-size:.65rem;">Lesson history</div>
                    @foreach([
                        ['Thu 9 Apr · 90 min','Highway driving · merging practice','Marrickville'],
                        ['Tue 1 Apr · 60 min','Roundabouts · reverse parallel','Newtown'],
                        ['Thu 26 Mar · 90 min','Lane changes · blind spots','Marrickville'],
                        ['Sat 21 Mar · 90 min','First highway attempt · nervous but solid','Tempe'],
                    ] as [$when,$what,$where])
                        <div class="lg-row" style="padding:.4rem 0;">
                            <i class="bi bi-clock-history text-warning"></i>
                            <div><div class="fw-semibold small">{{ $when }}</div><div class="small text-muted">{{ $what }}</div></div>
                            <span class="small text-muted ms-auto">{{ $where }}</span>
                        </div>
                    @endforeach
                    <div class="mt-2 p-2 rounded small" style="background:#fff8e1;">
                        <span class="fw-semibold"><i class="bi bi-lock-fill text-warning"></i> Private note</span><br>
                        <span class="text-muted">Struggles with roundabouts, keep practicing. Mum pays, dad does supervision. Test booked for June.</span>
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
            <h2 class="fw-bolder mt-3">Your learners live in seven places. None of them talk.</h2>
            <p class="text-muted mx-auto" style="max-width:640px;">Phone contacts, WhatsApp threads, the notebook in your glovebox, that one Google Doc. Here's what that costs you.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['The "which one are you?"','Learner calls. You recognise the voice but not the name. You fumble. They notice. You lose 10 points of professionalism in 3 seconds.'],
                ['The lost context','"What did we cover last time?" You stare at them. They stare at you. You re-do roundabouts for the third week running. They\'re not progressing.'],
                ['The repeat-customer trap','You taught their cousin two years ago. Their friend last month. They reach out again, and you can\'t remember a thing about how the previous lessons went. Awkward.'],
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
            <h2 class="fw-bolder mt-3">Import once. Trust it forever.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lg-step-num">1</div>
                <h5 class="fw-bold">Invite your existing learners</h5>
                <p class="text-muted small">Share your invite link, or fire off an SMS invite straight from your dashboard. Your learners create their own profile from the link, no spreadsheet juggling on your end.</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i><div><div class="fw-semibold small">Added 12 learners</div><div class="small text-muted">From learners-export.csv</div></div></div>
                    <div class="small text-success mt-1"><i class="bi bi-check-circle-fill"></i> Profiles ready · 0 duplicates</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">2</div>
                <h5 class="fw-bold">Build their profile as you teach</h5>
                <p class="text-muted small">Every booking adds to the history. Add a private note after each lesson. Mark licence stage. The record grows itself.</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex align-items-center gap-2 mb-2"><span class="lg-avatar" style="width:30px;height:30px;font-size:.65rem;">JT</span><div><div class="fw-semibold small">Jack Thompson</div><div class="small text-muted">L's · Stage 2 · Bondi</div></div></div>
                    <div class="row-line"><span class="text-muted">Lessons</span><span class="fw-bold">12</span></div>
                    <div class="row-line"><span class="text-muted">Last lesson</span><span class="fw-bold">Ela T. (sister)</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">3</div>
                <h5 class="fw-bold">Find anyone in two taps</h5>
                <p class="text-muted small">Search by name, suburb, stage, or note content. Filter to "test booked this month" or "haven't seen in 3 weeks". Your brain, indexed.</p>
                <div class="lg-statbox mt-3">
                    <div class="d-flex align-items-center gap-2 small mb-1"><i class="bi bi-search text-muted"></i> Olivia</div>
                    <div class="d-flex align-items-center gap-2 small mb-1"><span class="lg-avatar" style="width:24px;height:24px;font-size:.55rem;">OK</span> Olivia Kim · Marrickville</div>
                    <div class="d-flex align-items-center gap-2 small"><span class="lg-avatar" style="width:24px;height:24px;font-size:.55rem;">OS</span> Olivia Smith · Coogee</div>
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
                ['bi-people','Scale without drowning','Go from 5 to 50 learners and still remember every detail. The system scales with you, your brain doesn\'t have to.'],
                ['bi-heart','Your memory, externalised','Every private note, every quirk, every progression, captured the moment it happens. Search it back in two taps, months later.'],
                ['bi-lock','Private by default','Your learner list is yours. Not a franchise\'s, not a marketplace\'s. Only you see the notes. Only you own the relationship.'],
                ['bi-lightning-charge','Every record, one tap','Bookings, payments, reminders, history, auto-linked to the learner. No re-keying, no cross-referencing, no "let me check".'],
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
                ['Martin · 18 years · Adelaide Hills','"I had 40 learners in a spiral notebook. Now I open a profile on my phone before every lesson and I sound like I\'ve been thinking about them all week. I have."'],
                ['Skye · 6 years · Sutherland Shire','"I had 50 learners across three years and was forgetting names mid-lesson. Notes + history per learner means I sound like I remember everything. Game-changer."'],
                ['Jarrod · 9 years · Brisbane North','"Private notes changed my teaching. \'Nervous on highways, dad is pushy\', I read that before every lesson. Learners feel seen. Retention is up."'],
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

@include('frontend.pages.instructors._ecosystem', ['exclude' => 'learner-management'])
@include('frontend.pages.instructors._business-cta')

@endsection
