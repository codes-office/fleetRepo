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
      <p class="login-box-msg">@lang('fleet.enter_customer_details')</p>
      <form method="POST" name="checkout" action="https://api.razorpay.com/v1/checkout/embedded">
        {{ csrf_field() }}
        {!! Form::hidden('booking_id',$booking_id) !!}
        <input type="hidden" class="form-control" name="key_id" value="{{Hyvikk::payment('razorpay_key')}}">
      <input type="hidden" name="name" value="{{ Hyvikk::get('app_name') }}">
      <input type="hidden" name="amount" value="{{ $amount }}">
      <input type="hidden" name="order_id" value="{{$order_id}}">
      <input type="hidden" name="callback_url" value="{{ url('razorpay-success?booking_id='.$booking_id) }}">
      <input type="hidden" name="cancel_url" value="{{ url('razorpay-failed') }}">
        <div class="form-group has-feedback">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="fa fa-user form-control-feedback input-group-text"></span>
            </div>
            <input type="text" class="form-control" placeholder="Name" name="prefill[name]"id="name" autofocus required>
          </div>
        </div>
        <div class="form-group has-feedback">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="fa fa-phone form-control-feedback input-group-text"></span>
            </div>
            <input type="text" class="form-control" placeholder="Contact Number" name="prefill[contact]" id="contact" required>
          </div>
        </div>
        <div class="form-group has-feedback">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="fa fa-envelope form-control-feedback input-group-text"></span>
            </div>
            <input type="email" class="form-control" placeholder="Email" name="prefill[email]" id="email" required>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block btn-flat">@lang('fleet.make_payment')</button>
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