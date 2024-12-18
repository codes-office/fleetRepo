@extends('layouts.app')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

@section("breadcrumb")
<li class="breadcrumb-item"><a href="#">@lang('menu.reports')</a></li>
<li class="breadcrumb-item active">@lang('fleet.deliquentReport')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.deliquentReport')
        </h3>
      </div>

      <div class="card-body">
        {!! Form::open(['route' => 'reports.delinquent','method'=>'post','class'=>'form-inline']) !!}
        <div class="row">
          <div class="form-group" style="margin-right: 10px">
            {!! Form::label('year', __('fleet.year'), ['class' => 'form-label']) !!}
            <div class="form-group" style="margin-left:5px;">
            {!! Form::select('year', $years, $year_select,['class'=>'form-control']) !!}</div>
          </div>

          <div class="form-group" style="margin-right: 10px">
            {!! Form::label('month', __('fleet.month'), ['class' => 'form-label']) !!}
            <div class="form-group" style="margin-left:5px;">
            {!! Form::selectMonth('month',$month_select,['class'=>'form-control']) !!}</div>
          </div>

          <div class="form-group" style="margin-right: 10px">
            {!! Form::label('vehicle', __('fleet.vehicles'), ['class' => 'form-label']) !!}
            <div class="form-group" style="margin-left:5px;">
            <select id="vehicle_id" name="vehicle_id" class="form-control vehicles" style="width: 250px" required>
              <option value="">@lang('fleet.selectVehicle')</option>
              @foreach($vehicles as $vehicle)
              <option value="{{ $vehicle->id }}" @if($vehicle->id==$vehicle_id) selected @endif>{{$vehicle->make_name}}-{{$vehicle->model_name}}-{{$vehicle->license_plate}}</option>
              @endforeach
            </select></div>
          </div>
          <button type="submit" class="btn btn-info" style="margin-right: 10px">@lang('fleet.generate_report')</button>
          <button type="submit" formaction="{{url('admin/print-deliquent-report')}}" class="btn btn-danger" style="margin-right: 10px"><i class="fa fa-print"></i> @lang('fleet.print')</button>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>

@if(isset($result))
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
        @lang('fleet.report')
        </h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped table-hover"  id="myTable">
          <thead>
            <tr>
              <th>@lang('fleet.day')</th>
              <th>@lang('fleet.date')</th>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.income')</th>
            </tr>
          </thead>
          <tbody>
          @foreach($data as $row)
            <tr>
              <td>{{$row->day}}</td>
              <td>{{date($date_format_setting,strtotime($row->date))}}</td>
              <td>{{$v[$row->vehicle_id]->make_name}}-{{$v[$row->vehicle_id]->model_name}}-{{$v[$row->vehicle_id]['license_plate']}}</td>
              <td>{{Hyvikk::get('currency')}} {{$row->Income2}}</td>
            </tr>
          @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th>@lang('fleet.day')</th>
              <th>@lang('fleet.date')</th>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.income')</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@section("script")
<script type="text/javascript">
	$(document).ready(function() {
    $("#vehicle_id").select2();
		$('#myTable tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input type="text" placeholder="'+title+'" />' );
    });
    var myTable = $('#myTable').DataTable( {
        dom: 'Bfrtip',
        buttons: [{
             extend: 'collection',
                text: 'Export',
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
              // individual column search
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