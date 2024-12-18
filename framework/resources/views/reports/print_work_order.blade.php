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
          <h3>@lang('fleet.work_order_report')&nbsp;<small>{{date('F', mktime(0, 0, 0, $month_select, 10))}}-{{$year_select}}</small></h3>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="table-responsive">
            <table class="table table-responsive table-bordered" id="data_table" style="padding-bottom: 35px">
              <thead>
                <th>@lang('fleet.vehicle_name')</th>
                <th>@lang('fleet.created_on')</th>
                <th>@lang('fleet.required_by')</th>
                <th>@lang('fleet.vendor_name')</th>
                <th>@lang('fleet.description')</th>
                <th>@lang('fleet.work_order_price')</th>
                <th>@lang('fleet.total') @lang('fleet.parts') @lang('fleet.cost')</th>
                <th>@lang('fleet.total_cost')</th>
                <th>@lang('fleet.meter')</th>
                <th>@lang('fleet.status')</th>
              </thead>
              <tbody>
               @foreach($data as $row)
                <tr>
                  <td>{{$row->vehicle['year']}}
                    {{$row->vehicle->make_name}} - {{$row->vehicle->model_name}}
                    <br>
                    <b> @lang('fleet.vin'): </b>{{$row->vehicle['vin']}}
                    <br>
                    <b> @lang('fleet.plate'):</b> {{$row->vehicle['license_plate']}}
                  </td>
                  <td>{{ date($date_format_setting,strtotime($row->created_at)) }}</td>
                  <td> {{date($date_format_setting,strtotime($row->required_by))}}</td>
                  <td>{{$row->vendor->name}}</td>
                  <td>{{$row->description}}</td>
                  <td> {{Hyvikk::get('currency')}} {{$row->price}}</td>
                  <td> {{Hyvikk::get('currency')}} {{ $row->parts->sum('total') }}</td>
                  <td> {{Hyvikk::get('currency')}} {{ $row->price + $row->parts->sum('total') }}</td>
                  <td>{{$row->meter}}</td>
                  <td>{{$row->status}}</td>
                  {{-- <td>{{$row->note}}</td> --}}
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>
<!-- ./wrapper -->
</body>
</html>