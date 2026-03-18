@extends('layouts.frontend')
@section('title', 'Refresher Driving Lessons')
@section('content')
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li><li class="breadcrumb-item active">Refresher Lessons</li></ol></nav>
    </div>
</div>
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <h1 class="display-6 fw-bold mb-3" style="color: var(--ez-dark);">Refresher Driving Lessons</h1>
                <p class="lead text-muted mb-4">Haven't driven in a while? Our refresher lessons are designed for licenced drivers who want to rebuild their confidence behind the wheel.</p>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">Who Are Refresher Lessons For?</h4>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Drivers who haven't been behind the wheel for months or years</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Nervous drivers looking to build confidence</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Drivers returning after a medical condition or injury</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Anyone wanting to improve specific driving skills (highway, parking, etc.)</li>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">What to Expect</h4>
                        <p>Your instructor will assess your current skill level and tailor the lesson to your specific needs. Whether you need help with parallel parking, highway driving, or just general confidence, our instructors will create a personalised plan. Most refresher students need between 2 to 5 hours of lessons to feel fully confident again.</p>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-lg fw-bold">Book a Refresher Lesson</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
