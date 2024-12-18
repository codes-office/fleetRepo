@extends('layouts.app')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('bookings.index')}}">@lang('menu.bookings')</a></li>
<li class="breadcrumb-item active">@lang('fleet.booking_receipt')</li>
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
        <small class="float-right"> <b>@lang('fleet.date') : </b>{{
          date($date_format_setting,strtotime($i->booking_income->date)) }}</small>
      </h4>
    </div>
  </div>
  <div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
      <b>@lang('fleet.From')</b>
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
      <b>@if($booking->customer->getMeta('address') != null)@lang('fleet.To') @endif</b>
      <address>
        {!! nl2br(e($booking->customer->getMeta('address'))) !!}
      </address>
    </div>
    <div class="col-sm-4 invoice-col">
      <b>@lang('fleet.invoice')#</b>
      <b>@lang('fleet.tax_no')</b>
      {{Hyvikk::get('tax_no')}}
      <br>
      <b>Invoice#</b>
      {{ $i['income_id'] }}
      <br>
      <b>{{ $booking->customer->name }}</b>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6 invoice-col">
      <strong> @lang('fleet.pickup_addr'):</strong>
      <address>
        {{$booking->pickup_addr}}
        <br>
        @lang('fleet.pickup'):
        <b> {{date($date_format_setting.' g:i A',strtotime($booking->pickup))}}</b>
      </address>
    </div>
    <div class="col-sm-6 invoice-col">
      <strong>@lang('fleet.dropoff_addr'):</strong>
      <address>
        {{$booking->dest_addr}}
        <br>
        @lang('fleet.dropoff'):
        <b>{{date($date_format_setting.' g:i A',strtotime($booking->dropoff))}}</b>
      </address>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6"></div>
    <div class="col-md-6 pull-right">
      <p class="lead"></p>
      <div class="table-responsive">
        <table class="table">
          @if($booking->vehicle_id != null)
          <tr>
            <th style="width:50%">@lang('fleet.vehicle'):</th>
            <td>
              {{$booking->vehicle->make_name}} - {{$booking->vehicle->model_name}} -
              {{$booking->vehicle['license_plate']}}
            </td>
          </tr>
          @endif
          @if($booking->driver_id != null)
          <tr>
            <th>@lang('fleet.driver'):</th>
            <td>{{ $booking->driver->name }}</td>
          </tr>
          @endif
          <tr>
            <th>@lang('fleet.mileage'):</th>
            <td>{{ $i->booking_income->mileage }} {{ Hyvikk::get('dis_format') }}</td>
          </tr>
          <tr>
            <th>@lang('fleet.waitingtime'):</th>
            <td>
              {{ $booking->getMeta('waiting_time') }}
            </td>
          </tr>
          <tr>
            <th>@lang('fleet.amount'):</th>
            <td> {{ Hyvikk::get('currency') }} {{ $booking->total }}</td>
          </tr>
          <tr>
            <th>@lang('fleet.total_tax') (%) :</th>
            <td>{{ ($booking->total_tax_percent) ? $booking->total_tax_percent : 0 }} %</td>
          </tr>
          <tr>
            <th>@lang('fleet.total') @lang('fleet.tax_charge') :</th>
            <td>{{ Hyvikk::get('currency') }} {{ ($booking->total_tax_charge_rs) ? $booking->total_tax_charge_rs : 0 }}
            </td>
          </tr>
          <tr>
            <th>@lang('fleet.total'):</th>
            <td>{{ Hyvikk::get('currency') }} {{ $i->booking_income->amount }}</td>
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
      <a href="{{url('admin/print/'.$id)}}" target="_blank" class="btn btn-danger"><i class="fa fa-print"></i>
        @lang('fleet.print')</a>
    </div>
  </div>
</div>
@endsection