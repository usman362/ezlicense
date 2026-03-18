@extends('layouts.frontend')
@section('title', 'International Licence Conversions')
@section('content')
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li><li class="breadcrumb-item active">International Licence Conversions</li></ol></nav>
    </div>
</div>
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <h1 class="display-6 fw-bold mb-3" style="color: var(--ez-dark);">International Licence Conversions</h1>
                <p class="lead text-muted mb-4">Moving to Australia and need to convert your overseas licence? Our experienced instructors will help you gain confidence on Australian roads and prepare for your driving test.</p>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">Why Take Conversion Lessons?</h4>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Learn Australian road rules and driving conditions</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Get familiar with driving on the left side of the road</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Understand roundabouts, give-way rules, and hook turns</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Practice the driving test route near your local test centre</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Use the instructor's vehicle for your driving test</li>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">How Many Lessons Do I Need?</h4>
                        <p>The number of lessons varies depending on your experience. As a general guide, we recommend 3 to 5 hours for experienced international drivers who just need to adapt to Australian conditions. Your instructor will assess your skill level during the first lesson and recommend the best plan for you.</p>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-lg fw-bold">Find an Instructor Near You</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
