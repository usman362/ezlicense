@extends('layouts.frontend')

@section('title', 'Secure Licences Instructor Academy')

@section('content')
<section class="bg-light py-4">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Instructor Academy</li>
        </ol></nav>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <h1 class="display-5 fw-bold mb-3">Secure Licences Instructor Academy</h1>
                <p class="lead text-muted">Everything you need to become a successful driving instructor. From getting your instructor licence to building a thriving business.</p>
                <a href="{{ route('contact') }}" class="btn btn-warning btn-lg fw-bold px-4 mt-3">Enquire Now</a>
            </div>
            <div class="col-lg-5 text-center">
                <div class="bg-dark bg-opacity-10 rounded-4 p-5">
                    <i class="bi bi-mortarboard display-1 text-warning"></i>
                    <p class="fw-bold mt-3 mb-0">Professional training for instructors</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="h3 fw-bold text-center mb-5">What the Academy offers</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <i class="bi bi-journal-check fs-3 text-warning mb-3 d-block"></i>
                        <h5 class="fw-bold">Instructor licence preparation</h5>
                        <p class="text-muted small">Comprehensive training materials and practice tests to help you pass your driving instructor licence exam.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <i class="bi bi-graph-up-arrow fs-3 text-warning mb-3 d-block"></i>
                        <h5 class="fw-bold">Business growth strategies</h5>
                        <p class="text-muted small">Learn marketing, client retention, and business management skills to grow your driving school.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <i class="bi bi-shield-check fs-3 text-warning mb-3 d-block"></i>
                        <h5 class="fw-bold">Safety and compliance</h5>
                        <p class="text-muted small">Stay up to date with the latest road safety regulations, insurance requirements, and compliance standards.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <i class="bi bi-people fs-3 text-warning mb-3 d-block"></i>
                        <h5 class="fw-bold">Teaching techniques</h5>
                        <p class="text-muted small">Master proven teaching methods for nervous learners, test preparation, and building confident drivers.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <i class="bi bi-laptop fs-3 text-warning mb-3 d-block"></i>
                        <h5 class="fw-bold">Platform training</h5>
                        <p class="text-muted small">Learn how to maximise your Secure Licences profile, manage bookings efficiently, and get more positive reviews.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <i class="bi bi-award fs-3 text-warning mb-3 d-block"></i>
                        <h5 class="fw-bold">Certification</h5>
                        <p class="text-muted small">Earn a Secure Licences Academy certification badge displayed on your instructor profile to build trust with learners.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h2 class="h3 fw-bold text-center mb-5">Become a driving instructor in 4 steps</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3 text-center">
                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;font-weight:700;font-size:1.2rem;">1</div>
                <h6 class="fw-bold">Check eligibility</h6>
                <p class="small text-muted">You'll need a full licence (held for 3+ years), Working with Children Check, and pass a police check.</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center">
                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;font-weight:700;font-size:1.2rem;">2</div>
                <h6 class="fw-bold">Complete training</h6>
                <p class="small text-muted">Complete an approved Certificate IV in Transport and Logistics (Driving Instruction).</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center">
                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;font-weight:700;font-size:1.2rem;">3</div>
                <h6 class="fw-bold">Get your licence</h6>
                <p class="small text-muted">Apply for your driving instructor licence with your state's transport authority.</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center">
                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;font-weight:700;font-size:1.2rem;">4</div>
                <h6 class="fw-bold">Join Secure Licences</h6>
                <p class="small text-muted">Create your profile on Secure Licences and start accepting bookings immediately.</p>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('contact') }}" class="btn btn-warning btn-lg fw-bold px-5">Contact the Academy</a>
        </div>
    </div>
</section>
@endsection
