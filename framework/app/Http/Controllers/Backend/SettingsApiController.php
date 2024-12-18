<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Address;
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
use App\Model\FrontendModel;
use App\Model\FuelModel;
use App\Model\IncCats;
use App\Model\IncomeModel;
use App\Model\MessageModel;
use App\Model\NotesModel;
use App\Model\PartsCategoryModel;
use App\Model\PartsModel;
use App\Model\PartsUsedModel;
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
use App\Model\Vendor;
use App\Model\WorkOrderLogs;
use App\Model\WorkOrders;
use Auth;
use DB;
use Hyvikk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class SettingsApiController extends Controller {

	public function upload_documents(Request $request) {
		$validation = Validator::make($request->all(), [
			'icon_img' => 'required_if:logo_img,|image|mimes:png,jpg,jpeg',
			'logo_img' => 'required_if:icon_img,|image|mimes:png,jpg,jpeg',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			if ($request->file('icon_img') && $request->file('icon_img')->isValid()) {
				$this->upload_file($request->file('icon_img'), "value", 'icon_img');
			}

			if ($request->file('logo_img') && $request->file('logo_img')->isValid()) {
				$this->upload_file($request->file('logo_img'), "value", 'logo_img');
			}
			$data['success'] = "1";
			$data['message'] = "Image(s) uploaded successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update_front_settings(Request $request) {
		$validation = Validator::make($request->all(), [
			'about_us' => 'required|max:130',
			'customer_support' => 'required',
			'contact_number' => 'required',
			'contact_email' => 'required',
			'about_description' => 'required',
			'about_title' => 'required',
			'faq_link' => 'nullable|url',
			'cancellation_link' => 'nullable|url',
			'terms' => 'nullable|url',
			'privacy_policy' => 'nullable|url',
			'cities' => 'required|integer',
			'vehicles' => 'required|integer',
			'is_enable' => 'required|integer|in:0,1',
			'language' => 'required|in:en,es,ar',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			FrontendModel::where('key_name', 'about_us')->update(['key_value' => $request->about_us]);
			FrontendModel::where('key_name', 'contact_email')->update(['key_value' => $request->contact_email]);
			FrontendModel::where('key_name', 'contact_phone')->update(['key_value' => $request->contact_number]);
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
			FrontendModel::where('key_name', 'cancellation')->update(['key_value' => $request->cancellation_link]);
			FrontendModel::where('key_name', 'terms')->update(['key_value' => $request->terms]);
			FrontendModel::where('key_name', 'privacy_policy')->update(['key_value' => $request->privacy_policy]);
			FrontendModel::where('key_name', 'enable')->update(['key_value' => $request->is_enable]);
			$enable = 'no';
			if ($request->is_enable == 1) {
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
			$data['success'] = "1";
			$data['message'] = "Frontend settings updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function get_front_settings() {

		$details = array(
			'is_enable' => (Hyvikk::frontend('enable') == 1) ? 1 : 0,
			'is_enable_value' => (Hyvikk::frontend('enable') == 1) ? "Enable" : "Disable",
			'about_us' => Hyvikk::frontend('about_us'),
			'customer_support' => Hyvikk::frontend('customer_support'),
			'contact_number' => Hyvikk::frontend('contact_phone'),
			'contact_email' => Hyvikk::frontend('contact_email'),
			'about_description' => Hyvikk::frontend('about_description'),
			'about_title' => Hyvikk::frontend('about_title'),
			'language' => Hyvikk::frontend('language'),
			'faq_link' => Hyvikk::frontend('faq_link'),
			'cities' => Hyvikk::frontend('cities'),
			'vehicles' => Hyvikk::frontend('vehicles'),
			'cancellation_link' => Hyvikk::frontend('cancellation'),
			'terms' => Hyvikk::frontend('terms'),
			'privacy_policy' => Hyvikk::frontend('privacy_policy'),
			'facebook' => Hyvikk::frontend('facebook'),
			'twitter' => Hyvikk::frontend('twitter'),
			'instagram' => Hyvikk::frontend('instagram'),
			'linkedin' => Hyvikk::frontend('linkedin'),
		);
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'app_name' => 'required',
			'email' => 'required|email',
			'address1' => 'required',
			'address2' => 'required',
			'city' => 'required',
			'state' => 'required',
			'country' => 'required',
			'distance_format' => 'required|in:km,miles',
			'fuel_unit' => 'required|in:gallon,liter',
			'language' => 'required',
			'icon_img' => 'image|mimes:jpg,png,gif,jpeg',
			'logo_img' => 'image|mimes:jpg,png,gif,jpeg',
			'time_interval' => 'required|integer',
			'currency' => 'required',
			'date_format' => 'required',
			'tax_no' => 'required',
			'invoice_text' => 'required',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$date_setting = "d-m-Y";
			if ($request->date_format == "YYYY-MM-DD") {
				$date_setting = 'Y-m-d';
			}
			if ($request->date_format == "MM-DD-YYYY") {
				$date_setting = 'm-d-Y';
			}
			Settings::where('name', 'app_name')->update(['value' => $request->app_name]);
			Settings::where('name', 'email')->update(['value' => $request->email]);
			Settings::where('name', 'badd1')->update(['value' => $request->address1]);
			Settings::where('name', 'badd2')->update(['value' => $request->address2]);
			Settings::where('name', 'city')->update(['value' => $request->city]);
			Settings::where('name', 'state')->update(['value' => $request->state]);
			Settings::where('name', 'country')->update(['value' => $request->country]);
			Settings::where('name', 'dis_format')->update(['value' => $request->distance_format]);
			Settings::where('name', 'fuel_unit')->update(['value' => $request->fuel_unit]);
			Settings::where('name', 'time_interval')->update(['value' => $request->time_interval]);
			Settings::where('name', 'currency')->update(['value' => $request->currency]);
			Settings::where('name', 'date_format')->update(['value' => $date_setting]);
			Settings::where('name', 'tax_no')->update(['value' => $request->tax_no]);
			Settings::where('name', 'invoice_text')->update(['value' => $request->invoice_text]);

			Settings::where('name', 'language')->update(['value' => $request->language]);
			$user = Auth::user();
			$user->language = $request->language;
			$user->save();

			$taxes = json_encode($request->tax);
			Settings::where('name', 'tax_charge')->update(['value' => $taxes]);

			$app_name = str_replace(" ", "_", $request->app_name);
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

			$data['success'] = "1";
			$data['message'] = "Settings updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	private function upload_file($file, $field, $name) {
		$destinationPath = './assets/images'; // upload path
		$extension = $file->getClientOriginalExtension();
		$fileName1 = Str::uuid() . '.' . $extension;

		$x = Settings::where("name", $name)->first();
		if (file_exists('./uploads/' . $x->$field) && !is_dir('./uploads/' . $x->$field)) {
			unlink('./uploads/' . $x->$field);
		}
		$file->move($destinationPath, $fileName1);

		$x->update([$field => $fileName1]);

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
		WorkOrderLogs::whereNotNull('id')->delete();
		WorkOrders::whereNotNull('id')->delete();
		EmailContent::where('key', 'users')->update(['value' => '']);
		EmailContent::where('key', 'options')->update(['value' => '']);
		DB::table('notifications')->truncate();

		$data['success'] = "1";
		$data['message'] = "Database cleared successfully!";
		$data['data'] = "";
		return $data;
	}

	public function get_general_settings() {
		$user = Auth::user();
		$date_setting = "DD-MM-YYYY";
		if (Hyvikk::get('date_format') == 'Y-m-d') {
			$date_setting = "YYYY-MM-DD";
		}
		if (Hyvikk::get('date_format') == 'm-d-Y') {
			$date_setting = "MM-DD-YYYY";
		}
		if ($user->language != null) {
			$lang = $user->language;
		} else {
			$lang = Hyvikk::get("language");
		}
		$language = explode('-', $lang);
		$taxes = null;
		$new_taxes = array();
		if (Hyvikk::get('tax_charge') != "null") {
			$taxes = json_decode(Hyvikk::get('tax_charge'), true);
			foreach ($taxes as $key => $val) {
				$new_taxes[] = array(
					'name' => $key,
					'value' => $val,
				);
			}
		}
		$details = array(
			'app_name' => Hyvikk::get('app_name'),
			'email' => Hyvikk::get('email'),
			'address1' => Hyvikk::get('badd1'),
			'address2' => Hyvikk::get('badd2'),
			'city' => Hyvikk::get('city'),
			'state' => Hyvikk::get('state'),
			'country' => Hyvikk::get('country'),
			'distance_format' => Hyvikk::get("dis_format"),
			'fuel_unit' => Hyvikk::get("fuel_unit"),
			'time_interval' => Hyvikk::get('time_interval'),
			'icon_image' => asset('assets/images/' . Hyvikk::get('icon_img')),
			'logo_image' => asset('assets/images/' . Hyvikk::get('logo_img')),
			'currency' => Hyvikk::get('currency'),
			'date_format' => $date_setting,
			'tax_no' => Hyvikk::get('tax_no'),
			'invoice_text' => Hyvikk::get('invoice_text'),
			'language' => ($language[1] == "en") ? "en-us" : $language[1],
			'selected_lang' => $lang,
			// 'tax' => $taxes,
			'tax' => $new_taxes,
		);

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
