<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditProfileRequest;
use App\Http\Requests\PasswordRequest;
use App\Model\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Storage;
use Illuminate\Support\Facades\App;

class UtilityController extends Controller {

	public function changepass($id) {
		$data['languages'] = Storage::disk('views')->directories('');

		$data['user_data'] = User::find(Auth::user()->id);

		return view('utilities.changepass', $data);

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

	public function changepassword(EditProfileRequest $request) {
		// dd($request->all());
		$id = Auth::id();
		$user = User::find($id);
		$user->name = $request->name;
		$user->email = $request->email;
		$user->theme = $request->theme;
		$name = explode(' ', $request->name);

		$user->first_name = $name[0] ?? '';
		$user->middle_name = $name[1] ?? '';
		$user->last_name = $name[2] ?? '';

		$user->language = $request->get('language');
		// $user->password = bcrypt($request->passwd);
		$user->save();
		if ($user->user_type == "D") {
			$field = "driver_image";
		} elseif ($user->user_type == "C") {
			$field = "profile_pic";
		} else {
			$field = "profile_image";
		}
		if ($request->file('image') && $request->file('image')->isValid()) {
			if (file_exists('./uploads/' . $user->$field) && !is_dir('./uploads/' . $user->$field)) {
				unlink('./uploads/' . $user->$field);
			}
			$this->upload_file($request->file('image'), $field, $user->id);
		}
		// return back();
		App::setLocale($request->get('language'));
		return redirect()->back()->with('message',__('fleet.profile_updated'));
	}

	public function password_change(Request $request) {
		// $id = $request->get('id');
		$user = User::find($request->get("driver_id"));
		$user->password = bcrypt($request->get("passwd"));
		$user->save();
	}

	public function change() {
		return view('utilities.password');
	}

	public function change_post(PasswordRequest $request) {
		$user = User::find($request->get('id'));
		$user->password = bcrypt($request->get('password'));
		$user->save();
		return redirect()->back();
	}
}
