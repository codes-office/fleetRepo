@extends("layouts.app")
@section('extra_css')
<style type="text/css">
  .custom{
  min-width: 50px;
  left: -105px !important;
  right: 0;
}
</style>
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ url('admin/vehicle-inspection')}}">@lang('fleet.vehicle_inspection')</a></li>
<li class="breadcrumb-item active">@lang('fleet.view_vehicle_inspection')</li>
@endsection

@section('content')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.vehicle_inspection') : {{$review->vehicle->make_name}} - {{$review->vehicle->model_name}} - {{$review->vehicle->types['displayname']}}&nbsp; <a href="{{url('admin/print-vehicle-inspection/'.$review->id)}}" class="btn btn-danger"><i class="fa fa-print"></i>&nbsp; @lang('fleet.print')</a></h3>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('vehicle_id',__('fleet.vehicle')." : ", ['class' => 'form-label']) !!}
              {{$review->vehicle->make_name}} - {{$review->vehicle->model_name}} - {{$review->vehicle->types['displayname']}}
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('reg_no',__('fleet.reg_no')." : ", ['class' => 'form-label']) !!}
              {{$review->reg_no}}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('kms_out',__('fleet.kms_out')." (".Hyvikk::get('dis_format').") : ", ['class' => 'form-label']) !!}
              {{$review->kms_outgoing}} {{Hyvikk::get('dis_format')}}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('kms_in',__('fleet.kms_in')." (".Hyvikk::get('dis_format').") : ", ['class' => 'form-label']) !!}
              {{$review->kms_incoming}} {{Hyvikk::get('dis_format')}}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('fuel_out',__('fleet.fuel_out')." : ", ['class' => 'form-label']) !!}
              @if($review->fuel_level_out == 0) 1/4 @endif
              @if($review->fuel_level_out == 1) 1/2 @endif
              @if($review->fuel_level_out == 2) 3/4 @endif
              @if($review->fuel_level_out == 3) Full Tank @endif
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('fuel_in',__('fleet.fuel_in')." : ", ['class' => 'form-label']) !!}
              @if($review->fuel_level_in == 0) 1/4 @endif
              @if($review->fuel_level_in == 1) 1/2 @endif
              @if($review->fuel_level_in == 2) 3/4 @endif
              @if($review->fuel_level_in == 3) Full Tank @endif
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('datetime_out',__('fleet.datetime_out')." : ", ['class' => 'form-label']) !!}
              {{date($date_format_setting.' g:i A',strtotime($review->datetime_outgoing))}}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('datetime_in',__('fleet.datetime_in')." : ", ['class' => 'form-label']) !!}
              {{date($date_format_setting.' g:i A',strtotime($review->datetime_incoming))}}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            @php($petrol_card=unserialize($review->petrol_card))
            <div class="form-group">
              {!! Form::label('petrol_card',__('fleet.petrol_card')." : ", ['class' => 'form-label']) !!}
              @if($petrol_card['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($petrol_card['flag'] == 0 && $petrol_card['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp;{{$petrol_card['text']}}
            </div>
          </div>

          <div class="col-md-6">
            @php($light=unserialize($review->lights))
            <div class="form-group">
              {!! Form::label('lights',__('fleet.lights')." : ", ['class' => 'form-label']) !!}
              @if($light['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($light['flag'] == 0 && $light['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$light['text']}}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            @php($invertor=unserialize($review->invertor))
            <div class="form-group">
              {!! Form::label('invertor',__('fleet.invertor')." : ", ['class' => 'form-label']) !!}
              @if($invertor['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($invertor['flag'] == 0 && $invertor['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$invertor['text']}}
            </div>
          </div>

          <div class="col-md-6">
            @php($car_mat=unserialize($review->car_mats))
            <div class="form-group">
              {!! Form::label('car_mats',__('fleet.car_mats')." : ", ['class' => 'form-label']) !!}
              @if($car_mat['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($car_mat['flag'] == 0 && $car_mat['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$car_mat['text']}}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            @php($int_damage=unserialize($review->int_damage))
            <div class="form-group">
              {!! Form::label('int_damage',__('fleet.int_damage')." : ", ['class' => 'form-label']) !!}
              @if($int_damage['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($int_damage['flag'] == 0 && $int_damage['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$int_damage['text']}}
            </div>
          </div>

          <div class="col-md-6">
            @php($int_lights=unserialize($review->int_lights))
            <div class="form-group">
              {!! Form::label('int_lights',__('fleet.int_lights')." : ", ['class' => 'form-label']) !!}
              @if($int_lights['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($int_lights['flag'] == 0 && $int_lights['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$int_lights['text']}}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            @php($ext_car=unserialize($review->ext_car))
            <div class="form-group">
              {!! Form::label('ext_car',__('fleet.ext_car')." : ", ['class' => 'form-label']) !!}
              @if($ext_car['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($ext_car['flag'] == 0 && $ext_car['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$ext_car['text']}}
            </div>
          </div>

          <div class="col-md-6">
            @php($tyre=unserialize($review->tyre))
            <div class="form-group">
            {!! Form::label('tyre',__('fleet.tyre')." : ", ['class' => 'form-label']) !!}
            @if($tyre['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
            @if($tyre['flag'] == 0 && $tyre['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
            &nbsp; {{$tyre['text']}}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            @php($ladder=unserialize($review->ladder))
            <div class="form-group">
              {!! Form::label('ladder',__('fleet.ladder')." : ", ['class' => 'form-label']) !!}
              @if($ladder['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($ladder['flag'] == 0 && $ladder['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$ladder['text']}}
            </div>
          </div>

          <div class="col-md-6">
            @php($leed=unserialize($review->leed))
            <div class="form-group">
              {!! Form::label('leed',__('fleet.leed')." : ", ['class' => 'form-label']) !!}
              @if($leed['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($leed['flag'] == 0 && $leed['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$leed['text']}}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            @php($power_tool=unserialize($review->power_tool))
            <div class="form-group">
              {!! Form::label('power_tool',__('fleet.power_tool')." : ", ['class' => 'form-label']) !!}
              @if($power_tool['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($power_tool['flag'] == 0 && $power_tool['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$power_tool['text']}}
            </div>
          </div>

          <div class="col-md-6">
            @php($ac=unserialize($review->ac))
            <div class="form-group">
              {!! Form::label('ac',__('fleet.ac')." : ", ['class' => 'form-label']) !!}
              @if($ac['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($ac['flag'] == 0 && $ac['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$ac['text']}}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            @php($head_light=unserialize($review->head_light))
            <div class="form-group">
              {!! Form::label('head_light',__('fleet.head_light')." : ", ['class' => 'form-label']) !!}
              @if($head_light['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($head_light['flag'] == 0 && $head_light['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$head_light['text']}}
            </div>
          </div>

          <div class="col-md-6">
            @php($lock=unserialize($review->lock))
            <div class="form-group">
              {!! Form::label('lock',__('fleet.lock')." : ", ['class' => 'form-label']) !!}
              @if($lock['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($lock['flag'] == 0 && $lock['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$lock['text']}}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            @php($windows=unserialize($review->windows))
            <div class="form-group">
              {!! Form::label('windows',__('fleet.windows')." : ", ['class' => 'form-label']) !!}
              @if($windows['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($windows['flag'] == 0 && $windows['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$windows['text']}}
            </div>
          </div>

          <div class="col-md-6">
            @php($condition=unserialize($review->condition))
            <div class="form-group">
              {!! Form::label('condition',__('fleet.condition')." : ", ['class' => 'form-label']) !!}
              @if($condition['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($condition['flag'] == 0 && $condition['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$condition['text']}}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            @php($oil_chk=unserialize($review->oil_chk))
            <div class="form-group">
              {!! Form::label('oil_chk',__('fleet.oil_chk')." : ", ['class' => 'form-label']) !!}
              @if($oil_chk['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($oil_chk['flag'] == 0 && $oil_chk['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$oil_chk['text']}}
            </div>
          </div>

          <div class="col-md-6">
            @php($suspension=unserialize($review->suspension))
            <div class="form-group">
              {!! Form::label('suspension',__('fleet.suspension')." : ", ['class' => 'form-label']) !!}
              @if($suspension['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($suspension['flag'] == 0 && $suspension['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$suspension['text']}}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            @php($tool_box=unserialize($review->tool_box))
            <div class="form-group">
              {!! Form::label('tool_box',__('fleet.tool_box')." : ", ['class' => 'form-label']) !!}
              @if($tool_box['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
              @if($tool_box['flag'] == 0 && $tool_box['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
              &nbsp; {{$tool_box['text']}}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection