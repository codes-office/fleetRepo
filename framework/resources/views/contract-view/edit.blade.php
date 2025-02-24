@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h1 class="h4 mb-0">Edit Vehicle Contract</h1>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @elseif(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('contract-route.update', $contract->id) }}" method="POST">
                            @csrf
                            @method('PUT') <!-- Required for update request -->

                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contractType" class="form-label">Contract Type</label>
                                        <input type="text" class="form-control @error('contractType') is-invalid @enderror" id="contractType" name="contractType" value="{{ old('contractType', $contract->contractType) }}" required>
                                        @error('contractType')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="contractTypePackage" class="form-label">Contract Type Package</label>
                                        <input type="text" class="form-control" id="contractTypePackage" name="contractTypePackage" value="PACKAGE" readonly required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="shortCode" class="form-label">Short Code</label>
                                        <input type="text" class="form-control @error('shortCode') is-invalid @enderror" id="shortCode" name="shortCode" value="{{ old('shortCode', $contract->shortCode) }}" required>
                                        @error('shortCode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>



                                    <div class="mb-3">
                                    <label for="numberOfDuties" class="form-label">Number of Duties</label>
                                    <input type="number" class="form-control @error('numberOfDuties') is-invalid @enderror" id="numberOfDuties" name="numberOfDuties" value="{{ old('numberOfDuties', $contract->numberOfDuties ) }}" required>
                                    @error('numberOfDuties')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="allottedKmPerMonth" class="form-label">Allotted KM per Month</label>
                                    <input type="number" class="form-control @error('allottedKmPerMonth') is-invalid @enderror" id="allottedKmPerMonth" name="allottedKmPerMonth" value="{{ old('allottedKmPerMonth', $contract->allottedKmPerMonth) }}" required>
                                    @error('allottedKmPerMonth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="minHoursPerDay" class="form-label">Minimum Hours per Day</label>
                                    <input type="number" class="form-control @error('minHoursPerDay') is-invalid @enderror" id="minHoursPerDay" name="minHoursPerDay" value="{{ old('minHoursPerDay',$contract->minHoursPerDay) }}" required>
                                    @error('minHoursPerDay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="packageCostPerMonth" class="form-label">Package Cost per Month</label>
                                    <input type="number" class="form-control @error('packageCostPerMonth') is-invalid @enderror" id="packageCostPerMonth" name="packageCostPerMonth" value="{{ old('packageCostPerMonth',$contract->packageCostPerMonth) }}" required>
                                    @error('packageCostPerMonth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="pricingForExtraDuty" class="form-label">Pricing for Extra Duty</label>
                                    <input type="number" class="form-control @error('pricingForExtraDuty') is-invalid @enderror" id="pricingForExtraDuty" name="pricingForExtraDuty" value="{{ old('pricingForExtraDuty',$contract->pricingForExtraDuty) }}" required>
                                    @error('pricingForExtraDuty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="costPerKmAfterMinKm" class="form-label">Cost per KM after Min KM (Rs)</label>
                                    <input type="number" class="form-control @error('costPerKmAfterMinKm') is-invalid @enderror" id="costPerKmAfterMinKm" name="costPerKmAfterMinKm" value="{{ old('costPerKmAfterMinKm',$contract->costPerKmAfterMinKm) }}" required>
                                    @error('costPerKmAfterMinKm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="costPerHourAfterMinHours" class="form-label">Cost per Hour after Min Hours per Day</label>
                                    <input type="number" class="form-control @error('costPerHourAfterMinHours') is-invalid @enderror" id="costPerHourAfterMinHours" name="costPerHourAfterMinHours" value="{{ old('costPerHourAfterMinHours',$contract->costPerHourAfterMinHours) }}" required>
                                    @error('costPerHourAfterMinHours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="garageKmOnReporting" class="form-label">Garage KM on Reporting to Duty</label>
                                    <input type="number" class="form-control @error('garageKmOnReporting') is-invalid @enderror" id="garageKmOnReporting" name="garageKmOnReporting" value="{{ old('garageKmOnReporting',$contract->garageKmOnReporting) }}" required>
                                    @error('garageKmOnReporting')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="garageHoursPerDay" class="form-label">Garage Hours per Day</label>
                                    <input type="number" class="form-control @error('garageHoursPerDay') is-invalid @enderror" id="garageHoursPerDay" name="garageHoursPerDay" value="{{ old('garageHoursPerDay',$contract->garageHoursPerDay) }}" required>
                                    @error('garageHoursPerDay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="baseDieselPrice" class="form-label">Base Diesel Price</label>
                                    <input type="number" class="form-control @error('baseDieselPrice') is-invalid @enderror" id="baseDieselPrice" name="baseDieselPrice" value="{{ old('baseDieselPrice',$contract->baseDieselPrice) }}" required>
                                    @error('baseDieselPrice')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="mileage" class="form-label">Mileage</label>
                                    <input type="number" class="form-control @error('mileage') is-invalid @enderror" id="mileage" name="mileage" value="{{ old('mileage',$contract->mileage) }}" required>
                                    @error('mileage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="seatingCapacity" class="form-label">Seating Capacity</label>
                                    <input type="number" class="form-control @error('seatingCapacity') is-invalid @enderror" id="seatingCapacity" name="seatingCapacity" value="{{ old('seatingCapacity',$contract->seatingCapacity) }}" required>
                                    @error('seatingCapacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="acPriceAdjustmentPerKm" class="form-label">AC Price Adjustment per KM</label>
                                    <input type="number" class="form-control @error('acPriceAdjustmentPerKm') is-invalid @enderror" id="acPriceAdjustmentPerKm" name="acPriceAdjustmentPerKm" value="{{ old('acPriceAdjustmentPerKm',$contract->acPriceAdjustmentPerKm) }}" required>
                                    @error('acPriceAdjustmentPerKm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Full Width Fields -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="minTripsPerMonth" class="form-label">Minimum Trips per Month</label>
                                    <input type="number" class="form-control @error('minTripsPerMonth') is-invalid @enderror" id="minTripsPerMonth" name="minTripsPerMonth" value="{{ old('minTripsPerMonth',$contract->minTripsPerMonth) }}" required>
                                    @error('minTripsPerMonth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        

               <!-- ////////////////////////////////////////////////////////////// -->


                                    <div class="mb-3">
                                        <label for="Vechiletype" class="form-label">Vehicle Type</label>
                                        <select class="form-control @error('Vechiletype') is-invalid @enderror" id="Vechiletype" name="Vechiletype" required>
                                            <option value="">Select Vehicle Type</option>
                                            @foreach($vehicleTypes as $type)
                                                <option value="{{ $type->vehicletype }}" {{ old('Vechiletype', $contract->Vechiletype) == $type->vehicletype ? 'selected' : '' }}>
                                                    {{ $type->vehicletype }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('Vechiletype')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="company_name" class="form-label">Company Name</label>
                                        <select class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" required>
                                            <option value="">Select a Company</option>
                                            @foreach ($customers as $key => $value)
                                                <option value="{{ $key }}" {{ old('company_name', $contract->company_name) == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Update Contract</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
