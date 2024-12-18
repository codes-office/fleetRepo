@extends('layouts.app')
@section("breadcrumb")
<li><a href="#">@lang('menu.reports')</a></li>
<li class="active">@lang('menu.partsReport')</li>
@endsection
@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="box box-info">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('menu.partsReport')
				</h3>
			</div>

			<div class="box-body">
			{!! Form::open(['route' => 'reports.parts','method'=>'post','class'=>'form-inline']) !!}

			<div class="form-group">
				{!! Form::label('part', __('fleet.partName'), ['class' => 'form-label']) !!}
				<select id="part" name="part" class="form-control vehicles" required>
					<option value="" >Select Part</option>
					@foreach($parts as $part)
					<option value="{{ $part->id }}" >{{$part->description}}</option>
					</option>
					@endforeach
				</select>
			</div>
			<button type="submit" class="btn btn-info">@lang('fleet.generate_report')</button>
			{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@if(isset($result))
<div class="row">
	<div class="col-md-12">
		<div class="box box-info">
			<div class="box-header with-border">
				<h3 class="box-title">
				@lang('fleet.report')
				</h3>
			</div>

			<div class="box-body table-responsive">
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th>@lang('fleet.date')</th>
							<th>@lang('fleet.cost')</th>
							<th>@lang('fleet.vehicles')</th>
							<th>@lang('fleet.mileage')</th>
						</tr>
					</thead>
					<tbody>
					@foreach($parts2 as $part)
						<tr>
							<td>{{ $part->date }}</td>
							<td>{{ $part->cost }}</td>
							<td>{{ $part->vehicle->make_name }} {{ $part->vehicle->model_name }} {{ $part->vehicle->license_plate }}</td>
							<td>{{ $part->mileage}}</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>

			</div>
		</div>
	</div>
@endif

@endsection