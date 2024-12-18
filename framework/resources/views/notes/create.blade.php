@extends('layouts.app')
@section('extra_css')
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('notes.index')}}">@lang('fleet.notes')</a></li>
<li class="breadcrumb-item active">@lang('fleet.add_note')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.create_note')</h3>
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

        {!! Form::open(['route' => 'notes.store','method'=>'post']) !!}
        {!! Form::hidden('user_id',Auth::user()->id)!!}

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('vehicle_id',__('fleet.vehicle'), ['class' => 'form-label']) !!}
              <select id="vehicle_id" name="vehicle_id" class="form-control" required>
                <option value="">-</option>
                @foreach($vehicles as $vehicle)
                <option value="{{$vehicle->id}}">{{$vehicle->make_name}} - {{$vehicle->model_name}} -
                  {{$vehicle->license_plate}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              {!! Form::label('customer_id',__('fleet.person_incharge'), ['class' => 'form-label']) !!}
              <select id="customer_id" name="customer_id" class="form-control" required>
                <option value="">-</option>
                @if(Auth::user()->user_type == "D")
                <option value="{{Auth::user()->id}}" selected>{{Auth::user()->name}}</option>
                @else
                @foreach($customers as $customer)
                <option value="{{$customer->id}}">{{$customer->name}}</option>
                @endforeach
                @endif
              </select>
            </div>
            <div class="form-group">
              {!! Form::label('submitted_on', __('fleet.submitted_on'), ['class' => 'form-label']) !!}
              <div class="input-group date">
                <div class="input-group-prepend"><span class="input-group-text"><span class="fa fa-calendar"></span>
                </div>
                {!! Form::text('submitted_on',null,['class'=>'form-control','required']) !!}
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('status',__('fleet.status'), ['class' => 'form-label']) !!}
              {!! Form::select('status',["Pending"=>__("fleet.pending"), "Processing"=>__("fleet.Processing"),
              "Completed"=>__("fleet.Completed"),"Hold"=>__("fleet.Hold")],null,['class' => 'form-control','required']) !!}
            </div>
            <div class="form-group">
              {!! Form::label('note',__('fleet.note'), ['class' => 'form-label']) !!}
              {!! Form::textarea('note',null,['class'=>'form-control','size'=>'30x2','required']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            {!! Form::submit(__('fleet.add_note'), ['class' => 'btn btn-success']) !!}
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