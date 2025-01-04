@extends('layouts.app')

@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.company_team')</li>
@endsection

@section('extra_css')
<style>
    .checkbox, #chk_all {
        width: 20px;
        height: 20px;
    }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
          @lang('fleet.company_teams')
          <a href="{{ route('company_teams.create') }}" class="btn btn-success" title="Add New">
            <i class="fa fa-plus"></i>
          </a>
        </h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table" id="company_team_table">
          <thead>
              <tr>
                  <th><input type="checkbox" id="chk_all"></th>
                  <th>@lang('fleet.team_name')</th>
                  <th>@lang('fleet.manager')</th>
                  <th>@lang('fleet.action')</th>
              </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Bulk Delete Modal -->
<div id="bulkModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.delete')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        {!! Form::open(['url'=>'admin/delete-companyteam', 'method'=>'POST', 'id'=>'form_delete']) !!}
        <div id="bulk_hidden"></div>
        <p>@lang('fleet.confirm_bulk_delete')</p>
        <button id="bulk_action" class="btn btn-danger" type="submit">@lang('fleet.delete')</button>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
<!-- End of Modal -->
@endsection

@section('extra_js')
<script>
  $(document).ready(function () {
    $('#companyTeamsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/company_teams/fetch_data', // Adjust the URL as needed
            type: 'GET',
        },
        columns: [
            { data: 'team_name', name: 'team_name' },
            { data: 'Manager', name: 'Manager' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    });
},
        columns: [
            {data: 'check', name: 'check', searchable: false, orderable: false},
            {data: 'team_name', name: 'team_name'},
            {data: 'Manager', name: 'Manager'},
            {data: 'action', name: 'action', searchable: false, orderable: false},
        ],
        order: [[1, 'desc']],
    });

    // Bulk delete functionality
    $('#bulk_delete').click(function () {
        const ids = [];
        $('input.checkbox:checked').each(function () {
            ids.push($(this).val());
        });
        if (ids.length > 0) {
            $('#bulkModal').modal('show');
            $('#bulk_hidden').html(`<input type="hidden" name="ids" value="${ids.join(',')}">`);
        } else {
            alert('Please select at least one record to delete.');
        }
    });
});
</script>
@endsection
