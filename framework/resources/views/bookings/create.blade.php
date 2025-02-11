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
                                {!! Form::label('customer_id', __('fleet.selectCustomer'), ['class' => 'form-label']) !!}
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

<!-- <div class="row" id="dateSection" style="display: none;">
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('date', __('Select Dates'), ['class' => 'form-label']) !!}
            {!! Form::text('date', null, ['class' => 'form-control', 'id' => 'datePicker', 'autocomplete' => 'off']) !!}
        </div>
    </div>
</div>           -->

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
    <script src="{{asset('assets/js/bookings/create.js')}}"></script>    @if (Hyvikk::api('google_api') == '1')
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

$(document).ready(function () {
    // Initially hide the datepicker section
    $('#datepickerSection').hide();

    // When the 'action' dropdown changes
    $('#actionDropdown').change(function () {
        var selectedAction = $(this).val();
        console.log("Selected Action: ", selectedAction); // Debugging

        // Hide the datepicker section by default
        $('#datepickerSection').hide();

        // Show the datepicker section only if 'login' or 'logout' is selected
        if (selectedAction === 'login' || selectedAction === 'logout') {
            $('#datepickerSection').show();
        }
    });
    $(document).ready(function () {
    // Ensure MultiDatesPicker Plugin is Loaded
    if ($.fn.multiDatesPicker) {
        $('#datePicker').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0, // Disable past dates
            beforeShowDay: function (date) {
                let day = date.getDay();
                return (day === 0 || day === 6) ? [false, "", "Weekend Off"] : [true, "", ""];
            },
            onSelect: function (dateText, inst) {
                console.log("onSelect triggered");

                let selectedDate = new Date(dateText);
                console.log("Selected Date:", selectedDate);

                // Fix: Move to Monday if selected on weekend
                if (selectedDate.getDay() === 0) {
                    selectedDate.setDate(selectedDate.getDate() + 1);
                } else if (selectedDate.getDay() === 6) {
                    selectedDate.setDate(selectedDate.getDate() + 2);
                }

                let selectedDates = [];
                let count = 0;
                let nextDate = new Date(selectedDate.getTime()); // Fix: Clone the date
                let weekdays = { "Monday": [], "Tuesday": [], "Wednesday": [], "Thursday": [], "Friday": [] };

                while (count < 30) {
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

                console.log(" Selected Dates:", selectedDates);
                console.log(" Dates Grouped by Weekday:", weekdays);

                localStorage.setItem("selectedWeekdayDates", JSON.stringify(weekdays));

                $('#datePicker').multiDatesPicker('resetDates', 'disabled'); // Fix: Correct resetDates
                $('#datePicker').multiDatesPicker('addDates', selectedDates);
            }
        });
    } else {
        console.error("multiDatesPicker plugin not found!");
    }
});
})
</script>


<script>
public function getAvailableDays(Request $request)
{
	$days_available = []; // Initialize an empty array

	if ($request->ajax()) {
		Log::info('days_avail');
		$days_available = $request->timeslot;// Get the selected timeslot from the request
		Log::info($days_available);

		// Fetch the days_available from the database, which will be a comma-separated string of days (e.g., "Sunday,Monday,Wednesday")
		$days_available = Timeslot::where('shift', $timeslot)->pluck('days_available')->first();
		
		// If days are stored as a comma-separated string, convert to an array
		$days_available = explode(',' , $days_available);
		
		// Get the current month and year (you can adjust the month if needed)
		$currentMonth = Carbon::now()->month;
		$currentYear = Carbon::now()->year;

		// Generate the specific dates for the available days in the current month
		$dates = [];
		
		// Loop through the days available (e.g., Sunday, Monday, Wednesday)
		foreach ($days_available as $day) {
			// Get the first date of the current month
			$firstDayOfMonth = Carbon::create($currentYear, $currentMonth, 1);
			
			// Find the first occurrence of this day in the current month
			$date = $firstDayOfMonth->copy()->next($day); // Carbon's next() will get the next occurrence of that day of the week
			
			// Loop to get all occurrences of the specified day in the month
			while ($date->month === $currentMonth) {
				$dates[] = $date->toDateString(); // Push the date in YYYY-MM-DD format
				$date->addWeek(); // Move to the next occurrence of that day
			}
		}

		// Return the available dates as a JSON response
		return response()->json(['days_available' => $dates]);
	}

	return response()->json(['error' => 'Invalid request']);
}

</script>





        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ Hyvikk::api('api_key') }}&libraries=places&callback=initMap"
            async defer></script>
    @endif
@endsection
