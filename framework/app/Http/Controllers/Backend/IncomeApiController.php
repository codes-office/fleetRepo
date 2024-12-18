<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\IncCats;
use App\Model\IncomeModel;
use App\Model\VehicleModel;
use Auth;
use Hyvikk;
use Illuminate\Http\Request;
use Validator;

class IncomeApiController extends Controller {
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

			IncomeModel::whereIn('id', $request->ids)->delete();

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
			IncomeModel::find($request->id)->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'income_type_id' => 'required|integer',
			'total_amount' => 'required|numeric',
			'vehicle_id' => 'required|required',
			'mileage' => 'required|numeric',
			'date' => 'required|date',
			'total_tax_percent' => 'required|numeric',
			'tax_charge_rs' => 'required|numeric',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			IncomeModel::create([
				"vehicle_id" => $request->vehicle_id,
				"amount" => $request->total_amount,
				"user_id" => Auth::id(),
				"date" => date('Y-m-d', strtotime($request->date)),
				"mileage" => $request->mileage,
				"income_cat" => $request->income_type_id,
				"tax_percent" => $request->total_tax_percent,
				"tax_charge_rs" => $request->tax_charge_rs,
			]);
			$v = VehicleModel::find($request->vehicle_id);
			$v->mileage = $request->mileage;
			$v->save();

			$data['success'] = "1";
			$data['message'] = "Income record added successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function income_dropdowns() {
		$vehicle_details = array();
		$income_types = array();
		$tax = 0;
		$user = Auth::user();
		if ($user->group_id == null || $user->user_type == "S") {
			$vehicles = VehicleModel::whereIn_service(1)->get();
		} else {
			$vehicles = VehicleModel::whereIn_service(1)->where('group_id', $user->group_id)->get();
		}
		$types = IncCats::get();
		foreach ($vehicles as $row) {
			$vehicle_details[] = array(
				'vehicle_id' => $row->id,
				'vehicle' => $row->make_name . " - " . $row->model_name . " - " . $row->license_plate,
			);
		}
		foreach ($types as $row) {
			$income_types[] = array(
				'income_type_id' => $row->id,
				'type' => $row->name,
			);
		}

		if (Hyvikk::get('tax_charge') != "null") {
			$taxes = json_decode(Hyvikk::get('tax_charge'), true);
			foreach ($taxes as $key => $val) {
				$tax += $val;
			}
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = array(
			"vehicle_details" => $vehicle_details,
			"income_types" => $income_types,
			"total_tax_percent" => $tax,
		);
		return $data;
	}

	public function income_records(Request $request) {
		$validation = Validator::make($request->all(), [
			'from_date' => 'required|date|date_format:Y-m-d',
			'to_date' => 'required|date|date_format:Y-m-d',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$date1 = date('Y-m-d', strtotime($request->from_date));
			$date2 = date('Y-m-d', strtotime($request->to_date));
			$user = Auth::user();
			if ($user->group_id == null || $user->user_type == "S") {
				$vehicles = VehicleModel::whereIn_service(1)->get();

			} else {
				$vehicles = VehicleModel::whereIn_service(1)->where('group_id', $user->group_id)->get();
			}
			$vehicle_ids = $vehicles->pluck('id')->toArray();

			$income = IncomeModel::whereIn('vehicle_id', $vehicle_ids)->whereBetween('date', [$date1, $date2])->get();
			$total_today = IncomeModel::whereIn('vehicle_id', $vehicle_ids)->whereDate('date', date('Y-m-d'))->sum('amount');

			$details = array();
			foreach ($income as $row) {
				$details[] = array(
					'id' => $row->id,
					'vehicle_make' => $row->vehicle->make_name,
					'vehicle_model' => $row->vehicle->model_name,
					'vehicle_license_plate' => $row->vehicle->license_plate,
					'income_type' => $row->category->name,
					'date' => $row->date,
					'amount' => Hyvikk::get('currency') . " " . $row->amount,
					'mileage' => $row->mileage . " " . Hyvikk::get('dis_format'),
				);
			}

			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = array(
				"income" => $details,
				"total_today" => Hyvikk::get('currency') . " " . $total_today,
				"from_date" => $request->from_date,
				"to_date" => $request->to_date,
			);
		}
		return $data;

	}

	public function income() {
		$user = Auth::user();
		if ($user->group_id == null || $user->user_type == "S") {
			$vehicles = VehicleModel::whereIn_service(1)->get();
		} else {
			$vehicles = VehicleModel::whereIn_service(1)->where('group_id', $user->group_id)->get();
		}
		$vehicle_ids = $vehicles->pluck('id')->toArray();
		$income = IncomeModel::whereIn('vehicle_id', $vehicle_ids)->whereDate('date', date('Y-m-d'));
		$today = $income->get();
		$total = $income->sum('amount');
		$details = array();
		foreach ($today as $row) {
			$details[] = array(
				'id' => $row->id,
				'vehicle_make' => $row->vehicle->make_name,
				'vehicle_model' => $row->vehicle->model_name,
				'vehicle_license_plate' => $row->vehicle->license_plate,
				'income_type' => $row->category->name,
				'date' => $row->date,
				'amount' => Hyvikk::get('currency') . " " . $row->amount,
				'mileage' => $row->mileage . " " . Hyvikk::get('dis_format'),
			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = array(
			"income" => $details,
			'total_today' => Hyvikk::get('currency') . " " . $total,
		);
		return $data;
	}
}
