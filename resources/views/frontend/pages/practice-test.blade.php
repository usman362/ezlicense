@extends('layouts.frontend')
@section('title', 'Free Practice Learners Tests Online — Secure Licence')

@section('content')

{{-- ─────────── HERO ─────────── --}}
<section class="pt-hero">
    <div class="pt-hero-pattern"></div>
    <div class="container position-relative">
        <nav aria-label="breadcrumb" class="pt-hero-breadcrumb mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Secure Licence</a></li>
                <li class="breadcrumb-item active">Learner Test</li>
            </ol>
        </nav>

        <h1 class="pt-hero-title">FREE Learners Tests Online</h1>
        <p class="pt-hero-sub">Choose your FREE practice learners test</p>

        <div class="row g-3 mt-2">
            @foreach($listing as $slug => $s)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('practice-test.state', $slug) }}" class="pt-hero-btn">
                        <span class="pt-hero-btn-state">{{ $s['name'] }}</span>
                        <small class="pt-hero-btn-test">{{ $s['testName'] }}</small>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── FACEBOOK FOLLOW STRIP ─────────── --}}
<section class="pt-fb-strip">
    <div class="container">
        <div class="pt-fb-row">
            <div class="pt-fb-icon"><i class="bi bi-facebook"></i></div>
            <div class="pt-fb-text">Like our page to receive regular learner test questions &amp; driving lesson discounts.</div>
            <a href="https://www.facebook.com/securelicence" target="_blank" rel="noopener" class="pt-fb-btn">
                <i class="bi bi-hand-thumbs-up-fill"></i> Like
            </a>
        </div>
    </div>
</section>

{{-- ─────────── ALTERNATING STATE SECTIONS ─────────── --}}
@foreach($listing as $slug => $s)
    @php $imgRight = $loop->iteration % 2 === 1; @endphp
    <section class="pt-state-section {{ $loop->iteration % 2 === 0 ? 'pt-state-section-alt' : '' }}">
        <div class="container">
            <div class="row align-items-center g-4">
                @if($imgRight)
                    <div class="col-lg-6 pt-state-text">
                        <h3 class="pt-state-heading">
                            <span class="pt-state-name">{{ $s['name'] }}</span>
                            <small class="pt-state-testname">{{ $s['testName'] }}</small>
                        </h3>
                        <hr class="pt-state-divider">
                        <p class="pt-state-blurb">{{ $s['blurb'] }}</p>
                        <a href="{{ route('practice-test.state', $slug) }}" class="btn btn-warning fw-bold pt-state-btn">
                            Learn More
                        </a>
                    </div>
                    <div class="col-lg-6 text-center d-none d-lg-block">
                        @include('frontend.partials.au-map', ['highlight' => $slug])
                    </div>
                @else
                    <div class="col-lg-6 text-center d-none d-lg-block">
                        @include('frontend.partials.au-map', ['highlight' => $slug])
                    </div>
                    <div class="col-lg-6 pt-state-text">
                        <h3 class="pt-state-heading">
                            <span class="pt-state-name">{{ $s['name'] }}</span>
                            <small class="pt-state-testname">{{ $s['testName'] }}</small>
                        </h3>
                        <hr class="pt-state-divider">
                        <p class="pt-state-blurb">{{ $s['blurb'] }}</p>
                        <a href="{{ route('practice-test.state', $slug) }}" class="btn btn-warning fw-bold pt-state-btn">
                            Learn More
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endforeach

{{-- ─────────── BOTTOM CTA ─────────── --}}
<section class="pt-bottom-cta">
    <div class="container text-center">
        <p class="pt-bottom-text">
            Secure Licence gives you the resources and training you need to confidently get your driver's licence. Practice your learner test, find verified instructors, and book your lessons online — all from one place. If you want the best chance at passing your learner test first time, start with Secure Licence.
        </p>
        <a href="{{ route('find-instructor') }}" class="btn btn-warning fw-bold btn-lg px-4 mt-2">
            Book driving lessons online <i class="bi bi-chevron-right ms-1"></i>
        </a>
    </div>
</section>

@endsection
