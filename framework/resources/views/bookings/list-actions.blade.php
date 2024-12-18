<div class="btn-group">
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
      <span class="fa fa-gear"></span>
      <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu custom" role="menu">
      @if($row->status==0 && $row->ride_status != "Cancelled")
      @can('Bookings edit')<a class="dropdown-item" href="{{ url('admin/bookings/'.$row->id.'/edit')}}"> <span aria-hidden="true" class="fa fa-edit" style="color: #f0ad4e;"></span> @lang('fleet.edit')</a>@endcan
      @if($row->receipt != 1)
      <a class="dropdown-item vtype" data-id="{{$row->id}}" data-toggle="modal" data-target="#cancelBooking" > <span class="fa fa-times" aria-hidden="true" style="color: #dd4b39"></span> @lang('fleet.cancel_booking')</a>
      @endif
      @endif
      @can('Bookings delete')<a class="dropdown-item vtype" data-id="{{$row->id}}" data-toggle="modal" data-target="#myModal" > <span class="fa fa-trash" aria-hidden="true" style="color: #dd4b39"></span> @lang('fleet.delete')</a>@endcan
      @if($row->vehicle_id != null)
      @if($row->status==0 && $row->receipt != 1)
      @if(Auth::user()->user_type != "C" && $row->ride_status != "Cancelled")
      <a data-toggle="modal" data-target="#receiptModal" class="open-AddBookDialog dropdown-item" data-booking-id="{{$row->id}}" data-user-id="{{$row->user_id}}" data-customer-id="{{$row->customer_id}}" data-vehicle-id= "{{$row->vehicle_id}}" data-vehicle-type="{{strtolower(str_replace(' ','',$row->vehicle->types->vehicletype))}}" data-base-mileage="{{($row->total_kms)?$row->total_kms:Hyvikk::fare(strtolower(str_replace(' ','',$row->vehicle->types->vehicletype)).'_base_km')}}" data-base-fare="{{($row->total)?$row->total:Hyvikk::fare(strtolower(str_replace(' ','',$row->vehicle->types->vehicletype)).'_base_fare')}}"
      data-base_km_1="{{Hyvikk::fare(strtolower(str_replace(' ','',$row->vehicle->types->vehicletype)).'_base_km')}}"
      data-base_fare_1="{{Hyvikk::fare(strtolower(str_replace(' ','',$row->vehicle->types->vehicletype)).'_base_fare')}}"
      data-wait_time_1="{{Hyvikk::fare(strtolower(str_replace(' ','',$row->vehicle->types->vehicletype)).'_base_time')}}"
      data-std_fare_1="{{Hyvikk::fare(strtolower(str_replace(' ','',$row->vehicle->types->vehicletype)).'_std_fare')}}"

      data-base_km_2="{{Hyvikk::fare(strtolower(str_replace(' ','',$row->vehicle->types->vehicletype)).'_weekend_base_km')}}"
      data-base_fare_2="{{Hyvikk::fare(strtolower(str_replace(' ','',$row->vehicle->types->vehicletype)).'_weekend_base_fare')}}"
      data-wait_time_2="{{Hyvikk::fare(strtolower(str_replace(' ','',$row->vehicle->types->vehicletype)).'_weekend_wait_time')}}"
      data-std_fare_2="{{Hyvikk::fare(strtolower(str_replace(' ','',$row->vehicle->types->vehicletype)).'_weekend_std_fare')}}"

      data-base_km_3="{{Hyvikk::fare(strtolower(str_replace(' ','',$row->vehicle->types->vehicletype)).'_night_base_km')}}"
      data-base_fare_3="{{Hyvikk::fare(strtolower(str_replace(' ','',$row->vehicle->types->vehicletype)).'_night_base_fare')}}"
      data-wait_time_3="{{Hyvikk::fare(strtolower(str_replace(' ','',$row->vehicle->types->vehicletype)).'_night_wait_time')}}"
      data-std_fare_3="{{Hyvikk::fare(strtolower(str_replace(' ','',$row->vehicle->types->vehicletype)).'_night_std_fare')}}"
      ><span aria-hidden="true" class="fa fa-file" style="color: #5cb85c;">

      </span> @lang('fleet.invoice')
      </a>
      @endif
      @elseif($row->receipt == 1)
      <a class="dropdown-item" href="{{ url('admin/bookings/receipt/'.$row->id)}}"><span aria-hidden="true" class="fa fa-list" style="color: #31b0d5;"></span> @lang('fleet.receipt')
      </a>
      @if($row->receipt == 1 && $row->status == 0 && Auth::user()->user_type != "C")
      <a class="dropdown-item" href="{{ url('admin/bookings/complete/'.$row->id)}}" data-id="{{ $row->id }}" data-toggle="modal" data-target="#journeyModal"><span aria-hidden="true" class="fa fa-check" style="color: #5cb85c;"></span> @lang('fleet.complete')
      </a>
      @endif
      @endif
      @endif

      @if($row->status==1)
      @if($row->payment==0 && Auth::user()->user_type !="C")
      <a class="dropdown-item" href="{{ url('admin/bookings/payment/'.$row->id)}}"><span aria-hidden="true" class="fa fa-credit-card" style="color: #5cb85c;"></span> @lang('fleet.make_payment')
      </a>
      @elseif($row->payment==1)
      <a class="dropdown-item text-muted" class="disabled"><span aria-hidden="true" class="fa fa-credit-card" style="color: #5cb85c;"></span> @lang('fleet.paid')
      </a>
      @endif
      @endif
    </div>
</div>
{!! Form::open(['url' => 'admin/bookings/'.$row->id,'method'=>'DELETE','class'=>'form-horizontal','id'=>'book_'.$row->id]) !!}
{!! Form::hidden("id",$row->id) !!}
{!! Form::close() !!}