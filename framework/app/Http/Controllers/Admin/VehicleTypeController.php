<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleTypeRequest;
use App\Model\FareSettings;
use App\Model\VehicleTypeModel;
use DataTables;
use Illuminate\Http\Request;

class VehicleTypeController extends Controller {

	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:VehicleType add', ['only' => ['create']]);
		$this->middleware('permission:VehicleType edit', ['only' => ['edit']]);
		$this->middleware('permission:VehicleType delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:VehicleType list');
	}
	public function index() {
		$index['data'] = VehicleTypeModel::get();
		return view('vehicle_types.index', $index);
	}

	public function fetch_data(Request $request) {
		if ($request->ajax()) {

			$vehicle_types = VehicleTypeModel::query();

			return DataTables::eloquent($vehicle_types)
				->addColumn('check', function ($vehicle) {
					$tag = '<input type="checkbox" name="ids[]" value="' . $vehicle->id . '" class="checkbox" id="chk' . $vehicle->id . '" onclick=\'checkcheckbox();\'>';

					return $tag;
				})
				->editColumn('icon', function ($vehicle) {
					$src = ($vehicle->icon != null)?asset('uploads/' . $vehicle->icon): asset('assets/images/vehicle.jpeg');

					return '<img src="' . $src . '" height="70px" width="70px">';
				})

				->addColumn('isenable', function ($vehicle) {
					return ($vehicle->isenable) ? "YES" : "NO";
				})
				->filterColumn('isenable', function ($query, $keyword) {
					$query->whereRaw("IF(isenable = 1, 'YES', 'NO') like ?", ["%{$keyword}%"]);
				})
				->addColumn('action', function ($vehicle) {
					return view('vehicle_types.list-actions', ['row' => $vehicle]);
				})
				->addIndexColumn()
				->rawColumns(['icon', 'action', 'check'])
				->make(true);
			//return datatables(User::all())->toJson();

		}
	}

	public function create() {
		return view('vehicle_types.create');
	}

	public function store(VehicleTypeRequest $request) {
		if ($request->isenable == 1) {
			$enable = 1;
		} else {
			$enable = 0;
		}
		$new = VehicleTypeModel::create([
			'vehicletype' => $request->vehicletype,
			'displayname' => $request->displayname,
			'isenable' => $enable,
			'seats' => $request->seats,
		]);
		$file = $request->file('icon');

		if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
			$destinationPath = './uploads'; // upload path
			$extension = $file->getClientOriginalExtension();

			$fileName1 = 'vehicle_type_' . time() . '.' . $extension;

			$file->move($destinationPath, $fileName1);
			$new->icon = $fileName1;
			$new->save();
		}

		$key = $request->get('vehicletype');
		FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_base_fare', 'key_value' => '500', 'type_id' => $new->id]);
		FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_base_km', 'key_value' => '10', 'type_id' => $new->id]);
		FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_base_time', 'key_value' => '2', 'type_id' => $new->id]);
		FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_std_fare', 'key_value' => '20', 'type_id' => $new->id]);
		FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_weekend_base_fare', 'key_value' => '500', 'type_id' => $new->id]);
		FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_weekend_base_km', 'key_value' => '10', 'type_id' => $new->id]);
		FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_weekend_wait_time', 'key_value' => '2', 'type_id' => $new->id]);
		FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_weekend_std_fare', 'key_value' => '20', 'type_id' => $new->id]);
		FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_night_base_fare', 'key_value' => '500', 'type_id' => $new->id]);
		FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_night_base_km', 'key_value' => '10', 'type_id' => $new->id]);
		FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_night_wait_time', 'key_value' => '2', 'type_id' => $new->id]);
		FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_night_std_fare', 'key_value' => '20', 'type_id' => $new->id]);

		return redirect()->route('vehicle-types.index');
	}

	public function edit($id) {
		$data['vehicle_type'] = VehicleTypeModel::find($id);
		return view('vehicle_types.edit', $data);
	}

	public function update(VehicleTypeRequest $request) {
		if ($request->isenable == 1) {
			$enable = 1;
		} else {
			$enable = 0;
		}
		$data = VehicleTypeModel::find($request->get('id'));
		$data->update([
			'vehicletype' => $request->vehicletype,
			'displayname' => $request->displayname,
			'isenable' => $enable,
			'seats' => $request->seats,
		]);

		$file = $request->file('icon');
		if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
			$destinationPath = './uploads'; // upload path
			$extension = $file->getClientOriginalExtension();

			$fileName1 = 'vehicle_type_' . time() . '.' . $extension;

			$file->move($destinationPath, $fileName1);
			$data->icon = $fileName1;
			$data->save();
		}

		$settings = FareSettings::where('type_id', $request->get('id'))->get();
		// dd($settings);
		foreach ($settings as $key) {
			// echo "old  " . $key->key_name . "  === ";
			// echo "new " . str_replace($request->get('old_type'), strtolower(str_replace(' ', '', $request->get('type'))), $key->key_name) . "<br>";

			// update key_name in fare settings
			$key->key_name = str_replace($request->get('old_type'), strtolower(str_replace(' ', '', $request->get('vehicletype'))), $key->key_name);
			$key->save();
		}

		return redirect()->route('vehicle-types.index');
	}

	public function destroy(Request $request) {
		VehicleTypeModel::find($request->get('id'))->delete();
		return redirect()->route('vehicle-types.index');
	}

	public function bulk_delete(Request $request) {
		VehicleTypeModel::whereIn('id', $request->ids)->delete();
		return back();
	}
}
