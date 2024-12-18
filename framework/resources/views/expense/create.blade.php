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
<li class="breadcrumb-item active">@lang('fleet.expense')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.addRecord')
        </h3>
      </div>

      <div class="card-body">
        <div class="row">
          @if (count($errors) > 0)
          <div class="alert alert-danger col-md-12">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif
          {!! Form::open(['route' => 'expense.store','method'=>'post','class'=>'form-inline','id'=>'exp_form']) !!}

          <div class="col-md-4 col-sm-6">
            <select id="vehicle_id" name="vehicle_id" class="form-control vehicles" style="width: 100%" required>
              <option value="" >@lang('fleet.selectVehicle')</option>
              @foreach($vehicels as $vehicle)
              <option value="{{ $vehicle->id }}">{{$vehicle->make_name}}-{{$vehicle->model_name}}-{{$vehicle->license_plate}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4" style="margin-top: 5px;">
            <select id="expense_type" name="expense_type" class="form-control vehicles" required  style="width: 100%">
              <option value="" >@lang('fleet.expenseType')</option>
              @foreach($types as $type)
              <option value="e_{{ $type->id }}">{{$type->name}}</option>
              @endforeach
              <optgroup label="@lang('fleet.serviceItems')">
              @foreach($service_items as $item)
              <option value="s_{{ $item->id }}">{{$item->description}}</option>
              @endforeach
              </optgroup>
            </select>
          </div>
          <div class="col-md-4" style="margin-top: 5px">
            <select id="vendor_id" name="vendor_id" class="form-control vendor" style="width: 100%">
              <option value="">@lang('fleet.select_vendor')</option>
              @foreach($vendors as $vendor)
              <option value="{{ $vendor->id }}">{{$vendor->name}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4" style="margin-top: 5px;">
            <div class="input-group">
              <div class="input-group-prepend">
              <span class="input-group-text">{{$currency}}</span></div>
              <input required="required" name="revenue" type="number" step="0.01" id="revenue" class="form-control">
            </div>
          </div>
          <div class="col-md-4" style="margin-top: 10px;">
            <div class="input-group">
              <input  name="comment" type="text" id="comment" class="form-control" placeholder=" @lang('fleet.note')" style="width: 250px">
            </div>
          </div>
          <div class="col-md-3" style="margin-top: 10px;">
            <div class="input-group">
              <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
              <input  name="date" type="text"  id="date" value="{{ date('Y-m-d')}}" class="form-control">
            </div>
          </div>
          <div class="col-md-1" style="margin-top: 10px;">
            @can('Transactions add')<button type="submit" class="btn btn-success">@lang('fleet.add')</button>@endcan
          </div>
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
</div>
