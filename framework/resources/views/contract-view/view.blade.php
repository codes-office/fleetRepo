@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Contract Details</h2>
    <table class="table table-bordered">
        <tr><th>Contract Type</th><td>{{ $contract->contractType }}</td></tr>
        <tr><th>Short Code</th><td>{{ $contract->shortCode }}</td></tr>
        <tr><th>Package Cost Per Month</th><td>{{ $contract->packageCostPerMonth }}</td></tr>
        <tr><th>Seating Capacity</th><td>{{ $contract->seatingCapacity }}</td></tr>
        <tr><th>Company Name</th><td>{{ $contract->company_name }}</td></tr>
        <tr><th>Vehicle Type</th><td>{{ $contract->Vechiletype }}</td></tr>
        <tr><th>Base Diesel Price</th><td>{{ $contract->baseDieselPrice }}</td></tr>
        <tr><th>Garage Km on Reporting</th><td>{{ $contract->garageKmOnReporting }}</td></tr>
        <tr><th>Updated At</th><td>{{ $contract->updated_at }}</td></tr>
    </table>
    <a href="{{ route('contracts.index') }}" class="btn btn-primary">Back</a>
</div>
@endsection
