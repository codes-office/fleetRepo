<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleGroupRequest;
use App\Model\VehicleGroupModel;
use Auth;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleGroupController extends Controller {
	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:VehicleGroup add', ['only' => ['create']]);
		$this->middleware('permission:VehicleGroup edit', ['only' => ['edit']]);
		$this->middleware('permission:VehicleGroup delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:VehicleGroup list');
	}

	public function index() {
		return view('vehicle_groups.index');
	}

	public function fetch_data(Request $request) {
		if ($request->ajax()) {

			if (Auth::user()->user_type == "S" || Auth::user()->group_id == null) {
				$vehicle_groups = VehicleGroupModel::query();
			} else {
				// $vehicle_groups = VehicleGroupModel::where('id', Auth::user()->group_id);
				$vehicle_groups = VehicleGroupModel::where('user_id', Auth::user()->id)->orwhere('id', Auth::user()->group_id);
				// dd($vehicle_groups);
			}

			return DataTables::eloquent($vehicle_groups)
				->addColumn('check', function ($vehicle) {
					$tag = '';
					if ($vehicle->id == '1') {
						$tag = '<i class="fa fa-ban" style="color:#767676;"></i>';
					}else{
						$tag = '<input type="checkbox" name="ids[]" value="' . $vehicle->id . '" class="checkbox" id="chk' . $vehicle->id . '" onclick=\'checkcheckbox();\'>';
					}

					return $tag;
				})

				->addColumn('vehicle_count', function ($vehicle) {
					$v = DB::table('vehicles')
						->where('group_id', $vehicle->id)->where('deleted_at', null)
						->count('group_id');
					return $v;
				})
				->addColumn('user_count', function ($vehicle) {
					$v = DB::table('users')->where('group_id', $vehicle->id)->where('deleted_at', null)->count('group_id');
					return $v;
				})
				->addColumn('action', function ($vehicle) {
					return view('vehicle_groups.list-actions', ['row' => $vehicle]);
				})
				->addIndexColumn()
				->rawColumns(['action', 'check'])
				->make(true);
			//return datatables(User::all())->toJson();

		}
	}

	public function create() {
		return view('vehicle_groups.create');
	}

	public function store(VehicleGroupRequest $request) {

		$group = new VehicleGroupModel();
		$group->name = $request->get('name');
		$group->description = $request->get('description');
		$group->note = $request->get('note');
		$group->user_id = Auth::user()->id;
		$group->save();
		return redirect()->route('vehicle_group.index');
	}

	public function edit($id) {
		$index['data'] = VehicleGroupModel::where('id', $id)->first();
		return view('vehicle_groups.edit', $index);

	}

	public function update(VehicleGroupRequest $request) {

		$group = VehicleGroupModel::find($request->get('id'));
		$group->name = $request->get('name');
		$group->description = $request->get('description');
		$group->note = $request->get('note');
		$group->save();
		return redirect()->route('vehicle_group.index');

	}

	public function destroy(Request $request) {
		VehicleGroupModel::find($request->get('id'))->delete();
		return redirect()->route('vehicle_group.index');
	}

	public function bulk_delete(Request $request) {
		VehicleGroupModel::whereIn('id', $request->ids)->delete();
		return back();
	}
}
