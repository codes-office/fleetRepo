<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Imports\VendorImport;
use App\Model\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class VendorsApiController extends Controller {

	public function import_records(Request $request) {
		$validation = Validator::make($request->all(), [
			'excel' => 'required|mimes:xlsx,xls',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			try {
				$file = $request->excel;
				$destinationPath = './assets/samples/'; // upload path
				$extension = $file->getClientOriginalExtension();
				$fileName = Str::uuid() . '.' . $extension;
				$file->move($destinationPath, $fileName);

				Excel::import(new VendorImport, 'assets/samples/' . $fileName);

				// $excel = Importer::make('Excel');
				// $excel->load('assets/samples/' . $fileName);
				// $collection = $excel->getCollection()->toArray();
				// array_shift($collection);
				// // dd($collection);
				// foreach ($collection as $vendor) {
				//     if ($vendor[0] != null) {
				//         Vendor::create([
				//             'name' => $vendor[0],
				//             'phone' => $vendor[1],
				//             'email' => $vendor[2],
				//             'type' => $vendor[3],
				//             'website' => $vendor[4],
				//             'address1' => $vendor[5],
				//             'address2' => $vendor[6],
				//             'city' => $vendor[7],
				//             'province' => $vendor[8],
				//             'postal_code' => $vendor[9],
				//             'country' => $vendor[10],
				//             'note' => $vendor[11],
				//         ]);
				//     }

				// }
				$data['success'] = "1";
				$data['message'] = "Records imported successfully!";
				$data['data'] = "";
			} catch (Exception $e) {
				$data['success'] = "0";
				$data['message'] = "Unable to import records.";
				$data['data'] = "";
			}
		}

		return $data;
	}

	public function upload_documents(Request $request, $id) {
		$validation = Validator::make($request->all(), [
			// 'id' => 'required|integer',
			'photo' => 'required|image|mimes:jpg,png,jpeg',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$vendor = Vendor::find($id);
			if ($request->file('photo') && $request->file('photo')->isValid()) {
				$file = $request->file('photo');
				$destinationPath = './uploads'; // upload path
				$extension = $file->getClientOriginalExtension();
				$fileName1 = Str::uuid() . '.' . $extension;

				$file->move($destinationPath, $fileName1);

				$vendor->photo = $fileName1;
				$vendor->save();

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

			Vendor::whereIn('id', $request->ids)->delete();

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
			Vendor::find($request->id)->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required',
			'name' => 'required',
			'type' => 'required',
			'website' => 'required',
			'phone' => 'required',
			'address1' => 'required',
			'email' => 'required',
			'city' => 'required',
			'postal_code' => 'regex:/\d{5}(-\d{0,4})?/|nullable',
			'country' => 'required',
			'photo' => 'nullable|image|mimes:jpg,png,jpeg',
			'udf' => 'nullable|array',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$form_data = $request->all();
			// unset($form_data['photo']);
			unset($form_data['id']);
			unset($form_data['udf']);
			$id = Vendor::where('id', $request->id)->update($form_data);
			$vendor = Vendor::find($request->id);
			$vendor->udf = serialize($request->udf);
			$vendor->save();
			// if ($request->file('photo') && $request->file('photo')->isValid()) {
			//     $file = $request->file('photo');
			//     $destinationPath = './uploads'; // upload path
			//     $extension = $file->getClientOriginalExtension();
			//     $fileName1 = Str::uuid() . '.' . $extension;

			//     $file->move($destinationPath, $fileName1);

			//     $vendor->photo = $fileName1;
			//     $vendor->save();

			// }
			$data['success'] = "1";
			$data['message'] = "Vendor record updated successfully!";
			$data['data'] = "";
		}
		return $data;

	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'name' => 'required',
			'type' => 'required',
			'website' => 'required',
			'phone' => 'required',
			'address1' => 'required',
			'email' => 'required',
			'city' => 'required',
			'postal_code' => 'regex:/\d{5}(-\d{0,4})?/|nullable',
			'country' => 'required',
			'photo' => 'nullable|image|mimes:jpg,png,jpeg',
			'udf' => 'nullable|array',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$form_data = $request->all();
			// unset($form_data['photo']);
			unset($form_data['udf']);
			$id = Vendor::create($form_data)->id;
			$vendor = Vendor::find($id);
			$vendor->udf = serialize($request->udf);
			$vendor->save();
			// if ($request->file('photo') && $request->file('photo')->isValid()) {
			//     $file = $request->file('photo');
			//     $destinationPath = './uploads'; // upload path
			//     $extension = $file->getClientOriginalExtension();
			//     $fileName1 = Str::uuid() . '.' . $extension;

			//     $file->move($destinationPath, $fileName1);

			//     $vendor->photo = $fileName1;
			//     $vendor->save();

			// }
			$data['success'] = "1";
			$data['message'] = "Vendor record added successfully!";
			$data['data'] = array('id' => $id);
		}
		return $data;

	}

	public function types() {

		$vendor_types = Vendor::orderBy("name")->groupBy("type")->get()->pluck("type")->toArray();
		array_push($vendor_types, "Machinaries", "Fuel", "Parts");
		$vendor_types = array_unique($vendor_types);
		$array = array();
		foreach ($vendor_types as $row) {
			$array[] = $row;
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $array;
		return $data;
	}

	public function vendors() {
		// Passport::refreshTokensExpireIn(Carbon::now()->add(120, 'minutes'));

		// foreach (Auth::user()->tokens->where('revoked', 0) as $token) {
		//     if (strtotime($token->expires_at) > strtotime("now")) {
		//         $token->expires_at = \Carbon\Carbon::now()->add(120, 'minutes');
		//         $token->save();
		//     }
		// }
		// dd(123);
		$vendors = Vendor::orderBy('id', 'desc')->get();
		$details = array();
		foreach ($vendors as $row) {
			$image = asset("assets/images/no-user.jpg");
			if ($row->photo) {
				$image = asset('uploads/' . $row->photo);
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
				'name' => $row->name,
				'photo' => $image,
				'email' => $row->email,
				'website' => $row->website,
				'phone' => $row->phone,
				'type' => $row->type,
				'address1' => $row->address1,
				'address2' => $row->address2,
				'city' => $row->city,
				'province' => $row->province,
				'postal_code' => $row->postal_code,
				'country' => $row->country,
				'note' => $row->note,
				"udf" => $udf,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}

	public function vendor_list() {
		// Passport::refreshTokensExpireIn(Carbon::now()->add(120, 'minutes'));

		// foreach (Auth::user()->tokens->where('revoked', 0) as $token) {
		//     if (strtotime($token->expires_at) > strtotime("now")) {
		//         $token->expires_at = \Carbon\Carbon::now()->add(120, 'minutes');
		//         $token->save();
		//     }
		// }
		// dd(123);
		$vendors = Vendor::orderBy('id', 'desc')->get();
		$details = array();
		foreach ($vendors as $row) {
			$image = asset("assets/images/no-user.jpg");
			if ($row->photo) {
				$image = asset('uploads/' . $row->photo);
			}
			$details[] = array(
				'id' => $row->id,
				'name' => $row->name,

			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
