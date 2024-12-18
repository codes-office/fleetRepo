@extends('layouts.app')
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

  .custom .nav-link.active {

    background-color: #f4bc4b !important;
    color: inherit;
  }

  /* .select2-selection:not(.select2-selection--multiple) {
    height: 38px !important;
  }

  span.select2-selection.select2-selection--multiple {
    width: 100%;
  }

  input.select2-search__field {
    width: auto !important;
  }

  span.select2.select2-container {
    width: 100% !important;
  } */
</style>
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('vehicles.index')}}">@lang('fleet.vehicles')</a></li>
<li class="breadcrumb-item active">@lang('fleet.edit_vehicle')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    @if (count($errors) > 0)
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <div class="card card-warning">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.edit_vehicle')</h3>
      </div>

      <div class="card-body">
        <div class="nav-tabs-custom">
          <ul class="nav nav-pills custom">
            <li class="nav-item"><a class="nav-link active" href="#info-tab" data-toggle="tab">
                @lang('fleet.general_info') <i class="fa"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="#insurance" data-toggle="tab"> @lang('fleet.insurance') <i
                  class="fa"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="#acq-tab" data-toggle="tab"> @lang('fleet.purchase_info') <i
                  class="fa"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="#driver" data-toggle="tab"> @lang('fleet.assign_driver') <i
                  class="fa"></i></a></li>
          </ul>
        </div>
        <div class="tab-content">
          <div class="tab-pane active" id="info-tab">
            {!! Form::open(['route' =>['vehicles.update',$vehicle->id],'files'=>true,
            'method'=>'PATCH','class'=>'form-horizontal','id'=>'accountForm1']) !!}
            {!! Form::hidden('user_id',Auth::user()->id) !!}
            {!! Form::hidden('id',$vehicle->id) !!}
            <div class="row card-body">
              <div class="col-md-6">
                <div class="form-group">
                  {{-- @dd($makes) --}}
                  {!! Form::label('make_name', __('fleet.SelectVehicleMake'), ['class' => 'col-xs-5 control-label']) !!}
                  <a data-toggle="modal" data-target="#myModal"><i class="fa fa-info-circle fa-lg" aria-hidden="true"  style="color: #8639dd"></i></a>

                  <div class="col-xs-6">
                    <select name="make_name" class="form-control" required id="make_name">
                      <option></option>
                      @foreach($makes as $make)
                      <option value="{{$make}}" @if($make == $vehicle->make_name) selected @endif>{{$make}}
                      </option>
                      @endforeach
                    </select>
                  </div>
                 
                </div>
                <div class="col-md-6">
                </div>
                
                <div class="form-group">
                  {!! Form::label('model_name', __('fleet.SelectVehicleModel'), ['class' => 'col-xs-5 control-label']) !!}
                  <a data-toggle="modal" data-target="#myModal2"><i class="fa fa-info-circle fa-lg" aria-hidden="true"  style="color: #8639dd"></i></a>

                  <div class="col-xs-6">
                    <select name="model_name" class="form-control" required id="model_name">
                      @foreach($models as $model)
                      <option value="{{ $model }}" @if($model == $vehicle->model_name) selected @endif>{{
                        $model }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('type', __('fleet.type'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    <select name="type_id" class="form-control" required id="type_id">
                      <option></option>
                      @foreach($types as $type)
                      <option value="{{$type->id}}" @if($vehicle->type_id == $type->id) selected
                        @endif>{{$type->displayname}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                @if(Hyvikk::get('traccar_enable')==1)
                <div class="form-group">
                  {!! Form::label('traccar_device_id', __('fleet.traccar_device_id'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::text('traccar_device_id', $vehicle->getMeta('traccar_device_id'),['class' => 'form-control',]) !!}
                  </div>
                </div>
                {{-- <div class="form-group">
                  {!! Form::label('traccar_vehicle_id', __('fleet.traccar_vehicle_id'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::text('traccar_vehicle_id', $vehicle->getMeta('traccar_vehicle_id'),['class' => 'form-control',]) !!}
                  </div>
                </div> --}}
                @endif

                <div class="form-group">
                  {!! Form::label('year', __('fleet.year'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::number('year', $vehicle->year,['class' => 'form-control','required']) !!}
                  </div>
                </div>

                <div class="form-group">
                  @if(Hyvikk::get('dis_format') == "km")
                  @if(Hyvikk::get('fuel_unit') == "gallon") {!! Form::label('average',
                  __('fleet.average')."(".__('fleet.kmpg').")", ['class' => 'col-xs-5 control-label']) !!} @else {!!
                  Form::label('average', __('fleet.average')."(".__('fleet.kmpl').")", ['class' => 'col-xs-5
                  control-label']) !!} @endif
                  @else
                  @if(Hyvikk::get('fuel_unit') == "gallon"){!! Form::label('average',
                  __('fleet.average')."(".__('fleet.mpg').")", ['class' => 'col-xs-5 control-label']) !!} @else {!!
                  Form::label('average', __('fleet.average')."(".__('fleet.mpl').")", ['class' => 'col-xs-5
                  control-label']) !!} @endif
                  @endif
                  <div class="col-xs-6">
                    {!! Form::number('average', $vehicle->average,['class' => 'form-control','required','step'=>'any'])
                    !!}
                  </div>
                </div>

                <div class="form-group">
                  @if(Hyvikk::get('dis_format') == "km")
                  {!! Form::label('int_mileage', __('fleet.intMileage')."(".__('fleet.km').")", ['class' => 'col-xs-5
                  control-label']) !!}
                  @else
                  {!! Form::label('int_mileage', __('fleet.intMileage')."(".__('fleet.miles').")", ['class' => 'col-xs-5
                  control-label']) !!}
                  @endif
                  <div class="col-xs-6">
                    {!! Form::text('int_mileage', $vehicle->int_mileage,['class' => 'form-control','required']) !!}
                  </div>
                </div>
                <div class="form-group">
                  {!! Form::label('vehicle_image', __('fleet.vehicleImage'), ['class' => 'col-xs-5 control-label']) !!}
                  @if($vehicle->vehicle_image != null)
                  <a href="{{ asset('uploads/'.$vehicle->vehicle_image) }}" target="_blank"
                    class="col-xs-3 control-label">View</a>
                  @endif
                  <br>
                  {!! Form::file('vehicle_image',null,['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                  {!! Form::label('reg_exp_date',__('fleet.reg_exp_date'), ['class' => 'col-xs-5 control-label
                  required']) !!}
                  <div class="col-xs-6">
                    <div class="input-group date">
                      <div class="input-group-prepend"><span class="input-group-text"><i
                            class="fa fa-calendar"></i></span></div>
                      {!! Form::text('reg_exp_date', $vehicle->reg_exp_date,['class' => 'form-control','required']) !!}
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      {!! Form::label('in_service', __('fleet.is_active'), ['class' => 'col-xs-5 control-label']) !!}
                    </div>
                    <div class="col-ms-6" style="margin-left: -140px">
                      <label class="switch">
                        <input type="checkbox" name="in_service" value="1" @if($vehicle->in_service == '1') checked
                        @endif>
                        <span class="slider round"></span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('color_name', __('fleet.SelectVehicleColor'), ['class' => 'col-xs-5 control-label']) !!}
                  <a data-toggle="modal" data-target="#myModal3"><i class="fa fa-info-circle fa-lg" aria-hidden="true"  style="color: #8639dd"></i></a>

                  <div class="col-xs-6">
                    <select name="color_name" class="form-control" required id="color_name">
                      <option></option>
                      @foreach($colors as $color)
                      <option value="{{$color}}" @if($color == $vehicle->color_name)selected
                        @endif>{{$color}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('engine_type', __('fleet.engine'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!!
                    Form::select('engine_type',["Petrol"=>"Petrol","Diesel"=>"Diesel"],$vehicle->engine_type,['class' =>
                    'form-control','required']) !!}
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('horse_power', __('fleet.horsePower'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::text('horse_power', $vehicle->horse_power,['class' => 'form-control','required']) !!}
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('vin', __('fleet.vin'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::text('vin', $vehicle->vin,['class' => 'form-control','required']) !!}
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('license_plate', __('fleet.licensePlate'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::text('license_plate', $vehicle->license_plate,['class' => 'form-control','required']) !!}
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('lic_exp_date',__('fleet.lic_exp_date'), ['class' => 'col-xs-5 control-label
                  required']) !!}
                  <div class="col-xs-6">
                    <div class="input-group date">
                      <div class="input-group-prepend"><span class="input-group-text"><i
                            class="fa fa-calendar"></i></span></div>
                      {!! Form::text('lic_exp_date', $vehicle->lic_exp_date,['class' => 'form-control','required']) !!}
                    </div>
                  </div> 
              </div>
               

                <div class="form-group">
                  {!! Form::label('group_id',__('fleet.selectGroup'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    <select id="group_id" name="group_id" class="form-control">
                      <option value="">@lang('fleet.vehicleGroup')</option>
                      @foreach($groups as $group)
                      <option value="{{$group->id}}" @if($group->id == $vehicle->group_id) selected
                        @endif>{{$group->name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                
                <hr>
                <div class="form-group">
                  {!! Form::label('udf1',__('fleet.add_udf'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="row">
                    <div class="col-md-8">
                      {!! Form::text('udf1', null,['class' => 'form-control']) !!}
                    </div>
                    <div class="col-md-4">
                      <button type="button" class="btn btn-info add_udf"> @lang('fleet.add')</button>
                    </div>
                  </div>
                </div>
               
                <div class="blank"></div>
                @if($udfs != null)
                @foreach($udfs as $key => $value)
                <div class="row">
                  <div class="col-md-8">
                    <div class="form-group"> <label class="form-label text-uppercase">{{$key}}</label> <input
                        type="text" name="udf[{{$key}}]" class="form-control" required value="{{$value}}"></div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group" style="margin-top: 30px"><button class="btn btn-danger" type="button"
                        onclick="this.parentElement.parentElement.parentElement.remove();">Remove</button> </div>
                  </div>
                </div>
                @endforeach
                @endif
              </div>
            </div>
            
            <div style=" margin-bottom: 20px;">
              <div class="form-group" style="margin-top: 15px;">
                <div class="col-xs-6 col-xs-offset-3">
                  {!! Form::submit(__('fleet.submit'), ['class' => 'btn btn-warning']) !!}
                </div>
              </div>
            </div>
            {!! Form::close() !!}
          </div>

          <div class="tab-pane " id="insurance">
            {!! Form::open(['url' => 'admin/store_insurance','files'=>true,
            'method'=>'post','class'=>'form-horizontal','id'=>'accountForm']) !!}
            {!! Form::hidden('user_id',Auth::user()->id) !!}
            {!! Form::hidden('vehicle_id',$vehicle->id) !!}
            <div class="row card-body">
              <div class="col-md-8">
                <div class="form-group">
                  {!! Form::label('insurance_number', __('fleet.insuranceNumber'), ['class' => 'control-label']) !!}
                  {!! Form::text('insurance_number', $vehicle->getMeta('ins_number'),['class' =>
                  'form-control','required']) !!}
                </div>
                <div class="form-group">
                  <label for="documents" class="control-label">@lang('fleet.inc_doc')
                  </label>
                  @if($vehicle->getMeta('documents') != null)
                  <a href="{{ asset('uploads/'.$vehicle->getMeta('documents')) }}" target="_blank">View</a>
                  @endif
                  {!! Form::file('documents',null,['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                  {!! Form::label('exp_date', __('fleet.inc_expirationDate'), ['class' => 'control-label required']) !!}
                  <div class="input-group date">
                    <div class="input-group-prepend"><span class="input-group-text"><i
                          class="fa fa-calendar"></i></span></div>
                    {!! Form::text('exp_date', $vehicle->getMeta('ins_exp_date'),['class' => 'form-control','required'])
                    !!}
                  </div>
                </div>
              </div>
            </div>
            <div style=" margin-bottom: 20px;">
              <div class="form-group" style="margin-top: 15px;">
                {!! Form::submit(__('fleet.submit'), ['class' => 'btn btn-warning']) !!}
              </div>
            </div>
            {!! Form::close() !!}
          </div>

          <div class="tab-pane " id="acq-tab">
            <div class="row card-body">
              <div class="col-md-12">
                <div class="card card-success">
                  <div class="card-header">
                    <h3 class="card-title">@lang('fleet.acquisition') @lang('fleet.add')</h3>
                  </div>

                  <div class="card-body">
                    {!! Form::open(['route' =>
                    'acquisition.store','method'=>'post','class'=>'form-inline','id'=>'add_form']) !!}
                    {!! Form::hidden('user_id',Auth::user()->id) !!}
                    {!! Form::hidden('vehicle_id',$vehicle->id) !!}
                    <div class="form-group" style="margin-right: 10px;">
                      {!! Form::label('exp_name', __('fleet.expenseType'), ['class' => 'form-label']) !!}
                      {!! Form::text('exp_name', null,['class'=>'form-control','required','id'=>'exp_name']) !!}
                    </div>
                    <div class="form-group"></div>
                    <div class="form-group" style="margin-right: 10px;">
                      {!! Form::label('exp_amount', __('fleet.expenseAmount'), ['class' => 'form-label']) !!}
                      <div class="input-group" style="margin-right: 10px;">
                        <div class="input-group-prepend">
                          <span class="input-group-text">{{Hyvikk::get('currency')}}</span>
                        </div>
                        {!! Form::number('exp_amount',null,['class'=>'form-control','required','step'=>'any','id'=>'exp_amount','min'=>0.01]) !!}
                      </div>
                    </div>
                    <div class="form-group"></div>
                    <button type="submit" class="btn btn-success">@lang('fleet.add')</button>
                    {!! Form::close() !!}
                  </div>
                </div>
              </div>
            </div>
            <div class="row card-body">
              <div class="col-md-12">
                <div class="card card-info">
                  <div class="card-header">
                    <h3 class="card-title">@lang('fleet.acquisition') :<strong>@if($vehicle->make_name){{
                        $vehicle->make_name }}@endif @if($vehicle->model_name){{ $vehicle->model_name }}@endif
                        {{ $vehicle->license_plate }}</strong>
                    </h3>
                  </div>
                  <div class="card-body" id="acq_table">
                    <div class="row">
                      <div class="col-md-12 table-responsive">
                        @php($value = unserialize($vehicle->getMeta('purchase_info')))
                        <table class="table">
                          <thead>
                            <th>@lang('fleet.expenseType')</th>
                            <th>@lang('fleet.expenseAmount')</th>
                            <th>@lang('fleet.action')</th>
                          </thead>
                          <tbody id="hvk">
                            @if($value != null)
                            @php($i=0)
                            @foreach($value as $key=>$row)
                            <tr>
                              @php($i+=$row['exp_amount'])
                              <td>{{$row['exp_name']}}</td>
                              <td>{{Hyvikk::get('currency')." ". $row['exp_amount']}}</td>
                              <td>
                                {!! Form::open(['route'
                                =>['acquisition.destroy',$vehicle->id],'method'=>'DELETE','class'=>'form-horizontal'])
                                !!}
                                {!! Form::hidden("vid",$vehicle->id) !!}
                                {!! Form::hidden("key",$key) !!}
                                <button type="button" class="btn btn-danger del_info" data-vehicle="{{$vehicle->id}}"
                                  data-key="{{$key}}">
                                  <span class="fa fa-times"></span>
                                </button>
                                {!! Form::close() !!}
                              </td>
                            </tr>
                            @endforeach
                            <tr>
                              <td><strong>@lang('fleet.total')</strong></td>
                              <td><strong>{{Hyvikk::get('currency')." ". $i}}</strong></td>
                              <td></td>
                            </tr>
                            @endif
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane " id="driver">
            <div class="card-body">
              {!! Form::open(['url' => 'admin/assignDriver',
              'method'=>'post','class'=>'form-horizontal','id'=>'driverForm']) !!}

              {!! Form::hidden('vehicle_id',$vehicle->id) !!}

              <div class="col-md-12">
                <div class="form-group">
                  {!! Form::label('driver_id',__('fleet.selectDriver'), ['class' => 'form-label']) !!}

                  <select id="driver_id" name="driver_id" class="form-control w-100"  required>
                    {{-- <option value="">@lang('fleet.selectDriver')</option> --}}
                    @foreach($drivers as $driver)

                      <option value="{{$driver->id}}" @if($vehicle->getMeta('assign_driver_id')==$driver->id) selected @endif>
                        {{$driver->name}}@if($driver->getMeta('is_active') != 1)
                        ( @lang('fleet.in_active') ) @endif
                      </option>
                      
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-6" style=" margin-bottom: 20px;">
                <div class="form-group" style="margin-top: 15px;">
                  <div class="col-xs-6 col-xs-offset-3">
                    {!! Form::submit(__('fleet.submit'), ['class' => 'btn btn-warning']) !!}
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          {!! Form::close() !!}
         
        </div>
      </div>
    </div>
  </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.add_new_cat')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>@lang('fleet.new_cat_text')</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>

<div id="myModal2" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.add_new_cat')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>@lang('fleet.new_cat_text')</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>

<div id="myModal3" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.add_new_cat')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>@lang('fleet.new_cat_text')</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section("script")
<script type="text/javascript">
  $(".add_udf").click(function () {
    // alert($('#udf').val());
    var udf_validation = "@lang('fleet.Enter_field_name')";
    var field = $('#udf1').val();
    if(field == "" || field == null){
      alert(udf_validation);
    }

    else{
      $(".blank").append('<div class="row"><div class="col-md-8">  <div class="form-group"> <label class="form-label">'+ field.toUpperCase() +'</label> <input type="text" name="udf['+ field +']" class="form-control" placeholder="Enter '+ field +'" required></div></div><div class="col-md-4"> <div class="form-group" style="margin-top: 30px"><button class="btn btn-danger" type="button" onclick="this.parentElement.parentElement.parentElement.remove();">Remove</button> </div></div></div>');
      $('#udf1').val("");
    }
  });
</script>
<script type="text/javascript">
  $(document).ready(function() {
  $('#driver_id').select2({placeholder: "@lang('fleet.selectDriver')"});
  $('.select2_driver').select2({placeholder: "@lang('fleet.selectDriver')"});
  $('#group_id').select2({placeholder: "@lang('fleet.selectGroup')"});
  $('#type_id').select2({placeholder:"@lang('fleet.type')"});
  $('#make_name').select2({placeholder:"@lang('fleet.SelectVehicleMake')",tags:true});
  $('#color_name').select2({placeholder:"@lang('fleet.SelectVehicleColor')",tags:true});
  $('#model_name').select2({placeholder:"@lang('fleet.SelectVehicleModel')",tags:true});
  $('#make_name').on('change',function(){
        // alert($(this).val());
        $.ajax({
          type: "GET",
          url: "{{url('admin/get-models')}}/"+$(this).val(),
          success: function(data){
            var models =  $.parseJSON(data);
              $('#model_name').empty();
              $.each( models, function( key, value ) {
                $('#model_name').append($('<option>', {
                  value: value.id,
                  text: value.text
                }));
                $('#model_name').select2({placeholder:"@lang('fleet.SelectVehicleModel')",tags:true});
              });
          },
          dataType: "html"
        });
      });
  @if(isset($_GET['tab']) && $_GET['tab']!="")
    $('.nav-pills a[href="#{{$_GET['tab']}}"]').tab('show')
  @endif
  $('#start_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });
  $('#end_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });
  $('#exp_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });
  $('#lic_exp_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });
  $('#reg_exp_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });
  $('#issue_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });

  $(document).on("click",".del_info",function(e){
    var hvk=confirm("Are you sure?");
    if(hvk==true){
      var vid=$(this).data("vehicle");
      var key = $(this).data('key');
      var action="{{ route('acquisition.index')}}/"+vid;

      $.ajax({
        type: "POST",
        url: action,
        data: "_method=DELETE&_token="+window.Laravel.csrfToken+"&key="+key+"&vehicle_id="+vid,
        success: function(data){
          $("#acq_table").empty();
          $("#acq_table").html(data);
          new PNotify({
            title: 'Deleted!',
            text:'@lang("fleet.deleted")',
            type: 'wanring'
          })
        }
        ,
        dataType: "HTML",
      });
    }
  });

  $("#add_form").on("submit",function(e){
    $.ajax({
      type: "POST",
      url: $(this).attr("action"),
      data: $(this).serialize(),
      success: function(data){
        $("#acq_table").empty();
        $("#acq_table").html(data);
        new PNotify({
          title: 'Success!',
          text: '@lang("fleet.exp_add")',
          type: 'success'
        });
        $('#exp_name').val("");
        $('#exp_amount').val("");
      },
      dataType: "HTML"
    });
    e.preventDefault();
  });

  // $("#accountForm").on("submit",function(e){
  //   $.ajax({
  //     type: "POST",
  //     url: $("#accountForm").attr("action"),
  //     data: new FormData(this),
  //     mimeType: 'multipart/form-data',
  //     contentType: false,
  //               cache: false,
  //               processData:false,
  //     success: new PNotify({
  //           title: 'Success!',
  //           text: '@lang("fleet.ins_add")',
  //           type: 'success'
  //       }),
  //   dataType: "json",
  //   });
  //   e.preventDefault();
  // });

  // $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
  //   checkboxClass: 'icheckbox_flat-green',
  //   radioClass   : 'iradio_flat-green'
  // });

});

  // Initialize Select2 on your select boxes
// Listen for the select2:select event on the first select box
$('#make_name').on('select2:select', function(e) {
  // Clear the contents of the second select box
  $('#model_name').val(null).trigger('change');
  $('#color_name').val(null).trigger('change');

});

$('#year').on('input', function(evt) {
    var inputVal = $(this).val();
    var cleanedVal = inputVal.replace(/[^0-9.]/g, '').replace(/^0+/, '');
    if (cleanedVal.length > 4) {
      cleanedVal = cleanedVal.slice(0, 4);
    }
    $(this).val(cleanedVal);
}); 

$('#exp_amount').on('input', function() {
    var inputValue = $(this).val();
    var decimalIndex = inputValue.indexOf('.');
    
    if (decimalIndex !== -1 && inputValue.length - decimalIndex > 3) {
        // Only allow up to 2 digits after the decimal point
        $(this).val(inputValue.substr(0, decimalIndex + 3));
    }
});

$('#exp_name').on('input', function(evt) {
  var inputVal = $(this).val();
  // Only allow alphanumeric characters (A-Z, a-z)
  var cleanedVal = inputVal.replace(/[^A-Za-z]/g, '');
  // Update the input value with the cleaned value
  $(this).val(cleanedVal);
});
</script>
@endsection