@extends('layouts.frontend')

@section('title', 'Free Practice Learners Test Online')

@section('content')
<section class="bg-light py-4">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Learner Tests Online</li>
        </ol></nav>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h1 class="display-5 fw-bold mb-3">FREE Practice Learners Test</h1>
        <p class="lead text-muted mb-5">Prepare for your learner's permit knowledge test with our free online practice tests. Choose your state below to get started.</p>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('practice-test.state', 'nsw') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-card">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px;height:56px;">
                                <span class="fw-bold text-primary">NSW</span>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">NSW Driver Knowledge Test</h5>
                                <p class="small text-muted mb-0">Practice for the NSW DKT with real-style questions</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('practice-test.state', 'vic') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-card">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="bg-info bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px;height:56px;">
                                <span class="fw-bold text-info">VIC</span>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">VIC Learner Permit Knowledge Test</h5>
                                <p class="small text-muted mb-0">Practice questions for the VIC learner permit test</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('practice-test.state', 'qld') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-card">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="bg-danger bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px;height:56px;">
                                <span class="fw-bold text-danger">QLD</span>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">QLD Road Rules Test</h5>
                                <p class="small text-muted mb-0">Queensland road rules practice questions</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('practice-test.state', 'wa') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-card">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px;height:56px;">
                                <span class="fw-bold text-success">WA</span>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">WA Road Rules Theory Test</h5>
                                <p class="small text-muted mb-0">Western Australia driving theory practice</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('practice-test.state', 'sa') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-card">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="bg-warning bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px;height:56px;">
                                <span class="fw-bold text-warning">SA</span>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">SA Learner Theory Test</h5>
                                <p class="small text-muted mb-0">South Australia learner theory practice</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('practice-test.state', 'tas') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-card">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="bg-secondary bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px;height:56px;">
                                <span class="fw-bold text-secondary">TAS</span>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">TAS Driver Knowledge Test</h5>
                                <p class="small text-muted mb-0">Tasmania driver knowledge practice questions</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('practice-test.state', 'act') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-card">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="bg-dark bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px;height:56px;">
                                <span class="fw-bold">ACT</span>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">ACT Road Rules Knowledge Test</h5>
                                <p class="small text-muted mb-0">ACT road rules practice questions</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="h3 fw-bold text-center mb-5">Tips for passing your learner's test</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <i class="bi bi-check-circle-fill text-success fs-4 flex-shrink-0"></i>
                    <div>
                        <h6 class="fw-bold">Practice regularly</h6>
                        <p class="text-muted small">Take the practice test multiple times until you consistently score above 90%. Repetition builds confidence.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <i class="bi bi-check-circle-fill text-success fs-4 flex-shrink-0"></i>
                    <div>
                        <h6 class="fw-bold">Read the handbook</h6>
                        <p class="text-muted small">Your state's driver handbook contains everything you need to know. Read it cover to cover at least once.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <i class="bi bi-check-circle-fill text-success fs-4 flex-shrink-0"></i>
                    <div>
                        <h6 class="fw-bold">Focus on road signs</h6>
                        <p class="text-muted small">Many test questions are about road signs and their meanings. Make sure you can recognise all of them.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-3">
                    <i class="bi bi-check-circle-fill text-success fs-4 flex-shrink-0"></i>
                    <div>
                        <h6 class="fw-bold">Understand give-way rules</h6>
                        <p class="text-muted small">Intersection and give-way rules are heavily tested. Study roundabouts, T-intersections, and unmarked intersections.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

<style>
.hover-card { transition: transform 0.15s, box-shadow 0.15s; }
.hover-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,0.1) !important; }
</style>
