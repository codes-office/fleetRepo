
  $(document).ready(function() {
    $('#date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });
  });
  $("#del_btn").on("click",function(){
    var id=$(this).data("submit");
    $("#book_"+id).submit();
  });

  $("#del_btn2").on("click",function(){
    var id=$(this).data("submit");
    $("#reject_"+id).submit();
  });

  $('#myModal').on('show.bs.modal', function(e) {
    var id = e.relatedTarget.dataset.id;
    $("#del_btn").attr("data-submit",id);
  });

  $('#rejectModal').on('show.bs.modal', function(e) {
    var id = e.relatedTarget.dataset.id;
    $("#del_btn2").attr("data-submit",id);
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

  $(function(){
    
    var table = $('#ajax_data_table').DataTable({      
      "language": {
          "url": dataTableUrl,
      },
      processing: true,
      serverSide: true,
      ajax: {
        url: bookingQuotationFetch,
        type: 'POST',
        data:{}
      },
      columns: [
        {data: 'check',   name: 'check', searchable:false, orderable:false},
        {data: 'customer',   name: 'customer.name'},
        {data: 'vehicle', name: 'vehicle'},
        {data: 'pickup_addr',    name: 'pickup_addr'},
        {data: 'dest_addr',    name: 'dest_addr'},
        {name: 'pickup',data: {_: 'pickup.display',sort: 'pickup.timestamp'}},
        {name: 'dropoff',data: {_: 'dropoff.display',sort: 'dropoff.timestamp'}},
        {data: 'travellers',  name: 'travellers'},
        {data: 'tax_total',  name: 'tax_total'},
        {data: 'status',  name: 'status'},
        {data: 'action',  name: 'action', searchable:false, orderable:false}
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

  $('#bulk_delete').on('click',function(){
    // console.log($( "input[name='ids[]']:checked" ).length);
    if($( "input[name='ids[]']:checked" ).length == 0){
      $('#bulk_delete').prop('type','button');
        new PNotify({
            title: 'Failed!',
            text: deleteError,
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
