@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item">@lang('menu.settings')</li>
<li class="breadcrumb-item active">@lang('fleet.twilio_settings')</li>
@endsection
@section('extra_css')
<style type="text/css">
  .nav-link {
    padding: .5rem !important;
  }

  .custom .nav-link.active {

      background-color: #21bc6c !important;
  }

  /* The switch - the box around the slider */
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

/* Hide default HTML checkbox */
.switch input {display:none;}

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

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
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
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.twilio_settings')
        </h3>
      </div>
      {!! Form::open(['url' => 'admin/twilio-settings','method'=>'post']) !!}
      <div class="card-body">
        <div class="row">
          @if (count($errors) > 0)
            <div class="alert alert-danger">
              <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
              </ul>
            </div>
          @endif
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('sid',__('fleet.sid'), ['class' => 'form-label']) !!}
              {!! Form::text('sid', Hyvikk::twilio('sid') ,['class' => 'form-control','required']) !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('token',__('fleet.token'), ['class' => 'form-label']) !!}
              {!! Form::text('token', Hyvikk::twilio('token') ,['class' => 'form-control','required']) !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('from',__('fleet.from'), ['class' => 'form-label']) !!}
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-phone"></i></span>
                </div>
                {!! Form::text('from', Hyvikk::twilio('from') ,['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('customer_message',__('fleet.customer_message'), ['class' => 'form-label']) !!}
              {!! Form::textarea('customer_message', Hyvikk::twilio('customer_message') ,['class' => 'form-control','required','size'=>'30x3']) !!}
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('driver_message',__('fleet.driver_message'), ['class' => 'form-label']) !!}
              {!! Form::textarea('driver_message', Hyvikk::twilio('driver_message') ,['class' => 'form-control','required','size'=>'30x3']) !!}
            </div>
          </div>
        </div>

        <h6 class="text-danger"> <strong>@lang('fleet.important_Notes'):</strong></h6>
        <div class="row" style="margin-top: 20px">
                
            <div class="col-md-6">
              <div class="form-group">

                <h6 class="text-success"> <strong>@lang('fleet.replace')</strong></h6>
                <ul class="text-muted">
                  <li>$@lang('fleet.customers name') :<span>@lang('fleet.customers name')</span></li>
                  <li>$@lang('fleet.driver_name') :<span>@lang('fleet.drivername')</span></li>
                  <li>$@lang('fleet.driver_contact') :<span>@lang('fleet.dcf')</span></li>
                  <li>$@lang('fleet.pickup_address') :<span>@lang('fleet.pab')</span></li>
                  <li>$@lang('fleet.destination_address') :<span>@lang('fleet.dab')</span></li>
                  <li>$@lang('fleet.pickup_datetime') :<span>@lang('fleet.pdtb')</span></li>
                  <li>$@lang('fleet.dropoff_datetime') :<span>@lang('fleet.ddtb')</span></li>
                  <li>$@lang('fleet.Passengers') :<span>@lang('fleet.npb')</span></li>
                </ul>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">                
                <ul class="text-muted">
                  <li>@lang('fleet.have')(e.g: +911234567890)</li>
                  <li>@lang('fleet.console')</li>
                  <li>@lang('fleet.trial') <a href="https://www.twilio.com/console/phone-numbers/verified">@lang('fleet.click here')</a>
                  </li>                  
                </ul>
              </div>
            </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="row">
          <div class="form-group">
            <input type="submit" class="form-control btn btn-success" value="@lang('fleet.save')"/>
          </div>
        </div>
      </div>
      {!! Form::close()!!}
      </div>
    </div>
  </div>
</div>
@endsection

@section("script")

<script type="text/javascript">
  @if(Session::get('msg'))
    new PNotify({
        title: 'Success!',
        text: '{{ Session::get('msg') }}',
        type: 'success'
      });
  @endif
</script>

@endsection