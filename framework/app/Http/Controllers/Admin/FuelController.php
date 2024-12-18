<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FuelRequest;
use App\Model\Expense;
use App\Model\FuelModel;
use App\Model\VehicleModel;
use App\Model\Vendor;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FuelController extends Controller {
	public function __construct() {
		// $this->middleware(['role:Admin']);
		// dd(\App\Model\User::find(43)->name,\App\Model\User::find(43)->getPermissionNames()	);
		$this->middleware('permission:Fuel add', ['only' => ['create']]);
		$this->middleware('permission:Fuel edit', ['only' => ['edit']]);
		$this->middleware('permission:Fuel delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:Fuel list');
	}

	public function index() {
		if (Auth::user()->user_type == "S" || Auth::user()->user_type != "D") {
			if (Auth::user()->group_id == null) {
				$vehicle_ids = VehicleModel::pluck('id')->toArray();
			} else {
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			}
		}
		if (Auth::user()->user_type == "D") {
			// $vehicle = DriverLogsModel::where('driver_id',Auth::user()->id)->get()->toArray();
			// $vehicle_ids = VehicleModel::where('id', $vehicle[0]['vehicle_id'])->pluck('id')->toArray();
			// $vehicle_ids = auth()->user()->vehicles()->pluck('vehicles.id')->toArray();
			$vehicle_ids = VehicleModel::pluck('id')->toArray();
			$data['data'] = FuelModel::orderBy('id', 'desc')->where('user_id', Auth::user()->id)->get();
		}
		else{
			$data['data'] = FuelModel::orderBy('id', 'desc')->whereIn('vehicle_id', $vehicle_ids)->get();
		}
		return view('fuel.index', $data);
	}
	public function create() {
		if (Auth::user()->user_type == "S" || Auth::user()->user_type != "D") {
			if (Auth::user()->group_id == null) {
				$data['vehicles'] = VehicleModel::whereIn_service("1")->get();
			} else {
				$data['vehicles'] = VehicleModel::where('group_id', Auth::user()->group_id)->whereIn_service("1")->get();
			}
		}

		if (Auth::user()->user_type == "D") {
			// $vehicle = DriverLogsModel::where('driver_id',Auth::user()->id)->get()->toArray();
			// $data['vehicles'] = VehicleModel::where('id', $vehicle[0]['vehicle_id'])->whereIn_service("1")->get();
			// $data['vehicles'] = auth()->user()->vehicles()->whereIn_service("1")->get();
			$data['vehicles'] =VehicleModel::whereIn_service("1")->get();
			// dd($data['vehicles']);
		}
		$data['vendors'] = Vendor::where('type', 'fuel')->get();
		return view('fuel.create', $data);
	}

	public function store(FuelRequest $request) {
		// dd($request->all());

		$fuel = new FuelModel();
		$fuel->vehicle_id = $request->get('vehicle_id');
		$fuel->user_id = $request->get('user_id');
		$condition = FuelModel::orderBy('id', 'desc')->where('vehicle_id', $request->get('vehicle_id'))->first();
		// dd($condition->qty);
		if ($condition != null) {

			$fuel->start_meter = $request->get('start_meter');
			$fuel->end_meter = "0";
			$fuel->consumption = "0";
			$condition->end_meter = $end = $request->get('start_meter');
			// dd($condition->end_meter);
			// $fuel->start_meter = $start = $request->get('start_meter');
			// dd($condition->start_meter);
			// dd($end); //value fetched
			if ($request->get('qty') == 0) {
				$condition->consumption = $con = 0;
			} else {
				$condition->consumption = $con = ($end - $condition->start_meter) / $condition->qty;
			}
			// dd($con);
			$condition->save();

		} else {

			$fuel->start_meter = $request->get('start_meter');
			$fuel->end_meter = "0";
			$fuel->consumption = "0";

		}
		$fuel->reference = $request->get('reference');
		$fuel->province = $request->get('province');
		$fuel->note = $request->get('note');
		$fuel->qty = $request->get('qty');
		$fuel->fuel_from = $request->get('fuel_from');
		$fuel->vendor_name = $request->get('vendor_name');
		$fuel->cost_per_unit = $request->get('cost_per_unit');
		$fuel->date = $request->get('date');
		$fuel->complete = $request->get("complete");

		$file = $request->file('image');
		if ($file && $file->isValid()) {
			$destinationPath = './uploads'; // upload path
			$extension = $file->getClientOriginalExtension();

			$fileName1 = Str::uuid() . '.' . $extension;

			$file->move($destinationPath, $fileName1);

			$fuel->image = $fileName1;
		}

		$fuel->save();

		$expense = new Expense();
		$expense->vehicle_id = $request->get('vehicle_id');
		$expense->user_id = $request->get('user_id');
		$expense->expense_type = '8';
		$expense->comment = $request->get('note');
		$expense->date = $request->get('date');
		$amount = $request->get('qty') * $request->get('cost_per_unit');
		$expense->amount = $amount;
		$expense->exp_id = $fuel->id;
		$expense->save();
		VehicleModel::where('id', $request->vehicle_id)->update(['mileage' => $request->start_meter]);
		return redirect('admin/fuel');
	}

	public function edit($id) {
		// return $id;
		// $data['vehicle'] = VehicleModel::whereId(2)->get();
		$data['data'] = $data = FuelModel::whereId($id)->get()->first();
		$data['vehicle_id'] = $data->vehicle_id;
		$data['vendors'] = Vendor::where('type', 'fuel')->get();
		return view('fuel.edit', $data);
	}

	public function update(FuelRequest $request) {
		// dd($request->all());
		$fuel = FuelModel::find($request->get("id"));
		// $form_data = $request->all();
		$old = FuelModel::where('vehicle_id', $fuel->vehicle_id)->where('end_meter', $fuel->start_meter)->first();
		if ($old != null) {
			$old->end_meter = $request->get('start_meter');
			$old->consumption = ($old->end_meter - $old->start_meter) / $old->qty;
			$old->save();
		}

		$fuel->start_meter = $request->get('start_meter');
		$fuel->reference = $request->get('reference');
		$fuel->province = $request->get('province');
		$fuel->note = $request->get('note');
		$fuel->qty = $request->get('qty');
		$fuel->fuel_from = $request->get('fuel_from');
		$fuel->vendor_name = $request->get('vendor_name');
		$fuel->cost_per_unit = $request->get('cost_per_unit');
		$fuel->date = $request->get('date');
		$fuel->complete = $request->get("complete");
		if ($fuel->end_meter != 0) {
			$fuel->consumption = ($fuel->end_meter - $request->get('start_meter')) / $request->get('qty');
		}

		$file = $request->file('image');
		if ($file && $file->isValid()) {
			$destinationPath = './uploads'; // upload path
			$extension = $file->getClientOriginalExtension();

			$fileName1 = Str::uuid() . '.' . $extension;

			$file->move($destinationPath, $fileName1);

			$fuel->image = $fileName1;
		}

		$fuel->save();
		$exp = Expense::where('exp_id', $request->get('id'))->where('expense_type', 8)->first();
		if ($exp != null) {
			$exp->amount = $request->get('qty') * $request->get('cost_per_unit');
			$exp->save();
		}
		VehicleModel::where('id', $request->vehicle_id)->update(['mileage' => $request->start_meter]);
		return redirect()->route('fuel.index');

	}

	public function destroy(Request $request) {
		$fuel = FuelModel::find($request->get('id'));

		if (!is_null($fuel->image) && file_exists('uploads/' . $fuel->image)) {
			unlink('uploads/' . $fuel->image);
		}

		$fuel->delete();

		Expense::where('exp_id', $request->get('id'))->where('expense_type', 8)->delete();
		return redirect()->route('fuel.index');
	}

	public function bulk_delete(Request $request) {
		// dd($request->all());
		$fuels = FuelModel::whereIn('id', $request->ids)->get();
		foreach ($fuels as $fuel) {
			if (!is_null($fuel->image) && file_exists('uploads/' . $fuel->image)) {
				unlink('uploads/' . $fuel->image);
			}
			$fuel->delete();
		}

		Expense::whereIn('exp_id', $request->ids)->where('expense_type', 8)->delete();
		return redirect()->back();
	}
}
