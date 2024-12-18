<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\ServiceReminderModel;
use App\Model\User;
use App\Model\VehicleModel;
use Auth;
use Hyvikk;
use Illuminate\Http\Request;
use Validator;

class ServiceRemindersApiController extends Controller {

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

			ServiceReminderModel::whereIn('id', $request->ids)->delete();

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
			ServiceReminderModel::find($request->id)->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'vehicle_id' => 'required|integer',
			'start_date' => 'required|date',
			'checkbox' => 'required',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$users = User::where('user_type', 'S')->get();
			foreach ($request->checkbox as $item) {

				$history = ServiceReminderModel::whereVehicleId($request->vehicle_id)->where('service_id', $item)->orderBy('id', 'desc')->first();
				if ($history == null) {
					$last_date = "N/D";
					$last_meter = "0";
				} else {
					$interval = substr($history->services->overdue_unit, 0, -3);
					$int = $history->services->overdue_time . $interval;
					$date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))));
					$last_date = $date;
					if ($history->last_meter == 0) {
						$total = $history->vehicle->int_mileage;
					} else {
						$total = $history->last_meter;
					}
					$last_meter = $total + $history->services->overdue_meter;
				}
				ServiceReminderModel::create([
					'vehicle_id' => $request->vehicle_id,
					'service_id' => $item,
					'last_date' => date('Y-m-d', strtotime($request->start_date)),
					'last_meter' => $last_meter,
				]);

			}

			$data['success'] = "1";
			$data['message'] = "Service reminder added successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function reminders() {
		$details = array();
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$vehicle_ids = VehicleModel::pluck('id')->toArray();
		} else {
			$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
		}
		$records = ServiceReminderModel::whereIn('vehicle_id', $vehicle_ids)->get();
		foreach ($records as $row) {
			$image = asset("assets/images/vehicle.jpeg");
			if ($row->vehicle->vehicle_image != null) {
				$image = asset('uploads/' . $row->vehicle->vehicle_image);
			}

			$interval = $row->services->overdue_time . " " . $row->services->overdue_unit;
			if ($row->services->overdue_meter != null) {
				$interval .= " or " . $row->services->overdue_meter . " " . Hyvikk::get('dis_format');
			}
			$next_meter = null;
			if ($row->services->overdue_meter != null) {
				if ($row->last_meter == 0) {
					$next_meter = $row->vehicle->int_mileage + $row->services->overdue_meter . " " . Hyvikk::get('dis_format');
				} else {
					$next_meter = $row->last_meter + $row->services->overdue_meter . " " . Hyvikk::get('dis_format');
				}
			}

			$next_date = null;
			$interval = substr($row->services->overdue_unit, 0, -3);
			if ($row->services->overdue_time != null) {
				$int = $row->services->overdue_time . $interval;
			} else {
				$int = Hyvikk::get('time_interval') . "day";
			}

			if ($row->last_date != 'N/D') {
				$next_date = date('Y-m-d', strtotime($int, strtotime($row->last_date)));
			} else {
				$next_date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))));
			}

			$details[] = array(
				'id' => $row->id,
				'vehicle_id' => $row->vehicle_id,
				'service_id' => $row->service_id,
				'image' => $image,
				'unit' => $row->vehicle_id,
				'vehicle' => $row->vehicle->year . " " . $row->vehicle->make_name . " - " . $row->vehicle->model_name,
				'vin' => $row->vehicle->vin,
				'plate' => $row->vehicle->license_plate,
				'service_item' => $row->services->description,
				'interval' => $interval,
				'start_date' => $row->last_date,
				'last_performed_meter' => $row->last_meter,
				'next_meter' => $next_meter,
				'next_date' => $next_date,

			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
