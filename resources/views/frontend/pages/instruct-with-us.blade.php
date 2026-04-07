@extends('layouts.frontend')

@section('title', 'Instruct with Secure Licences')

@section('content')
<section class="bg-light py-4">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Instruct with Secure Licences</li>
        </ol></nav>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <h1 class="display-5 fw-bold mb-3">Grow your driving school with Secure Licences</h1>
                <p class="lead text-muted">Join Australia's fastest-growing platform connecting driving instructors with learners. Get more bookings, manage your schedule, and grow your business.</p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="{{ route('register') }}" class="btn btn-warning btn-lg fw-bold px-4">Get Started Free</a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-secondary btn-lg px-4">Contact Us</a>
                </div>
            </div>
            <div class="col-lg-5 text-center">
                <div class="bg-warning bg-opacity-10 rounded-4 p-5">
                    <i class="bi bi-car-front display-1 text-warning"></i>
                    <p class="fw-bold mt-3 mb-0">Join 1000+ instructors Australia-wide</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="h3 fw-bold text-center mb-5">Why instructors choose Secure Licences</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-calendar-check fs-3 text-warning"></i>
                        </div>
                        <h5 class="fw-bold">Easy booking management</h5>
                        <p class="text-muted small">Set your availability, manage bookings, and let learners book directly online. No more back-and-forth calls.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-people fs-3 text-warning"></i>
                        </div>
                        <h5 class="fw-bold">Reach more learners</h5>
                        <p class="text-muted small">Get discovered by thousands of learners searching for driving instructors in your area every day.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi bi-cash-coin fs-3 text-warning"></i>
                        </div>
                        <h5 class="fw-bold">Secure payments</h5>
                        <p class="text-muted small">Get paid reliably. Learners pay upfront through the platform, and you receive payouts on a regular schedule.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h2 class="h3 fw-bold text-center mb-5">How it works</h2>
        <div class="row g-4">
            <div class="col-md-3 text-center">
                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;font-weight:700;font-size:1.2rem;">1</div>
                <h6 class="fw-bold">Sign up</h6>
                <p class="small text-muted">Create your free instructor account and set up your profile.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;font-weight:700;font-size:1.2rem;">2</div>
                <h6 class="fw-bold">Get verified</h6>
                <p class="small text-muted">Upload your documents for verification. We check WWCC, licence, and insurance.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;font-weight:700;font-size:1.2rem;">3</div>
                <h6 class="fw-bold">Set availability</h6>
                <p class="small text-muted">Define your service areas, opening hours, pricing, and calendar preferences.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;font-weight:700;font-size:1.2rem;">4</div>
                <h6 class="fw-bold">Start teaching</h6>
                <p class="small text-muted">Learners find you, book online, and you manage everything from your dashboard.</p>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('register') }}" class="btn btn-warning btn-lg fw-bold px-5">Join Secure Licences Today</a>
        </div>
    </div>
</section>
@endsection
