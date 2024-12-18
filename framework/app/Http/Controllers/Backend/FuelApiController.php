<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Expense;
use App\Model\FuelModel;
use App\Model\VehicleModel;
use Auth;
use Hyvikk;
use Illuminate\Http\Request;
use Validator;

class FuelApiController extends Controller {

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

			FuelModel::whereIn('id', $request->ids)->delete();
			Expense::whereIn('exp_id', $request->ids)->where('expense_type', 8)->delete();

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
			FuelModel::find($request->id)->delete();
			Expense::where('exp_id', $request->id)->where('expense_type', 8)->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'start_meter' => 'required|numeric',
			'cost_per_unit' => 'required|numeric',
			'date' => 'required|date|date_format:Y-m-d',
			'qty' => 'required|numeric',
			'vendor_id' => 'required_if:fuel_from,Vendor',
			'fuel_from' => 'required',
			'complete_fill_up' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$fuel = FuelModel::find($request->id);
			// $form_data = $request->all();
			$old = FuelModel::where('vehicle_id', $fuel->vehicle_id)->where('end_meter', $fuel->start_meter)->first();
			if ($old != null) {
				$old->end_meter = $request->start_meter;
				$old->consumption = ($old->end_meter - $old->start_meter) / $old->qty;
				$old->save();
			}

			$fuel->start_meter = $request->start_meter;
			$fuel->reference = $request->reference;
			$fuel->province = $request->province;
			$fuel->note = $request->note;
			$fuel->qty = $request->qty;
			$fuel->fuel_from = $request->fuel_from;
			$fuel->vendor_name = $request->vendor_id;
			$fuel->cost_per_unit = $request->cost_per_unit;
			$fuel->date = date('Y-m-d', strtotime($request->date));
			$fuel->complete = $request->complete_fill_up;
			if ($fuel->end_meter != 0) {
				$fuel->consumption = ($fuel->end_meter - $request->start_meter) / $request->qty;
			}
			$fuel->save();
			$exp = Expense::where('exp_id', $request->id)->where('expense_type', 8)->first();
			if ($exp != null) {
				$exp->amount = $request->qty * $request->cost_per_unit;
				$exp->save();
			}
			VehicleModel::where('id', $request->vehicle_id)->update(['mileage' => $request->start_meter]);

			$data['success'] = "1";
			$data['message'] = "fuel entry updated successfully!";
			$data['data'] = "";
		}
		return $data;

	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'vehicle_id' => 'required|integer',
			'start_meter' => 'required|numeric',
			'cost_per_unit' => 'required|numeric',
			'date' => 'required|date|date_format:Y-m-d',
			'qty' => 'required|numeric',
			'vendor_id' => 'required_if:fuel_from,Vendor',
			'fuel_from' => 'required',
			'complete_fill_up' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$condition = FuelModel::orderBy('id', 'desc')->where('vehicle_id', $request->vehicle_id)->first();

			$fuel = FuelModel::create([
				'vehicle_id' => $request->vehicle_id,
				'user_id' => Auth::id(),
			]);

			if ($condition != null) {

				$fuel->start_meter = $request->start_meter;
				$fuel->end_meter = "0";
				$fuel->consumption = "0";
				$condition->end_meter = $end = $request->start_meter;

				if ($request->qty == 0) {
					$condition->consumption = $con = 0;
				} else {
					$condition->consumption = $con = ($end - $condition->start_meter) / $condition->qty;
				}

				$condition->save();

			} else {

				$fuel->start_meter = $request->start_meter;
				$fuel->end_meter = "0";
				$fuel->consumption = "0";

			}
			$fuel->reference = $request->reference;
			$fuel->province = $request->province;
			$fuel->note = $request->note;
			$fuel->qty = $request->qty;
			$fuel->fuel_from = $request->fuel_from;
			$fuel->vendor_name = $request->vendor_id;
			$fuel->cost_per_unit = $request->cost_per_unit;
			$fuel->date = date('Y-m-d', strtotime($request->date));
			$fuel->complete = $request->complete_fill_up;
			$fuel->save();

			Expense::create([
				'vehicle_id' => $request->vehicle_id,
				'user_id' => Auth::id(),
				'expense_type' => '8',
				'comment' => $request->note,
				'date' => date('Y-m-d', strtotime($request->date)),
				'amount' => $request->qty * $request->cost_per_unit,
				'exp_id' => $fuel->id,
			]);
			VehicleModel::where('id', $request->vehicle_id)->update(['mileage' => $request->start_meter]);

			$data['success'] = "1";
			$data['message'] = "fuel entry added successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function fuel() {
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$vehicle_ids = VehicleModel::pluck('id')->toArray();
		} else {
			$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
		}
		$fuel = FuelModel::orderBy('id', 'desc')->whereIn('vehicle_id', $vehicle_ids)->get();
		$details = array();
		foreach ($fuel as $row) {
			$image = asset("assets/images/vehicle.jpeg");
			if ($row->vehicle_data->vehicle_image) {
				$image = asset('uploads/' . $row->photo);
			}
			if (Hyvikk::get('dis_format') == "km") {

				$consumption_unit = (Hyvikk::get('fuel_unit') == "gallon") ? "KMPG" : "KMPL";
			} else {

				$consumption_unit = (Hyvikk::get('fuel_unit') == "gallon") ? "MPG" : "MPL";
			}

			$details[] = array(
				'id' => $row->id,
				'photo' => $image,
				'vehicle' => $row->vehicle_data->year . " " . $row->vehicle_data->make_name . "-" . $row->vehicle_data->model,
				'vin' => $row->vehicle_data->vin,
				'date' => $row->date,
				'qty_unit' => Hyvikk::get('fuel_unit'),
				'qty' => $row->qty,
				'currency' => Hyvikk::get('currency'),
				'cost_per_unit' => $row->cost_per_unit,
				'total_cost' => $row->qty * $row->cost_per_unit,
				'distance_unit' => Hyvikk::get('dis_format'),
				'start_meter' => $row->start_meter,
				'end_meter' => $row->end_meter,
				'distance' => ($row->end_meter > 0) ? $row->end_meter - $row->start_meter : 0,
				'consumption' => $row->consumption . " " . $consumption_unit,

				'reference' => $row->reference,
				'province' => $row->province,
				'note' => $row->note,
				'complete_fill_up' => ($row->complete == 1) ? 1 : 0,
				'fuel_from' => $row->fuel_from,
				'vendor_id' => $row->vendor_name,
				'vehicle_id' => $row->vehicle_id,

			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
