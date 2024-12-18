@extends('layouts.app')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('booking-quotation.index')}}">@lang('fleet.booking_quotes')</a></li>
<li class="breadcrumb-item active">@lang('fleet.receipt')</li>
@endsection
@section('content')
<div class="invoice p-3 mb-3">
  <div class="row">
    <div class="col-12">
      <h4>
        <span class="logo-lg">
          <img src="{{ asset('assets/images/'. Hyvikk::get('icon_img') ) }}" class="navbar-brand"
            style="margin-top: -15px">
          {{ Hyvikk::get('app_name') }}
        </span>
        <small class="float-right"> <b>@lang('fleet.date') : </b>{{ date($date_format_setting) }}</small>
      </h4>
    </div>
  </div>
  <div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
      <b>From</b>
      <address>
        {{Hyvikk::get('badd1')}}
        <br>
        {{Hyvikk::get('badd2')}}
        <br>
        {{Hyvikk::get('city')}},

        {{Hyvikk::get('state')}}
        <br>
        {{Hyvikk::get('country')}}
      </address>
    </div>
    <div class="col-sm-4 invoice-col">
      <b>@if($data->customer->getMeta('address') != null) To @endif</b>
      <address>
        {!! nl2br(e($data->customer->getMeta('address'))) !!}
      </address>
    </div>
    <div class="col-sm-4 invoice-col">
      <b>@lang('fleet.bookingQuote')#</b>
      {{ $data->id }}
      <br>
      <b>{{ $data->customer->name }}</b>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6 invoice-col">
      <strong> @lang('fleet.pickup_addr'):</strong>
      <address>
        {{$data->pickup_addr}}
        <br>
        @lang('fleet.pickup'):
        <b> {{date($date_format_setting.' g:i A',strtotime($data->pickup))}}</b>
      </address>
    </div>
    <div class="col-sm-6 invoice-col">
      <strong>@lang('fleet.dropoff_addr'):</strong>
      <address>
        {{$data->dest_addr}}
        <br>
        @lang('fleet.dropoff'):
        <b>{{date($date_format_setting.' g:i A',strtotime($data->dropoff))}}</b>
      </address>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6"></div>
    <div class="col-md-6 pull-right">
      <p class="lead"></p>
      <div class="table-responsive">
        <table class="table">
          @if($data->vehicle_id != null)
          <tr>
            <th style="width:50%">@lang('fleet.vehicle'):</th>
            <td>
              {{$data->vehicle->make_name}} - {{$data->vehicle->model_name}} -
              {{$data->vehicle['license_plate']}}
            </td>
          </tr>
          @endif
          @if($data->driver_id != null)
          <tr>
            <th>@lang('fleet.driver'):</th>
            <td>{{ $data->driver->name }}</td>
          </tr>
          @endif
          <tr>
            <th>@lang('fleet.mileage'):</th>
            <td>{{ $data->mileage }} {{ Hyvikk::get('dis_format') }}</td>
          </tr>
          <tr>
            <th>@lang('fleet.waitingtime'):</th>
            <td>
              {{ $data->waiting_time }}
            </td>
          </tr>
          <tr>
            <th>@lang('fleet.amount'):</th>
            <td>{{ Hyvikk::get('currency') }} {{ $data->total }}</td>
          </tr>
          <tr>
            <th>@lang('fleet.total_tax') (%) :</th>
            <td>{{ ($data->total_tax_percent) ? $data->total_tax_percent : 0 }} %</td>
          </tr>
          <tr>
            <th>@lang('fleet.total') @lang('fleet.tax_charge') :</th>
            <td>{{ Hyvikk::get('currency') }} {{ ($data->total_tax_charge_rs) ? $data->total_tax_charge_rs : 0 }} </td>
          </tr>
          <tr>
            <th>@lang('fleet.total'):</th>
            <td>{{ Hyvikk::get('currency') }} {{ ($data->tax_total) ? $data->tax_total : $data->total }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
        {{Hyvikk::get('invoice_text')}}
      </p>
    </div>
  </div>
  <div class="row no-print">
    <div class="col-xs-12">
      <a href="{{url('admin/print-quote/'.$data->id)}}" target="_blank" class="btn btn-danger"><i
          class="fa fa-print"></i> @lang('fleet.print')</a>
    </div>
  </div>
</div>
@endsection