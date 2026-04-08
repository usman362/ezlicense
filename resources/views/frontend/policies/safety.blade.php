@extends('frontend.policies.layout', [
    'policyTitle'   => 'Safety Policy',
    'policyLead'    => 'The safety standards we require from every instructor and expect from every learner.',
    'policyVersion' => '1.0',
])

@section('policy-body')
<div class="mb-4">
    <h4 class="fw-bold">1. Our safety commitment</h4>
    <p>Driving lessons should be safe, professional, and comfortable for everyone. Secure Licences only accepts instructors who meet the state legal standards and our additional internal checks. We monitor safety on an ongoing basis and act quickly when a concern is raised.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">2. Instructor checks on joining</h4>
    <p>Before an instructor can list on Secure Licences, they must upload and verify:</p>
    <ul>
        <li><strong>Driver's Licence</strong> — front and back, with expiry date.</li>
        <li><strong>Driving Instructor's Licence</strong> — the accreditation issued by their state or territory.</li>
        <li><strong>Working With Children Check (WWCC)</strong> — or equivalent in states that use a different name.</li>
        <li><strong>Vehicle details</strong> — make, model, year, transmission, and ANCAP safety rating where applicable.</li>
        <li><strong>Insurance</strong> — comprehensive motor insurance that covers driving instruction.</li>
    </ul>
    <p>All documents are reviewed manually by a member of our team before the profile goes live. Documents are re-checked when they approach expiry.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">3. Vehicle standards</h4>
    <ul>
        <li>Current registration and roadworthiness.</li>
        <li>Dual controls where required by state regulations.</li>
        <li>Functioning seatbelts for every occupant.</li>
        <li>Clean and free of obstructions around the pedals or steering area.</li>
        <li>No smoking in the vehicle, including between lessons.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">4. Pickup and drop-off</h4>
    <ul>
        <li>Lessons should begin and end at safe, public locations agreed with the learner in advance.</li>
        <li>Instructors should not pick up or drop off at locations that make the learner feel unsafe.</li>
        <li>For learners under 18, we recommend lessons start and end at or near the learner's home, school, or another familiar public place.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">5. Professional boundaries</h4>
    <p>Instructors must follow the physical and communication boundaries set out in the <a href="{{ route('policies.instructor-conduct') }}">Instructor Code of Conduct</a>. In particular:</p>
    <ul>
        <li>No physical contact except where needed for immediate safety.</li>
        <li>No romantic, sexual, or personal conversation.</li>
        <li>No private communication outside the platform except for the purpose of coordinating the lesson.</li>
        <li>No photos or videos of a learner during a lesson without their consent.</li>
    </ul>
</div>

<div class="mb-4">
    <h4 class="fw-bold">6. Reporting a safety concern</h4>
    <p>If you feel unsafe during a lesson — whether you are the instructor or the learner — you should:</p>
    <ol>
        <li>Pull over or end the lesson at the nearest safe location.</li>
        <li>Call emergency services on <strong>000</strong> if anyone is in immediate danger.</li>
        <li>Report the incident to Secure Licences as soon as possible through the in-app report link or by emailing <a href="mailto:support@securelicences.com.au">support@securelicences.com.au</a>.</li>
    </ol>
    <p>We treat every safety report urgently and follow the process in our <a href="{{ route('policies.complaint-handling') }}">Complaint Handling Policy</a>.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">7. Incident records</h4>
    <p>Every safety incident reported to Secure Licences is added to our internal records. This allows us to see patterns, follow up on unresolved matters, and share information with the police or licensing authorities where legally required.</p>
</div>

<div class="mb-4">
    <h4 class="fw-bold">8. Zero-tolerance matters</h4>
    <p>The following will lead to immediate permanent removal from the platform, and may be reported to the police or state regulator:</p>
    <ul>
        <li>Sexual misconduct or harassment of any kind.</li>
        <li>Physical assault or threats of violence.</li>
        <li>Conducting a lesson under the influence of alcohol, drugs, or impairing medication.</li>
        <li>Driving the lesson vehicle without a valid licence or insurance.</li>
        <li>Providing false identity or credential documents.</li>
    </ul>
</div>
@endsection
