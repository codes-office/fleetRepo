@extends('layouts.app')

@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.customers')</li>
@endsection

@section('extra_css')
<style type="text/css">
  .checkbox, #chk_all{
    width: 20px;
    height: 20px;
  }
  .show-password-button{
    outline: none;
    border: 1px solid #ced4da;
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    @if (count($errors) > 0)
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">ADD Team
            &nbsp; @can('Customer add')<a href="{{route('company_teams.create')}}" class="btn btn-success" title="@lang('fleet.add_new')"><i class="fa fa-plus"></i></a>@endcan
            @can('Customer import')&nbsp;&nbsp;<button data-toggle="modal" data-target="#import" class="btn btn-warning">@lang('fleet.import')</button>@endcan
            </h3>
      </div>

      <div class="card-body table-responsive">
        @if (!Auth::guest() && Auth::user()->user_type != "D" && Auth::user()->user_type != "C"  && Auth::user()->user_type != 'O')
        <table class="table" id="ajax_data_table" style="padding-bottom: 25px">
          <thead class="thead-inverse">
            <tr>
              <th>
                <input type="checkbox" id="chk_all">
              </th>
              <th>@lang('fleet.name')</th>
              <th>Manager</th>
              <th>Employees</th>
              <th>@lang('fleet.action')</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
              <th>
                @can('Customer delete')<button class="btn btn-danger" id="bulk_delete" data-toggle="modal" data-target="#bulkModal" disabled title="@lang('fleet.delete')"><i class="fa fa-trash"></i></button>@endcan
              </th>
              <th>@lang('fleet.name')</th>
              <th>Manager</th>
              <th>Employees</th>
              <th>@lang('fleet.action')</th>
            </tr>
          </tfoot>
        </table>
        @endif

        @if(!Auth::guest() && Auth::user()->user_type != "D" && Auth::user()->user_type != "C" && Auth::user()->user_type != "M" && Auth::user()->user_type != 'S')
        <table class="table" id="ajax_admin_data_table" style="padding-bottom: 25px">
          <thead class="thead-inverse">
            <tr>
              <th>
                <input type="checkbox" id="chk_all">
              </th>
              <th>@lang('fleet.name')</th>
               <th>Manager</th>
                <th>Employees</th>
              <th>@lang('fleet.action')</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
              <th>
                @can('Customer delete')<button class="btn btn-danger" id="bulk_delete" data-toggle="modal" data-target="#bulkModal" disabled title="@lang('fleet.delete')"><i class="fa fa-trash"></i></button>@endcan
              </th>
              <th>@lang('fleet.name')</th>
              <th>Manager</th>
              <th>Employees</th>
              <th>@lang('fleet.action')</th>
            </tr>
          </tfoot>
        </table>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="import" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.importCustomers')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        {!! Form::open(['url'=>'admin/import-customers','method'=>'POST','files'=>true]) !!}
        <div class="form-group">
          {!! Form::label('excel',__('fleet.importCustomers'),['class'=>"form-label"]) !!}
          {!! Form::file('excel',['class'=>"form-control",'required']) !!}
        </div>
        <div class="form-group">
          <a href="{{ asset('assets/samples/customers.xlsx') }}">@lang('fleet.downloadSampleExcel')</a>
        </div>
        <div class="form-group">
          <h6 class="text-muted">@lang('fleet.note'):</h6>
          <ul class="text-muted">
            <li>@lang('fleet.customerImportNote')</li>
            <li>@lang('fleet.excelNote')</li>
            <li>@lang('fleet.fileTypeNote')</li>
            <li>@lang('fleet.skipNote')</li>
          </ul>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-warning" type="submit">@lang('fleet.import')</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
        {!! Form::close() !!}
    </div>
  </div>
</div>
<!-- Modal -->

<!-- Modal -->
<div id="bulkModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.delete')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        {!! Form::open(['url'=>'admin/delete-customer','method'=>'POST','id'=>'form_delete']) !!}
        <div id="bulk_hidden"></div>
        <p>@lang('fleet.confirm_bulk_delete')</p>
      </div>
      <div class="modal-footer">
        <button id="bulk_action" class="btn btn-danger" type="submit">@lang('fleet.delete')</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
        {!! Form::close() !!}
    </div>
  </div>
</div>
<!-- Modal -->

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
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

{{-- <!-- Modal -->
<div id="changepass" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.change_password')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        {!! Form::open(['url'=>url('admin/change_password'),'id'=>'changepass_form']) !!}
        <form id="change" action="{{url('admin/change_password')}}" method="POST">
          {!! Form::hidden('driver_id',"",['id'=>'driver_id'])!!}
        <div class="form-group">
          {!! Form::label('passwd',__('fleet.password'),['class'=>"form-label"]) !!}
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-lock"></i></span>
            </div>
            {!! Form::password('passwd',['class'=>"form-control",'id'=>'passwd','required']) !!}
            <div class="input-group-prepend">
              <button type="button" id="show-password-button" class="show-password-button">
                <i class="fa fa-eye" aria-hidden="true"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button id="password" class="btn btn-info" type="submit" >@lang('fleet.change_password')</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
        </div>
        {!! Form::close() !!}
        </form>
      </div>
    </div>
  </div>
</div> --}}
<!-- Modal -->
@endsection

@section('extra_js')

<script type="text/javascript">
  $(document).ready(function () {
    $('#ajax_data_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('company_team.fetch_data') }}",
            type: 'GET'
        },
        columns: [
            {data: 'check', name: 'check', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'team_manager', name: 'team_manager'},
            {data: 'employee_count', name: 'employee_count'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    $('#chk_all').on('click', function () {
        $('input.checkbox').prop('checked', this.checked);
    });

//     $('#bulk_delete').click(function () {
//         var ids = [];
//         $('input.checkbox:checked').each(function () {
//             ids.push($(this).val());
//         });
//         if (ids.length > 0) {
//             if (confirm('Are you sure you want to delete selected teams?')) {
//                 $.ajax({
//                     url: "{{ route('company_team.bulk_delete') }}",
//                     type: 'POST',
//                     data: {
//                         _token: "{{ csrf_token() }}",
//                         ids: ids
//                     },
//                     success: function (data) {
//                         $('#ajax_data_table').DataTable().ajax.reload();
//                     }
//                 });
//             }
//         } else {
//             alert('Please select at least one record to delete.');
//         }
//     });
 });

</script>

@endsection
