<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FrontEndRequest;
use App\Http\Requests\PaymentSettignsRequest;
use App\Http\Requests\SettingsRequest;
use App\Mail\RenewDrivingLicence;
use App\Mail\RenewInsurance;
use App\Mail\RenewRegistration;
use App\Mail\RenewVehicleLicence;
use App\Mail\ServiceReminder;
use App\Model\Address;
use App\Model\ApiSettings;
use App\Model\BookingIncome;
use App\Model\BookingPaymentsModel;
use App\Model\BookingQuotationModel;
use App\Model\Bookings;
use App\Model\CompanyServicesModel;
use App\Model\DriverLogsModel;
use App\Model\DriverVehicleModel;
use App\Model\EmailContent;
use App\Model\ExpCats;
use App\Model\Expense;
use App\Model\FareSettings;
use App\Model\FrontendModel;
use App\Model\FuelModel;
use App\Model\IncCats;
use App\Model\IncomeModel;
use App\Model\Mechanic;
use App\Model\MessageModel;
use App\Model\NotesModel;
use App\Model\PartsCategoryModel;
use App\Model\PartsModel;
use App\Model\PartsUsedModel;
use App\Model\PaymentSettings;
use App\Model\ReasonsModel;
use App\Model\ReviewModel;
use App\Model\ServiceItemsModel;
use App\Model\ServiceReminderModel;
use App\Model\Settings;
use App\Model\TeamModel;
use App\Model\Testimonial;
use App\Model\User;
use App\Model\UserData;
use App\Model\VehicleGroupModel;
use App\Model\VehicleModel;
use App\Model\VehicleReviewModel;
use App\Model\VehicleTypeModel;
use App\Model\Vendor;
use App\Model\WorkOrderLogs;
use App\Model\WorkOrders;
use Auth;
use DB;
use Edujugon\PushNotification\PushNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Redirect;
use Storage;
use Illuminate\Support\Facades\Artisan;


class SettingsController extends Controller {
	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:Settings list');
	}

	public function clear_database() {
		Address::whereNotNull('id')->delete();
		BookingIncome::whereNotNull('id')->delete();
		BookingPaymentsModel::whereNotNull('id')->delete();
		BookingQuotationModel::whereNotNull('id')->delete();
		Bookings::whereNotNull('id')->delete();
		CompanyServicesModel::whereNotNull('id')->delete();
		DriverLogsModel::whereNotNull('id')->delete();
		DriverVehicleModel::whereNotNull('id')->delete();
		Expense::whereNotNull('id')->delete();
		ExpCats::where('type', 'u')->delete();
		FuelModel::whereNotNull('id')->delete();
		IncCats::where('type', 'u')->delete();
		IncomeModel::whereNotNull('id')->delete();
		MessageModel::whereNotNull('id')->delete();
		NotesModel::whereNotNull('id')->delete();
		PartsCategoryModel::whereNotNull('id')->delete();
		PartsModel::whereNotNull('id')->delete();
		PartsUsedModel::whereNotNull('id')->delete();
		ReasonsModel::whereNotNull('id')->delete();
		ReviewModel::whereNotNull('id')->delete();
		ServiceItemsModel::whereNotNull('id')->delete();
		ServiceReminderModel::whereNotNull('id')->delete();
		TeamModel::whereNotNull('id')->delete();
		Testimonial::whereNotNull('id')->delete();
		User::where('id', '!=', 1)->delete();
		UserData::where('user_id', '!=', 1)->delete();
		VehicleGroupModel::whereNotNull('id')->delete();
		VehicleModel::whereNotNull('id')->delete();
		VehicleReviewModel::whereNotNull('id')->delete();
		// VehicleTypeModel::whereNotNull('id')->delete();
		Vendor::whereNotNull('id')->delete();
		Mechanic::whereNotNull('id')->delete();
		WorkOrderLogs::whereNotNull('id')->delete();
		WorkOrders::whereNotNull('id')->delete();
		EmailContent::where('key', 'users')->update(['value' => '']);
		EmailContent::where('key', 'options')->update(['value' => '']);
		DB::table('notifications')->truncate();

		return back()->with(['msg' => __('fleet.db_cleared')]);
	}

	public function payment_settings() {
		return view("utilities.payment_settings");
	}

	public function payment_settings_post(PaymentSettignsRequest $request) {
		// dd($request->all());
		$only_razorpay = array("CUP", "GHS", "SSP", "SVC");
		$only_stripe = array("AFN", "ANG", "AOA", "AZN", "BAM", "BGN", "BIF", "BRL", "CDF", "CLP", "CVE", "DJF", "FKP", "GEL", "GNF", "ISK", "JPY", "KMF", "KRW", "MGA", "MRO", "MZN", "PAB", "PLN", "PYG", "RON", "RSD", "RWF", "SBD", "SHP", "SRD", "STD", "THB", "TJS", "TOP", "TRY", "TWD", "UAH", "UGX", "VND", "VUV", "WST", "XAF", "XCD", "XOF", "XPF", "ZMW");

		if (in_array("stripe", $request->method) && in_array($request->currency_code, $only_stripe)) {
			// stripe in array methods && currency in array only Razorpay => error
			return back()->with(['error_msg' => 'Stripe Payment Method does not support payments in ' . $request->currency_code . ' currency']);
		}
		if (in_array("razorpay", $request->method) && in_array($request->currency_code, $only_razorpay)) {
			// razorpay in array methods && currency in array only stripe => error
			return back()->with(['error_msg' => 'RazorPay Payment Method does not support payments in ' . $request->currency_code . ' currency']);
		}

		# paystack only allowed in below currency code.
		$only_paystack = array("NGN", "GHS", "ZAR", "USD");
		if (in_array("paystack", $request->method) && !in_array($request->currency_code, $only_paystack)) {
			// razorpay in array methods && currency in array only stripe => error
			return back()->with(['error_msg' => 'Paystack Payment Method does not support payments in ' . $request->currency_code . ' currency']);
		}

		PaymentSettings::where('name', 'method')->update(['value' => json_encode($request->method)]);
		PaymentSettings::where('name', 'currency_code')->update(['value' => $request->currency_code]);
		PaymentSettings::where('name', 'stripe_publishable_key')->update(['value' => $request->stripe_publishable_key]);
		PaymentSettings::where('name', 'stripe_secret_key')->update(['value' => $request->stripe_secret_key]);
		PaymentSettings::where('name', 'razorpay_key')->update(['value' => $request->razorpay_key]);
		PaymentSettings::where('name', 'razorpay_secret')->update(['value' => $request->razorpay_secret]);
		PaymentSettings::where('name', 'paystack_secret')->update(['value' => $request->paystack_secret]);
		return back()->with(['msg' => __('fleet.payment_settingsUpdated')]);
	}

	public function frontend() {
		$data['languages'] = Storage::disk('views')->directories('');
		return view('utilities.frontend', $data);
	}

	public function store_frontend(FrontEndRequest $request) {
		if ($request->approval_required == 1) {
			$admin_approval = 1;
		} else {
			$admin_approval = 0;
		}
		FrontendModel::where('key_name', 'about_us')->update(['key_value' => $request->about]);
		FrontendModel::where('key_name', 'contact_email')->update(['key_value' => $request->email]);
		FrontendModel::where('key_name', 'contact_phone')->update(['key_value' => $request->phone]);
		FrontendModel::where('key_name', 'customer_support')->update(['key_value' => $request->customer_support]);
		FrontendModel::where('key_name', 'about_description')->update(['key_value' => $request->about_description]);
		FrontendModel::where('key_name', 'about_title')->update(['key_value' => $request->about_title]);
		FrontendModel::where('key_name', 'facebook')->update(['key_value' => $request->facebook]);
		FrontendModel::where('key_name', 'twitter')->update(['key_value' => $request->twitter]);
		FrontendModel::where('key_name', 'instagram')->update(['key_value' => $request->instagram]);
		FrontendModel::where('key_name', 'linkedin')->update(['key_value' => $request->linkedin]);
		FrontendModel::where('key_name', 'faq_link')->update(['key_value' => $request->faq_link]);
		FrontendModel::where('key_name', 'cities')->update(['key_value' => $request->cities]);
		FrontendModel::where('key_name', 'vehicles')->update(['key_value' => $request->vehicles]);
		FrontendModel::where('key_name', 'booking_time')->update(['key_value' => $request->booking_time]);
		FrontendModel::where('key_name', 'cancellation')->update(['key_value' => $request->cancellation]);
		FrontendModel::where('key_name', 'terms')->update(['key_value' => $request->terms]);
		FrontendModel::where('key_name', 'privacy_policy')->update(['key_value' => $request->privacy_policy]);
		FrontendModel::where('key_name', 'enable')->update(['key_value' => $request->enable]);
		FrontendModel::where('key_name', 'admin_approval')->update(['key_value' => $admin_approval]);

		$enable = 'no';
		if ($request->enable == 1) {
			$enable = 'yes';
		}

		if (!(env('front_enable'))) {
			file_put_contents(base_path('.env'), "front_enable=" . $enable . PHP_EOL, FILE_APPEND);
		}
		if ((env('front_enable'))) {
			file_put_contents(base_path('.env'), str_replace(
				'front_enable=' . env('front_enable'), 'front_enable=' . $enable, file_get_contents(base_path('.env'))));

		}
		FrontendModel::where('key_name', 'language')->update(['key_value' => $request->language]);
		return redirect('admin/frontend-settings');
	}

	public function index() {
		$data['settings'] = Settings::all();
		$data['languages'] = Storage::disk('views')->directories('');

		return view("utilities.settings", $data);
	}

	private function upload_file($file, $field, $name) {
		$destinationPath = './assets/images'; // upload path
		$extension = $file->getClientOriginalExtension();
		$fileName1 = Str::uuid() . '.' . $extension;

		$file->move($destinationPath, $fileName1);

		$x = Settings::where("name", $name)->update([$field => $fileName1]);

	}

	public function store(SettingsRequest $request) {
		// dd($request->all());
		$fuel_enable = 0;
		$income_enable = 0;
		$expense_enable = 0;
		$traccar_enable = 0;
		$theme = "";
		if ($request->fuel_enable_driver == 1) {
			$fuel_enable = 1;
		}

		if ($request->income_enable_driver == 1) {
			$income_enable = 1;
		}
		if ($request->expense_enable_driver == 1) {
			$expense_enable = 1;
		}
		if ($request->theme) {
			$theme = "dark-mode";
		}
		foreach ($request->get('name') as $key => $val) {
			Settings::where('name', $key)->update(['value' => $val]);
			Settings::where('name', 'fuel_enable_driver')->update(['value' => $fuel_enable]);

			Settings::where('name', 'traccar_username')->update(['value' => $request->traccar_username]);
			Settings::where('name', 'traccar_password')->update(['value' => $request->traccar_password]);
			Settings::where('name', 'traccar_server_link')->update(['value' => $request->traccar_server_link]);
			Settings::where('name', 'income_enable_driver')->update(['value' => $income_enable]);
			Settings::where('name', 'expense_enable_driver')->update(['value' => $expense_enable]);
			Settings::where('name', 'theme')->update(['value' => $theme]);
			if ($key == 'language') {
				$user = Auth::user();
				$user->language = $val;
				$user->save();
			}

		}

		$taxes = json_encode($request->udf);
		Settings::where('name', 'tax_charge')->update(['value' => $taxes]);

		$app_name = str_replace(" ", "_", $request->name['app_name']);
		if (!env('APP_NAME')) {

			file_put_contents(base_path('.env'), "APP_NAME=" . $app_name . PHP_EOL, FILE_APPEND);
		}
		if (env('APP_NAME')) {

			file_put_contents(base_path('.env'), str_replace(
				'APP_NAME=' . env('APP_NAME'), 'APP_NAME=' . $app_name, file_get_contents(base_path('.env'))));
		}

		if ($request->file('icon_img') && $request->file('icon_img')->isValid()) {
			$this->upload_file($request->file('icon_img'), "value", 'icon_img');
		}

		if ($request->file('logo_img') && $request->file('logo_img')->isValid()) {
			$this->upload_file($request->file('logo_img'), "value", 'logo_img');
		}

		// Cache::flush();
		return Redirect::route("settings.index");
	}

	public function api_settings() {
		$data['settings'] = ApiSettings::all();

		return view("utilities.api_settings", $data);
	}

	public function store_settings(Request $request) {
		ApiSettings::where('key_name', 'api')->update(['key_value' => 0]);
		ApiSettings::where('key_name', 'anyone_register')->update(['key_value' => 0]);

		ApiSettings::where('key_name', 'driver_review')->update(['key_value' => 0]);

		ApiSettings::where('key_name', 'google_api')->update(['key_value' => 0]);
		foreach ($request->get('name') as $key => $val) {
			ApiSettings::where('key_name', $key)->update(['key_value' => $val]);
		}

		// Cache::flush();
		return redirect('admin/api-settings');
	}

	public function fare_settings() {
		$data['settings'] = FareSettings::all();
		$vehicle_types = VehicleTypeModel::get();
		$all = array();
		foreach ($vehicle_types as $type) {
			$all[] = $type->vehicletype;
		}
		$data['types'] = array_unique($all);
		return view('utilities.fare_settings', $data);
	}

	public function store_fareSettings(Request $request) {
		foreach ($request->get('name') as $key => $val) {
			FareSettings::where('key_name', $key)->update(['key_value' => $val]);
		}
		$tab = $_GET['tab'];

		return redirect('admin/fare-settings?tab=' . $tab);

	}

	public function send_email() {
		$data['users'] = User::where('user_type', '!=', 'C')->where('user_type', '!=', 'D')->get();
		$selected_users = EmailContent::where('key', 'users')->first();
		$selected_options = EmailContent::where('key', 'options')->first();
		$data['options'] = array();
		$data['selected_users'] = array();
		if ($selected_options->value != null) {
			$data['options'] = unserialize($selected_options->value);
		}
		if ($selected_users->value != null) {
			$data['selected_users'] = unserialize($selected_users->value);
		}

		return view('utilities.send_email', $data);
	}

	public function enable_mail(Request $request) {
		if ($request->email == '1') {
			$email = 1;
		} else {
			$email = 0;
		}
		EmailContent::where('key', 'email')->update(['value' => $email]);
		return redirect()->back();
	}

	public function email_settings(Request $request) {
		EmailContent::where('key', 'users')->update(['value' => serialize($request->get('users'))]);
		EmailContent::where('key', 'options')->update(['value' => serialize($request->get('chk'))]);
		return redirect()->back();

	}

	public function email_notification(Request $request) {
		$chk = $request->get('chk');
		$users = User::whereIn('id', $request->get('users'))->get();
		$d = VehicleModel::get();

		if (in_array(1, $chk)) {

			foreach ($d as $data) {

				$vehicle = $data->make_name . '-' . $data->model_name . '-' . $data->license_plate;
				$reg_date = $data->reg_exp_date;
				$to = \Carbon\Carbon::now();
				$from = \Carbon\Carbon::createFromFormat('Y-m-d', $reg_date);
				$diff_in_days = $to->diffInDays($from);
				if ($diff_in_days <= 20) {
					foreach ($users as $user) {

						Mail::to($user->email)->send(new RenewRegistration($vehicle, $reg_date, $user->name));

					}
				}
			}
		}

		if (in_array(3, $chk)) {

			foreach ($d as $data) {

				$vehicle = $data->make_name . '-' . $data->model_name . '-' . $data->license_plate;
				$lic_date = $data->lic_exp_date;
				$to = \Carbon\Carbon::now();
				$from = \Carbon\Carbon::createFromFormat('Y-m-d', $lic_date);
				$diff_in_days = $to->diffInDays($from);
				if ($diff_in_days <= 20) {
					foreach ($users as $user) {
						Mail::to($user->email)->send(new RenewVehicleLicence($vehicle, $lic_date, $user->name));

					}
				}
			}
		}

		if (in_array(4, $chk)) {

			$d1 = User::where('user_type', 'D')->where('deleted_at', null)->get();

			foreach ($d1 as $data) {

				$driver = $data->name;
				$lic_date = $data->getMeta('exp_date');
				$to = \Carbon\Carbon::now();
				$from = \Carbon\Carbon::createFromFormat('Y-m-d', $lic_date);
				$diff_in_days = $to->diffInDays($from);
				if ($diff_in_days <= 20) {
					foreach ($users as $user) {
						Mail::to($user->email)->send(new RenewDrivingLicence($driver, $lic_date, $diff_in_days, $user->name));

					}
				}
			}
		}

		if (in_array(2, $chk)) {

			$v = VehicleModel::get();
			foreach ($v as $vehicle) {
				$ins_date = $vehicle->getMeta('ins_exp_date');
				$vehicle = $vehicle->make_name . '-' . $vehicle->model_name . '-' . $vehicle->license_plate;
				$to = \Carbon\Carbon::now();
				$from = \Carbon\Carbon::createFromFormat('Y-m-d', $ins_date);
				$diff_in_days = $to->diffInDays($from);
				if ($diff_in_days <= 20) {
					foreach ($users as $user) {
						Mail::to($user->email)->send(new RenewInsurance($vehicle, $ins_date, $diff_in_days, $user->name));

					}
				}
			}
		}

		if (in_array(5, $chk)) {

			$s = ServiceReminderModel::get();
			foreach ($s as $data) {
				$interval = substr($data->services->overdue_unit, 0, -3);
				$int = $data->services->overdue_time . $interval;
				$date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))));

				$to = \Carbon\Carbon::now();
				$from = \Carbon\Carbon::createFromFormat('Y-m-d', $date);
				$diff_in_days = $to->diffInDays($from);

				$duesoon = substr($data->services->duesoon_unit, 0, -3);
				$int1 = $data->services->duesoon_time . $duesoon;
				$date1 = date('Y-m-d', strtotime($int1, strtotime(date('Y-m-d'))));

				$from1 = \Carbon\Carbon::createFromFormat('Y-m-d', $date1);
				$condition = $to->diffInDays($from1);
				if ($data->services->duesoon_time = null) {
					$condition = 20;
				}
				$detail = $data->services->description;
				$vehicle = $data->vehicle->make_name . '-' . $data->vehicle->model_name . '-' . $data->vehicle->license_plate;
				if ($diff_in_days <= $condition) {
					foreach ($users as $user) {
						Mail::to($user->email)->send(new ServiceReminder($detail, $vehicle, $date, $diff_in_days, $user->name));

					}
				}
			}
		}

		return redirect()->back();
	}

	public function set_email() {
		return view('utilities.set_email');
	}

	public function set_content(Request $request, $type) {

		if ($type == "insurance") {
			$validator = $request->validate([
				'insurance' => 'required',
			]);
			EmailContent::where('key', 'insurance')->update(['value' => $request->get('insurance')]);

		} elseif ($type == "vehicle-licence") {
			$request->validate([
				'vehicle_licence' => 'required',
			]);
			EmailContent::where('key', 'vehicle_licence')->update(['value' => $request->get('vehicle_licence')]);
		} elseif ($type == "driver-licence") {
			$request->validate([
				'driving_licence' => 'required',
			]);
			EmailContent::where('key', 'driving_licence')->update(['value' => $request->get('driving_licence')]);
		} elseif ($type == "registration") {
			$request->validate([
				'registration' => 'required',
			]);
			EmailContent::where('key', 'registration')->update(['value' => $request->get('registration')]);
		} elseif ($type == "reminder") {
			$request->validate([
				'service_reminder' => 'required',
			]);
			EmailContent::where('key', 'service_reminder')->update(['value' => $request->get('service_reminder')]);
		}

		// return redirect()->back();
		return redirect('admin/set-email?tab=' . $type);
	}

	public function firebase(Request $request) {
		Artisan::call('config:clear');
		$db_url = $request->get('db_url');
		// $db_secret = $request->get('db_secret');
		$url = "db_url=" . $db_url;
		// $secret = "db_secret=" .$db_secret;
		
		try {
			$db_url = $request->get('db_url');
			$database = Firebase::database($db_url);
			$data= $value = $database->getReference('User_Locations')->getValue();
			
			
			// $factory = (new Factory())
			// 	->withServiceAccount(storage_path('firebase/firebase_credentials.json'))
			// 	->withDatabaseUri($db_url);
			// $database = $factory->createDatabase();

			// $database->getReference('testing1')->set([
			// 	'name' => 'Fleet Testing1',
			// ]);
			// // dd($database);
			// $data = $database->getReference('testing1');
			// $value = $data->getValue();

			// Firebase::set('/test/', ["testing"]);
			//$data = Firebase::get('/test/');
			//$details = json_decode($data, true);

			if (isset($value['error']) || $value == null) {
				// dd("no records");
				$success = "0";
			} else {
				if (!env('db_url')) {
					file_put_contents(base_path('.env'), $url . PHP_EOL, FILE_APPEND);
				}
				if (env('db_url')) {
					file_put_contents(base_path('.env'), str_replace(
						'db_url=' . env('db_url'), 'db_url=' . $db_url, file_get_contents(base_path('.env'))));
				}

				// if (!env('db_secret')) {
				//     file_put_contents(base_path('.env'), $secret . PHP_EOL, FILE_APPEND);
				// }
				// if (env('db_secret')) {
				//     file_put_contents(base_path('.env'), str_replace(
				//         'db_secret=' . env('db_secret'), 'db_secret=' . $db_secret, file_get_contents(base_path('.env'))));
				// }

				$success = "1";
				// ApiSettings::where('key_name', 'db_secret')->update(['key_value' => $db_secret]);
				ApiSettings::where('key_name', 'db_url')->update(['key_value' => $db_url]);
			}

		} catch (Exception $e) {
			// dd($e);
			$success = "0";
		}

		return redirect('admin/api-settings?tab=firebase&success=' . $success);

	}

	public function store_key(Request $request) {
		$key = $request->get('server_key');
		$env = "server_key=" . $key;
		if (!env('server_key')) {
			// dd('test');
			file_put_contents(base_path('.env'), $env . PHP_EOL, FILE_APPEND);
		}
		if (env('server_key')) {

			file_put_contents(base_path('.env'), str_replace(
				'server_key=' . env('server_key'), 'server_key=' . $key, file_get_contents(base_path('.env'))));
		}

		return redirect('admin/test-key');
	}

	public function test_key() {

		try {

			$notification = new PushNotification('fcm');
			$notification->setMessage(['body' => 'This is the message', 'title' => 'This is the title'])
				->setApiKey(env('server_key'))
				->setDevicesToken(['d5Av2XvAAns:APA91bGH34jdo6UlKCLsf724FMGhlZhTFGCBhmP2pON5fNit7p245RFLjGF24wa_4kIO3kJ-6hHM3aYHPPAfVvFyUX78KbzrPMY18TynUHuYREr3HJuIHbu56BmSNViw6-CnUYn3DZST'])
				->send();

			// $notification = PushNotification::app('appNameAndroid')
			//     ->to('d5Av2XvAAns:APA91bGH34jdo6UlKCLsf724FMGhlZhTFGCBhmP2pON5fNit7p245RFLjGF24wa_4kIO3kJ-6hHM3aYHPPAfVvFyUX78KbzrPMY18TynUHuYREr3HJuIHbu56BmSNViw6-CnUYn3DZST')
			//     ->send('testing');
			// dd($notification->service->feedback);
			if ($notification->service->feedback->success == 1) {
				ApiSettings::where('key_name', 'server_key')->update(['key_value' => env('server_key')]);
				// dd($notification);
				return redirect('admin/api-settings?tab=serverkey&key=1');
			} else {
				return redirect('admin/api-settings?tab=serverkey&key=0');
			}

		} catch (Exception $e) {
			//dd($e);
			return redirect('admin/api-settings?tab=serverkey&key=0');
		}
	}

	public function store_api(Request $request) {
		$key = $request->get('api_key');
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=40.714224,-73.961452&key=' . $key;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($data, true);
		// dd($response);
		if ($response['status'] != "OK" && $response['error_message']) {
			$msg = $response['error_message'];
			return redirect('admin/api-settings?tab=maps&api_key=0&msg=' . $msg . '&test_key=' . $key);
		}
		if ($response['status'] == "OK") {
			$msg = "API key successfully saved";
			ApiSettings::where('key_name', 'api_key')->update(['key_value' => $key]);
			return redirect('admin/api-settings?tab=maps&api_key=1&msg=' . $msg . '&test_key=' . $key);
		} else {
			$msg = "Something went wrong, please try again";
			return redirect('admin/api-settings?tab=maps&api_key=0&msg=' . $msg . '&test_key=' . $key);
		}
	}

	public function ajax_api_store($api) {
		ApiSettings::where('key_name', 'api_key')->update(['key_value' => $api]);
		return "true";
	}

}
