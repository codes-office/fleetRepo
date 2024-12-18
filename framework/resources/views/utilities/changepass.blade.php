@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.change_details')</li>
@endsection
@section('content')
<div class="row col-md-12">
	@if (count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
        </ul>
      </div>
    @endif
	{{-- @if(session()->has('message'))
	<div class="alert alert-success col-sm-6 offset-sm-3 text-center">
		{{  session()->get('message') }}
	</div>
	@endif --}}
</div>
<div class="row">
	<div class="col-md-12">
		<div class="card card-warning">
			<div class="card-header">
				<h3 class="card-title">@lang('fleet.change_details') : <strong>{{ $user_data->name}}</strong></h3>
			</div>
			<div class="card-body">
				{!! Form::open(array("url"=>"admin/change-details/".$user_data->id,'files'=>true,'method'=>'POST'))!!}
				<input type="hidden" name="id" value="{{ $user_data->id}}">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							{!! Form::label('name',__('fleet.name'),['class'=>"form-label"]) !!}
							{!! Form::text('name',$user_data->name,['class'=>"form-control",'required']) !!}
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
						
							{!! Form::label('email',__('fleet.email'),['class'=>"form-label"]) !!}
							<div class="input-group mb-3">
								<div class="input-group-prepend">
								<span class="input-group-text"><i class="fa fa-envelope"></i></span></div>
								{!! Form::email('email',$user_data->email,['class'=>"form-control",'required']) !!}
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
						{!! Form::label('image', __('fleet.picture'), ['class' => 'form-label']) !!}
						@if($user_data->user_type == "D" && $user_data->getMeta('driver_image') != null)
						@if(starts_with($user_data->getMeta('driver_image'),'http'))
						@php($src = $user_data->getMeta('driver_image'))
						@else
						@php($src=asset('uploads/'.$user_data->getMeta('driver_image')))
						@endif
						<a href="{{ $src }}" target="_blank">View</a>

						@elseif($user_data->user_type != "D" && $user_data->getMeta('profile_image') != null)
						<a href="{{ asset('uploads/'.$user_data->getMeta('profile_image')) }}"  target="_blank">@lang('fleet.view')
						</a>
						@elseif($user_data->user_type == "C" && $user_data->getMeta('profile_pic') != null)
						@if(starts_with($user_data->getMeta('profile_pic'),'http'))
						@php($src = $user_data->getMeta('profile_pic'))
						@else
						@php($src=asset('uploads/'.$user_data->getMeta('profile_pic')))
						@endif
						<a href="{{ $src }}" target="_blank">View
						</a>
						@endif
						<br>
						{!! Form::file('image',null,['class' => 'form-control']) !!}
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							{!! Form::label('language',__('fleet.language'),['class'=>"form-label"]) !!}
							<select id='language' name='language' class="form-control" required>
								<option value="">-</option>
								@if(Auth::user()->getMeta('language')!= null)
								@php ($language = Auth::user()->getMeta('language'))
								@else
								@php($language = Hyvikk::get("language"))
								@endif
								@foreach($languages as $lang)
								@if($lang != "vendor")
								@php($l = explode('-',$lang))
								@if($language == $lang)
								<option value="{{$lang}}" selected> {{$l[0]}}</option>
								@else
								<option value="{{$lang}}" > {{$l[0]}} </option>
								@endif
								@endif

								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
						  {!! Form::label('dark_mode',__('fleet.dark_mode'), ['class' => 'form-label']) !!}
						  {{-- <a data-toggle="modal" data-target="#myModal8"><i class="fa fa-info-circle fa-lg" aria-hidden="true"  style="color: #8639dd"></i></a> --}}
						  <br>
						  <label class="switch">
							<input type="checkbox" name="theme" value="dark-mode" id="expense_enable_driver" @if($user_data->theme=='dark-mode') checked @endif>
							<span class="slider round"></span>
						  </label>
						</div>
					  </div>
					</div>
					<div class="row">
						<div class="form-group">
							<input type="submit"  class="form-control btn btn-warning"  value="@lang('fleet.change_details')" />
						</div>
						@if(session()->has('message'))
							<div class="pt-1 ml-2 col-md-10">
								<b class="update_message text-success mb-0" style="font-size: 20px !important;">{{  session()->get('message') }}</b>
							</div>
						@endif 
						
					</div>
				{!! Form::close()!!}

			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="card card-warning">
			<div class="card-header">
				<h3 class="card-title">@lang('fleet.change_password') : <strong>{{ Auth::user()->name}}</strong> </h3>
			</div>
			<div class="card-body">

				{!! Form::open(array("url"=>"admin/changepassword/".Auth::user()->id))!!}
				<input type="hidden" name="id" value="{{ Auth::user()->id}}">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							{!! Form::label('password',__('fleet.password'),['class'=>"form-label"]) !!}
							<div class="input-group mb-3">
								<div class="input-group-prepend">
								<span class="input-group-text"><i class="fa fa-lock"></i></span></div>
								<input type="password" name="password" class="form-control" required>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<input type="submit"  class="form-control btn btn-warning"  value="@lang('fleet.change_password')" />
						</div>
						{!! Form::close()!!}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section("script")
<script>
	$(document).ready(function () {
		// window.setTimeout(function () { 
		// 	$(".update_message").fadeOut();
		// }, 5000);
		
		// window.setTimeout(function(){ 
		// 	$(".alert-success").alert('close');
		// }, 5000);
		setTimeout(function(){
			$('.update_message').remove();
		}, 5000);
	});
	</script>
@endsection