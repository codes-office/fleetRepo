@extends('layouts.app')
@section('extra_css')
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="#">@lang('menu.reports')</a></li>
<li class="breadcrumb-item active">@lang('fleet.vendorReport')</li>
@endsection
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
        @lang('fleet.vendorReport') &nbsp; <a href="{{url('admin/print-vendor-report')}}" class="btn btn-danger"><i class="fa fa-print"></i> @lang('fleet.print')</a>
        </h3>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-md-4"></div>
          <div class="col-md-8">
            {!! Form::open(['route' => 'reports.vendors','method'=>'post','class'=>'form-inline']) !!}

            <div class="form-group">
              {!! Form::label('date1',__('fleet.From')) !!}
              <div class="input-group ml-2">
                <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                {!! Form::text('date1', $date1,['class' => 'form-control','placeholder'=>__('fleet.start_date'),'required']) !!}
              </div>
            </div>
            <div class="form-group ml-2" style="margin-right: 5px">
              {!! Form::label('date2',__('fleet.To')) !!}
              <div class="input-group ml-2">
                <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                {!! Form::text('date2', $date2,['class' => 'form-control','placeholder'=>__('fleet.end_date'),'required']) !!}
              </div>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-info">
              <i class="fa fa-search"></i>
              </button>
            </div>
            {!! Form::close() !!}
          </div>
        </div>
        <div class="row" style="margin-top: 25px;">
          <div class="col-md-2"></div>
          <div class="col-md-8">
            <div class="card card-warning">
              <div class="card-body">
                <canvas id="canvas1" width="400" height="300"></canvas>
                <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover" id="myTable">
                    <thead>
                      <tr>
                        <th scope="row">@lang('fleet.vendor'):</th>
                        <td><strong>@lang('fleet.total')</strong></td>
                      </tr>
                    </thead>
                    <tbody>
                      <hr>
                      @foreach($details as $d)
                      <tr>
                        <th scope="row">{{$vendors[$d->vendor_id]}}</th>
                        <td>{{ Hyvikk::get("currency")}} {{$d->total}}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-2"></div>
        </div>
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
  labels: [@foreach($details as $d) "{{$vendors[$d->vendor_id]}}", @endforeach],
  datasets: [{
      type: 'pie',
      label: "",
      backgroundColor: random_color({{count($details)}}),
      borderColor: window.chartColors.black,
      borderWidth: 1,
      data: [@foreach($details as $d) {{$d->total}}, @endforeach]
  }]
};


window.onload = function() {
  var ctx = document.getElementById("canvas1").getContext("2d");
  window.myMixedChart = new Chart(ctx, {
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

@section('script')
<script src="{{ asset('assets/js/moment.js') }}"></script>
<!-- bootstrap datepicker -->
<script src="{{asset('assets/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#date1').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
    });
    var from_date=$('#date1').val();
    $('#date2').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        minDate:from_date
    }).on('show', function() {
    var pickupdate = $( "#date1" ).datepicker('getDate');
    if (pickupdate) {
      $( "#date2" ).datepicker('setStartDate', pickupdate);
    }
  });
   
  });
</script>

<script type="text/javascript" src="{{ asset('assets/js/cdn/jszip.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/cdn/pdfmake.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/cdn/vfs_fonts.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/cdn/buttons.html5.min.js')}}"></script>
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