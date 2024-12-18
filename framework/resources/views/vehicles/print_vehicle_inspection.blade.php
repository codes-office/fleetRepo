<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{Hyvikk::get('app_name')}}</title>
  <!-- Tell be browser to be responsive to screen widb -->
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

          <small class="pull-right"> <b>@lang('fleet.date') :</b> {{date($date_format_setting)}}</small>
        </h2>
      </div>
      <!-- /.col -->
    </div>

    <div class="row">
      <div class="col-md-12 text-center">
        <h3>@lang('fleet.vehicle_inspection')&nbsp;<small>{{$review->vehicle->make_name}}-{{$review->vehicle->model_name}}-{{$review->vehicle->types['displayname']}}</small></h3>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <table class="table" id="data_table">
          <tr>
            <td><b>@lang('fleet.vehicle'): </b>
            {{$review->vehicle->make_name}} - {{$review->vehicle->model_name}} - {{$review->vehicle->types['displayname']}}</td>
          </tr>
          <tr>
            <td><b>@lang('fleet.review_by'): </b>
              {{$review->user->name}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.reg_no'): </b>
            {{$review->reg_no}}</td>
          </tr>
          <tr>
            <td><b>@lang('fleet.kms_out') ({{Hyvikk::get('dis_format')}}): </b>
            {{$review->kms_outgoing}} {{Hyvikk::get('dis_format')}}</td>
          </tr>
          <tr>
            <td><b>@lang('fleet.kms_in') ({{Hyvikk::get('dis_format')}}): </b>
            {{$review->kms_incoming}} {{Hyvikk::get('dis_format')}}</td>
          </tr>
          <tr>
            <td><b>@lang('fleet.fuel_out'): </b>

              @if($review->fuel_level_out == 0) 1/4 @endif
              @if($review->fuel_level_out == 1) 1/2 @endif
              @if($review->fuel_level_out == 2) 3/4 @endif
              @if($review->fuel_level_out == 3) Full Tank @endif
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.fuel_in'): </b>

              @if($review->fuel_level_in == 0) 1/4 @endif
              @if($review->fuel_level_in == 1) 1/2 @endif
              @if($review->fuel_level_in == 2) 3/4 @endif
              @if($review->fuel_level_in == 3) Full Tank @endif
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.datetime_out'): </b>
            {{date($date_format_setting.' g:i A',strtotime($review->datetime_outgoing))}}</td>
          </tr>
          <tr>
            <td><b>@lang('fleet.datetime_in'): </b>
            {{date($date_format_setting.' g:i A',strtotime($review->datetime_incoming))}}</td>
          </tr>
          <tr>
            <td><b>@lang('fleet.petrol_card'): </b>

              @php($petrol_card=unserialize($review->petrol_card))
              @if($petrol_card['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($petrol_card['flag'] == 0 && $petrol_card['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp;{{$petrol_card['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.lights'): </b>

              @php($light=unserialize($review->lights))
              @if($light['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($light['flag'] == 0 && $light['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$light['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.invertor'): </b>

              @php($invertor=unserialize($review->invertor))
              @if($invertor['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($invertor['flag'] == 0 && $invertor['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$invertor['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.car_mats'): </b>

              @php($car_mat=unserialize($review->car_mats))
              @if($car_mat['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($car_mat['flag'] == 0 && $car_mat['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$car_mat['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.int_damage'): </b>

               @php($int_damage=unserialize($review->int_damage))
                @if($int_damage['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                  @if($int_damage['flag'] == 0 && $int_damage['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                  &nbsp; {{$int_damage['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.int_lights'): </b>

              @php($int_lights=unserialize($review->int_lights))
              @if($int_lights['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($int_lights['flag'] == 0 && $int_lights['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$int_lights['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.ext_car'): </b>

              @php($ext_car=unserialize($review->ext_car))
              @if($ext_car['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($ext_car['flag'] == 0 && $ext_car['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$ext_car['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.tyre'): </b>

              @php($tyre=unserialize($review->tyre))
              @if($tyre['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($tyre['flag'] == 0 && $tyre['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$tyre['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.ladder'): </b>

              @php($ladder=unserialize($review->ladder))
              @if($ladder['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($ladder['flag'] == 0 && $ladder['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$ladder['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.leed'): </b>

              @php($leed=unserialize($review->leed))
              @if($leed['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($leed['flag'] == 0 && $leed['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$leed['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.power_tool') : </b>

              @php($power_tool=unserialize($review->power_tool))
              @if($power_tool['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($power_tool['flag'] == 0 && $power_tool['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$power_tool['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.ac'): </b>

              @php($ac=unserialize($review->ac))
              @if($ac['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($ac['flag'] == 0 && $ac['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$ac['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.head_light'): </b>

              @php($head_light=unserialize($review->head_light))
              @if($head_light['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($head_light['flag'] == 0 && $head_light['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$head_light['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.lock'): </b>

              @php($lock=unserialize($review->lock))
              @if($lock['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($lock['flag'] == 0 && $lock['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$lock['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.windows'): </b>

              @php($windows=unserialize($review->windows))
              @if($windows['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($windows['flag'] == 0 && $windows['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$windows['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.condition'): </b>

              @php($condition=unserialize($review->condition))
              @if($condition['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($condition['flag'] == 0 && $condition['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$condition['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.oil_chk'): </b>

              @php($oil_chk=unserialize($review->oil_chk))
              @if($oil_chk['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($oil_chk['flag'] == 0 && $oil_chk['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$oil_chk['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.suspension'): </b>

              @php($suspension=unserialize($review->suspension))
              @if($suspension['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($suspension['flag'] == 0 && $suspension['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$suspension['text']}}
            </td>
          </tr>
          <tr>
            <td><b>@lang('fleet.tool_box'): </b>

              @php($tool_box=unserialize($review->tool_box))
              @if($tool_box['flag'] == 1) <i class="fa fa-check fa-lg"></i> @endif
                @if($tool_box['flag'] == 0 && $tool_box['flag'] != null) <i class="fa fa-times fa-lg"></i> @endif
                &nbsp; {{$tool_box['text']}}
            </td>
          </tr>
        </table>
      </div>
    </div>
  </section>
  </div>
  <!-- ./wrapper -->
</body>
</html>