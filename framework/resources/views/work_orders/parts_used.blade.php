@extends("layouts.app")
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('work_order.index')}}">@lang('fleet.work_orders') </a></li>
<li class="breadcrumb-item active">@lang('fleet.partsUsed')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
          @lang('fleet.partsUsed')
        </h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table" id="data_table">
          <thead class="thead-inverse">
            <tr>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.description')</th>
              <th>@lang('fleet.part')</th>
              <th>@lang('fleet.qty')</th>
              <th>@lang('fleet.unit_cost')</th>
              <th>@lang('fleet.total_cost')</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->parts as $row)
            <tr>
              <td>{{ $row->workorder->vehicle->make_name }} - {{ $row->workorder->vehicle->model_name }} - {{
                $row->workorder->vehicle->license_plate }}</td>
              <td>{!! $row->workorder->description !!}</td>
              <td>{{ $row->part->title }}</td>
              <td>{{ $row->qty }}</td>
              <td>{{Hyvikk::get('currency')." ". $row->price }}</td>
              <td>{{ Hyvikk::get('currency')." ". $row->total }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection