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
  {{--
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/square/blue.css')}}"> --}}
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  {{-- <link rel="stylesheet" href="{{asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}" /> --}}

  @yield("extra_css")
  <style>
    i.fa {
      padding: 0 !important;
      margin: 0 !important;
    }

    .input-group-text {
      width: 41px !important;
      padding: 0 !important;
      margin: 0 !important;
      text-align: center;
      display: flex;
      align-items: center;
      vertical-align: middle;
      width: 100%;
      justify-content: center;
    }
  </style>
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
      <center> <img src="{{ asset('assets/images/'. Hyvikk::get('logo_img') ) }}" height="140px" width="300px" />
      </center>
    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">@lang('passwords.login_desc')</p>
        {{-- Sign in to start your session --}}
        <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
          {{ csrf_field() }}
          <div class="form-group has-feedback">
            <div class="input-group ">
              <input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                placeholder="@lang('passwords.email')" name="email" value="{{ old('email') }}" id="email" autofocus
                required>
              <div class="input-group-append">
                <span class="input-group-text"><i class="fa fa-envelope"></i></span> {{-- this class was added
                form-control-feedback --}}

              </div>

              @if ($errors->has('email'))
              <span class="invalid-feedback">
                <strong class="text-danger">{{ $errors->first('email') }}</strong>
              </span>
              @endif
            </div>
          </div>
          <div class="form-group has-feedback">
            <div class="input-group">
              <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
                placeholder="@lang('passwords.password')" id="password" name="password" required>
              <div class="input-group-append">
                <span class="fa fa-lock  input-group-text"></span> {{-- this class was added form-control-feedback --}}

              </div>
              @if ($errors->has('password'))
              <span class="invalid-feedback">
                <strong class="text-danger">{{ $errors->first('password') }}</strong>
              </span>
              @endif
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} id="remember">
                <label for="remember"> @lang('passwords.remember')</label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">@lang('passwords.sign_in')
                {{-- Sign In --}}
              </button>
            </div>
            <!-- /.col -->
          </div>
        </form>
        <!-- /.social-auth-links -->
        <p class="mb-1">
          <a href="{{ route('password.request') }}">@lang('passwords.forgot_password')
            {{-- I forgot my password --}}
          </a>
        </p>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
  <!-- /.login-box -->
  <!-- iCheck -->
  <script>
    // $(function () {
    // $('input').iCheck({
    //   checkboxClass: 'icheckbox_square-blue',
    //   radioClass   : 'iradio_square-blue',
    //   increaseArea : '20%' // optional
    // })
  // })
  </script>
</body>

</html>