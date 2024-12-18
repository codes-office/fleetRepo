<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ Hyvikk::get('app_name') }}</title>

<link rel="icon" href="{{ asset('favicon.ico') }}" type="image/png">

<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/cdn-bootstrap.min.css')}}" />
<link href="{{ asset('assets/css/cdn-ionicons.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/cdn-font-awesome.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/cdn-jquery-ui.min.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/cdn-dataTables.bootstrap.min.css')}}">
<link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/AdminLTE.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/skins-all-skins.min.css') }}" rel="stylesheet">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

@yield("extra_css")
<script>
window.Laravel = {!! json_encode([
'csrfToken' => csrf_token(),
]) !!};


</script>
</head>
<body class="hold-transition skin-black-light sidebar-mini">
  <div class="wrapper">
    <section class="content">
      <div class="row col-md-8 col-md-offset-2 text-center">
        <h1 style="font-size: 5em"><i class="fa fa-warning text-danger"></i></h1>
        <h1 class="headline text-danger"> Failed!</h1>
      </div>
      <div class="row col-md-8 col-md-offset-2 text-center">
        <h4> Oops! Transaction Failed.</h4>
        <p>
          <strong>Error Message: </strong>{{ $error_msg }}
        </p>
        <p>
          <a href="{{ url("/")}}"> <i class="fa fa-backward"></i> BACK TO HOME</a>
        </p>
      </div>
    </section>
  </div>
</body>
</html>