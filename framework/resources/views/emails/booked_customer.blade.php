@component('mail::message')
# Ride Booked.

Dear {{$booking->customer->name}},

	Your Ride is booked for your journey. Below are the details for your journey.

@component('mail::table')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

<table>
	@if($booking->vehicle_id)
	<tr><td>Vehicle: </td><td>{{$booking->vehicle->make_name}} {{$booking->vehicle->model_name}}</td></tr>
	<tr><td>Vehicle Licence Plate: </td><td>{{$booking->vehicle->license_plate}}</td></tr>
	@endif
	<tr><td>Journey Date: </td><td>{{date($date_format_setting,strtotime($booking->pickup))}}</td></tr>
	<tr><td>Pickup Time: </td><td>{{date('g:i A',strtotime($booking->pickup))}}</td></tr>
	<tr><td>Pickup Address: </td><td>{{$booking->pickup_addr}}</td></tr>
	<tr><td>Destination Address: </td><td>{{$booking->dest_addr}}</td></tr>
	<tr><td>Travellers: </td><td>{{$booking->travellers}}</td></tr>

</table>
@endcomponent

We Wish you a happy journey.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
