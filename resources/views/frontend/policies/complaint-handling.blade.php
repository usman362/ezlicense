@extends('frontend.policies.layout', [
    'policyTitle'   => 'Complaint Handling Policy',
    'policyLead'    => 'How Secure Licences investigates complaints fairly and protects both learners and instructors.',
    'policyVersion' => '1.0',
])

@section('policy-body')
<div class="mb-4">
    <h4 class="fw-bold">1. Our commitment</h4>
    <p>Secure Licences takes every complaint seriously — whether it comes from a learner, a parent, an instructor, or a third party. We investigate each report thoroughly, impartially, and with respect for everyone involved.</p>
    <p>We also recognise that not every complaint is accurate. An instructor deserves the same fairness as the person making the complaint. This policy exists so that nobody — learner or instructor — is judged without the facts being properly checked.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">2. What you can complain about</h4>
    <p>You can raise a complaint about any of the following:</p>
    <ul>
        <li>Inappropriate behaviour, harassment, or misconduct by an instructor or learner.</li>
        <li>Safety concerns about a vehicle or driving environment.</li>
        <li>A no-show, late arrival, or cancelled lesson without notice.</li>
        <li>A billing or refund dispute.</li>
        <li>A review you believe is false or defamatory.</li>
        <li>Anything else you feel is a breach of our <a href="{{ route('policies.instructor-conduct') }}">Instructor</a> or <a href="{{ route('policies.learner-conduct') }}">Learner</a> Code of Conduct.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">3. How to submit a complaint</h4>
    <p>You can submit a complaint in any of these ways:</p>
    <ul>
        <li><strong>In-app:</strong> Use the "Report an issue" option on the booking page.</li>
        <li><strong>Email:</strong> <a href="mailto:support@securelicences.com.au">support@securelicences.com.au</a></li>
        <li><strong>Phone:</strong> via the number listed on our <a href="{{ route('contact') }}">Contact page</a>. Calls may be recorded for quality and investigation purposes, with your prior consent.</li>
    </ul>
    <p>Please include: the booking ID, the date and time of the incident, what happened, and any evidence you have (photos, screenshots, messages).</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">4. How we investigate — step by step</h4>
    <ol>
        <li><strong>Acknowledgement (within 1 business day).</strong> We confirm we have received your complaint and assign it a reference number.</li>
        <li><strong>Initial review (within 3 business days).</strong> A member of our support team reads the complaint, checks the booking history, and reviews any existing record on file for both parties.</li>
        <li><strong>Contact with both parties.</strong> We speak to the person who raised the complaint <em>and</em> to the person it is about. Each side is given the chance to explain in their own words. We ask for any supporting evidence.</li>
        <li><strong>Independent checks.</strong> Where relevant, we may:
            <ul>
                <li>Review the instructor's history on the platform — other ratings, prior complaints (if any), warnings, and time on the platform.</li>
                <li>Review the learner's history — other bookings, other complaints they have made, and their interactions with previous instructors.</li>
                <li>Reach out to past learners who recently had a lesson with the instructor, to quietly check whether anyone else has had a similar concern. These calls are always voluntary and the original complainant's identity is kept confidential.</li>
                <li>Request records from our internal systems — messages, correspondence, audit logs, and call recordings (where consented to).</li>
            </ul>
        </li>
        <li><strong>Decision and outcome (typically within 10 business days).</strong> We write a short summary of what we found and what action, if any, we are taking. Both parties are told the outcome.</li>
        <li><strong>Right of reply.</strong> If you disagree with the outcome, you can ask for a review under our <a href="{{ route('policies.dispute-resolution') }}">Dispute Resolution Policy</a>.</li>
    </ol>
</div>

<div class="mb-4">
    <h4 class="fw-bold">5. What we keep on file</h4>
    <p>For every complaint, we record in our internal system:</p>
    <ul>
        <li>The date, time, and channel the complaint was received.</li>
        <li>The full text of the complaint, plus any attachments.</li>
        <li>Notes from every call or message in connection with the investigation.</li>
        <li>The outcome and the reasoning behind it.</li>
        <li>Any action taken (warning, suspension, refund, removal, etc.).</li>
    </ul>
    <p>This record is kept on both the instructor's and the learner's internal profile so that we can see patterns over time. Either party has the right to request a copy of their own file at any time.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">6. Fairness to instructors</h4>
    <p>Instructors are the backbone of our platform. A complaint alone is not proof — it is the start of an investigation.</p>
    <ul>
        <li>We will never remove, suspend, or publicly penalise an instructor based on a complaint that has not been investigated.</li>
        <li>If we find a complaint to be unsupported, the complaint record is closed as <em>"not upheld"</em> and does not affect the instructor's rating, ranking, or standing on the platform.</li>
        <li>If we find a complaint to be knowingly false, the learner who submitted it may receive a warning, have their account suspended, or be removed from the platform under our <a href="{{ route('policies.learner-conduct') }}">Learner Code of Conduct</a>.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">7. Fairness to learners</h4>
    <ul>
        <li>Your identity will not be shared with the instructor without your permission, unless law enforcement requires it or you consent.</li>
        <li>You will not be penalised for raising a concern in good faith, even if it is not upheld after investigation.</li>
        <li>If you are under 18, a parent or guardian may submit the complaint on your behalf.</li>
        <li>You can withdraw a complaint at any time before a final decision is made.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">8. Serious matters and police referral</h4>
    <p>Some complaints are too serious to be handled on the platform alone. This includes but is not limited to:</p>
    <ul>
        <li>Physical or sexual assault.</li>
        <li>Threats or stalking.</li>
        <li>Drug or alcohol use while conducting a lesson.</li>
        <li>Fraud or identity theft.</li>
    </ul>
    <p>For these matters we will, where appropriate and with the complainant's knowledge, refer the case to the police or state regulator and cooperate fully with any investigation. We will also support anyone affected in making their own report directly.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">9. Confidentiality</h4>
    <p>Everything said during an investigation stays within the Secure Licences support team unless:</p>
    <ul>
        <li>You give us permission to share it with the other party;</li>
        <li>Disclosure is required by law;</li>
        <li>It is necessary to protect someone from serious harm.</li>
    </ul>
    <p>All files are stored in accordance with our <a href="{{ route('privacy') }}">Privacy Policy</a>.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">10. Free to use</h4>
    <p>There is no cost to lodge or pursue a complaint through Secure Licences. You never need a lawyer to raise an issue with us.</p>
</div>
@endsection
