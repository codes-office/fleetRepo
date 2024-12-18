@extends('layouts.app')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

@section("breadcrumb")
<li class="breadcrumb-item active">@lang('menu.my_bookings')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('menu.my_bookings')</h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table" id="data_table">
          <thead class="thead-inverse">
            <tr>
              <th>@lang('fleet.id')</th>
              <th>@lang('fleet.customer')</th>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.pickup')</th>
              <th>@lang('fleet.dropoff')</th>
              <th>@lang('fleet.pickup_addr')</th>
              <th>@lang('fleet.dropoff_addr')</th>
              <th>@lang('fleet.passengers')</th>
            </tr>
          </thead>
          <tbody>
            @foreach($data as $row)
            <tr>
              <td>{{ $row->id }}</td>
              <td>{{$row->customer->name}}</td>
              <td>{{$row->vehicle->make_name}} - {{$row->vehicle->model_name}} - {{$row->vehicle['license_plate']}}</td>
              <td>
              @if($row->pickup != null)
              {{date($date_format_setting.' g:i A',strtotime($row->pickup))}}
              @endif
              </td>
              <td>
              @if($row->dropoff != null)
              {{date($date_format_setting.' g:i A',strtotime($row->dropoff))}}
              @endif
              </td>
              <td>{{$row->pickup_addr}}</td>
              <td>{{$row->dest_addr}}</td>
              <td>{{$row->travellers}}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th></th>
              <th>@lang('fleet.customer')</th>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.pickup')</th>
              <th>@lang('fleet.dropoff')</th>
              <th>@lang('fleet.pickup_addr')</th>
              <th>@lang('fleet.dropoff_addr')</th>
              <th>@lang('fleet.Passengers')</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection