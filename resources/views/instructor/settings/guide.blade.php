@extends('layouts.instructor')

@section('title', 'Getting Started Guide')
@section('heading', 'Settings › Guide')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.settings.personal-details') }}">Settings</a></li>
        <li class="breadcrumb-item active" aria-current="page">Guide</li>
    </ol>
</nav>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h4 class="fw-bold mb-3"><i class="bi bi-book me-2 text-warning"></i>Getting Started Guide</h4>
        <p class="text-muted">Welcome to Secure Licences! Follow these steps to set up your instructor profile and start receiving bookings.</p>

        <div class="mt-4">
            {{-- Step 1 --}}
            <div class="d-flex gap-3 mb-4">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;color:#333;">1</div>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Complete Your Personal Details</h6>
                    <p class="text-muted small mb-1">Add your name, phone number, postcode, and gender. You'll also set your password here.</p>
                    <a href="{{ route('instructor.settings.personal-details') }}" class="btn btn-sm btn-outline-primary">Go to Personal Details <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>

            {{-- Step 2 --}}
            <div class="d-flex gap-3 mb-4">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;color:#333;">2</div>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Set Up Your Profile</h6>
                    <p class="text-muted small mb-1">Upload a profile photo, write your instructor bio, add languages you speak, and set your services. A great profile helps learners choose you!</p>
                    <a href="{{ route('instructor.settings.profile') }}" class="btn btn-sm btn-outline-primary">Go to Profile <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>

            {{-- Step 3 --}}
            <div class="d-flex gap-3 mb-4">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;color:#333;">3</div>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Add Your Vehicle</h6>
                    <p class="text-muted small mb-1">Enter your vehicle details (make, model, year, safety rating) and upload a photo of your car. Learners like to know what they'll be driving!</p>
                    <a href="{{ route('instructor.settings.vehicle') }}" class="btn btn-sm btn-outline-primary">Go to Vehicle <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>

            {{-- Step 4 --}}
            <div class="d-flex gap-3 mb-4">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;color:#333;">4</div>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Set Your Service Area</h6>
                    <p class="text-muted small mb-1">Select the suburbs where you offer driving lessons. You can add multiple suburbs to maximise your reach.</p>
                    <a href="{{ route('instructor.settings.service-area') }}" class="btn btn-sm btn-outline-primary">Go to Service Area <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>

            {{-- Step 5 --}}
            <div class="d-flex gap-3 mb-4">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;color:#333;">5</div>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Set Your Opening Hours</h6>
                    <p class="text-muted small mb-1">Define your weekly availability. Learners can only book during your open hours. You can set different hours for each day.</p>
                    <a href="{{ route('instructor.settings.opening-hours') }}" class="btn btn-sm btn-outline-primary">Go to Opening Hours <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>

            {{-- Step 6 --}}
            <div class="d-flex gap-3 mb-4">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;color:#333;">6</div>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Set Your Pricing</h6>
                    <p class="text-muted small mb-1">Set your lesson price, duration, and optionally a test package price. Competitive pricing helps attract more learners.</p>
                    <a href="{{ route('instructor.settings.pricing') }}" class="btn btn-sm btn-outline-primary">Go to Pricing <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>

            {{-- Step 7 --}}
            <div class="d-flex gap-3 mb-4">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;color:#333;">7</div>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Upload Your Documents</h6>
                    <p class="text-muted small mb-1">Upload your Driver's Licence, Instructor Licence, and Working with Children Check (WWCC). These are required for verification.</p>
                    <a href="{{ route('instructor.settings.documents') }}" class="btn btn-sm btn-outline-primary">Go to Documents <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>

            {{-- Step 8 --}}
            <div class="d-flex gap-3 mb-4">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;color:#333;">8</div>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Add Your Banking Details</h6>
                    <p class="text-muted small mb-1">Enter your business details (ABN, GST) and bank account information to receive payouts from bookings.</p>
                    <a href="{{ route('instructor.settings.banking') }}" class="btn btn-sm btn-outline-primary">Go to Banking <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="bg-light rounded p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-lightbulb me-2 text-warning"></i>Tips for Success</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                        <div class="small"><strong>Complete your profile fully</strong> — Instructors with complete profiles get 3x more bookings.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                        <div class="small"><strong>Upload a professional photo</strong> — A clear, friendly photo builds trust with potential learners.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                        <div class="small"><strong>Set wide opening hours</strong> — More availability means more booking opportunities.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                        <div class="small"><strong>Respond quickly to bookings</strong> — Fast response times lead to better reviews.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                        <div class="small"><strong>Add multiple service areas</strong> — Cover more suburbs to reach more learners in your region.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                        <div class="small"><strong>Keep documents up to date</strong> — Expired documents will pause your profile visibility.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <p class="text-muted small">Need help? <a href="{{ route('contact') }}">Contact our support team</a></p>
        </div>
    </div>
</div>
@endsection
