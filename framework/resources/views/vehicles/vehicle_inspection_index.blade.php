@extends("layouts.app")
@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.vehicle_inspection')</li>
@endsection
@section('extra_css')
<style type="text/css">
  .checkbox, #chk_all{
    width: 20px;
    height: 20px;
  }
</style>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
        @lang('fleet.vehicle_inspection')
        &nbsp;
        <a href="{{ url('admin/vehicle-inspection-create')}}" class="btn btn-success" title="@lang('fleet.add_vehicle_inspection')"><i class="fa fa-plus"></i></a></h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table" id="data_table" style="padding-bottom: 25px">
          <thead class="thead-inverse">
            <tr>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.review_by')</th>
              <th>@lang('fleet.reg_no')</th>
              <th>@lang('fleet.action')</th>
            </tr>
          </thead>
          <tbody>
            {{-- @dd($reviews->vehicle) --}}
          @foreach($reviews as $r)
            <tr>
              <td>{{$r->vehicle->make_name}} - {{$r->vehicle->model_name}} - {{$r->vehicle->types['displayname']}}</td>
              <td>{{$r->user->name}}</td>
              <td>{{$r->reg_no}}</td>
              <td>
                <div class="btn-group">
                  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                    <span class="fa fa-gear"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <div class="dropdown-menu custom" role="menu">
                    <a class="dropdown-item" href="{{url('admin/view-vehicle-inspection/'.$r->id)}}">
                    <span class="fa fa-eye" aria-hidden="true" style="color: #398439"></span> @lang('fleet.view')
                    </a>
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.review_by')</th>
              <th>@lang('fleet.reg_no')</th>
              <th>@lang('fleet.action')</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
