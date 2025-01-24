@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Vehicle Contracts Management</li>
@endsection

@section('extra_css')
<style type="text/css">
    .checkbox, #chk_all {
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
                <h3 class="card-title">Vehicle Contracts &nbsp;
                    @can('Users add')
                    <a href="{{ route('vehiclecontracts.create') }}" class="btn btn-success" title="Add Vehicle Contract">
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
                            <th>Name</th>
                            <th>Vehicle Type</th>
                            <th>Company Name</th>
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
                            <th>Name</th>
                            <th>Vehicle Type</th>
                            <th>Company Name</th>
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

function showToast(type, message) {
    Swal.fire({
        icon: type,  // 'success', 'error', etc.
        text: message,
        toast: false,
        position: 'center',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        background: type === 'success' ? '#fff' : '#fff',
        color: type === 'success' ? '##464849f3' : '#990000',
        customClass: {
                                popup: 'small-toast'  // Custom class to reduce height
                            },
                            showClass: {
                                popup: 'animate_animated animate_fadeInRight' // Smooth show animation
                            },
                            hideClass: {
                                popup: 'animate_animated animate_fadeOutRight' // Smooth hide animation
                            },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        },
        didClose: () => {
            // Ensure any additional animations have time to complete
            setTimeout(() => {}, 1000);
        }, backdrop: `
        rgba(0, 0, 0, 0.6)
    `
    });
    
}



    $(function () {
    var table = $('#ajax_data_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('vehiclecontracts.fetch') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
            },
        },
        columns: [
            { data: 'check', name: 'check', orderable: false, searchable: false },
            { data: 'Name', name: 'Name' },
            { data: 'Vechiletype', name: 'Vechiletype' },
            { data: 'company_name', name: 'company_name' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[1, 'desc']],
    });

    // Handle check/uncheck all checkboxes
    $('#chk_all').on('click', function () {
        $('.checkbox').prop('checked', this.checked);
        toggleBulkDeleteButton();
    });

    // Enable/disable bulk delete button based on selected checkboxes
    $(document).on('click', '.checkbox', function () {
        $('#chk_all').prop('checked', $('.checkbox:checked').length === $('.checkbox').length);
        toggleBulkDeleteButton();
    });

    // Function to toggle bulk delete button
    function toggleBulkDeleteButton() {
        $('#bulk_delete').prop('disabled', $('.checkbox:checked').length === 0);
    }



    // Handle delete button click
$(document).on('click', '.delete-contract', function() {
    var id = $(this).data('id');
    
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/admin/vehiclecontracts/' + id,  // Adjust this URL based on your routes
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        showToast('success', response.message);
                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function() {
                    showToast('error', 'An error occurred while deleting the contract.');
                }
            });
        }
    });
});


















    // Handle bulk delete action
    $('#bulk_delete').on('click', function () {
        var ids = $('.checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        if (ids.length > 0) {
            if (confirm('Are you sure you want to delete the selected contracts?')) {
                $.ajax({
                    url: '{{ route('vehiclecontracts.bulkDelete') }}',
                    type: 'POST',
                    data: {
                        ids: ids,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        if (response.success) {
                            table.ajax.reload(); // Reload the table data
                            showToast('success',response.message);
                        } else {
                            showToast('success', response.message);
                        }
                    },
                    error: function () {
                        showToast('error','An error occurred while deleting the contracts.');
                    },
                });
            }
        } else {
            alert('No rows selected');
        }
    });
});

</script>
@endsection
