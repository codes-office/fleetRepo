@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item">{{ link_to_route('roles.index', __('fleet.roles'))}}</li>
<li class="breadcrumb-item active">@lang('fleet.add_role')</li>
@endsection

@section('extra_css')
<style>
  input[type="checkbox"]{
    width:20px;
    margin: 0px auto;
  }
  .table-row label{
    margin-top: 0.5rem;
  }
  .table td, .table th {
    padding: 0.30rem 0.50rem;
  }
</style>  
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.add_role')</h3>
      </div>

      <div class="card-body">
        @if (count($errors) > 0)
          <div class="alert alert-danger">
            <ul>
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {!! Form::open(['route' => 'roles.store','method'=>'post']) !!}
        <div class="row">
          <div class="form-group col-md-6">
            {!! Form::label('name', __('fleet.name'), ['class' => 'form-label']) !!}
            {!! Form::text('name', null,['class' => 'form-control','required']) !!}
          </div>
        </div>
        <div class="row">
          {!! Form::label('permission',__('fleet.module_permission').":", ['class' => 'col-xs-5 control-label']) !!}
        </div>
        <div class="row" style="height:500px; overflow-y:auto;">  
          <table class="table table-bordered">
            <thead>
              <tr>
                <th style="width:22px !important;" scope="col">@lang('fleet.select_modules')</th>
                <th class="text-center" style="width:13px !important;" scope="col">@lang('menu.list')</th>
                <th class="text-center" style="width:13px !important;" scope="col">@lang('menu.add')</th>
                <th class="text-center" style="width:13px !important;" scope="col">@lang('menu.edit')</th>
                <th class="text-center" style="width:13px !important;" scope="col">@lang('menu.delete')</th>
                <th class="text-center" style="width:13px !important;" scope="col">@lang('menu.import')</th>
                <th class="text-center" style="width:13px !important;" scope="col">@lang('menu.all')</th>
              </tr>
            </thead>
            <tbody>
              @php $new_names = ['users','drivers','customers','vehicles','vehicle_type','vehicleGroup','vehicle_inspection','transactions','bookings','booking_quotes','reports','fuel','vendors','parts','parts_category','work_orders','mechanics','notes','serviceReminders','serviceItems','testimonials','team','settings','inquiries']; @endphp
              @php $new_modules = array_combine($modules,$new_names); @endphp
              {{-- @dd($new_modules) --}}
              @foreach($new_modules as $row=>$val)
              <tr>
                <th scope="row" class="table-row">{!! Form::label('permission',__('menu.'.$val), ['class' => 'col-xs-5 control-label']) !!}</th>
                <td>
                  @if(!in_array($row,["Inquiries","Reports","Settings"]))
                    <input type="checkbox" name="{{ $row." list" }}" value="1" class="flat-red form-control {{ $row }}">
                  @endif
                </td>
                <td>
                  @if(!in_array($row,["Inquiries","Reports","Settings"]))
                    <input type="checkbox" name="{{ $row." add" }}" value="1" class="flat-red form-control {{ $row }}">
                  @endif
                </td>
                <td>
                  @if(!in_array($row,["Inquiries","Reports","Transactions","ServiceReminders","Settings"]))
                    <input type="checkbox" name="{{ $row." edit" }}" value="1" class="flat-red form-control {{ $row }}">
                  @endif
                </td>
                <td>
                  @if(!in_array($row,["Inquiries","Reports","Settings"]))
                    <input type="checkbox" name="{{ $row." delete" }}" value="1" class="flat-red form-control {{ $row }}">
                  @endif
                </td>
                <td>
                  @if(in_array($row,["Drivers","Customer","Vendors"]))
                    <input type="checkbox" name="{{ $row." import" }}" value="1" class="flat-red form-control {{ $row }}">
                  @endif
                </td>
                <td>
                  @if(in_array($row,["Inquiries","Reports","Settings"]))
                    <input type="checkbox" name="{{ $row." list" }}" value="1" class="flat-red form-control">
                  @else
                    <input type="checkbox" class="flat-red form-control All" data-type="{{ $row }}">
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer">
        <div class="row">
          <div class="form-group col-md-4">
            {!! Form::submit(__('fleet.save'), ['class' => 'btn btn-success']) !!}
          </div>
        </div>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
@endsection

@section('script')
  <script>
    $(".All").change(function() {
      var name = $(this).data('type');
      if(this.checked) {
        $('.'+name).prop("checked", true);
      }
      else{
        $('.'+name).prop("checked", false);
      }
    });
  </script>
@endsection