@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('service-reminder.index')}}">@lang('fleet.serviceReminders')</a></li>
<li class="breadcrumb-item active">@lang('fleet.add_service_reminder')</li>
@endsection
@section('extra_css')
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.serviceReminders')</h3>
      </div>
      {!! Form::open(['route' => 'service-reminder.store','method'=>'post']) !!}
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
        <div class="row">
          <div class="form-group col-md-6">
            {!! Form::label('vehicle_id',__('fleet.selectVehicle'), ['class' => 'form-label']) !!}
            <select id="vehicle_id" name="vehicle_id" class="form-control" required>
              <option value="">-</option>
              @foreach($vehicles as $vehicle)
              <option value="{{$vehicle->id}}">{{$vehicle->make_name}} - {{$vehicle->model_name}} -
                {{$vehicle->year}}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-6">
            {!! Form::label('start_date', __('fleet.start_date'), ['class' => 'form-label']) !!}
            <div class="input-group date">
              <div class="input-group-prepend"><span class="input-group-text"><span
                    class="fa fa-calendar"></span></span></div>
              {!! Form::text('start_date',date('Y-m-d'),['class'=>'form-control','required','id'=>'start_date']) !!}
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead class="thead-inverse">
              <tr>
                <th>
                </th>
                <th>@lang('fleet.description')</th>
                <th>@lang('fleet.service_interval')</th>
                <th>@lang('fleet.create_reminder')</th>
              </tr>
            </thead>
            <tbody>
              @foreach($services as $service)
              <tr>
                <td>
                  <input type="checkbox" name="chk[]" value="{{$service->id}}" class="flat-red">
                </td>
                <td>
                  {{$service->description}}
                </td>
                <td>
                  {{$service->overdue_time}} {{$service->overdue_unit}}
                  @if($service->overdue_meter != null)
                  @lang('fleet.or') {{$service->overdue_meter}} {{Hyvikk::get('dis_format')}}
                  @endif
                </td>
                <td>
                  {{$service->duesoon_time}} {{$service->duesoon_unit}} @lang('fleet.before_due')
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="col-md-12">
          {!! Form::submit(__('fleet.save'), ['class' => 'btn btn-success']) !!}
        </div>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>

@endsection
@section('script')
<script type="text/javascript">
  $(document).ready(function() {
    $('#vehicle_id').select2({placeholder: "@lang('fleet.selectVehicle')"});
    //Flat green color scheme for iCheck
    // $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
    //   checkboxClass: 'icheckbox_flat-green',
    //   radioClass   : 'iradio_flat-green'
    // });

  $('#start_date').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  });
  });
</script>
@endsection