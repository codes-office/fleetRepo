@extends('layouts.app')
@section('extra_css')
<style type="text/css">
  .nav-link {
    padding: .5rem !important;
  }

  .custom .nav-link.active {

      background-color: #21bc6c !important;
  }

  .ck-editor__editable {
      min-height: 200px;
  }
</style>
@endsection
@section("breadcrumb")
<li class="breadcrumb-item">@lang('menu.settings')</li>
<li class="breadcrumb-item active">@lang('menu.email_content')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">
          @lang('menu.email_content')
        </h3>
      </div>
      <div class="card-body">
        <div>
          <ul class="nav nav-pills custom">
            <li class="nav-item"><a href="#insurance" data-toggle="tab" class="nav-link active"> @lang('fleet.i_notify') <i class="fa"></i></a></li>
            <li class="nav-item"><a href="#vehicle-licence" data-toggle="tab" class="nav-link"> @lang('fleet.v_lic') <i class="fa"></i></a></li>
            <li class="nav-item"><a href="#driver-licence" data-toggle="tab" class="nav-link"> @lang('fleet.d_lic') <i class="fa"></i></a></li>
            <li class="nav-item"><a href="#registration" data-toggle="tab" class="nav-link"> @lang('fleet.v_reg') <i class="fa"></i></a></li>
            <li class="nav-item"><a href="#reminder" data-toggle="tab" class="nav-link"> @lang('fleet.serviceReminders') <i class="fa"></i></a></li>
          </ul>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="tab-content card-body">
              <div class="tab-pane active" id="insurance">
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                  <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                  </ul>
                </div>
                @endif
                {!! Form::open(['url' => 'admin/set-content/insurance','method'=>'post']) !!}
                <div class="form-group">
                  {!! Form::label('insurance', __('fleet.ins_content'), ['class' => 'form-label']) !!}
                  <textarea name="insurance" id="ins">{{Hyvikk::email_msg('insurance')}}</textarea>
                </div>

                <div class="col-md-2">
                  <div class="form-group">
                    <input type="submit" class="form-control btn btn-success" value="@lang('fleet.set')" />
                  </div>
                </div>
                {!! Form::close()!!}
              </div>

              <div class="tab-pane" id="vehicle-licence">
                {!! Form::open(['url' => 'admin/set-content/vehicle-licence','method'=>'post']) !!}
                <div class="form-group">
                  {!! Form::label('vehicle_licence', __('fleet.vehicle_lic'), ['class' => 'form-label']) !!}
                  <textarea name="vehicle_licence" id="vl">{{Hyvikk::email_msg('vehicle_licence')}}</textarea>
                </div>

                <div class="col-md-2">
                  <div class="form-group">
                    <input type="submit" class="form-control btn btn-success" value="@lang('fleet.set')" />
                  </div>
                </div>
                {!! Form::close()!!}
              </div>

              <div class="tab-pane" id="driver-licence">
                {!! Form::open(['url' => 'admin/set-content/driver-licence','method'=>'post']) !!}
                <div class="form-group">
                  {!! Form::label('driving_licence', __('fleet.driver_lic'), ['class' => 'form-label']) !!}
                  <textarea name="driving_licence" id="dl">{{Hyvikk::email_msg('driving_licence')}}</textarea>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <input type="submit" class="form-control btn btn-success" value="@lang('fleet.set')" />
                  </div>
                </div>
                {!! Form::close()!!}
              </div>

              <div class="tab-pane" id="registration">
                {!! Form::open(['url' => 'admin/set-content/registration','method'=>'post']) !!}
                <div class="form-group">
                  {!! Form::label('registration', __('fleet.reg_content'), ['class' => 'form-label']) !!}
                  <textarea name="registration" id="reg">{{Hyvikk::email_msg('registration')}}</textarea>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <input type="submit" class="form-control btn btn-success" value="@lang('fleet.set')" />
                  </div>
                </div>
                {!! Form::close()!!}
              </div>

              <div class="tab-pane" id="reminder">
                {!! Form::open(['url' => 'admin/set-content/reminder','method'=>'post']) !!}
                <div class="form-group">
                  {!! Form::label('service_reminder', __('fleet.service_reminder_content'), ['class' => 'form-label']) !!}
                  <textarea name="service_reminder" id="sr">{{Hyvikk::email_msg('service_reminder')}}</textarea>
                </div>
                {!! Form::hidden('sr','1') !!}

                <div class="col-md-2">
                  <div class="form-group">
                    <input type="submit" class="form-control btn btn-success" value="@lang('fleet.set')" />
                  </div>
                </div>
                {!! Form::close()!!}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script>
    ClassicEditor.create( document.querySelector( '#ins' ) );
    ClassicEditor.create( document.querySelector( '#vl' ) );
    ClassicEditor.create( document.querySelector( '#dl' ) );
    ClassicEditor.create( document.querySelector( '#reg' ) );
    ClassicEditor.create( document.querySelector( '#sr' ) );
</script>
<script type="text/javascript">
$(document).ready(function() {
  @if(isset($_GET['tab']) && $_GET['tab']!="")
  $('.nav-pills a[href="#{{$_GET['tab']}}"]').tab('show')
  @endif
});
</script>
@endsection