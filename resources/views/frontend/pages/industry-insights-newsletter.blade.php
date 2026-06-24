@extends('layouts.frontend')
@section('title', 'Industry Insights Newsletter — Secure Licence')
@section('meta_description', 'The monthly playbook driving instructors actually read. One tactic, pricing and demand intel, and case studies from Australia\'s largest driving-instructor marketplace. Free, first Tuesday of every month.')

@push('styles')
<style>
    .ni-eyebrow{display:inline-block;background:#fff3cd;color:#8a6d00;font-weight:700;font-size:.72rem;letter-spacing:.05em;text-transform:uppercase;padding:.35rem .75rem;border-radius:999px;}
    .ni-card{background:#fff;border:1px solid #eef0f2;border-radius:1rem;padding:1.5rem;height:100%;box-shadow:0 4px 18px rgba(20,23,28,.05);}
    .ni-ic{width:42px;height:42px;border-radius:.6rem;background:#fff3cd;color:#8a6d00;display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:1rem;}
    .ni-issue{background:#fff;border:1px solid #eef0f2;border-radius:1.2rem;overflow:hidden;box-shadow:0 22px 60px rgba(20,23,28,.16);}
    .ni-issue-head{background:#ffe27a;display:flex;align-items:center;gap:.8rem;padding:1.1rem 1.4rem;}
    .ni-issue-ic{width:46px;height:46px;border-radius:.7rem;background:#ffd500;color:#1a1d21;display:flex;align-items:center;justify-content:center;font-size:1.25rem;flex-shrink:0;}
    .ni-issue-head .lbl{font-size:.7rem;font-weight:700;letter-spacing:.06em;color:#8a6d00;text-transform:uppercase;}
    .ni-issue-head .date{font-weight:800;font-size:1.1rem;color:#1a1d21;line-height:1.1;}
    .ni-issue-read{margin-left:auto;background:#fff;border-radius:999px;padding:.45rem 1rem;font-weight:700;font-size:.85rem;color:#1a1d21;white-space:nowrap;box-shadow:0 2px 6px rgba(0,0,0,.08);}
    .ni-issue-body{padding:1.5rem;}
    .ni-issue-body .lbl{font-size:.7rem;font-weight:700;letter-spacing:.06em;color:#8b929c;text-transform:uppercase;margin-bottom:.5rem;}
    .ni-faces{display:flex;gap:5px;margin-bottom:1rem;}
    .ni-faces img{flex:1;width:0;height:155px;object-fit:cover;border-radius:.5rem;}
    .ni-stat{ text-align:center;}
    .ni-stat .n{font-weight:700;}
    .ni-stat .l{font-size:.78rem;color:#8b929c;line-height:1.25;}
    .ni-issuecard{border:1px solid #eef0f2;border-radius:.9rem;overflow:hidden;height:100%;background:#fff;transition:box-shadow .12s ease;}
    .ni-issuecard:hover{box-shadow:0 10px 28px rgba(20,23,28,.1);}
    .ni-issuecard .ni-img{height:150px;background-size:cover;background-position:center;position:relative;}
    .ni-issuecard .ni-img .tag,.ni-cs-top .tag{position:absolute;top:.7rem;left:.7rem;background:rgba(255,255,255,.94);color:#1a1d21;font-size:.62rem;font-weight:700;padding:.18rem .5rem;border-radius:.35rem;text-transform:uppercase;letter-spacing:.03em;}
    .ni-cs-top{height:150px;background:#ffd500;position:relative;padding:.9rem;}
    .ni-cs-top .tag{background:rgba(0,0,0,.1);}
    .ni-cs-head{font-weight:800;font-size:.92rem;line-height:1.18;text-transform:uppercase;color:#1a1d21;max-width:62%;margin-top:1.8rem;}
    .ni-cs-photo{width:66px;height:66px;border-radius:50%;object-fit:cover;border:3px solid #fff;position:absolute;right:.9rem;bottom:.9rem;box-shadow:0 4px 12px rgba(0,0,0,.15);}
    .ni-face{width:38px;height:38px;border-radius:50%;border:2px solid #fff;object-fit:cover;}
    .ni-issuecard .body{padding:1rem;}
    .ni-form .form-control,.ni-form .form-select{border-radius:.6rem;}
    .ni-cta{background:#ffd500;border-radius:1.25rem;}
    .ni-check{font-size:.8rem;color:#1a1d21;font-weight:600;}
</style>
@endpush

@section('content')

{{-- ─────────── HERO ─────────── --}}
<section class="py-5">
    <div class="container">
        <nav class="small text-muted mb-3">
            <a href="{{ url('/') }}" class="text-muted text-decoration-none">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('industry-insights') }}" class="text-muted text-decoration-none">Industry Insights</a>
            <span class="mx-1">/</span>
            <span class="text-dark">Newsletter</span>
        </nav>

        @if (session('newsletter_success'))
            <div class="alert alert-success">{{ session('newsletter_success') }}</div>
        @endif

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="ni-eyebrow mb-3">Secure Licence Industry Insights · Free Monthly Newsletter</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">The monthly playbook driving instructors actually read.</h1>
                <p class="text-muted mb-4">
                    On the first Tuesday of every month, 1,800+ Australian driving instructors open one email.
                    It takes 5 minutes to read. It pays back for the rest of the month.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>One tactic you can apply that same day to lift bookings or earnings.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Pricing + demand intel from the 50,000 lessons booked across the network each month.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Regulation changes explained in plain English before they hit you.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Short case studies from instructors already doing what you want to do.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Zero fluff, no spam, unsubscribe in one click.</span></li>
                </ul>

                <form method="post" action="{{ route('industry-insights.newsletter.subscribe') }}" class="ni-form">
                    @csrf
                    <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">
                    <div class="row g-2">
                        <div class="col-sm-8">
                            <input type="email" name="email" class="form-control form-control-lg" placeholder="you@example.com.au" required>
                        </div>
                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-warning btn-lg w-100 fw-semibold">Get free insights</button>
                        </div>
                    </div>
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </form>
                <small class="text-muted d-block mt-2">By subscribing you agree to receive the Secure Licence Industry Insights newsletter. Unsubscribe anytime.</small>
            </div>

            <div class="col-lg-6">
                <div class="ni-issue">
                    <div class="ni-issue-head">
                        <span class="ni-issue-ic"><i class="bi bi-envelope-fill"></i></span>
                        <div>
                            <div class="lbl">Issue #22</div>
                            <div class="date">Tuesday, 7 Apr 2026</div>
                        </div>
                        <span class="ni-issue-read">5 min read</span>
                    </div>
                    <div class="ni-issue-body">
                        <div class="lbl">This issue</div>
                        <h4 class="fw-bolder">Upselling tactics that lifted avg. booking value 23% this month</h4>
                        <p class="text-muted mb-0">Plus: NSW test-centre wait times, Omar's weekend schedule, and 2 pricing plays</p>
                        <hr class="my-4">
                        <div class="lbl">Read by</div>
                        <div class="ni-faces">
                            @foreach([12,32,9,45,5,68] as $n)
                                <img src="https://i.pravatar.cc/200?img={{ $n }}" alt="">
                            @endforeach
                        </div>
                        <p class="text-muted mb-0"><strong class="text-dark">1,847</strong> Australian driving instructors, from Perth to Parramatta, first-year to 30-year veterans.</p>
                        <hr class="my-4">
                        <div class="d-flex align-items-center gap-2 small text-success fw-semibold">
                            <i class="bi bi-check-circle-fill"></i> Delivered first Tuesday of every month, 7am AEST · 22 issues shipped
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── STATS STRIP ─────────── --}}
<section class="py-5" style="background:#eef1f6;">
    <div class="container">
        <div class="row g-4">
            @foreach([
                ['bi-people','1,847+ driving instructors subscribed'],
                ['bi-calendar-check','First Tuesday of every month, 7am AEST'],
                ['bi-clock','5 min read. One tactic per issue.'],
                ['bi-graph-up-arrow','22 issues shipped, zero missed'],
                ['bi-currency-dollar','Free forever, no upsells'],
                ['bi-heart','Backed by 50k+ lessons/mo of real data'],
            ] as [$ic,$label])
                <div class="col-6 col-md-4 col-lg-2 text-center">
                    <i class="bi {{ $ic }}" style="font-size:1.9rem;color:#1a1d21;"></i>
                    <div class="fw-semibold mt-3" style="color:#1a1d21;line-height:1.35;">{{ $label }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── THREE THINGS ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="ni-eyebrow mb-3">What you'll get</span>
            <h2 class="fw-bolder mt-3">Three things. Every month. Forever.</h2>
            <p class="text-muted mx-auto" style="max-width:640px;">No filter. No sponsor posts. Just the signal we pull from running Australia's largest driving-instructor marketplace.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-lightning-charge','One tactic. Done this month.','Every issue gives you one specific, implementable play, on upsell script, a profile tweak, a pricing move, a follow-up template, tested against real booking data across the network.', ['Upselling scripts that lifted booking value','Profile copy swaps that doubled click-throughs','Post-lesson follow-ups that convert to re-books']],
                ['bi-bar-chart-line','Pricing + demand intel you can\'t get elsewhere','We see 50,000+ bookings a month across every postcode in Australia. You get the patterns first, before they show up in your own calendar.', ['Average hourly rate shifts by state, by month','Test-centre wait times & demand hotspots','Regulation changes explained in plain English']],
                ['bi-trophy','Case studies from instructors already winning','Real instructors, real numbers, real playbooks. How Omar hit 35 lessons a week, how Rebecca built consistent income, Nathan\'s part-time model, Jacob\'s week-one bookings.', ['Exact schedules & pricing','What they did in week 1 vs month 5','The one thing they\'d do differently']],
            ] as [$ic,$t,$d,$points])
                <div class="col-md-4">
                    <div class="ni-card">
                        <div class="ni-ic"><i class="bi {{ $ic }}"></i></div>
                        <h5 class="fw-bold">{{ $t }}</h5>
                        <p class="text-muted small">{{ $d }}</p>
                        <ul class="list-unstyled small mb-0">
                            @foreach($points as $p)
                                <li class="d-flex gap-2 mb-1"><i class="bi bi-check2 text-warning"></i><span class="text-muted">{{ $p }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── RECENT ISSUES ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="ni-eyebrow mb-2">Recent issues</span>
                <h2 class="fw-bolder mt-2 mb-1">A taste of what lands in your inbox</h2>
                <p class="text-muted mb-0">Every issue is archived and sent to new subscribers. Here's a sample of the last few months.</p>
            </div>
            <a href="#subscribe" class="btn btn-warning fw-semibold d-none d-md-inline-flex">Get the next one →</a>
        </div>
        <div class="row g-4">
            @php
                $issues = [
                    ['type'=>'photo','tag'=>'Money','meta'=>'Issue #22 · Apr 2026','title'=>'Tax-time tips for instructors','img'=>'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=600&q=80&auto=format&fit=crop'],
                    ['type'=>'photo','tag'=>'Tactics','meta'=>'Issue #21 · Mar 2026','title'=>'Upselling techniques to boost your bookings and earnings','img'=>'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&q=80&auto=format&fit=crop'],
                    ['type'=>'photo','tag'=>'Profile','meta'=>'Issue #20 · Feb 2026','title'=>'Fine-tuning service details for better learner engagement','img'=>'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=600&q=80&auto=format&fit=crop'],
                    ['type'=>'photo','tag'=>'Scheduling','meta'=>'Issue #19 · Jan 2026','title'=>'Maximising your availability: tips to increase your booking rate','img'=>'https://images.unsplash.com/photo-1484981138541-3d074aa97716?w=600&q=80&auto=format&fit=crop'],
                    ['type'=>'photo','tag'=>'Profile','meta'=>'Issue #18 · Dec 2025','title'=>'Crafting an appealing public profile that attracts learners','img'=>'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&q=80&auto=format&fit=crop'],
                    ['type'=>'cs','tag'=>'Case Study','meta'=>'Issue #17 · Nov 2025','head'=>'Omar generates 35 lessons weekly with Secure Licence','photo'=>'https://i.pravatar.cc/120?img=47'],
                    ['type'=>'cs','tag'=>'Case Study','meta'=>'Issue #16 · Oct 2025','head'=>'How Rebecca built consistent, reliable income','photo'=>'https://i.pravatar.cc/120?img=45'],
                    ['type'=>'cs','tag'=>'Case Study','meta'=>'Issue #15 · Sep 2025','head'=>'Nathan\'s part-time model: job satisfaction + extra income','photo'=>'https://i.pravatar.cc/120?img=33'],
                    ['type'=>'cs','tag'=>'Case Study','meta'=>'Issue #14 · Aug 2025','head'=>'Jacob\'s new business received bookings immediately','photo'=>'https://i.pravatar.cc/120?img=68'],
                ];
            @endphp
            @foreach($issues as $it)
                <div class="col-md-6 col-lg-4">
                    <div class="ni-issuecard">
                        @if($it['type'] === 'photo')
                            <div class="ni-img" style="background-image:url('{{ $it['img'] }}');">
                                <span class="tag">{{ $it['tag'] }}</span>
                            </div>
                            <div class="body">
                                <div class="small text-muted mb-1">{{ $it['meta'] }}</div>
                                <h6 class="fw-bold">{{ $it['title'] }}</h6>
                                <a href="#subscribe" class="small fw-semibold text-decoration-none" style="color:#caa300;">Subscribe to read →</a>
                            </div>
                        @else
                            <div class="ni-cs-top">
                                <span class="tag">{{ $it['tag'] }}</span>
                                <div class="ni-cs-head">{{ $it['head'] }}</div>
                                <img src="{{ $it['photo'] }}" alt="" class="ni-cs-photo">
                            </div>
                            <div class="body">
                                <div class="small text-muted mb-1">{{ $it['meta'] }}</div>
                                <a href="#subscribe" class="small fw-semibold text-decoration-none" style="color:#caa300;">Subscribe to read →</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── TESTIMONIAL ─────────── --}}
<section class="py-5">
    <div class="container text-center">
        <div class="text-warning mb-2">★★★★★</div>
        <p class="fs-4 fw-semibold mx-auto" style="max-width:760px;">
            "I was skeptical of another newsletter. Three months in, I'd changed my profile copy using their
            playbook and my booking rate jumped 40%. I forward it to two mates the moment it lands now."
        </p>
        <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
            <span class="ni-ic" style="width:40px;height:40px;border-radius:50%;background:#ffd500;color:#1a1d21;font-size:.8rem;">RW</span>
            <div class="text-start">
                <div class="fw-bold small">Ryan W.</div>
                <div class="text-muted small">Driving Instructor · Brisbane · 4 years on Secure Licence</div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── FINAL CTA ─────────── --}}
<section class="py-5" id="subscribe" style="background:#14171c;">
    <div class="container">
        <div class="ni-cta p-4 p-md-5 mx-auto" style="max-width:780px;">
            <div class="text-center mb-4">
                <h2 class="fw-bolder mb-3">One email. First Tuesday of every month.<br>Read in 5 minutes. Pays back all month.</h2>
                <p class="mb-0 mx-auto" style="max-width:520px;">Join 1,847 Australian driving instructors who read Industry Insights to grow their business. Free forever. Unsubscribe anytime.</p>
            </div>
            <form method="post" action="{{ route('industry-insights.newsletter.subscribe') }}" class="ni-form mx-auto" style="max-width:560px;">
                @csrf
                <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">
                <div class="row g-2 mb-2">
                    <div class="col-6"><input type="text" name="first_name" class="form-control form-control-lg" placeholder="First name"></div>
                    <div class="col-6"><input type="text" name="last_name" class="form-control form-control-lg" placeholder="Last name"></div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <select name="state" class="form-select form-select-lg">
                            <option value="">State</option>
                            @foreach(['NSW','VIC','QLD','WA','SA','TAS','ACT','NT'] as $st)
                                <option value="{{ $st }}">{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6"><input type="email" name="email" class="form-control form-control-lg" placeholder="you@example.com.au" required></div>
                </div>
                <button type="submit" class="btn btn-dark btn-lg w-100 fw-semibold">Subscribe free</button>
            </form>
            <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                <span class="ni-check"><i class="bi bi-check2"></i> No spam, ever</span>
                <span class="ni-check"><i class="bi bi-check2"></i> Unsubscribe 1 click</span>
                <span class="ni-check"><i class="bi bi-check2"></i> Free forever</span>
                <span class="ni-check"><i class="bi bi-check2"></i> Not a marketing list</span>
            </div>
        </div>
    </div>
</section>

@endsection
