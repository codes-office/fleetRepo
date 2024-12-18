<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\ExpCats;
use App\Model\Expense;
use App\Model\ServiceItemsModel;
use App\Model\VehicleModel;
use App\Model\Vendor;
use Auth;
use Hyvikk;
use Illuminate\Http\Request;
use Validator;

class ExpenseApiController extends Controller {

	public function vehicle_expenses($id) {

		$expense = Expense::where('vehicle_id', $id)->get();
		$total_today = Expense::where('vehicle_id', $id)->sum('amount');

		$details = array();
		foreach ($expense as $row) {
			if ($row->type == "s") {
				$category = $row->service->description;
			} else {
				$category = $row->category->name;
			}

			$details[] = array(
				'id' => $row->id,
				'vehicle_make' => $row->vehicle->make_name,
				'vehicle_model' => $row->vehicle->model_name,
				'vehicle_license_plate' => $row->vehicle->license_plate,
				'expense_type' => $category,
				'vendor' => ($row->vendor_id) ? $row->vendor->name : null,
				'date' => $row->date,
				'amount' => Hyvikk::get('currency') . " " . $row->amount,
				'note' => $row->comment,
			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = array(
			"expense" => $details,
			"total" => Hyvikk::get('currency') . " " . $total_today,
		);

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

			Expense::whereIn('id', $request->ids)->delete();

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
			Expense::find($request->get('id'))->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'vehicle_id' => 'required|integer',
			'expense_type_id' => 'required|integer',
			'amount' => 'required|numeric',
			'type' => 'required|in:s,e',
			'date' => 'required|date',
			'vendor_id' => 'nullable|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			Expense::create([
				"vehicle_id" => $request->vehicle_id,
				"amount" => $request->amount,
				"user_id" => Auth::id(),
				"date" => date('Y-m-d', strtotime($request->date)),
				"comment" => $request->note,
				"expense_type" => $request->expense_type_id,
				"type" => $request->type,
				"vendor_id" => $request->vendor_id,
			]);

			$data['success'] = "1";
			$data['message'] = "Expense record added successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function expense_dropdowns() {
		$vehicle_details = array();
		$expense_types = array();
		$service_items = array();
		$vendors = array();

		$user = Auth::user();
		if ($user->group_id == null || $user->user_type == "S") {
			$vehicles = VehicleModel::whereIn_service(1)->get();
		} else {
			$vehicles = VehicleModel::whereIn_service(1)->where('group_id', $user->group_id)->get();
		}
		$types = ExpCats::get();
		$items = ServiceItemsModel::get();
		$vendor_details = Vendor::get();

		foreach ($vehicles as $row) {
			$vehicle_details[] = array(
				'vehicle_id' => $row->id,
				'vehicle' => $row->make_name . " - " . $row->model_name . " - " . $row->license_plate,
			);
		}
		foreach ($types as $row) {
			$expense_types[] = array(
				'exp_type_id' => $row->id,
				'type' => $row->name,
			);
		}
		foreach ($vendor_details as $row) {
			$vendors[] = array(
				'vendor_id' => $row->id,
				'name' => $row->name,
			);
		}
		foreach ($items as $row) {
			$service_items[] = array(
				'exp_type_id' => $row->id,
				'type' => $row->description,
			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = array(
			"vehicle_details" => $vehicle_details,
			"expense_types" => $expense_types,
			"service_items" => $service_items,
			"vendors" => $vendors,
		);
		return $data;
	}

	public function expense_records(Request $request) {
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

			$expense = Expense::whereIn('vehicle_id', $vehicle_ids)->whereBetween('date', [$date1, $date2])->get();
			$total_today = Expense::whereIn('vehicle_id', $vehicle_ids)->whereDate('date', date('Y-m-d'))->sum('amount');

			$details = array();
			foreach ($expense as $row) {
				if ($row->type == "s") {
					$category = $row->service->description;
				} else {
					$category = $row->category->name;
				}

				$details[] = array(
					'id' => $row->id,
					'vehicle_make' => $row->vehicle->make_name,
					'vehicle_model' => $row->vehicle->model_name,
					'vehicle_license_plate' => $row->vehicle->license_plate,
					'expense_type' => $category,
					'vendor' => ($row->vendor_id) ? $row->vendor->name : null,
					'date' => $row->date,
					'amount' => Hyvikk::get('currency') . " " . $row->amount,
					'note' => $row->comment,
				);
			}

			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = array(
				"expense" => $details,
				"total_today" => Hyvikk::get('currency') . " " . $total_today,
				"from_date" => $request->from_date,
				"to_date" => $request->to_date,
			);
		}
		return $data;

	}

	public function expense() {
		$user = Auth::user();
		if ($user->group_id == null || $user->user_type == "S") {
			$vehicles = VehicleModel::whereIn_service(1)->get();

		} else {
			$vehicles = VehicleModel::whereIn_service(1)->where('group_id', $user->group_id)->get();
		}
		$vehicle_ids = $vehicles->pluck('id')->toArray();

		$total = Expense::whereIn('vehicle_id', $vehicle_ids)->whereDate('date', date('Y-m-d'))->sum('amount');
		$today = Expense::whereIn('vehicle_id', $vehicle_ids)->whereDate('date', date('Y-m-d'))->get();

		$details = array();
		foreach ($today as $row) {
			if ($row->type == "s") {
				$category = $row->service->description;
			} else {
				$category = $row->category->name;
			}
			$details[] = array(
				'id' => $row->id,
				'vehicle_make' => $row->vehicle->make_name,
				'vehicle_model' => $row->vehicle->model_name,
				'vehicle_license_plate' => $row->vehicle->license_plate,
				'expense_type' => $category,
				'vendor' => ($row->vendor_id) ? $row->vendor->name : null,
				'date' => $row->date,
				'amount' => Hyvikk::get('currency') . " " . $row->amount,
				'note' => $row->comment,
			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = array(
			"expense" => $details,
			'total_today' => Hyvikk::get('currency') . " " . $total,
		);
		return $data;
	}
}
