<div class="row">
	<div class="col-md-12 table-responsive">
		<table class="table">
			<thead>
				<th>@lang('fleet.expenseType')</th>
				<th>@lang('fleet.expenseAmount')</th>
				<th>@lang('fleet.action')</th>
			</thead>
			<tbody id="hvk">
				@php
				$i=0;
				$data = unserialize($vehicle->getMeta('purchase_info'));
				@endphp
				@foreach($data as $key=>$row)
				<tr>
					@php
					$i+=$row['exp_amount'];
					@endphp
					<td>{{$row['exp_name']}}</td>
					<td>{{Hyvikk::get('currency')." ". $row['exp_amount']}}</td>
					<td>
					{!! Form::open(['route' =>['acquisition.destroy',$vehicle->id],'method'=>'DELETE','class'=>'form-horizontal']) !!}
					{!! Form::hidden("vid",$vehicle->id) !!}
					{!! Form::hidden("key",$key) !!}
					<button type="button" class="btn btn-danger del_info" data-vehicle="{{$vehicle->id}}" data-key="{{$key}}">
					<span class="fa fa-times"></span>
					</button>
					{!! Form::close() !!}
					</td>
				</tr>
				@endforeach
				<tr>
					<td><strong>@lang('fleet.total')</strong></td>
					<td><strong>{{Hyvikk::get('currency')." ". $i}}</strong></td>
					<td></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>