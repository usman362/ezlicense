@extends('frontend.policies.layout', [
    'policyTitle'   => 'Learner Code of Conduct',
    'policyLead'    => 'The standards every learner agrees to when booking a lesson through Secure Licences.',
    'policyVersion' => '1.0',
])

@section('policy-body')
<div class="mb-4">
    <h4 class="fw-bold">1. Who this applies to</h4>
    <p>This Code applies to every learner who creates an account on Secure Licences and books a driving lesson, test package, or any other service through our platform.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">2. Respect for your instructor</h4>
    <ul>
        <li>Treat your instructor with the same professional respect you would expect from them.</li>
        <li>Avoid personal, political or romantic topics during lessons.</li>
        <li>Do not record audio or video of your lesson without your instructor's explicit, prior consent.</li>
        <li>Do not offer tips, gifts or payments to your instructor outside the platform in exchange for passing marks or favourable assessments.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">3. Attendance and punctuality</h4>
    <ul>
        <li>Be ready at your agreed pickup location at the scheduled start time.</li>
        <li>If you need to cancel or reschedule, do so through the platform with as much notice as possible — see the <a href="{{ route('policies.refund-cancellation') }}">Refund & Cancellation Policy</a>.</li>
        <li>Repeated no-shows may lead to a temporary or permanent restriction on your account.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">4. Honesty</h4>
    <ul>
        <li>Provide accurate information when creating your account — your real name, contact details and date of birth.</li>
        <li>Hold a valid learner permit for your state or territory before you book a lesson.</li>
        <li>Disclose any medical condition that could affect your ability to drive safely.</li>
        <li>Do not use the platform on behalf of someone else.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">5. Safety and fitness to drive</h4>
    <ul>
        <li>Do not attend a lesson under the influence of alcohol, drugs or any substance that may impair your driving.</li>
        <li>Wear glasses or contact lenses if your permit requires them.</li>
        <li>Follow your instructor's reasonable directions at all times during the lesson.</li>
        <li>If you feel unwell or unsafe during a lesson, tell your instructor immediately.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">6. Reviews and feedback</h4>
    <p>You are welcome — and encouraged — to leave honest feedback after a lesson. Your review helps other learners choose a good instructor and helps instructors improve.</p>
    <ul>
        <li>Reviews must be based on your actual experience with the instructor named.</li>
        <li>Do not submit reviews that contain abusive language, threats, discriminatory remarks or false factual claims.</li>
        <li>Do not submit reviews in exchange for a discount or any other benefit.</li>
        <li>Secure Licences may moderate, hide or remove reviews that breach this policy.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">7. Complaints — making one in good faith</h4>
    <p>If something goes wrong in a lesson, we want to hear about it. Our <a href="{{ route('policies.complaint-handling') }}">Complaint Handling Policy</a> explains exactly how we investigate. Please:</p>
    <ul>
        <li>Report the issue as soon as possible while details are fresh.</li>
        <li>Be specific — dates, times, what happened, and any evidence you have.</li>
        <li>Be truthful. We take every complaint seriously, and we investigate both sides.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">8. False or malicious complaints</h4>
    <p>Making a false complaint — whether to damage an instructor's reputation, to avoid paying for a lesson, or for any other reason — is a serious breach of this Code.</p>
    <p>If we determine that a complaint was knowingly false, we may:</p>
    <ul>
        <li>Record a warning on your account.</li>
        <li>Suspend or permanently close your account.</li>
        <li>Refuse any refund claim related to the false complaint.</li>
        <li>In cases involving defamation or serious harm, cooperate with any legal or police action the instructor chooses to pursue.</li>
    </ul>
    <p>We will always investigate fairly before drawing any conclusion. A complaint that is simply not upheld is not the same as a false complaint.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">9. Your rights as a learner</h4>
    <ul>
        <li>You have the right to a safe, respectful, and professional lesson.</li>
        <li>You have the right to change instructors at any time.</li>
        <li>You have the right to raise a complaint and have it investigated fairly.</li>
        <li>You have the right to a refund under the <a href="{{ route('policies.refund-cancellation') }}">Refund & Cancellation Policy</a>.</li>
        <li>You have the right to access and correct the personal information we hold about you.</li>
    </ul>
</div>
@endsection
