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
  <link rel="stylesheet" href="{{ asset('assets/css/cdn-fonts.css')}}">
  <style type="text/css">
    body {
      height: auto;
    }
    @media print{@page {size: landscape}}
  </style>
</head>
<body onload="window.print();">
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

  <div class="wrapper">
  <!-- Main content -->
    <section class="invoice">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
          <span class="logo-lg">
          <img src="{{ asset('assets/images/'. Hyvikk::get('icon_img') ) }}" class="navbar-brand" style="margin-top: -15px">
          {{  Hyvikk::get('app_name')  }}
          </span>

            <small class="pull-right"> <b>@lang('fleet.date') : </b> {{date($date_format_setting)}}</small>
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <div class="row">
        <div class="col-md-12 text-center">
          <h3>@lang('fleet.booking_report')&nbsp;<small>{{date('F', mktime(0, 0, 0, $month_select, 10))}}-{{$year_select}}</small></h3>

        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <table class="table table-bordered" id="data_table">
            <thead class="thead-inverse">
              <tr>
                <th>@lang('fleet.customer')</th>
                <th>@lang('fleet.vehicle')</th>
                <th style="width:10% !important">@lang('fleet.pickup_addr')</th>
                <th style="width:10% !important">@lang('fleet.dropoff_addr')</th>
                <th>@lang('fleet.from_date')</th>
                <th>@lang('fleet.to_date')</th>
                <th>@lang('fleet.passengers')</th>
                <th>@lang('fleet.status')</th>
              </tr>
            </thead>
            <tbody>
            @foreach($bookings as $row)
              <tr>
                <td>{{$row->customer->name}}</td>
                <td>
                  @if($row->vehicle_id != null)
                  {{$row->vehicle->make_name}} - {{$row->vehicle->model_name}} - {{$row->vehicle->license_plate}}
                  @endif
                </td>
                <td style="width:10% !important">{!! str_replace(",", ",<br>", $row->pickup_addr) !!}</td>
                <td style="width:10% !important">{!! str_replace(",", ",<br>", $row->dest_addr) !!}</td>
                <td>{{date($date_format_setting.' g:i A',strtotime($row->pickup))}}</td>
                <td>{{date($date_format_setting.' g:i A',strtotime($row->dropoff))}}</td>
                <td>{{$row->travellers}}</td>
                <td>@if($row->status == 0)<span style="color:orange;">@lang('fleet.journey_not_ended')</span> @else <span style="color:green;">@lang('fleet.journey_ended')</span> @endif</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </div>
  <!-- ./wrapper -->
</body>
</html>