<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Http\Controllers;

use App\Helpers\InstalledFileManager;
use App\Model\ApiSettings;
use App\Model\Bookings;
use App\Model\ChatSettingsModel;
use App\Model\CompanyServicesModel;
use App\Model\EmailContent;
use App\Model\FareSettings;
use App\Model\FrontendModel;
use App\Model\Hyvikk;
use App\Model\PaymentSettings;
use App\Model\Settings;
use App\Model\TeamModel;
use App\Model\Testimonial;
use App\Model\TwilioSettings;
use App\Model\User;
use App\Model\VehicleModel;
use App\Model\VehicleTypeModel;
use Faker\Generator as Faker;
use File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UpdateVersion extends Controller {

	public function upgrade61(InstalledFileManager $fileManager) {
		Artisan::call('migrate');

		$this->upgradeTo61();

		$fileManager->update();
		return view('laravel_web_installer.finished');
	}

	public function upgrade603(InstalledFileManager $fileManager) {
		Artisan::call('migrate');

		$this->upgradeTo603();

		$fileManager->update();
		return view('laravel_web_installer.finished');
	}
	public function upgrade602(InstalledFileManager $fileManager) {
		Artisan::call('migrate');
		$this->upgradeTo602();
		$this->upgradeTo603();
		$fileManager->update();

		return view('laravel_web_installer.finished');
	}

	public function upgrade601(InstalledFileManager $fileManager) {
		Artisan::call('migrate');
		$this->upgradeTo601();
		$this->upgradeTo602();
		$this->upgradeTo603();

		$fileManager->update();

		return view('laravel_web_installer.finished');
	}

	public function upgrade6(InstalledFileManager $fileManager) {
		Artisan::call('migrate');
		$fileManager->update();
		$this->add_permission();
		$this->upgradeTo601();
		$this->upgradeTo602();

		return view('laravel_web_installer.finished');
	}

	public function upgrade5(InstalledFileManager $fileManager) {
		Artisan::call('migrate');
		Artisan::call('passport:install');
		TwilioSettings::create(['name' => 'sid', 'value' => '']);
		TwilioSettings::create(['name' => 'token', 'value' => '']);
		TwilioSettings::create(['name' => 'from', 'value' => '']);
		TwilioSettings::create(['name' => 'customer_message', 'value' => '']);
		TwilioSettings::create(['name' => 'driver_message', 'value' => '']);
		$fileManager->update();
		return view('laravel_web_installer.finished');
	}

	public function upgrade403(InstalledFileManager $fileManager) {
		Artisan::call('migrate');
		Artisan::call('passport:install');
		TwilioSettings::create(['name' => 'sid', 'value' => '']);
		TwilioSettings::create(['name' => 'token', 'value' => '']);
		TwilioSettings::create(['name' => 'from', 'value' => '']);
		TwilioSettings::create(['name' => 'customer_message', 'value' => '']);
		TwilioSettings::create(['name' => 'driver_message', 'value' => '']);
		Settings::updateOrCreate(['label' => 'Date Format', 'name' => 'date_format', 'value' => 'd-m-Y']);
		$fileManager->update();
		return view('laravel_web_installer.finished');
	}

	public function upgrade402(InstalledFileManager $fileManager, Faker $faker) {
		Artisan::call('migrate');
		Artisan::call('passport:install');
		$this->tax_charge();
		$this->payment_settings();
		Settings::updateOrCreate(['label' => 'Date Format', 'name' => 'date_format', 'value' => 'd-m-Y']);
		// version5
		TwilioSettings::create(['name' => 'sid', 'value' => '']);
		TwilioSettings::create(['name' => 'token', 'value' => '']);
		TwilioSettings::create(['name' => 'from', 'value' => '']);
		TwilioSettings::create(['name' => 'customer_message', 'value' => '']);
		TwilioSettings::create(['name' => 'driver_message', 'value' => '']);
		$fileManager->update();
		return view('laravel_web_installer.finished');
	}

	public function upgrade4(InstalledFileManager $fileManager, Faker $faker) {
		Artisan::call('migrate');
		Artisan::call('passport:install');
		$this->frontend_settings($faker);
		$this->move_folders();
		$this->booking_receipts();
		$this->tax_charge();
		$this->payment_settings();
		Settings::updateOrCreate(['label' => 'Date Format', 'name' => 'date_format', 'value' => 'd-m-Y']);
		// version5
		TwilioSettings::create(['name' => 'sid', 'value' => '']);
		TwilioSettings::create(['name' => 'token', 'value' => '']);
		TwilioSettings::create(['name' => 'from', 'value' => '']);
		TwilioSettings::create(['name' => 'customer_message', 'value' => '']);
		TwilioSettings::create(['name' => 'driver_message', 'value' => '']);
		$fileManager->update();
		return view('laravel_web_installer.finished');
	}

	public function upgrade3(InstalledFileManager $fileManager, Faker $faker) {
		Artisan::call('migrate');
		Artisan::call('passport:install');
		$this->api_change();
		$fare_change = FareSettings::where('key_name', 'base_fare')->first();
		if ($fare_change != null) {
			$this->vehicle_types();
		}
		$this->frontend_settings($faker);
		$this->move_folders();
		$this->booking_receipts();
		$this->tax_charge();
		$this->payment_settings();
		Settings::updateOrCreate(['label' => 'Date Format', 'name' => 'date_format', 'value' => 'd-m-Y']);
		// version5
		TwilioSettings::create(['name' => 'sid', 'value' => '']);
		TwilioSettings::create(['name' => 'token', 'value' => '']);
		TwilioSettings::create(['name' => 'from', 'value' => '']);
		TwilioSettings::create(['name' => 'customer_message', 'value' => '']);
		TwilioSettings::create(['name' => 'driver_message', 'value' => '']);
		$fileManager->update();
		return view('laravel_web_installer.finished');
	}

	public function payment_settings() {
		PaymentSettings::updateOrCreate(['name' => 'method', 'value' => json_encode(["cash"])]);
		PaymentSettings::updateOrCreate(['name' => 'currency_code', 'value' => 'INR']);
		PaymentSettings::updateOrCreate(['name' => 'stripe_publishable_key', 'value' => null]);
		PaymentSettings::updateOrCreate(['name' => 'stripe_secret_key', 'value' => null]);
		PaymentSettings::updateOrCreate(['name' => 'razorpay_key', 'value' => null]);
		PaymentSettings::updateOrCreate(['name' => 'razorpay_secret', 'value' => null]);

		FrontendModel::updateOrCreate(['key_name' => 'language', 'key_value' => 'en']);

	}

	public function tax_charge() {
		Settings::updateOrCreate(['label' => 'Tax Charge', 'name' => 'tax_charge', 'value' => 'null']);
		Settings::updateOrCreate(['label' => 'Fuel Unit', 'name' => 'fuel_unit', 'value' => 'gallon']);

		// add 0% tax charge to existing bookings
		$bookings = Bookings::get();
		foreach ($bookings as $booking) {
			if ($booking->total) {
				$booking->tax_total = $booking->total;
				$booking->total_tax_percent = 0;
				$booking->total_tax_charge_rs = 0;
				$booking->save();
			}
		}
	}

	public function add_permission() {
		$modules = array(
			'Users',
			'Drivers',
			'Customer',
			'VehicleType',
			'VehicleGroup',
			'VehicleInspection',
			'BookingQuotations',
			'PartsCategory',
			'Mechanics',
			'Vehicles',
			'Transactions',
			'Bookings',
			'Reports',
			'Fuel',
			'Vendors',
			'Parts',
			'WorkOrders',
			'Notes',
			'ServiceReminders',
			'ServiceItems',
			'Testimonials',
			'Team',
			'Settings',
			'Inquiries',
		);
		foreach ($modules as $row) {

			Permission::create(['name' => $row . " add"]);
			Permission::create(['name' => $row . " edit"]);
			Permission::create(['name' => $row . " delete"]);
			Permission::create(['name' => $row . " list"]);
			Permission::create(['name' => $row . " import"]);
		}
		$all = Permission::all();
		$role = Role::create(['name' => 'Super Admin']);
		$role->givePermissionTo($all);
		$role = Role::create(['name' => 'Admin']);
		$role->givePermissionTo(['Bookings list', 'Bookings add', 'Bookings edit', 'Bookings delete', 'Drivers list', 'Drivers add', 'Drivers edit', 'Drivers delete', 'Customer list', 'Customer add', 'Customer edit', 'Customer delete']);
		$users = User::where('user_type', 'S')->get();
		foreach ($users as $user) {
			$u = User::find($user->id);
			$u->assignRole('Super Admin');
		}
		$drivers = User::where('user_type', 'D')->get();
		foreach ($drivers as $driver) {
			$d = User::find($driver->id);
			$d->givePermissionTo(['Notes add', 'Notes edit', 'Notes delete', 'Notes list', 'Drivers list']);
		}
		$customers = User::where('user_type', 'C')->get();
		foreach ($customers as $customer) {
			$c = User::find($customer->id);
			$c->givePermissionTo(['Bookings add', 'Bookings edit', 'Bookings list', 'Bookings delete']);
		}
		$others = User::where('user_type', 'O')->get();
		foreach ($others as $other) {
			$o = User::find($other->id);
			$o->assignRole('Admin');
		}
	}

	public function booking_receipts() {
		$bookings = Bookings::where('status', 1)->get();
		foreach ($bookings as $booking) {
			$booking->receipt = 1;
			$booking->save();
		}
	}

	public function move_folders() {
		// move all images of img,files folder to assets/images folder and delete img,files folder
		$files = Storage::disk('public_files')->files();
		$imgs = Storage::disk('public_img')->files();

		foreach ($files as $file) {
			if (file_exists("files/" . $file)) {
				\File::copy("files/" . $file, "assets/images/" . $file);
			}
		}
		foreach ($imgs as $img) {
			if (file_exists("img/" . $img)) {
				\File::copy("img/" . $img, "assets/images/" . $img);
			}
		}

		// if (file_exists('img')) {
		//     File::deleteDirectory('img');
		// }
		if (file_exists('files')) {
			File::deleteDirectory('files');
		}
	}

	public function frontend_settings(Faker $faker) {
		FrontendModel::updateOrCreate(['key_name' => 'about_us'], ['key_name' => 'about_us', 'key_value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.']);
		FrontendModel::updateOrCreate(['key_name' => 'contact_email'], ['key_name' => 'contact_email', 'key_value' => 'master@admin.com']);
		FrontendModel::updateOrCreate(['key_name' => 'contact_phone'], ['key_name' => 'contact_phone', 'key_value' => '0123456789']);
		FrontendModel::updateOrCreate(['key_name' => 'customer_support'], ['key_name' => 'customer_support', 'key_value' => '0999988888']);
		FrontendModel::updateOrCreate(['key_name' => 'about_description'], ['key_name' => 'about_description', 'key_value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.']);
		FrontendModel::updateOrCreate(['key_name' => 'about_title'], ['key_name' => 'about_title', 'key_value' => 'Proudly serving you']);
		FrontendModel::updateOrCreate(['key_name' => 'facebook'], ['key_name' => 'facebook', 'key_value' => null]);
		FrontendModel::updateOrCreate(['key_name' => 'twitter'], ['key_name' => 'twitter', 'key_value' => null]);
		FrontendModel::updateOrCreate(['key_name' => 'instagram'], ['key_name' => 'instagram', 'key_value' => null]);
		FrontendModel::updateOrCreate(['key_name' => 'linkedin'], ['key_name' => 'linkedin', 'key_value' => null]);
		FrontendModel::updateOrCreate(['key_name' => 'faq_link'], ['key_name' => 'faq_link', 'key_value' => null]);
		FrontendModel::updateOrCreate(['key_name' => 'cities', 'key_value' => 5]);
		FrontendModel::updateOrCreate(['key_name' => 'vehicles', 'key_value' => 10]);
		FrontendModel::updateOrCreate(['key_name' => 'cancellation', 'key_value' => null]);
		FrontendModel::updateOrCreate(['key_name' => 'terms', 'key_value' => null]);
		FrontendModel::updateOrCreate(['key_name' => 'privacy_policy', 'key_value' => null]);
		FrontendModel::updateOrCreate(['key_name' => 'enable', 'key_value' => 1]);
		FrontendModel::updateOrCreate(['key_name' => 'admin_approval', 'key_value' => 0]);

		// add default service item time interval
		Settings::updateOrCreate(['label' => 'Time Interval', 'name' => 'time_interval', 'value' => 30]);
		// company services for frontend
		CompanyServicesModel::create(['title' => 'Best price guranteed', 'image' => 'fleet-bestprice.png', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.Neque at, nobis repudiandae dolores.']);
		CompanyServicesModel::create(['title' => '24/7 Customer care', 'image' => 'fleet-care.png', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.Neque at, nobis repudiandae dolores.']);
		CompanyServicesModel::create(['title' => 'Home pickups', 'image' => 'fleet-homepickup.png', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.Neque at, nobis repudiandae dolores.']);
		CompanyServicesModel::create(['title' => 'Easy Bookings', 'image' => 'fleet-easybooking.png', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.Neque at, nobis repudiandae dolores.']);

		// add fake data for testimonials and team
		for ($i = 0; $i < 5; $i++) {
			TeamModel::create(['name' => $faker->name, 'details' => 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Temporibus neque est nemo et ipsum fugiat, ab facere adipisci. Aliquam quibusdam molestias quisquam distinctio? Culpa, voluptatem voluptates exercitationem sequi velit quaerat.', 'designation' => 'Owner']);
			Testimonial::create(['name' => $faker->name, 'details' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet animi doloribus, repudiandae iusto magnam soluta voluptates, expedita aspernatur consectetur! Ex fugit ducimus itaque, quibusdam nemo in animi quae libero repellendus!']);
		}

		// add testimonials permission for every super admin
		$users = User::where('user_type', 'S')->get();
		foreach ($users as $user) {
			$u = User::find($user->id);
			$modules = unserialize($u->module);
			array_push($modules, "15");
			$u->module = serialize($modules);
			$u->save();
		}
	}

	public function upgrade(InstalledFileManager $fileManager, Faker $faker) {

		if (file_exists(base_path('resources/lang/de'))) {
			File::deleteDirectory(base_path('resources/lang/de'));
		}
		if (file_exists(base_path('resources/lang/en'))) {
			File::deleteDirectory(base_path('resources/lang/en'));
		}
		if (file_exists(base_path('resources/lang/es'))) {
			File::deleteDirectory(base_path('resources/lang/es'));
		}
		if (file_exists(base_path('resources/lang/fr'))) {
			File::deleteDirectory(base_path('resources/lang/fr'));
		}
		if (file_exists(base_path('resources/lang/pt'))) {
			File::deleteDirectory(base_path('resources/lang/pt'));
		}

		Artisan::call('migrate');
		Artisan::call('passport:install');
		$this->move_logo();
		$this->add_columns();
		$this->users_meta();
		$this->store_insurance();
		$this->purchase_info();
		$this->assign_drivers();
		$this->vehicle_types();
		$this->api_change();
		$this->frontend_settings($faker);
		$this->booking_receipts();
		Settings::updateOrCreate(['label' => 'Date Format', 'name' => 'date_format', 'value' => 'd-m-Y']);
		$fileManager->update();
		return view('laravel_web_installer.finished');
	}

	public function move_logo() {
		$oldPath = "public/" . Hyvikk::get('icon_img');
		$newPath = 'assets/images/' . Hyvikk::get('icon_img');
		if (file_exists("public/" . Hyvikk::get('icon_img'))) {
			\File::copy($oldPath, $newPath);
		}

		$oldPath1 = "public/" . Hyvikk::get('logo_img');
		$newPath1 = 'assets/images/' . Hyvikk::get('logo_img');
		if (file_exists("public/" . Hyvikk::get('logo_img'))) {
			\File::copy($oldPath1, $newPath1);
		}
	}

	public function add_columns() {
		if (!Schema::hasColumn('bookings', 'payment')) {
			Schema::table('bookings', function ($table) {
				$table->integer('payment')->default(0);
			});
		}

		if (!Schema::hasColumn('income', 'income_id')) {
			Schema::table('income', function ($table) {
				$table->integer('income_id')->nullable();
			});
		}

		if (!Schema::hasColumn('vendors', 'udf')) {
			Schema::table('vendors', function ($table) {
				$table->text('udf')->nullable();
			});
		}

		if (!Schema::hasColumn('expense', 'exp_id')) {
			Schema::table('expense', function ($table) {
				$table->integer('exp_id')->nullable();
			});
		}
		if (!Schema::hasColumn('vehicles', 'type_id')) {
			Schema::table('vehicles', function ($table) {
				$table->integer('type_id')->nullable();
			});
		}
		if (!Schema::hasColumn('expense', 'type')) {
			Schema::table('expense', function ($table) {
				$table->string('type', 10)->default("e");
			});
		}

		if (!Schema::hasColumn('expense', 'vendor_id')) {
			Schema::table('expense', function ($table) {
				$table->integer('vendor_id')->nullable();
			});
		}

		if (!Schema::hasColumn('work_orders', 'price')) {
			Schema::table('work_orders', function ($table) {
				$table->double('price', 8, 2)->default(0);
			});
		}

		if (!Schema::hasColumn('bookings_meta', 'booking_id')) {
			Schema::table('bookings_meta', function ($table) {
				$table->renameColumn('bookings_id', 'booking_id');
			});
		}

		Settings::where('name', 'language')->update(['value' => 'English-en']);
		ApiSettings::create(['key_name' => 'max_trip', 'key_value' => '1']);
		ApiSettings::create(['key_name' => 'api_key', 'key_value' => '']);
		ApiSettings::create(['key_name' => 'db_url', 'key_value' => '']);
		ApiSettings::create(['key_name' => 'db_secret', 'key_value' => '']);
		ApiSettings::create(['key_name' => 'server_key', 'key_value' => '']);

		DB::table('email_content')->truncate();
		EmailContent::create(['key' => 'insurance', 'value' => 'vehicle insurance email content']);
		EmailContent::create(['key' => 'vehicle_licence', 'value' => 'vehicle licence email content']);
		EmailContent::create(['key' => 'driving_licence', 'value' => 'driving licence email content']);
		EmailContent::create(['key' => 'registration', 'value' => 'vehicle registration email content']);
		EmailContent::create(['key' => 'service_reminder', 'value' => 'service reminder email content']);
		EmailContent::create(['key' => 'users', 'value' => '']);
		EmailContent::create(['key' => 'options', 'value' => '']);

	}

	private function chat_settings() {
		DB::table('chat_settings')->truncate();
		ChatSettingsModel::create(['name' => 'pusher_app_id', 'value' => '']);
		ChatSettingsModel::create(['name' => 'pusher_app_key', 'value' => '']);
		ChatSettingsModel::create(['name' => 'pusher_app_secret', 'value' => '']);
		ChatSettingsModel::create(['name' => 'pusher_app_cluster', 'value' => '']);
	}

	public function upgradeTo602() {
		$this->chat_settings();
		PaymentSettings::updateOrCreate(['name' => 'paystack_secret', 'value' => null]);
		FrontendModel::updateOrCreate(['key_name' => 'booking_time', 'key_value' => 1]);
	}

	public function upgradeTo603() {

	}

	public function upgradeTo61() {

	}

	public function upgradeTo601() {
		Settings::updateOrCreate(['name' => 'income_enable_driver'], ['label' => 'Income enable for Driver', 'value' => 0]);
		Settings::updateOrCreate(['name' => 'expense_enable_driver'], ['label' => 'Expense enable for Driver', 'value' => 0]);
		$drivers = User::where('user_type', 'D')->get();
		foreach ($drivers as $driver) {
			$driver->givePermissionTo(['Transactions list', 'Transactions add', 'Transactions edit', 'Transactions delete', 'VehicleInspection add', 'Fuel add', 'Fuel edit', 'Fuel delete', 'Fuel list']);
		}
	}

	public function users_meta() {
		$users = User::get();
		$modules = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15);
		foreach ($users as $u) {

			if ($u->user_type == "C" || $u->user_type == "D") {
				$u->setMeta(["language" => "English-en"]);
			}
			$u->setMeta(["language" => "English-en", "module" => serialize($modules)]);

			$u->save();

		}
	}

	public function store_insurance() {
		$ins = DB::table('insurance')->get();
		if ($ins->toArray() != null) {
			foreach ($ins as $i) {
				$v = VehicleModel::find($i->vehicle_id);
				$v->setMeta(['ins_number' => $i->ins_number,
					'ins_exp_date' => $i->ins_exp_date,
					'documents' => $i->documents]);
				$v->save();
			}
		}
	}

	public function purchase_info() {
		$acq = DB::table('acquisition')->groupBy('vehicle_id')->get(['vehicle_id']);
		if ($acq->toArray() != null) {
			foreach ($acq as $key) {
				$vehicles[] = $key->vehicle_id;
			}

			foreach ($vehicles as $vehicle) {
				$data = VehicleModel::find($vehicle);
				$purchase_info = array();
				foreach ($data->acq as $key) {

					$purchase_info[] = ['exp_name' => $key->exp_name, 'exp_amount' => $key->exp_amount];

				}

				$data->setMeta(['purchase_info' => serialize($purchase_info)]);
				$data->save();

			}
		}

	}

	public function assign_drivers() {
		$vehicles = VehicleModel::get();
		foreach ($vehicles as $v) {
			if ($v->driver_id != null) {
				$v->setMeta(['driver_id' => $v->driver_id]);
				$v->save();
			}
		}
		if (Schema::hasColumn('vehicles', 'driver_id')) {
			Schema::table('vehicles', function ($table) {
				$table->dropColumn('driver_id');
			});
		}

	}

	public function vehicle_types() {
		VehicleTypeModel::updateOrCreate(['vehicletype' => 'Hatchback'], ['vehicletype' => 'Hatchback',
			'displayname' => 'Hatchback',
			'isenable' => 1,
			'seats' => 4]);
		VehicleTypeModel::updateOrCreate(['vehicletype' => 'Sedan'], ['vehicletype' => 'Sedan', 'displayname' => 'Sedan', 'isenable' => 1,
			'seats' => 4]);
		VehicleTypeModel::updateOrCreate(['vehicletype' => 'Mini van'], ['vehicletype' => 'Mini van', 'displayname' => 'Mini van', 'isenable' => 1,
			'seats' => 7]);
		VehicleTypeModel::updateOrCreate(['vehicletype' => 'Saloon'], ['vehicletype' => 'Saloon', 'displayname' => 'Saloon', 'isenable' => 1,
			'seats' => 4]);
		VehicleTypeModel::updateOrCreate(['vehicletype' => 'SUV'], ['vehicletype' => 'SUV', 'displayname' => 'SUV', 'isenable' => 1,
			'seats' => 4]);
		VehicleTypeModel::updateOrCreate(['vehicletype' => 'Bus'], ['vehicletype' => 'Bus', 'displayname' => 'Bus', 'isenable' => 1,
			'seats' => 40]);
		VehicleTypeModel::updateOrCreate(['vehicletype' => 'Truck'], ['vehicletype' => 'Truck', 'displayname' => 'Truck', 'isenable' => 1,
			'seats' => 3]);

		$vehicles = VehicleModel::get();
		foreach ($vehicles as $key) {
			$type[] = $key->type;
		}
		$get_types = array_unique($type);

		$vehicle_types = VehicleTypeModel::get();
		foreach ($vehicle_types as $key) {
			$all_types[] = $key->vehicletype;

			FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_base_fare', 'key_value' => Hyvikk::fare('base_fare'), 'type_id' => $key->id]);
			FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_base_km', 'key_value' => Hyvikk::fare('base_km'), 'type_id' => $key->id]);
			FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_base_time', 'key_value' => Hyvikk::fare('base_time'), 'type_id' => $key->id]);
			FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_std_fare', 'key_value' => Hyvikk::fare('std_fare'), 'type_id' => $key->id]);
			FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_weekend_base_fare', 'key_value' => Hyvikk::fare('weekend_base_fare'), 'type_id' => $key->id]);
			FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_weekend_base_km', 'key_value' => Hyvikk::fare('weekend_base_km'), 'type_id' => $key->id]);
			FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_weekend_wait_time', 'key_value' => Hyvikk::fare('weekend_wait_time'), 'type_id' => $key->id]);
			FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_weekend_std_fare', 'key_value' => Hyvikk::fare('weekend_std_fare'), 'type_id' => $key->id]);
			FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_night_base_fare', 'key_value' => Hyvikk::fare('night_base_fare'), 'type_id' => $key->id]);
			FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_night_base_km', 'key_value' => Hyvikk::fare('night_base_km'), 'type_id' => $key->id]);
			FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_night_wait_time', 'key_value' => Hyvikk::fare('night_wait_time'), 'type_id' => $key->id]);
			FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_night_std_fare', 'key_value' => Hyvikk::fare('night_std_fare'), 'type_id' => $key->id]);

		}
		foreach ($get_types as $key) {
			if (!in_array($key, $all_types)) {
				$new = VehicleTypeModel::updateOrCreate(['vehicletype' => $key], ['vehicletype' => $key, 'displayname' => $key, 'isenable' => 1,
					'seats' => 4]);

				FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_base_fare', 'key_value' => '500', 'type_id' => $new->id]);
				FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_base_km', 'key_value' => '10', 'type_id' => $new->id]);
				FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_base_time', 'key_value' => '2', 'type_id' => $new->id]);
				FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_std_fare', 'key_value' => '20', 'type_id' => $new->id]);
				FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_weekend_base_fare', 'key_value' => '500', 'type_id' => $new->id]);
				FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_weekend_base_km', 'key_value' => '10', 'type_id' => $new->id]);
				FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_weekend_wait_time', 'key_value' => '2', 'type_id' => $new->id]);
				FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_weekend_std_fare', 'key_value' => '20', 'type_id' => $new->id]);
				FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_night_base_fare', 'key_value' => '500', 'type_id' => $new->id]);
				FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_night_base_km', 'key_value' => '10', 'type_id' => $new->id]);
				FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_night_wait_time', 'key_value' => '2', 'type_id' => $new->id]);
				FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_night_std_fare', 'key_value' => '20', 'type_id' => $new->id]);

			}
		}

		foreach ($vehicles as $vehicle) {
			$vehicle_type = VehicleTypeModel::where('vehicletype', $vehicle->type)->first();
			$vehicle->type_id = $vehicle_type->id;
			$vehicle->save();
		}
	}

	public function api_change() {
		ApiSettings::updateOrCreate(['key_name' => 'google_api', 'key_value' => '1']);
		EmailContent::updateOrCreate(['key' => 'email', 'value' => 0]);
	}
}
