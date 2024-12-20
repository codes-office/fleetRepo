<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\UserRequest;
use App\Model\Hyvikk;
use App\Model\User;
use App\Model\VehicleGroupModel;
use Auth;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Redirect;
use Spatie\Permission\Models\Role;

class UsersController extends Controller {
	public function __construct() {

		// $this->middleware(['role:Admin']);
		$this->middleware('permission:Users add', ['only' => ['create']]);
		$this->middleware('permission:Users edit', ['only' => ['edit']]);
		$this->middleware('permission:Users delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:Users list');
	}
	public function index() {
		return view("users.index");
	}
	public function mltindex() {
		return view("users.mltuser");
	}

	public function fetch_data(Request $request) {
		if ($request->ajax()) {
			$users = User::with(['metas'])
				->where(function ($query) {
					$query->where('user_type', 'O')
						  ->orWhere('user_type', 'S');
				});
	
			$date_format_setting = (Hyvikk::get('date_format')) ? Hyvikk::get('date_format') : 'd-m-Y';
	
			return DataTables::eloquent($users)
				->addColumn('check', function ($user) {
					$tag = '';
					if ($user->user_type == "S") {
						$tag = '<i class="fa fa-ban" style="color:#767676;"></i>';
					} else {
						$tag = '<input type="checkbox" name="ids[]" value="' . $user->id . '" class="checkbox" id="chk' . $user->id . '" onclick=\'checkcheckbox();\'>';
					}
					return $tag;
				})
				->addColumn('profile_image', function ($user) {
					$src = ($user->profile_image != null) ? asset('uploads/' . $user->profile_image) : asset('assets/images/no-user.jpg');
					return '<img src="' . $src . '" height="70px" width="70px">';
				})
				->editColumn('created_at', function ($user) use ($date_format_setting) {
					return date($date_format_setting . ' g:i A', strtotime($user->created_at));
				})
				->addColumn('action', function ($user) {
					return view('users.list-actions', ['row' => $user]);
				})
				->rawColumns(['profile_image', 'action', 'check'])
				->make(true);
		}
	}	
	public function mlt_fetch_data(Request $request) {
		if ($request->ajax()) {
			$users = User::with(['metas'])
				->where(function ($query) {
					$query->where('user_type', 'M');
				});
	
			$date_format_setting = (Hyvikk::get('date_format')) ? Hyvikk::get('date_format') : 'd-m-Y';
	
			return DataTables::eloquent($users)
				->addColumn('check', function ($user) {
					$tag = '';
					if ($user->user_type == "S") {
						$tag = '<i class="fa fa-ban" style="color:#767676;"></i>';
					} else {
						$tag = '<input type="checkbox" name="ids[]" value="' . $user->id . '" class="checkbox" id="chk' . $user->id . '" onclick=\'checkcheckbox();\'>';
					}
					return $tag;
				})
				->addColumn('profile_image', function ($user) {
					$src = ($user->profile_image != null) ? asset('uploads/' . $user->profile_image) : asset('assets/images/no-user.jpg');
					return '<img src="' . $src . '" height="70px" width="70px">';
				})
				->editColumn('created_at', function ($user) use ($date_format_setting) {
					return date($date_format_setting . ' g:i A', strtotime($user->created_at));
				})
				->addColumn('action', function ($user) {
					return view('users.list-actions', ['row' => $user]);
				})
				->rawColumns(['profile_image', 'action', 'check'])
				->make(true);
		}
	}	

	public function create() {
		$index['groups'] = VehicleGroupModel::all();
		$index['roles'] = Role::get();
		return view("users.create", $index);
	}

	public function destroy(Request $request) {
		$user = User::find($request->get('id'));
		$user->update([
			'email' => time() . "_deleted" . $user->email,
		]);
		if (file_exists('./uploads/' . $user->profile_image) && !is_dir('./uploads/' . $user->profile_image)) {
			unlink('./uploads/' . $user->profile_image);
		}
		$user->delete();

		return redirect()->route('users.index');
	}

	private function upload_file($file, $field, $id) {
		$destinationPath = './uploads'; // upload path
		$extension = $file->getClientOriginalExtension();
		$fileName1 = Str::uuid() . '.' . $extension;

		$file->move($destinationPath, $fileName1);
		$user = User::find($id);
		$user->setMeta([$field => $fileName1]);
		$user->save();

	}

	public function store(UserRequest $request) {

		$role = Role::find($request->role_id)->toArray();

		if ($role['name'] == "Super Admin") {
			$user_type = 'S';
		} else if($role['name'] == "MLT Admin") {
			$user_type = 'M';
		} else {
			$user_type = 'O';
		}

		$id = User::create([
			"name" => $request->get("first_name") . " " . $request->get("last_name"),
			"email" => $request->get("email"),
			"password" => bcrypt($request->get("password")),
			"user_type" => $user_type,
			"address" => $request->get("address"),
			"group_id" => $request->get("group_id"),
			'api_token' => str_random(60),
		])->id;

		$user = User::find($id);
		$user->user_id = Auth::user()->id;
		$user->module = serialize($request->get('module'));
		// $user->language = 'English-en';
		$user->language = Auth::user()->language;
		$user->first_name = $request->get("first_name");
		$user->last_name = $request->get("last_name");
		$user->setMeta(['emsourcelat' => $request->get('latitude')]);
		$user->setMeta(['emsourcelong' => $request->get('longitude')]);
		$user->setMeta(['address' => $request->get('address')]);
	
		$user->save();
		$role = Role::find($request->role_id);
		$user->assignRole($role);
		if ($request->file('profile_image') && $request->file('profile_image')->isValid()) {
			$this->upload_file($request->file('profile_image'), "profile_image", $id);
		}
		return Redirect::route("users.index");

	}
	public function edit($id) {
		$user = User::find($id);
		log:info($user);
		$groups = VehicleGroupModel::all();
		$roles = Role::get();
		return view("users.edit", compact("user", 'groups', "roles"));
	}

	public function update(EditUserRequest $request) {

		$user = User::whereId($request->get("id"))->first();
		$user->name = $request->get("first_name") . " " . $request->get("last_name");
		$user->email = $request->get("email");
		$user->group_id = $request->get("group_id");
		$user->module = serialize($request->get('module'));
		$user->address = $request->get('address');
		$user->first_name = $request->get("first_name");
		$user->last_name = $request->get("last_name");
		$old = Role::find($user->roles->first()->id);
		if ($old != null) {
			$user->removeRole($old);
		}

		// $user->profile_image = $request->get('profile_image');
		$role = Role::find($request->role_id);

		if ($role['name'] == "Super Admin") {
			$user->user_type = 'S';
		} else {
			$user->user_type = 'O';
		}

		$user->save();
		$role = Role::find($request->role_id);
		$user->assignRole($role);
		if ($request->file('profile_image') && $request->file('profile_image')->isValid()) {
			if (file_exists('./uploads/' . $user->profile_image) && !is_dir('./uploads/' . $user->profile_image)) {
				unlink('./uploads/' . $user->profile_image);
			}
			$this->upload_file($request->file('profile_image'), "profile_image", $user->id);
		}
		$modules = unserialize($user->getMeta('module'));
		// if (Auth::user()->id == $user->id && !(in_array(0, $modules))) {
		//     return redirect('admin/');
		// }
		return Redirect::route("users.index");
	}

	public function bulk_delete(Request $request) {
		// dd($request->all());
		$users = User::whereIn('id', $request->ids)->get();
		foreach ($users as $user) {
			$user->update([
				'email' => time() . "_deleted" . $user->email,
			]);
			if (file_exists('./uploads/' . $user->profile_image) && !is_dir('./uploads/' . $user->profile_image)) {
				unlink('./uploads/' . $user->profile_image);
			}
			$user->delete();
		}
		// return redirect('admin/customers');
		return back();
	}

}
