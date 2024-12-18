$('input:radio[name=radio1]').change(function() {
    if ($("input[name='radio1']:checked").val() == 'book_later') {
        $("#datepicker").attr('required', 'required');
        $("#datepicker").attr('readonly', false);
        $("#timepicker").attr('required', 'required');
        $("#timepicker").attr('readonly', false);
    }
});

// if(google_api == '1'){
//     function initMap() {
//         $('#pickup_address').attr("placeholder", "");
//         $('#dropoff_address').attr("placeholder", "");
//         // var input = document.getElementById('searchMapInput');
//         var pickup_addr = document.getElementById('pickup_address');
//         new google.maps.places.Autocomplete(pickup_addr);

//         var dest_addr = document.getElementById('dropoff_address');
//         new google.maps.places.Autocomplete(dest_addr);

//         // autocomplete.addListener('place_changed', function() {
//         //     var place = autocomplete.getPlace();
//         //     document.getElementById('pickup_addr').innerHTML = place.formatted_address;
//         // });
//     }
// }

$("#datepicker").flatpickr({
    disableMobile: "true"
});
$("#timepicker").flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    disableMobile: "true",

});
$('#timepicker,#timepicker_label').on(' click ', function() {
    //    alert('test');

    $('#timepicker_label').addClass('label_top');
    $('#timepicker').addClass('active');
    // console.log('done'); 
});

window.setTimeout(function() {
    $(".alert-danger").alert('close');
}, 8000);

if ($(location).attr('href').split('/')[4] == "#register") {
    $('#login-modal').addClass('active');
}