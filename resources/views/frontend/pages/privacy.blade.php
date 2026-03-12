@extends('layouts.frontend')
@section('title', 'Privacy Policy')
@section('content')
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li><li class="breadcrumb-item active">Privacy Policy</li></ol></nav>
    </div>
</div>
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <h1 class="display-6 fw-bold mb-4" style="color: var(--ez-dark);">Privacy Policy</h1>
                <p class="text-muted mb-4">Last updated: {{ date('F Y') }}</p>

                <div class="mb-4">
                    <h4 class="fw-bold">1. Introduction</h4>
                    <p>EzLicence is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our website and services. We comply with the Australian Privacy Principles (APPs) contained in the Privacy Act 1988 (Cth).</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">2. Information We Collect</h4>
                    <p>We collect information you provide directly to us, including:</p>
                    <ul class="mb-3">
                        <li>Personal details: name, email address, phone number, date of birth, gender</li>
                        <li>Location data: suburb, postcode, state</li>
                        <li>Learner permit details for verification purposes</li>
                        <li>Payment information (processed securely by our payment partners)</li>
                        <li>Booking history and lesson details</li>
                        <li>Communications with instructors and support</li>
                    </ul>
                    <p>For instructors, we additionally collect licence details, Working With Children Check (WWCC) information, vehicle details, ABN, and banking information for payments.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">3. How We Use Your Information</h4>
                    <p>We use the information we collect to:</p>
                    <ul class="mb-3">
                        <li>Provide, maintain, and improve our services</li>
                        <li>Process bookings and payments</li>
                        <li>Connect learners with suitable driving instructors</li>
                        <li>Send booking confirmations, reminders, and service updates</li>
                        <li>Respond to support requests and inquiries</li>
                        <li>Ensure instructor compliance with safety and licensing requirements</li>
                        <li>Detect and prevent fraud or other harmful activities</li>
                        <li>Analyse usage patterns to improve our platform</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">4. Information Sharing</h4>
                    <p>We may share your information with:</p>
                    <ul class="mb-3">
                        <li><strong>Instructors/Learners:</strong> Relevant booking and contact details are shared between matched instructors and learners to facilitate lessons.</li>
                        <li><strong>Payment processors:</strong> Payment details are shared with our secure payment partners to process transactions.</li>
                        <li><strong>Service providers:</strong> We may share information with third-party service providers who assist with platform operations, analytics, and customer support.</li>
                        <li><strong>Legal requirements:</strong> We may disclose information where required by law or to protect our rights and the safety of our users.</li>
                    </ul>
                    <p>We do not sell your personal information to third parties.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">5. Data Security</h4>
                    <p>We implement appropriate technical and organisational measures to protect your personal information against unauthorised access, alteration, disclosure, or destruction. This includes encryption of sensitive data, secure server infrastructure, and regular security assessments.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">6. Cookies and Tracking</h4>
                    <p>Our website uses cookies and similar tracking technologies to improve your browsing experience, analyse site traffic, and personalise content. You can control cookie preferences through your browser settings. Essential cookies required for platform functionality cannot be disabled.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">7. Your Rights</h4>
                    <p>Under Australian privacy law, you have the right to:</p>
                    <ul class="mb-3">
                        <li>Access the personal information we hold about you</li>
                        <li>Request correction of inaccurate or outdated information</li>
                        <li>Request deletion of your personal information (subject to legal obligations)</li>
                        <li>Opt out of marketing communications at any time</li>
                        <li>Lodge a complaint with the Office of the Australian Information Commissioner (OAIC)</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">8. Data Retention</h4>
                    <p>We retain your personal information for as long as your account is active or as needed to provide our services. We may retain certain information after account closure as required by law or for legitimate business purposes such as dispute resolution and fraud prevention.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">9. Children's Privacy</h4>
                    <p>Our services are intended for users who are eligible to hold a learner driving permit in their state or territory. We do not knowingly collect personal information from children under 15 years of age without parental consent.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">10. Changes to This Policy</h4>
                    <p>We may update this Privacy Policy from time to time. We will notify you of any material changes by posting the new policy on this page and updating the "Last updated" date. We encourage you to review this policy periodically.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">11. Contact Us</h4>
                    <p>If you have any questions or concerns about this Privacy Policy or our data practices, please contact us:</p>
                    <ul class="list-unstyled ms-3">
                        <li><strong>Email:</strong> privacy@ezlicence.com.au</li>
                        <li><strong>Web:</strong> <a href="{{ url('/contact') }}">Contact page</a></li>
                        <li><strong>Post:</strong> EzLicence Privacy Officer, Sydney, NSW, Australia</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
