<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class TestimonialsApiController extends Controller {

	public function upload_documents(Request $request, $id) {
		$validation = Validator::make($request->all(), [
			// 'id' => 'required|integer',
			'image' => 'required|image|mimes:jpg,png,jpeg',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$testimonial = Testimonial::find($id);
			$file = $request->file('image');

			if ($request->hasFile('image') && $request->file('image')->isValid()) {
				$destinationPath = './uploads'; // upload path
				$extension = $file->getClientOriginalExtension();

				$fileName1 = Str::uuid() . '.' . $extension;

				$file->move($destinationPath, $fileName1);
				$testimonial->image = $fileName1;
				$testimonial->save();
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

			Testimonial::whereIn('id', $request->ids)->delete();

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
			Testimonial::find($request->id)->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'name' => 'required',
			'details' => 'required|max:350',
			'image' => 'image|mimes:jpg,png,gif,jpeg',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$testimonial = Testimonial::find($request->id);
			$testimonial->name = $request->name;
			$testimonial->details = $request->details;
			$testimonial->save();
			$file = $request->file('image');

			if ($request->hasFile('image') && $request->file('image')->isValid()) {
				$destinationPath = './uploads'; // upload path
				$extension = $file->getClientOriginalExtension();

				$fileName1 = Str::uuid() . '.' . $extension;

				$file->move($destinationPath, $fileName1);
				$testimonial->image = $fileName1;
				$testimonial->save();
			}
			$data['success'] = "1";
			$data['message'] = "Testimonial updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'name' => 'required',
			'details' => 'required|max:350',
			'image' => 'image|mimes:jpg,png,gif,jpeg',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$testimonial = Testimonial::create(['name' => $request->name, 'details' => $request->details]);
			$file = $request->file('image');

			if ($request->hasFile('image') && $request->file('image')->isValid()) {
				$destinationPath = './uploads'; // upload path
				$extension = $file->getClientOriginalExtension();

				$fileName1 = Str::uuid() . '.' . $extension;

				$file->move($destinationPath, $fileName1);
				$testimonial->image = $fileName1;
				$testimonial->save();
			}
			$data['success'] = "1";
			$data['message'] = "Testimonial added successfully!";
			$data['data'] = array('id' => $testimonial->id);
		}
		return $data;
	}

	public function testimonials() {
		$records = Testimonial::orderBy('id', 'desc')->get();
		$details = array();
		foreach ($records as $row) {
			$image = asset('assets/images/no-user.jpg');
			if ($row->image != null) {
				$image = asset('uploads/' . $row->image);
			}
			$details[] = array(
				'id' => $row->id,
				'name' => $row->name,
				'details' => $row->details,
				'image' => $image,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
