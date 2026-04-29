@extends('layouts.learner')

@section('title', 'Support')
@section('heading', 'Support')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="{{ route('learner.dashboard') }}"><i class="bi bi-house"></i> Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Support</li>
    </ol>
</nav>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Hero --}}
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #fff7ed, #ffedd5);">
    <div class="card-body p-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <h3 class="fw-bolder mb-2"><i class="bi bi-headset text-warning me-2"></i>How can we help?</h3>
                <p class="text-muted mb-0">Find answers to common questions, get in touch with our team, or browse our policies.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="#contact-form" class="btn btn-warning fw-bold"><i class="bi bi-chat-dots me-1"></i>Contact Us</a>
            </div>
        </div>
    </div>
</div>

{{-- Quick contact options --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;background:rgba(255,213,0,0.15);">
                    <i class="bi bi-envelope-fill text-warning" style="font-size:1.5rem;"></i>
                </div>
                <h6 class="fw-bold mb-2">Email Support</h6>
                <p class="small text-muted mb-2">Reply within 1-2 business days</p>
                <a href="mailto:support@securelicences.com.au" class="text-decoration-none small fw-semibold">support@securelicences.com.au</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;background:rgba(16,185,129,0.15);">
                    <i class="bi bi-clock-history text-success" style="font-size:1.5rem;"></i>
                </div>
                <h6 class="fw-bold mb-2">Support Hours</h6>
                <p class="small text-muted mb-2">Monday – Friday<br>9:00 AM – 5:00 PM AEST</p>
                <span class="badge bg-success-subtle text-success">Currently online</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;background:rgba(255,132,0,0.15);">
                    <i class="bi bi-chat-square-heart-fill text-primary" style="font-size:1.5rem;"></i>
                </div>
                <h6 class="fw-bold mb-2">Got feedback?</h6>
                <p class="small text-muted mb-2">Tell us what's working or what's not.</p>
                <a href="{{ route('learner.feedback') }}" class="text-decoration-none small fw-semibold">Give Feedback →</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- FAQ Accordion --}}
    <div class="col-lg-7">
        <h5 class="fw-bold mb-3"><i class="bi bi-question-circle me-2 text-primary"></i>Frequently Asked Questions</h5>

        <div class="accordion accordion-flush" id="supportFAQ">
            @php
                $faqs = [
                    ['How do I book a driving lesson?', 'Search for an instructor by your suburb, pick a date and time that suits, then complete the booking. You\'ll receive an email confirmation immediately.'],
                    ['How do I cancel or reschedule my booking?', 'Go to your <a href="' . route('learner.dashboard') . '">dashboard</a> or <a href="' . route('learner.calendar') . '">calendar</a>, find the booking, and click Cancel or Reschedule. Cancellations within 24 hours of the lesson may incur a fee per our cancellation policy.'],
                    ['When does my instructor get paid?', 'Instructors are paid on a weekly schedule (every Monday). Your payment is held securely until your lesson is completed and confirmed.'],
                    ['Can I switch instructors?', 'Yes! You can book lessons with any verified instructor at any time — there\'s no lock-in. Each booking is independent.'],
                    ['What if I want a female instructor?', 'On the <a href="' . route('find-instructor') . '">Find Instructor</a> page you can filter by instructor gender. We respect your safety preferences.'],
                    ['How do I leave a review?', 'After your lesson is marked complete, you\'ll get an email with a link to leave a review. You can also leave one from your <a href="' . route('learner.dashboard') . '">dashboard</a> History tab.'],
                    ['What\'s the cancellation policy?', 'Cancellations more than 24 hours before the lesson are free. Within 24 hours, a 50% fee may apply unless the cancellation is due to illness, weather, or instructor issues. <a href="' . route('policies.refund-cancellation') . '" target="_blank">Read full policy</a>.'],
                    ['How do refunds work?', 'Refunds are processed back to your original payment method or your wallet credit (your choice) within 5 business days of approval.'],
                    ['Is my payment information secure?', 'Yes. We never store credit card details on our servers. All payments go through PCI-compliant payment gateways (Stripe / PayPal).'],
                    ['How does the lesson confirmation work?', 'After your lesson, your instructor marks it complete. You\'ll get an email + SMS asking you to confirm the lesson happened. This protects both you and the instructor.'],
                ];
            @endphp

            @foreach($faqs as $i => $faq)
                <div class="accordion-item mb-2 rounded shadow-sm border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-semibold rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $i }}">
                            {{ $faq[0] }}
                        </button>
                    </h2>
                    <div id="faq-{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#supportFAQ">
                        <div class="accordion-body small text-muted">
                            {!! $faq[1] !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Quick links --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Quick links</h6>
                <div class="row g-2">
                    <div class="col-sm-6">
                        <a href="{{ route('policies.refund-cancellation') }}" class="text-decoration-none d-flex align-items-center gap-2 p-2 rounded hover-bg-light">
                            <i class="bi bi-file-text text-primary"></i>
                            <span class="small">Cancellation & Refund Policy</span>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('terms') }}" class="text-decoration-none d-flex align-items-center gap-2 p-2 rounded hover-bg-light">
                            <i class="bi bi-file-text text-primary"></i>
                            <span class="small">Terms & Conditions</span>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('privacy') }}" class="text-decoration-none d-flex align-items-center gap-2 p-2 rounded hover-bg-light">
                            <i class="bi bi-shield-lock text-primary"></i>
                            <span class="small">Privacy Policy</span>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('policies.learner-conduct') }}" class="text-decoration-none d-flex align-items-center gap-2 p-2 rounded hover-bg-light">
                            <i class="bi bi-person-check text-primary"></i>
                            <span class="small">Learner Code of Conduct</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contact form --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm" id="contact-form">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-envelope-paper me-2 text-primary"></i>Send us a message</h6>
                <p class="small text-muted mb-3">Can't find what you're looking for? Send us a message and we'll get back to you within 1-2 business days.</p>

                <form method="POST" action="{{ route('learner.support.send') }}">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger small">
                            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Subject <span class="text-danger">*</span></label>
                        <select name="subject" class="form-select" required>
                            <option value="">Select a topic</option>
                            <option value="Booking Issue">Booking issue</option>
                            <option value="Payment Question">Payment / billing question</option>
                            <option value="Instructor Issue">Issue with instructor</option>
                            <option value="Cancel Account">Cancel my account</option>
                            <option value="Technical Issue">Technical issue / website not working</option>
                            <option value="Refund Request">Refund request</option>
                            <option value="General Enquiry">General enquiry</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="5" required minlength="10" maxlength="2000" placeholder="Please describe your issue or question in detail. Include booking IDs if relevant.">{{ old('message') }}</textarea>
                        <small class="text-muted">Min 10 characters</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning fw-bold">
                            <i class="bi bi-send me-1"></i>Send Message
                        </button>
                    </div>
                </form>

                <div class="alert alert-info small mt-3 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Logged in as <strong>{{ auth()->user()->name }}</strong>. We'll reply to <strong>{{ auth()->user()->email }}</strong>.
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-bg-light:hover { background: var(--sl-gray-50, #faf9f7); }
.accordion-button:not(.collapsed) {
    background: rgba(255, 213, 0, 0.08);
    color: var(--sl-gray-900);
    box-shadow: none;
}
.accordion-button:focus { box-shadow: none; border-color: var(--sl-accent-500); }
</style>
@endsection
