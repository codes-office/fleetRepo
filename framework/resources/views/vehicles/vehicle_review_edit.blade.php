@extends('layouts.app')
@section('extra_css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap-datetimepicker.min.css')}}">
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ url('admin/vehicle-reviews')}}">@lang('fleet.vehicle_inspection')</a></li>
<li class="breadcrumb-item active">@lang('fleet.edit_vehicle_inspection')</li>
@endsection
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="card card-warning">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.edit_vehicle_inspection')</h3>
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

      {!! Form::open(['url' => 'admin/vehicle-review-update','method'=>'post','files'=>true]) !!}
      {!! Form::hidden('user_id',Auth::user()->id)!!}
      {!! Form::hidden('id',$review->id)!!}

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('vehicle_id',__('fleet.selectVehicle'), ['class' => 'form-label']) !!}
            <select id="vehicle_id" name="vehicle_id" class="form-control" required>
              <option value=""></option>
              @foreach($vehicles as $vehicle)
              <option value="{{$vehicle->id}}" @if($vehicle->id == $review->vehicle_id) selected @endif>{{$vehicle->make_name}} - {{$vehicle->model_name}} - {{$vehicle->license_plate}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('reg_no',__('fleet.reg_no'), ['class' => 'form-label']) !!}
            {!! Form::text('reg_no',$review->reg_no,['class'=>'form-control','required']) !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('kms_out',__('fleet.kms_out')." (".Hyvikk::get('dis_format').")", ['class' => 'form-label']) !!}
            {!! Form::number('kms_out',$review->kms_outgoing,['class'=>'form-control','required']) !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('kms_in',__('fleet.kms_in')." (".Hyvikk::get('dis_format').")", ['class' => 'form-label']) !!}
            {!! Form::number('kms_in',$review->kms_incoming,['class'=>'form-control','required']) !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('fuel_out',__('fleet.fuel_out'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="fuel_out" class="flat-red" value="0" @if($review->fuel_level_out == 0) checked @endif> 1/4 &nbsp; &nbsp; &nbsp;
            <input type="radio" name="fuel_out" class="flat-red" value="1" @if($review->fuel_level_out == 1) checked @endif> 1/2 &nbsp; &nbsp; &nbsp;
            <input type="radio" name="fuel_out" class="flat-red" value="2" @if($review->fuel_level_out == 2) checked @endif> 3/4 &nbsp; &nbsp; &nbsp;
            <input type="radio" name="fuel_out" class="flat-red" value="3" @if($review->fuel_level_out == 3) checked @endif> Full Tank
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('fuel_in',__('fleet.fuel_in'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="fuel_in" class="flat-red" value="0" @if($review->fuel_level_in == 0) checked @endif> 1/4 &nbsp; &nbsp; &nbsp;
            <input type="radio" name="fuel_in" class="flat-red" value="1" @if($review->fuel_level_in == 1) checked @endif> 1/2 &nbsp; &nbsp; &nbsp;
            <input type="radio" name="fuel_in" class="flat-red" value="2" @if($review->fuel_level_in == 2) checked @endif> 3/4 &nbsp; &nbsp; &nbsp;
            <input type="radio" name="fuel_in" class="flat-red" value="3" @if($review->fuel_level_in == 3) checked @endif> Full Tank
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('datetime_out',__('fleet.datetime_out'), ['class' => 'form-label']) !!}
            <div class='input-group'>
              <div class="input-group-prepend">
                <span class="input-group-text"><span class="fa fa-calendar"></span></span>
              </div>
              {!! Form::text('datetime_out',$review->datetime_outgoing,['class'=>'form-control','required','id'=>'date_out']) !!}
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('datetime_in',__('fleet.datetime_in'), ['class' => 'form-label']) !!}
            <div class='input-group'>
              <div class="input-group-prepend">
                <span class="input-group-text"> <span class="fa fa-calendar"></span></span>
              </div>
              {!! Form::text('datetime_in',$review->datetime_incoming,['class'=>'form-control','required','id'=>'date_in']) !!}
            </div>
          </div>
        </div>

        <div class="col-md-6">
          @php($petrol_card=unserialize($review->petrol_card))
          <div class="form-group">
            {!! Form::label('petrol_card',__('fleet.petrol_card'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="petrol_card" class="flat-red" value="1" @if($petrol_card['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="petrol_card" class="flat-red" value="0" @if($petrol_card['flag'] == 0 && $petrol_card['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="petrol_card_text" style="width: 300px; margin-top:5px;" value="{{$petrol_card['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($light=unserialize($review->lights))
          <div class="form-group">
            {!! Form::label('lights',__('fleet.lights'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="lights" class="flat-red" value="1" @if($light['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="lights" class="flat-red" value="0" @if($light['flag'] == 0 && $light['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="lights_text" style="width: 300px; margin-top:5px;" value="{{$light['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($invertor=unserialize($review->invertor))
          <div class="form-group">
            {!! Form::label('invertor',__('fleet.invertor'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="invertor" class="flat-red" value="1" @if($invertor['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="invertor" class="flat-red" value="0" @if($invertor['flag'] == 0 && $invertor['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="invertor_text" style="width: 300px; margin-top:5px;" value="{{$invertor['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($car_mat=unserialize($review->car_mats))
          <div class="form-group">
            {!! Form::label('car_mats',__('fleet.car_mats'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="car_mats" class="flat-red" value="1" @if($car_mat['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="car_mats" class="flat-red" value="0" @if($car_mat['flag'] == 0 && $car_mat['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="car_mats_text" style="width: 300px; margin-top:5px;" value="{{$car_mat['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($int_damage=unserialize($review->int_damage))
          <div class="form-group">
            {!! Form::label('int_damage',__('fleet.int_damage'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="int_damage" class="flat-red" value="1" @if($int_damage['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="int_damage" class="flat-red" value="0" @if($int_damage['flag'] == 0 && $int_damage['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="int_damage_text" style="width: 300px; margin-top:5px;" value="{{$int_damage['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($int_lights=unserialize($review->int_lights))
          <div class="form-group">
            {!! Form::label('int_lights',__('fleet.int_lights'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="int_lights" class="flat-red" value="1" @if($int_lights['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="int_lights" class="flat-red" value="0" @if($int_lights['flag'] == 0 && $int_lights['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="int_lights_text" style="width: 300px; margin-top:5px;" value="{{$int_lights['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($ext_car=unserialize($review->ext_car))
          <div class="form-group">
            {!! Form::label('ext_car',__('fleet.ext_car'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="ext_car" class="flat-red" value="1" @if($ext_car['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="ext_car" class="flat-red" value="0" @if($ext_car['flag'] == 0 && $ext_car['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="ext_car_text" style="width: 300px; margin-top:5px;" value="{{$ext_car['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($tyre=unserialize($review->tyre))
          <div class="form-group">
            {!! Form::label('tyre',__('fleet.tyre'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="tyre" class="flat-red" value="1" @if($tyre['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="tyre" class="flat-red" value="0" @if($tyre['flag'] == 0 && $tyre['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="tyre_text" style="width: 300px; margin-top:5px;" value="{{$tyre['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($ladder=unserialize($review->ladder))
          <div class="form-group">
            {!! Form::label('ladder',__('fleet.ladder'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="ladder" class="flat-red" value="1" @if($ladder['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="ladder" class="flat-red" value="0" @if($ladder['flag'] == 0 && $ladder['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="ladder_text" style="width: 300px; margin-top:5px;" value="{{$ladder['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($leed=unserialize($review->leed))
          <div class="form-group">
            {!! Form::label('leed',__('fleet.leed'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="leed" class="flat-red" value="1" @if($leed['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="leed" class="flat-red" value="0" @if($leed['flag'] == 0 && $leed['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="leed_text" style="width: 300px; margin-top:5px;" value="{{$leed['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($power_tool=unserialize($review->power_tool))
          <div class="form-group">
          {!! Form::label('power_tool',__('fleet.power_tool'), ['class' => 'form-label']) !!}
          <br>
          <input type="radio" name="power_tool" class="flat-red" value="1" @if($power_tool['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
          <input type="radio" name="power_tool" class="flat-red" value="0" @if($power_tool['flag'] == 0 && $power_tool['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
          <input type="text" name="power_tool_text" style="width: 300px; margin-top:5px;" value="{{$power_tool['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($ac=unserialize($review->ac))
          <div class="form-group">
            {!! Form::label('ac',__('fleet.ac'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="ac" class="flat-red" value="1" @if($ac['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="ac" class="flat-red" value="0" @if($ac['flag'] == 0 && $ac['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="ac_text" style="width: 300px; margin-top:5px;" value="{{$ac['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($head_light=unserialize($review->head_light))
          <div class="form-group">
            {!! Form::label('head_light',__('fleet.head_light'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="head_light" class="flat-red" value="1" @if($head_light['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="head_light" class="flat-red" value="0" @if($head_light['flag'] == 0 && $head_light['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="head_light_text" style="width: 300px; margin-top:5px;" value="{{$head_light['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($lock=unserialize($review->lock))
          <div class="form-group">
            {!! Form::label('lock',__('fleet.lock'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="lock" class="flat-red" value="1" @if($lock['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="lock" class="flat-red" value="0" @if($lock['flag'] == 0 && $lock['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="lock_text" style="width: 300px; margin-top:5px;" value="{{$lock['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($windows=unserialize($review->windows))
          <div class="form-group">
            {!! Form::label('windows',__('fleet.windows'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="windows" class="flat-red" value="1" @if($windows['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="windows" class="flat-red" value="0" @if($windows['flag'] == 0 && $windows['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="windows_text" style="width: 300px; margin-top:5px;" value="{{$windows['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($condition=unserialize($review->condition))
          <div class="form-group">
          {!! Form::label('condition',__('fleet.condition'), ['class' => 'form-label']) !!}
          <br>
          <input type="radio" name="condition" class="flat-red" value="1" @if($condition['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
          <input type="radio" name="condition" class="flat-red" value="0" @if($condition['flag'] == 0 && $condition['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
          <input type="text" name="condition_text" style="width: 300px; margin-top:5px;" value="{{$condition['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($oil_chk=unserialize($review->oil_chk))
          <div class="form-group">
            {!! Form::label('oil_chk',__('fleet.oil_chk'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="oil_chk" class="flat-red" value="1" @if($oil_chk['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="oil_chk" class="flat-red" value="0" @if($oil_chk['flag'] == 0 && $oil_chk['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="oil_chk_text" style="width: 300px; margin-top:5px;" value="{{$oil_chk['text']}}">
          </div>
        </div>

        <div class="col-md-6">
          @php($suspension=unserialize($review->suspension))
          <div class="form-group">
            {!! Form::label('suspension',__('fleet.suspension'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="suspension" class="flat-red" value="1" @if($suspension['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="suspension" class="flat-red" value="0" @if($suspension['flag'] == 0 && $suspension['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="suspension_text" style="width: 300px; margin-top:5px;" value="{{$suspension['text']}}">
          </div>
        </div>

        <div class="col-md-12">
          @php($tool_box=unserialize($review->tool_box))
          <div class="form-group">
            {!! Form::label('tool_box',__('fleet.tool_box'), ['class' => 'form-label']) !!}
            <br>
            <input type="radio" name="tool_box" class="flat-red" value="1" @if($tool_box['flag'] == 1) checked @endif> Yes &nbsp; &nbsp; &nbsp;
            <input type="radio" name="tool_box" class="flat-red" value="0" @if($tool_box['flag'] == 0 && $tool_box['flag'] != null) checked @endif> No &nbsp; &nbsp; &nbsp;
            <input type="text" name="tool_box_text" style="width: 300px; margin-top:5px;" value="{{$tool_box['text']}}">
          </div>
        </div>
        <div class="form-group col-md-6">
          {!! Form::label('image',__('fleet.select_image'), ['class' => 'form-label']) !!}
          {!! Form::file('image',['class'=>'form-control']) !!}
        {{-- @dd(asset('/uploads/'.$review->image)) --}}
          @if (!is_null($review->image) && file_exists('uploads/'.$review->image))
              <a href="{{ url('uploads/'.$review->image) }}" target="_blank">View</a>
          @endif
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
        @if($udfs != null)
        @foreach($udfs as $key => $value)
        <div class="row"><div class="col-md-8">  <div class="form-group"> <label class="form-label text-uppercase">{{$key}}</label> <input type="text" name="udf[{{$key}}]" class="form-control" required value="{{$value}}"></div></div><div class="col-md-4"> <div class="form-group" style="margin-top: 30px"><button class="btn btn-danger" type="button" onclick="this.parentElement.parentElement.parentElement.remove();">Remove</button> </div></div></div>
        @endforeach
        @endif
      </div>
      <div class="row">
        <div class="col-md-12">
          {!! Form::submit(__('fleet.submit'), ['class' => 'btn btn-warning']) !!}
        </div>
      </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
$(function(){

  $('#vehicle_id').select2({placeholder:"@lang('fleet.selectVehicle')"});
  $('#date_in').datetimepicker({format: 'YYYY-MM-DD HH:mm:ss',sideBySide: true,icons: {
    previous: 'fa fa-arrow-left',
              next: 'fa fa-arrow-right',
              up: "fa fa-arrow-up",
              down: "fa fa-arrow-down"
            }
          });
          $('#date_out').datetimepicker({format: 'YYYY-MM-DD HH:mm:ss',sideBySide: true,icons: {
            previous: 'fa fa-arrow-left',
            next: 'fa fa-arrow-right',
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down"
          }}).on('dp.change', function (e) { 

            var selectedDate = e.date;
            $('#date_in').data("DateTimePicker").minDate(e.date);
           });
          
         
          
        });

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