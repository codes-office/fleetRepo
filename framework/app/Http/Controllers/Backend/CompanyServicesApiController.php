<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\CompanyServicesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class CompanyServicesApiController extends Controller {

	public function upload_documents(Request $request, $id) {
		$validation = Validator::make($request->all(), [
			// 'id' => 'required|integer',
			'image' => 'required|image|mimes:png',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$record = CompanyServicesModel::find($id);
			$file = $request->file('image');

			if ($request->hasFile('image') && $request->file('image')->isValid()) {
				$destinationPath = './uploads'; // upload path
				$extension = $file->getClientOriginalExtension();

				$fileName1 = Str::uuid() . '.' . $extension;

				$file->move($destinationPath, $fileName1);
				$record->image = $fileName1;
				$record->save();
			}
			$data['success'] = "1";
			$data['message'] = "Image uploaded successfully!";
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

			CompanyServicesModel::whereIn('id', $request->ids)->delete();

			$data['success'] = "1";
			$data['message'] = "Records deleted successfully!";
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
			CompanyServicesModel::find($request->id)->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'title' => 'required|max:54',
			'description' => 'required|max:93',
			'image' => 'image|mimes:png',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$record = CompanyServicesModel::find($request->id);
			$record->title = $request->title;
			$record->description = $request->description;
			$record->save();
			$file = $request->file('image');

			if ($request->hasFile('image') && $request->file('image')->isValid()) {
				$destinationPath = './uploads'; // upload path
				$extension = $file->getClientOriginalExtension();

				$fileName1 = Str::uuid() . '.' . $extension;

				$file->move($destinationPath, $fileName1);
				$record->image = $fileName1;
				$record->save();
			}
			$data['success'] = "1";
			$data['message'] = "Company service updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'title' => 'required|max:54',
			'description' => 'required|max:93',
			'image' => 'image|mimes:png',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$record = CompanyServicesModel::create(['title' => $request->title, 'description' => $request->description]);
			$file = $request->file('image');

			if ($request->hasFile('image') && $request->file('image')->isValid()) {
				$destinationPath = './uploads'; // upload path
				$extension = $file->getClientOriginalExtension();

				$fileName1 = Str::uuid() . '.' . $extension;

				$file->move($destinationPath, $fileName1);
				$record->image = $fileName1;
				$record->save();
			}
			$data['success'] = "1";
			$data['message'] = "Company service added successfully!";
			$data['data'] = array('id' => $record->id);
		}
		return $data;
	}

	public function services() {
		$records = CompanyServicesModel::orderBy('id', 'desc')->get();
		$details = array();
		foreach ($records as $row) {
			$image = null;
			if ($row->image != null) {
				$image = asset('uploads/' . $row->image);
			}
			$details[] = array(
				'id' => $row->id,
				'title' => $row->title,
				'description' => $row->description,
				'image' => $image,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
