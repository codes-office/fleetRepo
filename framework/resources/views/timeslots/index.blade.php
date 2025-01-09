@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Timeslot Management</li>
@endsection

@section('extra_css')
<style type="text/css">
  .checkbox,
  #chk_all {
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
        <h3 class="card-title">Timeslots &nbsp;
          @can('Users add')
          <a href="{{ route('timeslots.create') }}" class="btn btn-success" title="Add Timeslot">
            <i class="fa fa-plus"></i>
          </a>
          @endcan
        </h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table" id="ajax_data_table">
          <thead>
            <tr>
              <th><input type="checkbox" id="chk_all"></th>
              <th>@lang('fleet.id')</th>
              <th>Created By</th>
              <th>Created To</th>
              <th>Active</th>
              <th>Log</th>
              <th>shift</th>
              <th>Days Available</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
          <tfoot>
            <tr>
              <th>
                @can('Users delete')
                <button class="btn btn-danger" id="bulk_delete" disabled>
                  <i class="fa fa-trash"></i>
                </button>
                @endcan
              </th>
              <th>@lang('fleet.id')</th>
              <th>Created By</th>
              <th>Created To</th>
              <th>Active</th>
              <th>Log</th>
              <th>shift</th>
              <th>Days Available</th>
              <th>Actions</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
  $(function () {
    var table = $('#ajax_data_table').DataTable({
    language: {
        url: '{{ asset("assets/datatables/")."/". ("fleet.datatable_lang") }}',
    },
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ url('admin/timeslot-fetch') }}",
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}', // Include CSRF token
        },
    },
    columns: [
    { data: 'check', name: 'check', orderable: false, searchable: false },
    { data: 'id', name: 'id' },
    { data: 'user_name', name: 'user_name' },
    { data: 'company_name', name: 'company_name' },
    { data: 'active', name: 'active' },
    { data: 'log', name: 'log' },
    { data: 'shift', name: 'shift' },
    { data: 'days_available', name: 'days_available' },
    { data: 'action', name: 'action', orderable: false, searchable: false },
],

    order: [[1, 'desc']],
});


    $('#chk_all').on('click', function () {
      $('.checkbox').prop('checked', this.checked);
      $('#bulk_delete').prop('disabled', !this.checked);
    });

    $(document).on('click', '.checkbox', function () {
      $('#chk_all').prop('checked', $('.checkbox:checked').length === $('.checkbox').length);
      $('#bulk_delete').prop('disabled', $('.checkbox:checked').length === 0);
    });

    $('#bulk_delete').on('click', function () {
      if ($('.checkbox:checked').length > 0) {
        $('.checkbox:checked').each(function () {
          $('#bulk_hidden').append('<input type="hidden" name="ids[]" value="' + $(this).val() + '">');
        });
        $('#bulkModal').modal('show');
      } else {
        alert('No rows selected');
      }
    });
  });
</script>
@endsection