@extends('layouts.app')
@section('extra_css')
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('notes.index')}}">@lang('fleet.notes')</a></li>
<li class="breadcrumb-item active">@lang('fleet.edit_note')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-warning">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.edit_note')</h3>
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

        {!! Form::open(['route' => ['notes.update',$data->id],'method'=>'PATCH']) !!}
        {!! Form::hidden('user_id',Auth::user()->id)!!}
        {!! Form::hidden('id',$data->id)!!}

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('vehicle_id',__('fleet.vehicle'), ['class' => 'form-label']) !!}
              <select id="vehicle_id" name="vehicle_id" class="form-control" required>
                <option value="">-</option>
                @foreach($vehicles as $vehicle)
                <option value="{{$vehicle->id}}" @if($vehicle->id==$data->vehicle_id) selected @endif>
                  {{$vehicle->make_name}} - {{$vehicle->model_name}} -
                  {{$vehicle->license_plate}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              {!! Form::label('customer_id',__('fleet.person_incharge'), ['class' => 'form-label']) !!}
              <select id="customer_id" name="customer_id" class="form-control" required>
                <option value="">-</option>
                @foreach($customers as $customer)
                <option value="{{$customer->id}}" @if($customer->id==$data->customer_id) selected
                  @endif>{{$customer->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              {!! Form::label('submitted_on', __('fleet.submitted_on'), ['class' => 'form-label']) !!}
              <div class="input-group date">
                <div class="input-group-prepend"><span class="input-group-text"><span class="fa fa-calendar"></span>
                </div>
                {!! Form::text('submitted_on',$data->submitted_on,['class'=>'form-control']) !!}
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('status',__('fleet.status'), ['class' => 'form-label']) !!}
              {!! Form::select('status',["Pending"=>"Pending", "Processing"=>"Processing",
              "Completed"=>"Completed","Hold"=>"Hold"],$data->status,['class' => 'form-control','required']) !!}
            </div>
            <div class="form-group">
              {!! Form::label('note',__('fleet.note'), ['class' => 'form-label']) !!}
              {!! Form::textarea('note',$data->note,['class'=>'form-control','size'=>'30x2','required']) !!}
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

@section("script")
<script type="text/javascript">
  $(document).ready(function() {
  $('#vehicle_id').select2({placeholder: "@lang('fleet.selectVehicle')"});
  $('#customer_id').select2({placeholder: "@lang('fleet.person_incharge')"});

  $('#submitted_on').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  });
});
</script>
@endsection