@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Timeslot</h1>
    <form action="{{ route('timeslots.update', $timeslot->id) }}" method="POST">
        @csrf
        @method('PUT') <!-- Specify PUT method for updates -->
        
        <div class="mb-3">
            <label for="pickup_time" class="form-label">Pickup Time</label>
            <input 
                type="time" 
                name="pickup_time" 
                id="pickup_time" 
                class="form-control" 
                value="{{ old('pickup_time', $timeslot->pickup_time) }}" 
                required>
        </div>

        <div class="mb-3">
            <label for="drop_time" class="form-label">Drop Time</label>
            <input 
                type="time" 
                name="drop_time" 
                id="drop_time" 
                class="form-control" 
                value="{{ old('drop_time', $timeslot->drop_time) }}" 
                required>
        </div>

        <button type="submit" class="btn btn-primary">Update Timeslot</button>
    </form>
</div>
@endsection
