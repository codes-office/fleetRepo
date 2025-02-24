@extends('layouts.app')
<!-- Add SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                <h3 class="card-title">Contract &nbsp;
                    @can('Users add')
                    <a href="{{ route('contract-view.create') }}" class="btn btn-success" title="Add Vehicle Contract">
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
                <th>S No</th>
                <th>Name</th>
                <th>Vehicle Type</th>
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
                <th>S No</th>
                <th>Name</th>
                <th>Vehicle Type</th>
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
    
$(document).ready(function () {
    var table = $('#ajax_data_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('contracts.fetch') }}",
            type: 'POST',
            data: function (d) {
                d._token = "{{ csrf_token() }}";

            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", xhr.responseText);
                d.startIndex = d.start; // Pass the start index to the server
            }
        },
        columns: [
    { data: 'check', name: 'check', orderable: false, searchable: false },
    { data: null, name: 'id', render: function (data, type, row, meta) {
        return meta.row + 1; // Generates serial number
    }},
    { data: 'shortCode', name: 'shortCode' },
    { data: 'Vechiletype', name: 'Vechiletype' },
    { data: 'action', name: 'action', orderable: false, searchable: false, render: function (data, type, row, meta) {
        return data; // Keep default buttons for other rows
    }}
],

        order: [[1, 'asc']]
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

    // Handle Single Delete
    // $(document).on('click', '.delete-contract', function () {
    //     var id = $(this).data('id');

    //     Swal.fire({
    //         title: 'Are you sure?',
    //         text: "This contract will be permanently deleted!",
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#d33',
    //         cancelButtonColor: '#3085d6',
    //         confirmButtonText: 'Yes, Delete it!'
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             $.ajax({
    //                 url: "{{ url('/admin/vehiclecontracts') }}/" + id,
    //                 type: 'DELETE',
    //                 data: {
    //                     _token: '{{ csrf_token() }}'
    //                 },
    //                 success: function (response) {
    //                     if (response.success) {
    //                         table.ajax.reload();
    //                         showToast('success', response.message);
    //                     } else {
    //                         showToast('error', response.message);
    //                     }
    //                 },
    //                 error: function () {
    //                     showToast('error', 'An error occurred while deleting the contract.');
    //                 }
    //             });
    //         }
    //     });
    // });


    /////////////////////  View Button //////////////////////
    $(document).on('click', '.view-contract', function () {
    let contractId = $(this).data('id'); // Get contract ID from button

    $.ajax({
        url: `/admin/contracts/${contractId}`,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                let contract = response.contract;

                // Display contract details in SweetAlert
                Swal.fire({
                    title: 'Contract Details',
                    html: `
                    <table border="1" width="100%" style="border-collapse: collapse; text-align: left;">
                       <tr><th>Contract Type</th><td>${contract.contractType}</td></tr>
                        <tr><th>Contract Package</th><td>${contract.contractTypePackage}</td></tr>
                        <tr><th>Short Code</th><td>${contract.shortCode}</td></tr>
                        <tr><th>Number of Duties</th><td>${contract.numberOfDuties}</td></tr>
                        <tr><th>Allotted Km Per Month</th><td>${contract.allottedKmPerMonth}</td></tr>
                        <tr><th>Minimum Hours Per Day</th><td>${contract.minHoursPerDay}</td></tr>
                        <tr><th>Package Cost Per Month</th><td>${contract.packageCostPerMonth}</td></tr>
                        <tr><th>Pricing for Extra Duty</th><td>${contract.pricingForExtraDuty}</td></tr>
                        <tr><th>Cost Per Km After Minimum Km</th><td>${contract.costPerKmAfterMinKm}</td></tr>
                        <tr><th>Cost Per Hour After Minimum Hours</th><td>${contract.costPerHourAfterMinHours}</td></tr>
                        <tr><th>Garage Km on Reporting</th><td>${contract.garageKmOnReporting}</td></tr>
                        <tr><th>Garage Hours Per Day</th><td>${contract.garageHoursPerDay}</td></tr>
                        <tr><th>Base Diesel Price</th><td>${contract.baseDieselPrice}</td></tr>
                        <tr><th>Mileage</th><td>${contract.mileage}</td></tr>
                        <tr><th>Seating Capacity</th><td>${contract.seatingCapacity}</td></tr>
                        <tr><th>AC Price Adjustment Per Km</th><td>${contract.acPriceAdjustmentPerKm}</td></tr>
                        <tr><th>Minimum Trips Per Month</th><td>${contract.minTripsPerMonth}</td></tr>
                        <tr><th>Vehicle Type</th><td>${contract.Vechiletype}</td></tr>
                        <tr><th>Company Name</th><td>${contract.company_name}</td></tr>

                            </table>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Close'
                });
            } else {
                Swal.fire('Error', 'Contract details not found.', 'error');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            Swal.fire('Error', 'Failed to fetch contract details.', 'error');
        }
    });
});

    ////////
   // Handle delete button click
//    $(document).on('click', '.delete-contract', function() {
//     var id = $(this).data('id');
    
//     Swal.fire({
//         title: 'Are you sure?',
//         text: "You won't be able to revert this!",
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonColor: '#3085d6',
//         cancelButtonColor: '#d33',
//         confirmButtonText: 'Yes, delete it!'
//     }).then((result) => {
//         if (result.isConfirmed) {
//             $.ajax({
//                 url: '/admin/contract/' + id,  // Adjust this URL based on your routes
//                 type: 'DELETE',
//                 data: {
//                     _token: '{{ csrf_token() }}'
//                 },
//                 success: function(response) {
//                     if (response.success) {
//                         table.ajax.reload();
//                         showToast('success', response.message);
//                     } else {
//                         showToast('error', response.message);
//                     }
//                 },
//                 error: function() {
//                     showToast('error', 'An error occurred while deleting the contract.');
//                 }
//             });
//         }
//     });
// });

$(document).on('click', '.delete-contract', function () {
    let contractId = $(this).data('id'); // This should have the route URL
    if (confirm("Are you sure you want to delete this contract?")) {
        $.ajax({
            url: contractId, 
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for security
            },
            success: function (response) {
                alert("Contract deleted successfully!");
                location.reload(); // Refresh the table
            },
            error: function (xhr) {
                alert("Error deleting contract!");
            }
        });
    }
});












    
    // Handle Bulk Delete
    $('#bulk_delete').on('click', function () {
        var ids = $('.checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        if (ids.length > 0) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Selected contracts will be deleted permanently!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Delete Selected!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("contracts.bulkDelete") }}',
                        type: 'POST',
                        data: {
                            ids: ids,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function (response) {
                            if (response.success) {
                                table.ajax.reload();
                                showToast('success', response.message);
                            } else {
                                showToast('error', response.message);
                            }
                        },
                        error: function () {
                            showToast('error', 'An error occurred while deleting the contracts.');
                        },
                    });
                }
            });
        } else {
            Swal.fire('No Selection', 'Please select at least one contract to delete.', 'info');
        }
    });

});


</script>
@endsection
