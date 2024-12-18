<div class="btn-group">
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
    <span class="fa fa-gear"></span>
    <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu custom" role="menu">
    <a class="dropdown-item" href="{{ url('admin/booking-quotation/invoice/'.$row->id)}}"> <span aria-hidden="true" class="fa fa-list" style="color: #31b0d5;"></span> @lang('fleet.receipt')</a>
    @if($row->status == 0)
    @can('BookingQuotations edit')<a class="dropdown-item" href="{{ url('admin/booking-quotation/'.$row->id.'/edit')}}"> <span aria-hidden="true" class="fa fa-edit" style="color: #f0ad4e;"></span> @lang('fleet.edit')</a>@endcan
    @endif
    @can('BookingQuotations delete')<a class="dropdown-item vtype" data-id="{{$row->id}}" data-toggle="modal" data-target="#myModal" > <span class="fa fa-trash" aria-hidden="true" style="color: #dd4b39"></span> @lang('fleet.archive')</a>@endcan
    </div>
</div>
{!! Form::open(['url' => 'admin/booking-quotation/'.$row->id,'method'=>'DELETE','class'=>'form-horizontal','id'=>'book_'.$row->id]) !!}
{!! Form::hidden("id",$row->id) !!}
{!! Form::close() !!}

{!! Form::open(['url' => 'admin/reject-quotation','method'=>'POST','class'=>'form-horizontal','id'=>'reject_'.$row->id]) !!}
{!! Form::hidden("id",$row->id) !!}
{!! Form::close() !!}