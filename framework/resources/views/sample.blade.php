<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ Hyvikk::get('app_name') }}</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/png">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('assets/css/dist-adminlte.min.css')}}">
  <!-- iCheck -->
  {{-- <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/square/blue.css')}}"> --}}
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  @yield("extra_css")
  <script>
    window.Laravel = {!! json_encode([
    'csrfToken' => csrf_token(),
    ]) !!};
  </script>
</head>
<body class="hold-transition login-page">
  <!-- fleet manager version 4.0.2 -->
<div class="login-box">
  <div class="login-logo">
    <center> <img src="{{ asset('assets/images/'. Hyvikk::get('logo_img') ) }}" height="140px" width="300px" /> </center>
  </div>
  
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      
      <div style="margin: 5px;">
        <button class="btn btn-info" id="ajax">Ajax call</button>
        
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-secondary btn-flat pull-right"> <i class="fa fa-sign-out"></i>
        @lang('menu.logout')
        </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          {{ csrf_field() }}
      </form>
      </div>
      <!-- /.social-auth-links -->
      <p class="mb-1">
       
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- iCheck -->
{{-- <script src="{{asset('assets/plugins/iCheck/icheck.min.js')}}"></script> --}}
<script>
$("#ajax").on("click",function(){    
    $.ajax({
      type: "GET",
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

      url: "{{url('admin/fetch-data')}}",
     
      success: function(data){
        console.log(data);
      },
      dataType: "json"
    });
});
</script>
</body>
</html>