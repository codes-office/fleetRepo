@extends('layouts.app')

@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('vehicles.index')}}">@lang('fleet.vehicles')</a></li>
<li class="breadcrumb-item active">@lang('fleet.driver_logs')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.driver_logs')</h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table" id="ajax_data_table">
          <thead class="thead-inverse">
            <tr>
              <th>#</th>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.driver')</th>
              <th>@lang('fleet.assigned_on')</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
          <tfoot>
            <tr>
              <th>#</th>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.driver')</th>
              <th>@lang('fleet.assigned_on')</th>
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
  $(function(){
    
    var table = $('#ajax_data_table').DataTable({
          "language": {
              "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
          },
         processing: true,
         serverSide: true,
         ajax: {
          url: "{{ url('admin/driver-logs-fetch') }}",
          type: 'POST',
          data:{}
         },
         columns: [
            {data: 'id',name:'id'},
            {data: 'vehicle', name: 'vehicle'},      
            {data: 'driver', name: 'driver.name'},      
            {name: 'date',
                        data: {
                            _: 'date.display',
                            sort: 'date.timestamp'
                        }}            
        ],
        order: [[0, 'desc']],
        "initComplete": function() {
              table.columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change', function () {
                  // console.log($(this).parent().index());
                    that.search(this.value).draw();
                });
              });
            }
    });
  });
  $(document).on('click','input[type="checkbox"]',function(){
    if(this.checked){
      $('#bulk_delete').prop('disabled',false);

    }else { 
      if($("input[name='ids[]']:checked").length == 0){
        $('#bulk_delete').prop('disabled',true);
      } 
    } 
    
  });
$(document).ready(function() {
  $('#driver_logs tfoot th').each( function () {
    var title = $(this).text();
    $(this).html( '<input type="text" placeholder="'+title+'" />' );
  });

  var table = $('#driver_logs').DataTable({
    "language": {
        "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
     },
     columnDefs: [ { orderable: false, targets: [0] } ],
     // individual column search
     "initComplete": function() {
              table.columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change', function () {
                  // console.log($(this).parent().index());
                    that.search(this.value).draw();
                });
              });
            }
  });
});
</script>
@endsection