@extends('layouts.app')
@section('extra_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="{{ route('bookings.index') }}">@lang('menu.bookings')</a></li>
    <li class="breadcrumb-item active">@lang('fleet.new_booking')</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        @lang('fleet.new_booking')
                    </h3>
                </div>

                <div class="card-body">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {!! Form::open(['route' => 'bookings.store', 'method' => 'post']) !!}
                    {!! Form::hidden('user_id', Auth::user()->id) !!}
                    <!-- {!! Form::hidden('status', 0) !!} -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('customer_id', __('Employee ID'), ['class' => 'form-label']) !!}
                                @if (Auth::user()->user_type != 'C')
                                    <a href="#" data-toggle="modal" data-target="#exampleModal">@lang('fleet.new_customer')</a>
                                @endif
                                <select id="customer_id" name="customer_id" class="form-control" required>
                                    <option value="">-</option>
                                    @if (Auth::user()->user_type == 'C')
                                        <option value="{{ Auth::user()->id }}"
                                            data-address="{{ Auth::user()->getMeta('address') }}" data-address2=""
                                            data-id="{{ Auth::user()->id }}" selected>{{ Auth::user()->name }}
                                        </option>
                                    @else
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                data-address="{{ $customer->getMeta('address') }}" data-address2=""
                                                data-id="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                     </div>
                    
                  
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('action', __('Action'), ['class' => 'form-label']) !!}
            {!! Form::select('action', 
                ['' => __('Select')] + ['login' => 'Login', 'logout' => 'Logout'], 
                '', 
                ['class' => 'form-control', 'required', 'style' => 'height:50px', 'id' => 'actionDropdown']
            ) !!}
        </div>
    </div>
</div>

<!-- Datepicker Section (Initially Hidden) -->
<div id="datepickerSection" class="row" style="display: none;">
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('date', __('Select Date'), ['class' => 'form-label']) !!}
            {!! Form::text('date', null, ['class' => 'form-control', 'id' => 'datePicker', 'readonly' => 'readonly', 'required']) !!}
        </div>
    </div>
</div>




    <!-- Timeslot Dropdown (Initially Hidden) -->
    <!-- <div class="row" id="timeslotSection" style="display: none;">
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('timeslot', __('Timeslot'), ['class' => 'form-label']) !!}
            {!! Form::select('timeslot', [], null, [
                'class' => 'form-control', 
                'required', 
                'id' => 'timeslotSelect', 
                'placeholder' => __('Select Timeslot')
            ]) !!}
        </div>
    </div>
</div> -->

<div class="row" id="daysSection" style="display: show;">
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('days_available', __('Select Available Days'), ['class' => 'form-label']) !!}
            {!! Form::select('days_available', [], null, ['class' => 'form-control', 'id' => 'daysDropdown']) !!}
        </div>
    </div>
</div>


<div class="blank"></div>
        <div class="col-md-12">
             {!! Form::submit(__('fleet.save_booking'), ['class' => 'btn btn-success']) !!}
        </div>
             {!! Form::close() !!}
             </div>
        </div>
     </div>
</div>


    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">@lang('fleet.new_customer')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                {!! Form::open(['route' => 'customers.ajax_store', 'method' => 'post', 'id' => 'create_customer_form']) !!}
                <div class="modal-body">
                    <div class="alert alert-danger print-error-msg" style="display:none">
                        <ul></ul>
                    </div>
                    <div class="form-group">
                        {!! Form::label('first_name', __('fleet.firstname'), ['class' => 'form-label']) !!}
                        {!! Form::text('first_name', null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('last_name', __('fleet.lastname'), ['class' => 'form-label']) !!}
                        {!! Form::text('last_name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('gender', __('fleet.gender'), ['class' => 'form-label']) !!}<br>
                        <input type="radio" name="gender" class="flat-red gender" value="1" checked>
                        @lang('fleet.male')<br>

                        <input type="radio" name="gender" class="flat-red gender" value="0"> @lang('fleet.female')
                    </div>

                    <div class="form-group">
                        {!! Form::label('phone', __('fleet.phone'), ['class' => 'form-label']) !!}
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-phone"></i></span>
                            </div>
                            {!! Form::number('phone', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('email', __('fleet.email'), ['class' => 'form-label']) !!}
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                            </div>
                            {!! Form::email('email', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('address', __('fleet.address'), ['class' => 'form-label']) !!}
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-address-book-o"></i></span>
                            </div>
                            {!! Form::textarea('address', null, ['class' => 'form-control', 'size' => '30x2','required']) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
                    <button type="submit" class="btn btn-info">@lang('fleet.save_cust')</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection

@section('script')
    <script>
      var datet = "{{date('Y-m-d H:i:s')}}";
      var getDriverRoute='{{ url("admin/get_driver") }}';
      var getVehicleRoute='{{ url("admin/get_vehicle") }}';
      var prevAddress='{{ url("admin/prev-address") }}';
      var selectDriver="@lang('fleet.selectDriver')";
      var selectCustomer="@lang('fleet.selectCustomer')";
      var selectVehicle="@lang('fleet.selectVehicle')";
      var addCustomer="@lang('fleet.add_customer')";
      var prevAddressLang="@lang('fleet.prev_addr')";
     
      var fleet_email_already_taken="@lang('fleet.email_already_taken')";
    </script>
    <script src="{{asset('assets/js/bookings/create.js')}}"></script>   
     @if (Hyvikk::api('google_api') == '1')
        <script>
            function initMap() {
                $('#pickup_addr').attr("placeholder", "");
                $('#dest_addr').attr("placeholder", "");
                // var input = document.getElementById('searchMapInput');
                var pickup_addr = document.getElementById('pickup_addr');
                new google.maps.places.Autocomplete(pickup_addr);

                var dest_addr = document.getElementById('dest_addr');
                new google.maps.places.Autocomplete(dest_addr);

                // autocomplete.addListener('place_changed', function() {
                //     var place = autocomplete.getPlace();
                //     document.getElementById('pickup_addr').innerHTML = place.formatted_address;
                // });
            }
        </script>


<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-multidatespicker/1.6.6/jquery-ui.multidatespicker.min.js"></script>

<style>
    /* Highlight selected dates while dragging */
    .ui-selected {
        background: #007bff !important;
        color: white !important;
    }
</style>

<script>



    });

    // Ensure MultiDatesPicker Plugin is Loaded
    if ($.fn.multiDatesPicker) {
        $('#datePicker').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0, // Disable past dates
            beforeShowDay: function (date) {
                console.log("BeforeShowDay Triggered"); 
                console.log("Selected Action Inside BeforeShowDay:", selectedAction); 

                let day = date.getDay();
                return (day === 0 || day === 6) ? [false, "", "Weekend Off"] : [true, "", ""];
            },
            onSelect: function (dateText, inst) {
                console.log("onSelect triggered");
                let selectedDate = new Date(dateText);

                // Fix: Move to Monday if selected on weekend
                if (selectedDate.getDay() === 0) {
                    selectedDate.setDate(selectedDate.getDate() + 1);
                } else if (selectedDate.getDay() === 6) {
                    selectedDate.setDate(selectedDate.getDate() + 2);
                }

                let selectedDates = [];
                let count = 0;
                let nextDate = new Date(selectedDate.getTime()); // Clone the date
                let weekdays = { "Monday": [], "Tuesday": [], "Wednesday": [], "Thursday": [], "Friday": [] };

                while (count < 10) {
                    nextDate.setDate(nextDate.getDate() + 1);
                    let dayIndex = nextDate.getDay();
                    let dayName = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"][dayIndex];

                    if (dayIndex !== 0 && dayIndex !== 6) { // Skip weekends
                        let formattedDate = $.datepicker.formatDate('yy-mm-dd', nextDate);
                        selectedDates.push(formattedDate);
                        weekdays[dayName].push(formattedDate);
                        count++;
                    }
                }

                console.log("Dates Grouped by Weekday:", weekdays);

                localStorage.setItem("selectedWeekdayDates", JSON.stringify(weekdays));

                $('#datePicker').multiDatesPicker('resetDates', 'disabled'); // Fix: Correct resetDates
                $('#datePicker').multiDatesPicker('addDates', selectedDates);
            }
        });
    } else {
        console.error("multiDatesPicker plugin not found!");
    }
});

</script>


<button id="fetchDaysButton">Fetch Available Days</button>

    <script>
        $(document).ready(function () {
            // Trigger AJAX request when the button is clicked
            $('#fetchDaysButton').on('click', function () {
                fetchAvailableDays();
            });

            // Function to fetch available days
            function fetchAvailableDays() {
                $.ajax({
                    url: '/get-available-days', // Ensure this matches your route
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        console.log("AJAX Response:", response); // Debugging

                        if (response.success && response.days_available.length > 0) {
                            let options = '<option value="">Select a Day</option>';
                            
                            // Loop through the days and add them as options
                            response.days_available.forEach(day => {
                                options += `<option value="${day}">${day}</option>`;
                            });

                            // Populate the dropdown
                            $('#daysDropdown').html(options);
                            $('#daysSection').show(); // Show the dropdown section
                        } else {
                            alert('No available days found.');
                            $('#daysSection').hide(); // Hide the dropdown section
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", xhr.responseText); // Debugging
                        alert('Error fetching available days.');
                    }
                });
            }
        });
    </script>


        <!-- <script
            src="https://maps.googleapis.com/maps/api/js?key={{ Hyvikk::api('api_key') }}&libraries=places&callback=initMap"
            async defer></script> -->
    @endif
@endsection
