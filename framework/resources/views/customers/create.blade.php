@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('customers.index')}}">@lang('fleet.customers')</a></li>
<li class="breadcrumb-item active">@lang('fleet.add_new')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.add_new')
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

        {!! Form::open(['route' => 'customers.store','method'=>'post']) !!}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('first_name', __('fleet.firstname'), ['class' => 'form-label']) !!}
              {!! Form::text('first_name', null,['class' => 'form-control','required']) !!}
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('last_name', __('fleet.lastname'), ['class' => 'form-label']) !!}
              {!! Form::text('last_name', null,['class' => 'form-control','required']) !!}
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('phone',__('fleet.phone'), ['class' => 'form-label']) !!}
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-phone"></i></span>
                </div>
                {!! Form::number('phone', null,['class' => 'form-control','required']) !!}
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
                {!! Form::email('email', null,['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>

          <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('address', __('fleet.address'), ['class' => 'form-label required']) !!}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-address-book-o"></i></span>
                    </div>
                    {!! Form::text('address', null, ['class' => 'form-control', 'id' => 'address-input', 'required']) !!}
                </div>
            </div>
            <!-- Hidden fields for latitude and longitude -->
            {!! Form::hidden('latitude', null, ['id' => 'latitude-input']) !!}
            {!! Form::hidden('longitude', null, ['id' => 'longitude-input']) !!}
            <!-- Map Container -->
            <div id="map" style="height: 300px; margin-top: 10px;"></div>
        </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('gender', __('fleet.gender') , ['class' => 'form-label']) !!}<br>
              <input type="radio" name="gender" class="flat-red gender" value="1" checked> @lang('fleet.male')<br>

              <input type="radio" name="gender" class="flat-red gender" value="0"> @lang('fleet.female')
            </div>
          </div>
        </div>
        <div class="col-md-12">
          {!! Form::submit(__('fleet.add_new'), ['class' => 'btn btn-success']) !!}
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
<script src="https://maps.googleapis.com/maps/api/js?key={{ Hyvikk::api('api_key') }}&callback=initMap" async defer></script>

<script>
  let map, marker, geocoder;

  function initMap() {
      geocoder = new google.maps.Geocoder();
      
      // Check if geolocation is supported
      if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(
              function (position) {
                  const userLocation = {
                      lat: position.coords.latitude,
                      lng: position.coords.longitude,
                  };
                  initializeMap(userLocation);
              },
              function () {
                  // Fallback to a default location if user denies permission
                  const defaultLocation = { lat: -34.397, lng: 150.644 }; // Australia
                  alert('Geolocation failed. Showing default location.');
                  initializeMap(defaultLocation);
              }
          );
      } else {
          // Fallback to a default location if geolocation is not supported
          const defaultLocation = { lat: -34.397, lng: 150.644 }; // Australia
          alert('Geolocation is not supported by this browser. Showing default location.');
          initializeMap(defaultLocation);
      }
  }

  function initializeMap(location) {
      // Initialize map centered on the given location
      map = new google.maps.Map(document.getElementById('map'), {
          zoom: 15,
          center: location,
      });

      // Add a marker to the map
      marker = new google.maps.Marker({
          position: location,
          map: map,
          draggable: true, // Allow marker to be dragged
      });

      // Set the initial address
      setMarkerLocation(location);

      // Listen for map click event
      map.addListener('click', function (event) {
          setMarkerLocation(event.latLng);
      });

      // Listen for marker dragend event
      marker.addListener('dragend', function () {
          setMarkerLocation(marker.getPosition());
      });
  }

  function setMarkerLocation(latLng) {
      // Set the marker position
      marker.setPosition(latLng);
      map.setCenter(latLng);

      document.getElementById('latitude-input').value = latLng.lat;
      document.getElementById('longitude-input').value = latLng.lng;
      // Get the address using reverse geocoding
      geocoder.geocode({ location: latLng }, function (results, status) {
          if (status === 'OK' && results[0]) {
              const fullAddress = results[0].formatted_address;
              document.getElementById('address-input').value = fullAddress;
          } else {
              alert('Unable to retrieve address. Please try again.');
          }
      });
  }

  // Initialize the map after the page loads
  window.onload = initMap;
</script>



@endsection

