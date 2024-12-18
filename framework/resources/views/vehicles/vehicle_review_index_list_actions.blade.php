<div class="btn-group">
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
      <span class="fa fa-gear"></span>
      <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu custom" role="menu">
      @can('VehicleInspection edit')<a class="dropdown-item" href="{{ url("admin/vehicle-review/".$row->id."/edit") }}"> <span aria-hidden="true" class="fa fa-edit" style="color: #f0ad4e;"></span> @lang('fleet.edit')</a>@endcan
      {!! Form::hidden("id",$row->id) !!}
      @can('VehicleInspection delete')<a class="dropdown-item" data-id="{{$row->id}}" data-toggle="modal" data-target="#myModal"><span aria-hidden="true" class="fa fa-trash" style="color: #dd4b39"></span> @lang('fleet.delete')</a>@endcan
      <a class="dropdown-item" href="{{url('admin/view-vehicle-review/'.$row->id)}}">
      <span class="fa fa-eye" aria-hidden="true" style="color: #398439"></span> @lang('fleet.view')
      </a>
    </div>
</div>
{!! Form::open(['url' => 'admin/delete-vehicle-review/'.$row->id,'method'=>'DELETE','class'=>'form-horizontal','id'=>'form_'.$row->id]) !!}
{!! Form::hidden("id",$row->id) !!}
{!! Form::close() !!}