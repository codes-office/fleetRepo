@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Driver</h2>
    
    <form action="{{ route('drivers.update', $driver->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $driver->name }}" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <select name="phone_code" class="form-control" required>
                @foreach($phone_code as $code => $country)
                    <option value="{{ $code }}" {{ $driver->phone_code == $code ? 'selected' : '' }}>{{ $country }} ({{ $code }})</option>
                @endforeach
            </select>
            <input type="text" name="phone" id="phone" class="form-control mt-2" value="{{ $driver->phone }}" required>
        </div>

        <div class="form-group">
            <label for="vendor">Vendor</label>
            <select name="vendor_id" class="form-control" required>
                @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}" {{ $driver->vendor_id == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="identification_file">Identification File</label>
            <input type="file" name="identification_file" class="form-control">
            @if($driver->identification_file)
                <a href="{{ asset('storage/' . $driver->identification_file) }}" target="_blank">View File</a>
            @endif
        </div>

        <div class="form-group">
            <label for="training_file">Training File</label>
            <input type="file" name="training_file" class="form-control">
            @if($driver->training_file)
                <a href="{{ asset('storage/' . $driver->training_file) }}" target="_blank">View File</a>
            @endif
        </div>

        <div class="form-group">
            <label for="eye_test_file">Eye Test File</label>
            <input type="file" name="eye_test_file" class="form-control">
            @if($driver->eye_test_file)
                <a href="{{ asset('storage/' . $driver->eye_test_file) }}" target="_blank">View File</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
