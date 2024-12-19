@extends('layouts.app')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')
@section('extra_css')
<style type="text/css">
.show-password-button{
    outline: none;
    border: 1px solid #ced4da;
  }
  .mybtn1 {
    padding-top: 4px;
    padding-right: 8px;
    padding-bottom: 4px;
    padding-left: 8px;
  }

  .checkbox,
  #chk_all {
    width: 20px;
    height: 20px;
  }

  td>img {
    border-radius: 50%;
  }
</style>
@endsection
@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.drivers')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">

    @if (count($errors) > 0)
      <div class="alert alert-danger">
        @if (session('errors'))
            <p>{{ session('errors.error') }}</p>
            @if(session('errors.data'))
              <ul>
                  @foreach (session('errors.data') as $bookingId)
                      <li><a href="{{ route('bookings.edit', $bookingId) }}">Booking ID : {{ $bookingId }}</a></li>
                  @endforeach
              </ul>
            @endif
        @endif
        @if(!is_array($errors))
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        @endif
      </div>
    @endif

    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('menu.drivers') &nbsp;
          @can('Drivers add') <a href="{{ route('drivers.create') }}" class="btn btn-success"
            title="@lang('fleet.addDriver')"> <i class="fa fa-plus"></i> </a> @endcan
          @can('Drivers import') &nbsp;&nbsp;<button data-toggle="modal" data-target="#import"
            class="btn btn-warning">@lang('fleet.import')</button>@endcan
        </h3>
      </div>
   
      <div class="card-body table-responsive">
        @if(!Auth::guest() && Auth::user()->user_type != "D" && Auth::user()->user_type != "C"  && Auth::user()->user_type != 'M')
        <table class="table" id="ajax_data_table" style="padding-bottom: 15px">
          <thead class="thead-inverse">
            <tr>
              <th>
                <input type="checkbox" id="chk_all">
              </th>
              <th>#</th>
              <th>@lang('fleet.driverImage')</th>
              <th>@lang('fleet.name')</th>
              <th>@lang('fleet.email')</th>
              <th>@lang('fleet.is_active')</th>
              <th>@lang('fleet.phone')</th>
              {{-- <th>@lang('fleet.assigned_vehicle')</th> --}}
              <th>@lang('fleet.start_date')</th>
              <th>@lang('fleet.action')</th>
               @if (!Auth::guest() && Auth::user()->user_type != "D" && Auth::user()->user_type != "C"  && Auth::user()->user_type != 'M')
               <th>@lang('fleet.assign_admin')</th>
               @endif
               @if (!Auth::guest() && Auth::user()->user_type != "D" && Auth::user()->user_type != "C"  && Auth::user()->user_type != 'M')
               <th>@lang('fleet.assigned_admin')</th>
               @endif
            </tr>
          </thead>
          <tbody>

          </tbody>
          <tfoot>
            <tr>
              <th>
                @can('Drivers delete')<button class="btn btn-danger" id="bulk_delete" data-toggle="modal"
                  title="@lang('fleet.delete')" data-target="#bulkModal" disabled>
                  <i class="fa fa-trash"></i></button>@endcan
              </th>
              <th>#</th>
              <th>@lang('fleet.driverImage')</th>
              <th>@lang('fleet.name')</th>
              <th>@lang('fleet.email')</th>
              <th>@lang('fleet.is_active')</th>
              <th>@lang('fleet.phone')</th>
              {{-- <th>@lang('fleet.assigned_vehicle')</th> --}}
              <th>@lang('fleet.start_date')</th>
              <th>@lang('fleet.action')</th>
              <th>@lang('fleet.assign_admin')</th>
            </tr>
          </tfoot>
        </table>
        @endif
        @if(!Auth::guest() && Auth::user()->user_type != "D" && Auth::user()->user_type != "C" && Auth::user()->user_type != "O" && Auth::user()->user_type != 'S')
        <table class="table" id="ajax_admin_data_table" style="padding-bottom: 15px">
          <thead class="thead-inverse">
            <tr>
              <th>
                <input type="checkbox" id="chk_all">
              </th>
              <th>#</th>
              <th>@lang('fleet.driverImage')</th>
              <th>@lang('fleet.name')</th>
              <th>@lang('fleet.email')</th>
              <th>@lang('fleet.is_active')</th>
              <th>@lang('fleet.phone')</th>
              {{-- <th>@lang('fleet.assigned_vehicle')</th> --}}
              <th>@lang('fleet.start_date')</th>
              <th>@lang('fleet.action')</th>
              
            </tr>
          </thead>
          <tbody>

          </tbody>
          <!--<tfoot>-->
          <!--  <tr>-->
          <!--    <th>-->
          <!--      @can('Drivers delete')<button class="btn btn-danger" id="bulk_delete" data-toggle="modal"-->
          <!--        title="@lang('fleet.delete')" data-target="#bulkModal" disabled>-->
          <!--        <i class="fa fa-trash"></i></button>@endcan-->
          <!--    </th>-->
          <!--    <th>#</th>-->
          <!--    <th>@lang('fleet.driverImage')</th>-->
          <!--    <th>@lang('fleet.name')</th>-->
          <!--    <th>@lang('fleet.email')</th>-->
          <!--    <th>@lang('fleet.is_active')</th>-->
          <!--    <th>@lang('fleet.phone')</th>-->
          <!--    {{-- <th>@lang('fleet.assigned_vehicle')</th> --}}-->
          <!--    <th>@lang('fleet.start_date')</th>-->
          <!--    <th>@lang('fleet.action')</th>-->
          <!--  </tr>-->
          <!--</tfoot>-->
        </table>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="import" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.importDrivers')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        {!! Form::open(['url'=>'admin/import-drivers','method'=>'POST','files'=>true]) !!}
        <div class="form-group">
          {!! Form::label('excel',__('fleet.importDrivers'),['class'=>"form-label"]) !!}
          {!! Form::file('excel',['class'=>"form-control",'required']) !!}
        </div>
        <div class="form-group">
          <a href="{{ asset('assets/samples/drivers.xlsx') }}">@lang('fleet.downloadSampleExcel')</a>
        </div>
        <div class="form-group">
          <h6 class="text-muted">@lang('fleet.note'):</h6>
          <ul class="text-muted">
            <li>@lang('fleet.driverImportNote1')</li>
            <li>@lang('fleet.driverImportNote2')</li>
            <li>@lang('fleet.driverImportNote3')</li>
            <li>@lang('fleet.excelNote')</li>
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
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.delete')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        {!! Form::open(['url'=>'admin/delete-drivers','method'=>'POST','id'=>'form_delete']) !!}
        <div id="bulk_hidden"></div>
        <p>@lang('fleet.confirm_bulk_delete')</p>
      </div>
      <div class="modal-footer">
        <button id="bulk_action" class="btn btn-danger" type="submit" data-submit="">@lang('fleet.delete')</button>
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
    <!-- Modal content-->
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
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')
        </button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->

<!-- Modal -->
<div id="changepass" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
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
                <button type="button" id="show-password-button" class="show-password-button" >
                  <i class="fa fa-eye" aria-hidden="true"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button id="password" class="btn btn-info" type="submit">@lang('fleet.change_password')</button>
        </form>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')
        </button>
      </div>
    </div>
  </div>
</div>
</div>
<!-- Modal -->
@endsection

@section('script')
<script type="text/javascript">

  $("#del_btn").on("click",function(){
    var id=$(this).data("submit");
    $("#form_"+id).submit();
  });

  $('#myModal').on('show.bs.modal', function(e) {
    var id = e.relatedTarget.dataset.id;
    $("#del_btn").attr("data-submit",id);
  });

  $('#changepass').on('show.bs.modal', function(e) {
    var id = e.relatedTarget.dataset.id;
    $("#driver_id").val(id);
  });

  $("#changepass_form").on("submit",function(e){
    $.ajax({
      type: "POST",
      url: $(this).attr("action"),
      data: $(this).serialize(),
      success: function(data){
       new PNotify({
            title: 'Success!',
            text: "@lang('fleet.passwordChanged')",
            type: 'info'
        });
      },
      dataType: "html"
    });
    $('#changepass').modal("hide");
    e.preventDefault();
  });
  $(function(){
    
    var table = $('#ajax_data_table').DataTable({
          "language": {
              "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
          },
         processing: true,
         serverSide: true,
         ajax: {
          url: "{{ url('admin/drivers-fetch') }}",
          type: 'POST',
          data:{}
         },
         columns: [
            {data: 'check',name:'check', searchable:false, orderable:false},
            {data: 'id', name: 'users.id'},
            {data: 'driver_image',name:'driver_image', searchable:false, orderable:false},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'is_active', name: 'is_active'},
            {data: 'phone', name: 'phone'},
            // {data: 'vehicle', name: 'vehicle'},
            {data: 'start_date', name: 'start_date'},
            {data: 'action',name:'action',  searchable:false, orderable:false},
             {data : 'assign_admin',name:'assign_admin'},
             {data : 'assigned_admin',name:'assigned_admin'}
        ],
        order: [[1, 'desc']],
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
    $(document).on('change', '.assign-admin', function() {
        var driverId = $(this).data('driver-id');
        var adminId = $(this).val();
    
        $.ajax({
            url: "{{ url('admin/assign-admin') }}",
            method: 'POST',
            data: {
                driver_id: driverId,
                admin_id: adminId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Reload the DataTable
                    $('#ajax_data_table').DataTable().ajax.reload(null, false);
                } else {
                    alert('Failed to assign admin. Please try again.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
  });
  $(function(){
    
    var table = $('#ajax_admin_data_table').DataTable({
          "language": {
              "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
          },
         processing: true,
         serverSide: true,
         ajax: {
          url: "{{ url('admin/drivers-admin-fetch') }}",
          type: 'POST',
          data:{}
         },
         columns: [
            {data: 'check',name:'check', searchable:false, orderable:false},
            {data: 'id', name: 'users.id'},
            {data: 'driver_image',name:'driver_image', searchable:false, orderable:false},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'is_active', name: 'is_active'},
            {data: 'phone', name: 'phone'},
            // {data: 'vehicle', name: 'vehicle'},
            {data: 'start_date', name: 'start_date'},
            {data: 'action',name:'action',  searchable:false, orderable:false}
        ],
        order: [[1, 'desc']],
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

  $('#bulk_delete').on('click',function(){
    // console.log($( "input[name='ids[]']:checked" ).length);
    if($( "input[name='ids[]']:checked" ).length == 0){
      $('#bulk_delete').prop('type','button');
        new PNotify({
            title: 'Failed!',
            text: "@lang('fleet.delete_error')",
            type: 'error'
          });
        $('#bulk_delete').attr('disabled',true);
    }
    if($("input[name='ids[]']:checked").length > 0){
      // var favorite = [];
      $.each($("input[name='ids[]']:checked"), function(){
          // favorite.push($(this).val());
          $("#bulk_hidden").append('<input type=hidden name=ids[] value='+$(this).val()+'>');
      });
      // console.log(favorite);
    }
  });


  $('#chk_all').on('click',function(){
    if(this.checked){
      $('.checkbox').each(function(){
        $('.checkbox').prop("checked",true);
      });
    }else{
      $('.checkbox').each(function(){
        $('.checkbox').prop("checked",false);
      });
      $('#bulk_delete').prop('disabled',true);
    }
  });

    // Checkbox checked
  function checkcheckbox(){
    // Total checkboxes
    var length = $('.checkbox').length;
    // Total checked checkboxes
    var totalchecked = 0;
    $('.checkbox').each(function(){
        if($(this).is(':checked')){
            totalchecked+=1;
        }
    });
    // console.log(length+" "+totalchecked);
    // Checked unchecked checkbox
    if(totalchecked == length){
        $("#chk_all").prop('checked', true);
    }else{
        $('#chk_all').prop('checked', false);
    }
  }
</script>
{{-- show password script --}}
<script>
  $(document).ready(function() {
  $('#show-password-button').click(function() {
    $('#show-password-button').show();
    var passwordField = $('#passwd');
    var fieldType = passwordField.attr('type');
    if (fieldType === 'password') {
      passwordField.attr('type', 'text');
      $(this).attr('title', 'Hide password');
      $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
      passwordField.attr('type', 'password');
      $(this).attr('title', 'Show password');
      $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
    }
  });
});

</script>
{{-- show password script end --}}
@endsection