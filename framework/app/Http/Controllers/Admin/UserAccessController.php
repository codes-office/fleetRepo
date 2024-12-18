<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Requests\RolesRequest;
use DataTables;

class UserAccessController extends Controller {

	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:Settings list');
	}

	public function index() {
		$data['data'] = Role::get();
		return view('roles.index', $data);
	}

	public function create() {
		$modules = array(
			'Users',
			'Drivers',
			'Customer',
			'Vehicles',
			'VehicleType',
			'VehicleGroup',
			'VehicleInspection',
			'Transactions',
			'Bookings',
			'BookingQuotations',
			'Reports',
			'Fuel',
			'Vendors',
			'Parts',
			'PartsCategory',
			'WorkOrders',
			'Mechanics',
			'Notes',
			'ServiceReminders',
			'ServiceItems',
			'Testimonials',
			'Team',
			'Settings',
			'Inquiries',
		);
		return view('roles.create', compact('modules'));
	}

	public function store(RolesRequest $request) {
		// dd($request->all());
		$modules = array(
			'Users',
			'Drivers',
			'Customer',
			'Vehicles',
			'VehicleType',

			'VehicleGroup',
			'VehicleInspection',
			'Transactions',
			'Bookings',
			'BookingQuotations',
			'Reports',
			'Fuel',
			'Vendors',
			'Parts',
			'PartsCategory',
			'WorkOrders',
			'Mechanics',
			'Notes',
			'ServiceReminders',
			'ServiceItems',
			'Testimonials',
			'Team',
			'Settings',
			'Inquiries',
		);
		$role = Role::create(['name' => $request->name]);
		foreach ($modules as $row) {

			$add = $row . "_add";
			$edit = $row . "_edit";
			$delete = $row . "_delete";
			$list = $row . "_list";
			$import = $row . "_import";
			if ($request->$add == 1) {
				$name = str_replace("_", " ", $add);
				// $read_perm = Permission::create(['name' => $request->name . " " . $name]);
				$add_perm = Permission::findByName($name);
				$role->givePermissionTo($add_perm);
				$add_perm->assignRole($role);
			}
			if ($request->$edit == 1) {
				$name = str_replace("_", " ", $edit);
				// $write_perm = Permission::create(['name' => $request->name . " " . $name]);
				$edit_perm = Permission::findByName($name);
				$role->givePermissionTo($edit_perm);
				$edit_perm->assignRole($role);
			}
			if ($request->$delete == 1) {
				$name = str_replace("_", " ", $delete);
				// $read_perm = Permission::create(['name' => $request->name . " " . $name]);
				$delete_perm = Permission::findByName($name);
				$role->givePermissionTo($delete_perm);
				$delete_perm->assignRole($role);
			}
			if ($request->$list == 1) {
				$name = str_replace("_", " ", $list);
				// $write_perm = Permission::create(['name' => $request->name . " " . $name]);
				$list_perm = Permission::findByName($name);
				$role->givePermissionTo($list_perm);
				$list_perm->assignRole($role);
			}
			if ($request->$import == 1) {
				$name = str_replace("_", " ", $import);
				// $write_perm = Permission::create(['name' => $request->name . " " . $name]);
				$import_perm = Permission::findByName($name);
				$role->givePermissionTo($import_perm);
				$import_perm->assignRole($role);
			}
		}
		// dd($request->all());
		// $role = Role::findByName('Admin');
		// $user = User::find(1);
		// $user->assignRole('Admin');
		// dd($user->can('edit articles'));
		// $role = Role::where('name', 'Admin')->first();
		// dd($role->hasPermissionTo('write'));
		// $read = Permission::where('name', 'read')->first();
		// $write = Permission::where('name', 'write')->first();

		///////////////////
		// $role = Role::create(['name' => $request->name]);
		// $read = Permission::create(['name' => 'read ' . $request->name]);
		// $write = Permission::create(['name' => 'write ' . $request->name]);
		// if ($request->read == 1) {
		//     $role->givePermissionTo($read);
		//     $read->assignRole($role);
		// }
		// if ($request->write == 1) {
		//     $role->givePermissionTo($write);
		//     $write->assignRole($role);
		// }
		/////////////////////
		return redirect()->route('roles.index');
	}

	public function edit($id) {
		$data['modules'] = array(
			'Users',
			'Drivers',
			'Customer',
			'Vehicles',
			'VehicleType',

			'VehicleGroup',
			'VehicleInspection',
			'Transactions',
			'Bookings',
			'BookingQuotations',
			'Reports',
			'Fuel',
			'Vendors',
			'Parts',
			'PartsCategory',
			'WorkOrders',
			'Mechanics',
			'Notes',
			'ServiceReminders',
			'ServiceItems',
			'Testimonials',
			'Team',
			'Settings',
			'Inquiries',
		);
		$data['data'] = Role::find($id);
		return view('roles.edit', $data);
	}

	public function update(RolesRequest $request) {
		//dd($request->all());
		$role = Role::find($request->id);
		$role->name = $request->name;
		$role->save();

		$modules = array(
			'Users',
			'Drivers',
			'Customer',
			'Vehicles',
			'VehicleType',

			'VehicleGroup',
			'VehicleInspection',
			'Transactions',
			'Bookings',
			'BookingQuotations',
			'Reports',
			'Fuel',
			'Vendors',
			'Parts',
			'PartsCategory',
			'WorkOrders',
			'Mechanics',
			'Notes',
			'ServiceReminders',
			'ServiceItems',
			'Testimonials',
			'Team',
			'Settings',
			'Inquiries',
		);
		$all_permissions = array();
		foreach ($modules as $row) {

			$add = $row . "_add";
			$edit = $row . "_edit";
			$delete = $row . "_delete";
			$list = $row . "_list";
			$import = $row . "_import";
			if ($request->$add == 1) {
				$name = str_replace("_", " ", $add);
				$all_permissions[] = $name;
				$add_perm = Permission::findByName($name);
				$add_perm->assignRole($role);
			}
			if ($request->$edit == 1) {
				$name = str_replace("_", " ", $edit);
				$all_permissions[] = $name;
				$edit_perm = Permission::findByName($name);
				$edit_perm->assignRole($role);
			}
			if ($request->$delete == 1) {
				$name = str_replace("_", " ", $delete);
				$all_permissions[] = $name;
				$delete_perm = Permission::findByName($name);
				$delete_perm->assignRole($role);
			}
			if ($request->$list == 1) {
				$name = str_replace("_", " ", $list);
				$all_permissions[] = $name;
				$list_perm = Permission::findByName($name);
				$list_perm->assignRole($role);
			}
			if ($request->$import == 1) {
				$name = str_replace("_", " ", $import);
				$all_permissions[] = $name;
				$import_perm = Permission::findByName($name);
				$import_perm->assignRole($role);
			}
		}
		$role->syncPermissions($all_permissions);

		//return back();
		return redirect()->route('roles.index');
	}

	public function destroy(Request $request) {
		$role = Role::find($request->id);
		foreach ($role->getAllPermissions() as $permission) {
			$role->revokePermissionTo($permission);
			$permission->removeRole($role);
		}
		$role->delete();
		return redirect()->route('roles.index');
	}
}
