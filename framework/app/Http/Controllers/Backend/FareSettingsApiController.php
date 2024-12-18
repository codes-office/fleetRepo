<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\FareSettings;
use App\Model\VehicleTypeModel;
use Hyvikk;
use Illuminate\Http\Request;
use Validator;

class FareSettingsApiController extends Controller {

	public function store_fare_settings(Request $request) {
		$key = $request->key;

		$validation = Validator::make($request->all(), [
			$key . '_base_fare' => 'required|numeric',
			$key . '_base_km' => 'required|numeric',
			$key . '_std_fare' => 'required|numeric',
			$key . '_base_time' => 'required|numeric',
			$key . '_weekend_base_fare' => 'required|numeric',
			$key . '_weekend_base_km' => 'required|numeric',
			$key . '_weekend_wait_time' => 'required|numeric',
			$key . '_weekend_std_fare' => 'required|numeric',
			$key . '_night_base_fare' => 'required|numeric',
			$key . '_night_base_km' => 'required|numeric',
			$key . '_night_wait_time' => 'required|numeric',
			$key . '_night_std_fare' => 'required|numeric',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$base_fare = $key . '_base_fare';
			$base_km = $key . '_base_km';
			$std_fare = $key . '_std_fare';
			$base_time = $key . '_base_time';
			$weekend_base_fare = $key . '_weekend_base_fare';
			$weekend_base_km = $key . '_weekend_base_km';
			$weekend_wait_time = $key . '_weekend_wait_time';
			$weekend_std_fare = $key . '_weekend_std_fare';
			$night_base_fare = $key . '_night_base_fare';
			$night_base_km = $key . '_night_base_km';
			$night_wait_time = $key . '_night_wait_time';
			$night_std_fare = $key . '_night_std_fare';
			// dd($request->$test);
			FareSettings::where('key_name', $key . '_base_fare')->update(['key_value' => $request->$base_fare]);
			FareSettings::where('key_name', $key . '_base_km')->update(['key_value' => $request->$base_km]);
			FareSettings::where('key_name', $key . '_std_fare')->update(['key_value' => $request->$std_fare]);
			FareSettings::where('key_name', $key . '_base_time')->update(['key_value' => $request->$base_time]);
			FareSettings::where('key_name', $key . '_weekend_base_fare')->update(['key_value' => $request->$weekend_base_fare]);
			FareSettings::where('key_name', $key . '_weekend_base_km')->update(['key_value' => $request->$weekend_base_km]);
			FareSettings::where('key_name', $key . '_weekend_wait_time')->update(['key_value' => $request->$weekend_wait_time]);
			FareSettings::where('key_name', $key . '_weekend_std_fare')->update(['key_value' => $request->$weekend_std_fare]);
			FareSettings::where('key_name', $key . '_night_base_fare')->update(['key_value' => $request->$night_base_fare]);
			FareSettings::where('key_name', $key . '_night_base_km')->update(['key_value' => $request->$night_base_km]);
			FareSettings::where('key_name', $key . '_night_wait_time')->update(['key_value' => $request->$night_wait_time]);
			FareSettings::where('key_name', $key . '_night_std_fare')->update(['key_value' => $request->$night_std_fare]);

			$data['success'] = "1";
			$data['message'] = "Fare settings updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function get_fare_settings() {
		$vehicle_types = VehicleTypeModel::select('id', 'vehicletype', 'displayname', 'icon', 'seats')->where('isenable', 1)->get();

		$vehicle_type_data = array();
		$setings = FareSettings::all();
		foreach ($vehicle_types as $vehicle_type) {

			$type = strtolower(str_replace(" ", "", $vehicle_type->vehicletype));
			$vehicle_type_data[] = array(
				'id' => $vehicle_type->id,
				'key' => $type,
				'displayname' => $vehicle_type->displayname,
				'base_fare' => Hyvikk::fare($type . '_base_fare'), //done
				'base_km' => Hyvikk::fare($type . '_base_km'),
				'std_fare' => Hyvikk::fare($type . '_std_fare'),
				'base_time' => Hyvikk::fare($type . '_base_time'),
				'weekend_base_fare' => Hyvikk::fare($type . '_weekend_base_fare'),
				'weekend_base_km' => Hyvikk::fare($type . '_weekend_base_km'),
				'weekend_wait_time' => Hyvikk::fare($type . '_weekend_wait_time'),
				'weekend_std_fare' => Hyvikk::fare($type . '_weekend_std_fare'),
				'night_base_fare' => Hyvikk::fare($type . '_night_base_fare'),
				'night_base_km' => Hyvikk::fare($type . '_night_base_km'),
				'night_wait_time' => Hyvikk::fare($type . '_night_wait_time'),
				'night_std_fare' => Hyvikk::fare($type . '_night_std_fare'),
			);

		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $vehicle_type_data;
		return $data;
	}
}
