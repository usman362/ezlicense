@extends('layouts.frontend')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <h1>{{ $instructorProfile->user->name }}</h1>
            <p class="text-muted">{{ ucfirst($instructorProfile->transmission) }} · ${{ number_format($instructorProfile->lesson_price, 0) }}/lesson
                @if($instructorProfile->test_package_price)
                    · Test package ${{ number_format($instructorProfile->test_package_price, 0) }}
                @endif
            </p>
            @if($instructorProfile->bio)
                <p>{{ $instructorProfile->bio }}</p>
            @endif
            <p class="small">Vehicle: {{ $instructorProfile->vehicle_make }} {{ $instructorProfile->vehicle_model }} {{ $instructorProfile->vehicle_year }} {{ $instructorProfile->vehicle_safety_rating }}</p>
            <p class="small">Service areas: {{ $instructorProfile->serviceAreas->pluck('name')->join(', ') }}</p>
            @auth
                @if(auth()->user()->isLearner())
                    <p class="mt-3">
                        <a href="{{ route('learner.bookings.new', ['instructor_profile_id' => $instructorProfile->id]) }}" class="btn btn-warning fw-bold">Book a lesson</a>
                    </p>
                @endif
            @else
                <p class="mt-3">
                    <a href="{{ route('login') }}" class="btn btn-warning fw-bold">Log in to book</a>
                </p>
            @endauth
        </div>
    </div>
    <p class="mt-3"><a href="{{ route('find-instructor') }}">&larr; Find another instructor</a></p>
</div>
@endsection
