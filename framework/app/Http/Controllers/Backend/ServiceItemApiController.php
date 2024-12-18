<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\ServiceItemsModel;
use App\Model\ServiceReminderModel;
use Hyvikk;
use Illuminate\Http\Request;
use Validator;

class ServiceItemApiController extends Controller {

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

			ServiceItemsModel::whereIn('id', $request->ids)->delete();
			ServiceReminderModel::whereIn('service_id', $request->ids)->delete();

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
			ServiceItemsModel::find($request->id)->delete();
			ServiceReminderModel::where('service_id', $request->id)->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'description' => 'required',
			'overdue_time' => 'integer|required_if:time_interval,on',
			'duesoon_time' => 'integer|required_if:show_time,on',
			'overdue_meter' => 'integer',
			'time_interval' => 'required',
			'show_time' => 'required',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			if ($request->overdue_time != null) {
				$overdue_time = $request->overdue_time;
			} else {
				$overdue_time = Hyvikk::get('time_interval');
			}
			ServiceItemsModel::where('id', $request->id)->update([
				'description' => $request->description,
				'time_interval' => $request->time_interval,
				'overdue_time' => $overdue_time,
				'overdue_unit' => $request->overdue_unit,
				'meter_interval' => $request->meter_interval,
				'overdue_meter' => $request->overdue_meter,
				'show_time' => $request->show_time,
				'duesoon_time' => $request->duesoon_time,
				'duesoon_unit' => $request->duesoon_unit,
				'show_meter' => $request->show_meter,
				'duesoon_meter' => $request->duesoon_meter,
			]);
			$data['success'] = "1";
			$data['message'] = "Service item updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'description' => 'required',
			'overdue_time' => 'integer|required_if:time_interval,on',
			'duesoon_time' => 'integer|required_if:show_time,on',
			'overdue_meter' => 'integer',
			'time_interval' => 'required',
			'show_time' => 'required',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			if ($request->overdue_time != null) {
				$overdue_time = $request->overdue_time;
			} else {
				$overdue_time = Hyvikk::get('time_interval');
			}
			ServiceItemsModel::create([
				'description' => $request->description,
				'time_interval' => $request->time_interval,
				'overdue_time' => $overdue_time,
				'overdue_unit' => $request->overdue_unit,
				'meter_interval' => $request->meter_interval,
				'overdue_meter' => $request->overdue_meter,
				'show_time' => $request->show_time,
				'duesoon_time' => $request->duesoon_time,
				'duesoon_unit' => $request->duesoon_unit,
				'show_meter' => $request->show_meter,
				'duesoon_meter' => $request->duesoon_meter,
			]);
			$data['success'] = "1";
			$data['message'] = "Service item added successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function items() {
		$details = array();
		$records = ServiceItemsModel::orderBy('id', 'desc')->get();
		foreach ($records as $row) {
			$service_interval = $row->overdue_time . " " . $row->overdue_unit;
			if ($row->overdue_meter != null) {
				$service_interval .= " or " . $row->overdue_meter . " " . Hyvikk::get('dis_format');
			}
			$create_reminder = null;
			if ($row->duesoon_time != null) {
				$create_reminder = $row->duesoon_time . " " . $row->duesoon_unit . " before due";
			}

			$details[] = array(
				'id' => $row->id,
				'description' => $row->description,
				'service_interval' => $service_interval,
				'create_reminder' => $create_reminder,
				'time_interval' => ($row->time_interval == "on") ? "on" : "off",
				'overdue_time' => $row->overdue_time,
				'overdue_unit' => $row->overdue_unit,
				'meter_interval' => ($row->meter_interval == "on") ? "on" : "off",
				'overdue_meter' => $row->overdue_meter,
				'show_time' => ($row->show_time == "on") ? "on" : "off",
				'duesoon_time' => $row->duesoon_time,
				'duesoon_unit' => $row->duesoon_unit,
				'show_meter' => ($row->show_meter == "on") ? "on" : "off",
				'duesoon_meter' => $row->duesoon_meter,

			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
