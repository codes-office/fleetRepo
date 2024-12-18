<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\ApiSettings;
use Edujugon\PushNotification\PushNotification;
use Exception;
use Hyvikk;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Validator;

class ApiSettingsController extends Controller {

	public function store_api(Request $request) {
		$validation = Validator::make($request->all(), [
			"api_key" => "required",
		]);

		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			$key = $request->api_key;
			$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=40.714224,-73.961452&key=' . $key;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
			$response = json_decode($result, true);
			// dd($response);
			if ($response['status'] != "OK" && $response['error_message']) {
				$data['success'] = "0";
				$data['message'] = $response['error_message'];
				$data['data'] = "";
			}
			if ($response['status'] == "OK") {
				ApiSettings::where('key_name', 'api_key')->update(['key_value' => $key]);
				$data['success'] = "1";
				$data['message'] = "API key saved successfully.";
				$data['data'] = "";
			} else {
				$data['success'] = "0";
				$data['message'] = "Invalid API key please try again";
				$data['data'] = "";
			}
		}
		return $data;
	}

	public function store_server_key(Request $request) {
		$validation = Validator::make($request->all(), [
			"server_key" => "required",
		]);

		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			$key = $request->server_key;
			$env = "server_key=" . $key;
			if (!env('server_key')) {
				// dd('test');
				file_put_contents(base_path('.env'), $env . PHP_EOL, FILE_APPEND);
			}
			if (env('server_key')) {

				file_put_contents(base_path('.env'), str_replace(
					'server_key=' . env('server_key'), 'server_key=' . $key, file_get_contents(base_path('.env'))));
			}

			return redirect('backend/test-key');
		}
	}

	public function test_key() {
		try {
			// dd(123);
			$notification = new PushNotification('fcm');
			$notification->setMessage(['testing'])
				->setApiKey(env('server_key'))
				->setDevicesToken(['d5Av2XvAAns:APA91bGH34jdo6UlKCLsf724FMGhlZhTFGCBhmP2pON5fNit7p245RFLjGF24wa_4kIO3kJ-6hHM3aYHPPAfVvFyUX78KbzrPMY18TynUHuYREr3HJuIHbu56BmSNViw6-CnUYn3DZST'])
				->send();

			// $notification = PushNotification::app('appNameAndroid')
			//     ->to('d5Av2XvAAns:APA91bGH34jdo6UlKCLsf724FMGhlZhTFGCBhmP2pON5fNit7p245RFLjGF24wa_4kIO3kJ-6hHM3aYHPPAfVvFyUX78KbzrPMY18TynUHuYREr3HJuIHbu56BmSNViw6-CnUYn3DZST')
			//     ->send('testing');
			ApiSettings::where('key_name', 'server_key')->update(['key_value' => env('server_key')]);
			// dd($notification);
			$data['success'] = "1";
			$data['message'] = "Legacy server key stored successfully.";
			$data['data'] = "";
		} catch (Exception $e) {
			$data['success'] = "0";
			$data['message'] = "Legacy server key is invalid, Try again!";
			$data['data'] = "";
		}
		return $data;
	}

	public function firebase_settings(Request $request) {
		$validation = Validator::make($request->all(), [
			"db_url" => "required",
			'db_secret' => 'required',
		]);

		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			$db_url = $request->get('db_url');
			$db_secret = $request->get('db_secret');
			$url = "db_url=" . $db_url;

			if (!env('db_url')) {
				// dd('test');
				file_put_contents(base_path('.env'), $url . PHP_EOL, FILE_APPEND);
			}
			if (env('db_url')) {

				file_put_contents(base_path('.env'), str_replace(
					'db_url=' . env('db_url'), 'db_url=' . $db_url, file_get_contents(base_path('.env'))));
			}

			$secret = "db_secret=" . $db_secret;
			if (!env('db_secret')) {
				// dd('not exist');
				file_put_contents(base_path('.env'), $secret . PHP_EOL, FILE_APPEND);
			}
			if (env('db_secret')) {
				// dd("exist");
				file_put_contents(base_path('.env'), str_replace(
					'db_secret=' . env('db_secret'), 'db_secret=' . $db_secret, file_get_contents(base_path('.env'))));
			}
			return redirect()->route('firebase');
		}
	}

	public function fb_create() {
		try
		{
			$factory = (new Factory())
				->withDatabaseUri(env('db_url'));
			$database = $factory->createDatabase();

			$database->getReference('testing')->set([
				'name' => 'Fleet Testing',
			]);

			$data = $database->getReference('testing');
			$details = $data->getValue();

			// Firebase::set('/test/', ["testing"]);
			// $firebase = Firebase::get('/test/');
			// $details = json_decode($firebase, true);

			if (isset($details['error']) || $details == null) {
				// dd("no records");
				$data['success'] = "0";
				$data['message'] = "Firebase credentials does not matched, Try again!";
				$data['data'] = "";
			} else {
				ApiSettings::where('key_name', 'db_secret')->update(['key_value' => env('db_secret')]);
				ApiSettings::where('key_name', 'db_url')->update(['key_value' => env('db_url')]);
				$data['success'] = "1";
				$data['message'] = "Firebase settings updated successfully!";
				$data['data'] = "";
			}
		} catch (Exception $e) {
			$data['success'] = "0";
			$data['message'] = "Firebase credentials does not matched, Try again!";
			$data['data'] = "";
		}

		return $data;
	}

	public function update_api_setting(Request $request) {
		$validation = Validator::make($request->all(), [
			"api" => "required|integer",
			"anyone_register" => "integer",
			"driver_review" => "required|integer",
			"booking_days" => "required|integer",
			"cancel_days" => "required|integer",
			"google_api" => "required|integer",
			'max_trip_days' => 'required|integer',
		]);

		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			if (($request->anyone_register == 1 || $request->driver_review == 1) && ($request->api == 0)) {
				$data['success'] = "0";
				$data['message'] = "api must be enabled to enable driver_review or anyone_register";
				$data['data'] = "";
			} else {
				ApiSettings::where('key_name', 'api')->update(['key_value' => $request->api]);
				ApiSettings::where('key_name', 'max_trip')->update(['key_value' => $request->max_trip_days]);
				ApiSettings::where('key_name', 'google_api')->update(['key_value' => $request->google_api]);
				ApiSettings::where('key_name', 'anyone_register')->update(['key_value' => $request->anyone_register]);
				ApiSettings::where('key_name', 'driver_review')->update(['key_value' => $request->driver_review]);
				ApiSettings::where('key_name', 'booking')->update(['key_value' => $request->booking_days]);
				ApiSettings::where('key_name', 'cancel')->update(['key_value' => $request->cancel_days]);
				ApiSettings::where('key_name', 'region_availability')->update(['key_value' => $request->region_availability]);

				$data['success'] = "1";
				$data['message'] = "Api Settings updated successfully.";
				$data['data'] = "";
			}
		}
		return $data;
	}

	public function get_api_settings() {
		$data['success'] = "1";
		$data['message'] = "Data Received.";
		$data['data'] = array(
			'general_settings' => array(
				'api' => (Hyvikk::api('api') == 1) ? 1 : 0,
				'google_api' => (Hyvikk::api('google_api') == 1) ? 1 : 0,
				'anyone_register' => (Hyvikk::api('anyone_register') == 1) ? 1 : 0,
				'driver_review' => (Hyvikk::api('driver_review') == 1) ? 1 : 0,
				'region_availability' => Hyvikk::api('region_availability'),
				'booking_days' => Hyvikk::api('booking'),
				'cancel_days' => Hyvikk::api('cancel'),
				'max_trip_days' => Hyvikk::api('max_trip'),
			),
			'firebase_settings' => array(
				'db_url' => Hyvikk::api('db_url'),
				'db_secret' => Hyvikk::api('db_secret'),
			),
			'app_notification' => array(
				'server_key' => Hyvikk::api('server_key'),
			),
			'driver_maps' => array(
				'api_key' => Hyvikk::api('api_key'),
			),
		);

		return $data;
	}
}
