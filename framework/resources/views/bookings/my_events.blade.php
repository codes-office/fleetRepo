@foreach($bookings as $booking)
<table class="table table-striped">
	<th>@lang('fleet.booking_id') : {{$booking->id}}</th>
	<tr>
	<th>@lang('fleet.customer')</th>
	<td>{{ $booking->customer->name}}</td>
	</tr>
	<tr>
	<th>@lang('fleet.vehicle')</th>
	@if($booking->vehicle_id != null)
	<td>{{ $booking->vehicle->make_name}} - {{ $booking->vehicle->model_name}} - {{ $booking->vehicle->license_plate}}</td>
	@endif
	</tr>
	<tr>
	<th>@lang('fleet.travellers')</th>

	<td>{{ $booking->travellers}}</td>
	</tr>
	<tr>
	<th>@lang('fleet.note')</th>
	<td>{{ $booking->note}}</td>
	</tr>

	<tr>
	<th>@lang('fleet.pickup')</th>
	<td>{{date(Hyvikk::get('date_format').' g:i A',strtotime($booking->pickup))}}</td>
	</tr>

	<tr>
	<th>@lang('fleet.dropoff')</th>
	<td>{{date(Hyvikk::get('date_format').' g:i A',strtotime($booking->dropoff))}}</td>
	</tr>

	<tr>
	<th>@lang('fleet.pickup_addr')</th>
	<td>{{ $booking->pickup_addr}}</td>
	</tr>
	<tr>
	<th>@lang('fleet.dest_addr')</th>
	<td>{{ $booking->dest_addr}}</td>
	</tr>
</table>
<hr>
@endforeach