@extends('layouts.frontend')
@section('title', 'Terms and Conditions')
@section('content')
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li><li class="breadcrumb-item active">Terms and Conditions</li></ol></nav>
    </div>
</div>
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <h1 class="display-6 fw-bold mb-4" style="color: var(--ez-dark);">Terms and Conditions</h1>
                <p class="text-muted mb-4">Last updated: {{ date('F Y') }}</p>

                <div class="mb-4">
                    <h4 class="fw-bold">1. Acceptance of Terms</h4>
                    <p>By accessing and using the Secure Licences website and services, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by these terms, please do not use this service.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">2. Description of Service</h4>
                    <p>Secure Licences provides an online platform connecting learner drivers with qualified driving instructors. Our services include booking driving lessons, managing lesson schedules, processing payments, and facilitating communication between learners and instructors.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">3. User Accounts</h4>
                    <p>To use certain features of the platform, you must register for an account. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You agree to provide accurate and complete information during registration and to update such information to keep it current.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">4. Bookings and Payments</h4>
                    <p>All bookings made through the platform are subject to instructor availability and confirmation. Payments are processed securely through our payment partners. A platform service fee applies to all bookings. Prices displayed are in Australian Dollars (AUD) and include GST where applicable.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">5. Cancellation and Refund Policy</h4>
                    <p>Learners may cancel or reschedule a booking subject to the cancellation policy displayed at the time of booking. Cancellations made with less than 24 hours notice may incur a cancellation fee. Refunds, where applicable, will be processed to the original payment method or credited to the learner's Secure Licences wallet.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">6. Instructor Obligations</h4>
                    <p>Instructors on the platform are independent contractors, not employees of Secure Licences. Instructors must maintain valid licences, insurance, and accreditations required by their state or territory. Secure Licences reserves the right to verify instructor credentials and remove any instructor who fails to meet platform standards.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">7. Learner Obligations</h4>
                    <p>Learners must hold a valid learner permit for their state or territory. Learners agree to attend booked lessons on time and to comply with all road rules and instructor directions during lessons. Learners are responsible for informing their instructor of any medical conditions that may affect their ability to drive safely.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">8. Limitation of Liability</h4>
                    <p>Secure Licences acts as a marketplace platform connecting learners with instructors. We do not provide driving instruction directly and are not liable for the quality of instruction, outcomes of driving tests, or any incidents that may occur during lessons. To the maximum extent permitted by law, Secure Licences's liability is limited to the service fees paid by the user.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">9. Intellectual Property</h4>
                    <p>All content on the Secure Licences platform, including logos, text, graphics, and software, is the property of Secure Licences or its licensors and is protected by Australian and international copyright laws. You may not reproduce, distribute, or create derivative works from any content without prior written consent.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">10. Privacy</h4>
                    <p>Your use of the platform is also governed by our <a href="{{ url('/privacy-policy') }}">Privacy Policy</a>. By using Secure Licences, you consent to the collection and use of your information as described in the Privacy Policy.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">11. Modifications to Terms</h4>
                    <p>Secure Licences reserves the right to modify these terms at any time. Changes will be effective immediately upon posting to the website. Your continued use of the platform after changes are posted constitutes your acceptance of the modified terms.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">12. Governing Law</h4>
                    <p>These terms are governed by the laws of New South Wales, Australia. Any disputes arising from these terms or your use of the platform will be subject to the exclusive jurisdiction of the courts of New South Wales.</p>
                </div>

                <div class="mb-4">
                    <h4 class="fw-bold">13. Contact Us</h4>
                    <p>If you have any questions about these Terms and Conditions, please contact us at <a href="{{ url('/contact') }}">our contact page</a> or email <strong>support@securelicences.com.au</strong>.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
