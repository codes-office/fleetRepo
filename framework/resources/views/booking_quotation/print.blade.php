<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{Hyvikk::get('app_name')}}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/cdn-bootstrap.min.css')}}" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('assets/css/cdn-font-awesome.min.css')}}">
  <!-- Ionicons -->
  <link href="{{ asset('assets/css/cdn-ionicons.min.css')}}" rel="stylesheet">
  <!-- Theme style -->
  <link href="{{ asset('assets/css/AdminLTE.min.css') }}" rel="stylesheet">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="{{ asset('assets/css/cdn/fonts.css')}}">
</head>
<body onload="window.print();">
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')
  
  <div class="wrapper">
    <section class="invoice">
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <span class="logo-lg">
              <img src="{{ asset('assets/images/'. Hyvikk::get('icon_img') ) }}" class="navbar-brand" style="margin-top: -15px">
              {{  Hyvikk::get('app_name')  }}
            </span>
              <small class="pull-right"> <b>@lang('fleet.date') : </b>{{ date($date_format_setting) }}</small>
          </h2>
        </div>
      </div>
      <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
          <b>From</b>
          <address>
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
        <div class="col-sm-4 invoice-col">
         <b> To</b>
          <address>
            {!! nl2br(e($data->customer->getMeta('address'))) !!}
          </address>
        </div>

        <div class="col-sm-4 invoice-col">
          <b>@lang('fleet.bookingQuote')#</b>
               {{ $data->id }}
          <br>
          <b>{{ $data->customer->name }}</b>
        </div>

      </div>

      <div class="row">
        <div class="col-sm-6 invoice-col">
         <strong> @lang('fleet.pickup_addr'):</strong>
          <address>
           {{$data->pickup_addr}}
           <br>
           @lang('fleet.pickup'):
          <b> {{date($date_format_setting.' g:i A',strtotime($data->pickup))}}</b>
          </address>
        </div>

        <div class="col-sm-6 invoice-col">
          <strong>@lang('fleet.dropoff_addr'):</strong>
          <address>
            {{$data->dest_addr}}
            <br>
            @lang('fleet.dropoff'):
            <b>{{date($date_format_setting.' g:i A',strtotime($data->dropoff))}}</b>
          </address>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-6">
          @if(Hyvikk::get('invoice_text') != null)
          <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
           {{Hyvikk::get('invoice_text')}}
          </p>
          @endif
        </div>
        <div class="col-xs-6 pull-right">
          <p class="lead"></p>
          <div class="table-responsive">
            <table class="table">
               @if($data->vehicle_id != null)
              <tr>
                <th style="width:50%">@lang('fleet.vehicle'):</th>
                <td> {{$data->vehicle->make_name}} - {{$data->vehicle->model_name}} - {{$data->vehicle['license_plate']}}</td>
              </tr>
              @endif
              @if($data->driver_id != null)
              <tr>
                <th>@lang('fleet.driver'):</th>
                <td>{{ $data->driver->name }}</td>
              </tr>
              @endif
              <tr>
                <th>@lang('fleet.mileage'):</th>
                <td>{{ $data->mileage }} {{ Hyvikk::get('dis_format') }}</td>
              </tr>
              <tr>
                <th>@lang('fleet.waitingtime'):</th>
                <td>
                {{ $data->waiting_time }}
                </td>
              </tr>
              <tr>
                <th>@lang('fleet.amount'):</th>
                <td>{{ Hyvikk::get('currency') }} {{ $data->total }}</td>
              </tr>
              <tr>
                <th>@lang('fleet.total_tax') (%) :</th>
                <td>{{ ($data->total_tax_percent) ? $data->total_tax_percent : 0 }} %</td>
              </tr>
              <tr>
                <th>@lang('fleet.total') @lang('fleet.tax_charge') :</th>
                <td>{{ Hyvikk::get('currency') }} {{ ($data->total_tax_charge_rs) ? $data->total_tax_charge_rs : 0 }} </td>
              </tr>
              <tr>
                <th>@lang('fleet.total'):</th>
                <td>{{ Hyvikk::get('currency') }} {{ ($data->tax_total) ? $data->tax_total : $data->total }}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>
</body>
</html>