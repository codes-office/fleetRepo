@extends('layouts.app')
@section('extra_css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap-datetimepicker.min.css')}}">
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('bookings.index')}}">@lang('menu.bookings')</a></li>
<li class="breadcrumb-item active">@lang('fleet.edit_booking')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-warning">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.edit_booking')
        </h3>
      </div>

      <div class="card-body">
        @if (count($errors) > 0)
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        <div class="alert alert-info hide fade in alert-dismissable" id="msg_driver" style="display: none;">
          <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
          Your current driver is not available in the chosen times. Available driver has been selected.
        </div>
        <div class="alert alert-info hide fade in alert-dismissable" id="msg_vehicle" style="display: none;">
          <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
          Your current vehicle is not available in the chosen times. Available vehicle has been selected.
        </div>

        {!! Form::open(['route' => ['bookings.update',$data->id],'method'=>'PATCH']) !!}
        {!! Form::hidden('user_id',Auth::user()->id)!!}
        {!! Form::hidden('status',0)!!}
        {!! Form::hidden('id',$data->id)!!}

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('customer_id',__('fleet.selectCustomer'), ['class' => 'form-label']) !!}
              <select id="customer_id" readonly="" name="customer_id" class="form-control xxhvk" required>
                <option selected value="{{$data->customer['id']}}">{{$data->customer['name']}}</option>
              </select>
            </div>
          </div>
          {{-- @dd('test') --}}
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('pickup',__('fleet.pickup'), ['class' => 'form-label']) !!}
              <div class='input-group date' id='from_date'>
                <div class="input-group-prepend">
                  <span class="input-group-text"><span class="fa fa-calendar"></span></span>
                </div>
                {!! Form::text('pickup',$data->pickup,['class'=>'form-control','required']) !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('dropoff',__('fleet.dropoff'), ['class' => 'form-label']) !!}
              <div class='input-group date' id='to_date'>
                <div class="input-group-prepend">
                  <span class="input-group-text"><span class="fa fa-calendar"></span>
                  </span>
                </div>
                {!! Form::text('dropoff',$data->dropoff,['class'=>'form-control','required']) !!}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('vehicle_id',__('fleet.selectVehicle'), ['class' => 'form-label']) !!}
              <select id="vehicle_id" name="vehicle_id" class="form-control" required>
                <option value="">-</option>
                @foreach($vehicles as $vehicle)
                <option value="{{$vehicle['id']}}" @if($vehicle['id']==$data->vehicle_id) selected @endif data-driver="{{$vehicle->getMeta('assign_driver_id')}}">
                  {{$vehicle->make_name}} - {{$vehicle->model_name}} - {{$vehicle->license_plate}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('vehicle_id',__('fleet.selectDriver'), ['class' => 'form-label']) !!}
              <select id="driver_id" name="driver_id" class="form-control" required>
                <option value="">-</option>
                @foreach($drivers as $driver)
                <option value="{{$driver->id}}" @if($driver->id == $data->driver_id) selected
                  @endif>{{$driver->name}}@if($driver->getMeta('is_active') != 1)
                  ( @lang('fleet.in_active') ) @endif</option>
                </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('travellers',__('fleet.no_travellers'), ['class' => 'form-label']) !!}
              {!! Form::number('travellers',$data->travellers,['class'=>'form-control','min'=>1]) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('pickup_addr',__('fleet.pickup_addr'), ['class' => 'form-label']) !!}
              {!!
              Form::text('pickup_addr',$data->pickup_addr,['class'=>'form-control','required','style'=>'height:100px'])
              !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('dest_addr',__('fleet.dropoff_addr'), ['class' => 'form-label']) !!}
              {!! Form::text('dest_addr',$data->dest_addr,['class'=>'form-control','required','style'=>'height:100px'])
              !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('note',__('fleet.note'), ['class' => 'form-label']) !!}
              {!!
              Form::textarea('note',$data->note,['class'=>'form-control','placeholder'=>__('fleet.book_note'),'style'=>'height:100px'])
              !!}
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="form-group col-md-6">
            {!! Form::label('udf1',__('fleet.add_udf'), ['class' => 'col-xs-5 control-label']) !!}
            <div class="row">
              <div class="col-md-8">
                {!! Form::text('udf1', null,['class' => 'form-control']) !!}
              </div>
              <div class="col-md-4">
                <button type="button" class="btn btn-info add_udf"> @lang('fleet.add')</button>
              </div>
            </div>
          </div>
        </div>
        @if($udfs != null)
        @foreach($udfs as $key => $value)
        <div class="row">
          <div class="col-md-8">
            <div class="form-group"> <label class="form-label text-uppercase">{{$key}}</label> <input type="text"
                name="udf[{{$key}}]" class="form-control" required value="{{$value}}"></div>
          </div>
          <div class="col-md-4">
            <div class="form-group" style="margin-top: 30px"><button class="btn btn-danger" type="button"
                onclick="this.parentElement.parentElement.parentElement.remove();">Remove</button> </div>
          </div>
        </div>
        @endforeach
        @endif
        <div class="blank"></div>
        <div class="col-md-12">
          {!! Form::submit(__('fleet.update'), ['class' => 'btn btn-warning']) !!}
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>

@endsection

@section("script")
<script>
  var getDriverRoute='{{ url("admin/get_driver") }}';
  var getVehicleRoute='{{ url("admin/get_vehicle") }}';
  var prevAddress='{{ url("admin/prev-address") }}';
  var selectDriver="@lang('fleet.selectDriver')";
  var selectCustomer="@lang('fleet.selectCustomer')";
  var selectVehicle="@lang('fleet.selectVehicle')";
  var addCustomer="@lang('fleet.add_customer')";
  var prevAddressLang="@lang('fleet.prev_addr')";
  var fleet_email_already_taken="@lang('fleet.email_already_taken')";
</script>
<script src="{{asset('assets/js/bookings/edit.js')}}"></script>
@if(Hyvikk::api('google_api') == "1")
<script>
  function initMap() {
    $('#pickup_addr').attr("placeholder","");
    $('#dest_addr').attr("placeholder","");
      // var input = document.getElementById('searchMapInput');
      var pickup_addr = document.getElementById('pickup_addr');
      new google.maps.places.Autocomplete(pickup_addr);

      var dest_addr = document.getElementById('dest_addr');
      new google.maps.places.Autocomplete(dest_addr);

      // autocomplete.addListener('place_changed', function() {
      //     var place = autocomplete.getPlace();
      //     document.getElementById('pickup_addr').innerHTML = place.formatted_address;
      // });
  }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{Hyvikk::api('api_key')}}&libraries=places&callback=initMap"
  async defer></script>
@endif
@endsection