@extends('layouts.app')

@section('extra_css')
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">
<style type="text/css">
  .select2-selection:not(.select2-selection--multiple) {
    height: 38px !important;
  }
  .input-group-append,
  .input-group-prepend {
    display: flex;
  }
  .tab-content {
    margin-top: 20px;
  }
</style>
@endsection

@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('drivers.index')}}">@lang('fleet.drivers')</a></li>
<li class="breadcrumb-item active">@lang('fleet.editDriver')</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-primary">
      <div class="card-header with-border">
        <h3 class="card-title">@lang('fleet.editDriver')</h3>
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

        {!! Form::model($driver, ['route' => ['drivers.update', $driver->id], 'method' => 'PUT', 'files' => true, 'id' => 'driver-edit-form']) !!}

        <ul class="nav nav-tabs" id="formTabs" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab">Personal Details</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents" role="tab">Documents</a>
  </li>
</ul>

       <!-- Tab Content -->
       <div class="tab-content mt-3" id="formTabsContent">
        <!-- Information Tab -->
        @php
    // Retrieve profile photo, ensure the key exists
    $profilePhoto = $userFiles['profile_photo'] ?? null;

    // If the profile photo exists, construct the file path
    $profilePath = $profilePhoto && file_exists(public_path('uploads/' . $profilePhoto))
        ? asset('uploads/' . $profilePhoto) // Use asset() to generate a correct URL
        : asset('assets/images/no-user.jpg'); // Default image
@endphp

<!-- Profile Image -->
<div class="col-md-4 text-center">
    <div class="image-upload-container">
        <!-- Display Image -->
        <img id="profilePreview" 
             src="{{ $profilePath }}" 
             class="img-thumbnail profile-img" 
             alt="Profile Image" />

        <!-- Hidden File Input -->
        <input type="file" id="profilePhotoInput" name="profile_photo" 
               class="d-none" accept="image/png, image/jpeg, image/jpg">

        <!-- Upload Button -->
        @if($canUpload)
            <button type="button" class="btn btn-primary btn-sm mt-2" 
                    onclick="document.getElementById('profilePhotoInput').click();">
                <i class="fa fa-pencil"></i> Change Image (JPG, JPEG & PNG)
            </button>
        @endif

        <!-- View & Download Links -->
        @if($profilePhoto && file_exists(public_path('uploads/' . $profilePhoto)))
            <div class="mt-2">
                <a href="{{ url('admin/view', ['filename' => $profilePhoto]) }}" 
                   target="_blank" class="btn btn-info btn-sm">
                    <i class="fa fa-eye"></i> View
                </a>
                <a href="{{ url('admin/download', ['filename' => $profilePhoto]) }}" 
                   class="btn btn-success btn-sm">
                    <i class="fa fa-download"></i> Download
                </a>
            </div>
        @endif
    </div>
</div>



        <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
          <div class="row">
            <div class="col-md-4">
            <div class="form-group">
        {!! Form::label('first_name', __('DRIVER NAME'), ['class' => 'form-label required']) !!}
        {!! Form::text('first_name', $userDetails->name, ['class' => 'form-control', 'required', 'autofocus']) !!}
      </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                {!! Form::label('city', __('CITY'), ['class' => 'form-label']) !!}
                {!! Form::text('city', null, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                {!! Form::label('DOB', __('DATE OF BIRTH'), ['class' => 'form-label required']) !!}
                {!! Form::date('DOB', null, ['class' => 'form-control', 'required']) !!}
              </div>
            </div>
          </div>

          <!-- Additional Fields for Information Tab -->
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                {!! Form::label('phone', __('fleet.phone'), ['class' => 'form-label required']) !!}
                <div class="input-group">
                  <div class="input-group-prepend">
                    {!! Form::select('phone_code', $phone_code, null, ['class' => 'form-control code', 'required', 'style'=>'width:80px']) !!}
                  </div>
                  {!! Form::number('phone', $userDetails->number, ['class' => 'form-control', 'required']) !!}
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                {!! Form::label('emp_id', __('fleet.employee_id'), ['class' => 'form-label']) !!}
                {!! Form::text('emp_id', null, ['class' => 'form-control', 'required']) !!}
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                {!! Form::label('vendor_id', __('VENDORS'), ['class' => 'form-label required']) !!}
                {!! Form::select('vendor_id', $vendors->pluck('name','user_id'), null, ['class' => 'form-control', 'placeholder' => __('Select a Vendor'), 'required']) !!}
              </div>
            </div>
          </div>
        </div>
      </div>


          <!-- Documents Tab -->
          <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
              <table class="table">
                <tbody>
                  <!-- Driver License -->
        <tr>
          <td>License Number</td>
          <td>{!! Form::text('license_number', null, ['class' => 'form-control']) !!}</td>
          <td>Expiry Date</td>
          <td>{!! Form::date('license_number_date', null, ['class' => 'form-control']) !!}</td>
          <td>Document</td>
          <td>
            @if(isset($userFiles['driver_license']) && !empty($userFiles['driver_license']))
              <a href="{{url('admin/view', ['filename' => basename($userFiles['driver_license'])]) }}" target="_blank">view</a>
              <a href="{{ url('admin/download', ['filename' => basename($userFiles['driver_license'])]) }}" class="btn btn-sm btn-success">Download</a>
            @endif
          </td>
        </tr>
        <!-- Induction Date -->
                  <tr>
                    <td>Induction Date</td>
                    <td>{!! Form::date('induction_date', null, ['class' => 'form-control']) !!}</td>
                    <td>Documents</td>
                    <td>
            @if(isset($userFiles['induction']) && !empty($userFiles['induction']))
              <a href="{{url('admin/view', ['filename' => basename($userFiles['induction'])]) }}" target="_blank">view</a>
              <a href="{{ url('admin/download', ['filename' => basename($userFiles['induction'])]) }}" class="btn btn-sm btn-success">Download</a>
            @endif
          </td>
                  </tr>

                  <!-- badge number -->
                  <tr>
                    <td>Badge Number</td>
                    <td>{!! Form::text('badge_number', null, ['class' => 'form-control']) !!}</td>
                    <td>{!! Form::date('badge_number_date', null, ['class' => 'form-control']) !!}</td>
                  </tr>  
              <!-- Alternate Govt-->
                  <tr>
                  <td>Alternate Govt. ID</td>
                                <td>{!! Form::select('alternate_gov_id', ['AdharCard' => 'AdharCard', 'Rationcard' => 'Ration Card'], $driver->alternate_gov_id, ['class' => 'form-control']) !!}</td>
                                <td>ID Number</td>
                                <td>{!! Form::text('alternate_gov_id_number', $driver->alternate_gov_id_number, ['class' => 'form-control']) !!}</td>
                                <td>Document</td>
                              <td>
            @if(isset($userFiles['alternate_gov']) && !empty($userFiles['alternate_gov']))
              <a href="{{url('admin/view', ['filename' => basename($userFiles['alternate_gov'])]) }}" target="_blank">view</a>
              <a href="{{ url('admin/download', ['filename' => basename($userFiles['alternate_gov'])]) }}" class="btn btn-sm btn-success">Download</a>
            @endif
          </td>
          </tr>
          <!-- Background Verification -->
          <tr>
          <td>Background Verification</td>
                                <td>{!! Form::select('background_verification_status', ['Pending' => 'Pending', 'Success' => 'Success','Rejected' => 'Rejected'], $driver->background_verification_status, ['class' => 'form-control']) !!}</td>
                                <td>Expiry Date</td>
                                <td>{!! Form::date('background_verification_date', $driver->background_verification_date, ['class' => 'form-control']) !!}</td>
                                <td>Document</td>
          <td>
            @if(isset($userFiles['background_verification']) && !empty($userFiles['background_verification']))
              <a href="{{url('admin/view', ['filename' => basename($userFiles['background_verification'])]) }}" target="_blank">view</a>
              <a href="{{ url('admin/download', ['filename' => basename($userFiles['background_verification'])]) }}" class="btn btn-sm btn-success">Download</a>
            @endif
          </td>
        </tr>
        <!-- Police Verification -->
        <tr>
          <td>Police Verification</td>
                                <td>{!! Form::select('police_verification_status', ['Pending' => 'Pending', 'Success' => 'Success','Rejected' => 'Rejected'], $driver->police_verification_status, ['class' => 'form-control']) !!}</td>
                                <td>Expiry Date</td>
                                <td>{!! Form::date('police_verification_date', $driver->police_verification_date, ['class' => 'form-control']) !!}</td>
                                <td>Document</td>
          <td>
            @if(isset($userFiles['police_verification']) && !empty($userFiles['police_verification']))
              <a href="{{url('admin/view', ['filename' => basename($userFiles['police_verification'])]) }}" target="_blank">view</a>
              <a href="{{ url('admin/download', ['filename' => basename($userFiles['police_verification'])]) }}" class="btn btn-sm btn-success">Download</a>
            @endif
          </td>
        </tr>
        <!-- Medical Verification -->
        <tr>
                                <td>Medical Verification</td>
                                <td>{!! Form::select('medical_verification_status', ['Pending' => 'Pending', 'Success' => 'Success','Rejected' => 'Rejected'], $driver->medical_verification_status, ['class' => 'form-control']) !!}</td>
                                <td>Expiry Date</td>
                                <td>{!! Form::date('medical_verification_date', $driver->medical_verification_date, ['class' => 'form-control']) !!}</td>
                                <td>Document</td>
                                <td>
            @if(isset($userFiles['medical_verification']) && !empty($userFiles['medical_verification']))
              <a href="{{url('admin/view', ['filename' => basename($userFiles['medical_verification'])]) }}" target="_blank">view</a>
              <a href="{{ url('admin/download', ['filename' => basename($userFiles['medical_verification'])]) }}" class="btn btn-sm btn-success">Download</a>
            @endif
          </td>
          </tr>
          <!-- Training-->
          <tr>
                                <td>Training</td>
                                <td>{!! Form::select('training_verification_status', ['Pending' => 'Pending', 'Success' => 'Success','Rejected' => 'Rejected'], $driver->training_verification_status, ['class' => 'form-control']) !!}</td>
                                <td>Expiry Date</td>
                                <td>{!! Form::date('training_date', $driver->training_date, ['class' => 'form-control']) !!}</td>
                                <td>Document</td>
                                <td>
            @if(isset($userFiles['training']) && !empty($userFiles['training']))
              <a href="{{url('admin/view', ['filename' => basename($userFiles['training'])]) }}" target="_blank">view</a>
              <a href="{{ url('admin/download', ['filename' => basename($userFiles['training'])]) }}" class="btn btn-sm btn-success">Download</a>
            @endif
          </td>
          </tr>
           <!-- Eye Test -->
           <tr>
                                <td>Eye Test Expiry Date</td>
                                <td>{!! Form::date('eye_test_date', $driver->eye_test_date, ['class' => 'form-control']) !!}</td>
                                <td>Document</td>
                                <td>
            @if(isset($userFiles['training']) && !empty($userFiles['training']))
              <a href="{{url('admin/view', ['filename' => basename($userFiles['eye_test'])]) }}" target="_blank">view</a>
              <a href="{{ url('admin/download', ['filename' => basename($userFiles['eye_test'])]) }}" class="btn btn-sm btn-success">Download</a>
            @endif
          </td>
          </tr>
           <!-- Letter of Undertaking -->
           <tr>
                                <td>Letter of Undertaking</td>
                                <td>@if(isset($userFiles['documents']) && !empty($userFiles['documents']))
              <a href="{{url('admin/view', ['filename' => basename($userFiles['documents'])]) }}" target="_blank">view</a>
              <a href="{{ url('admin/download', ['filename' => basename($userFiles['documents'])]) }}" class="btn btn-sm btn-success">Download</a>
            @endif</td>
                                <td>Current Address</td>
                                <td>
            @if(isset($userFiles['current_address']) && !empty($userFiles['current_address']))
              <a href="{{url('admin/view', ['filename' => basename($userFiles['current_address'])]) }}" target="_blank">view</a>
              <a href="{{ url('admin/download', ['filename' => basename($userFiles['current_address'])]) }}" class="btn btn-sm btn-success">Download</a>
            @endif
          </td>
          </tr>

                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-md-12">
          {!! Form::submit(__('fleet.updateDriver'), ['class' => 'btn btn-primary']) !!}
        </div>

        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
@endsection

@section("script")
<script>
  $(document).ready(function () {
    $('#driver-edit-form').validate({
      rules: {
        password: {
          minlength: 6
        }
      },
      messages: {
        vehicle_id: "Assign Vehicle field is required."
      },
      errorPlacement: function (error, element) {
        if (element.hasClass('select2-hidden-accessible') && element.next('.select2-container').length) {
          error.insertAfter(element.next('.select2-container'));
        } else if (element.parent('.input-group').length) {
          error.insertAfter(element.parent());
        } else {
          error.insertAfter(element);
        }
      },
      highlight: function (element) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function (element) {
        $(element).removeClass('is-invalid');
      }
    });
  });
</script>
@endsection
