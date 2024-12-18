@extends('layouts.app')
@section('extra_css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap-datetimepicker.min.css')}}">
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ url('admin/vehicle-reviews')}}">@lang('fleet.vehicle_inspection')</a></li>
<li class="breadcrumb-item active">@lang('fleet.add_vehicle_inspection')</li>
@endsection
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.add_vehicle_inspection')</h3>
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

      {!! Form::open(['url' => 'admin/store-vehicle-review','method'=>'post','files'=>true]) !!}
      {!! Form::hidden('user_id',Auth::user()->id)!!}

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('vehicle_id',__('fleet.selectVehicle'), ['class' => 'form-label']) !!}
            <select id="vehicle_id" name="vehicle_id" class="form-control" required>
              <option value=""></option>
              {{-- @foreach($vehicles as $vehicle)
              <option value="{{$vehicle->vehicle_id}}">{{$vehicle->vehicle->make_name}} - {{$vehicle->vehicle->model_name}} - {{$vehicle->vehicle->license_plate}}</option>
              @endforeach --}}
              @foreach($vehicles as $vehicle)
              <option value="{{$vehicle->id}}">{{$vehicle->make_name}} - {{$vehicle->model_name}} - {{$vehicle->license_plate}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('reg_no',__('fleet.reg_no'), ['class' => 'form-label']) !!}
            {!! Form::text('reg_no',null,['class'=>'form-control','required']) !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('kms_out',__('fleet.kms_out')." (".Hyvikk::get('dis_format').")", ['class' => 'form-label']) !!}
            {!! Form::number('kms_out',null,['class'=>'form-control','required']) !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('kms_in',__('fleet.kms_in')." (".Hyvikk::get('dis_format').")", ['class' => 'form-label']) !!}
            {!! Form::number('kms_in',null,['class'=>'form-control','required']) !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('fuel_out',__('fleet.fuel_out'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="fuel_out" class="flat-red" value="0" checked> 1/4 &nbsp; &nbsp; &nbsp;
            <input type="radio" name="fuel_out" class="flat-red" value="1"> 1/2 &nbsp; &nbsp; &nbsp;
            <input type="radio" name="fuel_out" class="flat-red" value="2"> 3/4 &nbsp; &nbsp; &nbsp;
            <input type="radio" name="fuel_out" class="flat-red" value="3">@lang('fleet.Full_Tank')
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('fuel_in',__('fleet.fuel_in'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="fuel_in" class="flat-red" value="0" checked> 1/4 &nbsp; &nbsp; &nbsp;
            <input type="radio" name="fuel_in" class="flat-red" value="1"> 1/2 &nbsp; &nbsp; &nbsp;
            <input type="radio" name="fuel_in" class="flat-red" value="2"> 3/4 &nbsp; &nbsp; &nbsp;
            <input type="radio" name="fuel_in" class="flat-red" value="3">@lang('fleet.Full_Tank')
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('datetime_out',__('fleet.datetime_out'), ['class' => 'form-label']) !!}
            <div class='input-group'>
              <div class="input-group-prepend">
              <span class="input-group-text"> <span class="fa fa-calendar"></span></span>
              </div>
              {!! Form::text('datetime_out',null,['class'=>'form-control','required','id'=>'date_out']) !!}
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('datetime_in',__('fleet.datetime_in'), ['class' => 'form-label']) !!}
            <div class='input-group'>
              <div class="input-group-prepend">
                <span class="input-group-text">    <span class="fa fa-calendar"></span>
              </span>
              </div>
              {!! Form::text('datetime_in',null,['class'=>'form-control','required','id'=>'date_in']) !!}
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('petrol_card',__('fleet.petrol_card'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="petrol_card" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="petrol_card" class="flat-red" value="0">@lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="petrol_card_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('lights',__('fleet.lights'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="lights" class="flat-red" value="1"> @lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="lights" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="lights_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('invertor',__('fleet.invertor'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="invertor" class="flat-red" value="1"> @lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="invertor" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="invertor_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('car_mats',__('fleet.car_mats'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="car_mats" class="flat-red" value="1"> @lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="car_mats" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="car_mats_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('int_damage',__('fleet.int_damage'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="int_damage" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="int_damage" class="flat-red" value="0">@lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="int_damage_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('int_lights',__('fleet.int_lights'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="int_lights" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="int_lights" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="int_lights_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('ext_car',__('fleet.ext_car'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="ext_car" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="ext_car" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="ext_car_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('tyre',__('fleet.tyre'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="tyre" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="tyre" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="tyre_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('ladder',__('fleet.ladder'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="ladder" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="ladder" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="ladder_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('leed',__('fleet.leed'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="leed" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="leed" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="leed_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('power_tool',__('fleet.power_tool'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="power_tool" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="power_tool" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="power_tool_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('ac',__('fleet.ac'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="ac" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="ac" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="ac_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('head_light',__('fleet.head_light'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="head_light" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="head_light" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="head_light_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('lock',__('fleet.lock'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="lock" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="lock" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="lock_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('windows',__('fleet.windows'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="windows" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="windows" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="windows_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('condition',__('fleet.condition'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="condition" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="condition" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="condition_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('oil_chk',__('fleet.oil_chk'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="oil_chk" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="oil_chk" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="oil_chk_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('suspension',__('fleet.suspension'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="suspension" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="suspension" class="flat-red" value="0">  @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="suspension_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('tool_box',__('fleet.tool_box'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="tool_box" class="flat-red" value="1">@lang('fleet.yes') &nbsp; &nbsp; &nbsp;
            <input type="radio" name="tool_box" class="flat-red" value="0"> @lang('fleet.no') &nbsp; &nbsp; &nbsp;
            <input type="text" name="tool_box_text" style="width: 300px; margin-top:5px;">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('image',__('fleet.select_image'), ['class' => 'form-label']) !!}
            {!! Form::file('image',['class'=>'form-control']) !!}          
          </div>
        </div>
      </div>

      <hr>
      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('udf1',__('fleet.add_udf'), ['class' => 'col-xs-5 control-label']) !!}
          <div class="row">
            <div class="col-md-8">
              {!! Form::text('udf1', null,['class' => 'form-control']) !!}
            </div>
            <div class="col-md-4">
              <button type="button" class="btn btn-info add_udf"> @lang('fleet.add')</button>
            </div>
          </div>
        </div>
        <div class="blank"></div>
      </div>

      <div class="row">
        <div class="col-md-12">
          {!! Form::submit(__('fleet.submit'), ['class' => 'btn btn-success']) !!}
        </div>
      </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
  $('#vehicle_id').select2({placeholder:"@lang('fleet.selectVehicle')"});
  $('#date_in').datetimepicker({format: 'YYYY-MM-DD HH:mm:ss',sideBySide: true,icons: {
              previous: 'fa fa-arrow-left',
              next: 'fa fa-arrow-right',
              up: "fa fa-arrow-up",
              down: "fa fa-arrow-down"
  }});
  $('#date_out').datetimepicker({format: 'YYYY-MM-DD HH:mm:ss',sideBySide: true,icons: {
              previous: 'fa fa-arrow-left',
              next: 'fa fa-arrow-right',
              up: "fa fa-arrow-up",
              down: "fa fa-arrow-down"
  }});

  $(".add_udf").click(function () {
    // alert($('#udf').val());
    var field = $('#udf1').val();
    if(field == "" || field == null){
      alert('Enter field name');
    }

    else{
      $(".blank").append('<div class="row"><div class="col-md-8">  <div class="form-group"> <label class="form-label">'+ field.toUpperCase() +'</label> <input type="text" name="udf['+ field +']" class="form-control" placeholder="Enter '+ field +'" required></div></div><div class="col-md-4"> <div class="form-group" style="margin-top: 30px"><button class="btn btn-danger" type="button" onclick="this.parentElement.parentElement.parentElement.remove();">Remove</button> </div></div></div>');
      $('#udf1').val("");
    }
  });

  //Flat green color scheme for iCheck
  // $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
  //   checkboxClass: 'icheckbox_flat-green',
  //   radioClass   : 'iradio_flat-green'
  // });
</script>
@endsection