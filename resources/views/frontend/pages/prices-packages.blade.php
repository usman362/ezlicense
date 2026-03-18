@extends('layouts.frontend')
@section('title', 'Prices & Packages')
@section('content')
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li><li class="breadcrumb-item active">Prices & Packages</li></ol></nav>
    </div>
</div>
<section class="py-5">
    <div class="container">
        <h1 class="display-6 fw-bold mb-3 text-center" style="color: var(--ez-dark);">Prices & Packages</h1>
        <p class="lead text-muted text-center mb-5">Driving lesson prices are set by each individual instructor. Enter your suburb to see available instructors, their pricing, ratings, and vehicle details.</p>

        <div class="row g-4 justify-content-center mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center">
                    <div class="card-body p-4">
                        <div class="rounded-circle bg-warning bg-opacity-25 d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;"><i class="bi bi-clock text-warning fs-3"></i></div>
                        <h5 class="fw-bold">1 Hour Lesson</h5>
                        <p class="text-muted">A standard driving lesson. Great for regular weekly practice. Prices vary by instructor and location.</p>
                        <p class="h4 fw-bold text-warning">From $45/hr</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center position-relative">
                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">Most Popular</span>
                    <div class="card-body p-4">
                        <div class="rounded-circle bg-warning bg-opacity-25 d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;"><i class="bi bi-clock-history text-warning fs-3"></i></div>
                        <h5 class="fw-bold">2 Hour Lesson</h5>
                        <p class="text-muted">Double lesson for faster progress. Ideal for building confidence and covering more skills per session.</p>
                        <p class="h4 fw-bold text-warning">From $85/2hrs</p>
                        <p class="small text-success"><i class="bi bi-tag-fill me-1"></i>Save with longer lessons</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center">
                    <div class="card-body p-4">
                        <div class="rounded-circle bg-warning bg-opacity-25 d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;"><i class="bi bi-award text-warning fs-3"></i></div>
                        <h5 class="fw-bold">Test Package</h5>
                        <p class="text-muted">Includes 45-min warm-up, use of instructor's car for the test, and drop-off afterwards.</p>
                        <p class="h4 fw-bold text-warning">From $120</p>
                        <p class="small text-muted">Not available in ACT, SA, TAS</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-3"><i class="bi bi-info-circle text-warning me-2"></i>Important Information</h4>
                <p class="mb-2">Prices displayed are indicative and set individually by each instructor. Actual prices may vary based on your suburb, transmission type (auto/manual), and the instructor you choose.</p>
                <p class="mb-0">The best way to see exact pricing is to <a href="{{ route('find-instructor') }}" class="fw-bold">enter your suburb</a> and browse available instructors in your area.</p>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-lg fw-bold">Find Instructors & Compare Prices</a>
        </div>
    </div>
</section>
@endsection
