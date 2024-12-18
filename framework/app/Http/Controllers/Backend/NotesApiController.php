<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\NotesModel;
use App\Model\User;
use App\Model\VehicleModel;
use Auth;
use Illuminate\Http\Request;
use Validator;

class NotesApiController extends Controller {

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

			NotesModel::whereIn('id', $request->ids)->delete();

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
			NotesModel::find($request->id)->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'vehicle_id' => 'required|integer',
			'person_id' => 'required|integer',
			'note' => 'required',
			'status' => 'required',
			'submitted_on' => 'required|date|date_format:Y-m-d',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$note = NotesModel::find($request->id);
			$note->vehicle_id = $request->vehicle_id;
			$note->customer_id = $request->person_id;
			$note->note = $request->note;
			$note->submitted_on = date('Y-m-d', strtotime($request->submitted_on));
			$note->status = $request->status;
			$note->save();

			$data['success'] = "1";
			$data['message'] = "Note updated successfully!";
			$data['data'] = "";
		}
		return $data;

	}

	public function dropdowns() {
		$vehicle_details = array();
		$person_details = array();
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$vehicles = VehicleModel::whereIn_service("1")->get();
		} else {
			$vehicles = VehicleModel::where('group_id', Auth::user()->group_id)->whereIn_service("1")->get();
		}
		$persons = User::where('user_type', '!=', 'C')->where('deleted_at', null)->get();
		foreach ($vehicles as $row) {
			$vehicle_details[] = array(
				'vehicle_id' => $row->id,
				'vehicle' => $row->make_name . " - " . $row->model_name . " - " . $row->license_plate,
			);
		}
		foreach ($persons as $row) {
			$person_details[] = array(
				'person_id' => $row->id,
				'person' => $row->name,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = array(
			'vehicles' => $vehicle_details,
			'person_incharge' => $person_details,
		);
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'vehicle_id' => 'required|integer',
			'person_id' => 'required|integer',
			'note' => 'required',
			'status' => 'required',
			'submitted_on' => 'required|date|date_format:Y-m-d',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			NotesModel::create([
				'vehicle_id' => $request->vehicle_id,
				'customer_id' => $request->person_id,
				'note' => $request->note,
				'submitted_on' => date('Y-m-d', strtotime($request->submitted_on)),
				'status' => $request->status,
			]);

			$data['success'] = "1";
			$data['message'] = "Note added successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function notes() {
		$details = array();
		if (Auth::User()->user_type == "S") {
			$records = NotesModel::orderBy('id', 'desc')->get();
		} else {
			$records = NotesModel::where('customer_id', Auth::id())->orderBy('id', 'desc')->get();
		}

		foreach ($records as $row) {
			$image = asset("assets/images/vehicle.jpeg");
			if ($row->vehicle->vehicle_image != null) {
				$image = asset('uploads/' . $row->vehicle->vehicle_image);
			}

			$details[] = array(
				'id' => $row->id,
				'vehicle_id' => $row->vehicle_id,
				'person_id' => $row->customer_id,
				'note' => $row->note,
				'submitted_on' => $row->submitted_on,
				'status' => $row->status,
				'person_incharge' => $row->customer->name,
				'image' => $image,
				'unit' => $row->vehicle_id,
				'vehicle' => $row->vehicle->year . " " . $row->vehicle->make_name . " - " . $row->vehicle->model_name,
				'vin' => $row->vehicle->vin,
				'plate' => $row->vehicle->license_plate,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
