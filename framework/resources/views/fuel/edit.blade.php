@extends('layouts.app')

@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('fuel.index')}}">@lang('fleet.fuel')</a></li>
<li class="breadcrumb-item active"> @lang('fleet.edit_fuel')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-warning">
      <div class="card-header">
        <h3 class="card-title">
          @lang('fleet.edit_fuel')
        </h3>
      </div>

      <div class="card-body">
        @if (count($errors) > 0)
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        {!! Form::open(['route' => ['fuel.update',$data->id],'method'=>'PATCH','files'=>'true']) !!}
        {!! Form::hidden('user_id',Auth::user()->id)!!}
        {!! Form::hidden('vehicle_id',$vehicle_id)!!}
        {!! Form::hidden('id',$data->id)!!}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('vehicle_id',__('fleet.selectVehicle'), ['class' => 'form-label']) !!}
              <select id="vehicle_id" disabled="" name="vehicle_id" class="form-control xxhvk" required>
                <option selected value="{{$vehicle_id}}">{{$data->vehicle_data->make_name}} -
                  {{$data->vehicle_data->model_name}} - {{$data->vehicle_data['license_plate']}}</option>
              </select>
            </div>
            <div class="form-group">
              {!! Form::label('date',__('fleet.date'), ['class' => 'form-label']) !!}
              <div class='input-group'>
                <div class="input-group-prepend">
                  <span class="input-group-text"><span class="fa fa-calendar"></span>
                </div>
                {!! Form::text('date',$data->date,['class'=>'form-control','required']) !!}
              </div>
            </div>

            <div class="form-group">
              {!! Form::label('start_meter',__('fleet.start_meter'), ['class' => 'form-label']) !!}
              {!! Form::number('start_meter',$data->start_meter,['class'=>'form-control','required']) !!}
              <small>@lang('fleet.meter_reading')</small>
            </div>

            <div class="form-group">
              {!! Form::label('reference',__('fleet.reference'), ['class' => 'form-label']) !!}
              {!! Form::text('reference',$data->reference,['class'=>'form-control']) !!}
            </div>

            <div class="form-group">
              {!! Form::label('province',__('fleet.province'), ['class' => 'form-label']) !!}
              {!! Form::text('province',$data->province,['class'=>'form-control']) !!}
            </div>

            <div class="form-group">
              {!! Form::label('image',__('fleet.select_image'), ['class' => 'form-label']) !!}
              {!! Form::file('image',['class'=>'form-control']) !!}
              @if (!is_null($data->image) && file_exists('uploads/'.$data->image))
                <a href="{{ url('uploads/'.$data->image) }}" target="_blank">View</a>
              @endif
            </div>

            <div class="form-group">
              {!! Form::label('note',__('fleet.note'), ['class' => 'form-label']) !!}
              {!! Form::text('note',$data->note,['class'=>'form-control']) !!}
            </div>
            <div class="form-group row">
              <div class="col-md-6">
                <h4>@lang('fleet.complete_fill_up')</h4>
              </div>
              <div class="col-md-6">
                <label class="switch">
                  <input type="checkbox" name="complete" value="1" @if($data->complete == '1')checked @endif>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card card-solid">
              <div class="card-header">
                <h3 class="card-title">@lang('fleet.fuel_coming_from')</h3>
              </div>
              {{-- @dd($data) --}}
              <div class="card-body">
                <input type="radio" name="fuel_from" class="flat-red fuel_from" value="Fuel Tank" @if($data->fuel_from == "Fuel
                Tank") checked @endif>
                {!! Form::label('fuel_from', __('fleet.fuel_tank'), ['class' => 'form-label']) !!}
                <br>
                <input type="radio" name="fuel_from" class="flat-red fuel_from" value="N/D" @if($data->fuel_from == "N/D") checked
                @endif >
                {!! Form::label('fuel_from', __('fleet.nd'), ['class' => 'form-label']) !!}
                <br>
                <input type="radio" name="fuel_from" class="flat-red fuel_from" value="Vendor" @if($data->fuel_from == "Vendor")
                checked @endif>
                {!! Form::label('fuel_from', __('fleet.vendor'), ['class' => 'form-label']) !!}
                <select id="vendor_name" name="vendor_name" class="form-control" >
                  <option value="">-</option>
                  @foreach($vendors as $vendor)
                  <option value="{{$vendor->id}}" @if($data->vendor_name == $vendor->id) selected @endif>
                    {{$vendor->name}} </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="card card-solid">
              <div class="card-header">
                <h3 class="card-title">
                  @lang('fleet.fuel')
                </h3>
              </div>
              <div class="card-body">
                <div class="form-group">
                  {!! Form::label('qty',__('fleet.qty').' ('.Hyvikk::get('fuel_unit').')', ['class' => 'form-label'])
                  !!}
                  {!! Form::text('qty',$data->qty,['class'=>'form-control']) !!}
                </div>
                <div class="form-group">
                  {!! Form::label('cost_per_unit',__('fleet.cost_per_unit'), ['class' => 'form-label']) !!}
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">{{Hyvikk::get('currency')}}</span>
                    </div>
                    {!! Form::text('cost_per_unit',$data->cost_per_unit,['class'=>'form-control']) !!}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            {!! Form::submit(__('fleet.update'), ['class' => 'btn btn-warning']) !!}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
  $(document).ready(function() {
  var fuel_from = '{{ $data->fuel_from }}';

  if(fuel_from == "Vendor"){
    $('#vendor_name').prop('disabled', false);
  }else{
    $('#vendor_name').prop('disabled', true);
  }

  $("#vehicle_id").select2({placeholder: "@lang('fleet.selectVehicle')"});
  $("#vendor_name").select2({placeholder: "@lang('fleet.select_fuel_vendor')"});

  $('#date').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  });

  $("#date").on("dp.change", function (e) {
    var date=e.date.format("YYYY-MM-DD");
  });

  $('.fuel_from').on('change', function() {
    var select = $(this).val();
    if(this.value == "Vendor"){
        $('#vendor_name').prop('disabled', false);
    } else {
        $('#vendor_name').prop('disabled', true);
    }
  });

});
</script>
@endsection
@section('extra_css')
<style type="text/css">
  /* The switch - the box around the slider */
  .switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
  }

  /* Hide default HTML checkbox */
  .switch input {
    display: none;
  }

  /* The slider */
  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
  }

  input:checked+.slider {
    background-color: #2196F3;
  }

  input:focus+.slider {
    box-shadow: 0 0 1px #2196F3;
  }

  input:checked+.slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
  }

  /* Rounded sliders */
  .slider.round {
    border-radius: 34px;
  }

  .slider.round:before {
    border-radius: 50%;
  }
</style>
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">
@endsection