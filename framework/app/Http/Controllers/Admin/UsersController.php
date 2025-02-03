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

	public function assignAdmin(Request $request)
	{
	  
		// Validate the request
		$request->validate([
			'user_id' => 'required|exists:users,id',
			'admin_id' => 'nullable|exists:users,id'
		]);
	
		// Find the user by ID
		$user = User::findOrFail($request->user_id);
	
		// Assign the admin to the user
		$user->assigned_admin = $request->admin_id;
		$user->save();
	
		// Return a JSON response indicating success
		return response()->json(['success' => true]);
	}
	
	public function fetch_data(Request $request) {
		if ($request->ajax()) {
			$users = User::with(['metas'])
			->where(function ($query) {
				$query->where('user_type', 'O')	;
			})
			->whereHas('metas', function ($query) {
				$query->where('key', 'client')->where('value', 1);
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
			// $users = User::with(['metas'])
			// 	->where(function ($query) {
			// 		$query->where('user_type', 'O');
			// 	});
			$admins = User::with(['metas'])
			->where(function ($query) {
				$query->where('user_type', 'O');
			})
			->whereHas('metas', function ($query) {
				$query->where('key', 'client')->where('value', 1);
			})->select('id', 'name')->get();


			$users = User::with(['metas'])
			->where(function ($query) {
				$query->where('user_type', 'O');
			})
			->whereHas('metas', function ($query) {
				$query->where('key', 'client')->where('value', 0);
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
		 		->addColumn('assign_admin', function ($user) use ($admins) {
					$dropdown = '<select class="form-control assign-admin-user" data-user-id="' . $user->id . '">';
					$dropdown .= '<option value="">Select Admin</option>';
	
					foreach ($admins as $admin) {
						$selected = ($user->assigned_admin == $admin->id) ? 'selected' : '';
						$dropdown .= '<option value="' . $admin->id . '" ' . $selected . '>' . $admin->name . '</option>';
					}
	
					$dropdown .= '</select>';
					return $dropdown;
				})


            ->addColumn('assigned_admin', function($user){
                return $user->assigned_admin ? User::find($user->assigned_admin)->name : 'No Admin Assigned';  
            })
			->rawColumns(['profile_image', 'action', 'check', 'assign_admin']) // Mark columns with HTML as raw
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


	public function store(DriverRequest $request)
{
    // Validate request
    $validated = $request->validate([
        'first_name' => 'required|string',
        'city' => 'required|string',
        'DOB' => 'required|date',
        'phone_code' => 'required|string',
        'phone' => 'required|numeric|unique:users,number',
        'emp_id' => 'required|string',
        'vendor_id' => 'required|numeric',
        'license_number' => 'required|string',
        'license_number_date' => 'nullable|date',
        'induction_date' => 'nullable|date',
        'badge_number' => 'nullable|string',
        'badge_number_date' => 'nullable|date',
        'alternate_gov_id' => 'nullable|string',
        'alternate_gov_id_number' => 'nullable|string',
        'background_verification_status' => 'nullable|string',
        'background_verification_date' => 'nullable|date',
        'police_verification_status' => 'nullable|string',
        'police_verification_date' => 'nullable|date',
        'medical_verification_status' => 'nullable|string',
        'medical_verification_date' => 'nullable|date',
        'training_verification_status' => 'nullable|string',
        'training_date' => 'nullable|date',
        'eye_test_date' => 'nullable|date',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'address' => 'nullable|string',

        // File validation
        'driver_license_image' => 'nullable|mimes:jpg,png,jpeg',
        'induction_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'alternate_gov_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'background_verification_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'police_verification_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'medical_verification_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'training_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'eye_test_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'documents_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
    ]);

    // Create user with only necessary fields
    $user = User::create([
        "name" => $validated['first_name'],
        "user_type" => "D",
        "api_token" => Str::random(60),
        "number" => $validated['phone']
    ]);

    // Prepare metadata (everything except first_name, user_type, api_token, and phone)
    $metaData = collect($validated)->except(['first_name', 'phone'])->toArray();

    // Handle file uploads and store paths in user_meta
    $this->handleFileUploads($request, $user, $metaData);

    // Save metadata in users_meta table
    $this->storeUserMeta($user->id, $metaData);

    // Assign permissions
    $user->givePermissionTo([
        'Notes add', 'Notes edit', 'Notes delete', 'Notes list',
        'Drivers list', 'Fuel add', 'Fuel edit', 'Fuel delete',
        'Fuel list', 'VehicleInspection add', 'Transactions list',
        'Transactions add', 'Transactions edit', 'Transactions delete'
    ]);

    return redirect()->route("drivers.index");
}

/**
 * Handle file uploads and store file paths in user_meta.
 */
private function handleFileUploads($request, $user, &$metaData)
{
    $fileFields = [
        'driver_license_image' => 'driver_license',
        'induction_file' => 'induction',
        'alternate_gov_file' => 'alternate_gov',
        'background_verification_file' => 'background_verification',
        'police_verification_file' => 'police_verification',
        'medical_verification_file' => 'medical_verification',
        'training_file' => 'training',
        'eye_test_file' => 'eye_test',
        'documents_file' => 'documents',
    ];

    foreach ($fileFields as $requestField => $metaKey) {
        if ($request->hasFile($requestField) && $request->file($requestField)->isValid()) {
            $filePath = $this->uploadFile($request->file($requestField), $metaKey, $user->id);
            $metaData[$metaKey . "_file_path"] = $filePath; // Store in user_meta
        }
    }
}

/**
 * Store metadata in users_meta table as key-value pairs.
 */
private function storeUserMeta($userId, $metaData)
{
    $userMetaEntries = [];
    
    foreach ($metaData as $key => $value) {
        if (!is_null($value)) {
            $userMetaEntries[] = [
                'user_id' => $userId,
                'meta_key' => $key,
                'meta_value' => is_array($value) ? json_encode($value) : $value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    }

    if (!empty($userMetaEntries)) {
        DB::table('users_meta')->insert($userMetaEntries);
    }
}

/**
 * Upload file and return the file path.
 */
private function uploadFile($file, $field, $id)
{
    $destinationPath = public_path('uploads'); // Directory for uploads
    $extension = $file->getClientOriginalExtension();
    $fileName = Str::uuid() . '.' . $extension;

    $file->move($destinationPath, $fileName);

    return 'uploads/' . $fileName; // Return file path for storage in user_meta
}


	// private function upload_file($file, $field, $id) {
	// 	$destinationPath = './uploads'; // upload path
	// 	$extension = $file->getClientOriginalExtension();
	// 	$fileName1 = Str::uuid() . '.' . $extension;

	// 	$file->move($destinationPath, $fileName1);
	// 	$user = User::find($id);
	// 	$user->setMeta([$field => $fileName1]);
	// 	$user->save();

	// }

	// public function store(UserRequest $request) {

	// 	// dd($request->all());
	// 	// exit();

	// 	$role = Role::find($request->role_id)->toArray();

	// 	if ($role['name'] == "Super Admin") {
	// 		$user_type = 'S';
	// 	} else if($role['name'] == "MLT Admin") {
	// 		$user_type = 'O';
	// 		$client ="0";
			
	// 	} else {
	// 		$user_type = 'O';
	// 		$client ="1";
	// 	}

	// 	$id = User::create([
	// 		"name" => $request->get("first_name") . " " . $request->get("last_name"),
	// 		"email" => $request->get("email"),
	// 		"password" => bcrypt($request->get("password")),
	// 		"user_type" => $user_type,
	// 		"address" => $request->get("address"),
	// 		"group_id" => $request->get("group_id"),
	// 		'api_token' => str_random(60),
	// 	])->id;

	// 	$user = User::find($id);
	// 	$user->user_id = Auth::user()->id;
	// 	$user->module = serialize($request->get('module'));
	// 	// $user->language = 'English-en';
	// 	$user->language = Auth::user()->language;
	// 	$user->first_name = $request->get("first_name");
	// 	$user->last_name = $request->get("last_name");
	// 	$user->setMeta(['emsourcelat' => $request->get('latitude')]);
	// 	$user->setMeta(['emsourcelong' => $request->get('longitude')]);
	// 	$user->setMeta(['address' => $request->get('address')]);
	// 	$user->setMeta(['Client' => $client]);
	// 	$user->save();
	// 	$role = Role::find($request->role_id);
	// 	$user->assignRole($role);
	// 	if ($request->file('profile_image') && $request->file('profile_image')->isValid()) {
	// 		$this->upload_file($request->file('profile_image'), "profile_image", $id);
	// 	}
	// 	return Redirect::route("users.index");

	// }
	public function edit($id) {
		$user = User::find($id);
		// log:info($user);
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
			$user->user_type = 'M';
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
