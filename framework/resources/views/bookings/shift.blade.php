@extends('layouts.app')

@section('extra_css')
<style type="text/css">
  .checkbox, #chk_all {
    width: 18px;
    height: 18px;
    cursor: pointer;
  }
  .table-responsive {
    overflow-x: auto;
  }
  .btn {
    margin-right: 5px;
  }
</style>
@endsection

@section('breadcrumb')
<li class="breadcrumb-item active">@lang('menu.bookings')</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('Shift Time')</h3>
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="data_table" style="width:100%">
            <thead>
              <tr>
                <th><input type="checkbox" id="chk_all"></th>
                <th>#</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Actual Office</th>
                <th>PickUp Location</th>
                <th>Drop Location</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete selected records?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirm_bulk_delete">Delete</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
  var table = $('#data_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ url('admin/bookings/ajaxdata') }}",
      type: 'POST',
      data: function(d) {
        d.selected_date = $('#dateFilter').val(); // Send selected date in AJAX request
      }
    },
    columns: [
      { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
      { data: 'user_id', name: 'user_id' },
      { data: 'name', name: 'name' },
      { data: 'phone', name: 'phone' },
      { data: 'gender', name: 'gender' },
      { data: 'actual_office', name: 'actual_office' },
      { data: 'pickup_location', name: 'pickup_location' },
      { data: 'drop_location', name: 'drop_location' }
    ],
    order: [[1, 'desc']]
  });

  $('#chk_all').click(function() {
    $('.checkbox').prop('checked', this.checked);
    toggleBulkDeleteButton();
  });

  function toggleBulkDeleteButton() {
    var checked = $('.checkbox:checked').length > 0;
    $('#bulk_delete').prop('disabled', !checked);
  }

  $('#data_table tbody').on('change', '.checkbox', function() {
    toggleBulkDeleteButton();
    if (!this.checked) {
      $('#chk_all').prop('checked', false);
    }
  });

  $('#confirm_bulk_delete').click(function() {
    var ids = $('.checkbox:checked').map(function() {
      return $(this).val();
    }).get();

    $.ajax({
      url: "{{ route('bookings.bulk_delete') }}",
      method: 'POST',
      data: {
        ids: ids,
        _token: "{{ csrf_token() }}"
      },
      success: function(response) {
        table.ajax.reload();
        $('#bulkModal').modal('hide');
      }
    });
  });
});
</script>
@endsection
