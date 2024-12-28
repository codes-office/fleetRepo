@extends('layouts.app')

@section('content')
<div class="container">
    <h1>All Timeslots</h1>
    
    <!-- Success message -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Created By</th>
                <th>Pickup Time</th>
                <th>Drop Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($timeslots as $timeslot)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $timeslot->user->name ?? 'Unknown' }}</td>
                    <td>{{ \Carbon\Carbon::parse($timeslot->pickup_time)->format('h:i A') }}</td>
                    <td>{{ \Carbon\Carbon::parse($timeslot->drop_time)->format('h:i A') }}</td>
                    <td>
                        <a href="{{ route('timeslots.edit', $timeslot->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('timeslots.destroy', $timeslot->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No timeslots available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Create new timeslot button -->
    <a href="{{ route('timeslots.create') }}" class="btn btn-primary">Create Timeslot</a>
</div>
@endsection
