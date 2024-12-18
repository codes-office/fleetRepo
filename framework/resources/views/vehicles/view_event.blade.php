
<div role="tabpanel">
    <ul class="nav nav-pills">
        <li class="nav-item"><a href="#info-tab" data-toggle="tab" class="nav-link custom_padding active"> @lang('fleet.general_info') <i class="fa"></i></a>
        </li>

        <li class="nav-item"><a href="#address-tab" data-toggle="tab" class="nav-link custom_padding"> @lang('fleet.insurance') <i class="fa"></i></a>
        </li>

        <li class="nav-item"><a href="#acq-tab" data-toggle="tab" class="nav-link custom_padding"> @lang('fleet.purchase_info') <i class="fa"></i></a>
        </li>

        <li class="nav-item"><a href="#reviews" data-toggle="tab" class="nav-link custom_padding"> @lang('fleet.vehicle_inspection') <i class="fa"></i></a>
        </li>
    </ul>

	<div class="tab-content">
		<!-- tab1-->
		<div class="tab-pane active" id="info-tab">
			<table class="table table-striped">
				<tr>
					<th>@lang('fleet.vehicle')</th>
					<td>{{$vehicle->make_name}}</td>
				</tr>

				<tr>
					<th>@lang('fleet.model')</th>
					<td>
						{{$vehicle->model_name}}
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.type')</th>
					<td>
						{{$vehicle->types->displayname}}
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.year')</th>
					<td>
						{{$vehicle->year}}
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.average') 
					@if(Hyvikk::get('dis_format') == "km" && Hyvikk::get('fuel_unit') == "gallon")
					(@lang('fleet.kmpg'))
					@endif
					@if(Hyvikk::get('dis_format') == "km" && Hyvikk::get('fuel_unit') == "liter")
					(@lang('fleet.kmpl'))
					@endif
					@if(Hyvikk::get('dis_format') == "miles" && Hyvikk::get('fuel_unit') == "gallon")
					(@lang('fleet.mpg'))
					@endif
					@if(Hyvikk::get('dis_format') == "miles" && Hyvikk::get('fuel_unit') == "liter")
					(@lang('fleet.mpl'))
					@endif
				    </th>
					<td>
						{{$vehicle->average}} 
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.intMileage') @if(Hyvikk::get('dis_format') == "km")(@lang('fleet.km')) @endif @if(Hyvikk::get('dis_format') == "miles")(@lang('fleet.miles')) @endif</th>
					<td>
						{{$vehicle->int_mileage}}
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.vehicleImage')</th>
					<td>
						@if($vehicle->vehicle_image != null)
			            <a href="{{ asset('uploads/'.$vehicle->vehicle_image) }}" target="_blank" class="col-xs-3 control-label">View</a>
			            @else
						-
						@endif
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.engine')</th>
					<td>
						{{$vehicle->engine_type}}
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.horsePower')</th>
					<td>
						{{$vehicle->horse_power}}
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.color')</th>
					<td>
						{{$vehicle->color_name ?? ''}}
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.vin')</th>
					<td>
						{{$vehicle->vin}}
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.licensePlate')</th>
					<td>
						{{$vehicle->license_plate}}
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.lic_exp_date')</th>
					<td>
						@if($vehicle->lic_exp_date)
						{{date(Hyvikk::get('date_format'),strtotime($vehicle->lic_exp_date))}}
						@endif
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.reg_exp_date')</th>
					<td>
						@if($vehicle->reg_exp_date)
						{{date(Hyvikk::get('date_format'),strtotime($vehicle->reg_exp_date))}}
						@endif
					</td>
				</tr>

				{{-- <tr>
					<th>@lang('fleet.assigned_driver')</th>
					<td>
						@php
						$drivers=($vehicle->getMeta('driver_id')) ? $vehicle->drivers()->pluck('name')->toArray()??[]:[];
						@endphp
						{{ implode(', ', $drivers) }}
					</td>
				</tr> --}}
			</table>
		</div>
		<!--tab1-->

		<!--tab2-->
		<div class="tab-pane" id="address-tab" >
			<table class="table table-striped">
				<tr>
					<th>@lang('fleet.vehicle')</th>
					<td>
					{{$vehicle->make_name}}-{{$vehicle->model_name}}-{{$vehicle->types->displayname}}
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.insuranceNumber')</th>
					<td>
					{{$vehicle->getMeta('ins_number')}}
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.inc_doc')</th>
					<td>
					@if($vehicle->getMeta('documents') != null)
					<a href="{{ asset('uploads/'.$vehicle->getMeta('documents')) }}" target="_blank">
					View
					</a>
					@endif
					</td>
				</tr>

				<tr>
					<th>@lang('fleet.inc_expirationDate')</th>
					<td>
						@if($vehicle->getMeta('ins_exp_date'))
							{{date(Hyvikk::get('date_format'),strtotime($vehicle->getMeta('ins_exp_date')))}}
						@endif
					</td>
				</tr>
			</table>
		</div>
		<!--tab2-->

		{{-- tab3 --}}
		<div class="tab-pane " id="acq-tab" >
			<div class="card card-default">
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table">
								<thead>
									<th>@lang('fleet.expenseType')</th>
									<th>@lang('fleet.expenseAmount')</th>
								</thead>
								@php
								$value = unserialize($vehicle->getMeta('purchase_info'));
								@endphp
								<tbody id="hvk">
									@if($value != null)
									@php
									$i=0;
									@endphp
									@foreach($value as $row)
									<tr>
										@php
										$i+=$row['exp_amount'];
										@endphp
										<td>{{$row['exp_name']}}</td>
										<td>{{Hyvikk::get('currency')." ". $row['exp_amount']}}</td>
									</tr>
									@endforeach
									<tr>
										<td><strong>@lang('fleet.total')</strong></td>
										<td><strong>{{Hyvikk::get('currency')." ". $i}}</strong></td>
										<td></td>
									</tr>
									@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!--tab3-->

		<!-- tab4 -->
		<div class="tab-pane " id="reviews" >
			<div class="card card-default">
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
						@foreach($vehicle->reviews as $r)
							<a href="{{url('admin/view-vehicle-review/'.$r->id)}}" class="btn btn-success" style="margin-bottom: 5px;" title="View Review">@lang('fleet.reg_no'): {{$r->reg_no}}</a>
							&nbsp; <a href="{{url('admin/print-vehicle-review/'.$r->id)}}" class="btn btn-danger" target="_blank" title="@lang('fleet.print')" style="margin-bottom: 5px;"><i class="fa fa-print"></i> @lang('fleet.print')</a>
							<br>
						@endforeach
						</div>

					</div>

				</div>
			</div>
		</div>
		<!-- tab4 -->
	</div>
</div>