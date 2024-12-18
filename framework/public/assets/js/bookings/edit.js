
  $(".add_udf").click(function () {
    // alert($('#udf').val());
    var field = $('#udf1').val();
    if(field == "" || field == null){
      alert('Enter field name');
    }

    else{
      $(".blank").append('<div class="row"><div class="col-md-8">  <div class="form-group"> <label class="form-label">'+ field.toUpperCase() +'</label> <input type="text" name="udf['+ field +']" class="form-control" placeholder="Enter '+ field +'" required></div></div><div class="col-md-4"> <div class="form-group" style="margin-top: 30px"><button class="btn btn-danger" type="button" onclick="this.parentElement.parentElement.parentElement.remove();">Remove</button> </div></div></div>');
      $('#udf1').val("");
    }
  });

 // {{-- it is for page load because driver should be change accoriding to time --}}

  // $(function(){
  //  var from_date= $('#pickup').val();
  //  var to_date= $('#dropoff').val();
  // //  alert(from_date);
  //   var id=$("input:hidden[name=id]").val();
  //   $.ajaxSetup({
  //     headers: {
  //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //     }
  //   });

  //   $.ajax({
  //     type: "POST",
  //     url: getDriverRoute,
  //     data: "req=edit&id="+id+"&from_date="+from_date+"&to_date="+to_date,
  //     success: function(data2){
  //       $("#driver_id").empty();
  //       $("#driver_id").select2({placeholder: selectDriver,data:data2.data});
  //       // if(data2.show_error=="yes"){
  //       //   // alert("test");
  //       // $("#msg_driver").removeClass("hide").fadeIn(1000);
  //       // } else {
  //       // $("#msg_driver").addClass("hide").fadeIn(1000);
  //       // }
  //     },
  //     error: function(data){
  //     var errors = $.parseJSON(data.responseText);

  //       $(".print-error-msg").find("ul").html('');
  //     $(".print-error-msg").css('display','block');
  //     $.each( errors, function( key, value ) {
  //       $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
  //     });

  //     },
  //     dataType: "json"
  //   });
  // });


  $('#customer_id').select2({placeholder: selectCustomer});
  $('#driver_id').select2({placeholder: selectDriver});
  $('#vehicle_id').select2({placeholder:selectVehicle});

  function get_driver(from_date,to_date){

    var id=$("input:hidden[name=id]").val();
    $.ajax({
      type: "POST",
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      url: getDriverRoute,
      data: "req=edit&id="+id+"&from_date="+from_date+"&to_date="+to_date,
      success: function(data2){
        $("#driver_id").empty();
        $("#driver_id").select2({placeholder: selectDriver,data:data2.data});
        // if(data2.show_error=="yes"){
        //   // alert("test");
        // $("#msg_driver").removeClass("hide").fadeIn(1000);
        // } else {
        // $("#msg_driver").addClass("hide").fadeIn(1000);
        // }
      },
      error: function(data){
      var errors = $.parseJSON(data.responseText);

      $(".print-error-msg").find("ul").html('');
      $(".print-error-msg").css('display','block');
      $.each( errors, function( key, value ) {
        $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
      });

      },
      dataType: "json"
    });
  }

  function get_vehicle(from_date,to_date){
    var id=$("input:hidden[name=id]").val();

    $.ajax({
      type: "POST",
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      url:getVehicleRoute,
      data: "req=edit&id="+id+"&from_date="+from_date+"&to_date="+to_date,
      success: function(data2){
        $("#vehicle_id").empty();
        $("#vehicle_id").select2({placeholder: selectVehicle,data:data2.data});
        // if(data2.show_error=="yes"){

        // $("#msg_vehicle").removeClass("hide").fadeIn(1000);
        // } else {
        // $("#msg_vehicle").addClass("hide").fadeIn(1000);
        // }
      },
      error: function(data){
        var errors = $.parseJSON(data.responseText);
        $(".print-error-msg").find("ul").html('');
        $(".print-error-msg").css('display','block');
        $.each( errors, function( key, value ) {
        $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
        });
      },
      dataType: "json"
    });
  }

  $(document).ready(function() {
    $('#pickup').datetimepicker({format: 'YYYY-MM-DD HH:mm:ss',sideBySide: true,icons: {
              previous: 'fa fa-arrow-left',
              next: 'fa fa-arrow-right',
              up: "fa fa-arrow-up",
              down: "fa fa-arrow-down"
    },
 
  });
    // getting pickup value so minimum date should be pickup date in dropoff.
    var today =$('#pickup').val();
    $('#dropoff').datetimepicker({format: 'YYYY-MM-DD HH:mm:ss',sideBySide: true,icons: {
              previous: 'fa fa-arrow-left',
              next: 'fa fa-arrow-right',
              up: "fa fa-arrow-up",
              down: "fa fa-arrow-down"
    },
  minDate:today,
  useCurrent:false,
  
  });

    $("#pickup").on("dp.change", function (e) {
      if($('#dropoff').val() == null || $('#dropoff').val() == ""){
        var to_date=e.date.format("YYYY-MM-DD HH:mm:ss");
      }
      else{
        var to_date=$('#dropoff').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss");
      }
      var from_date=e.date.format("YYYY-MM-DD HH:mm:ss");

      get_driver(from_date,to_date);
      get_vehicle(from_date,to_date);

      $('#dropoff').data("DateTimePicker").minDate(e.date);
    });

    $("#dropoff").on("dp.change", function (e) {
      $('#pickup').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss")
      var from_date=$('#pickup').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss");
      var to_date=e.date.format("YYYY-MM-DD HH:mm:ss");
      get_driver(from_date,to_date);
      get_vehicle(from_date,to_date);
    });
  });
