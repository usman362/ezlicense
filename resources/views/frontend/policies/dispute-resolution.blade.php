@extends('frontend.policies.layout', [
    'policyTitle'   => 'Dispute Resolution Policy',
    'policyLead'    => 'How disagreements between learners, instructors and Secure Licences are resolved fairly and in writing.',
    'policyVersion' => '1.0',
])

@section('policy-body')
<div class="mb-4">
    <h4 class="fw-bold">1. When this policy applies</h4>
    <p>This policy applies when you disagree with an outcome from Secure Licences — for example, the result of a complaint investigation, a refund decision, a warning on your profile, a suspension, or removal from the platform.</p>
    <p>It also applies to disagreements between a learner and an instructor that the two of you have not been able to resolve directly.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">2. Step 1 — Talk to the other side first</h4>
    <p>If the disagreement is between you and another user of the platform, we encourage you to try to resolve it directly and politely through our messaging system. A short, honest conversation often solves the problem faster than a formal process.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">3. Step 2 — Raise it with Secure Licences support</h4>
    <p>If that does not work, or if your disagreement is with a decision Secure Licences has made, email <a href="mailto:support@securelicences.com.au">support@securelicences.com.au</a> with:</p>
    <ul>
        <li>Your name and the email on your account.</li>
        <li>The booking or complaint reference number.</li>
        <li>A clear explanation of what you disagree with and what outcome you are seeking.</li>
        <li>Any new evidence you have that was not considered earlier.</li>
    </ul>
    <p>We will acknowledge your email within 1 business day.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">4. Step 3 — Internal review</h4>
    <p>Your dispute is reviewed by a different team member than the one who made the original decision. They will:</p>
    <ol>
        <li>Re-read the full file — your account, the booking, any call notes, messages, and evidence.</li>
        <li>Consider any new information you have provided.</li>
        <li>Contact you (and the other party, if relevant) for any follow-up questions.</li>
        <li>Send you a written outcome within 10 business days.</li>
    </ol>
    <p>The written outcome will explain the decision and the reasons for it, in plain language.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">5. Step 4 — External avenues</h4>
    <p>If you are still not satisfied after our internal review, you may take your matter to an external body. Depending on the nature of the dispute, these may include:</p>
    <ul>
        <li>Your state or territory's <strong>Fair Trading</strong> or consumer affairs office.</li>
        <li>The <strong>Australian Competition and Consumer Commission (ACCC)</strong> for consumer law matters.</li>
        <li>The <strong>Office of the Australian Information Commissioner (OAIC)</strong> for privacy concerns.</li>
        <li>The <strong>police</strong> for matters involving safety, threats, or criminal conduct.</li>
        <li>A <strong>court or tribunal</strong> in your state or territory.</li>
    </ul>
    <p>Going to an external body does not cost you anything at Secure Licences, and we will cooperate fully with any request for information from a lawful authority.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">6. Timeframes</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Step</th>
                    <th>Our commitment</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Acknowledge your dispute</td><td>1 business day</td></tr>
                <tr><td>Initial review complete</td><td>3 business days</td></tr>
                <tr><td>Follow-up questions (if any)</td><td>Within 5 business days</td></tr>
                <tr><td>Final written outcome</td><td>Within 10 business days</td></tr>
            </tbody>
        </table>
    </div>
    <p>If a matter is particularly complex we may need more time, in which case we will tell you and give you a new expected date.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">7. Good faith expectations</h4>
    <p>We expect everyone involved in a dispute to act in good faith — to be honest, to respond to our messages, and to treat the team and the other party with respect. Abusive or threatening conduct towards our staff may itself be treated as a breach of the relevant Code of Conduct.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">8. No cost to you</h4>
    <p>There is no fee for using any step in this process, and you do not need legal representation. If you would feel more comfortable with support, you are welcome to bring a trusted friend, family member or advocate.</p>
</div>
@endsection
