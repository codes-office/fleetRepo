<?php
Auth::routes();
Route::namespace ('Admin')->group(function () {
    // Route::get('export-events', 'HomeController@export_calendar');

    Route::get('test-login', 'Testcontroller@login');

    Route::get('/migrate', function () {
        \Artisan::call('migrate');
    });

    Route::post("logout", 'HomeController@logout')->name('logout');

    Route::get("/clear_cache", function () {
        Artisan::call('optimize:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
    });

    // Route::get('add-permission', function () {
    //     $drivers = App\Model\User::where('user_type', 'D')->get();
    //     foreach ($drivers as $driver) {
    //         $d = App\Model\User::find($driver->id);
    //         $d->givePermissionTo(['VehicleInspection add']);
    //         //$d->givePermissionTo(['Transactions list','Transactions add','Transactions edit','Transactions delete']);
    //     }
    // });
// dd('test');
    Route::get("/", 'HomeController@index')->middleware(['lang_check', 'auth']);
    Route::group(['middleware' => ['lang_check', 'auth', 'officeadmin', 'IsInstalled']], function () {

       
        Route::get('/vehicles-track/{id?}','TrackerController@vehicles_track');
        Route::get('/track/{id?}','TrackerController@track');
        Route::get('/traccar-settings','TrackerController@traccar_settings')->name('traccar.settings');
        Route::post('/traccar-settings','TrackerController@traccar_settings_store')->name('traccar_settings.store');


        Route::get('get-driver-index', 'DriversController@get_driver_index');
        Route::post('transactions-fetch', 'BookingsController@transactions_fetch_data');
        Route::get('transactions', 'BookingsController@transactions');

        Route::get('get-models/{id}', 'VehiclesController@get_models');

        // Route::post('vehicle-color-fetch', 'VehicleColorsController@fetch_data');
        // Route::resource('vehicle-color', 'VehicleColorsController');
        // Route::post('delete-vehicle-color', 'VehicleColorsController@bulk_delete');

        // Route::post('vehicle-make-fetch', 'VehicleMakeController@fetch_data');
        // Route::resource('vehicle-make', 'VehicleMakeController');
        // Route::post('delete-vehicle-make', 'VehicleMakeController@bulk_delete');

        // Route::post('vehicle-model-fetch', 'VehicleModelController@fetch_data');
        // Route::resource('vehicle-model', 'VehicleModelController');
        // Route::post('delete-vehicle-model', 'VehicleModelController@bulk_delete');

        Route::get('firebase', 'FirebaseController@index');

        Route::resource('roles', 'UserAccessController');

        Route::post('/users-fetch', 'UsersController@fetch_data');
        Route::post('assign-admin-user', 'UsersController@assignAdmin'); // assing the admin 
        Route::post('/mltusers-fetch', 'UsersController@mlt_fetch_data');
        Route::post('/assign-admin', 'DriversController@assignAdmin');
        Route::resource('/users', 'UsersController');
        
        Route::get('mlt', 'UsersController@mltindex');
// test 1
        Route::get('twilio-settings', 'TwilioController@index');
        Route::post('twilio-settings', 'TwilioController@update');

        Route::post('reject-quotation', 'BookingQuotationController@reject');

        Route::get('timeslots/create', 'TimeslotController@create')->name('timeslots.create');
        Route::post('timeslots/store', 'TimeslotController@store')->name('timeslots.store');
        Route::get('timeslots', 'TimeslotController@index')->name('timeslots.index');
        Route::get('timeslots/{id}/edit', 'TimeslotController@edit')->name('timeslots.edit');
        Route::delete('timeslots/{id}', 'TimeslotController@destroy')->name('timeslots.destroy');
        Route::put('timeslots/{id}', 'TimeslotController@update')->name('timeslots.update');
        

        Route::get('fetch-data', 'Testcontroller@fetch');
        // Route::get('test', function () {
        //     return view('geocode');
        // });
        Route::post('clear-database', 'SettingsController@clear_database');
        Route::post('cancel-booking', 'BookingsController@cancel_booking');
        Route::resource('team', 'TeamController');
        Route::resource('company-services', 'CompanyServicesController');
        Route::get('parts-used/{id}', 'WorkOrdersController@parts_used');
        Route::get('remove-part/{id}', 'WorkOrdersController@remove_part');
        Route::post('add-stock', 'PartsController@add_stock');
        Route::post('booking-quotation-fetch', 'BookingQuotationController@fetch_data');
        Route::resource('booking-quotation', 'BookingQuotationController');
        Route::post('add-booking', 'BookingQuotationController@add_booking');
        Route::get('booking-quotation/invoice/{id}', 'BookingQuotationController@invoice');
        Route::get('print-quote/{id}', 'BookingQuotationController@print');
        Route::get('booking-quotation/approve/{id}', 'BookingQuotationController@approve');
        Route::post('import-vehicles', 'VehiclesController@importVehicles');
        Route::post('import-drivers', 'DriversController@importDrivers');
        Route::post('import-income', 'IncomeCategories@importIncome');
        Route::post('import-expense', 'ExpenseCategories@importExpense');
        Route::post('import-customers', 'CustomersController@importCutomers');
        Route::post('import-vendors', 'VendorController@importVendors');
        Route::get('frontend-settings', 'SettingsController@frontend');
        Route::post('frontend-settings', 'SettingsController@store_frontend');
        Route::resource('testimonials', 'TestimonialController');
        // routes for bulk delete action
        Route::post('delete-team', 'TeamController@bulk_delete');
        Route::post('delete-company-services', 'CompanyServicesController@bulk_delete');
        Route::post('delete-testimonials', 'TestimonialController@bulk_delete');
        Route::post('delete-reasons', 'ReasonController@bulk_delete');
        Route::post('delete-income', 'IncomeController@bulk_delete');
        Route::post('delete-expense', 'ExpenseController@bulk_delete');
        Route::post('delete-reminders', 'ServiceReminderController@bulk_delete');
        Route::post('delete-service-items', 'ServiceItemsController@bulk_delete');
        Route::post('delete-parts', 'PartsController@bulk_delete');
        Route::post('delete-work-orders', 'WorkOrdersController@bulk_delete');
        Route::post('delete-parts-category', 'PartsCategoryController@bulk_delete');
        Route::post('delete-customer', 'CustomersController@bulk_delete');
        Route::post('delete-users', 'UsersController@bulk_delete');
        Route::post('delete-drivers', 'DriversController@bulk_delete');
        Route::post('delete-vehicles', 'VehiclesController@bulk_delete');
        // Route::post('delete-fuel', 'FuelController@bulk_delete');
        Route::post('delete-vendors', 'VendorController@bulk_delete');
        Route::post('delete-bookings', 'BookingsController@bulk_delete');
        Route::post('delete-quotes', 'BookingQuotationController@bulk_delete');
        Route::post('delete-vehicle-types', 'VehicleTypeController@bulk_delete');
        Route::post('delete-vehicle-groups', 'VehicleGroupController@bulk_delete');
        Route::post('delete-vehicle-reviews', 'VehiclesController@bulk_delete_reviews');

        Route::get('reports/income', 'ReportsController@income');
        Route::post('reports/income', 'ReportsController@income_post');
        Route::post('print-income', 'ReportsController@income_print');

        Route::get('reports/expense', 'ReportsController@expense');
        Route::post('reports/expense', 'ReportsController@expense_post');
        Route::post('print-expense', 'ReportsController@expense_print');

        Route::get('work_order/logs', 'WorkOrdersController@logs');
        Route::resource('parts-category', 'PartsCategoryController');

        Route::post('driver-logs-fetch', 'VehiclesController@driver_logs_fetch_data');
        Route::get('driver-logs', 'VehiclesController@driver_logs');
        Route::post('/vehicle-types-fetch', 'VehicleTypeController@fetch_data');
        Route::resource('/vehicle-types', 'VehicleTypeController');

        Route::post('vehicle-reviews-fetch', 'VehiclesController@vehicle_review_fetch_data');
        Route::get('vehicle-reviews', 'VehiclesController@vehicle_review_index')->name('vehicle_reviews');
        Route::get('print-vehicle-review/{id}', 'VehiclesController@print_vehicle_review');
        Route::get('view-vehicle-review/{id}', 'VehiclesController@view_vehicle_review');
        Route::get('vehicle-reviews-create', 'VehiclesController@vehicle_review');
        Route::get('vehicle-review/{id}/edit', 'VehiclesController@review_edit');
        Route::post('vehicle-review-update', 'VehiclesController@update_vehicle_review');
        Route::post('store-vehicle-review', 'VehiclesController@store_vehicle_review');
        Route::delete('delete-vehicle-review/{id}', 'VehiclesController@destroy_vehicle_review');
        //maps
        Route::get('single-driver/{id}', 'DriversController@single_driver');
        Route::get('driver-maps/', 'DriversController@driver_maps');
        Route::get('markers/', 'DriversController@markers');
        Route::get('track-driver/{id}', 'DriversController@track_driver');

        Route::post('print-users-report', 'ReportsController@print_users');
        Route::post('print-customer-report', 'ReportsController@print_customer');
        Route::get('print-vendor-report', 'ReportsController@print_vendor');
        Route::post('print-driver-report', 'ReportsController@print_driver');
        Route::post('print-yearly-report', 'ReportsController@print_yearly');
        Route::post('print-fuel-report', 'ReportsController@print_fuel');
        Route::post('print-booking-report', 'ReportsController@print_booking');
        Route::post('print-deliquent-report', 'ReportsController@print_deliquent');
        Route::post('print-monthly-report', 'ReportsController@print_monthly');
        // Route::get('print-bookings', 'BookingsController@print_bookings');
        Route::get('reviews', 'ReviewRatings@index');
        Route::get('messages', 'ContactUs@index');
        Route::get('api-settings', 'SettingsController@api_settings');
        Route::post('api-settings', 'SettingsController@store_settings');

        // Route::get('fb', 'SettingsController@fb_create')->name('fb');
        Route::post('firebase-settings', 'SettingsController@firebase');
        Route::get('fare-settings', 'SettingsController@fare_settings');
        Route::post('fare-settings', 'SettingsController@store_fareSettings');
        Route::post('store-key', 'SettingsController@store_key');
        Route::get('test-key', 'SettingsController@test_key');
        Route::post('store-api', 'SettingsController@store_api');
        Route::resource('service-reminder', 'ServiceReminderController');
        Route::resource('service-item', 'ServiceItemsController');
        Route::post('/vehicle-group-fetch', 'VehicleGroupController@fetch_data');
        Route::resource('/vehicle_group', 'VehicleGroupController');
        Route::post('/income_records', 'Income@income_records');
        Route::post('/expense_records', 'ExpenseController@expense_records');
        Route::post('/store_insurance', 'VehiclesController@store_insurance');
        Route::get('vehicle/event/{id}', 'VehiclesController@view_event');
        Route::post('assignDriver', 'VehiclesController@assign_driver');

        Route::post('/work-orders-fetch', 'WorkOrdersController@fetch_data');
        Route::resource('/work_order', 'WorkOrdersController');
        Route::post('/vendors-fetch', 'VendorController@fetch_data');
        Route::resource('/vendors', 'VendorController');
        Route::post('drivers-fetch', 'DriversController@fetch_data');
        Route::resource('/drivers', 'DriversController');       
        Route::resource('/parts', 'PartsController');
        Route::post('/vehicles-fetch', 'VehiclesController@fetch_data');
        Route::resource('/vehicles', 'VehiclesController');
        Route::get("/vehicles/enable/{id}", 'VehiclesController@enable');
        Route::get("/vehicles/disable/{id}", 'VehiclesController@disable');
        Route::post('/bookings-fetch', 'BookingsController@fetch_data');
        Route::post('/driver-bookings-fetch', 'DriversController@fetch_bookings_data');
        Route::resource('/bookings', 'BookingsController');
        Route::post('/prev-address', 'BookingsController@prev_address');
        Route::get('print/{id}', 'BookingsController@print');
        Route::resource('/acquisition', 'AcquisitionController');
        Route::resource('/income', 'IncomeController');
        Route::resource('/settings', 'SettingsController');
        Route::post('/customers-fetch', 'CustomersController@fetch_data');
        Route::resource('/customers', 'CustomersController');
        Route::post('assign-admin-customer', 'CustomersController@assignAdmin'); // assing the admin 
        Route::post('customer-admin-fetch', 'CustomersController@fetch_admin_data');
        Route::resource('/expense', 'ExpenseController');
        Route::resource('/expensecategories', 'ExpenseCategories');
        Route::resource('/incomecategories', 'IncomeCategories');
        Route::get('/bookings/complete/{id}', 'BookingsController@complete');
        Route::get('/bookings/receipt/{id}', 'BookingsController@receipt');
        Route::get('/bookings/payment/{id}', 'BookingsController@payment');
        Route::get("/reports/monthly", "ReportsController@monthly")->name("reports.monthly");
        Route::get("/reports/vendors", "ReportsController@vendors")->name("reports.vendors");
        Route::post("/reports/vendors", "ReportsController@vendors_post")->name("reports.vendors");
        Route::get("reports/drivers", "ReportsController@drivers")->name("reports.drivers");
        Route::post("reports/drivers", "ReportsController@drivers_post")->name("reports.drivers");
        Route::get("reports/customers", "ReportsController@customers")->name("reports.customers");
        Route::post("reports/customers", "ReportsController@customers_post")->name("reports.customers");
        Route::get("/reports/booking", "ReportsController@booking")->name("reports.booking");
        Route::get("/reports/delinquent", "ReportsController@delinquent")->name("reports.delinquent");
        Route::get("/reports/users", "ReportsController@users")->name("reports.users");
        Route::post("/reports/users", "ReportsController@users_post")->name("reports.users");
        Route::get('/calendar', 'BookingsController@calendar');
        Route::get('/calendar/event/{id}', 'BookingsController@calendar_event');
        Route::get("/drivers/enable/{id}", 'DriversController@enable');
        Route::get("/drivers/disable/{id}", 'DriversController@disable');
        Route::get("/reports/vehicle", "ReportsController@vehicle")->name("reports.vehicle");
        Route::post("/reports/booking", "ReportsController@booking_post")->name("reports.booking");
        Route::post("/reports/fuel", "ReportsController@fuel_post")->name("reports.fuel");
        Route::get("/reports/fuel", "ReportsController@fuel")->name("reports.fuel");
        Route::get("/reports/yearly", "ReportsController@yearly")->name("reports.yearly");
        Route::post("/reports/yearly", "ReportsController@yearly_post")->name("reports.yearly");
        Route::get("/reports/payments", "ReportsController@drivers_payments")->name("reports.payments");
        Route::post("/reports/payments", "ReportsController@drivers_payments_post")->name("reports.payments");

        Route::post('/customer/ajax_save', 'CustomersController@ajax_store')->name('customers.ajax_store');
        Route::get("/bookings_calendar", 'BookingsController@calendar_view')->name("bookings.calendar");
        Route::get('/calendar/event/calendar/{id}', 'BookingsController@calendar_event');
        Route::get('/calendar/event/service/{id}', 'BookingsController@service_view');
        Route::get('/calendar', 'BookingsController@calendar');
        Route::post('/get_driver', 'BookingsController@get_driver');
        Route::post('/get_vehicle', 'BookingsController@get_vehicle');
        Route::post('/bookings/complete', 'BookingsController@complete_post')->name("bookings.complete");
        Route::get('/bookings/complete', 'BookingsController@complete_post')->name("bookings.complete");

        Route::post("/reports/monthly", "ReportsController@monthly_post")->name("reports.monthly");
        Route::post("/reports/booking", "ReportsController@booking_post")->name("reports.booking");
        Route::post("/reports/delinquent", "ReportsController@delinquent_post")->name("reports.delinquent");
        Route::get("/send-email", "SettingsController@send_email");
        Route::post("/email-settings", "SettingsController@email_settings");
        Route::post('enable-mail', 'SettingsController@enable_mail');
        Route::get("/set-email", "SettingsController@set_email");
        Route::post("/set-content/{type}", "SettingsController@set_content");

        Route::get('ajax-api-store/{api}', 'SettingsController@ajax_api_store');

        Route::get('payment-settings', 'SettingsController@payment_settings');
        Route::post('payment-settings', 'SettingsController@payment_settings_post');
    });

    Route::group(['middleware' => ['lang_check', 'auth']], function () {

        // chat routes:
        Route::get('chat', 'MessagesController@index')->name('chat.index');
        Route::get('chat-settings', 'MessageSettingsController@index')->name('chat_settings.index');
        Route::post('chat-settings', 'MessageSettingsController@store')->name('chat_settings.store');
        Route::get('load-latest-messages', 'MessagesController@getLoadLatestMessages');
        Route::post('send', 'MessagesController@postSendMessage');

        Route::post('delete-notes', 'NotesController@bulk_delete');
        Route::get('changepassword/{id}', 'UtilityController@change');
        Route::post('changepassword/{id}', 'UtilityController@change_post');
        Route::get('/change-details/{id}', 'UtilityController@changepass')->name("changepass");
        Route::post('/change-details/{id}', 'UtilityController@changepassword')->name("changepass");
        Route::post('/change_password', 'UtilityController@password_change');
        Route::get('/vehicle_notification/{type}', 'NotificationController@vehicle_notification');
        Route::resource('/notes', 'NotesController');
        Route::get('driver_notification/{type}', 'NotificationController@driver_notification');
        Route::get('/reminder/{type}', 'NotificationController@service_reminder');
        Route::get('/my_bookings', 'DriversController@my_bookings')->name('my_bookings');

//report work order
        Route::get("/reports/work-order", "ReportsController@work_order")->name("reports.work_order");
        Route::post("/reports/work-order", "ReportsController@work_order_post")->name("reports.work_order");
        Route::post('print-workOrder-report', 'ReportsController@print_workOrder_report');
//mechanics  CRUD
        Route::resource('mechanic', 'MechanicController');
        Route::post("/delete-mechanics", "MechanicController@bulk_delete");
// vehicle inspection
        Route::get('vehicle-inspection', 'VehiclesController@vehicle_inspection_index')->name('vehicle_inspection');
        Route::get('print-vehicle-inspection/{id}', 'VehiclesController@print_vehicle_inspection');
        Route::get('vehicle-inspection-create', 'VehiclesController@vehicle_inspection_create');
        Route::post('store-vehicle-review', 'VehiclesController@store_vehicle_review');
        Route::get('view-vehicle-inspection/{id}', 'VehiclesController@view_vehicle_inspection');
// fuel detail
        Route::resource('/fuel', 'FuelController');
//vehicle Expense
        Route::resource('/expense', 'ExpenseController');
        Route::post('/expense_records', 'ExpenseController@expense_records');
        Route::post('delete-expense', 'ExpenseController@bulk_delete');
//vehicle income
        Route::resource('/income', 'IncomeController');
        Route::post('delete-income', 'IncomeController@bulk_delete');
        Route::post('/income_records', 'IncomeController@income_records');
// driver reports
        Route::get("/driver-reports/yearly", "DriversController@yearly")->name("dreports.yearly");
        Route::post("/driver-reports/yearly", "DriversController@yearly_post")->name("dreports.yearly");
        Route::get("/driver-reports/monthly", "DriversController@monthly")->name("dreports.monthly");
        Route::post("/driver-reports/monthly", "DriversController@monthly_post")->name("dreports.monthly");
        Route::get('/addresses', 'AddressController@index');

        Route::resource('/cancel-reason', 'ReasonController');
        Route::post('delete-fuel', 'FuelController@bulk_delete')->middleware('IsInstalled');
    });

});
