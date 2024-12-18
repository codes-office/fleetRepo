@extends("layouts.app")
@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.maps')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
        @lang('fleet.maps')
        </h3>
      </div>

      <div class="card-body">
        <div class="row">
      	<div style="width: 100%; height: 400px;" id="map_canvas"></div>
          <input type="hidden" name="lat" id="lat" value="">
          <input type="hidden" name="long" id="long" value="">
        </div>
        <div class="row table-responsive" style="margin-top: 10px;">

          <table class="table display" id="data_table">
            <thead class="thead-inverse">
              <tr>
                <th>@lang('fleet.driver')</th>
                <th>@lang('fleet.status')</th>
                <th>@lang('fleet.track')</th>
              </tr>
            </thead>
            <tbody id="details">
              @foreach($details as $d)
              <tr>
                <td>{{$d["user_name"]}}</td>
                <td>
                  @if($d['availability'] == "1")
                    <span class="text-success">@lang('fleet.online')</span>
                  @else
                    <span class="text-danger">@lang('fleet.offline')</span>
                  @endif
                </td>
                <td>
                  <a class="btn btn-info" href="{{url('admin/track-driver/'.$d['user_id'])}}">@lang('fleet.track')</a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('script')
<script src="https://maps.googleapis.com/maps/api/js?key={{Hyvikk::api('api_key')}}"></script>
<script type="text/javascript">
  function gm_authFailure() {
    new PNotify({
      title: 'Error!',
      text: 'Invalid API key.<br> Please check your API key and try again!',
      type: 'error',
      delay: 20000
    });
  };
    lat_long();
    function lat_long(){

        $.ajax({
        type: "GET",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

        url: "{{url('admin/markers')}}",

        success: function(data){

          $("#lat").val(data[0].position.lat);
          $("#long").val(data[0].position.long);

          // console.log("Ajax function: lat = " + $("#lat").val());
          // console.log("Ajax function: long = " + $("#long").val());
        },

        dataType: "json"
      });
    }


  $(document).ready(function() {
    setTimeout(function(){

         //Time between marker refreshes
        var INTERVAL = 5000;

        //Used to remember markers
        var markerStore = {};

        // var myLatlng = new google.maps.LatLng(30.2353412,-92.010498);

        var myLatlng = new google.maps.LatLng($("#lat").val(),$("#long").val());
          var myOptions = {
              zoom: 13,
              center: myLatlng,
              mapTypeId: google.maps.MapTypeId.ROADMAP,
          }
          var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);


        getMarkers();


        function getMarkers() {
          // console.log('getMarkers');
          $.get('{{url("admin/markers")}}', {}, function(res,resp) {
            // console.dir(res);

            $('#details').empty();
            for(var i=0, len=res.length; i<len; i++) {
              var active_status = res[i].status;
              if(active_status == 'Online'){
                var color = "text-success";
              }
              else{
                var color = "text-danger";
              }
              var d_id = res[i].id;
              // alert(d_id);
              $('#details').append("<tr><td>"+res[i].name+"</td><td><span class='"+color+"'>"+active_status+"</span></td><td><a class='btn btn-info' href={{url('admin/track-driver/')}}/"+d_id+">@lang('fleet.track')</a></td></tr>");
            }
            var bounds = new google.maps.LatLngBounds();
            var infowindow = new google.maps.InfoWindow();
            for(var i=0, len=res.length; i<len; i++) {

              //Do we have this marker already?
              // console.log(markerStore.hasOwnProperty(res[i].id));
              if(markerStore.hasOwnProperty(res[i].id)) {
                markerStore[res[i].id].setPosition(new google.maps.LatLng(res[i].position.lat,res[i].position.long));
                // console.log('just funna move it...');
                markerStore[res[i].id].setIcon('{{asset("assets/images/")}}/'+res[i].icon);
                markerStore[res[i].id].setTitle(res[i].name+" ("+res[i].status+")");
                bounds.extend(new google.maps.LatLng(res[i].position.lat,res[i].position.long));
                // infowindow.setContent('hello window');
                // infowindow.open(map, marker);

                // alert(markerStore[res[i].id]);
                // alert(res[i].id);
                // markerStore[res[i].id].addListener('click', function() {
                //     // alert(markerStore[res[i].name]);
                //     infowindow.setContent('test');
                //     infowindow.open(map, marker);
                //     // alert(infowindow.getContent());
                // });
              }
              else {
                var marker = new google.maps.Marker({
                  position: new google.maps.LatLng(res[i].position.lat,res[i].position.long),
                  title:res[i].name+" ("+res[i].status+")",
                  map:map,
                  // draggable: true,
                  icon:'{{asset("assets/images/")}}/'+res[i].icon
                });
                bounds.extend(new google.maps.LatLng(res[i].position.lat,res[i].position.long));
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                  return function() {
                    infowindow.setContent(res[i].name);
                    infowindow.open(map, marker);

                  }
                })(marker, i));

                markerStore[res[i].id] = marker;

              }
            }
            if(res.length == "1"){
              map.setZoom(13);
            }
            else{
            map.fitBounds(bounds);
            }

            window.setTimeout(getMarkers,INTERVAL);
          }, "json");
        }

    },2000);

})
</script>

@endsection