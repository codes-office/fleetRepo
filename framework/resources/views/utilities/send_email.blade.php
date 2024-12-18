@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item">@lang('menu.settings')</li>
<li class="breadcrumb-item active">@lang('menu.email_notification')</li>
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

input[type="checkbox"]{
  width:20px;
}
.flat-red{
  display: flex;
  align-items: center;
}
.checkbox-css{
  display: flex;
  align-items: center;
}

</style>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('menu.email_notification')
        </h3>
      </div>
      <div class="card-body">
        {!! Form::open(['url' => 'admin/enable-mail','method'=>'post']) !!}
        <div class=" card card-body">
        <div class="row">
          <div class="col-md-6 col-sm-12">
            <h4>  @lang('menu.email_notification')<span id="change" class="text-muted">
              @if(Hyvikk::email_msg('email')==1)
                (@lang('fleet.enable'))
              @else
                (@lang('fleet.disable'))
              @endif
            </span>
            <a data-toggle="modal" data-target="#myModal1"><i class="fa fa-info-circle fa-lg ml-1" aria-hidden="true"  style="color: #8639dd"></i></a>
          </h4>
          </div>
          <div class="col-md-3 col-sm-12">
            <label class="switch">
              <input type="checkbox" name="email" value="1" id="email" @if(Hyvikk::email_msg('email')==1) checked @endif>
              <span class="slider round"></span>
            </label>
          </div>
          <div class="col-md-3 col-sm-12">
            <button type="submit" class="btn btn-success">@lang('fleet.update')</button>
          </div>
        </div>
      </div>
        {!! Form::close() !!}
        <hr>
        {!! Form::open(['url' => 'admin/email-settings','method'=>'post']) !!}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('users', __('fleet.select_user'), ['class' => 'form-label']) !!}
              <select class="form-control" required name="users[]" multiple style="height: 200px;">
                @foreach($users as $user)
                <option value="{{$user->id}}" @if(in_array($user->id,$selected_users)) selected @endif>{{$user->name}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('chk',__('fleet.selectNotification'), ['class' => 'form-label']) !!} <br>
              <div class="checkbox-css">
                <input type="checkbox" name="chk[]" value="1" class="flat-red form-control" @if(in_array(1,$options)) checked @endif>&nbsp; @lang('fleet.rn')<br>
              </div>
              <div class="checkbox-css">
                <input type="checkbox" name="chk[]" value="2" class="flat-red form-control" @if(in_array(2,$options)) checked @endif>&nbsp; @lang('fleet.in') <br>
              </div>
              <div class="checkbox-css">
                <input type="checkbox" name="chk[]" value="3" class="flat-red form-control" @if(in_array(3,$options)) checked @endif>&nbsp; @lang('fleet.vn') <br>
              </div>
              <div class="checkbox-css">
                <input type="checkbox" name="chk[]" value="4" class="flat-red form-control" @if(in_array(4,$options)) checked @endif>&nbsp; @lang('fleet.dn') <br>
              </div>
              <div class="checkbox-css">
                <input type="checkbox" name="chk[]" value="5" class="flat-red form-control" @if(in_array(5,$options)) checked @endif>&nbsp; @lang('fleet.sn')<br>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="row">
            <div class="form-group">
              <input type="submit" class="form-control btn btn-success" value="@lang('fleet.save')" id="save"/>
            </div>
          </div>
        </div>
        {!! Form::close()!!}
      </div>
    </div>
  </div>
</div>

<!-- Modal 1-->
<div id="myModal1" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('menu.email_notification')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>@lang('fleet.email_notification_info')</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal 1-->
@endsection

@section('script')
<script type="text/javascript">
  $(document).ready(function() {
  //Flat green color scheme for iCheck
    // $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
    //   checkboxClass: 'icheckbox_flat-green',
    //   radioClass   : 'iradio_flat-green'
    // });

    $("#save").on("click",function(){
      if (($("input[name*='chk']:checked").length)<=0) {
          alert("You must select at least 1 Notification");
          return false;
      }else{
        return true;
      }
    });

    $('#email').change(function () {
      if($('#email').is(":checked")){
        // alert("checked");
        $("#change").empty();
        $("#change").append(" (@lang('fleet.enable'))");

      }
      else{
        // alert("unchecked");
        $("#change").empty();
        $("#change").append(" (@lang('fleet.disable'))");
      }
    });
  });
</script>
@endsection