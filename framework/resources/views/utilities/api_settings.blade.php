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
.switch input {display:none;}

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

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
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

.custom .nav-link.active {

    background-color: #21bc6c !important;
}

</style>
@endsection
@section("breadcrumb")
<li class="breadcrumb-item">@lang('menu.settings')</li>
<li class="breadcrumb-item active">@lang('menu.api_settings')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('menu.api_settings')
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

        <div>
          <ul class="nav nav-pills custom">
            <li class="nav-item"><a href="#general" data-toggle="tab" class="nav-link active"> @lang('menu.general_settings') <i class="fa"></i></a></li>
            <li class="nav-item"><a href="#firebase" data-toggle="tab" class="nav-link"> @lang('fleet.firebase_settings') <i class="fa"></i></a></li>
            <li class="nav-item"><a href="#serverkey" data-toggle="tab" class="nav-link"> @lang('fleet.app_notification') <i class="fa"></i></a></li>
            <li class="nav-item"><a href="#maps" data-toggle="tab" class="nav-link"> @lang('fleet.maps') <i class="fa"></i></a></li>
          </ul>
        </div>
      <div class="row">
        <div class="col-md-12">
          <div class="tab-content card-body">
            <div class="tab-pane active" id="general">
              {!! Form::open(['url' => 'admin/api-settings','files'=>true,'method'=>'post']) !!}

              <div class="row">
                <div class="form-group col-md-3">
                  <h6>{!! Form::label('api',__('fleet.api'),['class'=>"form-label"]) !!}
                    
                  </h6>
                  <label class="switch">
                  <input type="checkbox" class="api" id="api" name="name[api]" value="1"  @if(Hyvikk::api('api') == "1") checked @endif>
                  <span class="slider round"></span>
                  </label>
                </div>

                @if(Hyvikk::api('api') == "1")
                <div class="form-group col-md-3">
                  <h6>{!! Form::label('google_api',__('fleet.google_places_api'),['class'=>"form-label"]) !!} 
                    <a data-toggle="modal" data-target="#myModal2"><i class="fa fa-info-circle fa-lg" aria-hidden="true"  style="color: #8639dd"></i></a> <br>
                    <strong class="text-muted">(<a href="https://docs.listingprowp.com/knowledgebase/how-to-get-map-working-with-google-api-key/" target="blank">@lang('fleet.api_guide')</a>)</strong>
                  </h6>
                  <label class="switch">
                  <input type="checkbox" name="name[google_api]" value="1" @if(Hyvikk::api('google_api') == "1") checked @endif>
                  <span class="slider round"></span>
                  </label>
                </div>

                <div class="form-group col-md-3">
                  <h6>{!! Form::label('anyone_register',__('fleet.anyone_register'),['class'=>"form-label"]) !!} </h6>
                  <label class="switch">
                  <input type="checkbox" name="name[anyone_register]" value="1" @if(Hyvikk::api('anyone_register') == "1") checked @endif>
                  <span class="slider round"></span>
                  </label>
                </div>

                <div class="form-group col-md-3">
                  <h6>{!! Form::label('driver_review',__('fleet.driver_review'),['class'=>"form-label"]) !!}
                    <a data-toggle="modal" data-target="#myModal3"><i class="fa fa-info-circle fa-lg" aria-hidden="true"  style="color: #8639dd"></i></a>
                  </h6>
                  <label class="switch">
                  <input type="checkbox" name="name[driver_review]" value="1" @if(Hyvikk::api('driver_review') == "1") checked @endif>
                  <span class="slider round"></span>
                  </label>
                </div>

                <div class="form-group col-md-12">
                  {!! Form::label('region_availability',__('fleet.region_availability'),['class'=>"form-label"]) !!}
                  {!! Form::text('name[region_availability]',
                  Hyvikk::api('region_availability'),['class'=>"form-control"]) !!}
                </div>

                <div class="form-group col-md-4">
                  {!! Form::label('max_trip',__('fleet.max_trip'),['class'=>"form-label"]) !!}
                  <div class="input-group mb-3">
                  <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-clock-o"></i></span></div>
                  {!! Form::number('name[max_trip]',Hyvikk::api('max_trip'),['class'=>"form-control trip","required"]) !!}
                  </div>
                </div>

                <div class="form-group col-md-4">
                  {!! Form::label('booking',__('fleet.booking'),['class'=>"form-label"]) !!}
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-calendar-check-o"></i></span></div>
                    {!! Form::number('name[booking]',Hyvikk::api('booking'),['class'=>"form-control",'required']) !!}
                  </div>
                </div>
                <div class="form-group col-md-4">
                  {!! Form::label('cancel',__('fleet.cancel'),['class'=>"form-label"]) !!}
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-calendar-times-o"></i></span>
                    </div>
                    {!! Form::number('name[cancel]',Hyvikk::api('cancel'),['class'=>"form-control"]) !!}
                  </div>
                </div>
                @endif
              </div>

              <div class="col-md-2">
                <div class="form-group">
                  <input type="submit" class="form-control btn btn-success api_btn" value="@lang('fleet.save')" />
                </div>
              </div>

              {!! Form::close()!!}
            </div>

            <div class="tab-pane" id="firebase">
              {!! Form::open(['url' => 'admin/firebase-settings','files'=>true,'method'=>'post']) !!}
              <div class="row">
                <div class="form-group col-md-12">
                  {!! Form::label('db_url',__('fleet.db_url'),['class'=>"form-label"]) !!}
                  <a data-toggle="modal" data-target="#myModal5"><i class="fa fa-info-circle fa-lg" aria-hidden="true"  style="color: #8639dd"></i></a>
                  {!! Form::text('db_url',
                  Hyvikk::api('db_url'),['class'=>"form-control",'required','placeholder'=>'for example: https://PROJECT.firebaseio.com']) !!}
                </div>
                {{-- <div class="form-group col-md-6">
                  {!! Form::label('db_secret',__('fleet.db_secret'),['class'=>"form-label"]) !!}

                  {!! Form::text('db_secret',
                  Hyvikk::api('db_secret'),['class'=>"form-control",'required']) !!}
                </div> --}}
                <div class="col-md-12" style="font-size:20px" >
               
                  <h4>@lang('fleet.fcf') </h4>
                  <h6 class="text-uppercase"><a href="{{asset('assets/samples/firebase_credentials.json')}}" download>@lang('fleet.fsf')</a></h6>
                <ul style="margin-bottom: 1rem;">
                  <li>@lang('fleet.gtf')</li>
                  <li>@lang('fleet.yp')</li>
                  <li>@lang('fleet.ps')</li>
                  <li>@lang('fleet.sa')</li>
                  <li>@lang('fleet.gnpk')</li>
                  <li>@lang('fleet.tgf')Fleet\Framework\Storage\Firebase\Firebase_credentials.Json @lang('fleet.rtff')</li>
                </ul>
                {{-- <p class="text-capitalize" style="margin-bottom: 0rem;"></p> --}}
                
                </div>
                <div class="col-md-6">
                  <button type="submit" class="btn btn-success" style="margin-right: 10px">@lang('fleet.test')</button>
                </div>
                {!! Form::close()!!}
              </div>
            </div>

            <div class="tab-pane" id="serverkey">
              {!! Form::open(['url' => 'admin/store-key','files'=>true,'method'=>'post']) !!}
              <div class="row">
                <div class="form-group col-md-12">
                  {!! Form::label('server_key',__('fleet.legacy_key'),['class'=>"form-label"]) !!}
                  <a data-toggle="modal" data-target="#myModal4"><i class="fa fa-info-circle fa-lg" aria-hidden="true"  style="color: #8639dd"></i></a>
                  {!! Form::text('server_key',
                  Hyvikk::api('server_key'),['class'=>"form-control",'required']) !!}
                </div>
                <div class="col-md-6">
                  <button type="submit" class="btn btn-success" style="margin-right: 10px">@lang('fleet.test')</button>
                </div>
                {!! Form::close()!!}
              </div>
            </div>
            <div class="tab-pane" id="maps">
              {!! Form::open(['url' => 'admin/store-api','files'=>true,'method'=>'post']) !!}
              <div class="row">
                <div class="form-group col-md-12">
                  {!! Form::label('api_key',__('fleet.google_maps'),['class'=>"form-label"]) !!}
                  <a data-toggle="modal" data-target="#myModal6"><i class="fa fa-info-circle fa-lg" aria-hidden="true"  style="color: #8639dd"></i></a>
                  {!! Form::text('api_key',
                  Hyvikk::api('api_key'),['class'=>"form-control",'required']) !!}
                </div>
                <div class="col-md-6">
                  <button type="submit" id="test_api" class="btn btn-success" style="margin-right: 10px">@lang('fleet.test')</button>
                </div>
                {!! Form::close()!!}
              </div>
              <hr>
              <div class="row" style="margin-top: 20px">
                <div class="col-md-12">
                  <div class="form-group">
                    <h6 class="text-danger"> <strong>@lang('fleet.important_Notes'):</strong></h6>
                    <ol class="text-muted">
                      <li>@lang('fleet.gma') <a href="https://developers.google.com/maps/gmp-india-migration#action" target="_blank">@lang('fleet.Read_More')</a></li>
                      <li>@lang('fleet.ymcb') <strong><a href="{{ asset('assets/images/billing-Account.png') }}">@lang('fleet.gmp')</a></strong> @lang('fleet.tuma').</li>
                      <li>@lang('fleet.iusm') <strong>"Success"</strong>, @lang('fleet.imtgaw')</li>
                      <li>@lang('fleet.stm') <strong> @lang('fleet.paga')</strong> @lang('fleet.afe') <a href="https://console.developers.google.com/" target="_blank">@lang('fleet.visit_here')</a></li>
                      <li>@lang('fleet.yccga') - <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">@lang('fleet.giah')</a> </li>
                      <li><strong>@lang('fleet.em')</strong> @lang('fleet.information') <a href="https://developers.google.com/maps/documentation/javascript/error-messages" target="_blank">@lang('fleet.ah')</a></li>
                    </ol>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal 1-->
<div id="myModal1" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.api')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>@lang('fleet.api_info')</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal 1-->

<!-- Modal 2-->
<div id="myModal2" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.google_places_api')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>@lang('fleet.google_places_api_info1')</p>
        <p>@lang('fleet.google_places_api_info2')</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal 2-->

<!-- Modal 3-->
<div id="myModal3" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.driver_review')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>@lang('fleet.driver_review_info')</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal 3-->

<!-- Modal 3-->
<div id="myModal4" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.legacy_key')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>@lang('fleet.legacy_key_info')</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>

<div id="myModal5" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.db_url')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>@lang('fleet.db_url_info')</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>

<div id="myModal6" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.google_maps')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>@lang('fleet.google_maps_info')</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
  $(".api_btn").on("click",function(){
    // alert($(".trip").val());
    if (($(".trip").val())<1) {
        alert("Maximum Time a Trip can Go(in Days) must be greater than or equal to 1");
        return false;
    }else{
      return true;
    }
  });
</script>
<script type="text/javascript">
$(document).ready(function() {
  @if(isset($_GET['tab']) && $_GET['tab']!="")
  $('.nav-pills a[href="#{{$_GET['tab']}}"]').tab('show')
  @endif
});
</script>
<script type="text/javascript">
  $(window).on('load', function() {

    @if(isset($_GET['success']) && $_GET['success'] == 1)
      new PNotify({
        title: 'Success!',
        text: 'Firebase credentials matched.',
        type: 'success',
        delay: 15000
      });
    @elseif(isset($_GET['success']) && $_GET['success'] == 0)
      new PNotify({
        title: 'Failed!',
        text: 'Firebase credentials does not matched, Try again!',
        type: 'error',
        delay: 15000
      });
    @endif

    @if(isset($_GET['key']) && $_GET['key'] == 1)
      new PNotify({
        title: 'Success!',
        text: 'Legacy server key stored successfully.',
        type: 'success',
        delay: 15000
      });
    @elseif(isset($_GET['key']) && $_GET['key'] == 0)
      new PNotify({
        title: 'Failed!',
        text: 'Legacy server key is invalid, Try again!',
        type: 'error',
        delay: 15000
      });
    @endif

    @if(isset($_GET['api_key']) && $_GET['api_key'] == 1)

      new PNotify({
        title: 'Success!',
        text: '{{$_GET["msg"]}}',
        type: 'success',
        delay: 15000
      });
    @elseif(isset($_GET['api_key']) && $_GET['api_key'] == 0)
      new PNotify({
        title: 'Failed!',
        text: '{{$_GET["msg"]}}',
        type: 'error',
        delay: 5000
      });
    @endif

  });
</script>

@if((isset($_GET['test_key']) && $_GET['test_key']!= null) && (isset($_GET['api_key']) && $_GET['api_key'] == 0))
<script type="text/javascript">
  function initMap() {
    var geocoder = new google.maps.Geocoder();
    geocodeAddress(geocoder);

    // $.get('https://maps.googleapis.com/maps/api/geocode/json?latlng=40.714224,-73.961452&key={{ $_GET['test_key'] }}',function(data){
    //   console.log("$.get: "+data.status);
    //   if(data.status == "OK"){
    //     alert("$.get: "+data.status);

    //   }
    //   else{
    //     alert("$.get: "+data.error_message);
    //   }
    // });

  }

  function geocodeAddress(geocoder) {
    geocoder.geocode({'location': {lat: 40.714224, lng: -73.961452 }}, function(results, status) {
      // console.log(results);
      // console.log(status);
      if (status === 'OK') {
        // store api key
        $.ajax({
          type: "GET",
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

          url: "{{url('admin/ajax-api-store/').'/'.$_GET['test_key']}}",

          success: function(data){
            // console.log(data);
            new PNotify({
              title: 'Success!',
              text: 'API key successfully saved',
              type: 'success',
              delay: 15000
            });
          },

          dataType: "json"
        });
      } else {
        alert('Geocode was not successful for the following reason: ' + status);
      }
    });
  }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{$_GET['test_key']}}&callback=initMap">
</script>
@endif
@endsection