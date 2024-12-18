@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item"><a href="#">@lang('menu.reports')</a></li>
<li class="breadcrumb-item active">@lang('fleet.driverReport')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.driverReport')
        </h3>
      </div>

      <div class="card-body">
        {!! Form::open(['route' => 'reports.drivers','method'=>'post','class'=>'form-inline']) !!}
        <div class="row">
          <div class="form-group" style="margin-right: 10px">
            {!! Form::label('year', __('fleet.year1'), ['class' => 'form-label']) !!}
            <div class="form-group" style="margin-left:5px;">
            {!! Form::select('year', $years, $year_select,['class'=>'form-control']) !!}</div>
          </div>
          <div class="form-group" style="margin-right: 10px">
            {!! Form::label('month', __('fleet.month'), ['class' => 'form-label']) !!}
            <div class="form-group" style="margin-left:5px;">
            {!! Form::selectMonth('month',$month_select,['class'=>'form-control']) !!}</div>
          </div>

          <button type="submit" class="btn btn-info" style="margin-right: 10px">@lang('fleet.generate_report')</button>
          <button type="submit" formaction="{{url('admin/print-driver-report')}}" class="btn btn-danger"><i class="fa fa-print"></i> @lang('fleet.print')</button>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.monthlyReport')
        </h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <canvas id="canvas1" width="400" height="300"></canvas>
          </div>
          <div class="col-md-6 table-responsive">
            <table class="table table-bordered table-striped table-hover" id="myTable">
              <thead>
                <tr>
                  <th scope="row">@lang('fleet.driver'):</th>
                  <td><strong>@lang('fleet.total')</strong></td>
                </tr>
              </thead>
              <tbody>
              <hr>
              @foreach($drivers_by_month as $key=>$val)
                <tr>
                  <th scope="row">{{$key}}</th>
                  <td>{{ Hyvikk::get("currency")}} {{$val}}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.yearlyReport')
        </h3>
      </div>
      <div class="card-body">
      @php($useragent = $_SERVER['HTTP_USER_AGENT'])
          @if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4)))
          @php($height="600")
          @else
          @php($height="300")
          @endif
        <canvas id="yearly" width="800" height="{{ $height }}"></canvas>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script2')
<script src="{{ asset('assets/js/cdn/Chart.bundle.min.js')}}"></script>

<script type="text/javascript">

var config = {
    type: 'line',
    data: {
        labels: [@foreach($drivers_by_year as $key=>$val) "{{$key}}", @endforeach],
        datasets: [{
            label: "@lang('fleet.drivers')",
            backgroundColor: '#ff5462',
            borderColor: '#ff5462',
            data: [@foreach($drivers_by_year as $key=>$val) {{$val}}, @endforeach],
            fill: false,
        }]
    },
    options: {
        legend: {
              display: false,
        },
        responsive: true,
        title:{
            display:true,
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
                    labelString: "@lang('fleet.drivers')"
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
    },
};

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
    labels: [@foreach($drivers_by_month as $key=>$val) @if($val!=0) "{{$key}}", @endif @endforeach],
    datasets: [{
        type: 'pie',
        label: "",
        backgroundColor: random_color({{count($drivers_by_month)}}),
        borderColor: window.chartColors.black,
        borderWidth: 1,
        data: [@foreach($drivers_by_month as $key=>$val) @if($val!=0) {{$val}}, @endif @endforeach]
    }]
};

window.onload = function() {
  var ctx = document.getElementById("yearly").getContext("2d");
    window.myLine = new Chart(ctx, config);

    var ctx1 = document.getElementById("canvas1").getContext("2d");
    window.myMixedChart = new Chart(ctx1, {
        type: 'pie',
        data: chartData,
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
</script>
@endsection

@section("script")
<script type="text/javascript">
  $(document).ready(function() {
    $('#myTable').DataTable({
      dom: 'Bfrtip',
      buttons: [{
           extend: 'collection',
              text: '@lang('fleet.Export')',
              buttons: [
                  'copy',
                  'excel',
                  'csv',
                  'pdf',
              ]}
      ],
      "language": {
               "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
            }
    });
  });
</script>
@endsection