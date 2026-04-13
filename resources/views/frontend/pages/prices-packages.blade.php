@extends('layouts.frontend')
@section('title', 'Prices & Packages — Transparent Driving Lesson Pricing')
@section('content')

{{-- Breadcrumb --}}
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Prices & Packages</li>
            </ol>
        </nav>
    </div>
</div>

{{-- ============================================================
   SECTION 1 — Hero
   ============================================================ --}}
<section class="section-lg position-relative overflow-hidden" style="background: linear-gradient(135deg, var(--sl-gray-900) 0%, var(--sl-primary-900) 50%, var(--sl-gray-900) 100%);">
    {{-- Decorative dots --}}
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px); background-size: 24px 24px; pointer-events: none;"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-5 fw-bold mb-3 animate-fade-in-up" style="color: #fff; letter-spacing: -0.03em;">
                    Transparent Pricing,<br>
                    <span style="color: var(--sl-accent-500);">No Hidden Fees</span>
                </h1>
                <p class="lead mb-4 animate-fade-in-up animate-delay-100" style="color: var(--sl-gray-300); max-width: 600px; margin: 0 auto;">
                    Compare instructor rates in your area. Prices are set individually by each verified instructor.
                </p>

                {{-- Suburb search --}}
                <div class="animate-fade-in-up animate-delay-200" style="max-width: 520px; margin: 0 auto;">
                    <form action="{{ route('find-instructor') }}" method="GET" class="d-flex gap-2">
                        <input type="text" name="q" class="form-control form-control-lg" placeholder="Enter your suburb..." style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: #fff;">
                        <button type="submit" class="btn btn-warning btn-lg fw-bold px-4 flex-shrink-0">
                            <i class="bi bi-search me-1"></i> Find Instructors
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
   SECTION 2 — How Pricing Works
   ============================================================ --}}
<section class="section" style="background: var(--sl-gray-50);">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h3 fw-bold">How Pricing Works</h2>
            <p class="text-muted" style="max-width: 560px; margin: 0 auto;">
                Our marketplace connects you directly with instructors. Here is how pricing is structured.
            </p>
        </div>

        <div class="row g-4">
            {{-- Card 1 --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="icon-bubble icon-bubble-accent mx-auto mb-3">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Instructor-Set Rates</h5>
                        <p class="text-muted small mb-0">
                            Each instructor sets their own hourly rate based on experience, vehicle type, and location. Typically $45 &ndash; $75/hr.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="icon-bubble mx-auto mb-3">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Bundle & Save</h5>
                        <p class="text-muted small mb-0">
                            Book multiple lessons and many instructors offer discounted packages. Save 5 &ndash; 10% on 5+ hour bundles.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="icon-bubble icon-bubble-success mx-auto mb-3">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h5 class="fw-bold mb-2">No Platform Fees for Learners</h5>
                        <p class="text-muted small mb-0">
                            You pay the instructor's rate directly. No hidden service charges or booking fees.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
   SECTION 3 — Popular Packages
   ============================================================ --}}
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h3 fw-bold">Popular Packages</h2>
            <p class="text-muted" style="max-width: 520px; margin: 0 auto;">
                Most instructors offer these common options. Actual prices depend on your area and instructor.
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            {{-- Single Lesson --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 card-hover">
                    <div class="card-body p-4 p-lg-5 text-center d-flex flex-column">
                        <div class="icon-bubble icon-bubble-accent mx-auto mb-3">
                            <i class="bi bi-clock"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Single Lesson</h5>
                        <p class="display-6 fw-bold mb-2" style="color: var(--sl-primary-500);">From $45<small class="fs-6 fw-semibold text-muted">/hr</small></p>
                        <p class="text-muted small flex-grow-1">Perfect for a refresher or to try a new instructor.</p>
                        <a href="{{ route('find-instructor') }}" class="btn btn-outline-primary btn-sm mt-auto">
                            Find Instructors <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- 5-Hour Bundle --}}
            <div class="col-md-4">
                <div class="card h-100 card-hover position-relative" style="border: 2px solid var(--sl-primary-500); box-shadow: var(--sl-shadow-lg);">
                    <span class="badge position-absolute top-0 start-50 translate-middle fw-bold px-3 py-2" style="background: var(--sl-primary-500); color: #fff; font-size: 0.7rem; letter-spacing: 0.06em;">
                        MOST POPULAR
                    </span>
                    <div class="card-body p-4 p-lg-5 text-center d-flex flex-column">
                        <div class="icon-bubble mx-auto mb-3" style="background: var(--sl-primary-50); color: var(--sl-primary-600);">
                            <i class="bi bi-stars"></i>
                        </div>
                        <h5 class="fw-bold mb-1">5-Hour Bundle</h5>
                        <p class="display-6 fw-bold mb-2" style="color: var(--sl-primary-500);">From $210</p>
                        <p class="text-muted small flex-grow-1">Most popular for learners building confidence.</p>
                        <a href="{{ route('find-instructor') }}" class="btn btn-primary btn-sm mt-auto">
                            Find Instructors <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Driving Test Package --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 card-hover">
                    <div class="card-body p-4 p-lg-5 text-center d-flex flex-column">
                        <div class="icon-bubble icon-bubble-teal mx-auto mb-3">
                            <i class="bi bi-patch-check"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Driving Test Package</h5>
                        <p class="display-6 fw-bold mb-2" style="color: var(--sl-primary-500);">From $120</p>
                        <p class="text-muted small flex-grow-1">Pre-test lesson + test day pickup & vehicle hire.</p>
                        <a href="{{ route('find-instructor') }}" class="btn btn-outline-primary btn-sm mt-auto">
                            Find Instructors <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
   SECTION 4 — How Many Lessons Do You Need?
   ============================================================ --}}
<section class="section" style="background: var(--sl-gray-50);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="h3 fw-bold">How Many Lessons Do You Need?</h2>
                    <p class="text-muted" style="max-width: 520px; margin: 0 auto;">
                        Every learner is different. Here is a general guide to help you plan.
                    </p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Driver Type</th>
                                        <th class="text-end pe-4">Recommended Hours</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="icon-bubble icon-bubble-sm icon-bubble-accent"><i class="bi bi-person-plus"></i></span>
                                                <div>
                                                    <span class="fw-semibold">New learner</span>
                                                    <span class="d-block text-muted small">No prior experience</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="fw-bold" style="color: var(--sl-primary-600);">10 &ndash; 15 hours</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="icon-bubble icon-bubble-sm"><i class="bi bi-globe2"></i></span>
                                                <div>
                                                    <span class="fw-semibold">Some experience</span>
                                                    <span class="d-block text-muted small">Overseas licence holder</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="fw-bold" style="color: var(--sl-primary-600);">3 &ndash; 6 hours</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="icon-bubble icon-bubble-sm icon-bubble-teal"><i class="bi bi-arrow-clockwise"></i></span>
                                                <div>
                                                    <span class="fw-semibold">Refresher</span>
                                                    <span class="d-block text-muted small">Lapsed licence</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="fw-bold" style="color: var(--sl-primary-600);">4 &ndash; 7 hours</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="icon-bubble icon-bubble-sm icon-bubble-success"><i class="bi bi-clipboard-check"></i></span>
                                                <div>
                                                    <span class="fw-semibold">Test preparation only</span>
                                                    <span class="d-block text-muted small">Confident driver</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="fw-bold" style="color: var(--sl-primary-600);">2 &ndash; 3 hours</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <p class="text-muted small text-center mt-3">
                    <i class="bi bi-info-circle me-1"></i>
                    These are general estimates. Your instructor will recommend a personalised plan based on your skill level.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
   SECTION 5 — FAQ Accordion
   ============================================================ --}}
<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="h3 fw-bold">Frequently Asked Questions</h2>
                    <p class="text-muted">Everything you need to know about pricing and lessons.</p>
                </div>

                <div class="accordion" id="pricingFaq">
                    {{-- FAQ 1 --}}
                    <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: var(--sl-radius-lg) !important; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false" aria-controls="faq1">
                                Why do prices vary between instructors?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#pricingFaq">
                            <div class="accordion-body text-muted">
                                Each instructor is an independent professional who sets their own rates. Pricing can vary based on their years of experience, the type of vehicle they use (automatic or manual), their location, and the demand in your area. This ensures you can find an option that fits your budget and preferences.
                            </div>
                        </div>
                    </div>

                    {{-- FAQ 2 --}}
                    <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: var(--sl-radius-lg) !important; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                                What's included in a driving test package?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#pricingFaq">
                            <div class="accordion-body text-muted">
                                A typical driving test package includes a 45-minute warm-up lesson before your test, use of the instructor's dual-controlled vehicle for the test itself, and pickup and drop-off on the day. Some instructors may also include a pre-test assessment lesson. Details vary by instructor, so check their profile for specifics.
                            </div>
                        </div>
                    </div>

                    {{-- FAQ 3 --}}
                    <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: var(--sl-radius-lg) !important; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                                How many lessons do I need before my test?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#pricingFaq">
                            <div class="accordion-body text-muted">
                                This depends on your experience level. Complete beginners typically need 10 to 15 professional lessons alongside supervised practice. If you already have some driving experience or hold an overseas licence, 3 to 6 lessons may be enough. Your instructor will assess your skills and give you an honest recommendation.
                            </div>
                        </div>
                    </div>

                    {{-- FAQ 4 --}}
                    <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: var(--sl-radius-lg) !important; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                                Are there extra charges or booking fees?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#pricingFaq">
                            <div class="accordion-body text-muted">
                                No. The price you see on an instructor's profile is what you pay. Secure Licences does not charge learners any platform fees, booking fees, or service surcharges. Our platform is free for learners to use.
                            </div>
                        </div>
                    </div>

                    {{-- FAQ 5 --}}
                    <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: var(--sl-radius-lg) !important; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">
                                Can I switch instructors if I'm not happy?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#pricingFaq">
                            <div class="accordion-body text-muted">
                                Absolutely. You are not locked in with any instructor. If you feel a different teaching style would suit you better, you can browse other instructors in your area and book with someone new at any time. We encourage you to find the right fit.
                            </div>
                        </div>
                    </div>

                    {{-- FAQ 6 --}}
                    <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: var(--sl-radius-lg) !important; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq6" aria-expanded="false" aria-controls="faq6">
                                Do you offer gift vouchers?
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#pricingFaq">
                            <div class="accordion-body text-muted">
                                Yes! Driving lesson gift vouchers make a great present for birthdays, graduations, or special occasions. You can purchase them through our <a href="{{ route('gift-vouchers') }}" class="fw-semibold">gift vouchers page</a> and the recipient can use them with any instructor on the platform.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
   SECTION 6 — CTA
   ============================================================ --}}
<section class="section-lg position-relative overflow-hidden" style="background: linear-gradient(135deg, var(--sl-primary-700) 0%, var(--sl-primary-500) 100%);">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: radial-gradient(rgba(255,255,255,0.08) 1px, transparent 1px); background-size: 20px 20px; pointer-events: none;"></div>

    <div class="container position-relative text-center" style="z-index: 2;">
        <h2 class="display-6 fw-bold mb-3" style="color: #fff;">Ready to Start?</h2>
        <p class="lead mb-4" style="color: rgba(255,255,255,0.85); max-width: 520px; margin: 0 auto;">
            Find verified instructors in your suburb and compare prices.
        </p>
        <a href="{{ route('find-instructor') }}" class="btn btn-warning btn-lg fw-bold px-5 py-3" style="font-size: 1.1rem;">
            <i class="bi bi-search me-2"></i>Find an Instructor
        </a>
    </div>
</section>

@push('scripts')
<style>
    /* Accordion custom styling for pricing page */
    #pricingFaq .accordion-button {
        background: #fff;
        color: var(--sl-gray-800);
        padding: 1.125rem 1.5rem;
        font-size: var(--sl-text-base);
        border: none;
    }
    #pricingFaq .accordion-button:not(.collapsed) {
        background: #fff;
        color: var(--sl-primary-600);
        box-shadow: none;
    }
    #pricingFaq .accordion-button:focus {
        box-shadow: none;
        border-color: transparent;
    }
    #pricingFaq .accordion-button::after {
        background-size: 1rem;
        width: 1rem;
        height: 1rem;
    }
    #pricingFaq .accordion-body {
        padding: 0 1.5rem 1.25rem 1.5rem;
        font-size: var(--sl-text-sm);
        line-height: 1.7;
    }

    /* Hero search placeholder color */
    .section-lg .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5) !important;
    }

    /* Table icon bubbles on small screens */
    @media (max-width: 575.98px) {
        .icon-bubble-sm {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }
    }
</style>
@endpush

@endsection
