@extends('layouts.app')

@section('extra_css')
<style type="text/css">
  .nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: #00a65a !important;
  }

  .switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
  }

  .switch input {
    display: none;
  }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
  }

  input:checked+.slider {
    background-color: #2196F3;
  }

  input:focus+.slider {
    box-shadow: 0 0 1px #2196F3;
  }

  input:checked+.slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
  }

  .slider.round {
    border-radius: 34px;
  }

  .slider.round:before {
    border-radius: 50%;
  }

  .custom .nav-link.active {
    background-color: #21bc6c !important;
  }
</style>
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">
@endsection

@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('vehicles.index')}}">@lang('fleet.vehicles')</a></li>
<li class="breadcrumb-item active">@lang('fleet.editVehicle')</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    @if (count($errors) > 0)
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.editVehicle')</h3>
      </div>

      <div class="card-body">
        <div class="nav-tabs-custom">
          <ul class="nav nav-pills custom">
            <li class="nav-item"><a class="nav-link active" href="#personal-info" data-toggle="tab">@lang('fleet.personal_info')</a></li>
            <li class="nav-item"><a class="nav-link" href="#contracts" data-toggle="tab">@lang('Contracts')</a></li>
            <li class="nav-item"><a class="nav-link" href="#driver" data-toggle="tab">@lang('fleet.driver')</a></li>
          </ul>
        </div>
        <div class="tab-content">
          <!-- Personal Information Tab -->
          <div class="tab-pane active" id="personal-info">
            {!! Form::open(['route' => ['vehicles.update', $vehicle->id], 'files'=>true, 'method'=>'put','class'=>'form-horizontal','id'=>'accountForm']) !!}
            {!! Form::hidden('user_id', Auth::user()->id) !!}

            <div class="row">
              <!-- Left Column -->
              <div class="col-md-6">
                <!-- Vendor -->
                <div class="form-group">
                  {!! Form::label('vendor_id', __('fleet.vendor'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-7">
                    <select name="vendor_id" class="form-control" required id="vendor_id">
                      <option value="">Select Vendor</option>
                      @foreach($vendors as $vendor)
                      <option value="{{$vendor->id}}" {{ $vehicle->vendor_id == $vendor->id ? 'selected' : '' }}>{{$vendor->name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <!-- Registration Number -->
                <div class="form-group">
                  {!! Form::label('registration_no', __('Registration Number'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-7">
                    {!! Form::text('registration_no', $vehicle->registration_no, [
                        'class' => 'form-control',
                        'required',
                        'placeholder' => 'KA-12-EC-1123',
                        'id' => 'registration_no',
                        'maxlength' => '13'
                    ]) !!}
                    <small id="reg_error" style="color: red; display: none;">Invalid Format! Use KA-12-EC-1123</small>
                  </div>
                </div>

                <!-- Status -->
                <div class="form-group">
                  {!! Form::label('status', __('Status'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-7">
                    {!! Form::select('status', ['active' => 'Active', 'inactive' => 'Inactive'], $vehicle->status, ['class' => 'form-control', 'required', 'id' => 'status']) !!}
                  </div>
                </div>

                <!-- Inactive Reason (Hidden initially) -->
                <div class="form-group" id="inactive_reason_div" style="display: {{ $vehicle->status == 'inactive' ? 'block' : 'none' }};">
                  {!! Form::label('inactive_reason', __('Inactive Reason'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-7">
                    {!! Form::text('inactive_reason', $vehicle->inactive_reason, ['class' => 'form-control', 'placeholder' => 'Inactive from any random date']) !!}
                  </div>
                </div>
              </div>

              <!-- Right Column -->
              <div class="col-md-6">
                <!-- Sim Number -->
                <div class="form-group">
                  {!! Form::label('sim_number', __('Sim Number'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-7">
                    {!! Form::text('sim_number', $vehicle->sim_number, ['class' => 'form-control', 'required']) !!}
                  </div>
                </div>

                <!-- Device IMEI -->
                <div class="form-group">
                  {!! Form::label('device_imei', __('Device IMEI'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-7">
                    {!! Form::text('device_imei', $vehicle->device_imei, ['class' => 'form-control', 'placeholder' => 'IE121RT842', 'required']) !!}
                  </div>
                </div>

                <!-- Vehicle ID (Read-Only) -->
                <div class="form-group">
                  {!! Form::label('vehicle_id', __('Vehicle ID'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-7">
                    {!! Form::text('vehicle_id', $vehicle->vehicle_id, ['class' => 'form-control', 'readonly', 'placeholder' => 'Generated Automatically', 'id' => 'vehicle_id']) !!}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Contracts Tab -->
          <div class="tab-pane" id="contracts">
            <div class="row card-body">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('vehicle_type', __('Vehicle Type'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    <select name="vehicle_type" class="form-control" required id="vehicle_type">
                      <option value="">Select Vehicle Type</option>
                      @foreach($types as $type)
                      <option value="{{$type->id}}" {{ $vehicle->vehicle_type == $type->id ? 'selected' : '' }}>{{$type->vehicletype}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('contract', __('Contract'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    <input type="text" name="contract" class="form-control" value="{{ $vehicle->contract }}" readonly>
                    <small class="text-muted">Please select Vendor/Vehicle Type first</small>
                    <span class="text-danger">Please enter select Contract</span>
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('working_time', __('Working Time (min)'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::text('working_time', $vehicle->working_time, ['class' => 'form-control', 'required']) !!}
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('change_contract_from', __('Change Contract From'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    <input type="text" name="change_contract_from" class="form-control" value="{{ $vehicle->change_contract_from }}" readonly>
                  </div>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('start_time', __('Start Time'), ['class' => 'col-xs-5 control-label']) !!}
                <div class="col-xs-6 d-flex">
                  <select name="start_hour" class="form-control" style="width: 45%;">
                    @for ($i = 0; $i < 24; $i++)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $vehicle->start_hour == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                  </select>
                  <span class="mx-2">:</span>
                  <select name="start_minute" class="form-control" style="width: 45%;">
                    @for ($i = 0; $i < 60; $i++)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $vehicle->start_minute == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                  </select>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('send_audit_sms', __('Send Audit SMS'), ['class' => 'col-xs-5 control-label']) !!}
                <div class="col-xs-6">
                  <label>
                    <input type="radio" name="send_audit_sms" value="Driver" {{ $vehicle->send_audit_sms == $vehicle->driver_id ? 'checked' : '' }} onclick="toggleOtherInput(false)"> 
                    Driver
                  </label>
                  <label class="ml-3">
                    <input type="radio" name="send_audit_sms" value="Other" {{ $vehicle->send_audit_sms != $vehicle->driver_id ? 'checked' : '' }} onclick="toggleOtherInput(true)">
                    To Other
                  </label>
                  <input type="text" name="send_audit_sms_other" id="sendAuditSmsOther" class="form-control mt-2" placeholder="Enter other recipient" value="{{ $vehicle->send_audit_sms != $vehicle->driver_id ? $vehicle->send_audit_sms : '' }}" style="display: {{ $vehicle->send_audit_sms != $vehicle->driver_id ? 'block' : 'none' }};">
                </div>
              </div>
            </div>
          </div>

          <!-- Driver Tab -->
          <div class="tab-pane" id="driver">
            <div class="row card-body">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('driver_id', __('Driver'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    <select name="driver_id" class="form-control" required id="driver_id">
                      <option value="">Select Driver</option>
                      @foreach($drivers as $driver)
                      <option value="{{$driver->id}}" {{ $vehicle->driver_id == $driver->id ? 'selected' : '' }}>{{$driver->name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('mobile_number', __('Mobile Number'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::tel('mobile_number', $vehicle->mobile_number, ['class' => 'form-control', 'placeholder' => '1234567890', 'required', 'pattern' => '[0-9]{10}', 'maxlength' => '10']) !!}
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('alternative_number', __('Alternative Number'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::tel('alternative_number', $vehicle->alternative_number, ['class' => 'form-control', 'placeholder' => '1234567890', 'pattern' => '[0-9]{10}', 'maxlength' => '10']) !!}
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('comments', __('Comments'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::textarea('comments', $vehicle->comments, ['class' => 'form-control', 'rows' => 3]) !!}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xs-6 col-xs-offset-3">
            {!! Form::submit(__('fleet.update'), ['class' => 'btn btn-success']) !!}
          </div>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>

@endsection

@section("script")
<script type="text/javascript">
  $(document).ready(function() {
    $('#vendor_id').select2({placeholder: "Select Vendor"});
    $('#vehicle_type').select2({placeholder: "Select Vehicle Type"});
    $('#driver_id').select2({placeholder: "Select Driver"});

    $('.datepicker').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });

    $('#status').change(function() {
      if ($(this).val() == 'inactive') {
        $('#inactive_reason_div').show();
      } else {
        $('#inactive_reason_div').hide();
      }
    });

    $('#driver_id').change(function() {
      let selectedDriver = $(this).val();
      if (selectedDriver) {
        $('input[name="send_audit_sms"][value="Driver"]').prop('checked', true);
      }
    });

    $('#registration_no').on('input', function () {
      let value = $(this).val().toUpperCase();
      let cleanedValue = value.replace(/[^A-Z0-9-]/gi, '');
      let formatted = cleanedValue.replace(/^([A-Z]{2})(\d{2})([A-Z]{0,2})(\d{0,4})$/, function (match, p1, p2, p3, p4) {
        return p1 + '-' + p2 + (p3 ? '-' + p3 : '') + (p4 ? '-' + p4 : '');
      });
      $(this).val(formatted.substring(0, 13));
    });

    $('#registration_no').on('blur', function () {
      let value = $(this).val();
      let regex = /^[A-Z]{2}-\d{2}-[A-Z]{2}-\d{4}$/;
      if (!regex.test(value)) {
        $('#reg_error').show();
        $(this).css('border', '2px solid red');
      } else {
        $('#reg_error').hide();
        $(this).css('border', '');
      }
    });

    function generateVehicleID() {
      let vendorName = $('#vendor_id option:selected').data('name');
      let registrationNo = $('#registration_no').val();
      if (vendorName && registrationNo.length >= 4) {
        let vendorPrefix = vendorName.split(' ')[0].toUpperCase();
        let regSuffix = registrationNo.slice(-4);
        let vehicleID = vendorPrefix + '-' + regSuffix;
        $('#vehicle_id').val(vehicleID);
      } else {
        $('#vehicle_id').val(''); // Clear if conditions are not met
            }
        }

        $('#status').change(function() {
            if ($(this).val() === 'inactive') {
                $('#inactive_reason_div').show();
            } else {
                $('#inactive_reason_div').hide();
            }
        });
    });
    </script>
    @endsection