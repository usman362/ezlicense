@extends('frontend.policies.layout', [
    'policyTitle'   => 'Refund & Cancellation Policy',
    'policyLead'    => 'When you can cancel, when you can get a refund, and how disputed bookings are resolved.',
    'policyVersion' => '1.0',
])

@section('policy-body')
<div class="mb-4">
    <h4 class="fw-bold">1. Your consumer rights</h4>
    <p>Nothing in this policy limits your rights under the Australian Consumer Law. If a service is not delivered with due care and skill, or is not fit for purpose, you are entitled to a remedy regardless of what this policy says.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">2. Cancelling a lesson — learners</h4>
    <p>You can cancel or reschedule a booking from your Secure Licences dashboard. The following notice periods apply unless your instructor has set different terms on their profile (which will be shown at the time of booking):</p>
    <div class="table-responsive mb-3">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Notice before lesson start</th>
                    <th>What you pay</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>More than 48 hours</td>
                    <td>Full refund or free reschedule</td>
                </tr>
                <tr>
                    <td>24 to 48 hours</td>
                    <td>Free reschedule, or 50% refund</td>
                </tr>
                <tr>
                    <td>Less than 24 hours</td>
                    <td>No refund, lesson counted as used</td>
                </tr>
                <tr>
                    <td>No-show (you do not attend)</td>
                    <td>No refund, lesson counted as used</td>
                </tr>
            </tbody>
        </table>
    </div>
    <p>Where a refund applies, it is returned to your original payment method or credited to your Secure Licences wallet — your choice.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">3. Cancelling a lesson — instructors</h4>
    <p>If an instructor cancels a lesson, the learner is always entitled to a full refund or a free reschedule, whichever they prefer. Frequent cancellations by an instructor may result in a warning under our <a href="{{ route('policies.instructor-conduct') }}">Instructor Code of Conduct</a>.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">4. When we cancel a lesson</h4>
    <p>On rare occasions, Secure Licences may need to cancel or hold a booking — for example, if an instructor's licence has lapsed or if we are investigating a complaint. In these cases the learner receives a full refund or is matched with another instructor at the same price, at the learner's choice.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">5. Lesson disputes</h4>
    <p>If you believe a lesson was not delivered as described — for example, it was much shorter than booked, the instructor did not arrive, or the vehicle was unsafe — you can raise a dispute within 7 days of the lesson.</p>
    <p>We will review the booking, talk to both parties, and issue a full or partial refund where a genuine problem is found. See our <a href="{{ route('policies.complaint-handling') }}">Complaint Handling Policy</a> for the full process.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">6. Package and bundle refunds</h4>
    <ul>
        <li>If you purchased a multi-lesson package, you can request a refund on any lessons you have not yet used.</li>
        <li>Lessons already completed are not refundable.</li>
        <li>Any platform discount applied to the package is deducted proportionally from the refunded amount.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">7. Gift vouchers</h4>
    <ul>
        <li>Gift vouchers are non-refundable once issued, except where required by law.</li>
        <li>Vouchers can be used for any driving lesson or test package on the platform until their expiry date.</li>
        <li>If a voucher code has not been redeemed and you believe it was issued in error, contact support within 14 days of purchase.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">8. Refunds from false complaints</h4>
    <p>If a learner raises a complaint that is later determined to be knowingly false, any refund issued as part of that complaint may be reversed. This is in addition to any other action taken under our <a href="{{ route('policies.learner-conduct') }}">Learner Code of Conduct</a>.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">9. How to request a refund</h4>
    <ol>
        <li>Log in to your Secure Licences account.</li>
        <li>Go to <strong>Bookings</strong>, find the relevant lesson, and tap <strong>Cancel</strong> or <strong>Request a refund</strong>.</li>
        <li>Select the reason and add any notes or attachments.</li>
        <li>Our support team will review your request within 3 business days and notify you of the outcome.</li>
    </ol>
    <p>If you cannot access your account, email <a href="mailto:support@securelicences.com.au">support@securelicences.com.au</a> with your booking reference.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">10. Processing times</h4>
    <p>Approved refunds are usually processed within 5 business days. The time it takes to appear in your bank account depends on your bank or card provider and can vary.</p>
</div>
@endsection
