@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Vehicle Contract</h1>

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

        <!-- Form to edit the vehicle contract -->
        <form action="{{ route('vehiclecontracts.update', $contract->vec_id) }}" method="POST">
            @csrf
            @method('PUT') <!-- HTTP PUT method for update -->

            <div class="form-group">
                <label for="Name">Contract Name</label>
                <input 
                    type="text" 
                    class="form-control @error('Name') is-invalid @enderror" 
                    id="Name" 
                    name="Name" 
                    value="{{ old('Name', $contract->Name) }}" 
                    required>
                @error('Name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="Vechiletype">Vehicle Type</label>
                <select 
                    class="form-control @error('Vechiletype') is-invalid @enderror" 
                    id="Vechiletype" 
                    name="Vechiletype" 
                    required>
                    <option value="">Select Vehicle Type</option>
                    @foreach($vehicleTypes as $type)
                        <option value="{{ $type->vehicletype }}" 
                            {{ old('Vechiletype', $contract->Vechiletype) == $type->vehicletype ? 'selected' : '' }}>
                            {{ $type->vehicletype }}
                        </option>
                    @endforeach
                </select>
                @error('Vechiletype')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

         @if(Auth::user()->user_type === 'S' && isset($customers) && !empty($customers))
    <div class="form-group">
        {!! Form::label('company_id', 'Company Name') !!}
        {!! Form::select(
            'company_id', 
            $customers, // Directly use the prepared data
            old('company_id', $contract->company_id), 
            ['class' => 'form-control', 'placeholder' => 'Select a Company']
        ) !!}
    </div>
@endif



            

            <button type="submit" class="btn btn-primary">Update Contract</button>
        </form>
    </div>
@endsection
