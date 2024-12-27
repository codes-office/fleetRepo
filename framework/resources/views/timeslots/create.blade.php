@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Timeslot</h1>
    <form action="{{ route('timeslots.store') }}" method="POST">
        @csrf
        <!-- Input for Pickup Time -->
        <div class="mb-3">
            <label for="pickup_time" class="form-label">Pickup Time</label>
            <input 
                type="time" 
                name="pickup_time" 
                id="pickup_time" 
                class="form-control" 
                placeholder="Select Pickup Time" 
                value="{{ old('pickup_time') }}" 
                required>
        </div>

        <!-- Input for Drop Time -->
        <div class="mb-3">
            <label for="drop_time" class="form-label">Drop Time</label>
            <input 
                type="time" 
                name="drop_time" 
                id="drop_time" 
                class="form-control" 
                placeholder="Select Drop Time" 
                value="{{ old('drop_time') }}" 
                required>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">
            Create Timeslot
        </button>
    </form>
</div>
@endsection
