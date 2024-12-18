<html lang="en">
<head>
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Laravel 5.8 Custom Datatables filter and Search - W3Adda</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" />
<link  href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css" rel="stylesheet">

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>

</head>
<body>
 
<div class="container-fluid">
    {{-- <table class="table table-bordered table-striped" id="laravel_datatable">
       <thead>
          <tr>
            <th></th>
            <th>#</th>
            <th>@lang('fleet.driverImage')</th> 
            <th>@lang('fleet.name')</th>
            <th>@lang('fleet.email')</th> 
            <th>@lang('fleet.is_active')</th>
            <th>@lang('fleet.phone')</th>
            <th>@lang('fleet.assigned_vehicle')</th>
            <th>@lang('fleet.start_date')</th>
            <th>@lang('fleet.action')</th> 
          </tr>
       </thead>
    </table> --}}

    <table class="table" id="table_test">
      <thead>
        <tr>
          <th scope="col">Payment Id</th>
          <th scope="col">Reference</th>
          <th scope="col">Amount</th>
          <th scope="col">Gateway Response</th>
          <th scope="col">Channel</th>
          <th scope="col">Currency</th>
          <th scope="col">Customer Id</th>
          <th scope="col">Customer Name</th>
          <th scope="col">Customer Email</th>
          <th scope="col">Customer Phone</th>
          <th scope="col">Customer Code</th>
          <th scope="col">Authorization Code</th>
          <th scope="col">Created Date Time</th>
        </tr>
      </thead>
    </table>
</div>
 
 
</body>
</html>
<script>
$(document).ready(function()
{
  $.ajax({
      type: "GET",
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

      url: "{{url('transaction')}}",
     
      success: function(data){
        //alert(data);
          var trHTML = '';
          $.each(data, function (key,value) {
             trHTML += 
                '<tr><td>' + value.id + 
                '</td><td>' + value.reference + 
                '</td><td>' + value.amount + 
                '</td><td>' + value.gateway_response + 
                '</td><td>' + value.channel + 
                '</td><td>' + value.currency + 
                '</td><td>' + value.customer.id + 
                '</td><td>' + value.customer.first_name +' '+ value.customer.last_name +
                '</td><td>' + value.customer.email +
                '</td><td>' + value.customer.phone +
                '</td><td>' + value.customer.customer_code +
                '</td><td>' + value.authorization.authorization_code +
                '</td><td>' + new Date(value.created_at).getDate()  + "-" + (new Date(value.created_at).getMonth()+1) + "-" + new Date(value.created_at).getFullYear() + " " +
                  ("0" + new Date(value.created_at).getHours()).slice(-2) + ":" + ("0" + new Date(value.created_at).getMinutes()).slice(-2)+
                '</td></tr>';     
          });

          $('#table_test').append(trHTML);
      },
      dataType: "json"
    });

   $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
   });
  
  $('#laravel_datatable').DataTable({
         processing: true,
         serverSide: true,
         ajax: {
          url: "{{ url('dtable-posts-lists') }}",
          type: 'GET',
         },
         columns: [
            {data: 'check',"render": function (data) {
               return '<input type="checkbox" name="selected_users[]" value="' + data + '">';
            }},
            {data: 'id', name: 'id'},
            {data: 'driver_image',"render": function (data) {
               return '<img src="' + data + '" class="driver_image" width="50" height="50"/>';
            }},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'is_active', name: 'is_active'},
            {data: 'phone', name: 'phone'},
            {data: 'vehicle', name: 'vehicle'},
            {data: 'start_date', name: 'start_date'},
            {data: 'action',name:'action'}
        ],
        order: [[1, 'desc']],
  });
});

</script>