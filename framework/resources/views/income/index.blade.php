@extends('layouts.app')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')
@php($currency=Hyvikk::get('currency'))
@section('extra_css')
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">
<style type="text/css">
  .checkbox, #chk_all{
    width: 20px;
    height: 20px;
  }
</style>
@endsection
@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.manage_income')</li>
@endsection
@section('content')
@if (count($errors) > 0)
  <div class="alert alert-danger">
    <ul>
    @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
    </ul>
  </div>
@endif

@php($tax_percent=0)
            @if(Hyvikk::get('tax_charge') != "null")
              @php($taxes = json_decode(Hyvikk::get('tax_charge'), true))
              @foreach($taxes as $key => $val)
              @php($tax_percent += $val )
              @endforeach
            @endif

            <div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.addRecord')</h3>
      </div>
      <div class="card-body">
        <div class="row">
          {!! Form::open(['route' => 'income.store','method'=>'post','class'=>'form-inline','id'=>'income_form']) !!}

          <div class="col-md-3">
            <div class="form-group" style="margin-left:2px">
              {!! Form::label('vehicle_id', __('fleet.selectVehicle'), ['class' => 'col-xs-12 control-label']) !!}
              <div class="col-md-12">
                <select id="vehicle_id" name="vehicle_id" class="form-control vehicles" required style="width: 100%">
                  <option value="">@lang('fleet.selectVehicle')</option>
                  @foreach($vehicels as $vehicle)
                  <option value="{{ $vehicle->id }}" data-mileage="{{ $vehicle->mileage}}">{{$vehicle->make_name}}-{{$vehicle->model_name}}-{{$vehicle->license_plate}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-3" style=" margin-top: 5px;">
            <div class="form-group">
              {!! Form::label('income_type', __('fleet.incomeType'), ['class' => 'col-xs-12 control-label']) !!}
              <div class="col-md-12">
                <select id="income_type" name="income_type" class="form-control vehicles" required style="width: 100%">
                  <option value="">@lang('fleet.incomeType')</option>
                  @foreach($types as $type)
                  <option value="{{ $type->id }}">{{$type->name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('mileage', __('fleet.mileage'), ['class' => 'col-xs-12 control-label']) !!}
              <div class="col-md-12">
                <div class="input-group">
                  <div class="input-group-prepend">
                  <span class="input-group-text">{{Hyvikk::get('dis_format')}}</span></div>
                  <input required="required" name="mileage" type="number" id="mileage" class="form-control" min="0">
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('date', __('fleet.date'), ['class' => 'col-xs-12 control-label']) !!}
              <div class="col-md-12">
                <div class="input-group">
                  <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                  <input required="required" name="date" type="text" value="{{ date('Y-m-d')}}"  id="date" class="form-control">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3" style="margin-top: 5px;">
            <div class="form-group">
              {!! Form::label('revenue', __('fleet.amount'), ['class' => 'col-xs-5 control-label']) !!}
              <div class="col-xs-6">
            <div class="input-group">
              <div class="input-group-prepend">
              <span class="input-group-text">{{$currency}}</span></div>
              <input required="required" name="revenue" type="number" step="0.01" id="revenue" class="form-control">
            </div>
          </div>
        </div>
          </div>
          <div class="col-md-3" style="margin-top: 5px;">
            @php($tax_percent=0)
            @if(Hyvikk::get('tax_charge') != "null")
              @php($taxes = json_decode(Hyvikk::get('tax_charge'), true))
              @foreach($taxes as $key => $val)
              @php($tax_percent += $val )
              @endforeach
            @endif
            <div class="form-group">
              {!! Form::label('tax_percent', __('fleet.total_tax'). " (%)", ['class' => 'col-xs-5 control-label']) !!}
              <div class="col-xs-6">
                <div class="input-group">
                  <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-percent"></i></span></div>
                  <input name="tax_percent" type="text" id="tax_percent" class="form-control" readonly value="{{ $tax_percent }}">
                </div>
              </div>
            </div>

          </div>
          <div class="col-md-3" style=" margin-top: 5px;">
            <div class="form-group">
              {!! Form::label('tax_charge_rs', __('fleet.total')." ". __('fleet.tax_charge'), ['class' => 'col-xs-5 control-label']) !!}
              <div class="col-xs-6">
                <div class="input-group">
                  <div class="input-group-prepend">
                  <span class="input-group-text">{{$currency}}</span></div>
                  <input required="required" name="tax_charge_rs" type="text" id="tax_charge_rs" class="form-control" readonly value="0">
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3" style=" margin-top: 5px;">
            <div class="form-group">
              {!! Form::label('tax_total', __('fleet.total')." ". __('fleet.amount'), ['class' => 'col-xs-5 control-label']) !!}
              <div class="col-xs-6">
                <div class="input-group">
                  <div class="input-group-prepend">
                  <span class="input-group-text">{{$currency}}</span></div>
                  <input required="required" name="tax_total" type="text" id="tax_total" class="form-control" readonly>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6" style=" margin-top: 5px;">
            @can('Transactions add')<button type="submit" class="btn btn-success">@lang('fleet.add')</button>@endcan
          </div>
          {!!Form::close()!!}
        </div>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <div class="panel-heading">
          <div class="row">
            <div class="col-md-4">
              <h3 class="card-title">
                @lang('fleet.todayIncome'): <strong><span id="total_today"> {{$currency." ". $total}} </span> </strong>
              </h3>
            </div>

            <div class="col-md-8 pull-right" style="display: flex; justify-content: end;">
              {!! Form::open(['url'=>'admin/income_records','class'=>'form-inline']) !!}
              <div class="form-group">
                {!! Form::label('date1',__('fleet.From')) !!}
                <div class="input-group date ml-3 mr-3">
                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                  {!! Form::text('date1', $date1,['class' => 'form-control','placeholder'=>__('fleet.start_date'),'required']) !!}
                </div>
              </div>
              <div class="form-group" style="margin-right: 10px">
                {!! Form::label('date2',__('fleet.To')) !!}
                <div class="input-group date ml-3">
                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                  {!! Form::text('date2', $date2,['class' => 'form-control','placeholder'=>__('fleet.end_date'),'required']) !!}
                </div>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-warning">
                  <i class="fa fa-search"></i>
                </button>
              </div>
              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
      <div class="card-body table-responsive" id="income">
        <table class="table" id="data_table">
          <thead class="thead-inverse">
            <tr>
              <th>
                @if($today->count() > 0)
                  <input type="checkbox" id="chk_all">
                @endif
              </th>
              <th>@lang('fleet.make')</th>
              <th>@lang('fleet.model')</th>
              <th>@lang('fleet.licensePlate')</th>
              <th>@lang('fleet.incomeType')</th>
              <th>@lang('fleet.date')</th>
              <th>@lang('fleet.amount')</th>
              <th>@lang('fleet.mileage') ({{Hyvikk::get('dis_format')}})</th>
              <th>@lang('fleet.delete')</th>
            </tr>
          </thead>
          <tbody>
            @foreach($today as $row)
            <tr>
              <td>
                <input type="checkbox" name="ids[]" value="{{ $row->id }}" class="checkbox" id="chk{{ $row->id }}" onclick='checkcheckbox();'>
              </td>
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
              <td>{{$currency}} {{$row->amount}}</td>
              <td>{{$row->mileage}}</td>
              <td>
              {!! Form::open(['url' => 'admin/income/'.$row->id,'method'=>'DELETE','class'=>'form-horizontal','id'=>'form_'.$row->id]) !!}
              {!! Form::hidden("id",$row->id) !!}
              @can('Transactions delete')<button type="button" class="btn btn-danger delete" data-id="{{$row->id}}" title="@lang('fleet.delete')"><span class="fa fa-times" aria-hidden="true"></span></button>@endcan
              {!! Form::close() !!}
              </td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th>
                @if($today->count() > 0)
                @can('Transactions delete')<button class="btn btn-danger" id="bulk_delete" data-toggle="modal" data-target="#bulkModal" disabled title="@lang('fleet.delete')" ><i class="fa fa-trash"></i></button>@endcan
                @endif
              </th>
              <th>@lang('fleet.make')</th>
              <th>@lang('fleet.model')</th>
              <th>@lang('fleet.licensePlate')</th>
              <th>@lang('fleet.incomeType')</th>
              <th>@lang('fleet.date')</th>
              <th>@lang('fleet.amount')</th>
              <th>@lang('fleet.mileage') ({{Hyvikk::get('dis_format')}})</th>
              <th>@lang('fleet.delete')</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="bulkModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.delete')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        {!! Form::open(['url'=>'admin/delete-income','method'=>'POST','id'=>'form_delete']) !!}
        <div id="bulk_hidden"></div>
        <p>@lang('fleet.confirm_bulk_delete')</p>
      </div>
      <div class="modal-footer">
        <button id="bulk_action" class="btn btn-danger" type="submit" data-submit="">@lang('fleet.delete')</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
        {!! Form::close() !!}
    </div>
  </div>
</div>
<!-- Modal -->

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">@lang('fleet.delete')</h4>
      </div>
      <div class="modal-body">
        <p>@lang('fleet.confirm_delete')</p>
      </div>
      <div class="modal-footer">
        <button id="del_btn" class="btn btn-danger" type="button" data-submit="">@lang('fleet.delete')</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
@endsection


@section("script")
<script type="text/javascript">
  $('#revenue').on('change',function(){
    var amount = $('#revenue').val();
    var tax_percent = "{{ $tax_percent }}";
    var tax_charges = (Number('{{ $tax_percent }}') * Number(amount))/100;
  $('#tax_charge_rs').val(tax_charges);
  $('#tax_total').val(Number(amount) + Number(tax_charges));
    // console.log(tax_percent);
  });
$(document).ready(function() {
  $('#vehicle_id').select2({placeholder: "@lang('fleet.selectVehicle')"});
  $('#income_type').select2({placeholder: "@lang('fleet.incomeType')"});

  $("#vehicle_id").on("change",function(){
    $("#mileage").val($(this).find(':selected').data("mileage"));
    $("#mileage").attr("min",$(this).find(':selected').data("mileage"));
  });

  $('#date').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  });

  $('#date1').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  });
var date1=$('#date1').val();
  $('#date2').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    minDate:date1
  }).on('show', function() {
    var pickupdate = $( "#date1" ).datepicker('getDate');
    if (pickupdate) {
      $( "#date2" ).datepicker('setStartDate', pickupdate);
    };
  });
  $("#del_btn").on("click",function(){
    var id=$(this).data("submit");
    $("#form_"+id).submit();
  });

  $('#myModal').on('show.bs.modal', function(e) {
    var id = e.relatedTarget.dataset.id;
    $("#del_btn").attr("data-submit",id);
  });

$(document).on("click",".delete",function(e){
  var hvk=confirm("Are you sure?");
  if(hvk==true){
    var id=$(this).data("id");
    var action="{{ url('admin/income')}}"+"/" +id;

      $.ajax({
        type: "POST",
        url: action,
        data: "_method=DELETE&_token="+window.Laravel.csrfToken+"&id="+id,
        success: function(data){
          // alert(data);
          $("#income").empty();
          $("#income").html(data);

          new PNotify({
              title: 'Deleted!',
              text: '@lang("fleet.deleted")',
              type: 'warning'
          })
        }
      ,
      dataType: "HTML"
    });
  }
});

});

  $('input[type="checkbox"]').on('click',function(){
    $('#bulk_delete').removeAttr('disabled');
  });

  $('#bulk_delete').on('click',function(){
    // console.log($( "input[name='ids[]']:checked" ).length);
    if($( "input[name='ids[]']:checked" ).length == 0){
      $('#bulk_delete').prop('type','button');
        new PNotify({
            title: 'Failed!',
            text: "@lang('fleet.delete_error')",
            type: 'error'
          });
        $('#bulk_delete').attr('disabled',true);
    }
    if($("input[name='ids[]']:checked").length > 0){
      // var favorite = [];
      $.each($("input[name='ids[]']:checked"), function(){
          // favorite.push($(this).val());
          $("#bulk_hidden").append('<input type=hidden name=ids[] value='+$(this).val()+'>');
      });
      // console.log(favorite);
    }
  });


  $('#chk_all').on('click',function(){
    if(this.checked){
      $('.checkbox').each(function(){
        $('.checkbox').prop("checked",true);
      });
    }else{
      $('.checkbox').each(function(){
        $('.checkbox').prop("checked",false);
      });
    }
  });

  // Checkbox checked
  function checkcheckbox(){
    // Total checkboxes
    var length = $('.checkbox').length;
    // Total checked checkboxes
    var totalchecked = 0;
    $('.checkbox').each(function(){
        if($(this).is(':checked')){
            totalchecked+=1;
        }
    });
    // console.log(length+" "+totalchecked);
    // Checked unchecked checkbox
    if(totalchecked == length){
        $("#chk_all").prop('checked', true);
    }else{
        $('#chk_all').prop('checked', false);
    }
  }
</script>
@endsection