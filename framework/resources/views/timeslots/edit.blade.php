@extends('layouts.app')

@section('extra_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
    <style>
        /* Make the table and text a little bit bigger */
        .table {
            font-size: 15px; /* Slightly increase font size */
            width: 100%; /* Make the table take the full width */
            table-layout: auto; /* Allow table to adjust width based on content */
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
            max-width: 600px; /* Make the form container smaller */
            margin: 0 auto; /* Center the form horizontally */
            position: relative; /* For positioning the watermark */
            padding-right: 50px; /* Add some space on the right for watermark */
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
                    <h3 class="card-title">
                        Edit Timeslot
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

                    <form action="{{ route('timeslots.update', $timeslot->id) }}" method="POST" class="timeslot-form-container">
                        @csrf
                        @method('PUT') <!-- Specify PUT method for updates -->

                        <div class="form-group">
                            <label for="pickup_time">Pickup Time</label>
                            <input type="time" name="pickup_time" id="pickup_time" class="form-control time-input" value="{{ old('pickup_time', $timeslot->pickup_time) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="drop_time">Drop Time</label>
                            <input type="time" name="drop_time" id="drop_time" class="form-control time-input" value="{{ old('drop_time', $timeslot->drop_time) }}" required>
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
