
$('#customer_id').select2({
    placeholder: "@lang('fleet.selectCustomer')"
});
$('#driver_id').select2({
    placeholder: "@lang('fleet.selectDriver')"
});
$('#vehicle_id').select2({
    placeholder: "@lang('fleet.selectVehicle')"
});
$('#pickup').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss',
    sideBySide: true,
    icons: {
        previous: 'fa fa-arrow-left',
        next: 'fa fa-arrow-right',
        up: "fa fa-arrow-up",
        down: "fa fa-arrow-down"
    }
});
$('#dropoff').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss',
    sideBySide: true,
    icons: {
        previous: 'fa fa-arrow-left',
        next: 'fa fa-arrow-right',
        up: "fa fa-arrow-up",
        down: "fa fa-arrow-down"
    }
});

function get_driver(from_date, to_date) {
    $.ajax({
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ url('admin/get_driver') }}",
        data: "req=new&from_date=" + from_date + "&to_date=" + to_date,
        success: function(data2) {
            $("#driver_id").empty();
            $("#driver_id").select2({
                placeholder: "@lang('fleet.selectDriver')",
                data: data2.data
            });
        },
        dataType: "json"
    });
}

function get_vehicle(from_date, to_date) {
    $.ajax({
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ url('admin/get_vehicle') }}",
        data: "req=new&from_date=" + from_date + "&to_date=" + to_date,
        success: function(data2) {
            $("#vehicle_id").empty();
            $("#vehicle_id").select2({
                placeholder: 'Select Vehicle',
                data: data2.data
            });
        },
        dataType: "json"
    });
}

$(document).ready(function() {
    $("#d_pickup").on("change", function() {
        var address = $(this).find(":selected").data("address");
        $("#pickup_addr").val(address);
    });

    $("#d_dest").on("change", function() {
        var address = $(this).find(":selected").data("address");
        $("#dest_addr").val(address);
    });

    $("#pickup").on("dp.change", function(e) {
        var to_date = $('#dropoff').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss");
        var from_date = e.date.format("YYYY-MM-DD HH:mm:ss");
        get_driver(from_date, to_date);
        // get_vehicle(from_date,to_date);
        $('#dropoff').data("DateTimePicker").minDate(e.date);
    });

    $("#dropoff").on("dp.change", function(e) {
        $('#pickup').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss")
        var from_date = $('#pickup').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss");
        var to_date = e.date.format("YYYY-MM-DD HH:mm:ss");

        get_driver(from_date, to_date);
        // get_vehicle(from_date,to_date);
    });

    $("#vehicle_id").on("change", function() {
        var driver = $(this).find(":selected").data("driver");
        $("#driver_id").val(driver).change();
    });
});

// //Flat red color scheme for iCheck
// $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
//     checkboxClass: 'icheckbox_flat-green',
//     radioClass: 'iradio_flat-green'
// })

$("#vehicle_id").on('change', function() {
    $("#mileage").val($("#vehicle_id option:selected").data('base-mileage'));
    $("#waiting_time").val("0");
    $("#total").val($("#vehicle_id option:selected").data('base-fare'));
    $("#day").val("1");
    var tax_charges = (Number('{{ $tax_percent }}') * Number($('#total').val())) / 100;
    $('#total_tax_charge_rs').val(tax_charges);
    $('#tax_total').val(Number($('#total').val()) + Number(tax_charges));
});

$(".sum").change(function() {
    // alert($("#base_km_1").val());
    // alert($('.vtype').data('base_km_1'));
    // console.log($("#type").val());
    var day = $("#day").find(":selected").val();
    if (day == 1) {
        var base_km = $("#vehicle_id option:selected").data('base_km_1');
        var base_fare = $("#vehicle_id option:selected").data('base_fare_1');
        var wait_time = $("#vehicle_id option:selected").data('wait_time_1');
        var std_fare = $("#vehicle_id option:selected").data('std_fare_1');
        if (Number($("#mileage").val()) <= Number(base_km)) {
            var total = Number(base_fare) + (Number($("#waiting_time").val()) * Number(wait_time));
        } else {
            var sum = Number($("#mileage").val() - base_km) * Number(std_fare);
            var total = Number(base_fare) + Number(sum) + (Number($("#waiting_time").val()) * Number(
                wait_time));
        }
    }

    if (day == 2) {
        var base_km = $("#vehicle_id option:selected").data('base_km_2');
        var base_fare = $("#vehicle_id option:selected").data('base_fare_2');
        var wait_time = $("#vehicle_id option:selected").data('wait_time_2');
        var std_fare = $("#vehicle_id option:selected").data('std_fare_2');
        if (Number($("#mileage").val()) <= Number(base_km)) {
            var total = Number(base_fare) + (Number($("#waiting_time").val()) * Number(wait_time));
        } else {
            var sum = Number($("#mileage").val() - base_km) * Number(std_fare);
            var total = Number(base_fare) + Number(sum) + (Number($("#waiting_time").val()) * Number(
                wait_time));
        }
    }

    if (day == 3) {
        var base_km = $("#vehicle_id option:selected").data('base_km_3');
        var base_fare = $("#vehicle_id option:selected").data('base_fare_3');
        var wait_time = $("#vehicle_id option:selected").data('wait_time_3');
        var std_fare = $("#vehicle_id option:selected").data('std_fare_3');
        if (Number($("#mileage").val()) <= Number(base_km)) {
            var total = Number(base_fare) + (Number($("#waiting_time").val()) * Number(wait_time));
        } else {
            var sum = Number($("#mileage").val() - base_km) * Number(std_fare);
            var total = Number(base_fare) + Number(sum) + (Number($("#waiting_time").val()) * Number(
                wait_time));
        }
    }
    $("#total").val(total);
    var tax_charges = (Number('{{ $tax_percent }}') * Number($('#total').val())) / 100;
    $('#total_tax_charge_rs').val(tax_charges);
    $('#tax_total').val(Number($('#total').val()) + Number(tax_charges));

});

$('#total').on('change', function() {
    var tax_charges = (Number('{{ $tax_percent }}') * Number($('#total').val())) / 100;
    $('#total_tax_charge_rs').val(tax_charges);
    $('#tax_total').val(Number($('#total').val()) + Number(tax_charges));
});
