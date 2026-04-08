@extends('frontend.policies.layout', [
    'policyTitle'   => 'Instructor Code of Conduct',
    'policyLead'    => 'Professional standards every Secure Licences instructor agrees to when joining the platform.',
    'policyVersion' => '1.0',
])

@section('policy-body')
<div class="mb-4">
    <h4 class="fw-bold">1. Who this applies to</h4>
    <p>This Code applies to every driving instructor listed on Secure Licences. By activating a profile and accepting a booking, you agree to follow it for every lesson you conduct through our platform.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">2. Professional conduct</h4>
    <ul>
        <li>Treat every learner with courtesy, patience and respect, regardless of background, ability, gender, religion or origin.</li>
        <li>Use appropriate language. No swearing, intimidation, shouting or personal remarks about the learner's appearance or personal life.</li>
        <li>Keep conversation focused on driving instruction. Avoid personal, political or romantic topics.</li>
        <li>Dress in neat, weather-appropriate clothing suitable for a professional service.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">3. Physical boundaries</h4>
    <ul>
        <li>Do not touch a learner except where genuinely necessary for immediate safety (for example, helping them correct the steering wheel to avoid a collision).</li>
        <li>If you must assist physically, explain what you are about to do first and keep contact brief.</li>
        <li>Lessons are to be conducted in the vehicle. Do not ask a learner to accompany you to any other location unrelated to the lesson.</li>
        <li>Do not offer or accept rides outside of booked lessons.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">4. Communication outside lessons</h4>
    <ul>
        <li>Where possible, communicate with learners through the Secure Licences platform so all correspondence is logged.</li>
        <li>If a learner is under 18, keep all communication professional and limit it to lesson coordination.</li>
        <li>Do not add learners on personal social media. Do not send non-lesson-related messages.</li>
        <li>If a learner behaves inappropriately toward you, stop the lesson, document what happened and report it to Secure Licences the same day.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">5. Vehicle and safety</h4>
    <ul>
        <li>Your lesson vehicle must be roadworthy, comprehensively insured and fitted with dual controls where required by your state.</li>
        <li>The vehicle must be clean, smoke-free and free of items that could obstruct the learner or pose a hazard.</li>
        <li>You must hold a current driving instructor's licence and Working With Children Check (WWCC) for your state, and keep them current on your profile.</li>
        <li>Never conduct a lesson while affected by alcohol, drugs, or medication that impairs your ability to drive.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">6. Honesty on the platform</h4>
    <ul>
        <li>Keep your profile details, pricing, service areas and availability accurate.</li>
        <li>Do not request payment outside the platform for lessons booked through Secure Licences.</li>
        <li>Do not ask a learner to remove or alter a legitimate review.</li>
        <li>Do not create additional accounts or impersonate another instructor.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">7. Complaints and investigations</h4>
    <p>If a complaint is made about you, we will investigate it fairly under our <a href="{{ route('policies.complaint-handling') }}">Complaint Handling Policy</a>. You will have an opportunity to respond before any action is taken, and all evidence we consider will be recorded in your internal file.</p>
    <p>If a complaint turns out to be unfounded, it will be marked as such and will not affect your standing on the platform. We also keep track of learners who submit false complaints.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">8. Consequences of breaching this Code</h4>
    <p>Depending on the severity of a breach, Secure Licences may take one or more of the following actions:</p>
    <ul>
        <li>A written warning recorded on your profile.</li>
        <li>Temporary suspension of your profile (for example, 30, 60 or 90 days).</li>
        <li>Permanent removal from the platform.</li>
        <li>Referral to police or the state licensing authority where the law requires it.</li>
    </ul>
    <p>Serious breaches — assault, sexual misconduct, driving under the influence, or endangering a learner's safety — will result in immediate permanent removal and mandatory reporting.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">9. Your rights as an instructor</h4>
    <ul>
        <li>You have the right to stop a lesson if you feel unsafe, threatened or harassed.</li>
        <li>You have the right to refuse a booking where there is a reasonable concern.</li>
        <li>You have the right to request a full record of any complaint against you and to respond in writing.</li>
        <li>You have the right to appeal any warning, block or removal — see the <a href="{{ route('policies.dispute-resolution') }}">Dispute Resolution Policy</a>.</li>
    </ul>
</div>
@endsection
