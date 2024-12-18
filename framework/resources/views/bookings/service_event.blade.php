<table class="table table-striped">
	<tr>
		<th>@lang('fleet.vehicle')</th>
		<td> {{$service->vehicle->make_name}}-{{$service->vehicle->model_name}}-{{$service->vehicle->license_plate}}</td>
	</tr>
	<tr>
		<th>@lang('fleet.service_item')</th>
		<td>{{$service->services['description']}}</td>
	</tr>
	<tr>
		<th>@lang('fleet.next_due') (@lang('fleet.meter'))</th>
		<td>
			@if($service->last_meter == 0)
             {{$service->vehicle->int_mileage + $service->services->overdue_meter}} {{Hyvikk::get('dis_format')}}
	        @else
	            {{$service->last_meter + $service->services->overdue_meter}} {{Hyvikk::get('dis_format')}}
	        @endif
		</td>
	</tr>
	<tr>
		<th>@lang('fleet.next_due') (@lang('fleet.date'))</th>
		<td>
			@php($interval = substr($service->services->overdue_unit,0,-3))
              @php($int = $service->services->overdue_time.$interval)

              @if($service->last_date != 'N/D')
                 @php($date = date('Y-m-d', strtotime($int, strtotime($service->last_date)))) 
                @else
                 @php($date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))))) 
                @endif
				{{ date(Hyvikk::get('date_format'),strtotime($date)) }}
		</td>
	</tr>
	<tr>
		<th> @lang('fleet.start_date') / @lang('fleet.last_performed')</th>
		<td>
          @lang('fleet.start_date'): {{date(Hyvikk::get('date_format'),strtotime($service->last_date))}}
          <br>
          @lang('fleet.last_performed') @lang('fleet.meter'): {{$service->last_meter}}
        </td>
	</tr>
	<tr>
		<th>@lang('fleet.interval')</th>
		<td>
			{{$service->services->overdue_time}} {{$service->services->overdue_unit}}
			@if($service->services->overdue_meter != null)
			@lang('fleet.or') {{$service->services->overdue_meter}} {{Hyvikk::get('dis_format')}}
			@endif
		</td>
	</tr>
</table>