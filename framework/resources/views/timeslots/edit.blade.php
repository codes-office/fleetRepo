@extends('layouts.app')

@section('extra_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
    <style>
        /* Styling similar to Create Blade */
        .table {
            font-size: 15px;
            width: 100%;
            table-layout: auto;
        }
        .table th, .table td {
            padding: 12px;
            text-align: center;
        }
        .time-input {
            width: 200px;
            font-size: 21px;
            padding: 10px;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
        .btn {
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .timeslot-form-container {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            padding-right: 50px;
        }
        .timeslot-form-container::after {
            content: '\f017';
            font-family: 'FontAwesome';
            font-size: 50px;
            color: rgba(0, 0, 0, 0.1);
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            pointer-events: none;
        }
        .form-group {
            margin-bottom: 15px;
        }
    </style>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('timeslots.index') }}">Timeslots</a></li>
    <li class="breadcrumb-item active">Edit Timeslot</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Edit Timeslot</h3>
                </div>

                <div class="card-body">
                    <!-- Display errors if any -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('timeslots.update', $timeslot->id) }}" method="POST" class="timeslot-form-container">
                        @csrf
                        @method('PUT')

                        <!-- Days Checkboxes -->
                        <div class="form-group">
                            <label>Select Days</label>
                            <div class="days-checkboxes">
                                @php
                                    $selectedDays = old('days_available', $timeslot->days_available ?? []);
                                @endphp
                                <label><input type="checkbox" name="days_available[]" value="Monday" {{ in_array('Monday', $selectedDays) ? 'checked' : '' }}> Monday</label>
                                <label><input type="checkbox" name="days_available[]" value="Tuesday" {{ in_array('Tuesday', $selectedDays) ? 'checked' : '' }}> Tuesday</label>
                                <label><input type="checkbox" name="days_available[]" value="Wednesday" {{ in_array('Wednesday', $selectedDays) ? 'checked' : '' }}> Wednesday</label>
                                <label><input type="checkbox" name="days_available[]" value="Thursday" {{ in_array('Thursday', $selectedDays) ? 'checked' : '' }}> Thursday</label>
                                <label><input type="checkbox" name="days_available[]" value="Friday" {{ in_array('Friday', $selectedDays) ? 'checked' : '' }}> Friday</label>
                                <label><input type="checkbox" name="days_available[]" value="Saturday" {{ in_array('Saturday', $selectedDays) ? 'checked' : '' }}> Saturday</label>
                                <label><input type="checkbox" name="days_available[]" value="Sunday" {{ in_array('Sunday', $selectedDays) ? 'checked' : '' }}> Sunday</label>
                            </div>
                        </div>

                        <!-- Active Checkbox -->
                        <div class="form-group">
                            <label for="Active">Active</label>
                            <input type="hidden" name="Active" value="0"> <!-- Hidden input ensures 0 is sent when unchecked -->
                            <input type="checkbox" name="Active" id="Active" value="1" {{ old('Active', $timeslot->active) == 1 ? 'checked' : '' }}>
                        </div>
                        

                        <!-- Pickup Time -->
                        <div class="form-group">
                            <label for="from_time">From</label>
                            <input type="time" name="from_time" id="from_time" class="form-control time-input" value="{{ old('from_time', $timeslot->from_time) }}" required>
                        </div>

                        <!-- Drop Time -->
                        <div class="form-group">
                            <label for="to_time">To</label>
                            <input type="time" name="to_time" id="to_time" class="form-control time-input" value="{{ old('to_time', $timeslot->to_time) }}" required>
                        </div>

                        <!-- Login/Logout Radio Buttons -->
                        <div class="form-group">
                            <label>Select Login or Logout</label>
                            <div class="login-logout-radio">
                                <label>
                                    <input type="radio" name="log" value="login" {{ old('log', $timeslot->log) == 'login' ? 'checked' : '' }}> Login
                                </label>
                                <label>
                                    <input type="radio" name="log" value="logout" {{ old('log', $timeslot->log) == 'logout' ? 'checked' : '' }}> Logout
                                </label>
                            </div>
                        </div>
                        

                        <div id="time-error" class="error-message"></div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success mt-3">Update Timeslot</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        const form = document.querySelector('form');
        const pickupTime = document.getElementById('from_time');
        const dropTime = document.getElementById('to_time');
        const timeError = document.getElementById('time-error');

        form.addEventListener('submit', function(event) {
            const pickup = pickupTime.value;
            const drop = dropTime.value;

            if (pickup && drop && pickup >= drop) {
                event.preventDefault();
                timeError.textContent = 'Pickup time must be earlier than drop time.';
            } else {
                timeError.textContent = '';
            }
        });
    </script>
@endsection
