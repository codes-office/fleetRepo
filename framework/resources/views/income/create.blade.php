@extends('layouts.app')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')
@php($currency=Hyvikk::get('currency'))
@section('extra_css')
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">
<style type="text/css">
  .checkbox, #chk_all{
    width: 20px;
    height: 20px;
  }
</style>
@endsection
@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.manage_income')</li>
@endsection
@section('content')
@if (count($errors) > 0)
  <div class="alert alert-danger">
    <ul>
    @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
    </ul>
  </div>
@endif
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.addRecord')</h3>
      </div>
      <div class="card-body">
        <div class="row">
          {!! Form::open(['route' => 'income.store','method'=>'post','class'=>'form-inline','id'=>'income_form']) !!}

          <div class="col-md-3">
            <div class="form-group" style="margin-left:2px">
              {!! Form::label('vehicle_id', __('fleet.selectVehicle'), ['class' => 'col-xs-12 control-label']) !!}
              <div class="col-md-12">
                <select id="vehicle_id" name="vehicle_id" class="form-control vehicles" required style="width: 100%">
                  <option value="">@lang('fleet.selectVehicle')</option>
                  @foreach($vehicels as $vehicle)
                  <option value="{{ $vehicle->id }}" data-mileage="{{ $vehicle->mileage}}">{{$vehicle->make_name}}-{{$vehicle->model_name}}-{{$vehicle->license_plate}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-3" style=" margin-top: 5px;">
            <div class="form-group">
              {!! Form::label('income_type', __('fleet.incomeType'), ['class' => 'col-xs-12 control-label']) !!}
              <div class="col-md-12">
                <select id="income_type" name="income_type" class="form-control vehicles" required style="width: 100%">
                  <option value="">@lang('fleet.incomeType')</option>
                  @foreach($types as $type)
                  <option value="{{ $type->id }}">{{$type->name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('mileage', __('fleet.mileage'), ['class' => 'col-xs-12 control-label']) !!}
              <div class="col-md-12">
                <div class="input-group">
                  <div class="input-group-prepend">
                  <span class="input-group-text">{{Hyvikk::get('dis_format')}}</span></div>
                  <input required="required" name="mileage" type="number" id="mileage" class="form-control" min="0">
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('date', __('fleet.date'), ['class' => 'col-xs-12 control-label']) !!}
              <div class="col-md-12">
                <div class="input-group">
                  <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                  <input required="required" name="date" type="text" value="{{ date('Y-m-d')}}"  id="date" class="form-control">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3" style="margin-top: 5px;">
            <div class="form-group">
              {!! Form::label('revenue', __('fleet.amount'), ['class' => 'col-xs-5 control-label']) !!}
              <div class="col-xs-6">
            <div class="input-group">
              <div class="input-group-prepend">
              <span class="input-group-text">{{$currency}}</span></div>
              <input required="required" name="revenue" type="number" step="0.01" id="revenue" class="form-control">
            </div>
          </div>
        </div>
          </div>
          <div class="col-md-3" style="margin-top: 5px;">
            @php($tax_percent=0)
            @if(Hyvikk::get('tax_charge') != "null")
              @php($taxes = json_decode(Hyvikk::get('tax_charge'), true))
              @foreach($taxes as $key => $val)
              @php($tax_percent += $val )
              @endforeach
            @endif
            <div class="form-group">
              {!! Form::label('tax_percent', __('fleet.total_tax'). " (%)", ['class' => 'col-xs-5 control-label']) !!}
              <div class="col-xs-6">
                <div class="input-group">
                  <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-percent"></i></span></div>
                  <input name="tax_percent" type="text" id="tax_percent" class="form-control" readonly value="{{ $tax_percent }}">
                </div>
              </div>
            </div>

          </div>
          <div class="col-md-3" style=" margin-top: 5px;">
            <div class="form-group">
              {!! Form::label('tax_charge_rs', __('fleet.total')." ". __('fleet.tax_charge'), ['class' => 'col-xs-5 control-label']) !!}
              <div class="col-xs-6">
                <div class="input-group">
                  <div class="input-group-prepend">
                  <span class="input-group-text">{{$currency}}</span></div>
                  <input required="required" name="tax_charge_rs" type="text" id="tax_charge_rs" class="form-control" readonly value="0">
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3" style=" margin-top: 5px;">
            <div class="form-group">
              {!! Form::label('tax_total', __('fleet.total')." ". __('fleet.amount'), ['class' => 'col-xs-5 control-label']) !!}
              <div class="col-xs-6">
                <div class="input-group">
                  <div class="input-group-prepend">
                  <span class="input-group-text">{{$currency}}</span></div>
                  <input required="required" name="tax_total" type="text" id="tax_total" class="form-control" readonly>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6" style=" margin-top: 5px;">
            @can('Transactions add')<button type="submit" class="btn btn-success">@lang('fleet.add')</button>@endcan
          </div>
          {!!Form::close()!!}
        </div>
      </div>
    </div>
  </div>
</div>
