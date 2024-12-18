@extends('layouts.app')
@section('extra_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="{{ route('booking-quotation.index') }}">@lang('fleet.booking_quotes')</a></li>
    <li class="breadcrumb-item active">@lang('fleet.approve') @lang('fleet.bookingQuote')</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        @lang('fleet.approve') @lang('fleet.bookingQuote')
                    </h3>
                </div>

                <div class="card-body">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {!! Form::open(['url' => 'admin/add-booking', 'method' => 'POST']) !!}

                    {!! Form::hidden('status', 0) !!}
                    {!! Form::hidden('id', $data->id) !!}
                    {!! Form::hidden('user_id', Auth::id()) !!}
                    {!! Form::hidden('customer_id', $data->customer_id) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('customer_id', __('fleet.selectCustomer'), ['class' => 'form-label']) !!}
                                <select id="customer_id" name="customer_id" class="form-control" disabled>
                                    <option selected value="{{ $data->customer_id }}">{{ $data->customer['name'] }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('pickup', __('fleet.pickup'), ['class' => 'form-label']) !!}
                                <div class='input-group mb-3 date'>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <span class="fa fa-calendar"></span></span>
                                    </div>
                                    {!! Form::text('pickup', $data->pickup, ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('dropoff', __('fleet.dropoff'), ['class' => 'form-label']) !!}
                                <div class='input-group date'>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><span class="fa fa-calendar"></span></span>
                                    </div>
                                    {!! Form::text('dropoff', $data->dropoff, ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('vehicle_id', __('fleet.selectVehicle'), ['class' => 'form-label']) !!}
                                <select id="vehicle_id" name="vehicle_id" class="form-control" required>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="">-</option>
                                        <option @if ($vehicle->id == $data->vehicle_id) selected @endif
                                            value="{{ $vehicle->id }}" data-driver="{{ $vehicle->driver_id }}"
                                            data-vehicle-type="{{ strtolower(
                                                str_replace(
                                                    '
                                                              ',
                                                    '',
                                                    $vehicle->types->vehicletype,
                                                ),
                                            ) }}"
                                            data-base-mileage="{{ Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_base_km') }}"
                                            data-base-fare="{{ Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_base_fare') }}"
                                            data-base_km_1="{{ Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_base_km') }}"
                                            data-base_fare_1="{{ Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_base_fare') }}"
                                            data-wait_time_1="{{ Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_base_time') }}"
                                            data-std_fare_1="{{ Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_std_fare') }}"
                                            data-base_km_2="{{ Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_weekend_base_km') }}"
                                            data-base_fare_2="{{ Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_weekend_base_fare') }}"
                                            data-wait_time_2="{{ Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_weekend_wait_time') }}"
                                            data-std_fare_2="{{ Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_weekend_std_fare') }}"
                                            data-base_km_3="{{ Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_night_base_km') }}"
                                            data-base_fare_3="{{ Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_night_base_fare') }}"
                                            data-wait_time_3="{{ Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_night_wait_time') }}"
                                            data-std_fare_3="{{ Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_night_std_fare') }}">
                                            {{ $vehicle->make_name }} -
                                            {{ $vehicle->model_name }} - {{ $vehicle->license_plate }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('driver_id', __('fleet.selectDriver'), ['class' => 'form-label']) !!}

                                <select id="driver_id" name="driver_id" class="form-control" required>
                                    <option value="">-</option>
                                    @foreach ($drivers as $driver)
                                        <option value="{{ $driver->id }}"
                                            @if ($driver->id == $data->driver_id) selected @endif>{{ $driver->name }}
                                            @if ($driver->getMeta('is_active') != 1)
                                                (@lang('fleet.in_active'))
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('travellers', __('fleet.no_travellers'), ['class' => 'form-label']) !!}
                                {!! Form::number('travellers', $data->travellers, ['class' => 'form-control', 'min' => 1]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">@lang('fleet.daytype')</label>
                                <select id="day" name="day" class="form-control vehicles sum" required>
                                    <option value="1" @if ($data->day == 1) selected @endif>Weekdays
                                    </option>
                                    <option value="2" @if ($data->day == 2) selected @endif>Weekend
                                    </option>
                                    <option value="3" @if ($data->day == 3) selected @endif>Night</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">@lang('fleet.trip_mileage') ({{ Hyvikk::get('dis_format') }})</label>
                                {!! Form::number('mileage', $data->mileage, [
                                    'class' => 'form-control
                                              sum',
                                    'min' => 1,
                                    'id' => 'mileage',
                                    'required',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">@lang('fleet.waitingtime')</label>
                                {!! Form::number('waiting_time', $data->waiting_time, [
                                    'class' => 'form-control
                                              sum',
                                    'min' => 0,
                                    'id' => 'waiting_time',
                                    'required',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">@lang('fleet.total') @lang('fleet.amount') </label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ Hyvikk::get('currency') }}</span>
                                    </div>
                                    {!! Form::number('total', $data->total, [
                                        'class' => 'form-control',
                                        'id' => 'total',
                                        'required',
                                        'min' => 0,
                                        'step' => '0.01',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        @php($tax_percent = 0)
                        @if ($data->total_tax_percent != null)
                            @php($tax_percent = $data->total_tax_percent)
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">@lang('fleet.total_tax') (%) </label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text fa fa-percent"></span>
                                    </div>
                                    {!! Form::number('total_tax_percent', $data->total_tax_percent, [
                                        'class' => 'form-control
                                                    sum',
                                        'readonly',
                                        'id' => 'total_tax_charge',
                                        'min' => 0,
                                        'step' => '0.01',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">@lang('fleet.total') @lang('fleet.tax_charge')</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ Hyvikk::get('currency') }}</span>
                                    </div>
                                    {!! Form::number('total_tax_charge_rs', $data->total_tax_charge_rs, [
                                        'class' => 'form-control
                                                    sum',
                                        'readonly',
                                        'id' => 'total_tax_charge_rs',
                                        'min' => 0,
                                        'step' => '0.01',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">@lang('fleet.total') @lang('fleet.amount') </label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ Hyvikk::get('currency') }}</span>
                                    </div>
                                    {!! Form::number('tax_total', $data->tax_total, [
                                        'class' => 'form-control',
                                        'id' => 'tax_total',
                                        'readonly',
                                        'min' => 0,
                                        'step' => '0.01',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (Auth::user()->user_type == 'C')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('d_pickup', __('fleet.pickup_addr'), ['class' => 'form-label']) !!}
                                    <select id="d_pickup" name="d_pickup" class="form-control">
                                        <option value="">-</option>
                                        @foreach ($addresses as $address)
                                            <option value="{{ $address->id }}" data-address="{{ $address->address }}">
                                                {{ $address->address }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('d_dest', __('fleet.dropoff_addr'), ['class' => 'form-label']) !!}
                                    <select id="d_dest" name="d_dest" class="form-control">
                                        <option value="">-</option>
                                        @foreach ($addresses as $address)
                                            <option value="{{ $address->id }}" data-address="{{ $address->address }}">
                                                {{ $address->address }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('pickup_addr', __('fleet.pickup_addr'), ['class' => 'form-label']) !!}
                                {!! Form::text('pickup_addr', $data->pickup_addr, [
                                    'class' => 'form-control',
                                    'required',
                                    'style' => 'height:100px',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('dest_addr', __('fleet.dropoff_addr'), ['class' => 'form-label']) !!}
                                {!! Form::text('dest_addr', $data->dest_addr, [
                                    'class' => 'form-control',
                                    'required',
                                    'style' => 'height:100px',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('note', __('fleet.note'), ['class' => 'form-label']) !!}
                                {!! Form::textarea('note', $data->note, [
                                    'class' => 'form-control',
                                    'placeholder' => __('fleet.book_note'),
                                    'style' => 'height:100px',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        {!! Form::submit(__('fleet.approve'), ['class' => 'btn btn-info']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="{{asset('assets/js/booking_quotation/approve.js')}}"></script>

    @if (Hyvikk::api('google_api') == '1')
        <script>
            function initMap() {
                $('#pickup_addr').attr("placeholder", "");
                $('#dest_addr').attr("placeholder", "");
                // var input = document.getElementById('searchMapInput');
                var pickup_addr = document.getElementById('pickup_addr');
                new google.maps.places.Autocomplete(pickup_addr);

                var dest_addr = document.getElementById('dest_addr');
                new google.maps.places.Autocomplete(dest_addr);

                // autocomplete.addListener('place_changed', function() {
                //     var place = autocomplete.getPlace();
                //     document.getElementById('pickup_addr').innerHTML = place.formatted_address;
                // });
            }
        </script>
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ Hyvikk::api('api_key') }}&libraries=places&callback=initMap"
            async defer></script>
    @endif
@endsection
