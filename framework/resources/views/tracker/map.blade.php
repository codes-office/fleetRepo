@extends('layouts.app')
@php($date_format_setting = Hyvikk::get('date_format') ? Hyvikk::get('date_format') : 'd-m-Y')
@php($currency = Hyvikk::get('currency'))
@section('breadcrumb')
    <li class="breadcrumb-item active"> @lang('fleet.vehicle_track')</li>
@endsection
@section('extra_css')
    <style type="text/css">
        .checkbox,
        #chk_all {
            width: 20px;
            height: 20px;
        }

        .select {
            width: 35%;
        }
    </style>
@endsection
@section('content')
    @if (isset($response['message_traccar_fail']))
        <div class="text-center alert-danger"
            style="height: 10vh;display: flex;justify-content: center;align-items: center;">
            {{ $response['message_traccar_fail'] ?? '' }}</div>
        {{-- @dd($vehicle_data) --}}
    @else
        @if (isset($response['error_exist']))
            <div class="text-center alert-danger"
                style="height: 10vh; display: flex; justify-content: center; align-items: center;">
                {{ $response['error_exist'] ?? '' }}
            </div>
        @else
            @if (isset($response['message']))
                <div class="text-center alert alert-danger">
                    {{ $response['message'] }}
                </div>
            @else
                <div class="form-group text-center">
                    <label class="control-label col-md-3">Select Vehcile For Track</label>
                    <div class="col-lg-12 col-md-12">
                        <select name="vehicle" class="select mb-2" style="margin-bottom:10px;" id="vehicle_id">
                            <option value="">--Select--</option>
                            @foreach ($data['active_vehicle'] as $v)
                                <option value="{{ $v->id }}" @if ($data['select_vehicle'] == $v->id) selected @endif>
                                    {{ $v->model_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="map" style="width:100%;height:400px;"></div>
                <div class="text-center">
                    <div class="card">
                        <div class="card-header d-flex justify-content-center align-items-center" style="padding-top:15px">
                            <h3><strong>Car Infomration</strong></h3>
                        </div>
                        <div class="card-body">
                            <table class='table table-striped'>
                                <thead>
                                    <tr>
                                        <th>Vehicle Name</th>
                                        <th>Vehicle Speed</th>
                                        <th>Booking PickUp</th>
                                        <th>Driver Name</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endif
    <div id="map" style="width:100%;height:400px;"></div>
@endsection
@section('script')
    <!-- prettier-ignore -->
<script>
    $('.select').select2();
    var map;
    var markers = [];
    var lines=[];
    var lineOptions;
    var path;
    var carIcon;
    $('#vehicle_id').on('change',function(){
    var vehicle_id=$('#vehicle_id').val();
    // console.log(vehicle_id);
    if(vehicle_id != null){
    location.href="{{ url('admin/vehicles-track') }}/"+vehicle_id;
    }else{
    location.href="{{ url('admin/vehicles-track') }}";
    }
    });
    // Initialize the map and markers
    function initialize() {
    // car icon call only one time
    var carIcon = {
    url: '{{ asset('assets/images/small-car.png') }}',
    scaledSize: new google.maps.Size(50, 50),
    };
    var dotIcon = {
    path: google.maps.SymbolPath.CIRCLE,
    fillOpacity: 1,
    fillColor: "#FFFFFF",
    strokeOpacity: 1,
    strokeColor: "#FF0000",
    strokeWeight: 1,
    scale: 4,
    };
    //intial map
    // Set the initial center of the map
    @if (count($data['positions'] ?? []) > 0)
        const lat_first = '{{ $data['positions'][0]->latitude }}';
        const lng_first ='{{ $data['positions'][0]->longitude }}';
    @else
        const lat_first = 20.593683;
        const lng_first =78.962883;
    @endif
    var myLatlng = new google.maps.LatLng(lat_first,lng_first); // San Francisco
    // Map options
    var mapOptions = {
    zoom: 20,
    center: myLatlng
    };
    map = new google.maps.Map(document.getElementById("map"), mapOptions);
    @if (!isset($response['error_exist']) && !isset($response['message_traccar_fail']) && !isset($response['message']))
        @if ($data['vehicle_data'] != null)
            @foreach ($data['vehicle_data'] as $v)
                var myLatlng{{ $v->id }} = new
                google.maps.LatLng({{ $v->position->latitude }},{{ $v->position->longitude }});
                var marker = new google.maps.Marker({
                position: myLatlng{{ $v->id }},
                map: map,
                icon: carIcon,
                title: '{{ $v->model_name }}'
                });
                $('table tbody').append(
                "<tr>"+
                    "<td>{{ $v->model_name }}</td>"+
                    "<td>{{ $v->position->speed * 1.852 }} km/h</td>"+
                    "<td>{{ $v->bookings->pickup ?? '-' }}</td>"+
                    "<td>{{ $v->bookings->driver->name ?? '-' }}</td>"+
                    "</tr>");
                markers[{{ $v->id }}] = marker;
                // console.log(markers[{{ $v->id }}]);
            @endforeach
        @endif
    @endif
    }
    // Update the marker position for the given vehicle
    function updateMarker(vehicleId, lat, lng,speed,model_name,pickup,bookings_driver) {
    var marker = markers[vehicleId];
    console.log(bookings_driver=='');
    if (marker) {
    var rowHtml = "<tr>";
        rowHtml += "<td>" + (typeof model_name !== "undefined" ? model_name : "-") + "</td>";
        rowHtml += "<td>" + (typeof speed !== "undefined" ? speed*1.852+' km/h' : "-") + "</td>";
        rowHtml += "<td>" + (typeof pickup !== "undefined" ? pickup : "-") + "</td>";
        rowHtml += "<td>" + (bookings_driver !== "" ? bookings_driver : "-") + "</td>";
        rowHtml += "</tr>";
    $('table tbody').append(rowHtml);
    var myLatlng = new google.maps.LatLng(lat, lng);
    marker.setPosition(myLatlng);
    }
    }
    // Poll the server for all vehicles' locations every 10 seconds
    function pollServer() {
    setInterval(function() {
    var vehicle_id= $('#vehicle_id').val();
    vehicle_id!=null ? url='{{ url('admin/track/') }}/'+vehicle_id : url='{{ url('admin/track') }}';
    // Make an AJAX request to the server to get the current locations of all vehicles
    $.ajax({
    url:url ,
    type: "GET",
    dataType: "json",
    success: function(response) {
    // Update the marker positions for all vehicles
    $('tbody').text('');
    console.log(response);
    response.forEach(function(element){
    updateMarker(element.id, element.position.latitude,
    element.position.longitude,element.position.speed,element.model_name,element.pickup,element.bookings_driver);
    })
    },
    error: function(xhr, status, error) {
    // console.log("Error: " + error);
    }
    });
    }, 10000); // 10 seconds
    }
    // Initialize the map and start polling the server
    $(document).ready(function() {
    initialize();
    pollServer();
    });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ Hyvikk::get('traccar_map_key') }}"></script>
    {{-- AIzaSyDWLZZNErpNRPY-cZXlbOklBnpwrVb_PY4 --}}
@endsection