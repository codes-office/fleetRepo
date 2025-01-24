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
					<ul class="nav nav-pills custom ">
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


									<!-- <div class="form-group col-md-3">
										{!! Form::label($type.'base_fare',_('fleet.general_base_fare'),['class'=>"form-label"]) !!}

										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>
											{!! Form::number('name['.$type.'_base_fare]',Hyvikk::fare($type.'_base_fare'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>
									<div class="form-group col-md-3">
                                        {!! Form::label($type.'base_km',_('fleet.general_base_km'). " ".Hyvikk::get('dis_format'),['class'=>"form-label"]) !!}
										<div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{Hyvikk::get('dis_format')}}</span></div>
                                                {!! Form::number('name['.$type.'_base_km]',Hyvikk::fare($type.'_base_km'),['class'=>"form-control",'required']) !!}
                                            </div>
                                        </div> -->
                                        <!-- ////////////////////////////////////////////////////// -->

                                          
                                            
                                            








                                        <!-- /////////////////////////////////////////////////////////////// -->


                                        <!-- 

									<div class="form-group col-md-3">
										{!! Form::label($type.'base_time',_('fleet.general_wait_time'),['class'=>"form-label"]) !!}
										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>

											{!! Form::number('name['.$type.'_base_time]',Hyvikk::fare($type.'_base_time'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'std_fare',_('fleet.std_fare')." ".Hyvikk::get('dis_format') ,['class'=>"form-label"]) !!}
										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>
											{!! Form::number('name['.$type.'_std_fare]',Hyvikk::fare($type.'_std_fare'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'weekend_base_fare',_('fleet.weekend_base_fare'),['class'=>"form-label"]) !!}

										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>
											{!! Form::number('name['.$type.'_weekend_base_fare]',Hyvikk::fare($type.'_weekend_base_fare'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'weekend_base_km',_('fleet.weekend_base_km')." ".Hyvikk::get('dis_format'),['class'=>"form-label"]) !!}

										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('dis_format')}}</span></div>
											{!! Form::number('name['.$type.'_weekend_base_km]',Hyvikk::fare($type.'_weekend_base_km'),['class'=>"form-control",'required']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'weekend_wait_time',_('fleet.weekend_wait_time'),['class'=>"form-label"]) !!}
										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>

											{!! Form::number('name['.$type.'_weekend_wait_time]',Hyvikk::fare($type.'_weekend_wait_time'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'weekend_std_fare',_('fleet.weekend_std_fare')." ".Hyvikk::get('dis_format'),['class'=>"form-label"]) !!}
										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>
											{!! Form::number('name['.$type.'_weekend_std_fare]',Hyvikk::fare($type.'_weekend_std_fare'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'night_base_fare',_('fleet.night_base_fare'),['class'=>"form-label"]) !!}
										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>
											{!! Form::number('name['.$type.'_night_base_fare]',Hyvikk::fare($type.'_night_base_fare'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'night_base_km',_('fleet.night_base_km')." ".Hyvikk::get('dis_format'),['class'=>"form-label"]) !!}

										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('dis_format')}}</span></div>
											{!! Form::number('name['.$type.'_night_base_km]',Hyvikk::fare($type.'_night_base_km'),['class'=>"form-control",'required']) !!}
										</div>
									</div>

									<div class="form-group col-md-3">
										{!! Form::label($type.'night_wait_time',_('fleet.night_wait_time'),['class'=>"form-label"]) !!}
										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>

											{!! Form::number('name['.$type.'_night_wait_time]',Hyvikk::fare($type.'_night_wait_time'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div> -->

									<!-- <div class="form-group col-md-3">
										{!! Form::label($type.'night_std_fare',_('fleet.night_std_fare')." ".Hyvikk::get('dis_format'),['class'=>"form-label"]) !!}

										<div class="input-group mb-3">
											<div class="input-group-prepend">
											<span class="input-group-text">{{Hyvikk::get('currency')}}</span></div>
											{!! Form::number('name['.$type.'_night_std_fare]',Hyvikk::fare($type.'_night_std_fare'),['class'=>"form-control",'required','min'=>0,'step'=>'0.01']) !!}
										</div>
									</div> -->
                                        <!-- ////////////////////////////////////////// -->



<table class="table table-bordered" id="slab-table">
    <thead>
        <tr>
            <th>Slab ID</th>
            <th>Base Fare</th>
            <th>Base KM</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                {!! Form::number('slab_id['.$type.']', null, ['class' => 'form-control', 'required', 'min' => 0]) !!}
            </td>
            <td>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{ Hyvikk::get('currency') }}</span>
                    </div>
                    {!! Form::number('name['.$type.'_base_fare]', Hyvikk::fare($type.'_base_fare'), ['class' => 'form-control', 'required', 'min' => 0, 'step' => '0.01']) !!}
                </div>
            </td>
            <td>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{ Hyvikk::get('dis_format') }}</span>
                    </div>
                    {!! Form::number('name['.$type.'_base_km]', Hyvikk::fare($type.'_base_km'), ['class' => 'form-control', 'required']) !!}
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-primary add-slab">Add</button>
            </td>
        </tr>
    </tbody>
</table>

                                        <!-- /////////////////////////// -->

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
<script>
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('add-slab')) {
            const table = document.getElementById('slab-table').querySelector('tbody');
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td>
                    <input type="number" name="slab_id[]" class="form-control" required min="0">
                </td>
                <td>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ Hyvikk::get('currency') }}</span>
                        </div>
                        <input type="number" name="name[base_fare][]" class="form-control" required min="0" step="0.01">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ Hyvikk::get('dis_format') }}</span>
                        </div>
                        <input type="number" name="name[base_km][]" class="form-control" required>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-slab">Remove</button>
                </td>
            `;

            table.appendChild(newRow);
        }

        if (e.target && e.target.classList.contains('remove-slab')) {
            e.target.closest('tr').remove();
        }
    });
</script>

@endsection