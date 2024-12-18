@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item">@lang('menu.settings')</li>
<li class="breadcrumb-item active">@lang('menu.fare_settings')</li>
@endsection
@section('extra_css')
<style type="text/css">
.custom .nav-link.active {
    background-color: #21bc6c !important;
}
</style>
@endsection
@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card card-success">
			<div class="card-header">
				<h3 class="card-title">@lang('menu.fare_settings')
				</h3>
			</div>
			<div class="card-body">
				<div>
					<ul class="nav nav-pills custom">
					@foreach($types as $type)
						<li class="nav-item"><a href="#{{strtolower(str_replace(' ','',$type))}}" data-toggle="tab" class="nav-link text-uppercase @if(reset($types) == $type) active @endif "> {{$type}} <i class="fa"></i></a></li>
					@endforeach
					</ul>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="tab-content card-body">
							@foreach($types as $type)
							@php($type =strtolower(str_replace(" ","",$type)))

							<div class="tab-pane @if(strtolower(str_replace(' ','',reset($types))) == $type) active @endif" id="{{$type}}">
								{!! Form::open(['url' => 'admin/fare-settings?tab='.$type,'files'=>true,'method'=>'post']) !!}
								<div class="row">
									<div class="form-group col-md-3">
										{!! Form::label($type.'_base_fare',__('fleet.general_base_fare'),['class'=>"form-label"]) !!}

										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>
											{!! Form::number('name['.$type.'_base_fare]',Hyvikk::fare($type.'_base_fare'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'_base_km',__('fleet.general_base_km'). " ".Hyvikk::get('dis_format'),['class'=>"form-label"]) !!}
										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('dis_format')}}</span></div>
											{!! Form::number('name['.$type.'_base_km]',Hyvikk::fare($type.'_base_km'),['class'=>"form-control",'required']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'_base_time',__('fleet.general_wait_time'),['class'=>"form-label"]) !!}
										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>

											{!! Form::number('name['.$type.'_base_time]',Hyvikk::fare($type.'_base_time'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'_std_fare',__('fleet.std_fare')." ".Hyvikk::get('dis_format') ,['class'=>"form-label"]) !!}
										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>
											{!! Form::number('name['.$type.'_std_fare]',Hyvikk::fare($type.'_std_fare'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'_weekend_base_fare',__('fleet.weekend_base_fare'),['class'=>"form-label"]) !!}

										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>
											{!! Form::number('name['.$type.'_weekend_base_fare]',Hyvikk::fare($type.'_weekend_base_fare'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'_weekend_base_km',__('fleet.weekend_base_km')." ".Hyvikk::get('dis_format'),['class'=>"form-label"]) !!}

										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('dis_format')}}</span></div>
											{!! Form::number('name['.$type.'_weekend_base_km]',Hyvikk::fare($type.'_weekend_base_km'),['class'=>"form-control",'required']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'_weekend_wait_time',__('fleet.weekend_wait_time'),['class'=>"form-label"]) !!}
										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>

											{!! Form::number('name['.$type.'_weekend_wait_time]',Hyvikk::fare($type.'_weekend_wait_time'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'_weekend_std_fare',__('fleet.weekend_std_fare')." ".Hyvikk::get('dis_format'),['class'=>"form-label"]) !!}
										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>
											{!! Form::number('name['.$type.'_weekend_std_fare]',Hyvikk::fare($type.'_weekend_std_fare'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'_night_base_fare',__('fleet.night_base_fare'),['class'=>"form-label"]) !!}
										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>
											{!! Form::number('name['.$type.'_night_base_fare]',Hyvikk::fare($type.'_night_base_fare'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'_night_base_km',__('fleet.night_base_km')." ".Hyvikk::get('dis_format'),['class'=>"form-label"]) !!}

										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('dis_format')}}</span></div>
											{!! Form::number('name['.$type.'_night_base_km]',Hyvikk::fare($type.'_night_base_km'),['class'=>"form-control",'required']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'_night_wait_time',__('fleet.night_wait_time'),['class'=>"form-label"]) !!}
										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>

											{!! Form::number('name['.$type.'_night_wait_time]',Hyvikk::fare($type.'_night_wait_time'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'_night_std_fare',__('fleet.night_std_fare')." ".Hyvikk::get('dis_format'),['class'=>"form-label"]) !!}

										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>
											{!! Form::number('name['.$type.'_night_std_fare]',Hyvikk::fare($type.'_night_std_fare'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>
								</div>
								<div class="card-footer">
									<div class="col-md-2">
										<div class="form-group">
											<input type="submit"  class="form-control btn btn-success" value="@lang('fleet.save')" />
										</div>
									</div>
								</div>
								{!! Form::close()!!}
							</div>
							@endforeach
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function() {
  @if(isset($_GET['tab']) && $_GET['tab']!="")
  	$('.nav-pills a[href="#{{$_GET['tab']}}"]').tab('show')
  @endif
});
</script>
@endsection