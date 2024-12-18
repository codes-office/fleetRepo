@extends("layouts.app")
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
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('service-item.index')}}">@lang('fleet.serviceItems')</a></li>
<li class="breadcrumb-item active">@lang('fleet.add_service_item')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    {!! Form::open(['route' => 'service-item.store','method'=>'post']) !!}
    {!! Form::hidden('user_id',Auth::user()->id)!!}
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">
          @lang('fleet.create_service_item')
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
        <div class="row">
          <div class="col-md-12">
            {!! Form::label('description', __('fleet.description'), ['class' => 'col-md-2 control-label']) !!}

            <div class="col-md-10">
              {!! Form::textarea('description', null,['class' => 'form-control','required','rows'=>'3']) !!}
              <br />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="card card-default">
              <div class="card-header">
                <h3 class="card-title">
                  "@lang('fleet.overdue')" @lang('fleet.reminder_settings')
                </h3>
              </div>
              <div class="card-body">
                @lang('fleet.time_interval')
                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-3 col-xs-4">
                    <label class="switch">
                      <input type="checkbox" name="chk1" id="chk1">
                      <span class="slider round"></span>
                    </label>
                  </div>
                  <div class="form-group col-md-3 col-xs-4">
                    <input type="number" name="time1" class="form-control" id="time1">
                  </div>
                  <div class="col-md-4  col-xs-4">
                    <select id="interval" name="interval1" class="form-control">
                      <option value="day(s)"> @lang('fleet.days')</option>
                      <option value="week(s)"> @lang('fleet.weeks')</option>
                      <option value="month(s)"> @lang('fleet.months')</option>
                      <option value="year(s)"> @lang('fleet.years')</option>
                    </select>
                  </div>
                </div>
                <br>
                @lang('fleet.meter_interval')
                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-3">
                    <label class="switch">
                      <input type="checkbox" name="chk2" id="chk2">
                      <span class="slider round"></span>
                    </label>
                  </div>
                  <div class="form-group col-md-4">
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text">{{Hyvikk::get('dis_format')}}</span>
                      </div>
                      <input type="number" name="meter1" class="form-control" id="meter1">
                    </div>
                  </div>
                  <div class="col-md-3">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card card-default">
              <div class="card-header">
                <h3 class="card-title">
                  "@lang('fleet.due_soon')" @lang('fleet.reminder_settings')
                </h3>
              </div>
              <div class="card-body">
                @lang('fleet.show_reminder_time')
                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-3">
                    <label class="switch">
                      <input type="checkbox" name="chk3" id="chk3">
                      <span class="slider round"></span>
                    </label>
                  </div>
                  <div class="form-group col-md-3">
                    <input type="number" name="time2" class="form-control" id="time2">
                  </div>
                  <div class="col-md-4">
                    <select id="interval" name="interval2" class="form-control">
                      <option value="day(s)"> @lang('fleet.days')</option>
                      <option value="week(s)"> @lang('fleet.weeks')</option>
                      <option value="month(s)"> @lang('fleet.months')</option>
                      <option value="year(s)"> @lang('fleet.years')</option>
                    </select>
                  </div>
                </div>

                <br>
                @lang('fleet.show_reminder_meter')

                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-3">
                    <label class="switch">
                      <input type="checkbox" name="chk4" id="chk4">
                      <span class="slider round"></span>
                    </label>
                  </div>
                  <div class="form-group col-md-4">
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text">{{Hyvikk::get('dis_format')}}</span>
                      </div>
                      <input type="number" name="meter2" class="form-control" id="meter2">
                    </div>
                  </div>
                  <div class="col-md-3">
                  </div>
                </div>
              </div>
            </div>
          </div>
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
  $('#time1').on('change',function(){
    $("#chk1").prop('checked',true);
  });

  $('#time2').on('change',function(){
    $("#chk3").prop('checked',true);
  });

  $('#meter1').on('change',function(){
    $("#chk2").prop('checked',true);
  });

  $('#meter2').on('change',function(){
    $("#chk4").prop('checked',true);
  });

</script>
@endsection