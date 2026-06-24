@extends('layouts.frontend')
@section('title', 'Lesson Catalog — Sell Packages & Test-Day Bundles — Secure Licence')
@section('meta_description', 'Not just 1-hour bookings. Sell standard lessons, country drives, test-day packages and bulk bundles — each with its own price, duration and rules.')

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
            <span class="text-dark">Lesson Catalog</span>
        </nav>

        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="lg-eyebrow mb-3">Lesson Catalog</span>
                <h1 class="display-5 fw-bolder mt-3 mb-3">Not just 1-hour bookings.</h1>
                <p class="text-muted mb-4">
                    Sell 60-minute lessons to beginners. 3-hour country drives to intermediates. A full test-day
                    package to learners taking their P's tomorrow. Each product has its own price, its own duration,
                    its own rules. You decide the menu.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Standard durations: 1hr, 1.5hr, 2hr, 3hr, 4hr, 5hr.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Test packages: pre-test warm-up + vehicle + pickup/drop-off.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-warning"></i><span>Per-product pricing · bulk-pack discounts · any currency you set.</span></li>
                </ul>
                <a href="{{ route('support.request.show', ['topic' => 'instructor']) }}" class="btn btn-warning btn-lg fw-semibold">Build your lesson catalog</a>
            </div>

            <div class="col-lg-6">
                <div class="lg-panel">
                    @foreach([
                        ['1h','Standard lesson','Most popular · beginners + refresher','$85', false],
                        ['1.5h','Extended lesson','Highway intro + hazard work','$128', false],
                        ['3h','Country drive','Open road + country confidence','$255', false],
                        ['','Test-Day Package','Warm-up + vehicle + pickup/drop-off','$295', true],
                    ] as [$dur,$name,$sub,$price,$hi])
                        <div class="lg-row" @if($hi) style="background:#ffd500;border-radius:.6rem;padding:.7rem .85rem;border-bottom:0;margin-top:.4rem;" @endif>
                            @if($dur)
                                <span class="lg-avatar" style="width:42px;height:42px;font-size:.75rem;">{{ $dur }}</span>
                            @else
                                <span class="lg-avatar" style="width:42px;height:42px;background:#1a1d21;color:#ffd500;"><i class="bi bi-patch-check-fill"></i></span>
                            @endif
                            <div><div class="fw-semibold small">{{ $name }}</div><div class="small {{ $hi ? '' : 'text-muted' }}">{{ $sub }}</div></div>
                            <span class="fw-bold ms-auto">{{ $price }}</span>
                        </div>
                    @endforeach
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
            <h2 class="fw-bolder mt-3">One-size-fits-all pricing is leaving money on the road.</h2>
            <p class="text-muted mx-auto" style="max-width:640px;">Your learners' needs vary wildly. Your price shouldn't be one number.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['No upsell lever','"Come back for another 1hr" is the only pitch. You can\'t offer the 3-hour country drive because your calendar tool only knows "1 hour slots".'],
                ['Test-day chaos','Learner books a 1hr pre-test warm-up + needs your car + pickup + drop-off. You stitch it together manually. They pay you by bank transfer 3 days late.'],
                ['No bulk deals','Parents want 10 lessons for their kid. You want to offer 10% off a bundle. You end up giving it verbally, and nobody tracks it.'],
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
            <h2 class="fw-bolder mt-3">Build a proper menu. Charge proper prices.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="lg-step-num">1</div>
                <h5 class="fw-bold">Turn on the products you sell</h5>
                <p class="text-muted small">Start with standard 1hr. Add 1.5hr once you're ready. Add 3hr, 4hr, 5hr for longer drives. Enable the test-day package with a single click.</p>
                <div class="lg-statbox mt-3">
                    <div class="row-line"><span class="text-muted">1hr lesson</span><span class="fw-bold">$85</span></div>
                    <div class="row-line"><span class="text-muted">1.5hr lesson</span><span class="fw-bold">$128</span></div>
                    <div class="row-line"><span class="text-muted">2hr lesson</span><span class="fw-bold">$170</span></div>
                    <div class="row-line"><span class="text-muted">3hr country drive</span><span class="fw-bold">$255</span></div>
                    <div class="row-line"><span class="text-muted">4hr intensive</span><span class="fw-bold">$340</span></div>
                    <div class="row-line"><span class="text-muted">5hr full-day</span><span class="fw-bold">$425</span></div>
                    <div class="row-line"><span class="text-muted">Test-day package</span><span class="fw-bold">$295</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">2</div>
                <h5 class="fw-bold">Set prices per product</h5>
                <p class="text-muted small">Different price for each duration. Different price for test packages. Different price for weekends vs weekdays if you want. Total control.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">Test-day package</div>
                    <div class="row-line"><span class="text-muted">45min pre-test warm-up</span><span class="fw-bold text-success">Included</span></div>
                    <div class="row-line"><span class="text-muted">Vehicle use at test</span><span class="fw-bold text-success">Included</span></div>
                    <div class="row-line"><span class="text-muted">Pickup + drop-off</span><span class="fw-bold text-success">Included</span></div>
                    <div class="row-line border-top pt-1"><span class="fw-semibold">Package price</span><span class="fw-bold">$295</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lg-step-num">3</div>
                <h5 class="fw-bold">Offer bundle deals</h5>
                <p class="text-muted small">Sell 10-packs with a discount. Sell 5-lesson starter bundles for new learners. Parents love it. Cash flow loves it.</p>
                <div class="lg-statbox mt-3">
                    <div class="small fw-semibold text-uppercase text-muted mb-1" style="letter-spacing:.05em;">Bundles</div>
                    <div class="row-line"><span class="text-muted">5-lesson starter pack</span><span class="fw-bold">$405 <span class="text-success small">save 5%</span></span></div>
                    <div class="row-line"><span class="text-muted">10-lesson intensive</span><span class="fw-bold">$765 <span class="text-success small">save 10%</span></span></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BENEFITS (yellow) ─────────── --}}
<section class="py-5 lg-yellow">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">A proper catalog changes your business.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-cash','+$400/mo revenue','Instructors who enable test packages + 1.5hr lessons average an extra $400–$700/month in first 90 days.'],
                ['bi-bag','Upsell path','Start them on 1hr. Move them to 1.5hr. Finish with a test pack. Every stage has a price.'],
                ['bi-graph-up-arrow','Longer lessons = margin','A 3hr country drive yields 2.5× the revenue of three 1hr lessons and one pickup instead of three.'],
                ['bi-heart','Parent-friendly','Bundles + test packs look like serious products. Parents pay up front. Referrals follow.'],
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
            <h2 class="fw-bolder">Instructors who priced their way up.</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Tony · 11 years · Sunshine Coast','"Added 3-hour country drives last year. They\'re my highest-margin product and I do 2–3 a week. Didn\'t know how much I was leaving on the table."'],
                ['Layla · 5 years · Perth','"Test packages are my favourite product. Learners book weeks in advance. Parents love the all-in price. It\'s the best $295 I collect."'],
                ['Sanjay · New instructor · Hobart','"Started with just 1hr. Added 1.5hr after a month. Then test packs. Each expansion added another $150–200/wk. Insane."'],
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

@include('frontend.pages.instructors._ecosystem', ['exclude' => 'lesson-catalog'])
@include('frontend.pages.instructors._business-cta')

@endsection
