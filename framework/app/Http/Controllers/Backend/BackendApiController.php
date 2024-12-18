<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Bookings;
use App\Model\Expense;
use App\Model\IncomeModel;
use App\Model\MessageModel;
use App\Model\ReviewModel;
use App\Model\User;
use App\Model\VehicleModel;
use App\Model\Vendor;
use Auth;
use DB;
use Hyvikk;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth as Login;
use Illuminate\Support\Str;
use Storage;
use Validator;

class BackendApiController extends Controller {
	public function lang_dropdown() {
		$languages = Storage::disk('views')->directories('');
		return $languages;
	}

	public function dashboard($year) {
		$index['drivers'] = User::whereUser_type("D")->get()->count();
		$index['reviews'] = ReviewModel::all()->count();
		$index['customers'] = User::whereUser_type("C")->get()->count();
		$index['users'] = User::whereUser_type("O")->get()->count();
		$vehicle_ids = array(0);
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$index['vehicles'] = VehicleModel::all()->count();
			$index['bookings'] = Bookings::all()->count();
			$vehicle_ids = VehicleModel::pluck('id')->toArray();
		} else {
			$index['vehicles'] = VehicleModel::where('group_id', Auth::user()->group_id)->count();
			$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			$index['bookings'] = Bookings::whereIn('vehicle_id', $vehicle_ids)->count();
		}
		$index['vendors'] = Vendor::all()->count();
		$index['customers'] = User::whereUser_type("C")->get()->count();
		$index['income'] = IncomeModel::whereRaw('year(date) = ? and month(date)=?', [date("Y"), date("n")])->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
		// dd($vehicle_ids);
		$index['expense'] = Expense::whereRaw('year(date) = ? and month(date)=?', [date("Y"), date("n")])->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
		$index['currency'] = Hyvikk::get('currency');

		$vv = array();
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$all_vehicles = VehicleModel::get();
		} else {
			$all_vehicles = VehicleModel::where('group_id', Auth::user()->group_id)->get();
		}

		foreach ($all_vehicles as $key) {
			$vv[$key->id] = $key->make_name . "-" . $key->model_name . "-" . $key->license_plate;
		}
		$vehicle_expenses = array();
		$vehicle_name = $vv;
		$all_expenses = Expense::select('vehicle_id', DB::raw('sum(amount) as expense'))->whereIn('vehicle_id', $vehicle_ids)->whereYear('date', date('Y'))->whereMonth('date', date('n'))->groupBy('vehicle_id')->get();
		foreach ($all_expenses as $row) {
			$vehicle_expenses[$vehicle_name[$row->vehicle_id]] = $row->expense;
		}

		// dd($vehicle_expenses, $vehicle_name);

		// monthly pie-chart data
		$monthly_chart = array(
			'income_expense' => array(
				'income' => $index['income'],
				'expense' => $index['expense'],
			),
			'vehicle_expenses' => $vehicle_expenses,
		);
		// dd($vehicle_expenses, $monthly_chart);
		// year wise chart data
		$yearly_chart['income'] = $this->yearly_income($year);
		$yearly_chart['expense'] = $this->yearly_expense($year);

		// date wise chart report
		$exp = DB::select('select date,sum(amount) as tot from expense where deleted_at is null and vehicle_id in (' . join(",", $vehicle_ids) . ') group by date');
		$inc = DB::select('select date,sum(amount) as tot from income where deleted_at is null and vehicle_id in (' . join(",", $vehicle_ids) . ') group by date');
		$date1 = IncomeModel::pluck('date')->toArray();
		$date2 = Expense::pluck('date')->toArray();
		$all_dates = array_unique(array_merge($date1, $date2));
		$dates = array_count_values($all_dates);
		ksort($dates);
		$dates = array_slice($dates, -12, 12);
		$index['dates'] = $dates;
		$temp = array();
		foreach ($all_dates as $key) {
			$temp[$key] = 0;
		}
		$income2 = array();
		foreach ($inc as $income) {
			$income2[$income->date] = $income->tot;
		}
		$inc_data = array_merge($temp, $income2);
		ksort($inc_data);
		// $index['incomes'] = implode(",", array_slice($inc_data, -12, 12));
		$date_wise_income = array_slice($inc_data, -12, 12);

		$expense2 = array();
		foreach ($exp as $e) {
			$expense2[$e->date] = $e->tot;
		}
		$expenses = array_merge($temp, $expense2);
		ksort($expenses);
		// $index['expenses1'] = implode(",", array_slice($expenses, -12, 12));
		$date_wise_expense = array_slice($expenses, -12, 12);

		$date_wise_chart = array(
			'date_wise_income' => $date_wise_income,
			'date_wise_expense' => $date_wise_expense,
		);

		$details = array(
			'counts' => $index,
			'monthly_chart' => $monthly_chart,
			'yearly_chart' => $yearly_chart,
			'date_wise_chart' => $date_wise_chart,
		);
		// dd($details, $date_wise_chart);
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;

	}

	private function yearly_income($year) {

		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$all_vehicles = VehicleModel::get();
		} else {
			$all_vehicles = VehicleModel::where('group_id', Auth::user()->group_id)->get();
		}
		$vehicle_ids = array(0);
		foreach ($all_vehicles as $key) {
			$vehicle_ids[] = $key->id;
		}
		$incomes = DB::select('select monthname(date) as mnth,sum(amount) as tot from income where year(date)=? and  deleted_at is null and vehicle_id in (' . join(",", $vehicle_ids) . ') group by month(date)', [$year]);
		$months = ["January" => 0, "February" => 0, "March" => 0, "April" => 0, "May" => 0, "June" => 0, "July" => 0, "August" => 0, "September" => 0, "October" => 0, "November" => 0, "December" => 0];
		$income2 = array();

		foreach ($incomes as $income) {

			$income2[$income->mnth] = $income->tot;

		}
		$yr = array_merge($months, $income2);
		return $yr;
		// return implode(",", $yr);
	}
	private function yearly_expense($year) {
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$all_vehicles = VehicleModel::get();
		} else {
			$all_vehicles = VehicleModel::where('group_id', Auth::user()->group_id)->get();
		}
		$vehicle_ids = array(0);
		foreach ($all_vehicles as $key) {
			$vehicle_ids[] = $key->id;
		}
		$incomes = DB::select('select monthname(date) as mnth,sum(amount) as tot from expense where year(date)=? and  deleted_at is null and vehicle_id in (' . join(",", $vehicle_ids) . ') group by month(date)', [$year]);
		$months = ["January" => 0, "February" => 0, "March" => 0, "April" => 0, "May" => 0, "June" => 0, "July" => 0, "August" => 0, "September" => 0, "October" => 0, "November" => 0, "December" => 0];
		$income2 = array();

		foreach ($incomes as $income) {

			$income2[$income->mnth] = $income->tot;

		}
		$yr = array_merge($months, $income2);
		return $yr;
		// return implode(",", $yr);

	}

	public function update_profile_photo(Request $request) {
		$validation = Validator::make($request->all(), [
			'image' => 'required|image|mimes:png,jpg,jpeg',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$file = $request->file('image');
			$destinationPath = './uploads'; // upload path
			$extension = $file->getClientOriginalExtension();
			$fileName1 = Str::uuid() . '.' . $extension;

			$file->move($destinationPath, $fileName1);
			$user = User::find(Auth::id());
			if ($user->user_type == "D") {
				$field = "driver_image";
			} elseif ($user->user_type == "C") {
				$field = "profile_pic";
			} else {
				$field = "profile_image";
			}
			$user->setMeta([$field => $fileName1]);
			$user->save();
			$data['success'] = "1";
			$data['message'] = "Profile photo updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update_profile(Request $request) {
		$validation = Validator::make($request->all(), [
			'name' => 'required',
			'email' => 'required|email|unique:users,email,' . Auth::id(),
			'language' => 'required',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$id = Auth::id();
			$user = User::find($id);
			$user->name = $request->name;
			$user->email = $request->email;
			$user->language = $request->language;
			$user->save();
			// if ($user->user_type == "D") {
			//     $field = "driver_image";
			// } elseif ($user->user_type == "C") {
			//     $field = "profile_pic";
			// } else {
			//     $field = "profile_image";
			// }
			// if ($request->file('image') && $request->file('image')->isValid()) {

			//     $this->upload_file($request->file('image'), $field, $user->id);
			// }
			$data['success'] = "1";
			$data['message'] = "Profile has been updated successfully!";
			$data['data'] = array('id' => Auth::id());
		}
		return $data;
	}

	public function fare_calc(Request $request) {
		$validation = Validator::make($request->all(), [
			'day' => 'required|integer',
			'vehicle_id' => 'required|integer',
			'mileage' => 'required|numeric',
			'waiting_time' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$details = array();
			$vehicle = VehicleModel::find($request->vehicle_id);
			$day = $request->day;
			$mileage = $request->mileage;
			$waiting_time = $request->waiting_time;

			if ($day == 1) {
				$base_km = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_base_km');
				$base_fare = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_base_fare');
				$wait_time_fare = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_base_time');
				$std_fare = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_std_fare');

			} elseif ($day == 2) {
				$base_km = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_weekend_base_km');
				$base_fare = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_weekend_base_fare');
				$wait_time_fare = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_weekend_wait_time');
				$std_fare = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_weekend_std_fare');

			} elseif ($day == 3) {
				$base_km = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_night_base_km');
				$base_fare = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_night_base_fare');
				$wait_time_fare = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_night_wait_time');
				$std_fare = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicle->types->vehicletype)) . '_night_std_fare');
			} else {
				$data['success'] = "0";
				$data['message'] = "Type of day is incorrect";
				$data['data'] = "";
				return $data;
			}

			if ($mileage <= $base_km) {
				$total = $base_fare + ($waiting_time * $wait_time_fare);
			} else {
				$sum = ($mileage - $base_km) * $std_fare;
				$total = round($base_fare + $sum + ($waiting_time * $wait_time_fare), 2);
			}

			$tax_percent = 0;
			if (Hyvikk::get('tax_charge') != "null") {
				$taxes = json_decode(Hyvikk::get('tax_charge'), true);
				foreach ($taxes as $key => $val) {
					$tax_percent += $val;
				}
			}

			$tax_charges = round(($tax_percent * $total) / 100, 2);
			$tax_total = round($total + $tax_charges, 2);

			$details = array(
				'total' => $total,
				'total_tax_percent' => $tax_percent,
				'total_tax_charge_rs' => $tax_charges,
				'tax_total' => $tax_total,
			);

			$data['success'] = "1";
			$data['message'] = "Data fetched.";
			$data['data'] = $details;
		}
		return $data;
	}

	public function get_fare_calc(Request $request) {

		$tax_percent = 0;
		if (Hyvikk::get('tax_charge') != "null") {
			$taxes = json_decode(Hyvikk::get('tax_charge'), true);
			foreach ($taxes as $key => $val) {
				$tax_percent += $val;
			}
		}

		$details = array(

			'tax' => $tax_percent,
		);

		$data['success'] = "1";
		$data['message'] = "Data fetched.";
		$data['data'] = $details;

		return $data;
	}

	public function reviews() {
		$details = array();
		$records = ReviewModel::orderBy('id', 'desc')->get();
		foreach ($records as $row) {
			$details[] = array(
				'user' => $row->user->name,
				'booking_id' => $row->booking_id,
				'ratings' => $row->ratings,
				'review' => $row->review_text,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}

	public function inquiries() {
		$details = array();
		$records = MessageModel::orderBy('id', 'desc')->get();
		foreach ($records as $row) {
			$details[] = array(
				'name' => $row->name,
				'email' => $row->email,
				'message' => $row->message,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}

	public function change_password(Request $request) {
		$validation = Validator::make($request->all(), [
			'password' => 'required',
			'user_id' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$user = User::find($request->user_id);
			$user->password = bcrypt($request->password);
			$user->save();
			$data['success'] = "1";
			$data['message'] = "Password has been updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function csrf() {
		return csrf_token();
	}

	public function auth_check(Request $request) {
		// dd($header = $request->header('Authorization'));
		if (Auth::check()) {
			return "true";
		} else {
			return "false";
		}
		return "success";
	}

	public function login(Request $request) {
		$email = $request->email;
		$password = $request->password;

		if (Login::attempt(['email' => $email, 'password' => $password])) {
			$user = Login::user();
			$user->login_status = 1;
			$user->save();

			$data['success'] = "1";
			$data['message'] = "You have Signed in Successfully!";
			$src = url('assets/images/user-noimage.png');
			if ($user->user_type == "S" || $user->user_type == "O") {
				$src = ($user->profile_image)?url('uploads/' . $user->profile_image): $src;
			} elseif ($user->user_type == "D") {
				$src = ($user->driver_image)?url('uploads/' . $user->driver_image): $src;
			} elseif ($user->user_type == "C") {
				$src = ($user->profile_pic)?url('uploads/' . $user->profile_pic): $src;
			}
			if ($user->language != null) {
				$language = explode('-', $user->language);
			} else {
				$language = explode('-', Hyvikk::get("language"));
			}
			$modules = array();
			if ($user->user_type == "S" || $user->user_type == "O") {
				$permissions = unserialize($user->module);
				if (in_array(0, $permissions)) {
					array_push($modules, 'users');
				}
				if (in_array(1, $permissions)) {
					array_push($modules, 'vehicles');
				}
				if (in_array(2, $permissions)) {
					array_push($modules, 'transactions');
				}
				if (in_array(3, $permissions)) {
					array_push($modules, 'bookings');
				}

				if (in_array(4, $permissions)) {
					array_push($modules, 'reports');
				}
				if (in_array(5, $permissions)) {
					array_push($modules, 'fuel');
				}
				if (in_array(6, $permissions)) {
					array_push($modules, 'vendors');
				}
				if (in_array(7, $permissions)) {
					array_push($modules, 'work_orders');
				}
				if (in_array(8, $permissions)) {
					array_push($modules, 'notes');
				}
				if (in_array(9, $permissions)) {
					array_push($modules, 'service_reminders');
				}
				if (in_array(10, $permissions)) {
					array_push($modules, 'reviews');
				}
				if (in_array(12, $permissions)) {
					array_push($modules, 'maps');
				}
				if (in_array(13, $permissions)) {
					array_push($modules, 'helpus');
				}
				if (in_array(14, $permissions)) {
					array_push($modules, 'parts');
				}
				if (in_array(15, $permissions)) {
					array_push($modules, 'testimonials');
				}
				if ($user->user_type == "S") {
					array_push($modules, 'team');
					array_push($modules, 'settings');
				}
			} elseif ($user->user_type == "D") {
				$modules = array("notes");
			} elseif ($user->user_type == "C") {
				$modules = array("bookings");
			}
			$accessToken = $user->createToken('authToken')->accessToken;
			$date_setting = "DD-MM-YYYY";
			if (Hyvikk::get('date_format') == 'Y-m-d') {
				$date_setting = "YYYY-MM-DD";
			}
			if (Hyvikk::get('date_format') == 'm-d-Y') {
				$date_setting = "MM-DD-YYYY";
			}
			$data['userinfo'] = array(
				"user_id" => $user->id,
				// "api_token" => $user->api_token,
				"user_name" => $user->name,
				"user_type" => $user->user_type,
				"email" => $user->email,
				// "password" => $user->password,
				"profile_pic" => $src,
				"language" => ($language[1] == "en") ? "en-us" : $language[1],
				"permissions" => $modules,
				"access_token" => $accessToken,
				'date_format' => $date_setting,
			);
		} else {
			$data['success'] = "0";
			$data['message'] = "Invalid Login Credentials";
			$data['userinfo'] = "";
		}
		return response()->json($data);
	}

	public function logout(Request $request) {
		$user = Login::user();
		$user->login_status = 0;
		$user->save();
		foreach ($user->tokens as $token) {
			$token->revoke();
		}
		// dd($user->tokens);
		$this->guard()->logout();
		$request->session()->invalidate();
		$data['success'] = "1";
		$data['message'] = "User logged out successfully!";
		$data['data'] = "";
		return $data;
	}

	protected function guard() {
		return Login::guard();
	}
}
