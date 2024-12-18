<table class="table" id="ajax_table">
  <thead class="thead-inverse">
    <tr>
      <th>
        @if($today->count() > 0)
          <input type="checkbox" id="chk_all">
        @endif
      </th>
      <th>@lang('fleet.make')</th>
      <th>@lang('fleet.model')</th>
      <th>@lang('fleet.licensePlate')</th>
      <th>@lang('fleet.incomeType')</th>
      <th>@lang('fleet.date')</th>
      <th>@lang('fleet.amount')</th>
      <th>@lang('fleet.mileage') ({{Hyvikk::get('dis_format')}})</th>
      <th>@lang('fleet.delete')</th>
    </tr>
  </thead>
  <tbody>
  @foreach($today as $row)
     <tr>
      <td>
        <input type="checkbox" name="ids[]" value="{{ $row->id }}" class="checkbox" id="chk{{ $row->id }}" onclick='checkcheckbox();'>
      </td>
      <td>{{$row->vehicle->make_name}}</td>
      <td>{{$row->vehicle->model_name}}</td>
      <td>{{$row->vehicle->license_plate}}</td>
      <td>{{$row->category->name}}</td>
      <td>{{date(Hyvikk::get('date_format'),strtotime($row->date))}}</td>
      <td>{{Hyvikk::get('currency')}} {{$row->amount}}</td>
      <td>{{$row->mileage}}</td>
      <td>
        {!! Form::open(['url' => 'admin/income/'.$row->id,'method'=>'DELETE','class'=>'form-horizontal','id'=>'form_'.$row->id]) !!}
          {!! Form::hidden("id",$row->id) !!}
        <button type="button" class="btn btn-danger delete" data-id="{{$row->id}}" title="@lang('fleet.delete')"><span class="fa fa-times" aria-hidden="true"></span></button>
        {!! Form::close() !!}
      </td>
    </tr>
  @endforeach
  </tbody>
  <tfoot>
    <tr>
      <th>
        @if($today->count() > 0)
          <button class="btn btn-danger" id="bulk_delete" data-toggle="modal" data-target="#bulkModal" disabled title="@lang('fleet.delete')" ><i class="fa fa-trash"></i></button>
        @endif
      </th>
      <th>@lang('fleet.make')</th>
      <th>@lang('fleet.model')</th>
      <th>@lang('fleet.licensePlate')</th>
      <th>@lang('fleet.incomeType')</th>
      <th>@lang('fleet.date')</th>
      <th>@lang('fleet.amount')</th>
      <th>@lang('fleet.mileage') ({{Hyvikk::get('dis_format')}})</th>
      <th>@lang('fleet.delete')</th>
    </tr>
  </tfoot>
</table>


<script type="text/javascript">
  $("#total_today").empty();
  $("#total_today").html("{{ Hyvikk::get('currency').' '. $total }}");
  $('#ajax_table tfoot th').each( function () {
    if($(this).index() != 0){
      var title = $(this).text();
      $(this).html( '<input type="text" placeholder="'+title+'" />' );
    }
  });

  var ajax_table = $('#ajax_table').DataTable({
    "language": {
        "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
     },

    columnDefs: [ { orderable: false, targets: [0] } ],
     // individual column search
    "initComplete": function() {
        ajax_table.columns().every(function () {
          var that = this;
          $('input', this.footer()).on('keyup change', function () {
              that.search(this.value).draw();
          });
        });
      }
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
</script>