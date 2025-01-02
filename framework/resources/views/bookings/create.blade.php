@extends('layouts.app')
@section('extra_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="{{ route('bookings.index') }}">@lang('menu.bookings')</a></li>
    <li class="breadcrumb-item active">@lang('fleet.new_booking')</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        @lang('fleet.new_booking')
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

                    {!! Form::open(['route' => 'bookings.store', 'method' => 'post']) !!}
                    {!! Form::hidden('user_id', Auth::user()->id) !!}
                    {!! Form::hidden('status', 0) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('customer_id', __('fleet.selectCustomer'), ['class' => 'form-label']) !!}
                                @if (Auth::user()->user_type != 'C')
                                    <a href="#" data-toggle="modal" data-target="#exampleModal">@lang('fleet.new_customer')</a>
                                @endif
                                <!--<select id="customer_id" name="customer_id" class="form-control" required>-->
                                <!--    <option value="">-</option>-->
                                <!--    @if (Auth::user()->user_type == 'C')-->
                                <!--        <option value="{{ Auth::user()->id }}"-->
                                <!--            data-address="{{ Auth::user()->getMeta('address') }}" data-address2=""-->
                                <!--            data-id="{{ Auth::user()->id }}" selected>{{ Auth::user()->name }}-->
                                <!--        </option>-->
                                <!--    @else-->
                                <!--        @foreach ($customers as $customer)-->
                                <!--            <option value="{{ $customer->id }}"-->
                                <!--                data-address="{{ $customer->getMeta('address') }}" data-address2=""-->
                                <!--                data-id="{{ $customer->id }}">{{ $customer->name }}</option>-->
                                <!--        @endforeach-->
                                <!--    @endif-->
                                <!--</select>-->
                                <select id="customer_id" name="customer_id[]" class="form-control" required multiple>
                                    <option value="">-</option>
                                    @if (Auth::user()->user_type == 'C')
                                        <option value="{{ Auth::user()->id }}"
                                            data-address="{{ Auth::user()->getMeta('address') }}" data-address2=""
                                            data-id="{{ Auth::user()->id }}" selected>{{ Auth::user()->name }}
                                        </option>
                                    @else
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                data-address="{{ $customer->getMeta('address') }}" 
                                                data-address2="" 
                                                data-id="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach

                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('pickup', __('fleet.pickup'), ['class' => 'form-label']) !!}
                                <div class='input-group mb-3 date'>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <span class="fa fa-calendar"></span></span>
                                    </div>
                                    {!! Form::text('pickup', date('Y-m-d H:i:s'), ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('dropoff', __('fleet.dropoff'), ['class' => 'form-label']) !!}
                                <div class='input-group date'>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><span class="fa fa-calendar"></span></span>
                                    </div>
                                    {!! Form::text('dropoff', null, ['class' => 'form-control', 'required']) !!}
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
                                <option value="{{$vehicle->id}}" data-driver="{{$vehicle->getMeta('assign_driver_id')}}">{{$vehicle->make_name}} -
                                  {{$vehicle->model_name}} - {{$vehicle->license_plate}}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('driver_id', __('fleet.selectDriver'), ['class' => 'form-label']) !!}

                                <select id="driver_id" name="driver_id" class="form-control" required>
                                    <option value="">-</option>
                                    @foreach ($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }} @if ($driver->getMeta('is_active') != 1)
                                                (@lang('fleet.in_active'))
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('travellers', __('fleet.no_travellers'), ['class' => 'form-label']) !!}
                                {!! Form::number('travellers', 1, ['class' => 'form-control', 'min' => 1]) !!}
                            </div>
                        </div>
                    </div>
                    @if (Auth::user()->user_type == 'C')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('d_pickup', __('fleet.pickup_location'), ['class' => 'form-label']) !!}
                                <select id="d_pickup" name="d_pickup" class="form-control">
                                    <option value="">-</option>
                                    @foreach ($addresses as $address)
                                    <option value="{{ $address->id }}" data-address="{{ $address->address }}">
                                        {{ $address->address }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('d_dest', __('fleet.dropoff_location'), ['class' => 'form-label']) !!}
                                <select id="d_dest" name="d_dest" class="form-control">
                                    <option value="">-</option>
                                    @foreach ($addresses as $address)
                                    <option value="{{ $address->id }}" data-address="{{ $address->address }}">
                                        {{ $address->address }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif
                
                <!-- Separate Row for the Towards Section -->
                {{-- <div class="row mt-4"> --}}
                    <!-- Towards Section -->
                    <div class="card p-4 shadow-sm w-100" style="margin: 0; width: 100vw;">
                        <h3 class="mb-4">Towards</h3>
                    
                        <!-- Radio Buttons for Home, Office -->
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="booking_type" id="home_radio" value="Home">
                            <label class="form-check-label" for="home_radio">Home</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="booking_type" id="office_radio" value="Office">
                            <label class="form-check-label" for="office_radio">Office</label>
                        </div>
                    
                        <!-- Animated Message Display -->
                        <div id="towards_message" class="alert alert-info mt-3" style="display: none; opacity: 0; transition: opacity 0.5s;">
                        </div>
                    </div>
                    
                    <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        // Reference to the "towards_message" section
                        const towardsMessage = document.getElementById("towards_message");
                    
                        // Attach event listeners to all radio buttons
                        document.querySelectorAll('input[name="booking_type"]').forEach((radio) => {
                            radio.addEventListener("change", function () {
                                const bookingType = this.value;
                    
                                // Update the message based on the selected option
                                if (bookingType === "Home") {
                                    towardsMessage.textContent = "Going Towards Home";
                                } else if (bookingType === "Office") {
                                    towardsMessage.textContent = "Going Towards Office";
                                }
                    
                                // Show the message with fade-in animation
                                towardsMessage.style.display = "block";
                                setTimeout(() => {
                                    towardsMessage.style.opacity = "1"; // Fade-in effect
                                }, 10);
                    
                                // Optionally, remove the message after a delay (e.g., 5 seconds)
                                setTimeout(() => {
                                    towardsMessage.style.opacity = "0"; // Fade-out effect
                                    setTimeout(() => {
                                        towardsMessage.style.display = "none"; // Hide the element after fade-out
                                    }, 500); // Wait for the fade-out transition to complete
                                }, 5000);
                            });
                        });
                    });
                    </script>
                    

            
                    {{-- Add maps and sync up address with the map --}}
                    <div class="row" style="margin-top: 15px;">
                        <div class="form-group col-md-6">
                            {!! Form::label('udf1', __('fleet.add_udf'), ['class' => 'col-xs-5 control-label']) !!}
                            <div class="row">
                                <div class="col-md-8">
                                    {!! Form::text('udf1', null, ['class' => 'form-control']) !!}
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-info add_udf"> @lang('fleet.add')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="blank"></div>
                    <div class="col-md-12">
                        {!! Form::submit(__('fleet.save_booking'), ['class' => 'btn btn-success']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">@lang('fleet.new_customer')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                {!! Form::open(['route' => 'customers.ajax_store', 'method' => 'post', 'id' => 'create_customer_form']) !!}
                <div class="modal-body">
                    <div class="alert alert-danger print-error-msg" style="display:none">
                        <ul></ul>
                    </div>
                    <div class="form-group">
                        {!! Form::label('first_name', __('fleet.firstname'), ['class' => 'form-label']) !!}
                        {!! Form::text('first_name', null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('last_name', __('fleet.lastname'), ['class' => 'form-label']) !!}
                        {!! Form::text('last_name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('gender', __('fleet.gender'), ['class' => 'form-label']) !!}<br>
                        <input type="radio" name="gender" class="flat-red gender" value="1" checked>
                        @lang('fleet.male')<br>

                        <input type="radio" name="gender" class="flat-red gender" value="0"> @lang('fleet.female')
                    </div>

                    <div class="form-group">
                        {!! Form::label('phone', __('fleet.phone'), ['class' => 'form-label']) !!}
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-phone"></i></span>
                            </div>
                            {!! Form::number('phone', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('email', __('fleet.email'), ['class' => 'form-label']) !!}
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                            </div>
                            {!! Form::email('email', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('address', __('fleet.address'), ['class' => 'form-label']) !!}
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-address-book-o"></i></span>
                            </div>
                            {!! Form::textarea('address', null, ['class' => 'form-control', 'size' => '30x2','required']) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
                    <button type="submit" class="btn btn-info">@lang('fleet.save_cust')</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection

@section('script')
    <script>
      var datet = "{{date('Y-m-d H:i:s')}}";
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
    
    {{-- <script src="https://maps.googleapis.com/maps/api/js?key={{ Hyvikk::api('api_key') }}&callback=initMap" async defer></script>
    <script>
        var pickupMap, dropoffMap;
        var pickupMarker, dropoffMarker;
        var geocoder;
        
        document.addEventListener("DOMContentLoaded", function () {
        // Get references to the Google Maps section and route display elements
        const googleMapsSection = document.getElementById("google_maps_section");
        const routeDisplay = document.getElementById("route_display");
        const routeText = document.getElementById("route_text");

        // Ensure the Google Maps section and route display are hidden on page load
        googleMapsSection.style.display = "none";
        routeDisplay.style.display = "none";

        // Attach event listeners to all radio buttons
        document.addEventListener("DOMContentLoaded", function () {
    // Reference to the "towards_message" section
    const towardsMessage = document.getElementById("towards_message");

    // Attach event listeners to all radio buttons
    document.querySelectorAll('input[name="booking_type"]').forEach((radio) => {
        radio.addEventListener("change", function () {
            const bookingType = this.value;

            // Update the message based on the selected option
            if (bookingType === "Home") {
                towardsMessage.textContent = "Going Towards Home";
            } else if (bookingType === "Office") {
                towardsMessage.textContent = "Going Towards Office";
            }

            // Show the message with fade-in animation
            towardsMessage.style.display = "block";
            setTimeout(() => {
                towardsMessage.style.opacity = "1"; // Fade-in effect
            }, 10);

            // Optionally, remove the message after a delay (e.g., 5 seconds)
            setTimeout(() => {
                towardsMessage.style.opacity = "0"; // Fade-out effect
                setTimeout(() => {
                    towardsMessage.style.display = "none"; // Hide the element after fade-out
                }, 500); // Wait for the fade-out transition to complete
            }, 5000);
        });
    });
});
});

    /**
    * Initializes Google Maps for both pickup and drop-off locations.
    * This function is called only when the "RAC" option is selected.
    */
    function initMap() {
        // Create a geocoder instance for reverse geocoding
        geocoder = new google.maps.Geocoder();

        // Check if geolocation is supported and enabled
        if (navigator.geolocation) {
            // Get the user's current location
            navigator.geolocation.getCurrentPosition(function (position) {
                const userLat = position.coords.latitude;
             const userLng = position.coords.longitude;

                // Initialize the pickup location map
                pickupMap = new google.maps.Map(document.getElementById("pickup_map"), {
                    center: { lat: userLat, lng: userLng },
                    zoom: 13,
                });

                // Initialize the drop-off location map
                dropoffMap = new google.maps.Map(document.getElementById("dropoff_map"), {
                    center: { lat: userLat, lng: userLng },
                    zoom: 13,
                });

                // Add draggable markers for both maps
                pickupMarker = new google.maps.Marker({
                    position: { lat: userLat, lng: userLng },
                    map: pickupMap,
                 title: "Your Location",
                 draggable: true, // Allow marker to be moved by the user
                });

                dropoffMarker = new google.maps.Marker({
                    position: { lat: userLat, lng: userLng },
                 map: dropoffMap,
                    title: "Your Location",
                    draggable: true, // Allow marker to be moved by the user
                });

                // Update location fields when the pickup map is clicked
                pickupMap.addListener("click", function (e) {
                    updateLocation(pickupMap, pickupMarker, e.latLng, "pickup_location");
                });

                // Update location fields when the drop-off map is clicked
                dropoffMap.addListener("click", function (e) {
                    updateLocation(dropoffMap, dropoffMarker, e.latLng, "dropoff_location");
                });

                // Update location fields when pickup marker is dragged
             pickupMarker.addListener("dragend", function (e) {
                    updateLocation(pickupMap, pickupMarker, e.latLng, "pickup_location");
                });

                // Update location fields when drop-off marker is dragged
                dropoffMarker.addListener("dragend", function (e) {
                 updateLocation(dropoffMap, dropoffMarker, e.latLng, "dropoff_location");
                });
            }, function () {
                alert("Geolocation service failed. Unable to retrieve location."); // Handle errors
         });
     } else {
            alert("Geolocation is not supported by your browser."); // Browser doesn't support geolocation
        }
    }
    /**
    * Updates the input fields and moves the marker when the location is changed.
    * 
    * @param {object} map - Google Map instance
    * @param {object} marker - Marker instance for the map
    * @param {object} latLng - Latitude and longitude object
    * @param {string} inputId - ID of the input field to update
    */
    function updateLocation(map, marker, latLng, inputId) {
        // Move the marker to the new position
        marker.setPosition(latLng);
        map.panTo(latLng);

        // Use the Geocoding API to get the address for the new position
        geocoder.geocode({ location: latLng }, function (results, status) {
            if (status === "OK") {
                if (results[0]) {
                    // Update the input field with the formatted address
                    document.getElementById(inputId).value = results[0].formatted_address;
                } else {
                    document.getElementById(inputId).value = "No address found for this location";
                }
            } else {
             document.getElementById(inputId).value = "Geocoder failed due to: " + status;
            }
        });
    }    
    </script> --}}

    <script src="{{asset('assets/js/bookings/create.js')}}"></script>    @if (Hyvikk::api('google_api') == '1')
        <script>
            function initMap() {
                $('#pickup_addr').attr("placeholder", "");
                $('#dest_addr').attr("placeholder", "");
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
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ Hyvikk::api('api_key') }}&libraries=places&callback=initMap"
            async defer></script>
    @endif
@endsection
