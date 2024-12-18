@extends("layouts.app")
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{url('admin/driver-maps')}}">@lang('fleet.maps')</a></li>
<li class="breadcrumb-item active">@lang('fleet.driver_tracking')</li>
@endsection
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
        @lang('fleet.driver_tracking') : <small> {{$driver["name"]}} </small>
        </h3>
      </div>

      <div class="card-body">
        <div class="row">
      	  <div style="width: 100%; height: 400px;" id="map_canvas"></div>
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

  $(document).ready(function() {
    setTimeout(function(){
         //Time between marker refreshes
        var INTERVAL = 5000;

        //Used to remember markers
        var markerStore = {};

        // var myLatlng = new google.maps.LatLng(30.2353412,-92.010498);

        var myLatlng = new google.maps.LatLng("{{$driver['position']['lat']}}","{{$driver['position']['long']}}");
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
            }, "json");

            $.get('{{url("admin/single-driver")."/".$driver["id"]}}', {}, function(res,resp) {

             var infowindow = new google.maps.InfoWindow();
            for(var i=0, len=res.length; i<len; i++) {

              //Do we have this marker already?
              // console.log(markerStore.hasOwnProperty(res[i].id));
              if(markerStore.hasOwnProperty(res[i].id)) {
                markerStore[res[i].id].setPosition(new google.maps.LatLng(res[i].position.lat,res[i].position.long));
                // console.log('just funna move it...');
                markerStore[res[i].id].setIcon('{{asset("assets/images/")}}/'+res[i].icon);
                markerStore[res[i].id].setTitle(res[i].name+" ("+res[i].status+")");

              }
              else {
                var marker = new google.maps.Marker({
                  position: new google.maps.LatLng(res[i].position.lat,res[i].position.long),
                  title:res[i].name+" ("+res[i].status+")",
                  map:map,
                  // draggable: true,
                  icon:'{{asset("assets/images/")}}/'+res[i].icon
                });

                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                  return function() {
                    infowindow.setContent(res[i].name);
                    infowindow.open(map, marker);

                  }
                })(marker, i));
                google.maps.event.addListener(marker,'position_changed', function() {
                      // alert(marker.getPosition());
                      // map.setCenter(marker.getPosition());

                  });

                markerStore[res[i].id] = marker;

              }
            }

          }, "json");
            window.setTimeout(getMarkers,INTERVAL);
        }
    },2000);
});
</script>
@endsection