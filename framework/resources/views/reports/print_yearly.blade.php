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
          <h3>@lang('fleet.yearlyReport')&nbsp;<small>{{$year_select}}</small></h3>
          @if($vehicle != null)<h4>{{$vehicle->make_name}}-{{$vehicle->model_name}}-{{$vehicle['license_plate']}}</h4> @endif
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <h3>@lang('fleet.income')-@lang('fleet.expense')</h3>
          <table class="table table-bordered table-striped table-hover">
            @php ($income_amt = (is_null($income[0]->income) ? 0 : $income[0]->income))
            @php ($expense_amt = (is_null($expenses[0]->expense) ? 0 : $expenses[0]->expense))
            <thead>
              <tr>
                <th scope="row">@lang('fleet.pl')</th>

                <td><strong>{{ Hyvikk::get("currency")}}{{ $income_amt-$expense_amt}}</strong></td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th scope="row">@lang('fleet.income')</th>
                <td>{{ Hyvikk::get("currency")}}{{$income_amt}}</td>
              </tr>
              <tr>
                <th scope="row">@lang('fleet.expenses')</th>
                <td>{{ Hyvikk::get("currency")}}{{$expense_amt}}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-md-4">
          <h3>@lang('fleet.incomeByCategory')</h3>
          <table class="table table-bordered table-striped table-hover">
            @php($tot = 0)
            @foreach ($income_by_cat as $exp)
            @php($tot = $tot + $exp->amount)
            @endforeach
            <thead>
              <tr>
                <th scope="row">@lang('fleet.incomeByCategory')</th>
                <td>{{ Hyvikk::get("currency")}}{{$tot}}</td>
              </tr>
            </thead>
            <tbody>
              @foreach($income_by_cat as $exp)
              <tr>
                <th scope="row">{{$income_cats[$exp->income_cat]}}</th>
                <td>{{ Hyvikk::get("currency")}}{{$exp->amount}}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="col-md-4">
          <h3>@lang('fleet.expensesByCategory')</h3>
          <table class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                @php($tot = 0)
                @foreach ($expense_by_cat as $exp)
                @php($tot = $tot + $exp->expense)
                @endforeach
                <th scope="row">@lang('fleet.expensesByCategory')</th>
                <td><strong>{{ Hyvikk::get("currency")}}{{$tot}}</strong></td>
              </tr>
            </thead>
            <tbody>
              @foreach($expense_by_cat as $exp)
              <tr>
                <th scope="row">
                @if($exp->type == "s")
                {{$service[$exp->expense_type]}}
                @else
                {{$expense_cats[$exp->expense_type]}}
                @endif
                </th>
                <td>{{ Hyvikk::get("currency")}}{{$exp->expense}}</td>
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