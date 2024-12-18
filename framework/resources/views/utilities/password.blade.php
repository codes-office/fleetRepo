@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.change_password')</li>
@endsection
@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card card-warning">
			<div class="card-header">
				<h3 class="card-title">@lang('fleet.change_password') : <strong>{{ Auth::user()->name}}</strong> </h3>
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
				{!! Form::open(array("url"=>"admin/changepassword/".Auth::user()->id))!!}
				<input type="hidden" name="id" value="{{ Auth::user()->id}}">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							{!! Form::label('password',__('fleet.password'),['class'=>"form-label"]) !!}
							<input type="password" name="password" class="form-control" required>
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