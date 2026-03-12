@extends('layouts.frontend')
@section('title', 'About Us')
@section('content')
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li><li class="breadcrumb-item active">About</li></ol></nav>
    </div>
</div>
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold mb-4" style="color: var(--ez-dark);">About EzLicence</h1>
                <p class="lead mb-4">EzLicence is Australia's leading online platform for booking driving lessons and connecting learner drivers with verified, independent driving instructors.</p>
                <h2 class="h4 fw-bold mt-5 mb-3">Our Mission</h2>
                <p>We believe that learning to drive should be simple, transparent, and accessible. EzLicence takes the hassle out of choosing a driving school by giving learners the tools to find, compare, and book verified driving instructors online — all in one place.</p>
                <h2 class="h4 fw-bold mt-5 mb-3">What We Do</h2>
                <p>Unlike a traditional driving school, EzLicence is a marketplace that connects learners directly with qualified, independent driving instructors across Australia. Our platform offers real-time availability, transparent pricing, genuine learner reviews, and instant online booking — 24 hours a day, 7 days a week.</p>
                <h2 class="h4 fw-bold mt-5 mb-3">Why Choose EzLicence?</h2>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>1000+ verified driving instructors across Australia</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>3700+ suburbs serviced in NSW, VIC, QLD, SA, WA, TAS, ACT</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>100,000+ learners helped get road-ready</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Rated 4.9 from 10,000+ Google reviews</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Book online in under 60 seconds</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Change your instructor anytime, no questions asked</li>
                </ul>
                <h2 class="h4 fw-bold mt-5 mb-3">For Instructors</h2>
                <p>EzLicence also provides driving instructors with a powerful platform to grow their business. With automated scheduling, online payments, and access to thousands of learners, instructors can focus on what they do best — teaching people to drive safely.</p>
                <div class="mt-5 text-center">
                    <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-lg fw-bold">Find an Instructor Near You</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
