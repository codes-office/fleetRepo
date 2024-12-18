@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item active">@lang('menu.manageParts')</li>
@endsection
@section('extra_css')
<link rel="stylesheet" type="text/css"  href="https://cdn.datatables.net/buttons/1.4.0/css/buttons.dataTables.min.css" />
<style type="text/css">
  .checkbox, #chk_all{
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
        <h3 class="card-title">@lang('menu.manageParts')
          @can('Parts add')<a href="{{ route('parts.create')}}" class="btn btn-success">@lang('fleet.addParts')</a>@endcan
        </h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table" id="data_table2">
          <thead class="thead-inverse">
            <tr>
              <th>
                @if($data->count() > 0)
                  <input type="checkbox" id="chk_all">
                @endif
              </th>
              <th>@lang('fleet.image')</th>
              <th>@lang('fleet.title')</th>
              <th>@lang('fleet.parts_category')</th>
              <th>@lang('fleet.part_model')</th>
              <th>@lang('fleet.status')</th>
              <th>@lang('fleet.availability')</th>
              <th>@lang('fleet.unit_cost')</th>
              <th>@lang('fleet.qty_on_hand')</th>
              <th>@lang('fleet.vendor')</th>
              <th>@lang('fleet.manufacturer')</th>
              <th>@lang('fleet.action')</th>
            </tr>
          </thead>
          <tbody>
            @foreach($data as $row)
            <tr>
              <td>
                <input type="checkbox" name="ids[]" value="{{ $row->id }}" class="checkbox" id="chk{{ $row->id }}" onclick='checkcheckbox();'>
              </td>
              <td>
                @if($row->image != null)
                  <img src="{{asset('uploads/'.$row->image)}}" height="50px" width="50px">
                @else
                  <img src="{{asset('assets/images/no-image.png')}}" height="50px" width="50px">
                @endif
              </td>
              <td> {{$row->title}}
              </td>
              <td>{{$row->category->name}}</td>
              <td>{{$row->model}}</td>
              <td>{{$row->status}}</td>
              <td>
                @if($row->availability == 1)
                  @lang('fleet.available')
                @else
                  @lang('fleet.not_available')
                @endif
              </td>
              <td>{{Hyvikk::get('currency')." ". $row->unit_cost}}</td>
              <td>{{$row->stock}}</td>
              <td>{{$row->vendor->name}}
              <br>
              ({{$row->vendor->type}})
              <br>
              {{$row->vendor->phone}}
              </td>
              <td>{{$row->manufacturer}}</td>
              <td>
              <div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                  <span class="fa fa-gear"></span>
                  <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu custom" role="menu">
                  <a class="dropdown-item" data-id="{{$row->id}}" data-toggle="modal" data-target="#stockModal" data-title="{{ $row->title }}"> <span aria-hidden="true" class="fa fa-plus-square" style="color: green"></span> @lang('fleet.addStock')</a>
                  @can('Parts edit')<a class="dropdown-item" href="{{ url("admin/parts/".$row->id."/edit")}}"> <span aria-hidden="true" class="fa fa-edit" style="color: #f0ad4e;"></span> @lang('fleet.edit')</a>@endcan
                  @can('Parts delete')<a class="dropdown-item" data-id="{{$row->id}}" data-toggle="modal" data-target="#myModal"> <span aria-hidden="true" class="fa fa-trash" style="color: #dd4b39"></span> @lang('fleet.delete')</a>@endcan
                </div>
              </div>
              {!! Form::open(['url' => 'admin/parts/'.$row->id,'method'=>'DELETE','class'=>'form-horizontal','id'=>'form_'.$row->id]) !!}
              {!! Form::hidden("id",$row->id) !!}
              {!! Form::close() !!}
              </td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th>
                @if($data->count() > 0)
                @can('Parts delete')<button class="btn btn-danger" id="bulk_delete" data-toggle="modal" data-target="#bulkModal" disabled title="@lang('fleet.delete')" ><i class="fa fa-trash"></i></button>@endcan
                @endif
              </th>
              <th>@lang('fleet.image')</th>
              <th>@lang('fleet.title')</th>
              <th>@lang('fleet.parts_category')</th>
              <th>@lang('fleet.part_model')</th>
              <th>@lang('fleet.status')</th>
              <th>@lang('fleet.availability')</th>
              <th>@lang('fleet.unit_cost')</th>
              <th>@lang('fleet.qty_on_hand')</th>
              <th>@lang('fleet.vendor')</th>
              <th>@lang('fleet.manufacturer')</th>
              <th>@lang('fleet.action')</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div id="stockModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><span id="part_title"></span> : @lang('fleet.addStock')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      {!! Form::open(['method'=>'POST','url'=>'admin/add-stock']) !!}
      {!! Form::hidden('part_id',null,['id'=>'part_id']) !!}
      <div class="modal-body">
        <div class="form-group">
          {!! Form::label('stock', __('fleet.qty'), ['class' => 'form-label']) !!}
          {!! Form::number('stock', 1,['class' => 'form-control','required','min'=>1]) !!}
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-info" type="submit" data-submit="">@lang('fleet.addStock')</button>
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
        {!! Form::open(['url'=>'admin/delete-parts','method'=>'POST','id'=>'form_delete']) !!}
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
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
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

  $('input[type="checkbox"]').on('click',function(){
    $('#bulk_delete').removeAttr('disabled');
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

  $('#stockModal').on('show.bs.modal', function(e) {
    var id = e.relatedTarget.dataset.id;
    $("#part_id").val(id);
    $("#part_title").html(e.relatedTarget.dataset.title);
  });
  
  $('#data_table2 tfoot th').each( function () {
    if($(this).index() != 0 && $(this).index() != $('#data_table2 tfoot th').length - 1)
    {
      var title = $(this).text();
      $(this).html( '<input type="text" placeholder="'+title+'" />' );
    }
  });
  $(function(){
  var table2 = $('#data_table2').DataTable({
    dom: 'Bfrtip',
    buttons: [
          {
            extend: 'print',
            text: '<i class="fa fa-print"></i> {{__("fleet.print")}}',
            exportOptions: {
              columns: ([1,2,3,4,5,6,7,8,9,10]),
            },
            customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '10pt' )
                        .prepend(
                            '<h3>{{__("menu.manageParts")}}</h3>'
                        );
                    $(win.document.body).find( 'table' )
                        .addClass( 'table-bordered' );
                    // $(win.document.body).find( 'td' ).css( 'font-size', '10pt' );

            }
          },
          {
            extend: 'excel',
            text: '<i class="fa fa-file-excel-o"></i> @lang('fleet.Export')',
            exportOptions: {
              columns: ([1,2,3,4,5,6,7,8,9,10]),
            },
            customize: function (xlsx) {
              var sheet = xlsx.xl.worksheets['sheet1.xml'];
              var col = $('col', sheet);
              $(col[8]).attr('width', 70);
            }
          },
          {
            extend: 'pdf',
            text: '<i class="fa fa-file-pdf-o"></i> @lang('fleet.pdf')',
            exportOptions: {
              columns: ([1,2,3,4,5,6,7,8,9,10]),
            },
          }
    ],
    "language": {
             "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
          },
    columnDefs: [ { orderable: false, targets: [0] } ],
    // individual column search
   "initComplete": function() {
            table2.columns().every(function () {
              var that = this;
              $('input', this.footer()).on('keyup change', function () {
                  that.search(this.value).draw();
              });
            });
          },
          
  });
});
</script>
@endsection