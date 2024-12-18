@extends('layouts.app')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

@section("breadcrumb")
<li class="breadcrumb-item"><a href="#">@lang('menu.reports')</a></li>
<li class="breadcrumb-item active">@lang('fleet.fuelReport')</li>
@endsection
@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card card-info">
			<div class="card-header">
				<h3 class="card-title">@lang('fleet.fuelReport')
				</h3>
			</div>

			<div class="card-body">
				{!! Form::open(['route' => 'reports.fuel','method'=>'post','class'=>'form-inline']) !!}
				<div class="row">
					<div class="form-group" style="margin-right: 10px">
						{!! Form::label('year', __('fleet.year1'), ['class' => 'form-label']) !!}
						<div class="form-group" style="margin-left:5px;">
						{!! Form::select('year', $years, $year_select,['class'=>'form-control']) !!}</div>
					</div>

					<div class="form-group" style="margin-right: 10px">
						{!! Form::label('month', __('fleet.month'), ['class' => 'form-label']) !!}
						<div class="form-group" style="margin-left:5px;">
						<select name="month" id="month" class="form-control">
							<option value="0" @if($month_select == '0') selected @endif>all</option>
							<option value="1" @if($month_select == '1') selected @endif>January</option>
							<option value="2" @if($month_select == '2') selected @endif>February</option>
							<option value="3" @if($month_select == '3') selected @endif>March</option>
							<option value="4" @if($month_select == '4') selected @endif>April</option>
							<option value="5" @if($month_select == '5') selected @endif>May</option>
							<option value="6" @if($month_select == '6') selected @endif>June</option>
							<option value="7" @if($month_select == '7') selected @endif>July</option>
							<option value="8" @if($month_select == '8') selected @endif>August</option>
							<option value="9" @if($month_select == '9') selected @endif>September</option>
							<option value="10" @if($month_select == '10') selected @endif>October</option>
							<option value="11" @if($month_select == '11') selected @endif>November</option>
							<option value="12" @if($month_select == '12') selected @endif>December</option>
						</select></div>
					</div>

					<div class="form-group" style="margin-right: 10px">
						{!! Form::label('vehicle', __('fleet.vehicles'), ['class' => 'form-label']) !!}
						<div class="form-group" style="margin-left:5px;">
						<select id="vehicle_id" name="vehicle_id" class="form-control vehicles" required style="width: 250px;">
							<option value="">@lang('fleet.selectVehicle')</option>
							@foreach($vehicles as $vehicle)
							<option value="{{ $vehicle['id'] }}" @if($vehicle['id']==$vehicle_id) selected @endif>{{$vehicle->make_name}}-{{$vehicle->model_name}}-{{$vehicle['license_plate']}}</option>
							@endforeach
						</select></div>
					</div>
					<button type="submit" class="btn btn-info" style="margin-right: 10px">@lang('fleet.generate_report')</button>
					<button type="submit" formaction="{{url('admin/print-fuel-report')}}" class="btn btn-danger"><i class="fa fa-print"></i> @lang('fleet.print')</button>
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
				Fuel Report
				</h3>
			</div>

			<div class="card-body table-responsive">
				<table class="table table-bordered table-striped table-hover"  id="myTable">
					<thead>
						<tr>
							<th>@lang('fleet.date')</th>
							<th>@lang('fleet.vehicle')</th>
							<th>@lang('fleet.meter')</th>
							<th>@lang('fleet.qty')</th>
							<th>@lang('fleet.consumption')</th>
							<th>@lang('fleet.cost')</th>
						</tr>
					</thead>
					<tbody>
					@foreach($fuel as $f)
						<tr>
							<td>{{date($date_format_setting,strtotime($f->date))}}</td>
							<td>{{$f->vehicle_data->make_name}}-{{$f->vehicle_data->model_name}}-{{$f->vehicle_data->license_plate}}</td>
							<td>
							<b> @lang('fleet.start'): </b>{{$f->start_meter}} {{Hyvikk::get('dis_format')}}
							<br>
							<b> @lang('fleet.end'):</b>{{$f->end_meter}} {{Hyvikk::get('dis_format')}}
							</td>
							<td>
								{{ $f->qty }} {{Hyvikk::get('fuel_unit')}}
							</td>
							<td>{{$f->consumption}}
								@if(Hyvikk::get('dis_format') == "km")
				                 @if(Hyvikk::get('fuel_unit') == "gallon")KMPG @else KMPL @endif
				                @else
				                 @if(Hyvikk::get('fuel_unit') == "gallon")MPG @else MPL @endif
				                @endif
							</td>
							<td>{{Hyvikk::get('currency')}} {{$f->qty * $f->cost_per_unit}}</td>
						</tr>
					@endforeach
					</tbody>
					<tfoot>
						<tr>
							<th>@lang('fleet.date')</th>
							<th>@lang('fleet.vehicle')</th>
							<th>@lang('fleet.meter')</th>
							<th>@lang('fleet.qty')</th>
							<th>@lang('fleet.consumption')</th>
							<th>@lang('fleet.cost')</th>
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