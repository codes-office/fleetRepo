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
    <li class="breadcrumb-item active">timeslots</li>
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
                    <a href="{{ route('timeslots.create') }}" class="btn btn-success mt-3">Create Timeslot</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        var datet = "{{date('Y-m-d H:i:s')}}";
    </script>
@endsection
