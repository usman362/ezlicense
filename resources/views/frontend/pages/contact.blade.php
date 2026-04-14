@extends('layouts.frontend')
@section('title', 'Contact Us')
@section('content')
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li><li class="breadcrumb-item active">Contact</li></ol></nav>
    </div>
</div>
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6">
                <h1 class="display-6 fw-bold mb-4" style="color: var(--ez-dark);">Contact Us</h1>
                <p class="lead mb-4">Have a question or need help? We'd love to hear from you.</p>
                <div class="mb-4">
                    <h5 class="fw-bold"><i class="bi bi-envelope text-warning me-2"></i>Email</h5>
                    <p>support@securelicences.com.au</p>
                </div>
                <div class="mb-4">
                    <h5 class="fw-bold"><i class="bi bi-clock text-warning me-2"></i>Support Hours</h5>
                    <p>Monday – Friday: 9:00 AM – 5:00 PM AEST<br>Saturday – Sunday: Closed</p>
                </div>
                <div class="mb-4">
                    <h5 class="fw-bold"><i class="bi bi-chat-dots text-warning me-2"></i>Live Chat</h5>
                    <p>Available via the Help button on any page during business hours.</p>
                </div>
                <div class="card bg-light border-0 p-4">
                    <h5 class="fw-bold mb-2">For Instructors</h5>
                    <p class="small mb-0">If you're a driving instructor and want to join the Secure Licences platform, visit our <a href="{{ route('instruct-with-us') }}">Instruct with Secure Licences</a> page or email us at <strong>instructors@securelicences.com.au</strong></p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="h5 fw-bold mb-3">Send Us a Message</h3>
                        <form id="contact-form" method="POST" action="{{ route('contact.send') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Your Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Email Address *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Phone (optional)</label>
                                <input type="tel" class="form-control" name="phone">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Subject</label>
                                <select class="form-select" name="subject">
                                    <option>General Enquiry</option>
                                    <option>Booking Help</option>
                                    <option>Payment Issue</option>
                                    <option>Instructor Enquiry</option>
                                    <option>Technical Issue</option>
                                    <option>Feedback</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Message *</label>
                                <textarea class="form-control" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning fw-bold w-100" id="contact-submit-btn">Send Message</button>
                        </form>
                        <div id="contact-success" class="alert alert-success mt-3" style="display:none;">Thank you! Your message has been sent. We'll get back to you within 1-2 business days.</div>
                        <div id="contact-error" class="alert alert-danger mt-3" style="display:none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script>
document.getElementById('contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var btn = document.getElementById('contact-submit-btn');
    var errEl = document.getElementById('contact-error');
    errEl.style.display = 'none';
    btn.disabled = true;
    btn.textContent = 'Sending...';

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
            btn.textContent = 'Send Message';
        }
    })
    .catch(function() {
        errEl.textContent = 'Network error. Please try again.';
        errEl.style.display = 'block';
        btn.disabled = false;
        btn.textContent = 'Send Message';
    });
});
</script>
@endpush
@endsection
