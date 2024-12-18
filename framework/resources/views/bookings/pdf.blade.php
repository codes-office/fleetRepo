<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{Hyvikk::get('app_name')}}</title>
  <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">


  <!-- Bootstrap 3.3.7 -->
 <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/cdn/bootstrap.min.css')}}" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('assets/css/cdn/font-awesome.min.css')}}">
  <!-- Ionicons -->
  <link href="{{ asset('assets/css/cdn/ionicons.min.css')}}" rel="stylesheet">
  <!-- Theme style -->
   <link href="{{ asset('assets/css/AdminLTE.min.css') }}" rel="stylesheet">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <style type="text/css">
    img {
    /*-moz-transform: scale(2);
    -ms-transform: scale(2);
    -o-transform: scale(2);
    -webkit-transform: scale(2);
    transform: scale(2);*/
    zoom: 2
}
.page-header {
    padding-bottom: 40px !important;
    }
  </style>
  <!-- Google Font -->
  <link rel="stylesheet" href="{{ asset('assets/css/cdn/fonts.css')}}">
</head>
<body>
  <div class="wrapper">
    <section class="invoice">
      <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-12">
          <h2 class="page-header">
            <span class="logo-lg">
              <img src="{{ asset('assets/images/'. Hyvikk::get('logo_img') ) }}" class="navbar-brand logo_img" style="margin-top: -15px">

            </span>
              <small class="pull-right"> <b>@lang('fleet.date') : </b>{{ isset($booking->date) }}</small>
          </h2>
        </div>
      </div>
      <div class="row invoice-info">
        <div class="col-md-4 invoice-col">
          <b>From</b>
          <address>
            <b>{{  Hyvikk::get('app_name')  }}</b> <br>
           {{Hyvikk::get('badd1')}}
           <br>
           {{Hyvikk::get('badd2')}}
           <br>
           {{Hyvikk::get('city')}},

           {{Hyvikk::get('state')}}
           <br>
           {{Hyvikk::get('country')}}
          </address>
        </div>
        <div class="col-md-4 invoice-col">
         <b> To</b>
          <address>
            <b>{{isset( $booking->customer->name) }}</b> <br>
            {!! nl2br(e($booking->customer->getMeta('address'))) !!}
          </address>
        </div>

        <div class="col-md-4 invoice-col">
          <b>Invoice#</b>
               {{ $booking->id }}


        </div>

      </div>

      <div class="row">
        <div class="col-md-6 invoice-col">
         <strong> @lang('fleet.pickup_addr'):</strong>
          <address>
           {{$booking->pickup_addr}}
           <br>
           @lang('fleet.journeyDateTime'):
          <b>{{date('d/m/Y',strtotime($booking->journey_date))}}
       {{date('g:i A',strtotime($booking->journey_time))}}</b>
          </address>
        </div>

        <div class="col-md-6 invoice-col">
          <strong>@lang('fleet.dropoff_addr'):</strong>
          <address>
            {{$booking->dest_addr}}
            <br>
            @lang('fleet.dropoff'):
            <b>@if($booking->dropoff){{date('d/m/Y g:i A',strtotime($booking->dropoff))}}@endif</b>
          </address>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          @if(Hyvikk::get('invoice_text') != null)
          <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
           {{Hyvikk::get('invoice_text')}}
          </p>
          @endif
        </div>
        <div class="col-md-6 pull-right">
          <p class="lead"></p>
          <div class="table-responsive">
            <table class="table">
              <tr>
                <th>@lang('fleet.bookingOption'):</th>
                <td>{{ $booking->booking_option }}</td>
              </tr>
              @if($booking->vehicle_id??'' != null)
              <tr>
                <th style="width:50%">@lang('fleet.vehicle'):</th>
                <td> {{$booking->make_name}} - {{$booking->model_name}} - {{$booking->vehicle['license_plate']}}</td>
              </tr>
              @endif
              @if($booking->driver_id != null)
              <tr>
                <th>@lang('fleet.driver'):</th>
                <td>{{ $booking->driver->name }}</td>
              </tr>
              @endif
              <tr>
                <th>@lang('fleet.travel_time'):</th>
                <td>@if($booking->driving_time) {{ $booking->driving_time }} @endif</td>
              </tr>

              <tr>
                <th>@lang('fleet.mileage'):</th>
                <td>{{ $booking->total_kms }} {{ Hyvikk::get('dis_format') }}</td>
              </tr>
              @if($booking->booking_option != "Route")
              <tr>
                <th>@lang('fleet.waitingtime'):</th>
                <td>
                  {{ ($booking->getMeta('waiting_time'))?$booking->getMeta('waiting_time'):0 }}
                </td>
              </tr>
              @endif

              <tr>
                <th>@lang('fleet.amount'):</th>
                <td>{{ Hyvikk::get('currency') }} {{ $booking->total }} </td>
              </tr>
              <tr>
                <th>@lang('fleet.total_tax') (%) :</th>
                <td>{{ ($booking->total_tax_percent) ? $booking->total_tax_percent : 0 }} %</td>
              </tr>
              <tr>
                <th>@lang('fleet.total') @lang('fleet.tax_charge') :</th>
                <td>{{ Hyvikk::get('currency') }} {{ ($booking->total_tax_charge_rs) ? $booking->total_tax_charge_rs : 0 }} </td>
              </tr>
              <tr>
                <th>@lang('fleet.total'):</th>
                <td>{{ Hyvikk::get('currency') }} {{ $booking->tax_total }}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>
</body>
</html>