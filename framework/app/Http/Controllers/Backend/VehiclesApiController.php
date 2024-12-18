<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Imports\VehicleImport;
use App\Model\DriverLogsModel;
use App\Model\DriverVehicleModel;
use App\Model\Expense;
use App\Model\FuelModel;
use App\Model\IncomeModel;
use App\Model\ServiceReminderModel;
use App\Model\User;
use App\Model\VehicleModel;
use App\Model\VehicleReviewModel;
use Auth;
use Exception;
use Hyvikk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class VehiclesApiController extends Controller {

	public function store_purchase_info(Request $request) {
		$validation = Validator::make($request->all(), [
			'vehicle_id' => 'required|integer',
			'exp_name' => 'required',
			'exp_amount' => 'required|numeric',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			$vehicle = VehicleModel::find($request->vehicle_id);
			$old = array();
			if ($vehicle->getMeta('purchase_info') != "" || $vehicle->getMeta('purchase_info') != null) {
				$old = unserialize($vehicle->getMeta('purchase_info'));
			}
			$array1 = ['exp_name' => $request->exp_name, 'exp_amount' => $request->exp_amount];

			array_push($old, $array1);
			$vehicle->purchase_info = serialize($old);
			$vehicle->save();

			$data['success'] = "1";
			$data['message'] = "Purchase record added successfully!";
			$data['data'] = "";
		}
		return $data;

	}

	public function delete_purchase_info(Request $request) {
		$validation = Validator::make($request->all(), [
			'vehicle_id' => 'required|integer',
			'key' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			$vehicle = VehicleModel::where('id', $request->vehicle_id)->first();
			// dd($vehicle);
			$all = unserialize($vehicle->getMeta('purchase_info'));
			$index = $request->key;
			unset($all[$index]);
			// dd($all);
			$vehicle->purchase_info = serialize($all);
			$vehicle->save();

			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function import_records(Request $request) {
		$validation = Validator::make($request->all(), [
			'excel' => 'required|mimes:xlsx,xls',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			try {
				$file = $request->excel;
				$destinationPath = './assets/samples/'; // upload path
				$extension = $file->getClientOriginalExtension();
				$fileName = Str::uuid() . '.' . $extension;
				$file->move($destinationPath, $fileName);

				Excel::import(new VehicleImport, 'assets/samples/' . $fileName);

				// $excel = Importer::make('Excel');
				// $excel->load('assets/samples/' . $fileName);
				// $collection = $excel->getCollection()->toArray();
				// array_shift($collection);
				// // dd($collection);
				// foreach ($collection as $vehicle) {
				//     $id = VehicleModel::create([
				//         'make' => $vehicle[0],
				//         'model' => $vehicle[1],
				//         'year' => $vehicle[2],
				//         'int_mileage' => $vehicle[4],
				//         'reg_exp_date' => date('Y-m-d', strtotime($vehicle[5])),
				//         'engine_type' => $vehicle[6],
				//         'horse_power' => $vehicle[7],
				//         'color' => $vehicle[8],
				//         'vin' => $vehicle[9],
				//         'license_plate' => $vehicle[10],
				//         'lic_exp_date' => date('Y-m-d', strtotime($vehicle[11])),
				//         'user_id' => Auth::id(),
				//         'group_id' => Auth::user()->group_id,
				//     ])->id;

				//     $meta = VehicleModel::find($id);
				//     $meta->setMeta([
				//         'ins_number' => (isset($vehicle[12])) ? $vehicle[12] : "",
				//         'ins_exp_date' => (isset($vehicle[13]) && $vehicle[13] != null) ? date('Y-m-d', strtotime($vehicle[13])) : "",
				//         'documents' => "",
				//     ]);
				//     $meta->average = $vehicle[3];
				//     $meta->save();
				// }
				$data['success'] = "1";
				$data['message'] = "Records imported successfully!";
				$data['data'] = "";
			} catch (Exception $e) {
				$data['success'] = "0";
				$data['message'] = "Unable to import records.";
				$data['data'] = "";
			}
		}

		return $data;
	}

	public function upload_documents(Request $request, $id) {
		$validation = Validator::make($request->all(), [
			// 'id' => 'required|integer',
			'documents' => 'required|mimes:doc,pdf,docx,jpg,png,jpeg',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			if ($request->file('documents') && $request->file('documents')->isValid()) {
				$this->upload_doc($request->file('documents'), 'documents', $id);
			}

			$data['success'] = "1";
			$data['message'] = "Vehicle insurance document uploaded successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function upload_vehicle_image(Request $request, $id) {
		$validation = Validator::make($request->all(), [
			// 'id' => 'required|integer',
			'vehicle_image' => 'required|image|mimes:jpg,png,jpeg',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			if ($request->file('vehicle_image') && $request->file('vehicle_image')->isValid()) {

				$this->upload_file($request->file('vehicle_image'), "vehicle_image", $id);
			}

			$data['success'] = "1";
			$data['message'] = "Vehicle image uploaded successfully!";
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

			$vehicles = VehicleModel::whereIn('id', $request->ids)->get();
			foreach ($vehicles as $vehicle) {
				if ($vehicle->driver_id) {
					$driver = User::find($vehicle->driver_id);
					if ($driver != null) {
						$driver->vehicle_id = null;
						$driver->save();
					}
				}
				if (file_exists('./uploads/' . $vehicle->vehicle_image) && !is_dir('./uploads/' . $vehicle->vehicle_image)) {
					unlink('./uploads/' . $vehicle->vehicle_image);
				}
			}

			DriverVehicleModel::whereIn('vehicle_id', $request->ids)->delete();
			VehicleModel::whereIn('id', $request->ids)->delete();
			IncomeModel::whereIn('vehicle_id', $request->ids)->delete();
			Expense::whereIn('vehicle_id', $request->ids)->delete();
			VehicleReviewModel::whereIn('vehicle_id', $request->ids)->delete();
			ServiceReminderModel::whereIn('vehicle_id', $request->ids)->delete();
			FuelModel::whereIn('vehicle_id', $request->ids)->delete();
			$data['success'] = "1";
			$data['message'] = "Records deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function driver_logs() {
		$user = Auth::user();
		if ($user->group_id == null || $user->user_type == "S") {
			$vehicle_ids = VehicleModel::select('id')->get('id')->pluck('id')->toArray();

		} else {
			$vehicle_ids = VehicleModel::select('id')->where('group_id', $user->group_id)->get('id')->pluck('id')->toArray();
		}
		$logs = DriverLogsModel::whereIn('vehicle_id', $vehicle_ids)->get();
		$details = array();
		foreach ($logs as $log) {
			$details[] = array(
				'id' => $log->id,
				'vehicle' => $log->vehicle->make_name . " - " . $log->vehicle->model_name . " - " . $log->vehicle->license_plate,
				'driver' => $log->driver->name,
				'assigned_on' => $log->date,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;

	}

	public function assign_driver(Request $request) {
		$validation = Validator::make($request->all(), [
			'vehicle_id' => 'required|integer',
			'driver_id' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$records = User::meta()->where('users_meta.key', '=', 'vehicle_id')->where('users_meta.value', '=', $request->vehicle_id)->get();
			// remove records of this vehicle which are assigned to other drivers
			foreach ($records as $record) {
				$record->vehicle_id = null;
				$record->save();
			}
			$vehicle = VehicleModel::find($request->vehicle_id);
			$vehicle->driver_id = $request->driver_id;
			$vehicle->save();
			DriverVehicleModel::updateOrCreate(['vehicle_id' => $request->vehicle_id], ['vehicle_id' => $request->vehicle_id, 'driver_id' => $request->driver_id]);
			DriverLogsModel::create(['driver_id' => $request->driver_id, 'vehicle_id' => $request->vehicle_id, 'date' => date('Y-m-d H:i:s')]);
			$driver = User::find($request->driver_id);
			if ($driver != null) {
				$driver->vehicle_id = $request->vehicle_id;
				$driver->save();
			}

			$data['success'] = "1";
			$data['message'] = "Driver assigned successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function available_drivers($id) {
		$assigned = DriverVehicleModel::get();
		$did[] = 0;

		foreach ($assigned as $d) {
			$did[] = $d->driver_id;
		}

		$data = DriverVehicleModel::where('vehicle_id', $id)->first();
		// $except = array_diff($did, array($data->driver_id));
		if ($data != null) {
			$except = array_diff($did, array($data->driver_id));
		} else { $except = $did;}
		$drivers = User::whereUser_type("D")->whereNotIn('id', $except)->get();
		$details = array();
		foreach ($drivers as $row) {
			$details[] = array(
				'driver_id' => $row->id,
				'name' => $row->name,
			);
		}
		$index['success'] = "1";
		$index['message'] = "Data fetched!";
		$index['data'] = $details;
		return $index;
	}

	public function update_insurance(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'documents' => 'mimes:doc,pdf,docx,jpg,png,jpeg',
			'insurance_number' => 'required',
			'ins_exp_date' => 'required|date|date_format:Y-m-d',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$vehicle = VehicleModel::find($request->id);
			$vehicle->setMeta([
				'ins_number' => $request->insurance_number,
				'ins_exp_date' => date('Y-m-d', strtotime($request->ins_exp_date)),
			]);
			$vehicle->save();
			if ($request->file('documents') && $request->file('documents')->isValid()) {
				$this->upload_doc($request->file('documents'), 'documents', $vehicle->id);
			}
			$data['success'] = "1";
			$data['message'] = "Vehicle insurance details updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	private function upload_doc($file, $field, $id) {
		$destinationPath = './uploads'; // upload path
		$extension = $file->getClientOriginalExtension();
		$fileName1 = Str::uuid() . '.' . $extension;

		$file->move($destinationPath, $fileName1);
		$vehicle = VehicleModel::find($id);
		$vehicle->setMeta([$field => $fileName1]);
		$vehicle->save();

	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'make' => 'required',
			'model' => 'required',
			'year' => 'required|numeric',
			'engine_type' => 'required|in:Petrol,Diesel',
			'horse_power' => 'integer',
			'color' => 'required',
			'lic_exp_date' => 'required|date|date_format:Y-m-d',
			'reg_exp_date' => 'required|date|date_format:Y-m-d',
			'license_plate' => 'required|unique:vehicles,license_plate,' . \Request::get("id") . ',id,deleted_at,NULL',
			'int_mileage' => 'required|alpha_num',
			'vehicle_image' => 'nullable|image|mimes:jpg,png,jpeg',
			'average' => 'required|numeric',
			'in_service' => 'required|integer',
			'type_id' => 'required|integer',
			'group_id' => 'nullable|integer',
			'udf' => 'nullable|array',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			if ($request->file('vehicle_image') && $request->file('vehicle_image')->isValid()) {
				$this->upload_file($request->file('vehicle_image'), "vehicle_image", $request->id);
			}

			VehicleModel::where('id', $request->id)->update([
				'make' => $request->make,
				'model' => $request->model,
				'year' => $request->year,
				'engine_type' => $request->engine_type,
				'horse_power' => $request->horse_power,
				'color' => $request->color,
				'vin' => $request->vin,
				'license_plate' => $request->license_plate,
				'int_mileage' => $request->int_mileage,
				'group_id' => $request->group_id,
				// 'user_id' => Auth::id(),
				'lic_exp_date' => date('Y-m-d', strtotime($request->lic_exp_date)),
				'reg_exp_date' => date('Y-m-d', strtotime($request->reg_exp_date)),
				'in_service' => $request->in_service,
				'type_id' => $request->type_id,
			]);

			$user = VehicleModel::find($request->id);
			$user->average = $request->average;
			$user->udf = serialize($request->udf);
			$user->save();
			$data['success'] = "1";
			$data['message'] = "Vehicle updated successfully!";
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

			$vehicle = VehicleModel::find($request->get('id'));
			if ($vehicle->driver_id) {
				$driver = User::find($vehicle->driver_id);
				if ($driver != null) {
					$driver->vehicle_id = null;
					$driver->save();
				}

			}
			if (file_exists('./uploads/' . $vehicle->vehicle_image) && !is_dir('./uploads/' . $vehicle->vehicle_image)) {
				unlink('./uploads/' . $vehicle->vehicle_image);
			}
			DriverVehicleModel::where('vehicle_id', $request->id)->delete();

			VehicleModel::find($request->get('id'))->income()->delete();
			VehicleModel::find($request->get('id'))->expense()->delete();
			VehicleModel::find($request->get('id'))->delete();
			VehicleReviewModel::where('vehicle_id', $request->get('id'))->delete();

			ServiceReminderModel::where('vehicle_id', $request->get('id'))->delete();
			FuelModel::where('vehicle_id', $request->get('id'))->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'make' => 'required',
			'model' => 'required',
			'year' => 'required|numeric',
			'engine_type' => 'required|in:Petrol,Diesel',
			'horse_power' => 'integer',
			'color' => 'required',
			'lic_exp_date' => 'required|date|date_format:Y-m-d',
			'reg_exp_date' => 'required|date|date_format:Y-m-d',
			'license_plate' => 'required|unique:vehicles,license_plate',
			'int_mileage' => 'required|alpha_num',
			'vehicle_image' => 'nullable|image|mimes:jpg,png,jpeg',
			'average' => 'required|numeric',
			'in_service' => 'required|integer',
			'type_id' => 'required|integer',
			'group_id' => 'nullable|integer',
			'udf' => 'nullable|array',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			// dd($request->all());
			$vehicle = VehicleModel::create([
				'make' => $request->make,
				'model' => $request->model,
				'year' => $request->year,
				'engine_type' => $request->engine_type,
				'horse_power' => $request->horse_power,
				'color' => $request->color,
				'vin' => $request->vin,
				'license_plate' => $request->license_plate,
				'int_mileage' => $request->int_mileage,
				'group_id' => $request->group_id,
				'user_id' => Auth::id(),
				'lic_exp_date' => date('Y-m-d', strtotime($request->lic_exp_date)),
				'reg_exp_date' => date('Y-m-d', strtotime($request->reg_exp_date)),
				'in_service' => $request->in_service,
				'type_id' => $request->type_id,
			])->id;
			if ($request->file('vehicle_image') && $request->file('vehicle_image')->isValid()) {
				$this->upload_file($request->file('vehicle_image'), "vehicle_image", $vehicle);
			}

			$meta = VehicleModel::find($vehicle);
			$meta->setMeta([
				'ins_number' => "",
				'ins_exp_date' => "",
				'documents' => "",
			]);
			$meta->udf = serialize($request->get('udf'));
			$meta->average = $request->average;
			$meta->save();

			$data['success'] = "1";
			$data['message'] = "Vehicle added successfully!";
			$data['data'] = array('id' => $vehicle);
		}
		return $data;
	}

	private function upload_file($file, $field, $id) {
		$destinationPath = './uploads'; // upload path
		$extension = $file->getClientOriginalExtension();
		$fileName1 = Str::uuid() . '.' . $extension;

		$file->move($destinationPath, $fileName1);

		$x = VehicleModel::find($id);
		if (file_exists('./uploads/' . $x->$field) && !is_dir('./uploads/' . $x->$field)) {
			unlink('./uploads/' . $x->$field);
		}
		$x->update([$field => $fileName1]);

	}

	public function vehicles() {
		$user = Auth::user();
		if ($user->group_id == null || $user->user_type == "S") {
			$vehicles = VehicleModel::orderBy('id', 'desc')->get();
		} else {
			$vehicles = VehicleModel::where('group_id', $user->group_id)->orderBy('id', 'desc')->get();
		}
		$details = array();
		foreach ($vehicles as $row) {
			$inspections = array();
			foreach ($row->reviews as $r) {
				$inspections[] = $r->id;
			}
			$purchase_info = array();
			$info = unserialize($row->purchase_info);
			if ($info != null) {
				$total = 0;
				foreach ($info as $key => $val) {
					$total += $val['exp_amount'];
					$purchase_info[] = array(
						'key' => $key,
						'expense' => $val['exp_name'],
						'amount' => Hyvikk::get('currency') . " " . $val['exp_amount'],
					);
				}
				// array_push($purchase_info, ['total' => $total]);
			}

			$image = asset("assets/images/vehicle.jpeg");
			if ($row->vehicle_image != null) {
				$image = asset('uploads/' . $row->vehicle_image);
			}
			$udf = "";
			$blank = array();
			if (unserialize($row->getMeta('udf'))) {
				$test = unserialize($row->getMeta('udf'));
				foreach ($test as $key => $val) {
					$blank[] = array(
						'name' => $key,
						'value' => $val,
					);
				}
				$udf = $blank;
			}

			$details[] = array(
				"id" => $row->id,
				"general_info" => array(
					"id" => $row->id,
					"make" => $row->make_name,
					"model" => $row->model_name,
					"year" => $row->year,
					"vehicle_type" => ($row->type_id) ? $row->types->displayname : "",
					"type_id" => $row->type_id,
					"average" => $row->average,
					"int_mileage" => $row->int_mileage,
					"vehicle_image" => $image,
					"reg_exp_date" => $row->reg_exp_date,
					"in_service" => ($row->in_service) ? $row->in_service : 0,
					"engine_type" => $row->engine_type,
					"horse_power" => $row->horse_power,
					"color" => $row->color,
					"vin" => $row->vin,
					"license_plate" => $row->license_plate,
					"lic_exp_date" => $row->lic_exp_date,
					"group_id" => $row->group_id,
					"group" => ($row->group_id) ? $row->group->name : "",
					"driver_id" => $row->driver_id,
					"assigned_driver" => ($row->driver_id) ? $row->driver->assigned_driver->name : "",
					"udf" => $udf,
				),

				"insurance_info" => array(
					"insurance_number" => $row->ins_number,
					"documents" => ($row->documents)?asset('uploads/' . $row->documents): "",
					"ins_exp_date" => $row->ins_exp_date,
				),

				"purchase_info" => $purchase_info,
				'inspection_ids' => $inspections,
				"vehicle_info" => array(
					"Vehicle name" => $row->model_name,
					"Vehicle make" => $row->make_name,
					"Vehicle type" => ($row->type_id) ? $row->types->displayname : "",
					"Vehicle year" => $row->year,
					"Average(Miles per galon)" => $row->average,
					"Initial mileage (" . Hyvikk::get('dis_format') . ")" => $row->int_mileage,
					"Vehicle engine type" => $row->engine_type,
					"Vehicle horse power" => $row->horse_power,
					"Color" => $row->color,
					"VIN" => $row->vin,
					"License plate" => $row->license_plate,
					"License expiry date" => $row->lic_exp_date,
					"Register expiry date" => $row->reg_exp_date,
				),
			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
