<?php

use Illuminate\Http\Request;

Route::namespace ('Backend')->middleware(['throttle'])->group(function () {
    Route::get('/', function () {
        return redirect(url('spa/'));
    });
    Route::post('login', 'BackendApiController@login');

    Route::get('csrf', 'BackendApiController@csrf');
    Route::get('logout', 'BackendApiController@logout');
    // Route::get('export', 'ReportsApiController@export'); // only for test
});

Route::namespace ('Backend')->middleware(['throttle', 'auth:backend', 'updatepassporttoken'])->group(function () {
    Route::get('check-Auth', 'BackendApiController@auth_check');

    Route::get('vehicle-expenses/{id}', 'ExpenseApiController@vehicle_expenses');

    // import expense-income categories
    Route::post('import-expense-categories', 'ExpenseCategoriesApiController@import_records')->middleware('backendpermission:S');
    Route::post('import-income-categories', 'IncomeCategoriesApiController@import_records')->middleware('backendpermission:S');

    Route::post('customer/add-booking', 'BookingsApiController@store_by_customer')->middleware('backendpermission:C');
    Route::post('customer/update-booking', 'BookingsApiController@update_by_customer')->middleware('backendpermission:C');
    Route::get('languages', 'BackendApiController@lang_dropdown');

    Route::get('get-vehicles', 'VehicleInspectionApiController@get_vehicles');
    Route::get('dashboard/{year}', 'BackendApiController@dashboard');

    Route::get('service-reminder-notifications', 'NotificationApiController@service_reminder')->middleware('backendpermission:S');
    Route::get('driver-license-notifications', 'NotificationApiController@driver_license')->middleware('backendpermission:S');
    Route::get('vehicle-license-notifications', 'NotificationApiController@vehicle_license')->middleware('backendpermission:S');
    Route::get('vehicle-insurance-notifications', 'NotificationApiController@vehicle_insurance')->middleware('backendpermission:S');
    Route::get('vehicle-registration-notifications', 'NotificationApiController@vehicle_registration')->middleware('backendpermission:S');
    Route::get('notification-count', 'NotificationApiController@counts')->middleware('backendpermission:S');

    Route::get('vendor-list', 'VendorsApiController@vendor_list');

    // vehicle inspection APIs
    Route::post('delete-vehicle-inspections', 'VehicleInspectionApiController@bulk_delete')->middleware('backendpermission:1');
    Route::post('delete-vehicle-inspection', 'VehicleInspectionApiController@delete')->middleware('backendpermission:1');
    Route::post('edit-vehicle-inspection', 'VehicleInspectionApiController@update')->middleware('backendpermission:1');
    Route::post('add-vehicle-inspection', 'VehicleInspectionApiController@store')->middleware('backendpermission:1');
    Route::get('vehicle-inspection', 'VehicleInspectionApiController@inspections')->middleware('backendpermission:1');

    // import APIs
    Route::post('import-vendors', 'VendorsApiController@import_records')->middleware('backendpermission:6');
    Route::post('import-vehicles', 'VehiclesApiController@import_records')->middleware('backendpermission:1');
    Route::post('import-customers', 'CustomersApiController@import_records')->middleware('backendpermission:0');
    Route::post('import-drivers', 'DriversApiController@import_records')->middleware('backendpermission:0');

    // update profile API
    Route::post('update-profile', 'BackendApiController@update_profile');
    Route::post('update-profile-photo', 'BackendApiController@update_profile_photo');

    // driver profile API
    Route::post('driver/yearly-report', 'DriversApiController@yearly_report')->middleware('backendpermission:D');
    Route::post('driver/monthly-report', 'DriversApiController@monthly_report')->middleware('backendpermission:D');
    Route::get('driver/report-dropdown', 'DriversApiController@driver_reports_dropdown')->middleware('backendpermission:D');
    Route::get('driver/home', 'DriversApiController@profile')->middleware('backendpermission:D');
    Route::get('driver/my-bookings', 'DriversApiController@my_bookings')->middleware('backendpermission:D');

    // customer profile API
    Route::get('customer/addresses', 'CustomersApiController@addresses')->middleware('backendpermission:C');
    Route::post('customer/add-address', 'CustomersApiController@add_address')->middleware('backendpermission:C');
    Route::post('customer/edit-address', 'CustomersApiController@edit_address')->middleware('backendpermission:C');
    Route::post('customer/delete-address', 'CustomersApiController@delete_address')->middleware('backendpermission:C');
    Route::get('customer/home', 'CustomersApiController@home')->middleware('backendpermission:C');

    // vehicle types API
    Route::post('upload-vehicle-type-icon/{id}', 'VehicleTypesApiController@upload_documents')->middleware('backendpermission:1');
    Route::post('delete-vehicle-types', 'VehicleTypesApiController@bulk_delete')->middleware('backendpermission:1');
    Route::post('delete-vehicle-type', 'VehicleTypesApiController@delete')->middleware('backendpermission:1');
    Route::post('edit-vehicle-type', 'VehicleTypesApiController@update')->middleware('backendpermission:1');
    Route::post('add-vehicle-type', 'VehicleTypesApiController@store')->middleware('backendpermission:1');
    Route::get('vehicle-types', 'VehicleTypesApiController@types')->middleware('backendpermission:1');

    // reports API
    Route::get('report-dropdown', 'ReportsApiController@reports_dropdown');
    Route::post('yearly-report', 'ReportsApiController@yearly')->middleware('backendpermission:4');
    Route::post('vendor-report', 'ReportsApiController@vendor')->middleware('backendpermission:4');
    Route::post('customer-report', 'ReportsApiController@customer')->middleware('backendpermission:4');
    Route::post('driver-report', 'ReportsApiController@driver')->middleware('backendpermission:4');
    Route::post('fuel-report', 'ReportsApiController@fuel')->middleware('backendpermission:4');
    Route::post('user-report', 'ReportsApiController@user')->middleware('backendpermission:4');
    Route::post('booking-report', 'ReportsApiController@booking')->middleware('backendpermission:4');
    Route::post('monthly-report', 'ReportsApiController@monthly')->middleware('backendpermission:4');
    Route::post('delinquent-report', 'ReportsApiController@delinquent')->middleware('backendpermission:4');
    Route::post('income-report', 'ReportsApiController@income')->middleware('backendpermission:4');
    Route::post('expense-report', 'ReportsApiController@expense')->middleware('backendpermission:4');

    // fare settings
    Route::post('update-fare-settings', 'FareSettingsApiController@store_fare_settings')->middleware('backendpermission:S');
    Route::get('fare-settings', 'FareSettingsApiController@get_fare_settings')->middleware('backendpermission:S');

    // API settings API
    Route::post('store-api-key', 'ApiSettingsController@store_api')->middleware('backendpermission:S');
    Route::get('test-key', 'ApiSettingsController@test_key');
    Route::post('add-server-key', 'ApiSettingsController@store_server_key')->middleware('backendpermission:S');
    Route::get('fb', 'ApiSettingsController@fb_create')->name('firebase');
    Route::post('firebase-settings', 'ApiSettingsController@firebase_settings')->middleware('backendpermission:S');
    Route::post('edit-api-settings', 'ApiSettingsController@update_api_setting')->middleware('backendpermission:S');
    Route::get('api-settings', 'ApiSettingsController@get_api_settings')->middleware('backendpermission:S');

    // company services API
    Route::post('upload-service-image/{id}', 'CompanyServicesApiController@upload_documents')->middleware('backendpermission:S');

    Route::get('company-services', 'CompanyServicesApiController@services')->middleware('backendpermission:S');
    Route::post('add-company-service', 'CompanyServicesApiController@store')->middleware('backendpermission:S');
    Route::post('edit-company-service', 'CompanyServicesApiController@update')->middleware('backendpermission:S');
    Route::post('delete-company-service', 'CompanyServicesApiController@delete')->middleware('backendpermission:S');
    Route::post('delete-company-services', 'CompanyServicesApiController@bulk_delete')->middleware('backendpermission:S');

    // frontend settings API
    Route::post('edit-frontend-settings', 'SettingsApiController@update_front_settings')->middleware('backendpermission:S');
    Route::get('frontend-settings', 'SettingsApiController@get_front_settings')->middleware('backendpermission:S');

    // income categories API !doc
    Route::get('income-categories', 'IncomeCategoriesApiController@categories')->middleware('backendpermission:S');
    Route::post('add-income-category', 'IncomeCategoriesApiController@store')->middleware('backendpermission:S');
    Route::post('edit-income-category', 'IncomeCategoriesApiController@update')->middleware('backendpermission:S');
    Route::post('delete-income-category', 'IncomeCategoriesApiController@delete')->middleware('backendpermission:S');

    // expense categories API
    Route::get('expense-categories', 'ExpenseCategoriesApiController@categories')->middleware('backendpermission:S');
    Route::post('add-expense-category', 'ExpenseCategoriesApiController@store')->middleware('backendpermission:S');
    Route::post('edit-expense-category', 'ExpenseCategoriesApiController@update')->middleware('backendpermission:S');
    Route::post('delete-expense-category', 'ExpenseCategoriesApiController@delete')->middleware('backendpermission:S');

    // email settings API
    Route::post('set-email-content', 'EmailSettingsApiController@set_email_content')->middleware('backendpermission:S');
    Route::get('email-content', 'EmailSettingsApiController@get_email_content')->middleware('backendpermission:S');
    Route::post('update-email-notifications', 'EmailSettingsApiController@save_email_settings')->middleware('backendpermission:S');
    Route::post('update-email-setting', 'EmailSettingsApiController@enable_disable')->middleware('backendpermission:S');
    Route::get('email-notifications', 'EmailSettingsApiController@get_email_settings')->middleware('backendpermission:S');

    // cancel reasons API
    Route::get('cancellation-reasons', 'CancellationReasonsApiController@reasons')->middleware('backendpermission:S');
    Route::post('add-cancellation-reason', 'CancellationReasonsApiController@store')->middleware('backendpermission:S');
    Route::post('edit-cancellation-reason', 'CancellationReasonsApiController@update')->middleware('backendpermission:S');
    Route::post('delete-cancellation-reason', 'CancellationReasonsApiController@delete')->middleware('backendpermission:S');
    Route::post('delete-cancellation-reasons', 'CancellationReasonsApiController@bulk_delete')->middleware('backendpermission:S');

    // general settings API
    Route::post('upload-logo-images', 'SettingsApiController@upload_documents')->middleware('backendpermission:S');

    Route::post('edit-general-settings', 'SettingsApiController@update')->middleware('backendpermission:S');
    Route::post('clear-database', 'SettingsApiController@clear_database')->middleware('backendpermission:S');
    Route::get('general-settings', 'SettingsApiController@get_general_settings')->middleware('backendpermission:S');

    // work order API
    Route::get('remove-part/{id}', 'WorkOrdersApiController@remove_part')->middleware('backendpermission:7');
    Route::post('edit-work-order', 'WorkOrdersApiController@update')->middleware('backendpermission:7');
    Route::post('add-work-order', 'WorkOrdersApiController@store')->middleware('backendpermission:7');
    Route::post('delete-work-order', 'WorkOrdersApiController@delete')->middleware('backendpermission:7');
    Route::get('parts-used/{id}', 'WorkOrdersApiController@parts_used')->middleware('backendpermission:7');
    Route::get('work-order-logs', 'WorkOrdersApiController@logs')->middleware('backendpermission:7');
    Route::get('work-order-dropdowns', 'WorkOrdersApiController@dropdowns')->middleware('backendpermission:7');
    Route::get('work-orders', 'WorkOrdersApiController@orders')->middleware('backendpermission:7');
    Route::post('delete-work-orders', 'WorkOrdersApiController@bulk_delete')->middleware('backendpermission:7');

    Route::post('fare-calculation', 'BackendApiController@fare_calc');
    Route::get('fare-calculation', 'BackendApiController@get_fare_calc');

    // bookings API
    Route::post('booking-dropdowns', 'BookingsApiController@get_dropdowns')->middleware('backendpermission:3');
    Route::get('booking-dropdowns/{id}', 'BookingsApiController@dropdowns')->middleware('backendpermission:3');

    Route::post('view-event', 'BookingsApiController@view_event')->middleware('backendpermission:3');
    Route::get('events', 'BookingsApiController@events')->middleware('backendpermission:3');
    Route::get('bookings', 'BookingsApiController@bookings')->middleware('backendpermission:3');
    Route::post('add-booking', 'BookingsApiController@store')->middleware('backendpermission:3');
    Route::post('edit-booking', 'BookingsApiController@update')->middleware('backendpermission:3');
    Route::post('delete-booking', 'BookingsApiController@delete')->middleware('backendpermission:3');
    Route::get('booking-receipt/{id}', 'BookingsApiController@receipt')->middleware('backendpermission:3');
    Route::post('generate-invoice', 'BookingsApiController@generate_invoice')->middleware('backendpermission:3');
    Route::post('complete-journey', 'BookingsApiController@complete_journey')->middleware('backendpermission:3');
    Route::post('make-payment', 'BookingsApiController@make_payment')->middleware('backendpermission:3');
    Route::post('cancel-booking', 'BookingsApiController@cancel_booking')->middleware('backendpermission:3');
    Route::post('delete-bookings', 'BookingsApiController@bulk_delete')->middleware('backendpermission:3');

    // booking quotes API
    Route::post('delete-booking-quotations', 'BookingQuotationsApiController@bulk_delete')->middleware('backendpermission:3');

    Route::get('booking-quotations', 'BookingQuotationsApiController@quotes')->middleware('backendpermission:3');
    Route::post('add-booking-quotation', 'BookingQuotationsApiController@store')->middleware('backendpermission:3');
    Route::post('edit-booking-quotation', 'BookingQuotationsApiController@update')->middleware('backendpermission:3');
    Route::post('approve-booking-quotation', 'BookingQuotationsApiController@approve')->middleware('backendpermission:3');
    Route::post('reject-booking-quotation', 'BookingQuotationsApiController@reject')->middleware('backendpermission:3');
    Route::post('delete-booking-quotation', 'BookingQuotationsApiController@delete')->middleware('backendpermission:3');
    Route::get('booking-quotation-receipt/{id}', 'BookingQuotationsApiController@receipt')->middleware('backendpermission:3');

    // inquiries and review API
    Route::get('inquiries', 'BackendApiController@inquiries');
    Route::get('reviews', 'BackendApiController@reviews')->middleware('backendpermission:10');

    // teams API
    Route::post('upload-member-image/{id}', 'TeamApiController@upload_documents')->middleware('backendpermission:S');

    Route::get('team', 'TeamApiController@teams')->middleware('backendpermission:S');
    Route::post('add-team', 'TeamApiController@store')->middleware('backendpermission:S');
    Route::post('edit-team', 'TeamApiController@update')->middleware('backendpermission:S');
    Route::post('delete-team', 'TeamApiController@delete')->middleware('backendpermission:S');
    Route::post('delete-team-records', 'TeamApiController@bulk_delete')->middleware('backendpermission:S');

    // testimonials API
    Route::post('upload-testimonial-image/{id}', 'TestimonialsApiController@upload_documents')->middleware('backendpermission:15');

    Route::get('testimonials', 'TestimonialsApiController@testimonials')->middleware('backendpermission:15');
    Route::post('add-testimonial', 'TestimonialsApiController@store')->middleware('backendpermission:15');
    Route::post('edit-testimonial', 'TestimonialsApiController@update')->middleware('backendpermission:15');
    Route::post('delete-testimonial', 'TestimonialsApiController@delete')->middleware('backendpermission:15');
    Route::post('delete-testimonials', 'TestimonialsApiController@bulk_delete')->middleware('backendpermission:15');

    // service items API
    Route::get('service-items', 'ServiceItemApiController@items')->middleware('backendpermission:9');
    Route::post('add-service-item', 'ServiceItemApiController@store')->middleware('backendpermission:9');
    Route::post('edit-service-item', 'ServiceItemApiController@update')->middleware('backendpermission:9');
    Route::post('delete-service-item', 'ServiceItemApiController@delete')->middleware('backendpermission:9');
    Route::post('delete-service-items', 'ServiceItemApiController@bulk_delete')->middleware('backendpermission:9');

    // service reminders API
    Route::get('service-reminders', 'ServiceRemindersApiController@reminders')->middleware('backendpermission:9');
    Route::post('add-service-reminder', 'ServiceRemindersApiController@store')->middleware('backendpermission:9');
    Route::post('delete-service-reminder', 'ServiceRemindersApiController@delete')->middleware('backendpermission:9');
    Route::post('delete-service-reminders', 'ServiceRemindersApiController@bulk_delete')->middleware('backendpermission:9');

    // notes API
    Route::post('delete-notes', 'NotesApiController@bulk_delete')->middleware('backendpermission:8');
    Route::post('delete-note', 'NotesApiController@delete')->middleware('backendpermission:8');
    Route::post('edit-note', 'NotesApiController@update')->middleware('backendpermission:8');
    Route::post('add-note', 'NotesApiController@store')->middleware('backendpermission:8');
    Route::get('note-dropdowns', 'NotesApiController@dropdowns')->middleware('backendpermission:8');
    Route::get('notes', 'NotesApiController@notes')->middleware('backendpermission:8');

    // parts API
    Route::post('upload-part-image/{id}', 'PartsApiController@upload_documents')->middleware('backendpermission:14');

    Route::get('parts', 'PartsApiController@parts')->middleware('backendpermission:14');
    Route::post('add-part', 'PartsApiController@store')->middleware('backendpermission:14');
    Route::post('add-stock', 'PartsApiController@add_stock')->middleware('backendpermission:14');
    Route::post('edit-part', 'PartsApiController@update')->middleware('backendpermission:14');
    Route::post('delete-part', 'PartsApiController@delete')->middleware('backendpermission:14');
    Route::post('delete-parts', 'PartsApiController@bulk_delete')->middleware('backendpermission:14');

    // parts category API
    Route::post('delete-part-categories', 'PartsCategoryApiController@bulk_delete')->middleware('backendpermission:14');
    Route::post('delete-part-category', 'PartsCategoryApiController@delete')->middleware('backendpermission:14');
    Route::post('edit-part-category', 'PartsCategoryApiController@update')->middleware('backendpermission:14');
    Route::post('add-part-category', 'PartsCategoryApiController@store')->middleware('backendpermission:14');
    Route::get('part-categories', 'PartsCategoryApiController@categories')->middleware('backendpermission:14');

    // fuel API
    Route::post('delete-fuels', 'FuelApiController@bulk_delete')->middleware('backendpermission:5');
    Route::post('delete-fuel', 'FuelApiController@delete')->middleware('backendpermission:5');
    Route::post('edit-fuel', 'FuelApiController@update')->middleware('backendpermission:5');
    Route::post('add-fuel', 'FuelApiController@store')->middleware('backendpermission:5');
    Route::get('fuel-history', 'FuelApiController@fuel')->middleware('backendpermission:5');

    // vendor API
    Route::post('upload-vendor-photo/{id}', 'VendorsApiController@upload_documents')->middleware('backendpermission:6');
    Route::post('delete-vendors', 'VendorsApiController@bulk_delete')->middleware('backendpermission:6');
    Route::post('delete-vendor', 'VendorsApiController@delete')->middleware('backendpermission:6');
    Route::post('edit-vendor', 'VendorsApiController@update')->middleware('backendpermission:6');
    Route::post('add-vendor', 'VendorsApiController@store')->middleware('backendpermission:6');
    Route::get('vendor-types', 'VendorsApiController@types')->middleware('backendpermission:6');
    Route::get('vendors', 'VendorsApiController@vendors')->middleware('backendpermission:6');

    // expense API
    Route::get('expense', 'ExpenseApiController@expense')->middleware('backendpermission:2');
    Route::post('expense-records', 'ExpenseApiController@expense_records')->middleware('backendpermission:2');
    Route::get('expense-dropdowns', 'ExpenseApiController@expense_dropdowns')->middleware('backendpermission:2');
    Route::post('add-expense', 'ExpenseApiController@store')->middleware('backendpermission:2');
    Route::post('delete-expense', 'ExpenseApiController@delete')->middleware('backendpermission:2');
    Route::post('delete-expenses', 'ExpenseApiController@bulk_delete')->middleware('backendpermission:2');

    // income API
    Route::post('delete-incomes', 'IncomeApiController@bulk_delete')->middleware('backendpermission:2');
    Route::post('delete-income', 'IncomeApiController@delete')->middleware('backendpermission:2');
    Route::post('add-income', 'IncomeApiController@store')->middleware('backendpermission:2');
    Route::get('income-dropdowns', 'IncomeApiController@income_dropdowns')->middleware('backendpermission:2');
    Route::post('income-records', 'IncomeApiController@income_records')->middleware('backendpermission:2');
    Route::get('income', 'IncomeApiController@income')->middleware('backendpermission:2');

    // vehicles API

    Route::post('add-purchase-record', 'VehiclesApiController@store_purchase_info')->middleware('backendpermission:1');
    Route::post('delete-purchase-record', 'VehiclesApiController@delete_purchase_info')->middleware('backendpermission:1');
    Route::post('upload-vehicle-doc/{id}', 'VehiclesApiController@upload_documents')->middleware('backendpermission:1');
    Route::post('upload-vehicle-image/{id}', 'VehiclesApiController@upload_vehicle_image')->middleware('backendpermission:1');

    Route::get('driver-logs', 'VehiclesApiController@driver_logs')->middleware('backendpermission:1');
    Route::post('assign-driver', 'VehiclesApiController@assign_driver')->middleware('backendpermission:1');
    Route::get('available-drivers/{id}', 'VehiclesApiController@available_drivers')->middleware('backendpermission:1');
    Route::get('vehicles', 'VehiclesApiController@vehicles')->middleware('backendpermission:1');
    Route::post('add-vehicle', 'VehiclesApiController@store')->middleware('backendpermission:1');
    Route::post('edit-vehicle', 'VehiclesApiController@update')->middleware('backendpermission:1');
    Route::post('update-vehicle-insurance', 'VehiclesApiController@update_insurance')->middleware('backendpermission:1');
    Route::post('delete-vehicle', 'VehiclesApiController@delete')->middleware('backendpermission:1');
    Route::post('delete-vehicles', 'VehiclesApiController@bulk_delete')->middleware('backendpermission:1');

    // Vehicle groups API
    Route::get('vehicle-groups', 'VehicleGroupApiController@groups');
    Route::post('add-vehicle-group', 'VehicleGroupApiController@store')->middleware('backendpermission:1');
    Route::post('edit-vehicle-group', 'VehicleGroupApiController@update')->middleware('backendpermission:1');
    Route::post('delete-vehicle-group', 'VehicleGroupApiController@delete')->middleware('backendpermission:1');
    Route::post('delete-vehicle-groups', 'VehicleGroupApiController@bulk_delete')->middleware('backendpermission:1');

    // customers API
    Route::get('customers', 'CustomersApiController@customers')->middleware('backendpermission:0');
    Route::post('add-customer', 'CustomersApiController@store')->middleware('backendpermission:0');
    Route::post('edit-customer', 'CustomersApiController@update')->middleware('backendpermission:0');

    Route::post('delete-users', 'CustomersApiController@bulk_delete')->middleware('backendpermission:0');

    // users API
    Route::post('upload-user-photo/{id}', 'UsersApiController@upload_documents')->middleware('backendpermission:0');

    Route::get('users', 'UsersApiController@users')->middleware('backendpermission:0');
    Route::post('add-user', 'UsersApiController@store')->middleware('backendpermission:0');
    Route::post('edit-user', 'UsersApiController@update')->middleware('backendpermission:0');
    Route::post('delete-user', 'UsersApiController@delete')->middleware('backendpermission:0');

    Route::post('change-password', 'BackendApiController@change_password');

    // drivers API
    Route::post('upload-driver-documents/{id}', 'DriversApiController@upload_documents')->middleware('backendpermission:0');
    Route::get('drivers', 'DriversApiController@drivers')->middleware('backendpermission:0');
    Route::post('add-driver', 'DriversApiController@store')->middleware('backendpermission:0');
    Route::post('edit-driver', 'DriversApiController@update')->middleware('backendpermission:0');
    Route::get('phone-codes', 'DriversApiController@phone_codes');
    Route::get('available-vehicles/{vid}', 'DriversApiController@vehicles')->middleware('backendpermission:0');
    Route::post('change-active-status', 'DriversApiController@enable_disable')->middleware('backendpermission:0');
    Route::post('delete-driver', 'DriversApiController@delete')->middleware('backendpermission:0');
    Route::post('delete-drivers', 'DriversApiController@bulk_delete')->middleware('backendpermission:0');
});
