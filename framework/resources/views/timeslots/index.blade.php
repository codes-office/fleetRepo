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
    </style>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item "><a href="{{ route('timeslots.index') }}">All Timeslots</a></li>
    <li class="breadcrumb-item active">Timeslots</li>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        Timeslot List
                    </h3>
                </div>

                <div class="card-body">
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
                                <th>Slot</th>
                                <th>Log</th>
                                <th>Active</th>
                                <th>Selected days</th>
                                @if(Auth::user()->user_type == 's') <!-- Check if user_type is 's' -->
                                <th>Created to</th>
                            @endif
                            
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($timeslots as $timeslot)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $timeslot->user->name ?? 'Unknown' }}</td>
                                    <td>
                                        @if($timeslot->from_time && $timeslot->to_time)
                                            {{ \Carbon\Carbon::createFromFormat('H:i', $timeslot->from_time)->format('H:i') }}-
                                            {{ \Carbon\Carbon::createFromFormat('H:i', $timeslot->to_time)->format('H:i') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($timeslot->log) }}</td> 
                                    <td>
                                        @if($timeslot->active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ is_string($timeslot->days_available) ? implode(', ', json_decode($timeslot->days_available)) : implode(', ', $timeslot->days_available ?? []) }}</td>
                                    <td>{{ $timeslot->company->name ?? 'Unknown' }}</td>
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
                                    <td colspan="4" class="text-center">No timeslots available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        
                    </table>

                    <!-- Create new timeslot button -->
                    <a href="{{ route('timeslots.create') }}" class="btn btn-success mt-3">Create Timeslot</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        // Ensure jQuery is loaded and use the following to fade out success messages after 3 seconds
        $(document).ready(function() {
            // Check if a success message is present
            @if(session('success'))
                // Fade out the success message after 3 seconds
                setTimeout(function() {
                    $(".alert-success").fadeOut("slow");
                }, 3000); // 3000ms = 3 seconds
            @endif
        });
    </script>
@endsection
