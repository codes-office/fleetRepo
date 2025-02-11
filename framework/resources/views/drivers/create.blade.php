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
    /* width: calc(100% / 2); */
  }

  .tab-content {
    margin-top: 20px;
  }

  .image-upload-container {
    position: relative;
    display: inline-block;
    width: 150px;
    height: 150px;
}

.profile-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid #ccc;
}

.btn-sm {
    display: block;
    width: 100%;
    margin-top: 5px;
}

</style>
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('drivers.index')}}">@lang('fleet.drivers')</a></li>
<li class="breadcrumb-item active">@lang('fleet.addDriver')</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header with-border">
        <h3 class="card-title">@lang('fleet.addDriver')</h3>
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

        {!! Form::open(['route' => 'drivers.store','files'=>true,'method'=>'post','id'=>'driver-create-form']) !!}

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="formTabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true">PERSONAL DETAILS</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents" role="tab" aria-controls="documents" aria-selected="false">DOCUMENTS</a>
          </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="formTabsContent">
         <!-- Information Tab -->
<div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
    <div class="row">
        <!-- Profile Image (Left Side) -->
        <div class="col-md-4 text-center">
            <div class="image-upload-container">
                <!-- Image Preview -->
                <img id="profilePreview" src="{{ asset('path/to/default-image.jpg') }}" 
                     class="img-thumbnail profile-img" 
                     alt="Profile Image" />
                <!-- Hidden File Input -->
                <input type="file" id="profilePhotoInput" name="profile_photo" 
                       class="d-none" accept="image/png, image/jpeg, image/jpg">
                <!-- Upload Button -->
                <button type="button" class="btn btn-primary btn-sm mt-2" 
                        onclick="document.getElementById('profilePhotoInput').click();">
                    <i class="fa fa-pencil"></i> Add Image (JPG, JPEG & PNG)
                </button>
            </div>
        </div>

        <!-- Personal Information (Right Side) -->
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('first_name', __('DRIVER NAME'), ['class' => 'form-label required','autofocus']) !!}
                        {!! Form::text('first_name', null,['class' => 'form-control','required','autofocus']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('city', __('CITY'), ['class' => 'form-label']) !!}
                        {!! Form::text('city', null,['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('DOB', __('DATE OF BIRTH'), ['class' => 'form-label required']) !!}
                        {!! Form::date('DOB', null,['class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('phone', __('fleet.phone'), ['class' => 'form-label required']) !!}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                {!! Form::select('phone_code',$phone_code,null,['class' => 'form-control code','required','style'=>'width:80px']) !!}
                            </div>
                            {!! Form::number('phone', null,['class' => 'form-control','required']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('emp_id', __('fleet.employee_id'), ['class' => 'form-label']) !!}
                        {!! Form::text('emp_id', null,['class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('vendor_id', __('VENDORS'), ['class' => 'form-label required']) !!}
                        {!! Form::select('vendor_id', $vendors->pluck('name','user_id'), null, ['class' => 'form-control', 'placeholder' => __('Select a Vendor'), 'required']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

       <!-- Documents Tab -->
<div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
  <div class="table-responsive">
    <table class="table" style="border-collapse: collapse; width: 100%;">
      
      <tbody>
      <!-- License Number -->
        <tr style="border-bottom: 1px solid #dee2e6;">
          <td>License Number</td>
          <td>
          {!! Form::text('license_number', null, ['class' => 'form-control']) !!}
          </td>
          <td>
           Expiry Date
          </td>
          <td>
            {!! Form::date('license_number_date', null, ['class' => 'form-control']) !!}
          </td>
          <td>
            Document
          </td>
          
          <td>
            {!! Form::file('driver_license_image', null, ['class' => 'form-control', 'required']) !!}
          </td>
        </tr>
        <!-- Induction Date -->
        <tr style="border-bottom: 1px solid #dee2e6;">
          <td>Induction Date</td>

          <td>
            {!! Form::date('induction_date', null, ['class' => 'form-control']) !!}
          </td>
          <td>
            Documents
          </td>
          <td>
            {!! Form::file('induction_file', null, ['class' => 'form-control']) !!}
          </td>
        </tr>
        <!-- Badge Number -->
        <tr style="border-bottom: 1px solid #dee2e6;">
          <td>Badge Number</td>
          <td>
          {!! Form::text('badge_number', null, ['class' => 'form-control']) !!}
          </td>
          <td>
            Expiry Date
          </td>
          <td>
            {!! Form::date('badge_number_date', null, ['class' => 'form-control']) !!}
          </td>
          
        </tr>
       
        <!-- Alternate Govt.ID -->
        <tr style="border-bottom: 1px solid #dee2e6;">
          <td>Alternate Govt.ID</td>

          <td>
          {!! Form::select('alternate_gov_id', ['AdharCard' => 'AdharCard', 'Rationcard' => 'Ration card'], null, ['class' => 'form-control']) !!}
          </td>
          <td>
            ID Number
            </td>
            <td>
              {!! Form::text('alternate_gov_id_number', null, ['class' => 'form-control']) !!}
          <td>
            Document
          </td>
          <td>
            {!! Form::file('alternate_gov_file', null, ['class' => 'form-control']) !!}
          </td>
        </tr>
        <!-- BackGround Verification -->
        <tr style="border-bottom: 1px solid #dee2e6;">
          <td>BackGround Verification</td>

          <td>
          {!! Form::select('background_verification_status', ['Pending' => 'Pending', 'Success' => 'Success', 'Rejected' => 'Rejected'], null, ['class' => 'form-control']) !!}
          </td>
          <td>
            Expiry Date
            </td>
            <td>
            {!! Form::date('background_verification_date', null, ['class' => 'form-control']) !!}
          <td>
            Document
          </td>
          <td>
            {!! Form::file('background_verification_file', null, ['class' => 'form-control']) !!}
          </td>
        </tr>
        <!-- Police Verification -->
        <tr style="border-bottom: 1px solid #dee2e6;">
          <td>Police Verification</td>

          <td>
          {!! Form::select('police_verification_status', ['Pending' => 'Pending', 'Success' => 'Success','Rejected' => 'Rejected'], null, ['class' => 'form-control']) !!}
          </td>
          <td>
            Expiry Date
            </td>
            <td>
            {!! Form::date('police_verification_date', null, ['class' => 'form-control']) !!}
          <td>
            Document
          </td>
          <td>
            {!! Form::file('police_verification_file', null, ['class' => 'form-control']) !!}
          </td>
        </tr>
        <!-- Medical verification -->
        <tr style="border-bottom: 1px solid #dee2e6;">
          <td>Medical verification</td>

          <td>
          {!! Form::select('medical_verification_status', ['Pending' => 'Pending', 'Success' => 'Success','Rejected' => 'Rejected'], null, ['class' => 'form-control']) !!}
          </td>
          <td>
            Expiry Date
            </td>
            <td>
            {!! Form::date('medical_verification_date', null, ['class' => 'form-control']) !!}
          <td>
            Document
          </td>
          <td>
            {!! Form::file('medical_verification_file', null, ['class' => 'form-control']) !!}
          </td>
        </tr>
        <!--Training verification -->
        <tr style="border-bottom: 1px solid #dee2e6;">
          <td>Training verification</td>

          <td>
          {!! Form::select('training_verification_status', ['Pending' => 'Pending', 'Success' => 'Success','Rejected' => 'Rejected'], null, ['class' => 'form-control']) !!}
          </td>
          <td>
            Expiry Date
            </td>
            <td>
            {!! Form::date('training_date', null, ['class' => 'form-control']) !!}
          <td>
            Document
          </td>
          <td>
            {!! Form::file('training_file', null, ['class' => 'form-control']) !!}
          </td>
        </tr>
        <!--Eye test -->
        <tr style="border-bottom: 1px solid #dee2e6;">
          <td>Eye Test Expiry Date</td>

            <td>
            {!! Form::date('eye_test_date', null, ['class' => 'form-control']) !!}
          <td>
            Document
          </td>
          <td>
            {!! Form::file('eye_test_file', null, ['class' => 'form-control']) !!}
          </td>
        </tr>
         <!--Letter of Undertaking -->
         <tr style="border-bottom: 1px solid #dee2e6;">
          <td>Letter of Undertaking</td>

            <td>
            {!! Form::file('documents_file', null, ['class' => 'form-control']) !!}
          <td>
            Document
          </td>
          <td>
            {!! Form::file('current_address_file', null, ['class' => 'form-control']) !!}
          </td>
        </tr>
        <tr style="border-bottom: 1px solid #dee2e6;">
          <!-- <td>@lang('fleet.emergency_details')</td>
          <td colspan="5">
            {!! Form::textarea('econtact', null, ['class' => 'form-control']) !!}
          </td> -->
        </tr>
      </tbody>
    </table>
  </div>
</div>



        <!-- Submit Button -->
        <div class="col-md-12">
          {!! Form::submit(__('fleet.saveDriver'), ['class' => 'btn btn-success']) !!}
        </div>

        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>

@endsection

@section("script")
<script>
document.getElementById('profilePhotoInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});


  $(document).ready(function () {
    $('#driver-create-form').validate({
      rules: {
        password: {
          required: true,
          minlength: 6
        }
      },
      messages: {
        vehicle_id: "Assign Vehicle field is required.",
      },
      errorPlacement: function (error, element) {
        if (element.hasClass('select2-hidden-accessible') && element.next('.select2-container').length) {
          error.insertAfter(element.next('.select2-container'));
        } else if (element.parent('.input-group').length) {
          error.insertAfter(element.parent());
        }
        else {
          error.insertAfter(element);
        }
      },
      highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
      }
    });
  });
</script>
@endsection
