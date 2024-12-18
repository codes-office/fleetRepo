<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\PartsModel;
use Auth;
use Hyvikk;
use Illuminate\Http\Request;
use Validator;

class PartsApiController extends Controller {

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
			if ($request->file('image') && $request->file('image')->isValid()) {
				$file = $request->file('image');
				$destinationPath = './uploads'; // upload path
				$extension = $file->getClientOriginalExtension();
				$fileName1 = uniqid() . '.' . $extension;
				$file->move($destinationPath, $fileName1);

				$x = PartsModel::find($id)->update(['image' => $fileName1]);
			}

			$data['success'] = "1";
			$data['message'] = "Vendor photo uploaded successfully!";
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

			PartsModel::whereIn('id', $request->ids)->delete();

			$data['success'] = "1";
			$data['message'] = "Records deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function add_stock(Request $request) {
		$validation = Validator::make($request->all(), [
			'part_id' => 'required|integer',
			'stock' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$part = PartsModel::find($request->part_id);
			$part->stock = $part->stock + $request->stock;
			$part->save();
			$data['success'] = "1";
			$data['message'] = "Part stock updated successfully!";
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
			PartsModel::find($request->id)->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'barcode' => 'required',
			'number' => 'required',
			'description' => 'required',
			'unit_cost' => 'required|numeric',
			'vendor_id' => 'required|integer',
			'stock' => 'required|integer',
			'title' => 'required',
			'category_id' => 'required|integer',
			'year' => 'required|numeric',
			'model' => 'required',
			'image' => 'nullable|image|mimes:jpg,png,jpeg',
			'availability' => 'required|integer',
			'status' => 'required',
			'udf' => 'nullable|array',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$part = PartsModel::find($request->id);

			$part->barcode = $request->barcode;
			$part->number = $request->number;
			$part->description = $request->description;
			$part->unit_cost = $request->unit_cost;
			$part->vendor_id = $request->vendor_id;
			$part->manufacturer = $request->manufacturer;
			$part->note = $request->note;
			$part->stock = $request->stock;
			$part->udf = serialize($request->udf);
			$part->category_id = $request->category_id;
			$part->status = $request->status;
			$part->availability = $request->availability;
			$part->title = $request->title;
			$part->year = $request->year;
			$part->model = $request->model;
			$part->save();

			if ($request->file('image') && $request->file('image')->isValid()) {
				$file = $request->file('image');
				$destinationPath = './uploads'; // upload path
				$extension = $file->getClientOriginalExtension();
				$fileName1 = uniqid() . '.' . $extension;
				$file->move($destinationPath, $fileName1);

				$x = PartsModel::find($request->id)->update(['image' => $fileName1]);
			}
			$data['success'] = "1";
			$data['message'] = "Part updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'barcode' => 'required',
			'number' => 'required',
			'description' => 'required',
			'unit_cost' => 'required|numeric',
			'vendor_id' => 'required|integer',
			'stock' => 'required|integer',
			'title' => 'required',
			'category_id' => 'required|integer',
			'year' => 'required|numeric',
			'model' => 'required',
			'image' => 'nullable|image|mimes:jpg,png,jpeg',
			'availability' => 'required|integer',
			'status' => 'required',
			'udf' => 'nullable|array',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$id = PartsModel::create([
				'user_id' => Auth::id(),
				'barcode' => $request->barcode,
				'number' => $request->number,
				'description' => $request->description,
				'unit_cost' => $request->unit_cost,
				'vendor_id' => $request->vendor_id,
				'manufacturer' => $request->manufacturer,
				'note' => $request->note,
				'stock' => $request->stock,
				'udf' => serialize($request->udf),
				'category_id' => $request->category_id,
				'status' => $request->status,
				'availability' => $request->availability,
				'title' => $request->title,
				'year' => $request->year,
				'model' => $request->model,
			])->id;

			if ($request->file('image') && $request->file('image')->isValid()) {
				$file = $request->file('image');
				$destinationPath = './uploads'; // upload path
				$extension = $file->getClientOriginalExtension();
				$fileName1 = uniqid() . '.' . $extension;
				$file->move($destinationPath, $fileName1);

				$x = PartsModel::find($id)->update(['image' => $fileName1]);
			}
			$data['success'] = "1";
			$data['message'] = "Part added successfully!";
			$data['data'] = array('id' => $id);
		}
		return $data;
	}

	public function parts() {
		$records = PartsModel::orderBy('id', 'desc')->get();
		$details = array();
		foreach ($records as $row) {
			$image = asset('assets/images/no-image.png');
			if ($row->image != null) {

				$image = asset('uploads/' . $row->image);
			}
			$udf = "";
			$blank = array();
			if (unserialize($row->udf)) {
				$test = unserialize($row->udf);
				foreach ($test as $key => $val) {
					$blank[] = array(
						'name' => $key,
						'value' => $val,
					);
				}
				$udf = $blank;
			}
			$details[] = array(
				'id' => $row->id,
				'category_id' => $row->category_id,
				'vendor_id' => $row->vendor_id,
				'image' => $image,
				'barcode' => $row->barcode,
				'vendor' => $row->vendor->name,
				'category' => $row->category->name,
				'number' => $row->number,
				'description' => $row->description,
				'unit_cost' => $row->unit_cost,
				'manufacturer' => $row->manufacturer,
				'note' => $row->note,
				'stock' => $row->stock,
				'status' => $row->status,
				'availability' => $row->availability,
				'title' => $row->title,
				'year' => $row->year,
				'model' => $row->model,
				'currency' => Hyvikk::get('currency'),
				"udf" => $udf,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
