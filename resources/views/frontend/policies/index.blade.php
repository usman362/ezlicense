@extends('layouts.frontend')
@section('title', 'Policies — Secure Licences')
@section('content')
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Policies</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold mb-3" style="color: var(--ez-dark);">Our Policies</h1>
            <p class="lead text-muted mx-auto" style="max-width:720px;">
                Clear rules help everyone — learners, instructors, and our support team — feel safe on the platform.
                These are the policies that keep Secure Licences fair and accountable.
            </p>
        </div>

        {{-- Policy groups --}}
        <div class="row g-4">
            {{-- For Everyone --}}
            <div class="col-12">
                <h4 class="fw-bold text-muted text-uppercase small mb-3"><i class="bi bi-people me-2"></i>For Everyone</h4>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('policies.complaint-handling') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body policy-card">
                    <div class="card-body">
                        <div class="mb-3 text-danger"><i class="bi bi-exclamation-octagon fs-2"></i></div>
                        <h5 class="fw-bold">Complaint Handling</h5>
                        <p class="small text-muted mb-2">How we investigate complaints fairly, record evidence, and protect both parties from false accusations.</p>
                        <span class="small fw-semibold text-primary">Read policy <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('policies.refund-cancellation') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body policy-card">
                    <div class="card-body">
                        <div class="mb-3 text-info"><i class="bi bi-arrow-counterclockwise fs-2"></i></div>
                        <h5 class="fw-bold">Refund & Cancellation</h5>
                        <p class="small text-muted mb-2">When refunds apply, cancellation notice periods, no-show rules and dispute-adjusted refunds.</p>
                        <span class="small fw-semibold text-primary">Read policy <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('policies.safety') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body policy-card">
                    <div class="card-body">
                        <div class="mb-3 text-success"><i class="bi bi-shield-shaded fs-2"></i></div>
                        <h5 class="fw-bold">Safety Policy</h5>
                        <p class="small text-muted mb-2">Vehicle standards, WWCC checks, professional boundaries and incident reporting.</p>
                        <span class="small fw-semibold text-primary">Read policy <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('policies.dispute-resolution') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body policy-card">
                    <div class="card-body">
                        <div class="mb-3 text-warning"><i class="bi bi-people fs-2"></i></div>
                        <h5 class="fw-bold">Dispute Resolution</h5>
                        <p class="small text-muted mb-2">Step-by-step mediation process for disagreements between learners and instructors.</p>
                        <span class="small fw-semibold text-primary">Read policy <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            </div>

            {{-- For Instructors --}}
            <div class="col-12 mt-4">
                <h4 class="fw-bold text-muted text-uppercase small mb-3"><i class="bi bi-person-badge me-2"></i>For Instructors</h4>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('policies.instructor-conduct') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body policy-card">
                    <div class="card-body">
                        <div class="mb-3 text-primary"><i class="bi bi-person-badge fs-2"></i></div>
                        <h5 class="fw-bold">Instructor Code of Conduct</h5>
                        <p class="small text-muted mb-2">Professional standards, interaction boundaries, dress code and conflict handling.</p>
                        <span class="small fw-semibold text-primary">Read policy <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            </div>

            {{-- For Learners --}}
            <div class="col-12 mt-4">
                <h4 class="fw-bold text-muted text-uppercase small mb-3"><i class="bi bi-person me-2"></i>For Learners</h4>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('policies.learner-conduct') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body policy-card">
                    <div class="card-body">
                        <div class="mb-3 text-primary"><i class="bi bi-person fs-2"></i></div>
                        <h5 class="fw-bold">Learner Code of Conduct</h5>
                        <p class="small text-muted mb-2">Respectful behaviour, attendance, honest feedback and consequences of false reports.</p>
                        <span class="small fw-semibold text-primary">Read policy <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            </div>

            {{-- Legal --}}
            <div class="col-12 mt-4">
                <h4 class="fw-bold text-muted text-uppercase small mb-3"><i class="bi bi-file-earmark-text me-2"></i>Legal</h4>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('terms') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body policy-card">
                    <div class="card-body">
                        <div class="mb-3 text-secondary"><i class="bi bi-file-earmark-text fs-2"></i></div>
                        <h5 class="fw-bold">Terms & Conditions</h5>
                        <p class="small text-muted mb-2">The binding agreement between you and Secure Licences Pty Ltd.</p>
                        <span class="small fw-semibold text-primary">Read terms <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('privacy') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body policy-card">
                    <div class="card-body">
                        <div class="mb-3 text-secondary"><i class="bi bi-lock fs-2"></i></div>
                        <h5 class="fw-bold">Privacy Policy</h5>
                        <p class="small text-muted mb-2">How we collect, store and protect your personal information.</p>
                        <span class="small fw-semibold text-primary">Read policy <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            </div>
        </div>

        <div class="text-center mt-5 pt-4 border-top">
            <p class="text-muted mb-2">Can't find what you're looking for?</p>
            <a href="{{ route('contact') }}" class="btn btn-outline-primary"><i class="bi bi-envelope me-2"></i>Contact Support</a>
        </div>
    </div>
</section>

<style>
.policy-card { transition: transform 0.15s ease, box-shadow 0.15s ease; }
.policy-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important; }
</style>
@endsection
