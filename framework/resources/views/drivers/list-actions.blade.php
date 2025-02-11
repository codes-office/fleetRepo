<div class="btn-group">
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
      <span class="fa fa-gear"></span>
      <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu custom" role="menu">
      <a class="dropdown-item" class="mybtn changepass" data-id="{{$row->id}}" data-toggle="modal" data-target="#changepass" title="@lang('fleet.change_password')"><i class="fa fa-key" aria-hidden="true" style="color:#269abc;"></i> @lang('fleet.change_password')</a>
      @can('Drivers edit')<a class="dropdown-item" href="{{ url("admin/drivers/".$row->id."/edit")}}"> <span aria-hidden="true" class="fa fa-edit" style="color: #f0ad4e;"></span> @lang('fleet.edit')</a>@endcan
      @can('Drivers delete')<a class="dropdown-item" data-id="{{$row->id}}" data-toggle="modal" data-target="#myModal"><span aria-hidden="true" class="fa fa-trash" style="color: #dd4b39"></span> @lang('fleet.delete')</a>@endcan

      @if($row->getMeta('is_active'))
      <a class="dropdown-item" href="{{ url("admin/drivers/disable/".$row->id)}}" class="mybtn" data-toggle="tooltip"  title="@lang('fleet.disable_driver')"><span class="fa fa-times" aria-hidden="true" style="color: #5cb85c;"></span> @lang('fleet.disable_driver')</a>
      @else
      <a class="dropdown-item" href="{{ url("admin/drivers/enable/".$row->id)}}" class="mybtn" data-toggle="tooltip"  title="@lang('fleet.enable_driver')"><span class="fa fa-check" aria-hidden="true" style="color: #5cb85c;"></span> @lang('fleet.enable_driver')</a>
      @endif
    </div>
  </div>
  {!! Form::open(['url' => 'admin/drivers/'.$row->id,'method'=>'DELETE','class'=>'form-horizontal','id'=>'form_'.$row->id]) !!}
  {!! Form::hidden("id",$row->id) !!}
  {!! Form::close() !!}