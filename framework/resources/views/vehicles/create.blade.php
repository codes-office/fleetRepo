@extends('layouts.app')
@section('extra_css')
<style type="text/css">
  .nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: #00a65a !important;
  }

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
    background-color: #21bc6c !important;
  }
</style>
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('vehicles.index')}}">@lang('fleet.vehicles')</a></li>
<li class="breadcrumb-item active">@lang('fleet.addVehicle')</li>


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
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.addVehicle')</h3>
      </div>

      <div class="card-body">
        <div class="nav-tabs-custom">
          <ul class="nav nav-pills custom">
            <li class="nav-item"><a class="nav-link active" href="#info-tab" data-toggle="tab">
                @lang('fleet.general_info') <i class="fa"></i></a></li>
          </ul>
        </div>
        <div class="tab-content">
          <div class="tab-pane active" id="info-tab">
            {!! Form::open(['route' => 'vehicles.store','files'=>true,
            'method'=>'post','class'=>'form-horizontal','id'=>'accountForm']) !!}
            {!! Form::hidden('user_id',Auth::user()->id) !!}
            <div class="row card-body">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('make_name', __('fleet.SelectVehicleMake'), ['class' => 'col-xs-5 control-label']) !!}
                  <a data-toggle="modal" data-target="#myModal"><i class="fa fa-info-circle fa-lg" aria-hidden="true"  style="color: #8639dd"></i></a>
                  <div class="col-xs-6">
                    <select name="make_name" class="form-control" required id="make_name">
                      <option></option>
                      @foreach($makes as $make)
                      <option value="{{$make}}" @if(old('make_name')==$make) selected @endif>{{$make}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                
                <div class="form-group">
                  {!! Form::label('model_name', __('fleet.SelectVehicleModel'), ['class' => 'col-xs-5 control-label']) !!}
                  <a data-toggle="modal" data-target="#myModal2"><i class="fa fa-info-circle fa-lg" aria-hidden="true"  style="color: #8639dd"></i></a>
                  <div class="col-xs-6">
                    <select name="model_name" class="form-control" required id="model_name">
                      <option></option>
                      @foreach ($models as $model)   
                      <option value="{{$model}}" @if(old('model_name')==$model) selected @endif>{{$model}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('type_id', __('fleet.type'), ['class' => 'col-xs-5 control-label']) !!}

                  <div class="col-xs-6">
                    <select name="type_id" class="form-control" required id="type_id">
                      <option></option>
                      @foreach($types as $type)
                      <option value="{{$type->id}}" @if(old('type_id')==$type->id) selected @endif>{{$type->displayname}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                @if(Hyvikk::get('traccar_enable')==1)
                <div class="form-group">
                  {!! Form::label('traccar_device_id', __('fleet.traccar_device_id'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::text('traccar_device_id',null,['class' => 'form-control',]) !!}
                  </div>
                </div>
                {{-- <div class="form-group">
                  {!! Form::label('traccar_vehicle_id', __('fleet.traccar_vehicle_id'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::text('traccar_vehicle_id',null,['class' => 'form-control',]) !!}
                  </div>
                </div> --}}
                @endif

                <div class="form-group">
                  {!! Form::label('year', __('fleet.year'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::number('year', null,['class' => 'form-control','required','id'=>'year']) !!}
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
                    {!! Form::number('average', null,['class' => 'form-control','required','step'=>'any']) !!}
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
                    {!! Form::number('int_mileage', null,['class' => 'form-control','required']) !!}
                  </div>
                </div>
                <div class="form-group">
                  {!! Form::label('vehicle_image', __('fleet.vehicleImage'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::file('vehicle_image',null,['class' => 'form-control']) !!}
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('reg_exp_date',__('fleet.reg_exp_date'), ['class' => 'col-xs-5 control-label
                  required']) !!}
                  <div class="col-xs-6">
                    <div class="input-group date">
                      <div class="input-group-prepend"><span class="input-group-text"><i
                            class="fa fa-calendar"></i></span></div>
                      {!! Form::text('reg_exp_date', null,['class' => 'form-control','required']) !!}
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
                        <input type="checkbox" name="in_service" value="1">
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
                      <option value="{{$color}}" @if(old('color_name')==$color) selected @endif>{{$color}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('engine_type', __('fleet.engine'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::select('engine_type',["Petrol"=>__('fleet.petrol'),"Diesel"=>__('fleet.diesel')],null,['class' =>
                    'form-control','required']) !!}
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('horse_power', __('fleet.horsePower'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::number('horse_power', null,['class' => 'form-control','required']) !!}
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('vin', __('fleet.vin'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::text('vin', null,['class' => 'form-control','required']) !!}
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('license_plate', __('fleet.licensePlate'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    {!! Form::text('license_plate', null,['class' => 'form-control','required']) !!}
                  </div>
                </div>

                <div class="form-group">
                  {!! Form::label('lic_exp_date',__('fleet.lic_exp_date'), ['class' => 'col-xs-5 control-label
                  required']) !!}
                  <div class="col-xs-6">
                    <div class="input-group date">
                      <div class="input-group-prepend"><span class="input-group-text"><i
                            class="fa fa-calendar"></i></span></div>
                      {!! Form::text('lic_exp_date', null,['class' => 'form-control','required']) !!}
                    </div>
                  </div>
                </div>
              
                <div class="form-group">
                  {!! Form::label('group_id',__('fleet.selectGroup'), ['class' => 'col-xs-5 control-label']) !!}
                  <div class="col-xs-6">
                    <select id="group_id" name="group_id" class="form-control">
                      <option value="">@lang('fleet.vehicleGroup')</option>
                      @foreach($groups as $group)
                      @if($group->id == 1)
                      <option value="{{$group->id}}" selected>{{$group->name}}</option>
                      @else
                      <option value="{{$group->id}}">{{$group->name}}</option>
                      @endif
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
              </div>
            </div>
            <div style=" margin-bottom: 20px;">
              <div class="form-group" style="margin-top: 15px;">
                <div class="col-xs-6 col-xs-offset-3">
                  {!! Form::submit(__('fleet.submit'), ['class' => 'btn btn-success']) !!}
                </div>
              </div>
            </div>
            {!! Form::close() !!}
          </div>
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
  var udf_validation = "@lang('fleet.Enter_field_name')";
  $(".add_udf").click(function () {
    // alert($('#udf').val());
    var field = $('#udf1').val();
    if(field == "" || field == null){
      alert(udf_validation);
    }

    else{
      $(".blank").append('<div class="row"><div class="col-md-8">  <div class="form-group"> <label class="form-label">'+ field.toUpperCase() +'</label> <input type="text" name="udf['+ field +']" class="form-control" placeholder="Enter '+ field +'" required></div></div><div class="col-md-4"> <div class="form-group" style="margin-top: 30px"><button class="btn btn-danger" type="button" onclick="this.parentElement.parentElement.parentElement.remove();">Remove</button> </div></div></div>');
      $('#udf1').val("");
    }
  });

    $(document).ready(function() {
      $('#group_id').select2({placeholder: "@lang('fleet.selectGroup')"});
      $('#type_id').select2({placeholder:"@lang('fleet.type')"});
      $('#make_name').select2({placeholder:"@lang('fleet.SelectVehicleMake')",tags:true});
      $('#model_name').select2({placeholder:"@lang('fleet.SelectVehicleModel')",tags:true});
      $('#color_name').select2({placeholder:"@lang('fleet.SelectVehicleColor')",tags:true});
      $('#model_name').on('select2:select',()=>{
      selectionMade = true;
     
    });
    $('#make_name').on('select2:select',()=>{
      selectionMade = true;
     
    });
      $('#make_name').on('change',function(){
        // alert($(this).val());
        $.ajax({
          type: "GET",
          url: "{{url('admin/get-models')}}/"+$(this).val(),
          success: function(data){
            var models =  $.parseJSON(data);
              $('#model_name').empty();
              $('#model_name').append('<option value=""></option>');
              $.each( models, function( key, value ) {
                $('#model_name').append('<option value='+value.id+'>'+value.text+'</option>');
                $('#model_name').select2({placeholder:"@lang('fleet.SelectVehicleModel')",tags:true});
              });
          },
          dataType: "html"
    

        });
       
      });
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

      // Initialize Select2 on your select boxes
      // Listen for the select2:select event on the first select box

      $('#make_name').on('select2:select', function(e) {
        // Clear the contents of the second select box
        $('#model_name').val(null).trigger('change');
        $('#color_name').val(null).trigger('change');
      });

    //Flat green color scheme for iCheck
      // $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      //   checkboxClass: 'icheckbox_flat-green',
      //   radioClass   : 'iradio_flat-green'
      // });
    });

    $('#year').on('input', function(evt) {
    var inputVal = $(this).val();
    var cleanedVal = inputVal.replace(/[^0-9.]/g, '').replace(/^0+/, '');
    if (cleanedVal.length > 4) {
      cleanedVal = cleanedVal.slice(0, 4);
    }
    $(this).val(cleanedVal);
  }); 
</script>
@endsection