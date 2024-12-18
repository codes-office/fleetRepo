<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class UsersApiController extends Controller {

	public function upload_documents(Request $request, $id) {
		// dd($request->all());

		$validation = Validator::make($request->all(), [
			// 'id' => 'required|integer',
			'profile_image' => 'required|image|mimes:jpg,png,jpeg',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$user = User::find($id);
			if ($request->file('profile_image') && $request->file('profile_image')->isValid()) {
				$this->upload_file($request->file('profile_image'), "profile_image", $id);
			}

			$data['success'] = "1";
			$data['message'] = "Profile image uploaded successfully!";
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

			$user = User::find($request->id);
			if (file_exists('./uploads/' . $user->profile_image) && !is_dir('./uploads/' . $user->profile_image)) {
				unlink('./uploads/' . $user->profile_image);
			}
			$user->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'module' => 'required',
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required|email|unique:users,email,' . \Request::get("id"),
			'profile_image' => 'nullable|image|mimes:jpg,png,jpeg',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$user = User::whereId($request->get("id"))->first();
			$user->name = $request->first_name . " " . $request->last_name;
			$user->email = $request->email;
			$user->group_id = $request->group_id;
			$user->module = serialize($request->module);
			$user->first_name = $request->first_name;
			$user->last_name = $request->last_name;
			$user->user_type = $request->user_type;
			$user->save();
			if ($request->file('profile_image') && $request->file('profile_image')->isValid()) {
				$this->upload_file($request->file('profile_image'), "profile_image", $user->id);
			}
			$data['success'] = "1";
			$data['message'] = "User updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'module' => 'required',
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required|email|unique:users,email',
			'password' => 'required|min:6',
			'profile_image' => 'nullable|image|mimes:jpg,png,jpeg',
			'group_id' => 'nullable|integer',
			'user_type' => 'required|in:S,O',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$id = User::create([
				"name" => $request->first_name . " " . $request->last_name,
				"email" => $request->email,
				"password" => bcrypt($request->password),
				"user_type" => $request->user_type,
				"group_id" => $request->group_id,
				'api_token' => str_random(60),
			])->id;

			$user = User::find($id);
			$accessToken = $user->createToken('authToken')->accessToken;

			$user->module = serialize($request->module);
			$user->language = 'English-en';
			$user->first_name = $request->first_name;
			$user->last_name = $request->last_name;
			$user->save();

			if ($request->file('profile_image') && $request->file('profile_image')->isValid()) {
				$this->upload_file($request->file('profile_image'), "profile_image", $id);
			}
			$data['success'] = "1";
			$data['message'] = "User added successfully!";
			$data['data'] = array('id' => $user->id);
		}
		return $data;
	}

	private function upload_file($file, $field, $id) {
		$destinationPath = './uploads'; // upload path
		$extension = $file->getClientOriginalExtension();

		$fileName1 = Str::uuid() . '.' . $extension;

		$file->move($destinationPath, $fileName1);
		$user = User::find($id);
		if (file_exists('./uploads/' . $user->$field) && !is_dir('./uploads/' . $user->$field)) {
			unlink('./uploads/' . $user->$field);
		}
		$user->setMeta([$field => $fileName1]);
		$user->save();

	}

	public function users() {
		$users = User::whereIn("user_type", ["O", "S"])->orderBy('id', 'desc')->get();
		$details = array();
		foreach ($users as $row) {
			$names = explode(" ", $row->name);
			$details[] = array(
				"id" => $row->id,
				"name" => $row->name,
				"first_name" => $names[0],
				"last_name" => $names[1],
				"email" => $row->email,
				"profile_image" => ($row->profile_image)?url('uploads/' . $row->profile_image): url('assets/images/user-noimage.png'),
				"user_type" => $row->user_type,
				"group_id" => $row->group_id,
				"module" => unserialize($row->module),
				'created' => date("Y-m-d H:i:s", strtotime($row->created_at)),

			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}

}
