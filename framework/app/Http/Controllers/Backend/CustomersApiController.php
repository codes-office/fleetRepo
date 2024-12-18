<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Imports\CustomerImport;
use App\Model\Address;
use App\Model\Bookings;
use App\Model\IncomeModel;
use App\Model\User;
use Auth;
use DB;
use Exception;
use Hyvikk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class CustomersApiController extends Controller {

	public function add_address(Request $request) {
		$validation = Validator::make($request->all(), [
			'address' => 'required',
			'customer_id' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			Address::create(['customer_id' => $request->customer_id, 'address' => $request->address]);
			$data['success'] = "1";
			$data['message'] = "Address added successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function edit_address(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'address' => 'required',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			Address::where('id', $request->id)->update(['address' => $request->address]);
			$data['success'] = "1";
			$data['message'] = "Address updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function delete_address(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			Address::find($request->id)->delete();
			$data['success'] = "1";
			$data['message'] = "Address deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

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
				// dd($fileName);
				Excel::import(new CustomerImport, 'assets/samples/' . $fileName);

				// $excel = Importer::make('Excel');
				// $excel->load('assets/samples/' . $fileName);
				// $collection = $excel->getCollection()->toArray();
				// array_shift($collection);
				// // dd($collection);
				// foreach ($collection as $customer) {
				//     if ($customer[3] != null) {
				//         $id = User::create([
				//             "name" => $customer[0] . " " . $customer[1],
				//             "email" => $customer[3],
				//             "password" => bcrypt($customer[6]),
				//             "user_type" => "C",
				//             "api_token" => str_random(60),
				//         ])->id;
				//         $user = User::find($id);
				//         $user->first_name = $customer[0];
				//         $user->last_name = $customer[1];
				//         $user->address = $customer[5];
				//         $user->mobno = $customer[2];
				//         if ($customer[4] == "female") {
				//             $user->gender = 0;
				//         } else {
				//             $user->gender = 1;
				//         }
				//         $user->save();
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

	public function addresses() {
		$address = Address::where('customer_id', Auth::id())->select(['id', 'address'])->get()->toArray();
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $address;
		return $data;
	}

	public function home() {

		$total_kms = IncomeModel::select(DB::raw('sum(mileage) as total_kms'))->where('user_id', Auth::id())->get();
		$income = IncomeModel::select(DB::raw('sum(amount) as income'))->where('user_id', Auth::id())->get();

		$time = 0;
		$travel_time = 0;
		$bookings = Bookings::where('customer_id', Auth::user()->id)->get();
		foreach ($bookings as $b) {
			if ($b->status == 1) {
				$time += $b->getMeta('waiting_time');
				$times = explode(" ", $b->getMeta('driving_time'));
				if (sizeof($times) == 2) {
					if (starts_with($times[1], 'hour')) {
						$travel_time += $times[0] * 60;
					}

					if (starts_with($times[1], 'min')) {
						$travel_time += $times[0];
					}
					if (starts_with($times[1], 'day')) {
						$travel_time += $times[0] * 24 * 60;
					}
				}

				if (sizeof($times) == 4) {
					if (starts_with($times[1], 'hour')) {
						$travel_time += $times[0] * 60;
					}

					if (starts_with($times[1], 'day')) {
						$travel_time += $times[0] * 24 * 60;
					}

					if (starts_with($times[3], 'hour')) {
						$travel_time += $times[2] * 60;
					}

					if (starts_with($times[3], 'min')) {
						$travel_time += $times[2];
					}
				}

				if (sizeof($times) == 6) {
					if (starts_with($times[1], 'day')) {
						$travel_time += $times[0] * 24 * 60;
					}

					if (starts_with($times[3], 'hour')) {
						$travel_time += $times[2] * 60;
					}

					if (starts_with($times[5], 'min')) {
						$travel_time += $times[4];
					}
				}
			}
		}
		$user = Auth::user();
		if ($user->language != null) {
			$language = $user->language;
			// $language = explode('-', $user->language);
		} else {
			$language = Hyvikk::get("language");
			// $language = explode('-', Hyvikk::get("language"));
		}
		if (Auth::user()->getMeta('profile_pic') != null) {
			if (starts_with(Auth::user()->getMeta('profile_pic'), 'http')) {
				$src = Auth::user()->getMeta('profile_pic');
			} else {
				$src = asset('uploads/' . Auth::user()->getMeta('profile_pic'));
			}
		} else {
			$src = asset("assets/images/no-user.jpg");
		}
		$details = array(
			'id' => Auth::id(),
			'email' => Auth::user()->email,
			"language" => $language,
			'image' => $src,
			'customer' => Auth::user()->name,
			'total_amount' => Hyvikk::get('currency') . " " . ((is_null($income[0]->income) ? 0 : $income[0]->income)),
			'total_distance' => ((is_null($total_kms[0]->total_kms) ? 0 : $total_kms[0]->total_kms)) . " " . Hyvikk::get('dis_format'),
			'total_waiting_time' => $time,
			'total_travel_time' => $travel_time . " Minutes",
		);
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
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

			User::whereIn('id', $request->ids)->delete();
			$data['success'] = "1";
			$data['message'] = "Records deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'unique:users,email,' . \Request::get("id"),
			'phone' => 'required|numeric',
			'gender' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			$user = User::find($request->id);
			$user->name = $request->first_name . " " . $request->last_name;
			$user->email = $request->email;
			$user->first_name = $request->first_name;
			$user->last_name = $request->last_name;
			$user->address = $request->address;
			$user->mobno = $request->phone;
			$user->gender = $request->gender;
			$user->save();
			$data['success'] = "1";
			$data['message'] = "Customer updated successfully!";
			$data['data'] = "";
		}

		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'unique:users,email',
			'phone' => 'required|numeric',
			'gender' => 'required|integer',
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
				"password" => bcrypt("password"),
				"user_type" => "C",
				"api_token" => str_random(60),
			])->id;
			$user = User::find($id);
			$user->first_name = $request->first_name;
			$user->last_name = $request->last_name;
			$user->address = $request->address;
			$user->mobno = $request->phone;
			$user->gender = $request->gender;
			$user->save();
			$data['success'] = "1";
			$data['message'] = "Customer added successfully!";
			$data['data'] = array('id' => $user->id);
		}
		return $data;
	}

	public function customers() {
		$customers = User::where("user_type", "C")->orderBy('id', 'desc')->get();
		$details = array();
		foreach ($customers as $row) {
			$details[] = array(
				"id" => $row->id,
				"name" => $row->name,
				"first_name" => $row->first_name,
				"last_name" => $row->last_name,
				"email" => $row->email,
				"address" => $row->address,
				"phone" => $row->mobno,
				"gender" => $row->gender,
				'gender_text' => ($row->gender == 1) ? "Male" : "Female",
			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
