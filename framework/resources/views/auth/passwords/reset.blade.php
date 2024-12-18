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

  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  <script>
    window.Laravel = {!! json_encode([
    'csrfToken' => csrf_token(),
    ]) !!};
  </script>
</head>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <center> <img src="{{ asset('assets/images/'. Hyvikk::get('logo_img') ) }}" height="140px" width="300px"/> </center>
    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        @if (session('status'))
          <div class="alert alert-success">
              {{ session('status') }}
          </div>
        @endif
        <p class="login-box-msg">{{-- Reset Password --}}@lang('passwords.reset_password')</p>
        <form class="form-horizontal" role="form" method="POST" action="{{ route('password.request') }}">
          {{ csrf_field() }}
          <input type="hidden" name="token" value="{{ $token }}">

          <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
            <div class="input-group">
                        <div class="input-group-prepend">
              <span class="fa fa-envelope form-control-feedback input-group-text"></span>
            </div>
            <input type="email" class="form-control" placeholder="@lang('passwords.email')" name="email" value="{{ $email }}" id="email" autofocus required readonly>
            @if ($errors->has('email'))
              <span class="help-block">
                <strong class="text-danger">{{ $errors->first('email') }}</strong>
              </span>
            @endif
            </div>
          </div>

          <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
            <div class="input-group">
                        <div class="input-group-prepend">
              <span class="fa fa-lock form-control-feedback input-group-text"></span>
            </div>
           <input id="password" type="password" class="form-control" name="password" required placeholder="@lang('passwords.password')">
            @if ($errors->has('password'))
              <span class="help-block">
                <strong class="text-danger">{{ $errors->first('password') }}</strong>
              </span>
            @endif
            </div>
          </div>

          <div class="form-group has-feedback {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
            <div class="input-group">
                        <div class="input-group-prepend">
              <span class="fa fa-lock form-control-feedback input-group-text"></span>
            </div>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required placeholder="@lang('passwords.confirm_password')">{{-- Confirm Password --}}
            @if ($errors->has('password_confirmation'))
              <span class="help-block">
                  <strong class="text-danger">{{ $errors->first('password_confirmation') }}</strong>
              </span>
            @endif
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary btn-block btn-flat">@lang('passwords.reset_password'){{-- Reset Password --}}</button>
            </div>
            <!-- /.col -->
          </div>
        </form>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{asset('assets/js/plugins-jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('assets/js/plugins-bootstrap.bundle.min.js')}}"></script>
</body>
</html>