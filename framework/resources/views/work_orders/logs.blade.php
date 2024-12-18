@extends("layouts.app")
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('work_order.index')}}">@lang('fleet.work_orders') </a></li>
<li class="breadcrumb-item active">@lang('fleet.work_order_logs')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
          @lang('fleet.work_order_logs')
        </h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table" id="data_table">
          <thead class="thead-inverse">
            <tr>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.vehicle_name')</th>
              <th>@lang('fleet.created_on')</th>
              <th>@lang('fleet.required_by')</th>
              <th>@lang('fleet.personnel')</th>
              <th>@lang('fleet.description')</th>
              <th>@lang('fleet.work_order_price')</th>
              <th>@lang('fleet.total') @lang('fleet.parts') @lang('fleet.cost')</th>
              <th>@lang('fleet.total_cost')</th>
              <th>@lang('fleet.mechanics')</th>
              <th>@lang('fleet.status')</th>
              <th>@lang('fleet.vendor_type')</th>
            </tr>
          </thead>
          <tbody>
            @foreach($data as $row)
            <tr>
              <td>
                @if($row->vehicle['vehicle_image'] != null)
                <img src="{{asset('uploads/'.$row->vehicle['vehicle_image'])}}" height="70px" width="70px">
                @else
                <img src="{{ asset(" assets/images/vehicle.jpeg")}}" height="70px" width="70px">
                @endif
              </td>
              <td>
                {{$row->vehicle['year']}}
                {{$row->vehicle->make_name}} - {{$row->vehicle->model_name}}
                <br>
                <b> @lang('fleet.vin'): </b>{{$row->vehicle['vin']}}
                <br>
                <b> @lang('fleet.plate'):</b> {{$row->vehicle['license_plate']}}
              </td>
              <td>
                {{ date($date_format_setting,strtotime($row->created_on)) }}
              </td>
              <td>
                {{date($date_format_setting,strtotime($row->required_by))}}
              </td>
              <td>
                {{$row->vendor['name']}}
              </td>
              <td>{{$row->description}}</td>
              <td> {{Hyvikk::get('currency')}} {{$row->price}}</td>
              <td> {{Hyvikk::get('currency')}} {{$row->parts_price}}</td>
              <td> {{Hyvikk::get('currency')}} {{$row->price + $row->parts_price}}</td>
              <td>{{ ($row->mechanic_id)?$row->mechanic->name:"" }}</td>
              <td>
                @if($row->status == "Completed")
                <span class="text-success">{{$row->status}}</span>
                @elseif($row->status == "Pending")
                <span class="text-warning">{{$row->status}}</span>
                @else
                {{$row->status}}
                @endif
              </td>
              <td>
                {{$row->type}}
                on
                {{date($date_format_setting.' g:i A',strtotime($row->created_at))}}
              </td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.vehicle_name')</th>
              <th>@lang('fleet.created_on')</th>
              <th>@lang('fleet.required_by')</th>
              <th>@lang('fleet.personnel')</th>
              <th>@lang('fleet.description')</th>
              <th>@lang('fleet.work_order_price')</th>
              <th>@lang('fleet.total') @lang('fleet.parts') @lang('fleet.cost')</th>
              <th>@lang('fleet.total_cost')</th>
              <th>@lang('fleet.mechanics')</th>
              <th>@lang('fleet.status')</th>
              <th>@lang('fleet.vendor_type')</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.delete')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>@lang('fleet.confirm_delete')</p>
      </div>
      <div class="modal-footer">
        <button id="del_btn" class="btn btn-danger" type="button" data-submit="">@lang('fleet.delete')</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
@endsection

@section('script')
<script type="text/javascript">
  $("#del_btn").on("click",function(){
    var id=$(this).data("submit");
    $("#form_"+id).submit();
  });

  $('#myModal').on('show.bs.modal', function(e) {
    var id = e.relatedTarget.dataset.id;
    $("#del_btn").attr("data-submit",id);
  });
</script>
@endsection