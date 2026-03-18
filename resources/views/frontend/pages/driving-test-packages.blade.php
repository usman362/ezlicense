@extends('layouts.frontend')
@section('title', 'Driving Test Packages')
@section('content')
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li><li class="breadcrumb-item active">Driving Test Packages</li></ol></nav>
    </div>
</div>
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <h1 class="display-6 fw-bold mb-3" style="color: var(--ez-dark);">Driving Test Packages</h1>
                <p class="lead text-muted mb-4">Get everything you need to ace your driving test in one convenient package. Our test packages include a warm-up lesson, use of an instructor's vehicle, and drop-off after the test.</p>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-check-circle-fill text-warning me-2"></i>What's Included</h4>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Pick-up from your chosen location 1 hour prior to test start time</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>45-minute pre-test warm-up driving lesson</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Use of your instructor's dual-controlled vehicle for the driving test</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Drop-off after the test result is received</li>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">How It Works</h4>
                        <div class="row g-3">
                            <div class="col-md-4 text-center">
                                <div class="rounded-circle bg-warning text-dark d-inline-flex align-items-center justify-content-center mb-2 fw-bold" style="width:48px;height:48px;">1</div>
                                <h6 class="fw-bold">Find an Instructor</h6>
                                <p class="small text-muted">Enter your suburb and select an instructor who offers test packages in your area.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="rounded-circle bg-warning text-dark d-inline-flex align-items-center justify-content-center mb-2 fw-bold" style="width:48px;height:48px;">2</div>
                                <h6 class="fw-bold">Book Online</h6>
                                <p class="small text-muted">Choose your test date and time, then book and pay securely online.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="rounded-circle bg-warning text-dark d-inline-flex align-items-center justify-content-center mb-2 fw-bold" style="width:48px;height:48px;">3</div>
                                <h6 class="fw-bold">Pass Your Test</h6>
                                <p class="small text-muted">Your instructor picks you up, warms you up, and you sit the test confidently.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i><strong>Note:</strong> Test packages are available in most states but not currently offered in ACT, SA and TAS. Pricing varies by instructor.
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-lg fw-bold">Find Instructors with Test Packages</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
