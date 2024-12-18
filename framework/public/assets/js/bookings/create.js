
// $(function() {
//     var from_date = $('#pickup').val();
//     var to_date = $('#dropoff').val();
//     var yes = true;
//     //  alert(from_date);
//     var id = $("input:hidden[name=id]").val();
//     $.ajaxSetup({
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         }
//     });

//     $.ajax({
//         type: "POST",
//         url: getDriverRoute,
//         data: "req=" + yes + "&from_date=" + from_date + "&to_date=" + to_date,
//         success: function(data2) {
//             $("#driver_id").empty();
//             $("#driver_id").select2({
//                 placeholder:selectDriver,
//                 data: [{
//                     id: '',
//                     text: ''
//                 }].concat(data2.data)
//             });
//             // if(data2.show_error=="yes"){
//             //   // alert("test");
//             // $("#msg_driver").removeClass("hide").fadeIn(1000);
//             // } else {
//             // $("#msg_driver").addClass("hide").fadeIn(1000);
//             // }
//         },
//         error: function(data) {
//             var errors = $.parseJSON(data.responseText);

//             $(".print-error-msg").find("ul").html('');
//             $(".print-error-msg").css('display', 'block');
//             $.each(errors, function(key, value) {
//                 $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
//             });

//         },
//         dataType: "json"
//     });
// });
var today = $('#pickup').val();
// var today=new Date();
// console.log(today);
$('#customer_id').select2({
    placeholder: selectCustomer
});
$('#driver_id').select2({
    placeholder: selectDriver
});
$('#vehicle_id').select2({
    placeholder: selectVehicle
});
$('#pickup').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss',
    sideBySide: true,
    icons: {
        previous: 'fa fa-arrow-left',
        next: 'fa fa-arrow-right',
        up: "fa fa-arrow-up",
        down: "fa fa-arrow-down"
    },
    // minDate: today
});

$('#dropoff').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss',
    sideBySide: true,
    icons: {
        previous: 'fa fa-arrow-left',
        next: 'fa fa-arrow-right',
        up: "fa fa-arrow-up",
        down: "fa fa-arrow-down"
    },
    minDate: today,

});

$("#create_customer_form").on("submit", function(e) {
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").hide();
    var form = $(this);
    $.ajax({
        type: "POST",
        url: form.attr("action"),
        data: form.serialize(),
        success: function(data) {
            var customers = $.parseJSON(data);
            if (customers.error === 'true') {
                $(".print-error-msg").find("ul").html('');
                $(".print-error-msg").css('display', 'block');
                $.each(customers.messages, function(key, value) {
                    $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
                });
                // new PNotify({
                //     title: 'Failed!',
                //     text: fleet_email_already_taken,
                //     type: 'error'
                // });
            } else {
                form.find("input, textarea").val('');
                $('#customer_id').empty();
                $.each(customers, function(key, value) {
                    $('#customer_id').append($('<option>', {
                        value: value.id,
                        text: value.text
                    }));
                });
                $('#exampleModal').modal('hide');

                new PNotify({
                    title: 'Success!',
                    text: addCustomer,
                    type: 'success'
                });
            }
        },
        error: function(data) {
            var errors = $.parseJSON(data.responseText);
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display', 'block');
            $.each(errors, function(key, value) {
                $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
            });
        },
        dataType: "html"
    });
    e.preventDefault();
});

function get_driver(from_date, to_date) {
    $.ajax({
        type: "POST",
        // headers: {
        //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        // },
        url: getDriverRoute,
        data: "req=new&from_date=" + from_date + "&to_date=" + to_date,
        success: function(data2) {
            $("#driver_id").empty();
            $("#driver_id").select2({
                placeholder: selectDriver,
                data: [{
                    id: '',
                    text: ''
                }].concat(data2.data)
            });
        },
        dataType: "json"
    });
}

function get_vehicle(from_date, to_date) {
    $.ajax({
        type: "POST",
        // headers: {
        //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        // },
        url: getVehicleRoute,
        data: "req=new&from_date=" + from_date + "&to_date=" + to_date,
        success: function(data2) {
            $("#vehicle_id").empty();
            $("#vehicle_id").select2({
                placeholder: selectVehicle,
                data: data2.data
            });
        },
        dataType: "json"
    });
}

function prev_address(id) {
    $.ajax({
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        url: prevAddress,
        data: "id=" + id,
        success: function(data) {
            $("#pickup_addr").val(data.pickup_addr);
            $("#dest_addr").val(data.dest_addr);
            if (data.pickup_addr != "") {
                new PNotify({
                    title: 'Success!',
                    text: prevAddressLang,
                    type: 'success'
                });
            }
        },
        dataType: "json"
    });
}

$(document).ready(function() {
    $("#customer_id").on("change", function() {
        var id = $(this).find(":selected").data("id");
        prev_address(id);
    });

    $("#d_pickup").on("change", function() {
        var address = $(this).find(":selected").data("address");
        $("#pickup_addr").val(address);
    });

    $("#d_dest").on("change", function() {
        var address = $(this).find(":selected").data("address");
        $("#dest_addr").val(address);
    });

    $("#pickup").on("dp.change", function(e) {
        $('#dropoff').data("DateTimePicker").minDate(e.date);
        var to_date = $('#dropoff').data("DateTimePicker").date();
        if(to_date!=null){
            to_date = to_date.format("YYYY-MM-DD HH:mm:ss");
        }
        else{
            to_date = $('#pickup').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss");
        }
        var from_date = $('#pickup').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss");
        get_driver(from_date, to_date);
        get_vehicle(from_date, to_date);
    });

    $("#dropoff").on("dp.change", function(e) {
        $('#pickup').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss")
        var from_date = $('#pickup').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss");
        var to_date = e.date.format("YYYY-MM-DD HH:mm:ss");

        get_driver(from_date, to_date);
        get_vehicle(from_date, to_date);
        // console.log("testing");
    });

    // $("#vehicle_id").on("change",function(){
    //   var driver = $(this).find(":selected").data("driver");
    //   $("#driver_id").val(driver).change();
    // });
});
$(".add_udf").click(function() {
    // alert($('#udf').val());
    var field = $('#udf1').val();
    if (field == "" || field == null) {
        alert('Enter field name');
    } else {
        $(".blank").append(
            '<div class="row"><div class="col-md-8">  <div class="form-group"> <label class="form-label">' +
            field.toUpperCase() + '</label> <input type="text" name="udf[' + field +
            ']" class="form-control" placeholder="Enter ' + field +
            '" required></div></div><div class="col-md-4"> <div class="form-group" style="margin-top: 30px"><button class="btn btn-danger" type="button" onclick="this.parentElement.parentElement.parentElement.remove();">Remove</button> </div></div></div>'
            );
        $('#udf1').val("");
    }
});

//Flat red color scheme for iCheck
// $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
//     checkboxClass: 'icheckbox_flat-green',
//     radioClass: 'iradio_flat-green'
// })
