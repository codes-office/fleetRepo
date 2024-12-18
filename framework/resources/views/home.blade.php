@extends('layouts.app')
@php
$dk=array_keys($dates);
@endphp
@section('content')
<div class="row">
  @can('Users list')
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box">
      <span class="info-box-icon bg-info"><i class="fa fa-users"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">@lang('fleet.users')</span>
        <span class="info-box-number">{{$users}}</span>
      </div>
    </div>
  </div>
  @endcan
  @can('Drivers list')
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box">
      <span class="info-box-icon bg-info"><i class="fa fa-id-card"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">@lang('fleet.drivers')</span>
        <span class="info-box-number">{{$drivers}}</span>
      </div>
    </div>
  </div>
  @endcan
  <div class="clearfix hidden-md-up"></div>
  @can('Customer list')
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box">
      <span class="info-box-icon bg-info"><i class="fa fa-address-card"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">@lang('fleet.customers')</span>
        <span class="info-box-number">{{$customers}}</span>
      </div>
    </div>
  </div>
  @endcan

  @can('Vehicles list')
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box">
      <span class="info-box-icon bg-info"><i class="fa fa-taxi"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">@lang('fleet.vehicles')</span>
        <span class="info-box-number">{{$vehicles}}</span>
      </div>
    </div>
  </div>
  @endcan



</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">@lang('fleet.datewise')</h5>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-md-10">
            <p class="text-center"><strong>@lang('fleet.Transaction'): @if (count($dk) > 0){{$dk[0]}} - {{end($dk)}} @endif
              </strong></p>
            <div class="chart">
              @php($useragent = $_SERVER['HTTP_USER_AGENT'])
              @if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge
              |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm(
              os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows
              ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a
              wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r
              |s
              )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1
              u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp(
              i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac(
              |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt
              |kwc\-|kyo(c|k)|le(no|xi)|lg(
              g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-|
              |o|v)|zz)|mt(50|p1|v
              )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v
              )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-|
              )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4)))
              @php($height="600")
              @else
              @php($height="250")
              @endif
              <canvas id="datewise" width="800" height="{{ $height }}"></canvas>

            </div>
          </div>
          <div class="col-md-2">
            @can('Bookings list')
            <div class="info-box mb-3 bg-warning">
              <span class="info-box-icon"><i class="fa fa-address-book"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">@lang('fleet.bookings')</span>
                <span class="info-box-number">{{$bookings}}</span>
              </div>
            </div>
            @endcan
            @can('Transactions list')
            <div class="info-box mb-3 bg-success">
              <span class="info-box-icon"><i class="fa fa-money-bill"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">@lang('fleet.income')</span>
                <span class="info-box-number">{{ Hyvikk::get("currency")}}{{$income}}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <div class="info-box mb-3 bg-danger">
              <span class="info-box-icon"><i class="fa fa-credit-card"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">@lang('fleet.expense')</span>
                <span class="info-box-number">{{ Hyvikk::get("currency")}}{{$expense}}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            @endcan
            @can('Vendors list')
            <div class="info-box mb-3 bg-info">
              <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">@lang('fleet.vendors')</span>
                <span class="info-box-number">{{$vendors}}</span>
              </div>
            </div>
            @endcan
          </div>

        </div>

      </div>



    </div>

  </div>
</div>
<div class="row">

  <div class="col-md-12">
    <div class="card card-default">
      <div class="card-header">
        <h3 class="card-title">@lang("fleet.monthly_chart") {{date("F")}}</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="card card-info">
              <div class="card-header">
                <h5> @lang('fleet.income') - @lang('fleet.expense') </h5>
              </div>
              <div class="card-body">
                <canvas id="canvas" width="400" height="400"></canvas>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card card-info">
              <div class="card-header">
                <h5> @lang('fleet.vehicle') @lang('fleet.expenses') </h5>
              </div>
              <div class="card-body">
                <canvas id="canvas2" width="400" height="400"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="card card-default">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.yearly_chart')
          <div class="pull-right">
            {!! Form::select('year', $years,
            $year_select,['class'=>'form-control','style'=>'width:100px','id'=>'year'])!!}
          </div>
        </h3>
      </div>
      <div class="card-body">
        @php($useragent = $_SERVER['HTTP_USER_AGENT'])
        @if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge
        |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm(
        os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows
        ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a
        wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s
        )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1
        u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-|
        |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac(
        |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt
        |kwc\-|kyo(c|k)|le(no|xi)|lg(
        g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-|
        |o|v)|zz)|mt(50|p1|v
        )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v
        )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-|
        )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4)))
        @php($height="600")
        @else
        @php($height="300")
        @endif
        <div class="chart"><canvas id="yearly" width="800" height="{{ $height }}"></canvas> </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section("script2")
<script>
  window.chartColors = {
  red: 'rgb(255, 99, 132)',
  orange: 'rgb(255, 159, 64)',
  yellow: 'rgb(255, 205, 86)',
  green: 'rgb(75, 192, 192)',
  blue: 'rgb(54, 162, 235)',
  purple: 'rgb(153, 102, 255)',
  grey: 'rgb(201, 203, 207)',
  black: 'rgb(0,0,0)'
};



function random_color(i){
  var color1,color2,color3;
  var col_arr=[];
  for(x=0;x<=i;x++){

  var c1 = [176,255,84,220,134,66,238];
  var c2 = [254,61,147,114,51,26,137];
  var c3 = [27,111,153,93,157,216,187,44,243];
  color1 = c1[Math.floor(Math.random()*c1.length)];
  color2 = c2[Math.floor(Math.random()*c2.length)];
  color3 = c3[Math.floor(Math.random()*c3.length)];

  col_arr.push("rgba("+color1+","+color2+","+color3+",0.5)");
  }
  return col_arr;
}

        var chartData = {
            labels: ["@lang('fleet.income')", "@lang('fleet.expense')"],

            datasets: [{
                type: 'pie',
                label: '',
               backgroundColor: ['#21bc6c','#ff5462'],
                borderColor: window.chartColors.red,
                borderWidth: 1,
                data: [{{$income}},{{$expense}}]
            }]
        };

        var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July'];
        var config = {
            type: 'line',
            data: {
                labels: MONTHS,
                datasets: [{
                    label: "@lang('fleet.expense')",
                    backgroundColor: '#ff5462',
                    borderColor: '#ff5462',
                    data: [{{$yearly_expense}}],
                    fill: false,
                }, {
                    label: "@lang('fleet.income')",
                    fill: false,
                    backgroundColor: '#21bc6c',
                    borderColor: '#21bc6c',
                    data: [{{$yearly_income}}],
                }]
            },
            options: {
                responsive: true,
                title:{
                    display:false,
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: "@lang('fleet.month')"
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: "@lang('fleet.amount')"
                        }
                    }]
                }
            }
        };

        var datewise_config = {
            type: 'line',
            data: {
                labels: [
                      @foreach($dates as $k=>$v)
                        CanvasJS.formatDate( new Date("{{date('Y-m-d H:i:s',strtotime($k))}}"), "DD/MM/YY"),
                      @endforeach],
                datasets: [{
                    label: "@lang('fleet.expense')",
                    backgroundColor: '#ff5462',
                    borderColor: '#ff5462',
                    data: [{{$expenses1}}],
                    fill: false,
                }, {
                    label: "@lang('fleet.income')",
                    fill: false,
                    backgroundColor: '#21bc6c',
                    borderColor: '#21bc6c',
                    data: [{{$incomes}}],
                }]
            },
            options: {
                responsive: true,
                title:{
                    display:false,
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: "@lang('fleet.date')"
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: "@lang('fleet.amount')"
                        }
                    }]
                }
            }
        };

        window.onload = function() {
          var ctx = document.getElementById("yearly").getContext("2d");
            window.myLine = new Chart(ctx, config);
            var ctx = document.getElementById("canvas").getContext("2d");
            var datewise = document.getElementById("datewise").getContext("2d");
            window.myLine = new Chart(datewise, datewise_config);
            window.myMixedChart = new Chart(ctx, {
                type: 'pie',
                data: chartData,
                options: {
                  legend:{display:false},
                    responsive: true,

                    tooltips: {
                        mode: 'index',
                        intersect: false
                    }
                }
            });

            var ctx = document.getElementById("canvas2").getContext("2d");
            window.myMixedChart = new Chart(ctx, {
                type: 'pie',
                data: chartData3,
                options: {

                    responsive: true,
                    title: {
                        display: false,
                        text: "@lang('fleet.chart')"
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: true
                    }
                }
            });
        };



            var chartData3 = {
            labels: [@foreach($expenses as $exp) "{{$vehicle_name[$exp->vehicle_id]}}", @endforeach],
            datasets: [{
                type: 'pie',
                label: '',
                backgroundColor: random_color({{count($expenses)}}),
                borderColor: window.chartColors.black,
                borderWidth: 1,
                data: [@foreach($expenses as $exp) {{$exp->expense}}, @endforeach]
            }]
        };

</script>
@endsection

@section('script')

<script type="text/javascript">
  $("#year").on("change",function(){
    var year = this.value;
    // alert(status);
    window.location = "{{url('admin/')}}" + "?year=" + year; // redirect
  });
</script>
@endsection