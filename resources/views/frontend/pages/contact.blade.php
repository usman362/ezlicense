@extends('layouts.frontend')
@section('title', 'Contact & Support — Secure Licences')

@section('content')

{{-- ─────────── HERO ─────────── --}}
<section class="blog-hero">
    <div class="blog-hero-bg">
        <img src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=2000&q=80&auto=format&fit=crop"
             srcset="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=1200&q=80&auto=format&fit=crop 1200w,
                     https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=2000&q=80&auto=format&fit=crop 2000w"
             alt="">
        <div class="blog-hero-overlay"></div>
    </div>
    <div class="container blog-hero-inner">
        <nav aria-label="breadcrumb" class="blog-hero-breadcrumb mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Support</li>
            </ol>
        </nav>
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="blog-hero-eyebrow"><i class="bi bi-headset me-1"></i>We're here to help</span>
                <h1 class="blog-hero-title">
                    How can we <span class="blog-hero-title-accent">help you?</span>
                </h1>
                <p class="blog-hero-sub">
                    Real humans, fast responses. Most enquiries are answered within a few hours during business hours — usually faster.
                </p>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <a href="#contact-form" class="btn btn-warning fw-bold px-4 py-2">
                        <i class="bi bi-envelope-fill me-1"></i>Send us a message
                    </a>
                    <a href="#faq" class="btn btn-outline-light fw-bold px-4 py-2">
                        Browse FAQs
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── CONTACT METHODS GRID ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="blog-eyebrow"><i class="bi bi-lightning-charge-fill me-1"></i>Quick contact</span>
            <h2 class="cl-section-title">Pick the channel that works for you</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <a href="mailto:support@securelicences.com.au" class="pp-conf-card sup-method-card">
                    <div class="pp-conf-icon"><i class="bi bi-envelope-fill"></i></div>
                    <h3>Email Support</h3>
                    <p>support@securelicences.com.au<br><span class="sup-method-time">Reply within 1-2 business days</span></p>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="#contact-form" class="pp-conf-card sup-method-card">
                    <div class="pp-conf-icon"><i class="bi bi-chat-dots-fill"></i></div>
                    <h3>Live Chat</h3>
                    <p>Click the Help button bottom-right<br><span class="sup-method-time">Mon-Fri · 9 AM – 5 PM AEST</span></p>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="mailto:instructors@securelicences.com.au" class="pp-conf-card sup-method-card">
                    <div class="pp-conf-icon"><i class="bi bi-person-badge-fill"></i></div>
                    <h3>For Instructors</h3>
                    <p>instructors@securelicences.com.au<br><span class="sup-method-time">Priority support</span></p>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="#faq" class="pp-conf-card sup-method-card">
                    <div class="pp-conf-icon"><i class="bi bi-question-circle-fill"></i></div>
                    <h3>Help Centre</h3>
                    <p>Browse common questions<br><span class="sup-method-time">Instant answers · 24/7</span></p>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── RESPONSE PROMISE STRIP ─────────── --}}
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="cl-section-title text-center mb-4">Our support promise</h2>
        <div class="row g-4 cl-stats-row">
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">&lt; 4h</div>
                <div class="cl-stat-label">Average first response time during business hours</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">98%</div>
                <div class="cl-stat-label">Of enquiries resolved within 24 hours</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">4.9★</div>
                <div class="cl-stat-label">Average customer support satisfaction rating</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="cl-stat-num">100%</div>
                <div class="cl-stat-label">Real Australian-based support team — no offshore call centres</div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── CONTACT FORM SECTION ─────────── --}}
<section class="py-5" id="contact-form">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5">
                <span class="blog-eyebrow"><i class="bi bi-send-fill me-1"></i>Send a message</span>
                <h2 class="cl-section-title mb-3">Tell us how we can help</h2>
                <p class="text-muted">Fill out the form and our support team will get back to you. For urgent issues, please use Live Chat during business hours.</p>

                <ul class="sup-info-list">
                    <li>
                        <i class="bi bi-envelope-fill"></i>
                        <div>
                            <strong>Email</strong>
                            <span>support@securelicences.com.au</span>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-clock-fill"></i>
                        <div>
                            <strong>Support Hours</strong>
                            <span>Monday – Friday · 9:00 AM – 5:00 PM AEST</span>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-geo-alt-fill"></i>
                        <div>
                            <strong>Head Office</strong>
                            <span>Sydney, NSW Australia</span>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-shield-fill-check"></i>
                        <div>
                            <strong>Privacy promise</strong>
                            <span>Your details are never shared. We reply from a real inbox, not a no-reply bot.</span>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="col-lg-7">
                <div class="sup-form-card">
                    <h3 class="sup-form-title">Send Us a Message</h3>
                    <p class="text-muted small mb-4">All fields marked <span class="text-warning-emphasis fw-bold">*</span> are required.</p>

                    <form id="contact-form-el" method="POST" action="{{ route('contact.send') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Your Name <span class="text-warning-emphasis">*</span></label>
                                <div class="sup-form-input">
                                    <i class="bi bi-person"></i>
                                    <input type="text" class="form-control" name="name" placeholder="Jane Smith" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Email Address <span class="text-warning-emphasis">*</span></label>
                                <div class="sup-form-input">
                                    <i class="bi bi-envelope"></i>
                                    <input type="email" class="form-control" name="email" placeholder="you@email.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Phone <span class="text-muted">(optional)</span></label>
                                <div class="sup-form-input">
                                    <i class="bi bi-telephone"></i>
                                    <input type="tel" class="form-control" name="phone" placeholder="04xx xxx xxx">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Topic</label>
                                <div class="sup-form-input">
                                    <i class="bi bi-tag"></i>
                                    <select class="form-select" name="subject">
                                        <option>General Enquiry</option>
                                        <option>Booking Help</option>
                                        <option>Payment / Refund Issue</option>
                                        <option>Instructor Enquiry</option>
                                        <option>Technical Issue</option>
                                        <option>Feedback / Suggestion</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Message <span class="text-warning-emphasis">*</span></label>
                                <textarea class="form-control sup-form-textarea" name="message" rows="6" placeholder="Tell us as much detail as you can — booking IDs, dates, instructor names if relevant…" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning fw-bold w-100 py-2" id="contact-submit-btn">
                                    <i class="bi bi-send-fill me-1"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                    <div id="contact-success" class="alert alert-success mt-3" style="display:none;">
                        <i class="bi bi-check-circle-fill me-1"></i>Thank you! Your message has been sent. We'll get back to you within 1–2 business days.
                    </div>
                    <div id="contact-error" class="alert alert-danger mt-3" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── FAQ ─────────── --}}
<section class="py-5 bg-light" id="faq">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <span class="blog-eyebrow"><i class="bi bi-question-circle me-1"></i>Common questions</span>
                <h2 class="cl-section-title mb-3">Quick answers to popular questions</h2>
                <p class="text-muted small mb-3">Still need help? Send us a message above — we typically reply within a few hours during business hours.</p>
                <a href="#contact-form" class="btn btn-warning fw-bold"><i class="bi bi-envelope-fill me-1"></i>Send a message</a>
            </div>
            <div class="col-lg-8">
                <div class="accordion ilc-faq" id="sup-faq">
                    @foreach([
                        ['How do I cancel or reschedule a booking?', "Log into your dashboard, go to 'My Bookings', and click the booking you want to change. Reschedule and cancel buttons are right there. Free changes up to 24 hours before the lesson — after that your instructor's cancellation policy applies."],
                        ['When will I get a refund if I cancel?',     "Eligible refunds are processed within 3-5 business days back to your original payment method. If you paid by card, it'll show on your statement within a week."],
                        ["I can't find an instructor in my suburb. What now?", 'Contact us with your suburb name and we will check our active instructor list and waitlist. New instructors join weekly — we can also notify you the moment one signs up in your area.'],
                        ['How do I update my profile details?',       "Log in, click your avatar in the top-right, choose 'Settings' or 'Profile'. You can update your name, email, address, payment methods and notification preferences."],
                        ["My instructor hasn't shown up. What do I do?", "Try calling/messaging them through the platform first. If you can't reach them within 15 minutes of the lesson start, contact us immediately at support@securelicences.com.au — we'll arrange a full refund and help you find another instructor."],
                        ['How do I report an issue with an instructor?', 'Go to your booking and click "Report an issue". Your report goes straight to our trust & safety team and is reviewed within 24 hours. All reports are confidential.'],
                        ['Can I get a Tax Invoice for my lessons?',   'Yes — every booking has a downloadable PDF receipt in your dashboard. For multiple-lesson invoices or business accounts, email us and we will generate a custom invoice.'],
                        ['I forgot my password. How do I reset?',      'Click "Login", then "Forgot password?". Enter your email and we will send a reset link. Check your spam folder if you do not see it within a few minutes.'],
                    ] as $i => [$q, $a])
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#sup-faq-{{ $i }}">{{ $q }}</button>
                            </h3>
                            <div id="sup-faq-{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#sup-faq">
                                <div class="accordion-body text-muted small">{{ $a }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── INSTRUCTOR / LEARNER CTA SPLIT ─────────── --}}
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="sup-split-card sup-split-card-learner">
                    <i class="bi bi-mortarboard-fill sup-split-icon"></i>
                    <h3>Learners</h3>
                    <p>Need help with a booking, payment or finding an instructor? Our learner support team is one click away.</p>
                    <a href="mailto:support@securelicences.com.au" class="btn btn-warning fw-bold">
                        <i class="bi bi-envelope-fill me-1"></i>Email learner support
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="sup-split-card sup-split-card-instructor">
                    <i class="bi bi-person-badge-fill sup-split-icon"></i>
                    <h3>Instructors</h3>
                    <p>Verification, payouts, profile help, business questions — instructors get priority support and faster response times.</p>
                    <a href="mailto:instructors@securelicences.com.au" class="btn btn-dark fw-bold">
                        <i class="bi bi-envelope-fill me-1"></i>Email instructor team
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── BOTTOM CTA ─────────── --}}
<section class="py-5 blog-cta-section">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-8">
                <h2 class="mb-2 fw-bolder text-dark">Still can't find what you're looking for?</h2>
                <p class="mb-0 text-dark">Our support team is one message away — most replies arrive within a few hours.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="#contact-form" class="btn btn-dark fw-bold btn-lg px-4">
                    <i class="bi bi-chat-dots-fill me-2"></i>Contact us
                </a>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.getElementById('contact-form-el').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var btn = document.getElementById('contact-submit-btn');
    var errEl = document.getElementById('contact-error');
    errEl.style.display = 'none';
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Sending...';

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': form.querySelector('[name=_token]').value
        },
        body: new FormData(form)
    })
    .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
    .then(function(res) {
        if (res.ok) {
            form.style.display = 'none';
            document.getElementById('contact-success').style.display = 'block';
        } else {
            var msgs = res.data.errors ? Object.values(res.data.errors).flat().join('<br>') : (res.data.message || 'Something went wrong. Please try again.');
            errEl.innerHTML = msgs;
            errEl.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send-fill me-1"></i>Send Message';
        }
    })
    .catch(function() {
        errEl.textContent = 'Network error. Please try again.';
        errEl.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill me-1"></i>Send Message';
    });
});
</script>
@endpush

@endsection
