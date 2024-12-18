<div class="btn-group">
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
      <span class="fa fa-gear"></span>
      <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu custom" role="menu">
      @can('Vehicles edit')<a class="dropdown-item" href="{{ url("admin/vehicles/".$row->id."/edit") }}"> <span aria-hidden="true" class="fa fa-edit" style="color: #f0ad4e;"></span> @lang('fleet.edit')</a>@endcan
      {!! Form::hidden("id",$row->id) !!}
      @can('Vehicles delete')<a class="dropdown-item" data-id="{{$row->id}}" data-toggle="modal" data-target="#myModal"><span aria-hidden="true" class="fa fa-trash" style="color: #dd4b39"></span> @lang('fleet.delete')</a>@endcan
      <a class="dropdown-item openBtn" data-id="{{$row->id}}" data-toggle="modal" data-target="#myModal2" id="openBtn">
      <span class="fa fa-eye" aria-hidden="true" style="color: #398439"></span> @lang('fleet.view_vehicle')
      </a>
      @if($row->in_service==1)
      <a class="dropdown-item" href="{{ url("admin/vehicles/disable/".$row->id)}}" class="mybtn" data-toggle="tooltip"  title="@lang('fleet.disable')@lang('fleet.assigned_vehicle')"><span class="fa fa-times" aria-hidden="true" style="color: #5cb85c;"></span> @lang('fleet.disable')@lang('fleet.assigned_vehicle')</a>
      @else
      <a class="dropdown-item" href="{{ url("admin/vehicles/enable/".$row->id)}}" class="mybtn" data-toggle="tooltip"  title="@lang('fleet.enable')@lang('fleet.assigned_vehicle')"><span class="fa fa-check" aria-hidden="true" style="color: #5cb85c;"></span> @lang('fleet.enable')@lang('fleet.assigned_vehicle')</a>
      @endif
    </div>
  </div>
  {!! Form::open(['url' => 'admin/vehicles/'.$row->id,'method'=>'DELETE','class'=>'form-horizontal','id'=>'form_'.$row->id]) !!}

  {!! Form::hidden("id",$row->id) !!}

  {!! Form::close() !!}