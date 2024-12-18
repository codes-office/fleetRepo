<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\PartsModel;
use App\Model\PartsUsedModel;
use App\Model\VehicleModel;
use App\Model\Vendor;
use App\Model\WorkOrderLogs;
use App\Model\WorkOrders;
use Auth;
use Hyvikk;
use Illuminate\Http\Request;
use Validator;

class WorkOrdersApiController extends Controller {

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

			WorkOrders::whereIn('id', $request->ids)->delete();

			$data['success'] = "1";
			$data['message'] = "Records deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function remove_part($id) {
		$usedpart = PartsUsedModel::find($id);
		if ($usedpart == null) {
			$data['success'] = "0";
			$data['message'] = "Failed to remove part, please try again later!";
			$data['data'] = "";
		} else {
			$part = PartsModel::find($usedpart->part_id);
			$part->stock = $part->stock + $usedpart->qty;
			$part->save();
			if ($part->stock > 0) {
				$part->availability = 1;
				$part->save();
			}
			$usedpart->delete();
			$data['success'] = "1";
			$data['message'] = "Part removed successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'vehicle_id' => 'required|integer',
			'vendor_id' => 'required|integer',
			'required_by' => 'required|date|date_format:Y-m-d',
			'price' => 'required|numeric',
			'status' => 'required',
			'meter' => 'nullable|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$order = WorkOrders::find($request->id);
			$order->required_by = date('Y-m-d', strtotime($request->required_by));
			$order->vehicle_id = $request->vehicle_id;
			$order->vendor_id = $request->vendor_id;
			$order->status = $request->status;
			$order->description = $request->description;
			$order->meter = $request->meter;
			$order->price = $request->price;
			$order->note = $request->note;
			$order->save();

			$log = WorkOrderLogs::create([
				'created_on' => date('Y-m-d', strtotime($order->created_at)),
				'vehicle_id' => $order->vehicle_id,
				'vendor_id' => $order->vendor_id,
				'required_by' => $order->required_by,
				'status' => $order->status,
				'description' => $order->description,
				'meter' => $order->meter,
				'note' => $order->note,
				'price' => $order->price,
				'type' => "Updated",
			]);
			$parts = $request->parts;

			if ($parts != null) {
				foreach ($parts as $row) {
					$update_part = PartsModel::find($row['part_id']);

					PartsUsedModel::create(['work_id' => $order->id, 'part_id' => $row['part_id'], 'qty' => $row['qty'], 'price' => $update_part->unit_cost, 'total' => $row['qty'] * $update_part->unit_cost]);
					$update_part->stock = $update_part->stock - $row['qty'];
					$update_part->save();
					if ($update_part->stock == 0) {
						$update_part->availability = 0;
						$update_part->save();
					}
				}
			}
			$log->parts_price = $order->parts->sum('total');
			$log->save();
			$data['success'] = "1";
			$data['message'] = "Work order updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'vehicle_id' => 'required|integer',
			'vendor_id' => 'required|integer',
			'required_by' => 'required|date|date_format:Y-m-d',
			'price' => 'required|numeric',
			'status' => 'required',
			'meter' => 'nullable|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$order = WorkOrders::create([
				'required_by' => date('Y-m-d', strtotime($request->required_by)),
				'vehicle_id' => $request->vehicle_id,
				'vendor_id' => $request->vendor_id,
				'status' => $request->status,
				'description' => $request->description,
				'meter' => $request->meter,
				'price' => $request->price,
				'note' => $request->note,
			]);

			$log = WorkOrderLogs::create([
				'created_on' => date('Y-m-d', strtotime($order->created_at)),
				'vehicle_id' => $order->vehicle_id,
				'vendor_id' => $order->vendor_id,
				'required_by' => $order->required_by,
				'status' => $order->status,
				'description' => $order->description,
				'meter' => $order->meter,
				'note' => $order->note,
				'price' => $order->price,
				'type' => "Created",
			]);
			$parts = $request->parts;

			if ($parts != null) {
				foreach ($parts as $row) {
					$update_part = PartsModel::find($row['part_id']);
					PartsUsedModel::create(['work_id' => $order->id, 'part_id' => $row['part_id'], 'qty' => $row['qty'], 'price' => $update_part->unit_cost, 'total' => $row['qty'] * $update_part->unit_cost]);
					$update_part->stock = $update_part->stock - $row['qty'];
					$update_part->save();
					if ($update_part->stock == 0) {
						$update_part->availability = 0;
						$update_part->save();
					}
				}
			}
			$log->parts_price = $order->parts->sum('total');
			$log->save();
			$data['success'] = "1";
			$data['message'] = "Work order added successfully!";
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
			WorkOrders::find($request->id)->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function parts_used($id) {
		$order = WorkOrders::find($id);
		$details = array();
		foreach ($order->parts as $row) {
			$details[] = array(
				'vehicle' => $order->vehicle->make_name . " - " . $order->vehicle->model_name . " - " . $order->vehicle->license_plate,
				'description' => $order->description,
				'part' => $row->part->title,
				'qty' => $row->qty,
				'unit_cost' => Hyvikk::get('currency') . " " . $row->price,
				'total_cost' => Hyvikk::get('currency') . " " . $row->total,
			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}

	public function logs() {
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$vehicle_ids = VehicleModel::pluck('id')->toArray();
		} else {
			$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
		}
		$logs = WorkOrderLogs::whereIn('vehicle_id', $vehicle_ids)->latest()->get();
		$details = array();
		foreach ($logs as $row) {
			$image = asset("assets/images/vehicle.jpeg");
			if ($row->vehicle->vehicle_image != null) {
				$image = asset('uploads/' . $row->vehicle->vehicle_image);
			}

			$details[] = array(
				'id' => $row->id,
				'vehicle' => $row->vehicle->year . " " . $row->vehicle->make_name . " - " . $row->vehicle->model_name,
				'vin' => $row->vehicle->vin,
				'plate' => $row->vehicle->license_plate,
				'image' => $image,
				'created_on' => date('Y-m-d H:i:s', strtotime($row->created_at)),
				'required_by' => date('Y-m-d', strtotime($row->required_by)),
				'vendor' => $row->vendor->name,
				'description' => $row->description,
				'work_order_price' => $row->price,
				'parts_cost' => $row->parts_price,
				'total_cost' => $row->price + $row->parts_price,
				'currency' => Hyvikk::get('currency'),
				'status' => $row->status,
				'type' => $row->type,
				'timestamp' => date('Y-m-d H:i:s', strtotime($row->created_at)),
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}

	public function dropdowns() {
		$parts = PartsModel::where('stock', '>', 0)->where('availability', 1)->get(['id as part_id', 'unit_cost', 'title', 'stock as max_qty']);
		$vendors = Vendor::get(['id as vendor_id', 'name'])->toArray();
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$vehicles = VehicleModel::whereIn_service("1")->get();
		} else {
			$vehicles = VehicleModel::where('group_id', Auth::user()->group_id)->whereIn_service("1")->get();
		}
		// dd($vendors);
		$vehicle_list = array();
		foreach ($vehicles as $row) {
			$vehicle_list[] = array(
				'vehicle_id' => $row->id,
				'vehicle' => $row->make_name . " - " . $row->model_name . " - " . $row->license_plate,
			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = array(
			'vehicles' => $vehicle_list,
			'vendors' => $vendors,
			'parts' => $parts,
		);
		return $data;
	}

	public function orders() {
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$vehicle_ids = VehicleModel::pluck('id')->toArray();
		} else {
			$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
		}
		$records = WorkOrders::whereIn('vehicle_id', $vehicle_ids)->orderBy('id', 'desc')->get();

		$details = array();
		foreach ($records as $row) {
			$image = asset("assets/images/vehicle.jpeg");
			if ($row->vehicle->vehicle_image != null) {
				$image = asset('uploads/' . $row->vehicle->vehicle_image);
			}
			$attached_parts = array();
			foreach ($row->parts as $part) {
				$attached_parts[] = array(
					'record_id' => $part->id,
					'part_id' => $part->part_id,
					'part_name' => $part->part->title,
					'qty' => $part->qty,
					'unit_cost' => $part->price,
					'total_cost' => $part->price * $part->qty,
				);
			}

			$details[] = array(
				'id' => $row->id,
				'vehicle_id' => $row->vehicle_id,
				'vendor_id' => $row->vendor_id,
				'vehicle' => $row->vehicle->year . " " . $row->vehicle->make_name . " - " . $row->vehicle->model_name,
				'vin' => $row->vehicle->vin,
				'plate' => $row->vehicle->license_plate,
				'image' => $image,
				'created_on' => date('Y-m-d H:i:s', strtotime($row->created_at)),
				'required_by' => date('Y-m-d', strtotime($row->required_by)),
				'vendor' => $row->vendor->name,
				'description' => $row->description,
				'work_order_price' => $row->price,
				'parts_cost' => $row->parts->sum('total'),
				'total_cost' => $row->price + $row->parts->sum('total'),
				'currency' => Hyvikk::get('currency'),
				'status' => $row->status,
				'meter_reading' => $row->meter,
				'note' => $row->note,
				'attached_parts' => $attached_parts,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
