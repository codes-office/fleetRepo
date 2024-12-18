@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item "><a href="{{ route('bookings.index') }}">@lang('menu.bookings')</a></li>
    <li class="breadcrumb-item active">Merge Bookings</li>
@endsection

@section('content')
<div class="container">
    <h2>Merge Bookings</h2>

    <!-- Time Slot Selection -->
    <form action="{{ route('merge-bookings') }}" method="GET">

    <div class="form-group">
        <label for="date">Select Date:</label>
        <input type="date" id="date" name="date" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="start_time">Start Time:</label>
        <select id="start_time" name="start_time" class="form-control" required>
            @foreach ($timeSlots as $slot)
                <option value="{{ $slot['start'] }}">{{ $slot['start'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="end_time">End Time:</label>
        <select id="end_time" name="end_time" class="form-control" required>
            @foreach ($timeSlots as $slot)
                <option value="{{ $slot['end'] }}">{{ $slot['end'] }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
</form>

    <!-- Display Bookings -->
    @if ($bookings->isEmpty())
    <p>No bookings found for the selected time slot.</p>
@else
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>#</th>
                <th>Pickup Address</th>
                <th>Pickup Time</th>
                <th>Customer Name</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bookings as $booking)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $booking->pickup_address }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->pickup_time)->format('H:i') }}</td>
                    <td>{{ $booking->customer_name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

</div>
@endsection
