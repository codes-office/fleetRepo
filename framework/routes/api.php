<?php

use Illuminate\Http\Request;
Route::namespace ('Api')->middleware(['throttle'])->group(function () {
	Route::post('send-otp', 'UsersApi@send_otp');
	Route::post('verify-otp', 'UsersApi@verify_otp');
	Route::post('/login', 'Auth@login');
	Route::post('/user-registration', 'UsersApi@user_registration');
	Route::post('/user-login', 'UsersApi@user_login'); //without social media connected
	Route::post('/user-login-sm', 'UsersApi@login_with_sm'); //login through social media
	Route::post('/forgot-password', 'UsersApi@forgot_password');
	Route::post('/get-settings', 'DriversApi@get_settings');
	Route::get('/codes', 'DriversApi@get_code');
	Route::post('vendor/vendor-login-mobile', 'VendorApiController@mobile_login');  //use
	Route::get('vendor/get-month-years', 'VendorBookingsController@get_month_years'); //use
    Route::get('vendor/get-years', 'VendorApiController@years');//use
	Route::post('vendor/cities', 'VendorApiController@cities'); //use 
});


Route::namespace ('Api')->middleware(['throttle', 'auth:api'])->group(function () {
	Route::post('map-details', 'UsersApi@map_api');
	Route::post('/edit-user-profile', 'UsersApi@edit_profile');
	Route::post('/change-password', 'UsersApi@change_password');
	Route::post('/message-us', 'UsersApi@message_us');
	Route::post('/book-now', 'UsersApi@book_now');
	Route::post('/book-later', 'UsersApi@book_later');
	Route::post('/update-destination', 'UsersApi@update_destination');
	Route::post('/review', 'UsersApi@review');
	Route::post('/ride-history', 'UsersApi@ride_history');
	Route::post('/user-single-ride', 'UsersApi@user_single_ride_info');
	Route::post('/get-reviews', 'UsersApi@get_reviews');
	Route::post('vendor/user-logout', 'UsersApi@user_logout');
	Route::post('/change-availability', 'DriversApi@change_availability');
	Route::post('/ride-requests', 'DriversApi@ride_requests');
	Route::post('/single-ride-request', 'DriversApi@single_ride_request');
	Route::post('/accept-ride-request', 'DriversApi@accept_ride_request');
	Route::post('/cancel-ride-request', 'DriversApi@cancel_ride_request');
	Route::post('/reject-ride-request', 'DriversApi@reject_ride_request');
	Route::post('/driver-rides', 'DriversApi@driver_rides');
	Route::post('/single-ride-info', 'DriversApi@single_ride_info');
	Route::post('/start-ride', 'DriversApi@start_ride');
	Route::post('/destination-reached', 'DriversApi@destination_reached');
	Route::post('/confirm-payment', 'DriversApi@confirm_payment');
	Route::post('/active-drivers', 'DriversApi@active_drivers');
	Route::post('update-fcm-token', 'UsersApi@update_fcm');

	
});

Route::middleware('auth:api')->post('/user', function (Request $request) {
	return $request->user();
});

Route::prefix('vendor')->namespace ('Api')->middleware(['throttle', 'auth:api'])->group(function () {
	
	Route::post('dashboard-counts', 'VendorApiController@counts');//use
	Route::post('tax-calculation', 'VendorApiController@tax_calculation'); //use
	//vehicle
	Route::post('vehicles', 'VendorApiController@vehicles');//use
	Route::post('delete-vehicle', 'VendorApiController@delete_vehicle');
	Route::post('add-vehicle', 'VendorApiController@add_vehicle');
	Route::post('edit-vehicle', 'VendorApiController@edit_vehicle');
	Route::post('vehicle-types', 'VendorApiController@types');//use

	Route::post('upload-documents', 'VendorApiController@vehicle_document'); //use
    Route::post('download-pdf', 'VendorApiController@generate_pdf'); //use

    Route::post('update-image', 'VendorApiController@update_vendor_image'); //use

    Route::post('upload-driver-documents', 'VendorApiController@driver_document'); //use

	
    Route::post('get-vendor', 'VendorApiController@get_vendor');//use
    // Vendor's APIs
    Route::post('dashboard-income-chart', 'VendorApiController@income_chart');//use

	//customers  APIs
    Route::post('customers', 'VendorApiController@customers');//use
    Route::post('add-customer','VendorApiController@StoreCustomer');//use
    Route::post('edit-customer','VendorApiController@EditCustomer');
    Route::post('delete-customer','VendorApiController@DeleteCustomer');//use

    // bookings
    Route::post('generate-invoice','VendorBookingsController@generate_invoice'); //use
    Route::post('cancel-booking','VendorBookingsController@cancel_booking'); //use
    Route::post('add-booking', 'VendorBookingsController@add_booking'); //use 
    Route::post('bookings', 'VendorBookingsController@bookings'); //use
    Route::post('delete-booking', 'VendorBookingsController@delete_booking');
    Route::post('edit-booking', 'VendorBookingsController@update_booking'); //use
    Route::post('transactions', 'VendorBookingsController@transactions'); //use

	Route::post('edit-profile', 'VendorApiController@edit_profile');//use vendor profile edit

	//Drivers
	Route::post('manage-drivers', 'VendorApiController@drivers'); //use
	Route::post('add-driver', 'VendorApiController@add_driver'); //use
	Route::post('edit-driver', 'VendorApiController@edit_driver'); //use
	Route::post('enable-disable-driver', 'VendorApiController@enable_disable_driver'); //use
	Route::post('delete-driver', 'VendorApiController@delete_driver'); //use

	Route::post('groups', 'VendorApiController@groups'); //use

	//income APIs
    Route::post('income', 'VendorApiController@income_list'); //use
    Route::post('income-types', 'VendorApiController@income_types'); //use
    Route::post('add-income', 'VendorApiController@add_income'); //use
    Route::post('edit-income', 'VendorApiController@edit_income'); //use
    Route::post('delete-income', 'VendorApiController@delete_income'); //use

    //expense APIs
    Route::post('expense', 'VendorApiController@expense_list'); //use
    Route::post('expense-types', 'VendorApiController@expense_types');//use
    Route::post('add-expense', 'VendorApiController@add_expense'); //use
    Route::post('delete-expense', 'VendorApiController@delete_expense'); //use
    Route::post('update-expense','VendorApiController@update_expense');//use

	 //Fuel
	 Route::post('fuel','FuelApiController@fuelList');//use
	 Route::post('add-fuel','FuelApiController@storeFuel'); //use
	 Route::post('delete-fuel','FuelApiController@deleteFuel'); //use
	 Route::post('update-fuel','FuelApiController@updateFuel'); //use 
	 Route::post('edit-fuel','FuelApiController@editFuel');//use
 
	 //service reminders APIs
	 Route::post('service-reminder','ServiceReminderApiController@serviceReminderList');//use
	 Route::post('add-service-reminder','ServiceReminderApiController@storeServiceReminder');//use
	 Route::post('delete-service-reminder','ServiceReminderApiController@deleteServiceReminder');//use
	 Route::post('service-items','ServiceReminderApiController@serviceItemList');//use
	 Route::post('update-service-reminder','ServiceReminderApiController@updateServiceReminder');//use
 
	 //vendors API for fuel module
	 Route::post('get-vendors','VendorApiController@getVendors');//use

	 //vehicle make model and colors
	 Route::Post('vehicle-model','VendorApiController@vehicle_model');//use
	 Route::post('vehicle-make','VendorApiController@vehicle_make');//use
	 Route::post('vehicle-colors','VendorApiController@vehicle_color');//use
	 Route::post('enable-disable-vehicle','VendorApiController@enable_disable_vehicle');//use

	  //driver payments api
	  Route::post('driver-payment-report-list','VendorApiController@driver_payment_list');
	  Route::post('add-driver-payment-report','VendorApiController@add_driver_payment_report');

	  //settings api
	 Route::post('settings','VendorApiController@settings');
});