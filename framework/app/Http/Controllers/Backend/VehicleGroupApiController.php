<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\VehicleGroupModel;
use Auth;
use Illuminate\Http\Request;
use Validator;

class VehicleGroupApiController extends Controller {

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

			VehicleGroupModel::whereIn('id', $request->ids)->delete();
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

			VehicleGroupModel::find($request->id)->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'name' => 'required',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$group = VehicleGroupModel::find($request->id);
			$group->name = $request->name;
			$group->description = $request->description;
			$group->note = $request->note;
			$group->save();

			$data['success'] = "1";
			$data['message'] = "Vehicle group updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'name' => 'required',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			VehicleGroupModel::create([
				"name" => $request->name,
				"description" => $request->description,
				"note" => $request->note,
			]);

			$data['success'] = "1";
			$data['message'] = "Vehicle group added successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function groups() {
		if (Auth::user()->user_type == "S" || Auth::user()->group_id == null) {
			$groups = VehicleGroupModel::get();
		} else {
			$groups = VehicleGroupModel::where('id', Auth::user()->group_id)->get();
		}
		$details = array();
		foreach ($groups as $row) {
			$details[] = array(
				"id" => $row->id,
				"name" => $row->name,
				"description" => $row->description,
				"note" => $row->note,
			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
