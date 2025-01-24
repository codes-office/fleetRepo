@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Create Vehicle Contract</h1>

        <!-- Display success or error messages -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form to create a new vehicle contract -->
        <form action="{{ route('vehiclecontracts.store') }}" method="POST">
            @csrf <!-- CSRF protection -->

            <div class="form-group">
                <label for="Name">Contract Name</label>
                <input type="text" class="form-control @error('Name') is-invalid @enderror" id="Name" name="Name" value="{{ old('Name') }}" required>
                @error('Name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

                    <div class="form-group">
            <label for="Vechiletype">Vehicle Type</label>
            <select class="form-control @error('Vechiletype') is-invalid @enderror" id="Vechiletype" name="Vechiletype" required>
                <option value="">Select Vehicle Type</option>
                @foreach($vehicleTypes as $type)
                    <option value="{{ $type->vehicletype }}" {{ old('Vechiletype') == $type->vehicletype ? 'selected' : '' }}>
                        {{ $type->vehicletype }}
                    </option>
                @endforeach
            </select>
            @error('Vechiletype')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


                    <div class="form-group">
            <label for="company_name">Company Name</label>
            <select class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" required>
                <option value="">Select a Company</option>
                @foreach ($customers as $key => $value)
                    <option value="{{ $key }}" {{ old('company_name') == $key ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
            @error('company_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

            <button type="submit" class="btn btn-primary">Create Contract</button>
        </form>
    </div>
@endsection
