@extends('layouts.app')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

@section("breadcrumb")
<li class="breadcrumb-item"><a href="#">@lang('menu.reports')</a></li>
<li class="breadcrumb-item active">@lang('fleet.user_report')</li>
@endsection
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.user_report')
        </h3>
      </div>

      <div class="card-body">
        {!! Form::open(['route' => 'reports.users','method'=>'post','class'=>'form-inline']) !!}
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
          <div class="form-group" style="margin-right: 10px">
            {!! Form::label('user', __('fleet.users'), ['class' => 'form-label']) !!}
            <div class="form-group" style="margin-left:5px;">
            <select id="user_id" name="user_id" class="form-control" required>
              <option value="">@lang('fleet.selectUser')</option>
              @foreach($users as $user)
              <option value="{{ $user->id }}" @if($user['id']==$user_id) selected @endif>{{$user->name}}</option>
              @endforeach
            </select></div>
          </div>
          <button type="submit" class="btn btn-info" style="margin-right: 10px">@lang('fleet.generate_report')</button>
          <button type="submit" formaction="{{url('admin/print-users-report')}}" class="btn btn-danger"><i class="fa fa-print"></i> @lang('fleet.print')</button>
          {!! Form::close() !!}
        </div>
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

      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped table-hover"  id="myTable">
          <thead>
            <tr>
              <th>@lang('fleet.book_by')</th>
              <th>@lang('fleet.pickup_addr')</th>
              <th>@lang('fleet.dropoff_addr')</th>
              <th>@lang('fleet.pickup')</th>
              <th>@lang('fleet.dropoff')</th>
              <th>@lang('fleet.journey_status')</th>
              <th>@lang('fleet.amount')</th>
            </tr>
          </thead>

          <tbody>
            @foreach($data as $row)
            <tr>
              <td>{{$row->user->name}}</td>
              <td style="width:10% !important">{!! str_replace(",", ",<br>", $row->pickup_addr) !!}</td>
              <td style="width:10% !important">{!! str_replace(",", ",<br>", $row->dest_addr) !!}</td>
              <td>
                @if($row->pickup != null)
                {{date($date_format_setting.' g:i A',strtotime($row->pickup))}}
                @endif
              </td>
              <td>
                @if($row->dropoff != null)
                {{date($date_format_setting.' g:i A',strtotime($row->dropoff))}}
                @endif
              </td>
              <td>
                @if($row->status == 1)
                  <span class="text-success">
                  @lang('fleet.completed')
                  </span>
                @else
                  <span class="text-warning">
                  @lang('fleet.not_completed')
                  </span>
                @endif
              </td>
              <td>
                @if($row->receipt == 1)
                {{Hyvikk::get('currency')}} {{($row->tax_total) ? $row->tax_total : $row->total}}
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th>@lang('fleet.book_by')</th>
              <th>@lang('fleet.pickup_addr')</th>
              <th>@lang('fleet.dropoff_addr')</th>
              <th>@lang('fleet.pickup')</th>
              <th>@lang('fleet.dropoff')</th>
              <th>@lang('fleet.journey_status')</th>
              <th>@lang('fleet.amount')</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@section("script")

<script type="text/javascript">
	$(document).ready(function() {
		$("#user_id").select2();
	});
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#myTable tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input type="text" placeholder="'+title+'" />' );
    });
    var myTable = $('#myTable').DataTable({
      dom: 'Bfrtip',
      buttons: [{
           extend: 'collection',
              text: 'Export',
              buttons: [
                  'copy',
                  'excel',
                  'csv',
                  'pdf',
              ]}
      ],
      "language": {
               "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
            },
      "initComplete": function() {
              myTable.columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change', function () {
                    that.search(this.value).draw();
                });
              });
            }
    });
	});
</script>
@endsection