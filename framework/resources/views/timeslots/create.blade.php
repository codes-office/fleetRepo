@extends('layouts.app')

@section('extra_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
    <style>
        /* Make the table and text a little bit bigger */
        .table {
            font-size: 15px; /* Slightly increase font size */
            width: 100%; /* Make the table take the full width */
            table-layout: auto; /* Allow table to adjust width based on content */
            margin: 0; /* Remove any outside margin */
        }
        .table th, .table td {
            padding: 12px; /* Slightly increase padding */
        }
        .table th {
            text-align: center; /* Center align table headers */
        }
        .table td {
            text-align: center; /* Center align table data */
        }

        /* Make time inputs bigger */
        .time-input {
            width: 200px; /* Increase width of the input box */
            font-size: 21px; /* Increase font size for better visibility */
            padding: 10px; /* Add padding for a bigger clickable area */
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }

        /* Adding a smooth animation for time input */
        .time-input:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 5px rgba(0, 200, 0, 0.5);
        }

        /* Make buttons bigger */
        .btn {
            font-size: 16px; /* Increase font size */
            padding: 10px 20px; /* Increase padding for bigger buttons */
            border-radius: 5px; /* Slightly round the corners */
        }

        /* Create Timeslot Form Styling */
        .timeslot-form-container {
            max-width: 100%; /* Make the form container full-width */
            margin: 0; /* Remove outside margin */
        }

        /* Clock Icon Watermark */
        .timeslot-form-container::after {
            content: '\f017'; /* Unicode for clock icon (Font Awesome) */
            font-family: 'FontAwesome';
            font-size: 50px;
            color: rgba(0, 0, 0, 0.1); /* Light grey color */
            position: absolute;
            top: 50%;
            right: 10px; /* Position the clock on the right */
            transform: translateY(-50%);
            pointer-events: none; /* Prevent interaction with the watermark */
        }

        /* Add some spacing for the form fields */
        .form-group {
            margin-bottom: 15px;
        }

        /* Styling for checkboxes section */
        .days-checkboxes {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            gap: 10px;
        }

        .days-checkboxes label {
            font-size: 16px;
        }

        /* Styling for the columns */
        .left-column, .right-column {
            padding-right: 30px;
        }

        /* Styling for login and logout radio buttons */
        .login-logout-radio {
            display: flex;
            flex-direction: row;
            gap: 20px;
        }

        .login-logout-radio label {
            font-size: 16px;
        }
    </style>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('timeslots.index') }}">All Timeslots</a></li>
    <li class="breadcrumb-item active">Create Timeslot</li>
@endsection

@section('content')
<head>
    <!-- Add Font Awesome for the clock icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<div class="container">
    <div class="row">
        <div class="col-md-12"> <!-- Make this column full width -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        Create Timeslot
                    </h3>
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

                    <form action="{{ route('timeslots.store') }}" method="POST" class="timeslot-form-container">
                        @csrf
                        {{-- <!-- Hidden user_id field -->
                        <input type="hidden" name="user_id" value="{{ $user_id }}"> --}}

                        <div class="row">
                            <!-- Left Column: Days Checkboxes -->
                            <div class="col-md-6 left-column">
                                <div class="form-group">
                                    <label>Select Days</label>
                                    <div class="days-checkboxes">
                                        <label><input type="checkbox" name="days_available[]" value="Monday" {{ in_array('monday', old('days', [])) ? 'checked' : '' }}> Monday</label>
                                        <label><input type="checkbox" name="days_available[]" value="Tuesday" {{ in_array('tuesday', old('days', [])) ? 'checked' : '' }}> Tuesday</label>
                                        <label><input type="checkbox" name="days_available[]" value="Wednesday" {{ in_array('wednesday', old('days', [])) ? 'checked' : '' }}> Wednesday</label>
                                        <label><input type="checkbox" name="days_available[]" value="Thursday" {{ in_array('thursday', old('days', [])) ? 'checked' : '' }}> Thursday</label>
                                        <label><input type="checkbox" name="days_available[]" value="Friday" {{ in_array('friday', old('days', [])) ? 'checked' : '' }}> Friday</label>
                                        <label><input type="checkbox" name="days_available[]" value="Saturday" {{ in_array('saturday', old('days', [])) ? 'checked' : '' }}> Saturday</label>
                                        <label><input type="checkbox" name="days_available[]" value="Sunday" {{ in_array('sunday', old('days', [])) ? 'checked' : '' }}> Sunday</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Other Form Fields -->
                            <div class="col-md-6 right-column">
                                <div class="form-group">
                                    <label for="timeslot_Active">Active</label>
                                    <input type="hidden" name="Active" value="0"> <!-- Hidden input ensures 0 is sent when unchecked -->
                                    <input type="checkbox" name="Active" id="timeslot_Active" value="1">
                                </div>

                                <div class="form-group">
                                    <label for="from_time">From</label>
                                    <input type="time" name="from_time" id="from_time" class="form-control time-input" value="{{ old('pickup_time') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="to_time">To</label>
                                    <input type="time" name="to_time" id="to_time" class="form-control time-input" value="{{ old('drop_time') }}" required>
                                </div>

                                @if (Auth::user()->user_type == 'S')
                                <div class="form-group">
                                    <label for="company_id">Select Customer</label>
                                    <select name="company_id" id="company_id" class="form-control" required>
                                        <option value="">-- Select a Customer --</option>
                                        @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                <!-- Login/Logout Radio Buttons -->
                                <div class="form-group">
                                    <label>Select Login or Logout</label>
                                    <div class="login-logout-radio">
                                        <label><input type="radio" name="log" value="Login" {{ old('login_logout') == 'login' ? 'checked' : '' }}> Login</label>
                                        <label><input type="radio" name="log" value="Logout" {{ old('login_logout') == 'logout' ? 'checked' : '' }}> Logout</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="time-error" class="error-message"></div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success mt-3">Create Timeslot</button>
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
        // Add time validation for pickup time > drop time
        const form = document.querySelector('form');
        const pickupTime = document.getElementById('pickup_time');
        const dropTime = document.getElementById('drop_time');
        const timeError = document.getElementById('time-error');

        form.addEventListener('submit', function(event) {
            const pickup = pickupTime.value;
            const drop = dropTime.value;

            if (pickup && drop && pickup >= drop) {
                event.preventDefault(); // Prevent form submission
                timeError.textContent = 'Pickup time must be earlier than drop time.';
            } else {
                timeError.textContent = ''; // Clear error message if valid
            }
        });

        // Trigger time selection window when clicking on the input field
        document.querySelectorAll('.time-input').forEach(input => {
            input.addEventListener('click', function() {
                this.showPicker();  // Trigger the time picker directly when clicked
            });
        });
    </script>
@endsection