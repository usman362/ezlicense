@extends('layouts.frontend')

@section('title', 'Availability')

@section('content')
@php $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; @endphp
<div class="container py-5" style="max-width: 800px;">
    <h1 class="h3 fw-bold mb-2">Weekly Availability</h1>
    <p class="text-muted mb-4">Add the recurring time slots when you accept bookings.</p>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <form method="POST" action="{{ route('service-provider.availability.slots.store') }}" class="card shadow-sm mb-4">
        @csrf
        <div class="card-body d-flex flex-wrap gap-2">
            <select name="day_of_week" class="form-select" style="max-width:160px;">
                @foreach($days as $i => $name)
                    <option value="{{ $i }}">{{ $name }}</option>
                @endforeach
            </select>
            <input type="time" name="start_time" required class="form-control" style="max-width:140px;">
            <input type="time" name="end_time" required class="form-control" style="max-width:140px;">
            <button class="btn btn-primary">Add slot</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="list-group list-group-flush">
            @forelse($provider->availabilitySlots->sortBy('day_of_week') as $slot)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $days[$slot->day_of_week] }} &middot; {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i a') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i a') }}</span>
                    <form method="POST" action="{{ route('service-provider.availability.slots.destroy', $slot->id) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Remove</button>
                    </form>
                </div>
            @empty
                <p class="p-4 text-muted text-center mb-0">No availability slots yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
