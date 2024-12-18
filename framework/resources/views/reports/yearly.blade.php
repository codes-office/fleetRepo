@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item"><a href="#">@lang('menu.reports')</a></li>
<li class="breadcrumb-item active">@lang('fleet.yearlyReport')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.yearlyReport')
        </h3>
      </div>

      <div class="card-body">
        {!! Form::open(['route' => 'reports.yearly','method'=>'post','class'=>'form-inline']) !!}
        <div class="row">
          <div class="form-group" style="margin-right: 10px">
            {!! Form::label('year', __('fleet.year'), ['class' => 'form-label']) !!}
            <div class="form-group" style="margin-left:5px;">
            {!! Form::select('year', $years, $year_select,['class'=>'form-control']) !!}</div>
          </div>

          <div class="form-group" style="margin-right: 10px">
            {!! Form::label('vehicle', __('fleet.vehicles'), ['class' => 'form-label']) !!}
            <div class="form-group" style="margin-left:5px;">
            <select id="vehicle_id" name="vehicle_id" class="form-control vehicles" style="width: 250px">
              <option value="">@lang('fleet.selectVehicle')</option>
              @foreach($vehicles as $vehicle)
              <option value="{{ $vehicle->id }}" @if($vehicle->id == $vehicle_select) selected
              @endif>{{$vehicle->make_name}}-{{$vehicle->model_name}}-{{$vehicle->license_plate}}</option>
              @endforeach
            </select></div>
          </div>
          <button type="submit" class="btn btn-info" style="margin-right: 10px">@lang('fleet.generate_report')</button>
          <button type="submit" formaction="{{url('admin/print-yearly-report')}}" class="btn btn-danger"><i class="fa fa-print"></i> @lang('fleet.print')</button>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>

@if(isset($result))
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
          @lang('fleet.report')
        </h3>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="card card-warning">
              <div class="card-header">
                <h4 class="card-title">@lang('fleet.chart') - @lang('fleet.income')</h4>
              </div>

              <div class="card-body">
                <canvas id="canvas1" width="400" height="500"></canvas>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-hover myTable">
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
          </div>
          <div class="col-md-4">
            <div class="card card-warning">
              <div class="card-header">
                <h4 class="card-title">@lang('fleet.chart') - @lang('fleet.incomeByCategory')</h4>
              </div>
              <div class="card-body">
                <canvas id="canvas2" width="400" height="500"></canvas>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-hover myTable">
                @php($tot = 0)
                @foreach ($income_by_cat as $exp)
                @php($tot = $tot + $exp->amount)
                @endforeach
                <thead>
                  <tr>
                    <th scope="row">@lang('fleet.incomeByCategory')</th>
                    <td><strong>{{ Hyvikk::get("currency")}}{{$tot}}</strong></td>
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
          </div>

          <div class="col-md-4">
            <div class="card card-warning">
              <div class="card-header">
                <h4 class="card-title">@lang('fleet.chart') - @lang('fleet.expensesByCategory')</h4>
              </div>
              <div class="card-body">
                <canvas id="canvas3" width="400" height="500"></canvas>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-hover expTable">
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
        </div>
      </div>
    </div>
  </div>
</div>
@endif
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
      labels: ["@lang('fleet.income')", "@lang('fleet.expenses')"],
      datasets: [{
          type: 'pie',
          label: '',
          backgroundColor: [window.chartColors.green,window.chartColors.red],
          borderColor: window.chartColors.black,
          borderWidth: 1,
          data: [{{@$income_amt}},{{@$expense_amt}}]
      }]
  };

  var chartData2 = {
    labels: [@foreach($income_by_cat as $exp) "{{$income_cats[$exp->income_cat]}}", @endforeach],
    datasets: [{
        type: 'pie',
        label: '',
        backgroundColor: random_color({{count($income_by_cat)}}),
        borderColor: window.chartColors.black,
        borderWidth: 1,
        data: [@foreach($income_by_cat as $exp) {{$exp->amount}}, @endforeach]
    }]
  };

  var chartData3 = {
    labels: [@foreach($expense_by_cat as $exp) "@if($exp->type == "s") {{$service[$exp->expense_type]}}@else {{$expense_cats[$exp->expense_type]}}@endif", @endforeach],
    datasets: [{
        type: 'pie',
        label: '',
        backgroundColor: random_color({{count($expense_by_cat)}}),
        borderColor: window.chartColors.black,
        borderWidth: 1,
        data: [@foreach($expense_by_cat as $exp) {{$exp->expense}}, @endforeach]
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
               var ctx = document.getElementById("canvas3").getContext("2d");
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

      var ctx = document.getElementById("canvas2").getContext("2d");
      window.myMixedChart = new Chart(ctx, {
          type: 'pie',
          data: chartData2,
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
    $("#vehicle_id").select2();
    $('.myTable').DataTable({
      "paging":   false,
      "ordering": false,
      "searching": false,
      "info":     false,

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

  $('.expTable').DataTable({
    "ordering": false,
    "searching": false,
    "info":     false,
    "pageLength": 5,
    dom: 'Bfrtip',
    buttons: [{
         extend: 'collection',
            text:'@lang('fleet.Export')',
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