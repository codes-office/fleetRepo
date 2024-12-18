<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Http\Controllers;

use App\Model\User;
use DataTables;
use Illuminate\Http\Request;

class DatatablesController extends Controller {
	public function index(Request $request) {
		if ($request->ajax()) {

			$users = User::whereUser_type("D");

			return Datatables::of($users)
				->addColumn('check', function ($user) {
					return $user->id;
				})
				->editColumn('driver_image', function ($user) {
					return ($user->driver_image != null)?asset('uploads/' . $user->driver_image): asset('assets/images/no-user.jpg');
				})
				->editColumn('vehicle', function ($user) {
					return ($user->vehicle_id != null) ? $user->driver_vehicle->vehicle->make_name . '-' . $user->driver_vehicle->vehicle->model_name . '-' . $user->driver_vehicle->vehicle->license_plate : '';
				})
				->editColumn('is_active', function ($user) {
					return ($user->is_active == 1) ? "YES" : "NO";
				})
				->editColumn('phone', function ($user) {
					return $user->phone;
				})
				->editColumn('start_date', function ($user) {
					return $user->start_date;
				})
				->addColumn('action', function ($user) {
					return '<a href="' . url("admin/drivers/" . $user->id . "/edit") .
					'" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Edit</a>
                <a href="admin/drivers/' . $user->id .
						'" class="btn btn-xs btn-danger mt-1"><i class="fa fa-trash"></i> Delete</a>';
				})
				->make(true);
			//return datatables(User::all())->toJson();

		}
		return view('data_index');
	}

	// public function get_custom_posts(){
	//     return Datatables::eloquent(User::query())->make(true);
	// }
}
