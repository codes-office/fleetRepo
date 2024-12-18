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
        {!! Form::hidden('is_active',1) !!}
        {!! Form::hidden('is_available',0) !!}
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('first_name', __('fleet.firstname'), ['class' => 'form-label required','autofocus']) !!}
              {!! Form::text('first_name', null,['class' => 'form-control','required','autofocus']) !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('middle_name', __('fleet.middlename'), ['class' => 'form-label']) !!}
              {!! Form::text('middle_name', null,['class' => 'form-control']) !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('last_name', __('fleet.lastname'), ['class' => 'form-label required']) !!}
              {!! Form::text('last_name', null,['class' => 'form-control','required']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('address', __('fleet.address'), ['class' => 'form-label required']) !!}
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-address-book-o"></i></span>
                </div>
                {!! Form::text('address', null,['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('email', __('fleet.email'), ['class' => 'form-label required']) !!}
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                </div>
                {!! Form::email('email', null,['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('phone', __('fleet.phone'), ['class' => 'form-label required']) !!}
              <div class="input-group">
                <div class="input-group-prepend">
                  {!! Form::select('phone_code',$phone_code,null,['class' => 'form-control
                  code','required','style'=>'width:80px']) !!}
                </div>
                {!! Form::number('phone', null,['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('emp_id', __('fleet.employee_id'), ['class' => 'form-label']) !!}
              {!! Form::text('emp_id', null,['class' => 'form-control','required']) !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('contract_number', __('fleet.contract'), ['class' => 'form-label']) !!}
              {!! Form::text('contract_number', null,['class' => 'form-control','required']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('license_number', __('fleet.licenseNumber'), ['class' => 'form-label required']) !!}
              {!! Form::text('license_number', null,['class' => 'form-control','required']) !!}
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('issue_date', __('fleet.issueDate'), ['class' => 'form-label']) !!}
              <div class="input-group date">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
                {!! Form::text('issue_date', null,['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('exp_date', __('fleet.expirationDate'), ['class' => 'form-label required']) !!}
              <div class="input-group date">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
                {!! Form::text('exp_date', null,['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('start_date', __('fleet.join_date'), ['class' => 'form-label']) !!}
              <div class="input-group date">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
                {!! Form::text('start_date', null,['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('end_date', __('fleet.leave_date'), ['class' => 'form-label']) !!}
              <div class="input-group date">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
                {!! Form::text('end_date', null,['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('password', __('fleet.password'), ['class' => 'form-label']) !!}
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-lock"></i></span>
                </div>
                {!! Form::password('password', ['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('driver_commision_type', __('fleet.driver_commision_type'), ['class' => 'form-label']) !!}
              
                {!! Form::select('driver_commision_type',['amount'=>__('fleet.amount'), 'percent'=> __('fleet.percent')],null,['class' => 'form-control', 'placeholder' =>__('fleet.select'), 'required']) !!}            
            </div>
          </div>
          <div class="col-md-4" id="driver_commision_container" style="display: none;">
            <div class="form-group">
              {!! Form::label('driver_commision', __('fleet.driver_commision'), ['class' => 'form-label']) !!}              
                {!! Form::number('driver_commision',null,['class' => 'form-control']) !!}            
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('gender', __('fleet.gender') , ['class' => 'form-label']) !!}<br>
              <input type="radio" name="gender" class="flat-red gender" value="1" checked> @lang('fleet.male')<br>

              <input type="radio" name="gender" class="flat-red gender" value="0"> @lang('fleet.female')
            </div>

            <div class="form-group">
              {!! Form::label('driver_image', __('fleet.driverImage'), ['class' => 'form-label']) !!}

              {!! Form::file('driver_image',null,['class' => 'form-control','required']) !!}
            </div>
            <div class="form-group">
              {!! Form::label('documents', __('fleet.documents'), ['class' => 'form-label']) !!}
              {!! Form::file('documents',null,['class' => 'form-control','required']) !!}
            </div>


            <div class="form-group">
              {!! Form::label('license_image', __('fleet.licenseImage'), ['class' => 'form-label']) !!}
              {!! Form::file('license_image',null,['class' => 'form-control','required']) !!}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('econtact', __('fleet.emergency_details'), ['class' => 'form-label']) !!}
              {!! Form::textarea('econtact',null,['class' => 'form-control']) !!}
            </div>
          </div>
        </div>
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
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('#driver_commision_type').on('change', function(){
      var val = $(this).val();
      if(val==''){
        $('#driver_commision_container').hide();
      }else{
        if(val =='amount'){
          $('#driver_commision').attr('placeholder',"@lang('fleet.enter_amount')");
        }else{
          $('#driver_commision').attr('placeholder',"@lang('fleet.enter_percent')")
        }
        $('#driver_commision_container').show();
      }
    });
    
    $('.code').select2();
    $('#vehicle_id').select2({
      placeholder:"@lang('fleet.selectVehicle')"
    });
    
    $("#first_name").focus();
    $('#end_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    }).on('show', function() {
    var pickupdate = $( "#start_date" ).datepicker('getDate');
    if (pickupdate) {
      $("#end_date").datepicker('setStartDate', pickupdate);
    }
  
  });
    $('#exp_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    }).on('show', function() {
    var pickupdate = $( "#issue_date" ).datepicker('getDate');
    if (pickupdate) {
      $("#exp_date").datepicker('setStartDate', pickupdate);
    }
  });
    $('#issue_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });
    $('#start_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });

    $("#driver-create-form").validate({
      // in 'rules' user have to specify all the constraints for respective fields
      rules: {        
        password: {
          required:true,
          minlength: 6
        }
      },
      // in 'messages' user have to specify message as per rules
      messages: {
        vehicle_id: "Assign Vehicle field is required.",           
      },
      errorPlacement: function (error, element) {
        if(element.hasClass('select2-hidden-accessible') && element.next('.select2-container').length) {
            error.insertAfter(element.next('.select2-container'));
        }else if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        }
        else if (element.prop('type') === 'radio' && element.parent('.radio-inline').length) {
            error.insertAfter(element.parent().parent());
        }
        else if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
            error.appendTo(element.parent().parent());
        }
        else {
            error.insertAfter(element);
        }
      },
      highlight: function (element, errorClass, validClass) {
        if($(element).hasClass('select2-hidden-accessible') && $(element).next('.select2-container').length) {
          
          $(element).next('.select2-container').find('.select2-selection').addClass('border-danger');
        }else{

        $(element).addClass('is-invalid');     
        }
        // return false;
      },
      unhighlight: function (element, errorClass, validClass) {
        if($(element).hasClass('select2-hidden-accessible') && $(element).next('.select2-container').length) {
        console.log(element, errorClass, validClass)

          $(element).next('.select2-container').find('.select2-selection').removeClass('border-danger');
        }else{
          $(element).removeClass('is-invalid');
        }
      }
    });
    
    //Flat red color scheme for iCheck
    // $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
    //   checkboxClass: 'icheckbox_flat-green',
    //   radioClass   : 'iradio_flat-green'
    // })
  });
</script>
@endsection