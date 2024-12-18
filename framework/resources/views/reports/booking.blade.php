@extends('layouts.app')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

@section("breadcrumb")
<li class="breadcrumb-item"><a href="#">@lang('menu.reports')</a></li>
<li class="breadcrumb-item active">@lang('fleet.booking_report')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.booking_report')
        </h3>
      </div>

      <div class="card-body">
        {!! Form::open(['route' => 'reports.booking','method'=>'post','class'=>'form-inline']) !!}
        <div class="row">
          <div class="form-group" style="margin-right: 5px">
            {!! Form::label('year', __('fleet.year1'), ['class' => 'form-label']) !!}
            <div class="form-group" style="margin-left:5px;">
            {!! Form::select('year', $years, $year_select,['class'=>'form-control']) !!}</div>
          </div>
          <div class="form-group" style="margin-right: 5px">
            {!! Form::label('month', __('fleet.month'), ['class' => 'form-label']) !!}
            <div class="form-group" style="margin-left:5px;">
            {!! Form::selectMonth('month',$month_select,['class'=>'form-control']) !!}</div>
          </div>
          <div class="form-group" style="margin-right: 5px">
            {!! Form::label('vehicle', __('fleet.vehicles'), ['class' => 'form-label']) !!}
            <div class="form-group" style="margin-left:5px;">
            <select id="vehicle_id" name="vehicle_id" class="form-control vehicles" style="width: 150px">
              <option value="">@lang('fleet.selectVehicle')</option>
              @foreach($vehicles as $vehicle)
              <option value="{{ $vehicle->id }}" @if($vehicle_select == $vehicle->id) selected @endif>{{$vehicle->make_name}}-{{$vehicle->model_name}}-{{$vehicle->license_plate}}</option>
              @endforeach
            </select></div>
          </div>
          <div class="form-group" style="margin-right: 5px">
            {!! Form::label('customer_id', __('fleet.selectCustomer'), ['class' => 'form-label']) !!}
            <div class="form-group" style="margin-left:5px;">
            <select id="customer_id" name="customer_id" class="form-control vehicles" style="width: 150px">
              <option value="">@lang('fleet.selectCustomer')</option>
              @foreach($customers as $customer)
              <option value="{{ $customer->id }}" @if($customer_select == $customer->id) selected @endif>{{$customer->name}}</option>
              @endforeach
            </select></div>
          </div>

          <button type="submit" class="btn btn-info" style="margin-right: 1px">@lang('fleet.generate_report')</button>
          <button type="submit" formaction="{{url('admin/print-booking-report')}}" class="btn btn-danger"><i class="fa fa-print"></i> @lang('fleet.print')</button>
        </div>
        {!! Form::close() !!}

      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
        @lang('fleet.booking_count') : {{$bookings->count()}}
        </h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table" id="myTable">
          <thead class="thead-inverse">
            <tr>
              <th>@lang('fleet.customer')</th>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.pickup_addr')</th>
              <th>@lang('fleet.dropoff_addr')</th>
              <th>@lang('fleet.from_date')</th>
              <th>@lang('fleet.to_date')</th>
              <th>@lang('fleet.passengers')</th>
              <th>@lang('fleet.status')</th>
            </tr>
          </thead>
          <tbody>
            @foreach($bookings as $row)
            <tr>
              <td>{{$row->customer->name}}</td>
              <td>
              @if($row->vehicle_id != null)
              {{$row->vehicle->make_name}} - {{$row->vehicle->model_name}} - {{$row->vehicle->license_plate}}
              @endif
              </td>
              <td style="width:10% !important">{!! str_replace(",", ",<br>", $row->pickup_addr) !!}</td>
              <td style="width:10% !important">{!! str_replace(",", ",<br>", $row->dest_addr) !!}</td>
              <td>{{date($date_format_setting.' g:i A',strtotime($row->pickup))}}</td>
              <td>{{date($date_format_setting.' g:i A',strtotime($row->dropoff))}}</td>
              <td>{{$row->travellers}}</td>
              <td>@if($row->status==0)<span style="color:orange;">@lang('fleet.journey_not_ended') @else <span style="color:green;">@lang('fleet.journey_ended') @endif</span></td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th>@lang('fleet.customer')</th>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.pickup_addr')</th>
              <th>@lang('fleet.dropoff_addr')</th>
              <th>@lang('fleet.from_date')</th>
              <th>@lang('fleet.to_date')</th>
              <th>@lang('fleet.passengers')</th>
              <th>@lang('fleet.status')</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection


@section("script")
<script type="text/javascript">
  $(document).ready(function() {
    $('#customer_id').select2();
    $('#vehicle_id').select2();
    $('#myTable tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input type="text" placeholder="'+title+'" />' );
    });
    var myTable = $('#myTable').DataTable( {
        dom: 'Bfrtip',
        buttons: [{
             extend: 'collection',
                text: '@lang('fleet.Export')',
                buttons: [
                    'copy',
                    'excel',
                    'csv',
                    'pdf',
                ]}
        ],

        "language": {
                 "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
              },
        "initComplete": function() {
                myTable.columns().every(function () {
                  var that = this;
                  $('input', this.footer()).on('keyup change', function () {
                      that.search(this.value).draw();
                  });
                });
              }
    });
  });
</script>
@endsection