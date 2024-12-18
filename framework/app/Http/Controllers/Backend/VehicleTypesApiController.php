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
use Illuminate\Http\Request;
use Validator;

class VehicleTypesApiController extends Controller {
	public function upload_documents(Request $request, $id) {
		$validation = Validator::make($request->all(), [
			// 'id' => 'required|integer',
			'icon' => 'required|image|mimes:jpg,png,jpeg',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$vehicle_type = VehicleTypeModel::find($id);
			$file = $request->file('icon');
			if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
				$destinationPath = './uploads'; // upload path
				$extension = $file->getClientOriginalExtension();

				$fileName1 = 'vehicle_type_' . time() . '.' . $extension;

				$file->move($destinationPath, $fileName1);
				$vehicle_type->icon = $fileName1;
				$vehicle_type->save();
			}

			$data['success'] = "1";
			$data['message'] = "Icon uploaded successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function bulk_delete(Request $request) {
		$validation = Validator::make($request->all(), [
			'ids' => 'required|array',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			VehicleTypeModel::whereIn('id', $request->ids)->delete();

			$data['success'] = "1";
			$data['message'] = "Records deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function delete(Request $request) {

		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			VehicleTypeModel::find($request->id)->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'vehicletype' => 'required|unique:vehicle_types,vehicletype,' . \Request::get("id") . ',id,deleted_at,NULL',
			'displayname' => 'required',
			'icon' => 'nullable|image|mimes:jpg,png,jpeg',
			'isenable' => 'required|integer',
			'seats' => 'required|integer',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			$vehicle_type = VehicleTypeModel::find($request->id);
			$old_type = strtolower(str_replace(' ', '', $vehicle_type->vehicletype));
			$vehicle_type->update([
				'vehicletype' => $request->vehicletype,
				'displayname' => $request->displayname,
				'isenable' => $request->isenable,
				'seats' => $request->seats,
			]);

			$file = $request->file('icon');
			if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
				$destinationPath = './uploads'; // upload path
				$extension = $file->getClientOriginalExtension();

				$fileName1 = 'vehicle_type_' . time() . '.' . $extension;

				$file->move($destinationPath, $fileName1);
				$vehicle_type->icon = $fileName1;
				$vehicle_type->save();
			}

			$settings = FareSettings::where('type_id', $request->id)->get();

			foreach ($settings as $key) {
				// update key_name in fare settings
				$key->key_name = str_replace($old_type, strtolower(str_replace(' ', '', $request->vehicletype)), $key->key_name);
				$key->save();
			}
			$data['success'] = "1";
			$data['message'] = "Vehicle Type updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'vehicletype' => 'required|unique:vehicle_types,vehicletype',
			'displayname' => 'required',
			'icon' => 'nullable|image|mimes:jpg,png,jpeg',
			'isenable' => 'required|integer',
			'seats' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			$new = VehicleTypeModel::create([
				'vehicletype' => $request->vehicletype,
				'displayname' => $request->displayname,
				'isenable' => $request->isenable,
				'seats' => $request->seats,
			]);
			$file = $request->file('icon');

			if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
				$destinationPath = './uploads'; // upload path
				$extension = $file->getClientOriginalExtension();

				$fileName1 = 'vehicle_type_' . time() . '.' . $extension;

				$file->move($destinationPath, $fileName1);
				$new->icon = $fileName1;
				$new->save();
			}

			$key = $request->vehicletype;
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
			$data['success'] = "1";
			$data['message'] = "Vehicle Type added successfully!";
			$data['data'] = array('id' => $new->id);
		}
		return $data;
	}

	public function types() {
		$records = VehicleTypeModel::orderBy('id', 'desc')->get();
		$details = array();
		foreach ($records as $row) {
			$image = asset('assets/images/vehicle.jpeg');
			if ($row->icon != null) {
				$image = asset('uploads/' . $row->icon);
			}
			$details[] = array(
				'id' => $row->id,
				'vehicletype' => $row->vehicletype,
				'displayname' => $row->displayname,
				'seats' => $row->seats,
				'icon' => $image,
				'isenable' => ($row->isenable == 1) ? 1 : 0,
				'enable' => ($row->isenable == 1) ? "Yes" : "No",
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
