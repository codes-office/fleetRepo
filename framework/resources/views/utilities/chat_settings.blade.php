@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item">@lang('menu.settings')</li>
<li class="breadcrumb-item active">@lang('fleet.chat') @lang('menu.settings')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.chat') @lang('menu.settings')
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

        {!! Form::open(['route' => 'chat_settings.store','method'=>'post']) !!}
        <div class="row">          
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('pusher_app_id',__('fleet.pusher_app_id'),['class'=>"form-label"]) !!}
              {!! Form::text('pusher_app_id',
              Hyvikk::chat('pusher_app_id'),['class'=>"form-control"]) !!}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('pusher_app_key',__('fleet.pusher_app_key'),['class'=>"form-label"]) !!}
              {!! Form::text('pusher_app_key',
              Hyvikk::chat('pusher_app_key'),['class'=>"form-control razorpay"]) !!}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('pusher_app_secret',__('fleet.pusher_app_secret'),['class'=>"form-label"]) !!}
              {!! Form::text('pusher_app_secret',
              Hyvikk::chat('pusher_app_secret'),['class'=>"form-control"]) !!}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('pusher_app_cluster',__('fleet.pusher_app_cluster'),['class'=>"form-label"]) !!}
              {!! Form::text('pusher_app_cluster',
              Hyvikk::chat('pusher_app_cluster'),['class'=>"form-control"]) !!}
            </div>
          </div>          
        </div>
        {{-- <hr> --}}
        <div class="row">
          <div class="col-md-12">
            {{-- <div class="form-group">
              <h6 class="text-danger"> <strong>@lang('fleet.important_Notes'):</strong></h6>
              <ol class="text-muted">
                <li>To enable or disable international card payments from your <strong>RazorPay</strong> Dashboard: <a href="https://razorpay.com/docs/international-payments/#enable-or-disable-international-payments-from-the-dashboard" target="_blank">Click Here</a>
                <br>
                If you do not want to accept payments in currencies apart from INR (₹), you can turn off <strong>International Card Payment</strong> using the toggle switch <a href="https://dashboard.razorpay.com/#/app/config" target="_blank">available here.</a></li>
                <li>you can automatically email your customers upon successful payments using <strong>Stripe</strong>. Enable this feature with the email customers for successful payments option in your email receipt settings. <a href="https://dashboard.stripe.com/account/emails" target="_blank">Click Here</a></li>
              </ol>
            </div> --}}
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="col-md-2">
          <div class="form-group">
            <input type="submit"  class="form-control btn btn-success"  value="@lang('fleet.save')" />
          </div>
        </div>
      </div>
      {!! Form::close()!!}
    </div>
  </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
  

  @if(Session::get('msg'))
    new PNotify({
      title: 'Success!',
      text: '{{ Session::get('msg') }}',
      type: 'success',
      delay: 15000
    });
  @endif
  @if(Session::get('error_msg'))
    new PNotify({
      title: 'Failed!',
      text: '{{ Session::get('error_msg') }}',
      type: 'error',
      delay: 15000
    });
  @endif

  
</script>
@endsection