@extends('layouts.app')
@section('extra_css')
<style type="text/css">
  /* The switch - the box around the slider */
  .switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
  }

  /* Hide default HTML checkbox */
  .switch input {
    display: none;
  }

  /* The slider */
  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
  }

  input:checked+.slider {
    background-color: #2196F3;
  }

  input:focus+.slider {
    box-shadow: 0 0 1px #2196F3;
  }

  input:checked+.slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
  }

  /* Rounded sliders */
  .slider.round {
    border-radius: 34px;
  }

  .slider.round:before {
    border-radius: 50%;
  }
</style>
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('users.index')}}"> @lang('fleet.users')@lang('fleet.managers') </a></li>
<li class="breadcrumb-item active">@lang('fleet.addUser')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('Admin Creation Based on Roles')</h3>
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

        {!! Form::open(['route' => 'users.store','files'=>true,'method'=>'post']) !!}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('first_name', __('fleet.firstname'), ['class' => 'form-label']) !!}
              {!! Form::text('first_name', null,['class' => 'form-control','required']) !!}
            </div>

            <div class="form-group">
              {!! Form::label('last_name', __('fleet.lastname'), ['class' => 'form-label']) !!}
              {!! Form::text('last_name', null,['class' => 'form-control','required']) !!}
            </div>

            <div class="form-group">
              {!! Form::label('role_id',__('fleet.role'), ['class' => 'form-label']) !!}
              <select id="role_id" name="role_id" class="form-control" required>
                <option value="">@lang('fleet.role')</option>
                @foreach($roles as $role)
                <option value="{{$role->id}}">{{$role->name}}</option>
                @endforeach
              </select>
            </div>

            {{-- <div class="form-group">
              {!! Form::label('profile_image', __('fleet.profile_photo'), ['class' => 'form-label']) !!}

              {!! Form::file('profile_image',null,['class' => 'form-control']) !!}
            </div> --}}
            {{-- <div class="form-group" style="margin-top: 30px">
              <div class="row">
                <div class="col-md-3">
                  <label class="switch">
                    <input type="checkbox" name="is_admin" value="1">
                    <span class="slider round"></span>
                  </label>
                </div>
                <div class="col-md-3" style="margin-top: 5px">
                  <h4>@lang('fleet.is_admin')</h4>
                </div>
              </div>
            </div> --}}
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
            <div class="form-group">
              {!! Form::label('password', __('fleet.password'), ['class' => 'form-label']) !!}
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-lock"></i></span>
                </div>
                {!! Form::password('password', ['class' => 'form-control','required']) !!}
              </div>
            </div>

            <div class="form-group">
              {!! Form::label('group_id',__('fleet.selectGroup'), ['class' => 'form-label']) !!}
              <select id="group_id" name="group_id" class="form-control">
                <option value="">@lang('fleet.vehicleGroup')</option>
                @foreach($groups as $group)
                @if($group->id == 1)
                <option value="{{$group->id}}" selected>{{$group->name}}</option>
                @else
                <option value="{{$group->id}}">{{$group->name}}</option>
                @endif
                @endforeach
              </select>
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
        </div>

          <div class="form-group" style="margin-top: 30px;">
            {!! Form::label('profile_image', __('fleet.profile_photo'), ['class' => 'form-label']) !!}

            {!! Form::file('profile_image',null,['class' => 'form-control']) !!}
          </div>

            {{-- <div class="form-group">
              {!! Form::label('module',__('fleet.select_modules'), ['class' => 'form-label']) !!} <br>
              <div class="row">
                <div class="col-md-4" style="padding: 0px;">
                  <input type="checkbox" name="module[]" value="0" class="flat-red form-control">&nbsp;
                  @lang('menu.users')<br>
                  <input type="checkbox" name="module[]" value="1" class="flat-red form-control">&nbsp;
                  @lang('fleet.vehicles')<br>
                  <input type="checkbox" name="module[]" value="2" class="flat-red form-control">
                  &nbsp;@lang('menu.transactions') <br>
                  <input type="checkbox" name="module[]" value="3" class="flat-red form-control">&nbsp;
                  @lang('fleet.bookings')<br>
                  <input type="checkbox" name="module[]" value="13" class="flat-red form-control">&nbsp;
                  @lang('fleet.helpus')
                </div>
                <div class="col-md-4" style="padding: 0px;">
                  <input type="checkbox" name="module[]" value="4" class="flat-red form-control">&nbsp;
                  @lang('menu.reports')<br>
                  <input type="checkbox" name="module[]" value="5" class="flat-red form-control">&nbsp;
                  @lang('fleet.fuel')<br>

                  <input type="checkbox" name="module[]" value="6" class="flat-red form-control">&nbsp;
                  @lang('fleet.vendors')<br>
                  <input type="checkbox" name="module[]" value="7" class="flat-red form-control">&nbsp;
                  @lang('fleet.work_orders')<br>
                  <input type="checkbox" name="module[]" value="14" class="flat-red form-control">&nbsp;
                  @lang('fleet.parts')
                </div>
                <div class="col-md-4" style="padding: 0px;">
                  <input type="checkbox" name="module[]" value="8" class="flat-red form-control">&nbsp;
                  @lang('fleet.notes')<br>
                  <input type="checkbox" name="module[]" value="9" class="flat-red form-control">&nbsp;
                  @lang('fleet.serviceReminders')<br>
                  <input type="checkbox" name="module[]" value="10" class="flat-red form-control">&nbsp;
                  @lang('fleet.reviews')<br>
                  <input type="checkbox" name="module[]" value="12" class="flat-red form-control">&nbsp;
                  @lang('fleet.maps')<br>
                  <input type="checkbox" name="module[]" value="15" class="flat-red form-control">&nbsp;
                  @lang('fleet.testimonials')
                </div>
              </div>
            </div> --}}
          </div>
        </div>

        <div class="col-md-12">
          {!! Form::submit(__('fleet.addUser'), ['class' => 'btn btn-success']) !!}
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
@endsection