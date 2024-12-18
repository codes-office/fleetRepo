@extends('layouts.app')
@section('content')
@php($api1="API_KEY")
@php($api2="API_KEY")
@endsection
    @section('script')
<script type="text/javascript">
  function initMap() {
    var geocoder = new google.maps.Geocoder();
    geocodeAddress(geocoder);

      $.get('https://maps.googleapis.com/maps/api/geocode/json?latlng=40.714224,-73.961452&key={{ $api1 }}',function(data){
        console.log("$.get: "+data.status);
        if(data.status == "OK"){
          alert("json status: "+data.status+ ", api key: {{ $api1 }}");

        }
        else{
          alert("jason : "+data.status+ ", api key: {{ $api1 }}");
        }
      });

  }

  function geocodeAddress(geocoder) {
    geocoder.geocode({'location': {lat: 40.714224, lng: -73.961452 }}, function(results, status) {
      console.log("js file: "+status);
      if (status === 'OK') {
        alert("javascript status: "+status + ", api key: {{ $api1 }}");
      } else {
        alert('Geocode was not successful for the following reason: ' + status);
      }
    });
  }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $api1 }}&callback=initMap">
</script>
@endsection