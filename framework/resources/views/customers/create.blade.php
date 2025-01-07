@extends('layouts.app')

@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('customers.index')}}">@lang('fleet.customers')</a></li>
<li class="breadcrumb-item active">@lang('fleet.add_new')</li>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.add_new')</h3>
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

        {!! Form::open(['route' => 'customers.store', 'method' => 'post']) !!}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('first_name', __('fleet.firstname'), ['class' => 'form-label']) !!}
              {!! Form::text('first_name', null, ['class' => 'form-control', 'required']) !!}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('last_name', __('fleet.lastname'), ['class' => 'form-label']) !!}
              {!! Form::text('last_name', null, ['class' => 'form-control', 'required']) !!}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('phone', __('fleet.phone'), ['class' => 'form-label']) !!}
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-phone"></i></span>
                </div>
                {!! Form::number('phone', null, ['class' => 'form-control', 'required']) !!}
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
                {!! Form::email('email', null, ['class' => 'form-control', 'required']) !!}
              </div>
            </div>
          </div>
          
          @if ($user->user_type === 'S')
          <!-- Show Company selection for Super Admin -->
          <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('company', __('Company'), ['class' => 'form-label']) !!}
                {!! Form::select('company', $data['customers'], null, ['class' => 'form-control', 'placeholder' => __('Select Company'), 'id' => 'company']) !!}
            </div>
          </div>
          @else
          <!-- Auto-select Customer for 'O' user type -->
          <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('company', __('Company'), ['class' => 'form-label']) !!}
                {!! Form::text('company', $data['selected_customer_id'] ?? '', ['class' => 'form-control', 'readonly' => 'readonly']) !!}
            </div>
          </div>
          @endif

          <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('team', __('Team'), ['class' => 'form-label']) !!}
                {!! Form::select('team', [], null, ['class' => 'form-control', 'placeholder' => __('Select Team'), 'id' => 'team']) !!}
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('address', __('fleet.address'), ['class' => 'form-label required']) !!}
                {!! Form::text('address', null, ['class' => 'form-control', 'id' => 'address', 'required', 'placeholder' => 'Select an address']) !!}
            </div>
            <!-- Map for Address Selection -->
            <div id="address_map" style="height: 400px; margin-top: 10px; width: 100%;"></div>
          </div>
          
          <!-- Hidden Fields for Latitude and Longitude -->
          {!! Form::hidden('latitude', null, ['id' => 'latitude']) !!}
          {!! Form::hidden('longitude', null, ['id' => 'longitude']) !!}  
          
          <!-- Gender Selection Below Map -->
          <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('gender', __('fleet.gender'), ['class' => 'form-label']) !!}<br>
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

@section('script')
<script src="https://maps.googleapis.com/maps/api/js?key={{ Hyvikk::api('api_key') }}&callback=initMap" async defer></script>
<script>
  let map, marker, geocoder;

// Function to initialize the map
function initMap() {
  geocoder = new google.maps.Geocoder();

  // Try to get the user's current location
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      const userLocation = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };

      // Initialize map with user's location
      map = new google.maps.Map(document.getElementById('address_map'), {
        center: userLocation,
        zoom: 13,
      });

      // Place a draggable marker at the user's location
      marker = new google.maps.Marker({
        position: userLocation,
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

      // Set the initial location
      updateLocation(userLocation);

    }, function() {
      handleLocationError(true);
    });
  } else {
    // Browser doesn't support Geolocation
    handleLocationError(false);
  }
}

// Function to handle location errors
function handleLocationError(error) {
  const defaultLocation = { lat: 13.0380, lng: 77.4812 }; // Bengaluru as a fallback
  map = new google.maps.Map(document.getElementById('address_map'), {
    center: defaultLocation,
    zoom: 13,
  });

  marker = new google.maps.Marker({
    position: defaultLocation,
    map: map,
    draggable: true,
    title: "Drag to select location",
  });

  // Set default values for address, lat, and lng
  updateLocation(defaultLocation);
}

// Function to update the hidden fields and address
function updateLocation(latLng) {
  const lat = latLng.lat();
  const lng = latLng.lng();

  // Update the latitude and longitude hidden fields
  document.getElementById('latitude').value = lat;
  document.getElementById('longitude').value = lng;

  console.log('Updated Latitude:', lat); // Debugging line
  console.log('Updated Longitude:', lng); // Debugging line

  // Reverse geocode to get the address
  geocoder.geocode({ location: latLng }, function(results, status) {
    if (status === 'OK' && results[0]) {
      document.getElementById('address').value = results[0].formatted_address;
    } else {
      document.getElementById('address').value = "Location not found";
    }
  });
}

</script>

<script>
  $(document).ready(function () {
      $('#company').change(function () {
          const companyId = $(this).val();

          // Clear the team dropdown
          $('#team').empty().append('<option value="">' + "{{ __('fleet.select_team') }}" + '</option>');

          if (companyId) {
              // Make an AJAX request to fetch teams
              $.ajax({
                  url: "{{ url('admin/get-teams-by-company')}}",
                  type: 'POST',
                  data: {
                      company_id: companyId,
                      _token: '{{ csrf_token() }}'
                  },
                  success: function (data) {
                      // Populate the team dropdown with the returned data
                      $.each(data, function (id, name) {
                          $('#team').append('<option value="' + id + '">' + name + '</option>');
                      });
                  },
                  error: function () {
                      alert("{{ __('fleet.error_fetching_teams') }}");
                  }
              });
          }
      });

      // Trigger company change event on page load for 'O' user type
      const companyId = $('#company').val();
      if (companyId) {
          $('#company').trigger('change');
      }
  });
</script>
@endsection
