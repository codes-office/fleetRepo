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
  <div class="wrapper">
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

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
          <h3>@lang('fleet.income') @lang('fleet.report')&nbsp;<small>{{date('F', mktime(0, 0, 0, $month_select, 10))}}-{{$year_select}}</small></h3>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="table-responsive">
            <table class="table table-responsive table-bordered" id="data_table" style="padding-bottom: 35px">
              <thead>
                <tr>
                  <th>@lang('fleet.make')</th>
                  <th>@lang('fleet.model')</th>
                  <th>@lang('fleet.licensePlate')</th>
                  <th>@lang('fleet.incomeType')</th>
                  <th>@lang('fleet.date')</th>
                  <th>@lang('fleet.amount')</th>
                  <th>@lang('fleet.mileage') ({{Hyvikk::get('dis_format')}})</th>
                </tr>
              </thead>
              <tbody>
                @foreach($income as $row)
                <tr>
                  <td>
                      @if($row->vehicle_id != null)
                      {{$row->vehicle->make_name}}
                      @endif
                    </td>
                    <td>
                      @if($row->vehicle_id != null)
                      {{$row->vehicle->model_name}}
                      @endif
                    </td>
                    <td>
                      @if($row->vehicle_id != null)
                      {{$row->vehicle->license_plate}}
                      @endif
                    </td>
                    <td>{{$row->category->name}}</td>
                    <td>{{date($date_format_setting,strtotime($row->date))}}</td>
                    <td>{{Hyvikk::get('currency')}} {{$row->amount}}</td>
                    <td>{{$row->mileage}}</td>
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
