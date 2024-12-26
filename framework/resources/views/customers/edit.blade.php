@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('customers.index')}}">@lang('fleet.customers')</a></li>
<li class="breadcrumb-item active"> @lang('fleet.edit_customer')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-warning">
      <div class="card-header">
        <h3 class="card-title">
          @lang('fleet.edit_customer')
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

        {!! Form::open(['route' => ['customers.update',$data->id],'method'=>'PATCH']) !!}
        {!! Form::hidden('id',$data->id) !!}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('first_name', __('fleet.firstname'), ['class' => 'form-label']) !!}
              {!! Form::text('first_name', $data->getMeta('first_name'),['class' => 'form-control','required']) !!}
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('last_name', __('fleet.lastname'), ['class' => 'form-label']) !!}
              {!! Form::text('last_name', $data->getMeta('last_name'),['class' => 'form-control','required']) !!}
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('phone',__('fleet.phone'), ['class' => 'form-label']) !!}
              <div class=" input-group mb-3">
                <div class="input-group-prepend date">
                  <span class="input-group-text"><i class="fa fa-phone"></i></span>
                </div>
                {!! Form::number('phone', $data->getMeta('mobno'),['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('email', __('fleet.email'), ['class' => 'form-label']) !!}
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                </div>
                {!! Form::email('email', $data->email,['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('address', __('fleet.address'), ['class' => 'form-label required']) !!}
              {!! Form::text('address', $data->address, ['class' => 'form-control', 'id' => 'address', 'required', 'placeholder' => 'Select an address']) !!}
            </div>
          </div>
          
          <!-- Map for Address Selection -->
          <div id="address_map" style="height: 400px; margin-top: 10px; width: 100%;"></div>
        </div>

        <!-- Hidden Fields for Latitude and Longitude -->
        {!! Form::hidden('latitude', $data->latitude, ['id' => 'latitude']) !!}
        {!! Form::hidden('longitude', $data->longitude, ['id' => 'longitude']) !!}
        
          <div class="col-md-6">
            <div class="form-group" style="margin-top: 10px">
              {!! Form::label('gender', __('fleet.gender') , ['class' => 'form-label']) !!}<br>
              <input type="radio" name="gender" class="flat-red gender" value="1" @if($data->gender == 1) checked @endif
              required> @lang('fleet.male')<br>
              <input type="radio" name="gender" class="flat-red gender" value="0" @if($data->gender == 0) checked @endif
              required> @lang('fleet.female')
            </div>
          </div>
        </div>
        <div class="col-md-12">
          {!! Form::submit(__('fleet.update'), ['class' => 'btn btn-warning']) !!}
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>

@endsection
{{-- @section('script')
<script type="text/javascript">
  //Flat red color scheme for iCheck
  $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
    checkboxClass: 'icheckbox_flat-green',
    radioClass   : 'iradio_flat-green'
  })
</script>
@endsection --}}
@section('script')
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ Hyvikk::api('api_key') }}&callback=initMap" async defer></script>
<script>
 let map, marker, geocoder;

function initMap() {
  geocoder = new google.maps.Geocoder();

  // Check if latitude and longitude are available
  let latitude = parseFloat(document.getElementById('latitude').value);
  let longitude = parseFloat(document.getElementById('longitude').value);

  // If both latitude and longitude are valid, use them to initialize the map
  let location = (isNaN(latitude) || isNaN(longitude)) ? { lat: 13.0380, lng: 77.4812 } : { lat: latitude, lng: longitude };

  map = new google.maps.Map(document.getElementById('address_map'), {
    center: location,
    zoom: 13,
  });

  // Place a draggable marker at the location
  marker = new google.maps.Marker({
    position: location,
    map: map,
    draggable: true,
    title: "Drag to select location",
  });

  // Update latitude, longitude, and address when the marker is dragged
  marker.addListener('dragend', function() {
    const position = marker.getPosition();
    updateLocation(position);
  });

  // Update latitude, longitude, and address when the map is clicked
  map.addListener('click', function(event) {
    const position = event.latLng;
    marker.setPosition(position);
    updateLocation(position);
  });

  // Set the initial location based on the address field (if present)
  const addressField = document.getElementById('address').value;
  if (addressField) {
    geocodeAddress(addressField);
  } else {
    updateLocation(location);
  }
}

// Function to update the hidden fields and address
function updateLocation(latLng) {
  const lat = latLng.lat();
  const lng = latLng.lng();

  // Update the latitude and longitude hidden fields
  document.getElementById('latitude').value = lat;
  document.getElementById('longitude').value = lng;

  // Reverse geocode to get the address
  geocoder.geocode({ location: latLng }, function(results, status) {
    if (status === 'OK' && results[0]) {
      document.getElementById('address').value = results[0].formatted_address;
    } else {
      document.getElementById('address').value = "Location not found";
    }
  });
}

// Function to geocode the address and update the map and fields
function geocodeAddress(address) {
  geocoder.geocode({ address: address }, function(results, status) {
    if (status === 'OK') {
      const position = results[0].geometry.location;
      marker.setPosition(position);
      map.setCenter(position);
      updateLocation(position);
    } else {
      alert("Geocode was not successful for the following reason: " + status);
    }
  });
}
</script>
@endsection